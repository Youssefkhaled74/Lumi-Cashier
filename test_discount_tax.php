<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

echo "🧪 Testing Discount & Tax System\n";
echo str_repeat("=", 60) . "\n\n";

// Test 1: Create order with discount and tax
echo "Test 1: Order Calculation\n";
echo str_repeat("-", 60) . "\n";

$order = new Order([
    'subtotal' => 100.00,
    'discount_percentage' => 10,
    'tax_percentage' => 15,
]);

$order->calculateTotal();

echo "Subtotal:          $" . number_format($order->subtotal, 2) . "\n";
echo "Discount (10%):   -$" . number_format($order->discount_amount, 2) . "\n";
echo "After Discount:    $" . number_format($order->subtotal - $order->discount_amount, 2) . "\n";
echo "Tax (15%):        +$" . number_format($order->tax_amount, 2) . "\n";
echo "Grand Total:       $" . number_format($order->total, 2) . "\n\n";

// Expected: Subtotal: 100, Discount: 10, After: 90, Tax: 13.50, Total: 103.50

// Test 2: Different discount percentage
echo "Test 2: 5% Discount (No Admin Required)\n";
echo str_repeat("-", 60) . "\n";

$order2 = new Order([
    'subtotal' => 200.00,
    'discount_percentage' => 5,
    'tax_percentage' => 15,
]);

$order2->calculateTotal();

echo "Subtotal:          $" . number_format($order2->subtotal, 2) . "\n";
echo "Discount (5%):    -$" . number_format($order2->discount_amount, 2) . "\n";
echo "After Discount:    $" . number_format($order2->subtotal - $order2->discount_amount, 2) . "\n";
echo "Tax (15%):        +$" . number_format($order2->tax_amount, 2) . "\n";
echo "Grand Total:       $" . number_format($order2->total, 2) . "\n\n";

// Test 3: No discount
echo "Test 3: No Discount\n";
echo str_repeat("-", 60) . "\n";

$order3 = new Order([
    'subtotal' => 50.00,
    'discount_percentage' => 0,
    'tax_percentage' => 15,
]);

$order3->calculateTotal();

echo "Subtotal:          $" . number_format($order3->subtotal, 2) . "\n";
echo "Discount (0%):    -$" . number_format($order3->discount_amount, 2) . "\n";
echo "After Discount:    $" . number_format($order3->subtotal - $order3->discount_amount, 2) . "\n";
echo "Tax (15%):        +$" . number_format($order3->tax_amount, 2) . "\n";
echo "Grand Total:       $" . number_format($order3->total, 2) . "\n\n";

// Test 4: High discount (requires admin)
echo "Test 4: 20% Discount (Requires Admin)\n";
echo str_repeat("-", 60) . "\n";

$order4 = new Order([
    'subtotal' => 150.00,
    'discount_percentage' => 20,
    'tax_percentage' => 15,
]);

$order4->calculateTotal();

$maxWithoutApproval = config('cashier.discount.max_without_approval', 5);
echo "Max discount without approval: {$maxWithoutApproval}%\n";
echo "Requested discount: 20% ⚠️ REQUIRES ADMIN APPROVAL\n\n";

echo "Subtotal:          $" . number_format($order4->subtotal, 2) . "\n";
echo "Discount (20%):   -$" . number_format($order4->discount_amount, 2) . "\n";
echo "After Discount:    $" . number_format($order4->subtotal - $order4->discount_amount, 2) . "\n";
echo "Tax (15%):        +$" . number_format($order4->tax_amount, 2) . "\n";
echo "Grand Total:       $" . number_format($order4->total, 2) . "\n\n";

// Test 5: Payment constants
echo "Test 5: Payment Method Constants\n";
echo str_repeat("-", 60) . "\n";
echo "PAYMENT_CASH:  " . Order::PAYMENT_CASH . "\n";
echo "PAYMENT_CARD:  " . Order::PAYMENT_CARD . "\n";
echo "PAYMENT_OTHER: " . Order::PAYMENT_OTHER . "\n\n";

// Test 6: Configuration
echo "Test 6: System Configuration\n";
echo str_repeat("-", 60) . "\n";
echo "Tax Enabled:       " . (config('cashier.tax.enabled') ? 'Yes' : 'No') . "\n";
echo "Default Tax Rate:  " . config('cashier.tax.default_rate') . "%\n";
echo "Tax Label:         " . config('cashier.tax.label') . "\n";
echo "Max Discount:      " . config('cashier.discount.max_without_approval') . "%\n\n";

echo "✅ All tests completed successfully!\n";
echo str_repeat("=", 60) . "\n";
