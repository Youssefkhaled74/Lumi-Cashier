<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthController extends Controller
{
    /**
     * Display a list of months with statistics
     */
    public function index()
    {
        // Get all unique months from the days table (SQLite compatible)
        $months = Day::select(
            DB::raw("strftime('%Y', date) as year"),
            DB::raw("strftime('%m', date) as month"),
            DB::raw('COUNT(*) as total_days'),
            DB::raw('SUM(CASE WHEN closed_at IS NOT NULL THEN 1 ELSE 0 END) as closed_days'),
            DB::raw('SUM(CASE WHEN closed_at IS NULL THEN 1 ELSE 0 END) as open_days')
        )
        ->groupBy(DB::raw("strftime('%Y', date)"), DB::raw("strftime('%m', date)"))
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get()
        ->map(function ($month) {
            // Get orders statistics for this month
            $startDate = Carbon::create($month->year, $month->month, 1)->startOfMonth();
            $endDate = Carbon::create($month->year, $month->month, 1)->endOfMonth();
            
            $orders = Order::whereHas('day', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            });
            
            $month->total_orders = $orders->count();
            $month->completed_orders = $orders->where('status', Order::STATUS_COMPLETED)->count();
            $month->total_sales = $orders->where('status', Order::STATUS_COMPLETED)->sum('total');
            $month->month_name = Carbon::create($month->year, $month->month, 1)->format('F');
            $month->formatted_date = Carbon::create($month->year, $month->month, 1)->format('F Y');
            
            return $month;
        });

        return view('admin.months.index', compact('months'));
    }

    /**
     * Show specific month details with all days
     */
    public function show($year, $month)
    {
        // Validate month and year
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            abort(404);
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Get all days in this month
        $days = Day::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($day) {
                $day->total_orders = $day->orders()->count();
                $day->completed_orders = $day->orders()->where('status', Order::STATUS_COMPLETED)->count();
                $day->total_sales = $day->orders()->where('status', Order::STATUS_COMPLETED)->sum('total');
                return $day;
            });

        // Calculate month statistics
        $monthStats = [
            'total_days' => $days->count(),
            'open_days' => $days->where('closed_at', null)->count(),
            'closed_days' => $days->where('closed_at', '!=', null)->count(),
            'total_orders' => $days->sum('total_orders'),
            'completed_orders' => $days->sum('completed_orders'),
            'total_sales' => $days->sum('total_sales'),
            'average_daily_sales' => $days->count() > 0 ? $days->sum('total_sales') / $days->count() : 0,
        ];

        $monthName = $startDate->format('F Y');

        return view('admin.months.show', compact('days', 'monthStats', 'monthName', 'year', 'month'));
    }
}
