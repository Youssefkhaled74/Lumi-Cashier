<?php

namespace App\Http\Controllers;

use App\Services\DayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DayController extends Controller
{
    public function __construct(
        private DayService $dayService
    ) {}

    /**
     * Display a listing of all business days.
     */
    public function index(): View
    {
        $days = \App\Models\Day::orderBy('date', 'desc')->paginate(20);

        return view('admin.days.index', compact('days'));
    }

    /**
     * Show the current day status.
     */
    public function showDayStatus(): View
    {
        $currentDay = $this->dayService->getCurrentOpenDay();
        $stats = [];

        if ($currentDay) {
            $summary = $this->dayService->getDaySummary($currentDay->id);
            $stats = [
                'total_sales' => $summary['total_sales'],
                'total_orders' => $summary['total_orders'],
                'duration' => $currentDay->duration_in_hours,
                'is_open' => $currentDay->is_open,
            ];
        }

        return view('admin.day-status', [
            'day' => $currentDay,
            'stats' => $stats,
        ]);
    }

    /**
     * Open a new business day.
     */
    public function openDay(Request $request): RedirectResponse
    {
        try {
            $day = $this->dayService->openDay();

            return back()->with('success', 'Business day opened successfully at ' . $day->opened_at->format('h:i A'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Close the current business day.
     */
    public function closeDay(Request $request): RedirectResponse
    {
        try {
            $currentDay = $this->dayService->closeDay();
            $summary = $this->dayService->getDaySummary($currentDay->id);

            $totalSales = $summary['total_sales'];
            $totalOrders = $summary['total_orders'];

            return back()->with('success', "Business day closed successfully. Total Sales: \${$totalSales}, Total Orders: {$totalOrders}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get day summary (API endpoint).
     */
    public function summary(Request $request)
    {
        $currentDay = $this->dayService->getCurrentOpenDay();

        if (!$currentDay) {
            return response()->json([
                'status' => 'closed',
                'message' => 'No active business day',
            ]);
        }

        $summary = $this->dayService->getDaySummary($currentDay->id);

        return response()->json([
            'status' => 'open',
            'day' => [
                'id' => $currentDay->id,
                'date' => $currentDay->date->format('Y-m-d'),
                'opened_at' => $currentDay->opened_at->format('Y-m-d H:i:s'),
                'total_sales' => $summary['total_sales'],
                'total_orders' => $summary['total_orders'],
                'duration_hours' => $currentDay->duration_in_hours,
            ],
        ]);
    }
}
