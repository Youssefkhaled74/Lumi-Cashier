<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Day;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_order_items' => OrderItem::count(),
            'total_days' => Day::count(),
            'total_items' => Item::count(),
            'total_categories' => Category::count(),
        ];

        return view('admin.settings.index', compact('stats'));
    }

    /**
     * Show the printer settings page
     */
    public function printer()
    {
        return view('admin.settings.printer');
    }

    /**
     * Reset all transactional data (orders, order items, days)
     * Requires admin credentials verification
     */
    public function resetData(Request $request)
    {
        // Validate credentials
        $request->validate([
            'admin_email' => 'required|email',
            'admin_password' => 'required|string',
        ]);

        // Verify admin credentials against config
        $configEmail = config('cashier.admin.email');
        $configPassword = config('cashier.admin.password');
        
        if ($request->input('admin_email') !== $configEmail || 
            $request->input('admin_password') !== $configPassword) {
            return redirect()
                ->route('settings.index')
                ->with('error', __('pos.invalid_credentials'));
        }

        try {
            DB::beginTransaction();

            // Get counts before deletion
            $orderItemsCount = OrderItem::count();
            $ordersCount = Order::count();
            $daysCount = Day::count();

            // Delete all order items first (foreign key constraint)
            OrderItem::truncate();

            // Delete all orders
            Order::truncate();

            // Delete all days
            Day::truncate();

            DB::commit();

            $currentUser = Auth::user();
            Log::info('System data reset by admin', [
                'user_id' => $currentUser ? $currentUser->id : null,
                'user_email' => $currentUser ? $currentUser->email : $configEmail,
                'verified_as' => $configEmail,
                'orders_deleted' => $ordersCount,
                'order_items_deleted' => $orderItemsCount,
                'days_deleted' => $daysCount,
                'timestamp' => now(),
            ]);

            return redirect()
                ->route('settings.index')
                ->with('success', __('pos.data_reset_success', [
                    'orders' => $ordersCount,
                    'items' => $orderItemsCount,
                    'days' => $daysCount
                ]));

        } catch (\Exception $e) {
            DB::rollBack();
            
            $currentUser = Auth::user();
            Log::error('Failed to reset system data', [
                'error' => $e->getMessage(),
                'user_id' => $currentUser ? $currentUser->id : null,
            ]);

            return redirect()
                ->route('settings.index')
                ->with('error', __('pos.data_reset_failed'));
        }
    }

    /**
     * Reset specific data type
     * Requires admin credentials verification
     */
    public function resetSpecific(Request $request)
    {
        // Validate credentials and type
        $request->validate([
            'type' => 'required|in:orders,days,items,categories',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string',
        ]);

        // Verify admin credentials against config
        $configEmail = config('cashier.admin.email');
        $configPassword = config('cashier.admin.password');
        
        if ($request->input('admin_email') !== $configEmail || 
            $request->input('admin_password') !== $configPassword) {
            return redirect()
                ->route('settings.index')
                ->with('error', __('pos.invalid_credentials'));
        }

        try {
            DB::beginTransaction();

            $type = $request->input('type');
            $message = '';
            $currentUser = Auth::user();

            switch ($type) {
                case 'orders':
                    $orderItemsCount = OrderItem::count();
                    $ordersCount = Order::count();
                    
                    OrderItem::truncate();
                    Order::truncate();
                    
                    $message = __('pos.orders_reset_success', [
                        'count' => $ordersCount
                    ]);

                    Log::info('Orders reset by admin', [
                        'user_id' => $currentUser ? $currentUser->id : null,
                        'user_email' => $currentUser ? $currentUser->email : $configEmail,
                        'verified_as' => $configEmail,
                        'orders_deleted' => $ordersCount,
                        'order_items_deleted' => $orderItemsCount,
                    ]);
                    break;

                case 'days':
                    $daysCount = Day::count();
                    Day::truncate();
                    
                    $message = __('pos.days_reset_success', [
                        'count' => $daysCount
                    ]);

                    Log::info('Business days reset by admin', [
                        'user_id' => $currentUser ? $currentUser->id : null,
                        'user_email' => $currentUser ? $currentUser->email : $configEmail,
                        'verified_as' => $configEmail,
                        'days_deleted' => $daysCount,
                    ]);
                    break;

                case 'items':
                    $itemsCount = Item::count();
                    $itemUnitsCount = ItemUnit::count();
                    
                    // Delete item units first (foreign key constraint)
                    ItemUnit::truncate();
                    Item::truncate();
                    
                    $message = __('pos.items_reset_success', [
                        'count' => $itemsCount
                    ]);

                    Log::info('Items reset by admin', [
                        'user_id' => $currentUser ? $currentUser->id : null,
                        'user_email' => $currentUser ? $currentUser->email : $configEmail,
                        'verified_as' => $configEmail,
                        'items_deleted' => $itemsCount,
                        'item_units_deleted' => $itemUnitsCount,
                    ]);
                    break;

                case 'categories':
                    $categoriesCount = Category::count();
                    
                    // Check if there are items linked to categories
                    $itemsWithCategories = Item::whereNotNull('category_id')->count();
                    
                    if ($itemsWithCategories > 0) {
                        DB::rollBack();
                        return redirect()
                            ->route('settings.index')
                            ->with('error', __('pos.cannot_delete_categories_with_items', [
                                'count' => $itemsWithCategories
                            ]));
                    }
                    
                    Category::truncate();
                    
                    $message = __('pos.categories_reset_success', [
                        'count' => $categoriesCount
                    ]);

                    Log::info('Categories reset by admin', [
                        'user_id' => $currentUser ? $currentUser->id : null,
                        'user_email' => $currentUser ? $currentUser->email : $configEmail,
                        'verified_as' => $configEmail,
                        'categories_deleted' => $categoriesCount,
                    ]);
                    break;
            }

            DB::commit();

            return redirect()
                ->route('settings.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $currentUser = Auth::user();
            Log::error('Failed to reset specific data', [
                'error' => $e->getMessage(),
                'type' => $request->input('type'),
                'user_id' => $currentUser ? $currentUser->id : null,
            ]);
            
            return redirect()
                ->route('settings.index')
                ->with('error', __('pos.data_reset_failed'));
        }
    }
}
