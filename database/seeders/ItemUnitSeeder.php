<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemUnitSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $items = Item::all();

        foreach ($items as $item) {
            // create several units for each item (quantity represents available stock)
            for ($i = 0; $i < 3; $i++) {
                ItemUnit::create([
                    'item_id' => $item->id,
                    'quantity' => rand(5, 50),
                    'price' => $item->price,
                    'status' => ItemUnit::STATUS_AVAILABLE,
                ]);
            }
        }
    }
}
