<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('days')->truncate();

        $days = [
            // Closed days from the past week
            [
                'date' => Carbon::now()->subDays(7)->toDateString(),
                'opened_at' => Carbon::now()->subDays(7)->setHour(9)->setMinute(0),
                'closed_at' => Carbon::now()->subDays(7)->setHour(21)->setMinute(30),
                'notes' => 'Regular business day',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'date' => Carbon::now()->subDays(6)->toDateString(),
                'opened_at' => Carbon::now()->subDays(6)->setHour(9)->setMinute(0),
                'closed_at' => Carbon::now()->subDays(6)->setHour(22)->setMinute(0),
                'notes' => 'Busy day - many customers',
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'date' => Carbon::now()->subDays(5)->toDateString(),
                'opened_at' => Carbon::now()->subDays(5)->setHour(9)->setMinute(0),
                'closed_at' => Carbon::now()->subDays(5)->setHour(21)->setMinute(45),
                'notes' => 'Good sales performance',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'date' => Carbon::now()->subDays(4)->toDateString(),
                'opened_at' => Carbon::now()->subDays(4)->setHour(9)->setMinute(0),
                'closed_at' => Carbon::now()->subDays(4)->setHour(22)->setMinute(15),
                'notes' => 'New stock arrived',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'date' => Carbon::now()->subDays(3)->toDateString(),
                'opened_at' => Carbon::now()->subDays(3)->setHour(9)->setMinute(0),
                'closed_at' => Carbon::now()->subDays(3)->setHour(21)->setMinute(20),
                'notes' => 'Regular business day',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'date' => Carbon::now()->subDays(2)->toDateString(),
                'opened_at' => Carbon::now()->subDays(2)->setHour(9)->setMinute(0),
                'closed_at' => Carbon::now()->subDays(2)->setHour(22)->setMinute(30),
                'notes' => 'Weekend - extra traffic',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'date' => Carbon::now()->subDays(1)->toDateString(),
                'opened_at' => Carbon::now()->subDays(1)->setHour(9)->setMinute(0),
                'closed_at' => Carbon::now()->subDays(1)->setHour(21)->setMinute(50),
                'notes' => 'Good customer feedback',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            // Current open day
            [
                'date' => Carbon::now()->toDateString(),
                'opened_at' => Carbon::now()->setHour(9)->setMinute(0),
                'closed_at' => null,
                'notes' => 'Day in progress',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('days')->insert($days);
    }
}

