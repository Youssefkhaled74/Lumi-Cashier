<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the reports page with filters.
     */
    public function index(Request $request): View
    {
        // Default date range: last 30 days
        $fromDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        // If generate was not requested, show empty state
        if (!$request->has('generate')) {
            return view('admin.reports.index', [
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'reportData' => null,
            ]);
        }

        // Generate report
        $reportData = $this->generate($fromDate, $toDate);

        return view('admin.reports.index', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportData' => $reportData,
        ]);
    }

    /**
     * Generate report data for the specified date range.
     */
    private function generate(string $fromDate, string $toDate): array
    {
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        // Get all completed orders within date range
        $orders = Order::with(['items.itemUnit.item.category'])
            ->where('status', Order::STATUS_COMPLETED)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        // Total statistics
        $totalOrders = $orders->count();
        $totalSales = $orders->sum('total');

        // Daily sales breakdown
        $dailySales = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($dayOrders) {
            return [
                'date' => $dayOrders->first()->created_at->format('M d'),
                'total' => $dayOrders->sum('total'),
                'count' => $dayOrders->count(),
            ];
        })->values();

        // Top selling items (with null safety)
        $topSellingItems = OrderItem::with('itemUnit.item')
            ->whereHas('order', function ($query) use ($from, $to) {
                $query->where('status', Order::STATUS_COMPLETED)
                    ->whereBetween('created_at', [$from, $to]);
            })
            ->select('item_unit_id', DB::raw('COUNT(*) as total_sold'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('item_unit_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get()
            ->map(function ($orderItem) {
                $item = $orderItem->itemUnit?->item;
                return [
                    'name' => $item?->name ?? 'Unknown',
                    'sku' => $item?->sku ?? 'N/A',
                    'quantity_sold' => $orderItem->total_sold,
                    'revenue' => $orderItem->total_revenue,
                ];
            });

        // Category distribution (with null safety)
        $categoryDistribution = $orders->flatMap(function ($order) {
            return $order->items;
        })->groupBy(function ($orderItem) {
            return $orderItem->itemUnit?->item?->category?->name ?? 'Uncategorized';
        })->map(function ($items, $categoryName) {
            return [
                'category' => $categoryName,
                'count' => $items->count(),
                'revenue' => $items->sum('total'),
            ];
        })->values();

        // Current inventory status
        $inventoryStatus = Item::with('category')
            ->withCount(['units as available_units' => function ($query) {
                $query->where('status', ItemUnit::STATUS_AVAILABLE);
            }])
            ->withCount(['units as sold_units' => function ($query) {
                $query->where('status', ItemUnit::STATUS_SOLD);
            }])
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'category' => $item->category->name ?? 'N/A',
                    'price' => $item->price,
                    'available' => $item->available_units,
                    'sold' => $item->sold_units,
                    'total' => $item->available_units + $item->sold_units,
                    'inventory_value' => $item->price * $item->available_units,
                ];
            })
            ->sortByDesc('available');

        // Low stock alerts (items with less than 5 units)
        $lowStockItems = $inventoryStatus->filter(function ($item) {
            return $item['available'] > 0 && $item['available'] < 5;
        })->values();

        // Out of stock items
        $outOfStockItems = $inventoryStatus->filter(function ($item) {
            return $item['available'] == 0;
        })->values();

        return [
            'summary' => [
                'total_orders' => $totalOrders,
                'total_sales' => $totalSales,
                'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
                'date_range' => [
                    'from' => $from->format('M d, Y'),
                    'to' => $to->format('M d, Y'),
                    'days' => (int) ($from->diffInDays($to) + 1),
                ],
            ],
            'daily_sales' => $dailySales,
            'top_selling_items' => $topSellingItems,
            'category_distribution' => $categoryDistribution,
            'inventory' => [
                'all_items' => $inventoryStatus,
                'low_stock' => $lowStockItems,
                'out_of_stock' => $outOfStockItems,
                'total_inventory_value' => $inventoryStatus->sum('inventory_value'),
            ],
        ];
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        // Generate report data
        $reportData = $this->generate($fromDate, $toDate);

        // Get orders for detailed listing
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();
        
        $orders = Order::with(['items.itemUnit.item', 'day'])
            ->where('status', Order::STATUS_COMPLETED)
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('admin.reports.pdf', [
            'reportData' => $reportData,
            'orders' => $orders,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'generatedAt' => Carbon::now(),
        ]);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Stream PDF (better for desktop app)
        $filename = 'report_' . $fromDate . '_to_' . $toDate . '.pdf';
        return $pdf->stream($filename);
    }
}
