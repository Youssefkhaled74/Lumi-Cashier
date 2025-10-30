<?php

namespace App\Console\Commands;

use App\Models\{Category, Item, ItemUnit, Order, OrderItem, Day};
use Illuminate\Console\Command;

class TestModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Eloquent models and relationships';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Test 1: Category Relationships ===');
        $category = Category::with('items')->first();
        $this->line("Category: {$category->name}");
        $this->line("Items count: {$category->items->count()}");
        $this->line("Total items (accessor): {$category->total_items}");

        $this->newLine();
        $this->info('=== Test 2: Item Relationships & Accessors ===');
        $item = Item::with(['category', 'units'])->first();
        $this->line("Item: {$item->name}");
        $this->line("Category: {$item->category->name}");
        $this->line("Units count: {$item->units->count()}");
        $this->line("Total quantity: {$item->total_quantity}");
        $this->line("Inventory value: \${$item->inventory_value}");
        $this->line("In stock: " . ($item->is_in_stock ? 'Yes' : 'No'));

        $this->newLine();
        $this->info('=== Test 3: ItemUnit Methods ===');
        $unit = ItemUnit::with('item')->first();
        $this->line("Barcode: {$unit->barcode}");
        $this->line("Item: {$unit->item->name}");
        $this->line("Quantity: {$unit->quantity}");
        $this->line("Available quantity: {$unit->available_quantity}");
        $this->line("Is available: " . ($unit->is_available ? 'Yes' : 'No'));

        $this->newLine();
        $this->info('=== Test 4: Create Order ===');
        $day = Day::getOrCreateToday();
        $this->line("Day: {$day->date->format('Y-m-d')}");
        $this->line("Day is open: " . ($day->is_open ? 'Yes' : 'No'));

        $order = Order::create([
            'day_id' => $day->id,
            'status' => Order::STATUS_PENDING,
            'notes' => 'Test order from model test',
        ]);
        $this->line("Order created: #{$order->id}");

        // Add items to order
        $itemUnit = ItemUnit::inStock()->first();
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'item_unit_id' => $itemUnit->id,
            'quantity' => 2,
            'price' => $itemUnit->price,
        ]);
        $this->line("Order item added: {$orderItem->item_name} x {$orderItem->quantity}");
        $this->line("Order item total: \${$orderItem->total}");

        // Decrease stock
        $itemUnit->decreaseQuantity(2);
        $this->line("Stock decreased. New quantity: {$itemUnit->quantity}");

        // Calculate order total
        $order->calculateTotal();
        $this->line("Order total: \${$order->total}");

        // Complete order
        $order->markAsCompleted();
        $this->line("Order status: {$order->status}");
        $this->line("Order is completed: " . ($order->is_completed ? 'Yes' : 'No'));

        $this->newLine();
        $this->info('=== Test 5: Day Reporting ===');
        $day->refresh();
        $this->line("Total sales: \${$day->total_sales}");
        $this->line("Total orders: {$day->total_orders}");
        $this->line("Day duration: {$day->duration_in_hours} hours");

        $this->newLine();
        $this->info('=== Test 6: Query Scopes ===');
        $this->line("Items in stock: " . Item::inStock()->count());
        $this->line("Units in stock: " . ItemUnit::inStock()->count());
        $this->line("Completed orders: " . Order::completed()->count());
        $this->line("Pending orders: " . Order::pending()->count());
        $this->line("Open days: " . Day::query()->open()->count());

        $this->newLine();
        $this->info('=== All Tests Completed Successfully! ===');

        return self::SUCCESS;
    }
}
