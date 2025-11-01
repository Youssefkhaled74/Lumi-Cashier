<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DeleteAllOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inform the user
        $this->command?->getOutput()->writeln('<comment>Deleting all orders and related order items (including soft-deleted)...</comment>');

        DB::transaction(function () {
            // Force delete all orders (including soft deleted ones).
            // order_items have cascade delete on order_id, so they will be removed.
            Order::query()->withTrashed()->forceDelete();
        });

        $this->command?->getOutput()->writeln('<info>All orders have been deleted.</info>');
    }
}
