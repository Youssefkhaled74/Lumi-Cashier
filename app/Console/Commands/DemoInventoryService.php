<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Services\InventoryService;
use Illuminate\Console\Command;

class DemoInventoryService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrate InventoryService functionality';

    /**
     * Execute the console command.
     */
    public function handle(InventoryService $inventoryService): int
    {
        $this->info('=== InventoryService Demo ===');
        $this->newLine();

        // Get a test item
        $item = Item::first();

        if (!$item) {
            $this->error('No items found in database. Please run seeders first.');
            return self::FAILURE;
        }

        $this->info("Working with item: {$item->name} (ID: {$item->id})");
        $this->newLine();

        // Demo 1: Add Item Units
        $this->comment('📦 Demo 1: Adding Item Units');
        $this->line('Adding 5 units with unique barcodes...');

        $units = $inventoryService->addItemUnits($item, 5);

        $this->info("✓ Created {$units->count()} units");
        $this->table(
            ['ID', 'Barcode', 'Quantity', 'Price'],
            $units->map(fn($u) => [$u->id, $u->barcode, $u->quantity, '$' . $u->price])->toArray()
        );

        // Demo 2: Check Available Stock
        $this->newLine();
        $this->comment('📊 Demo 2: Check Available Stock');

        $availableStock = $inventoryService->getAvailableStock($item);
        $this->info("Available units: {$availableStock}");

        $hasStock = $inventoryService->hasStock($item, 3);
        $this->line("Has 3 units in stock: " . ($hasStock ? 'Yes ✓' : 'No ✗'));

        // Demo 3: Decrease Stock
        $this->newLine();
        $this->comment('🛒 Demo 3: Decreasing Stock (Simulating Sale)');
        $this->line('Selling 3 units...');

        try {
            $soldUnits = $inventoryService->decreaseStock($item, 3);
            $this->info("✓ Sold {$soldUnits->count()} units");

            $this->table(
                ['ID', 'Barcode', 'Status'],
                $soldUnits->map(fn($u) => [
                    $u->id,
                    $u->barcode,
                    $u->quantity === 0 ? 'SOLD' : 'AVAILABLE'
                ])->toArray()
            );
        } catch (\Exception $e) {
            $this->error("✗ Error: {$e->getMessage()}");
        }

        // Demo 4: Get Available Units
        $this->newLine();
        $this->comment('📋 Demo 4: Get Remaining Available Units');

        $availableUnits = $inventoryService->getAvailableUnits($item);
        $this->info("Available units: {$availableUnits->count()}");

        if ($availableUnits->count() > 0) {
            $this->table(
                ['ID', 'Barcode', 'Quantity', 'Price'],
                $availableUnits->map(fn($u) => [
                    $u->id,
                    $u->barcode,
                    $u->quantity,
                    '$' . $u->price
                ])->toArray()
            );
        }

        // Demo 5: Try Insufficient Stock
        $this->newLine();
        $this->comment('⚠️  Demo 5: Testing Insufficient Stock Protection');
        $this->line('Attempting to sell 10 units (more than available)...');

        try {
            $inventoryService->decreaseStock($item, 10);
            $this->error('✗ Should have thrown exception!');
        } catch (\Exception $e) {
            $this->info("✓ Correctly prevented: {$e->getMessage()}");
        }

        // Demo 6: Restore Units
        $this->newLine();
        $this->comment('↩️  Demo 6: Restoring Sold Units');
        $this->line('Simulating order cancellation - restoring 2 sold units...');

        $soldUnitsSample = $soldUnits->take(2);
        $restoredCount = $inventoryService->restoreUnits($soldUnitsSample);

        $this->info("✓ Restored {$restoredCount} units back to inventory");

        // Demo 7: Find by Barcode
        $this->newLine();
        $this->comment('🔍 Demo 7: Finding Unit by Barcode');

        $sampleUnit = $units->first();
        $this->line("Searching for barcode: {$sampleUnit->barcode}");

        $foundUnit = $inventoryService->findByBarcode($sampleUnit->barcode);

        if ($foundUnit) {
            $this->info("✓ Found unit ID: {$foundUnit->id}");
            $this->line("  Item: {$foundUnit->item->name}");
            $this->line("  Price: \${$foundUnit->price}");
            $this->line("  Status: " . ($foundUnit->quantity > 0 ? 'Available' : 'Sold'));
        }

        // Demo 8: Inventory Summary
        $this->newLine();
        $this->comment('📈 Demo 8: Inventory Summary Report');

        $summary = $inventoryService->getInventorySummary($item);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Item Name', $summary['item_name']],
                ['Total Units', $summary['total_units']],
                ['Available Units', $summary['available_units']],
                ['Sold Units', $summary['sold_units']],
                ['Stock Percentage', $summary['stock_percentage'] . '%'],
            ]
        );

        // Final Summary
        $this->newLine();
        $this->info('=== Demo Complete! ===');
        $this->line('All InventoryService methods demonstrated successfully.');

        return self::SUCCESS;
    }
}
