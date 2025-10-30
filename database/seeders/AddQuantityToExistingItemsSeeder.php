<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddQuantityToExistingItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder adds stock/quantity (ItemUnits) to all existing items.
     */
    public function run(): void
    {
        // Get all items that exist in the database
        $items = Item::all();

        if ($items->isEmpty()) {
            $this->command->warn('⚠️  No items found in the database!');
            $this->command->info('💡 Please run ItemSeeder first to create items.');
            return;
        }

        $this->command->info("📦 Found {$items->count()} items. Adding stock quantities...");

        $totalUnitsAdded = 0;

        foreach ($items as $item) {
            // Random quantity between 10 and 100 for each item
            $stockQuantity = rand(10, 100);

            // Create ItemUnits for this item
            // Each ItemUnit represents 1 physical unit with quantity
            // We'll create multiple units to represent the total stock
            
            // Option 1: Create a single ItemUnit with the total quantity
            ItemUnit::create([
                'item_id' => $item->id,
                'quantity' => $stockQuantity,
                'price' => $item->price,
                'status' => ItemUnit::STATUS_AVAILABLE,
            ]);

            $totalUnitsAdded++;

            $this->command->info("  ✅ {$item->name}: Added {$stockQuantity} units");
        }

        $this->command->info('');
        $this->command->info("✨ Successfully added stock to {$totalUnitsAdded} items!");
        $this->command->info("🎉 Total items with inventory: {$items->count()}");
    }
}
