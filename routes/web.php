<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PdfTestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShopSettingsController;
use App\Http\Middleware\AdminAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('login'));

// PDF Testing
Route::get('/pdf-test-ar', [PdfTestController::class, 'testArabic']);

// Language Switcher
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware(AdminAuth::class);

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by AdminAuth middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(AdminAuth::class)->group(function (): void {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories Management
    Route::get('categories/export-pdf', [CategoryController::class, 'exportPdf'])->name('categories.export-pdf');
    Route::resource('categories', CategoryController::class);

    // Items Management
    Route::get('items/export-pdf', [ItemController::class, 'exportPdf'])->name('items.export-pdf');
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
    Route::post('orders/verify-discount', [OrderController::class, 'verifyAdminForDiscount'])->name('orders.verify-discount');

    // Reports & Analytics
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Settings Management
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('settings/printer', [SettingsController::class, 'printer'])->name('settings.printer');
    Route::delete('settings/reset-data', [SettingsController::class, 'resetData'])->name('settings.reset-data');
    Route::delete('settings/reset-specific', [SettingsController::class, 'resetSpecific'])->name('settings.reset-specific');

    // Shop Settings (Password Protected)
    Route::get('settings/shop', [ShopSettingsController::class, 'index'])->name('settings.shop.index');
    Route::post('settings/shop/verify', [ShopSettingsController::class, 'verifyPassword'])->name('settings.shop.verify');
    Route::put('settings/shop', [ShopSettingsController::class, 'update'])->name('settings.shop.update');
    Route::delete('settings/shop/logo', [ShopSettingsController::class, 'deleteLogo'])->name('settings.shop.deleteLogo');

    // POS - Point of Sale
    Route::get('pos', [OrderController::class, 'create'])->name('pos.index');
});

/*
|--------------------------------------------------------------------------
| Public Marketing Routes
|--------------------------------------------------------------------------
*/

Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.index');
Route::get('/marketing/brochure', [MarketingController::class, 'brochure'])->name('marketing.brochure');
Route::get('/marketing/brochure/download', [MarketingController::class, 'downloadBrochure'])->name('marketing.brochure.download');
