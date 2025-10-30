<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();

        $orders = [];
        $orderItems = [];
        $orderId = 1;

        // Item prices for calculations
        $itemPrices = [
            1 => 299, 2 => 450, 3 => 850, 4 => 199, 5 => 399,
            6 => 599, 7 => 1200, 8 => 399, 9 => 499, 10 => 149, 11 => 349,
            12 => 250, 13 => 350, 14 => 499, 15 => 299,
            16 => 699, 17 => 599, 18 => 449, 19 => 350, 20 => 249,
            21 => 899, 22 => 299, 23 => 399, 24 => 199, 25 => 549, 26 => 799, 27 => 299,
            28 => 149, 29 => 249, 30 => 179, 31 => 199, 32 => 199,
            33 => 1299, 34 => 399, 35 => 899, 36 => 179, 37 => 129,
            38 => 349, 39 => 199, 40 => 149, 41 => 99, 42 => 299,
        ];

        // Generate orders for the past 7 days + today
        for ($dayOffset = 7; $dayOffset >= 0; $dayOffset--) {
            $dayId = 8 - $dayOffset;
            $date = Carbon::now()->subDays($dayOffset);
            
            // Generate 15-20 orders per day
            $ordersPerDay = rand(15, 20);
            
            for ($i = 0; $i < $ordersPerDay; $i++) {
                $hour = rand(9, 21);
                $minute = rand(0, 59);
                $orderDate = $date->copy()->setHour($hour)->setMinute($minute);
                
                $total = 0;
                
                // Add 1-4 items per order
                $itemsCount = rand(1, 4);
                $itemIds = array_rand($itemPrices, min($itemsCount, count($itemPrices)));
                if (!is_array($itemIds)) $itemIds = [$itemIds];
                
                foreach ($itemIds as $itemId) {
                    $quantity = rand(1, 3);
                    $price = $itemPrices[$itemId];
                    $itemTotal = $price * $quantity;
                    $total += $itemTotal;
                    
                    $orderItems[] = [
                        'order_id' => $orderId,
                        'item_id' => $itemId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ];
                }
                
                // Create order
                $orders[] = [
                    'id' => $orderId,
                    'day_id' => $dayId,
                    'status' => 'completed',
                    'total' => $total,
                    'notes' => 'Sale #' . $orderId,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ];
                
                $orderId++;
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($orders, 50) as $chunk) {
            DB::table('orders')->insert($chunk);
        }
        
        foreach (array_chunk($orderItems, 100) as $chunk) {
            DB::table('order_items')->insert($chunk);
        }
    }
}
