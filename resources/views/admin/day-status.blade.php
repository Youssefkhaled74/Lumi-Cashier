@extends('layouts.admin')

@section('title', 'Day Status')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Day Status</h2>
                <p class="text-gray-600">Monitor current business day operations</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-300">
                <i class="bi bi-arrow-left"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-animate bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill text-xl mr-3"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-animate bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <i class="bi bi-exclamation-circle-fill text-xl mr-3"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if($day)
        <!-- Day is Open -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Day Info Card -->
            <div class="card shadow-2xl animate-fadeInUp">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Current Day</h3>
                    <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-sm font-bold rounded-full shadow-lg flex items-center">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        OPEN
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="bi bi-calendar-check text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-semibold">Opened At</p>
                                <p class="text-lg font-bold text-gray-900">{{ $day->opened_at->format('h:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">{{ $day->opened_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="bi bi-clock-history text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-semibold">Duration</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['duration'] ?? 0, 2) }} hours</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="bi bi-hash text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-semibold">Day ID</p>
                                <p class="text-lg font-bold text-gray-900">#{{ $day->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card shadow-2xl animate-fadeInUp" style="animation-delay: 0.1s;">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Today's Performance</h3>

                <div class="space-y-4">
                    <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-600">Total Sales</span>
                            <i class="bi bi-cash-stack text-2xl text-green-600"></i>
                        </div>
                        <p class="text-3xl font-extrabold text-gray-900">${{ number_format($stats['total_sales'] ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Revenue generated today</p>
                    </div>

                    <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-600">Total Orders</span>
                            <i class="bi bi-receipt text-2xl text-blue-600"></i>
                        </div>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Completed transactions</p>
                    </div>

                    @if(($stats['total_orders'] ?? 0) > 0)
                        <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-600">Average Order</span>
                                <i class="bi bi-graph-up text-2xl text-purple-600"></i>
                            </div>
                            <p class="text-3xl font-extrabold text-gray-900">${{ number_format(($stats['total_sales'] ?? 0) / ($stats['total_orders'] ?? 1), 2) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Per transaction</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="card shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-1 animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="flex items-center mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="bi bi-cart-check text-white text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">View Orders</h4>
                        <p class="text-sm text-gray-500">Check today's orders</p>
                    </div>
                </div>
                <a href="{{ route('orders.index') }}" class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="bi bi-arrow-right-circle mr-2"></i>
                    Go to Orders
                </a>
            </div>

            <div class="card shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-1 animate-fadeInUp" style="animation-delay: 0.3s;">
                <div class="flex items-center mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="bi bi-box-seam text-white text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Manage Inventory</h4>
                        <p class="text-sm text-gray-500">Update stock levels</p>
                    </div>
                </div>
                <a href="{{ route('items.index') }}" class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="bi bi-arrow-right-circle mr-2"></i>
                    Go to Items
                </a>
            </div>

            <div class="card shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-1 animate-fadeInUp" style="animation-delay: 0.4s;">
                <div class="flex items-center mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-orange-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="bi bi-door-closed text-white text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ __('messages.close_day') }}</h4>
                        <p class="text-sm text-gray-500">{{ __('messages.end_business_operations') }}</p>
                    </div>
                </div>
                <form action="{{ route('day.close') }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_close_day') }}');">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold rounded-xl hover:from-red-700 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                        <i class="bi bi-x-circle mr-2"></i>
                        {{ __('messages.close_day') }}
                    </button>
                </form>
            </div>
        </div>

    @else
        <!-- No Day Open -->
        <div class="card shadow-2xl text-center py-16">
            <div class="flex flex-col items-center">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                    <i class="bi bi-moon-stars text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Active Day</h3>
                <p class="text-gray-600 mb-8 max-w-md">There is no business day currently open. Start a new day to begin processing orders and managing inventory.</p>
                
                <form action="{{ route('day.open') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center text-lg">
                        <i class="bi bi-sunrise mr-2 text-2xl"></i>
                        Open New Day
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="card shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-blue-50 to-indigo-50">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="bi bi-shield-check text-white text-xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Secure</h4>
                </div>
                <p class="text-sm text-gray-600">All transactions are tracked and secured</p>
            </div>

            <div class="card shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-purple-50 to-pink-50">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="bi bi-speedometer2 text-white text-xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Real-time</h4>
                </div>
                <p class="text-sm text-gray-600">Monitor your business in real-time</p>
            </div>

            <div class="card shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-green-50 to-emerald-50">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="bi bi-graph-up-arrow text-white text-xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Analytics</h4>
                </div>
                <p class="text-sm text-gray-600">Detailed insights and reports</p>
            </div>
        </div>
    @endif
@endsection
