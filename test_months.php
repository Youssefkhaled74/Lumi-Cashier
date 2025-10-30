<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Day;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

echo "🔍 Testing Months Feature\n";
echo str_repeat("=", 50) . "\n\n";

// Get unique months from days table
$months = Day::select(
    DB::raw("strftime('%Y', date) as year"),
    DB::raw("strftime('%m', date) as month"),
    DB::raw('COUNT(*) as total_days')
)
->groupBy(DB::raw("strftime('%Y', date)"), DB::raw("strftime('%m', date)"))
->orderBy('year', 'desc')
->orderBy('month', 'desc')
->get();

echo "📅 Available Months: " . $months->count() . "\n\n";

foreach ($months as $month) {
    $monthName = \Carbon\Carbon::create($month->year, $month->month, 1)->format('F Y');
    echo "Month: {$monthName}\n";
    echo "  Total Days: {$month->total_days}\n";
    
    // Get statistics for this month
    $startDate = \Carbon\Carbon::create($month->year, $month->month, 1)->startOfMonth();
    $endDate = \Carbon\Carbon::create($month->year, $month->month, 1)->endOfMonth();
    
    $totalOrders = Order::whereHas('day', function ($query) use ($startDate, $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
    })->count();
    
    $totalSales = Order::whereHas('day', function ($query) use ($startDate, $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
    })->where('status', Order::STATUS_COMPLETED)->sum('total');
    
    echo "  Total Orders: {$totalOrders}\n";
    echo "  Total Sales: \${$totalSales}\n";
    echo "\n";
}

echo "✅ Months feature is ready!\n";
echo "\nℹ️  Access at: http://localhost/Lumi/public/admin/months\n";
