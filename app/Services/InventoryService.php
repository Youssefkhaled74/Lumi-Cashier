<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Add item units (without individual barcodes).
     * Creates new ItemUnit records for the given item.
     *
     * @param Item $item The item to add units for
     * @param int $quantity Number of units to create
     * @param float|null $price Unit price (defaults to item price)
     * @return Collection Collection of created ItemUnit models
     * @throws \Exception
     */
    public function addItemUnits(Item $item, int $quantity, ?float $price = null): Collection
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }

        $price = $price ?? $item->price;
        $createdUnits = collect();

        DB::beginTransaction();

        try {
            // Lock the item to prevent concurrent modifications
            $item = Item::lockForUpdate()->findOrFail($item->id);

            // Create the specified number of units
            for ($i = 0; $i < $quantity; $i++) {
                $unit = ItemUnit::create([
                    'item_id' => $item->id,
                    'quantity' => 1, // Each unit represents 1 item
                    'price' => $price,
                    'status' => ItemUnit::STATUS_AVAILABLE,
                ]);

                $createdUnits->push($unit);
            }

            DB::commit();

            return $createdUnits;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to add item units: ' . $e->getMessage());
        }
    }

    /**
     * Decrease stock by marking available units as sold.
     * Uses lockForUpdate to ensure concurrency safety.
     *
     * @param Item $item The item to decrease stock for
     * @param int $quantity Number of units to mark as sold
     * @param int $orderId The order ID to link sold units to
     * @return Collection Collection of ItemUnit models that were marked as sold
     * @throws \Exception
     */
    public function decreaseStock(Item $item, int $quantity, int $orderId): Collection
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }

        DB::beginTransaction();

        try {
            // Get available units with lock to prevent race conditions
            $availableUnits = ItemUnit::where('item_id', $item->id)
                ->available()
                ->lockForUpdate()
                ->limit($quantity)
                ->get();

            if ($availableUnits->count() < $quantity) {
                throw new \Exception(
                    "Insufficient stock. Requested: {$quantity}, Available: {$availableUnits->count()}"
                );
            }

            $soldUnits = collect();

            // Mark each unit as sold and link to order
            foreach ($availableUnits as $unit) {
                $unit->markAsSold($orderId);
                $soldUnits->push($unit);
            }

            DB::commit();

            return $soldUnits;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all available (unsold) units for an item.
     *
     * @param Item $item The item to get available units for
     * @return Collection Collection of available ItemUnit models
     */
    public function getAvailableUnits(Item $item): Collection
    {
        return ItemUnit::where('item_id', $item->id)
            ->available()
            ->get();
    }

    /**
     * Get total available stock count for an item.
     *
     * @param Item $item The item to check stock for
     * @return int Total available quantity
     */
    public function getAvailableStock(Item $item): int
    {
        return ItemUnit::where('item_id', $item->id)
            ->available()
            ->count();
    }

    /**
     * Check if item has sufficient stock.
     *
     * @param Item $item The item to check
     * @param int $quantity Required quantity
     * @return bool True if sufficient stock is available
     */
    public function hasStock(Item $item, int $quantity): bool
    {
        return $this->getAvailableStock($item) >= $quantity;
    }

    /**
     * Generate a unique barcode using UUID v4.
     * Ensures the barcode doesn't already exist in the database.
     *
     * @return string Unique barcode
     */
    protected function generateUniqueBarcode(): string
    {
        do {
            // Generate UUID and remove hyphens for a cleaner barcode
            $barcode = strtoupper(str_replace('-', '', Str::uuid()->toString()));
        } while (ItemUnit::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Restore sold units back to available stock.
     * Useful for order cancellations or returns.
     *
     * @param int $orderId The order ID to restore units for
     * @return int Number of units restored
     * @throws \Exception
     */
    public function restoreUnits(int $orderId): int
    {
        DB::beginTransaction();

        try {
            $soldUnits = ItemUnit::where('order_id', $orderId)
                ->sold()
                ->lockForUpdate()
                ->get();

            $restoredCount = 0;

            foreach ($soldUnits as $unit) {
                $unit->status = ItemUnit::STATUS_AVAILABLE;
                $unit->order_id = null;
                $unit->save();
                $restoredCount++;
            }

            DB::commit();

            return $restoredCount;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to restore units: ' . $e->getMessage());
        }
    }

    /**
     * Find a unit by barcode.
     *
     * @param string $barcode The barcode to search for
     * @return ItemUnit|null The found unit or null
     */
    public function findByBarcode(string $barcode): ?ItemUnit
    {
        return ItemUnit::where('barcode', $barcode)->first();
    }

    /**
     * Get inventory summary for an item.
     *
     * @param Item $item The item to get summary for
     * @return array Inventory summary with total, available, and sold counts
     */
    public function getInventorySummary(Item $item): array
    {
        $totalUnits = ItemUnit::where('item_id', $item->id)->count();
        $availableUnits = ItemUnit::where('item_id', $item->id)
            ->where('quantity', '>', 0)
            ->count();
        $soldUnits = ItemUnit::where('item_id', $item->id)
            ->where('quantity', 0)
            ->count();

        return [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'total_units' => $totalUnits,
            'available_units' => $availableUnits,
            'sold_units' => $soldUnits,
            'stock_percentage' => $totalUnits > 0 ? round(($availableUnits / $totalUnits) * 100, 2) : 0,
        ];
    }
}
