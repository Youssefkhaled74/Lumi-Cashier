<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('categories')->truncate();

        $categories = [
            [
                'name' => 'Men\'s Clothing',
                'slug' => 'mens-clothing',
                'description' => 'Clothing and apparel for men - shirts, pants, suits, and more',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Women\'s Clothing',
                'slug' => 'womens-clothing',
                'description' => 'Fashion and clothing for women - dresses, tops, skirts, and more',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kids Clothing',
                'slug' => 'kids-clothing',
                'description' => 'Children\'s clothing and apparel for all ages',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shoes & Footwear',
                'slug' => 'shoes-footwear',
                'description' => 'Footwear for men, women, and children',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Fashion accessories - bags, belts, wallets, jewelry, and more',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sportswear',
                'slug' => 'sportswear',
                'description' => 'Athletic and sports clothing for active lifestyle',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Winter Collection',
                'slug' => 'winter-collection',
                'description' => 'Winter clothing - jackets, coats, sweaters, and warm wear',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Summer Collection',
                'slug' => 'summer-collection',
                'description' => 'Light and comfortable summer clothing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}
