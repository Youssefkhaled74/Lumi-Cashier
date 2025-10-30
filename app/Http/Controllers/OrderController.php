<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Day;
use App\Models\Order;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ItemRepositoryInterface $itemRepository,
        private OrderService $orderService
    ) {}

    /**
     * Display a listing of orders.
     */
    public function index(): View
    {
        $orders = $this->orderRepository->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): View
    {
        // Check if day is open
        $currentDay = Day::query()->open()->first();

        if (!$currentDay) {
            return view('admin.orders.create', [
                'error' => 'No business day is currently open. Please open a day first.',
                'items' => collect([]),
                'categories' => collect([]),
                'mostOrderedItems' => collect([]),
            ]);
        }

        $items = $this->itemRepository->getInStock();

        // Get all categories that have items
        $categories = \App\Models\Category::whereHas('items')->with('items')->get();

        // Get most ordered items (top 10 based on order_items quantity)
        $mostOrderedItems = \App\Models\Item::query()
            ->select('items.*')
            ->selectRaw('SUM(order_items.quantity) as total_ordered')
            ->join('item_units', 'items.id', '=', 'item_units.item_id')
            ->join('order_items', 'item_units.id', '=', 'order_items.item_unit_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->groupBy('items.id')
            ->orderByDesc('total_ordered')
            ->with(['category', 'units' => function($query) {
                $query->where('status', \App\Models\ItemUnit::STATUS_AVAILABLE);
            }])
            ->limit(10)
            ->get();

        return view('admin.orders.create', [
            'items' => $items,
            'currentDay' => $currentDay,
            'categories' => $categories,
            'mostOrderedItems' => $mostOrderedItems,
        ]);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            return redirect()->route('orders.show', $order)
                ->with('success', "Order #{$order->id} created successfully! Total: \${$order->total}");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load(['day', 'items.itemUnit.item.category']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cancel an order and restore stock.
     */
    public function cancel(Order $order): RedirectResponse
    {
        try {
            $this->orderService->cancelOrder($order->id);

            return back()->with('success', 'Order cancelled successfully. Stock has been restored.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate and download PDF invoice for an order.
     */
    public function invoice(Order $order): Response
    {
        // Load all necessary relationships
        $order->load(['day', 'items.itemUnit.item.category']);

        // Get company information from config
        $company = config('cashier.company', [
            'name' => 'Lumi POS',
            'address' => '123 Main Street',
            'city' => 'Your City, State 12345',
            'phone' => '+1 (555) 123-4567',
            'email' => 'info@lumipos.com',
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.show', [
            'order' => $order,
            'company' => $company,
        ]);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Download the PDF with a custom filename
        return $pdf->stream("invoice-{$order->id}.pdf");
    }
}
