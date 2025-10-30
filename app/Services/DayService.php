<?php

namespace App\Services;

use App\Models\Day;
use Illuminate\Support\Facades\DB;

class DayService
{
    /**
     * Get the current open day.
     */
    public function getCurrentOpenDay(): ?Day
    {
        return Day::query()->open()->first();
    }

    /**
     * Open a new business day.
     *
     * @return Day
     * @throws \Exception
     */
    public function openDay(): Day
    {
        return DB::transaction(function () {
            // Check if there's already an open day
            $openDay = $this->getCurrentOpenDay();

            if ($openDay) {
                throw new \Exception('A business day is already open. Please close it before opening a new one.');
            }

            // Check if today's day record exists (but is closed)
            $todayDay = Day::whereDate('date', today())->first();

            if ($todayDay) {
                // Reopen the existing closed day
                if ($todayDay->closed_at) {
                    $todayDay->update([
                        'opened_at' => now(),
                        'closed_at' => null,
                    ]);
                    return $todayDay->fresh();
                }
                
                // Day exists and is already open (shouldn't happen but handle it)
                return $todayDay;
            }

            // Create new day for today
            return Day::create([
                'date' => now()->toDateString(),
                'opened_at' => now(),
            ]);
        });
    }

    /**
     * Close the current business day.
     *
     * @return Day
     * @throws \Exception
     */
    public function closeDay(): Day
    {
        return DB::transaction(function () {
            $currentDay = Day::query()->open()->lockForUpdate()->first();

            if (!$currentDay) {
                throw new \Exception('No business day is currently open.');
            }

            // Check for pending orders
            $pendingOrders = $currentDay->orders()->where('status', 'pending')->count();

            if ($pendingOrders > 0) {
                throw new \Exception("Cannot close day with {$pendingOrders} pending order(s). Please complete or cancel them first.");
            }

            // Close the day
            $currentDay->update(['closed_at' => now()]);

            return $currentDay->fresh();
        });
    }

    /**
     * Get day summary statistics.
     *
     * @param int|Day $day
     * @return array
     */
    public function getDaySummary(int|Day $day): array
    {
        // If integer ID is passed, fetch the Day model
        if (is_int($day)) {
            $day = Day::findOrFail($day);
        }
        
        return [
            'day_id' => $day->id,
            'date' => $day->date->format('Y-m-d'),
            'status' => $day->is_open ? 'open' : 'closed',
            'opened_at' => $day->opened_at?->format('Y-m-d H:i:s'),
            'closed_at' => $day->closed_at?->format('Y-m-d H:i:s'),
            'total_orders' => $day->total_orders,
            'total_sales' => (float) $day->total_sales,
        ];
    }
}
