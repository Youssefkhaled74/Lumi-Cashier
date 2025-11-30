<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Day;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use App\Services\PdfGenerator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
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

        // Render the normal order details view
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
            'phone' => '01064338132',
            'email' => 'info@lumipos.com',
        ]);

        // Render invoice HTML and let PdfGenerator choose the best engine (mPDF preferred)
        $html = view('invoices.show', [
            'order' => $order,
            'company' => $company,
        ])->render();

        // Thermal receipt width (90mm) converted to approximate points used previously
        $paper = [0, 0, 255.12, 841.89];

        return app(PdfGenerator::class)->streamHtml($html, "receipt-{$order->id}.pdf", $paper, 'portrait');
    }

    /**
     * Verify admin credentials for high discount
     */
    public function verifyAdminForDiscount(Request $request): JsonResponse
    {
        $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'admin_email' => 'required_if:discount_percentage,>,5|email',
            'admin_password' => 'required_if:discount_percentage,>,5|string',
        ]);

        $discountPercentage = $request->input('discount_percentage');
        $maxWithoutApproval = config('cashier.discount.max_without_approval', 5);

        // If discount is within allowed range, approve immediately
        if ($discountPercentage <= $maxWithoutApproval) {
            return response()->json([
                'success' => true,
                'message' => __('pos.discount_approved'),
                'discount_percentage' => $discountPercentage,
            ]);
        }

        // Verify admin credentials for high discount
        $configEmail = config('cashier.admin.email');
        $configPassword = config('cashier.admin.password');
        
        if ($request->input('admin_email') !== $configEmail || 
            $request->input('admin_password') !== $configPassword) {
            return response()->json([
                'success' => false,
                'message' => __('pos.invalid_credentials'),
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => __('pos.discount_approved_by_admin'),
            'discount_percentage' => $discountPercentage,
        ]);
    }
}
