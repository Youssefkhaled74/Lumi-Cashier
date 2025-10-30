<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Drinks and beverages'],
            ['name' => 'Groceries', 'slug' => 'groceries', 'description' => 'Daily groceries'],
            ['name' => 'Household', 'slug' => 'household', 'description' => 'Household items'],
        ];

        foreach ($categories as $data) {
            Category::create($data);
        }
    }
}
