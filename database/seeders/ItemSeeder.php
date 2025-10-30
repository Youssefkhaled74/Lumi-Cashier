<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $beverages = Category::where('slug', 'beverages')->first();
        $groceries = Category::where('slug', 'groceries')->first();

        Item::create([
            'category_id' => $beverages->id,
            'name' => 'Bottled Water',
            'sku' => 'BW-001',
            'barcode' => $this->generateBarcode(),
            'description' => '500ml bottled water',
            'price' => 1.50,
        ]);

        Item::create([
            'category_id' => $groceries->id,
            'name' => 'Rice (5kg)',
            'sku' => 'RICE-5KG',
            'barcode' => $this->generateBarcode(),
            'description' => '5 kilogram rice bag',
            'price' => 12.00,
        ]);
    }

    /**
     * Generate a unique barcode for an item.
     */
    private function generateBarcode(): string
    {
        return 'ITEM-' . strtoupper(Str::random(8));
    }
}
