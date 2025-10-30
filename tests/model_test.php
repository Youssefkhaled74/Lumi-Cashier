<?php

/**
 * Test script to demonstrate Eloquent models and relationships.
 * Run with: php artisan tinker < tests/model_test.php
 * Or manually test relationships in tinker.
 */

use App\Models\{Category, Item, ItemUnit, Order, OrderItem, Day};

// Test 1: Category relationships
echo "\n=== Test 1: Category Relationships ===\n";
$category = Category::with('items')->first();
echo "Category: {$category->name}\n";
echo "Items count: {$category->items->count()}\n";
echo "Total items (accessor): {$category->total_items}\n";

// Test 2: Item relationships and accessors
echo "\n=== Test 2: Item Relationships & Accessors ===\n";
$item = Item::with(['category', 'units'])->first();
echo "Item: {$item->name}\n";
echo "Category: {$item->category->name}\n";
echo "Units count: {$item->units->count()}\n";
echo "Total quantity: {$item->total_quantity}\n";
echo "Inventory value: \${$item->inventory_value}\n";
echo "In stock: " . ($item->is_in_stock ? 'Yes' : 'No') . "\n";

// Test 3: ItemUnit methods
echo "\n=== Test 3: ItemUnit Methods ===\n";
$unit = ItemUnit::with('item')->first();
echo "Barcode: {$unit->barcode}\n";
echo "Item: {$unit->item->name}\n";
echo "Quantity: {$unit->quantity}\n";
echo "Available quantity: {$unit->available_quantity}\n";
echo "Is available: " . ($unit->is_available ? 'Yes' : 'No') . "\n";

// Test 4: Create an order
echo "\n=== Test 4: Create Order ===\n";
$day = Day::getOrCreateToday();
echo "Day: {$day->date->format('Y-m-d')}\n";
echo "Day is open: " . ($day->is_open ? 'Yes' : 'No') . "\n";

$order = Order::create([
    'day_id' => $day->id,
    'status' => Order::STATUS_PENDING,
    'notes' => 'Test order from model test',
]);
echo "Order created: #{$order->id}\n";

// Add items to order
$itemUnit = ItemUnit::inStock()->first();
$orderItem = OrderItem::create([
    'order_id' => $order->id,
    'item_unit_id' => $itemUnit->id,
    'quantity' => 2,
    'price' => $itemUnit->price,
]);
echo "Order item added: {$orderItem->item_name} x {$orderItem->quantity}\n";
echo "Order item total: \${$orderItem->total}\n";

// Decrease stock
$itemUnit->decreaseQuantity(2);
echo "Stock decreased. New quantity: {$itemUnit->quantity}\n";

// Calculate order total
$order->calculateTotal();
echo "Order total: \${$order->total}\n";

// Complete order
$order->markAsCompleted();
echo "Order status: {$order->status}\n";
echo "Order is completed: " . ($order->is_completed ? 'Yes' : 'No') . "\n";

// Test 5: Day reporting
echo "\n=== Test 5: Day Reporting ===\n";
$day->refresh();
echo "Total sales: \${$day->total_sales}\n";
echo "Total orders: {$day->total_orders}\n";
echo "Day duration: {$day->duration_in_hours} hours\n";

// Test 6: Scopes
echo "\n=== Test 6: Query Scopes ===\n";
echo "Items in stock: " . Item::inStock()->count() . "\n";
echo "Units in stock: " . ItemUnit::inStock()->count() . "\n";
echo "Completed orders: " . Order::completed()->count() . "\n";
echo "Pending orders: " . Order::pending()->count() . "\n";
echo "Open days: " . Day::open()->count() . "\n";

echo "\n=== All Tests Completed Successfully! ===\n\n";
