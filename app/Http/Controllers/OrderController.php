<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Day;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
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

        // Prefer mPDF for complex-script (Arabic) support if available
        if (class_exists(\Mpdf\Mpdf::class)) {
            $html = view('invoices.show', [
                'order' => $order,
                'company' => $company,
            ])->render();

            // mPDF setup: utf-8 mode, use DejaVu fonts by default which support Arabic shaping
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [90, 297], // width mm x height (approx); we'll let it paginate
                'default_font' => 'dejavusans',
            ]);

            // Allow RTL if locale is Arabic
            if (app()->getLocale() === 'ar') {
                $mpdf->SetDirectionality('rtl');
            }

            // Ensure the rendered HTML is valid UTF-8. mPDF throws when invalid bytes are present.
            // Try a few safe sanitizations: convert from an unknown encoding, strip control chars,
            // and finally drop any remaining invalid sequences using iconv with //IGNORE.
            try {
                $html = (string) $html;

                if (!mb_check_encoding($html, 'UTF-8')) {
                    // Try to convert from whatever encoding PHP thinks it is
                    $html = mb_convert_encoding($html, 'UTF-8', 'auto');
                }

                // Remove C0 control characters that can break XML/HTML handling in mPDF
                $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $html);

                // Final pass: drop any leftover invalid UTF-8 byte sequences
                $html = @iconv('UTF-8', 'UTF-8//IGNORE', $html);

                if (!mb_check_encoding($html, 'UTF-8')) {
                    // Try a set of likely source encodings (Arabic locales often use CP1256 or ISO-8859-6)
                    $tried = ['Windows-1256', 'CP1256', 'ISO-8859-6', 'Windows-1252', 'ISO-8859-1'];
                    $converted = false;

                    foreach ($tried as $enc) {
                        $try = @mb_convert_encoding($html, 'UTF-8', $enc);
                        $try = @iconv('UTF-8', 'UTF-8//IGNORE', $try);
                        if ($try !== false && mb_check_encoding($try, 'UTF-8')) {
                            $html = $try;
                            $converted = true;
                            Log::info('OrderController::invoice - converted HTML from encoding', ['order_id' => $order->id, 'from' => $enc]);
                            break;
                        }
                    }

                    if (!$converted) {
                        Log::warning('OrderController::invoice - HTML contains invalid UTF-8 after sanitization (no conversion succeeded)', ['order_id' => $order->id]);
                        // Mark for dompdf fallback (mPDF will fail on invalid UTF-8)
                        $useDompdfFallback = true;
                    }
                }
            } catch (\Throwable $e) {
                // Defensive: log and continue; iconv/mb_* may throw on some setups
                Log::warning('OrderController::invoice - UTF-8 sanitization failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                // Attempt a last-resort cleanup
                $html = @iconv('UTF-8', 'UTF-8//IGNORE', (string) $html);
            }

            // If we flagged a dompdf fallback due to encoding issues, use dompdf instead of mPDF
            if (!empty($useDompdfFallback)) {
                Log::warning('OrderController::invoice - falling back to dompdf due to UTF-8 issues', ['order_id' => $order->id]);

                $pdf = Pdf::setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                ])->loadHTML($html);

                $pdf->setPaper([0, 0, 255.12, 841.89], 'portrait');

                return $pdf->stream("receipt-{$order->id}.pdf");
            }

            try {
                $mpdf->WriteHTML($html);

                $pdfOutput = $mpdf->Output("receipt-{$order->id}.pdf", \Mpdf\Output\Destination::STRING_RETURN);

                return response($pdfOutput, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="receipt-' . $order->id . '.pdf"',
                ]);
            } catch (\Throwable $e) {
                // If mPDF fails (e.g., invalid UTF-8), fall back to dompdf with sanitized HTML
                Log::warning('OrderController::invoice - mPDF failed, falling back to dompdf', ['order_id' => $order->id, 'error' => $e->getMessage()]);

                $pdf = Pdf::setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                ])->loadHTML($html);

                $pdf->setPaper([0, 0, 255.12, 841.89], 'portrait');

                return $pdf->stream("receipt-{$order->id}.pdf");
            }
        }

        // Fallback to dompdf (existing behavior)
        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            // allow unicode font subsetting
            'isFontSubsettingEnabled' => true,
        ])->loadView('invoices.show', [
            'order' => $order,
            'company' => $company,
        ]);

        // Set paper size for thermal receipt (90mm width, auto height)
        $pdf->setPaper([0, 0, 255.12, 841.89], 'portrait'); // 90mm x auto (in points)

        // Download the PDF with a custom filename
        return $pdf->stream("receipt-{$order->id}.pdf");
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
