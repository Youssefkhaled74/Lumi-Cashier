<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Item;

class ArabicTruncateSeeder extends Seeder
{
    /**
     * Truncate all non-migration tables and seed Arabic categories & items.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('Seeding with truncation is disabled in production.');
            return;
        }

        $this->command->info('Disabling foreign key checks (driver aware)...');
        // Detect driver to run appropriate FK-disable statement
        try {
            $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $e) {
            $driver = Schema::getConnection()->getDriverName() ?? 'mysql';
        }

        if ($driver === 'mysql' || $driver === 'mysqli') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } elseif ($driver === 'pgsql' || $driver === 'postgres') {
            DB::statement("SET session_replication_role = 'replica'");
        } else {
            // fallback - try MySQL style, catch errors later
            try { DB::statement('SET FOREIGN_KEY_CHECKS=0'); } catch (\Throwable $e) {}
        }

        // Get all table names - driver aware
        $tables = [];
        try {
            // Prefer Doctrine where available
            $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        } catch (\Throwable $e) {
            // Fallback per driver
            if ($driver === 'sqlite') {
                $result = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($result as $row) {
                    $tables[] = $row->name ?? array_values((array) $row)[0];
                }
            } elseif ($driver === 'pgsql' || $driver === 'postgres') {
                $result = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
                foreach ($result as $row) {
                    $tables[] = $row->tablename ?? array_values((array) $row)[0];
                }
            } else {
                $result = DB::select('SHOW TABLES');
                foreach ($result as $row) {
                    $tables[] = array_values((array) $row)[0];
                }
            }
        }

        // Exclude migrations and optionally other system tables
        $exclude = [
            'migrations',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens',
        ];

        foreach ($tables as $table) {
            if (in_array($table, $exclude)) {
                $this->command->info("Skipping table: $table");
                continue;
            }

            // Some tables may not be truncatable via DB::table if they don't exist
            try {
                if ($driver === 'sqlite') {
                    DB::table($table)->delete();
                    // Reset sqlite sequence if exists
                    try {
                        DB::statement("DELETE FROM sqlite_sequence WHERE name = ?", [$table]);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                } else {
                    DB::table($table)->truncate();
                }
                $this->command->info("Truncated: $table");
            } catch (\Throwable $e) {
                $this->command->warn("Could not truncate $table: " . $e->getMessage());
            }
        }

        // Re-enable foreign keys based on driver
        if ($driver === 'mysql' || $driver === 'mysqli') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } elseif ($driver === 'pgsql' || $driver === 'postgres') {
            DB::statement("SET session_replication_role = 'origin'");
        } else {
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $e) {}
        }
        $this->command->info('Foreign key checks re-enabled.');

        // Seed Arabic categories
        $this->command->info('Seeding Arabic categories...');

        $categories = [
            ['name' => 'المشروبات', 'slug' => Str::slug('المشروبات')],
            ['name' => 'المخبوزات', 'slug' => Str::slug('المخبوزات')],
            ['name' => 'الفواكه', 'slug' => Str::slug('الفواكه')],
            ['name' => 'الوجبات الخفيفة', 'slug' => Str::slug('الوجبات الخفيفة')],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[] = Category::create(array_merge($cat, [
                'description' => $cat['name'] . ' - تصنيف تجريبي بالعربية',
            ]));
        }

        $this->command->info('Seeding Arabic items...');

        $items = [
            ['name' => 'قهوة عربية', 'price' => 15.00, 'description' => 'قهوة طازجة عربية', 'category' => 'المشروبات'],
            ['name' => 'شاي نعناع', 'price' => 8.50, 'description' => 'شاي بالنعناع الطازج', 'category' => 'المشروبات'],
            ['name' => 'خبز فرنسي', 'price' => 5.00, 'description' => 'خبز طازج من الفرن', 'category' => 'المخبوزات'],
            ['name' => 'كرواسون', 'price' => 7.00, 'description' => 'كرواسون زبدة لذيذ', 'category' => 'المخبوزات'],
            ['name' => 'تفاح أحمر', 'price' => 4.00, 'description' => 'تفاح طازج', 'category' => 'الفواكه'],
            ['name' => 'سندويتش صغير', 'price' => 12.50, 'description' => 'سندويتش لذيذ للتحلية', 'category' => 'الوجبات الخفيفة'],
        ];

        foreach ($items as $it) {
            $cat = collect($createdCategories)->firstWhere('name', $it['category']);
            $data = [
                'category_id' => $cat ? $cat->id : null,
                'name' => $it['name'],
                'sku' => null,
                'barcode' => null,
                'description' => $it['description'],
                'price' => $it['price'],
            ];

            try {
                Item::create($data);
                $this->command->info("Created item: {$it['name']}");
            } catch (\Throwable $e) {
                $this->command->warn("Could not create item {$it['name']}: " . $e->getMessage());
            }
        }

        $this->command->info('✅ Arabic truncation + seeding completed.');
    }
}
