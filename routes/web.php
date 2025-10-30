<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AdminAuth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Language Switcher Route
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Authentication routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Logout route (authenticated only)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware(AdminAuth::class);

// Admin routes (protected by AdminAuth middleware)
Route::prefix('admin')->middleware(AdminAuth::class)->group(function (): void {
    // Dashboard
    Route::get('/', function () {
        // Get today's day record (open or closed)
        $todayDay = \App\Models\Day::whereDate('date', today())->first();
        
        // Get today's stats
        $todayOrders = 0;
        $todaySales = 0;
        
        if ($todayDay) {
            $todayOrders = $todayDay->orders()->count();
            $todaySales = $todayDay->orders()->where('status', \App\Models\Order::STATUS_COMPLETED)->sum('total');
        }
        
        return view('admin.dashboard', [
            'todayOrders' => $todayOrders,
            'todaySales' => $todaySales,
            'totalItems' => \App\Models\Item::count(),
            'totalCategories' => \App\Models\Category::count(),
        ]);
    })->name('admin.dashboard');

    Route::get('/dashboard', function () {
        // Get today's day record (open or closed)
        $todayDay = \App\Models\Day::whereDate('date', today())->first();
        
        // Get today's stats
        $todayOrders = 0;
        $todaySales = 0;
        
        if ($todayDay) {
            $todayOrders = $todayDay->orders()->count();
            $todaySales = $todayDay->orders()->where('status', \App\Models\Order::STATUS_COMPLETED)->sum('total');
        }
        
        return view('admin.dashboard', [
            'todayOrders' => $todayOrders,
            'todaySales' => $todaySales,
            'totalItems' => \App\Models\Item::count(),
            'totalCategories' => \App\Models\Category::count(),
        ]);
    })->name('dashboard');

    // Categories CRUD
    Route::resource('categories', CategoryController::class);

    // Items CRUD
    Route::resource('items', ItemController::class);
    Route::post('items/{item}/add-stock', [ItemController::class, 'addStock'])->name('items.add-stock');

    // Day Management
    Route::get('days', [DayController::class, 'index'])->name('days.index');
    Route::post('day/open', [DayController::class, 'openDay'])->name('day.open');
    Route::post('day/close', [DayController::class, 'closeDay'])->name('day.close');
    Route::get('day/status', [DayController::class, 'showDayStatus'])->name('day.status');
    Route::get('day/summary', [DayController::class, 'summary'])->name('day.summary');

    // Months Management
    Route::get('months', [MonthController::class, 'index'])->name('months.index');
    Route::get('months/{year}/{month}', [MonthController::class, 'show'])->name('months.show');

    // Orders Management
    Route::resource('orders', OrderController::class)->except(['edit', 'update', 'destroy']);
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Reports & Analytics
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Settings
    Route::get('settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::delete('settings/reset-data', [\App\Http\Controllers\SettingsController::class, 'resetData'])->name('settings.reset-data');
    Route::delete('settings/reset-specific', [\App\Http\Controllers\SettingsController::class, 'resetSpecific'])->name('settings.reset-specific');

    // POS - Point of Sale (uses order creation interface)
    Route::get('pos', [OrderController::class, 'create'])->name('pos.index');
});

