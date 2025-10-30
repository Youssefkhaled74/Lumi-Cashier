<?php

namespace Database\Seeders;

use App\Models\Day;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DaySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Day::create([
            'date' => Carbon::now()->toDateString(),
            'opened_at' => Carbon::now(),
            'notes' => 'Seeded opening day',
        ]);
    }
}
