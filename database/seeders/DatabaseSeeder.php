<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
            DaysTableSeeder::class,
            // OrdersTableSeeder::class, // Skip for now - requires item_units setup
        ]);

        $this->command->info('✅ Database seeded successfully with clothing store data!');
        $this->command->info('📦 Categories: 8 (Men\'s, Women\'s, Kids, Shoes, Accessories, Sportswear, Winter, Summer)');
        $this->command->info('👕 Items: 42 products with realistic prices');
        $this->command->info('📅 Days: 8 days (7 closed + 1 open)');
        $this->command->info(' Users: 3 users (admin@lumi.com, ahmed@lumi.com, sara@lumi.com)');
        $this->command->info('🔑 Default password for all users: password');
        $this->command->info('⚠️  Note: Orders seeding skipped - requires item_units configuration');
    }
}


