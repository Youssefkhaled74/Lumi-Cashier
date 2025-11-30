@extends('layouts.admin')

@section('title', __('messages.dashboard'))

@section('content')
    <!-- Welcome Message handled by global flash partial -->

    <!-- Page Header -->
    <div class="mb-8">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-2">{{ __('messages.dashboard_title') }}</h2>
        <p class="text-lg text-gray-600">{{ __('messages.dashboard_subtitle') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Sales -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl opacity-0" style="animation-fill-mode: forwards;">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <i class="bi bi-currency-dollar text-3xl"></i>
                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full font-semibold backdrop-blur-sm">{{ __('messages.today') }}</span>
            </div>
            <p class="text-4xl font-extrabold mb-1">{{ config('cashier.currency', '$') }}{{ number_format($todaySales ?? 0, 2) }}</p>
            <p class="text-blue-100 text-sm font-medium">{{ __('messages.todays_sales') }}</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <p class="text-xs text-blue-100">↑ 0% {{ __('messages.yesterday') }}</p>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl opacity-0" style="animation-fill-mode: forwards;">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <i class="bi bi-receipt text-3xl"></i>
                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full font-semibold backdrop-blur-sm">{{ __('messages.today') }}</span>
            </div>
            <p class="text-4xl font-extrabold mb-1">{{ $todayOrders ?? 0 }}</p>
            <p class="text-green-100 text-sm font-medium">{{ __('messages.todays_orders') }}</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <p class="text-xs text-green-100">↑ 0% {{ __('messages.yesterday') }}</p>
            </div>
        </div>

        <!-- Total Items -->
        <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl opacity-0" style="animation-fill-mode: forwards;">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <i class="bi bi-box-seam text-3xl"></i>
                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full font-semibold backdrop-blur-sm">{{ __('messages.item_stock') }}</span>
            </div>
            <p class="text-4xl font-extrabold mb-1">{{ $totalItems ?? \App\Models\Item::count() }}</p>
            <p class="text-purple-100 text-sm font-medium">{{ __('messages.total_items') }}</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <a href="{{ route('items.index') }}" class="text-xs text-white hover:underline">{{ __('messages.view_details') }} →</a>
            </div>
        </div>

        <!-- Categories -->
        <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl opacity-0" style="animation-fill-mode: forwards;">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <i class="bi bi-folder text-3xl"></i>
                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full font-semibold backdrop-blur-sm">{{ __('messages.currently_active') }}</span>
            </div>
            <p class="text-4xl font-extrabold mb-1">{{ $totalCategories ?? \App\Models\Category::count() }}</p>
            <p class="text-orange-100 text-sm font-medium">{{ __('messages.total_categories') }}</p>
            <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                <a href="{{ route('categories.index') }}" class="text-xs text-white hover:underline">{{ __('messages.view_details') }} →</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card p-8 mb-8 shadow-xl">
        <h3 class="text-2xl font-extrabold text-gray-800 mb-6 flex items-center gap-4">
            <i class="bi bi-lightning-fill text-yellow-500 text-3xl"></i>
            {{ __('messages.quick_actions') }}
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="{{ route('pos.index') }}" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <i class="bi bi-cart-plus text-5xl text-purple-600 mb-3 group-hover:scale-110 transition-transform duration-300"></i>
                <span class="font-bold text-gray-700 group-hover:text-purple-600 transition-colors">{{ __('messages.new_sale') }}</span>
            </a>
            
            <a href="{{ route('items.create') }}" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <i class="bi bi-plus-circle text-5xl text-blue-600 mb-3 group-hover:scale-110 transition-transform duration-300"></i>
                <span class="font-bold text-gray-700 group-hover:text-blue-600 transition-colors">{{ __('messages.add_item') }}</span>
            </a>
            
            <a href="{{ route('categories.create') }}" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <i class="bi bi-folder-plus text-5xl text-green-600 mb-3 group-hover:scale-110 transition-transform duration-300"></i>
                <span class="font-bold text-gray-700 group-hover:text-green-600 transition-colors">{{ __('messages.add_category') }}</span>
            </a>
            
            <a href="{{ route('reports.index') }}" class="group flex flex-col items-center justify-center p-8 bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <i class="bi bi-file-earmark-pdf text-5xl text-orange-600 mb-3 group-hover:scale-110 transition-transform duration-300"></i>
                <span class="font-bold text-gray-700 group-hover:text-orange-600 transition-colors">{{ __('messages.nav.reports') }}</span>
            </a>
        </div>
        
        <!-- Settings Button (Full Width Below) -->
        <div class="mt-6">
            <a href="{{ route('settings.index') }}" class="group flex items-center justify-center gap-6 p-6 bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-red-200">
                <i class="bi bi-gear-fill text-4xl text-red-600 group-hover:rotate-90 transition-transform duration-500"></i>
                <div class="">
                    <span class="font-bold text-gray-700 group-hover:text-red-600 transition-colors text-lg block">System Settings</span>
                    <span class="text-sm text-gray-500">Manage system data and configurations</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity / Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card p-8 shadow-xl">
            <h3 class="text-xl font-extrabold text-gray-800 mb-6 flex items-center gap-4">
                <i class="bi bi-clock-history text-blue-600 text-2xl"></i>
                Recent Activity
            </h3>
            <div class="text-center py-12 text-gray-400">
                <i class="bi bi-inbox text-6xl mb-4"></i>
                <p class="text-lg font-medium">No recent activity</p>
                <p class="text-sm mt-2">Your recent transactions will appear here</p>
            </div>
        </div>

        <div class="card p-8 shadow-xl">
            <h3 class="text-xl font-extrabold text-gray-800 mb-6 flex items-center gap-4">
                <i class="bi bi-info-circle text-purple-600 text-2xl"></i>
                System Information
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">App Version</span>
                    <span class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded-lg">1.0.0</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Database</span>
                    <span class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded-lg">SQLite</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Current Date</span>
                    <span class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded-lg">{{ now()->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-600 font-medium">Status</span>
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-bold shadow-sm">
                        <i class="bi bi-circle-fill text-xs animate-pulse"></i>
                        Online
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection