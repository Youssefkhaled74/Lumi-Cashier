<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Day;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Use the currently open day only — do not fallback to general orders by created_at
        $todayDay = Day::query()->current()->first();

        // Default to zero when no open day
        $todayOrders = 0;
        $todaySales = 0;

        if ($todayDay) {
            // Prefer Day model accessors which already filter to completed orders
            $todayOrders = $todayDay->total_orders;
            $todaySales = (float) $todayDay->total_sales;
        }

        return view('admin.dashboard', [
            'todayOrders' => $todayOrders,
            'todaySales' => $todaySales,
            'totalItems' => Item::count(),
            'totalCategories' => Category::count(),
        ]);
    }
}
