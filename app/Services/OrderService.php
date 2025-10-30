<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryService $inventoryService,
        private DayService $dayService
    ) {}

    /**
     * Create a new order with items.
     *
     * @param array $orderData
     * @return Order
     * @throws \Exception
     */
    public function createOrder(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            // Verify an open day exists
            $currentDay = $this->dayService->getCurrentOpenDay();
            
            if (!$currentDay) {
                throw new \Exception('No business day is currently open. Cannot create order.');
            }

            // Create the order
            $order = $this->orderRepository->create([
                'day_id' => $currentDay->id,
                'status' => Order::STATUS_PENDING,
                'payment_method' => $orderData['payment_method'] ?? Order::PAYMENT_CASH,
                'customer_name' => $orderData['customer_name'] ?? null,
                'customer_phone' => $orderData['customer_phone'] ?? null,
                'customer_email' => $orderData['customer_email'] ?? null,
                'discount_percentage' => $orderData['discount_percentage'] ?? 0,
                'tax_percentage' => $orderData['tax_percentage'] ?? config('cashier.tax.default_rate', 0),
                'notes' => $orderData['notes'] ?? null,
                'subtotal' => 0,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total' => 0,
            ]);

            $orderTotal = 0;

            // Process each item in the cart
            foreach ($orderData['items'] as $itemData) {
                $item = app(\App\Repositories\Contracts\ItemRepositoryInterface::class)->find($itemData['item_id']);
                
                if (!$item) {
                    throw new \Exception("Item not found: {$itemData['item_id']}");
                }

                $quantity = (int) $itemData['quantity'];

                // Check stock availability
                $availableStock = $this->inventoryService->getAvailableStock($item);

                if ($availableStock < $quantity) {
                    throw new \Exception(
                        "Out of stock for item '{$item->name}'. Requested: {$quantity}, Available: {$availableStock}"
                    );
                }

                // Mark units as sold
                $soldUnits = $this->inventoryService->decreaseStock($item, $quantity, $order->id);

                // Create order items
                foreach ($soldUnits as $unit) {
                    $itemDiscountPercentage = $itemData['discount_percentage'] ?? 0;
                    
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'item_unit_id' => $unit->id,
                        'quantity' => 1,
                        'price' => $unit->price,
                        'discount_percentage' => $itemDiscountPercentage,
                        'discount_amount' => 0, // Will be calculated automatically
                        'total' => $unit->price, // Will be calculated automatically with discount
                    ]);

                    $orderTotal += $orderItem->total;
                }
            }

            // Update order totals with discount and tax
            $order->subtotal = $orderTotal;
            $order->calculateTotal(); // This will calculate discount and tax
            $order->status = Order::STATUS_COMPLETED;
            $order->save();

            return $order->fresh(['day', 'items.itemUnit.item.category']);
        });
    }

    /**
     * Cancel an order and restore stock.
     *
     * @param int $orderId
     * @return bool
     * @throws \Exception
     */
    public function cancelOrder(int $orderId): bool
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->orderRepository->find($orderId);

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            if ($order->is_cancelled) {
                throw new \Exception('Order is already cancelled.');
            }

            if (!$order->is_completed) {
                throw new \Exception('Only completed orders can be cancelled.');
            }

            // Restore stock
            $this->inventoryService->restoreUnits($orderId);

            // Mark order as cancelled
            $order->markAsCancelled();

            return true;
        });
    }
}
