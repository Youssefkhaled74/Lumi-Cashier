@extends('layouts.admin')

@section('title', __('messages.reports_analytics'))

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">
                    <i class="bi bi-bar-chart-line text-indigo-600"></i> {{ __('messages.reports_analytics') }}
                </h2>
                <p class="text-gray-600">{{ __('messages.view_sales_performance') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-300">
                <i class="bi bi-arrow-left"></i>
                <span>{{ __('messages.back_to_dashboard') }}</span>
            </a>
        </div>
    </div>

    <!-- Date Filter Form -->
    <div class="card shadow-2xl mb-6 animate-fadeInUp">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="from_date" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="bi bi-calendar-event"></i> {{ __('messages.from_date') }}
                </label>
                <input 
                    type="date" 
                    id="from_date" 
                    name="from_date" 
                    value="{{ $fromDate }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 shadow-sm hover:border-purple-300"
                >
            </div>
            
            <div>
                <label for="to_date" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="bi bi-calendar-check"></i> {{ __('messages.to_date') }}
                </label>
                <input 
                    type="date" 
                    id="to_date" 
                    name="to_date" 
                    value="{{ $toDate }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 shadow-sm hover:border-purple-300"
                >
            </div>
            
            <div class="md:col-span-2 flex items-end gap-3">
                <button 
                    type="submit" 
                    name="generate"
                    value="1"
                    class="flex-1 px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center"
                >
                    <i class="bi bi-graph-up mr-2"></i>
                    {{ __('messages.generate_report') }}
                </button>
                
                @if($reportData !== null)
                <a 
                    href="{{ route('reports.export-pdf', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" 
                    class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold rounded-xl hover:from-red-700 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center"
                    target="_blank"
                >
                    <i class="bi bi-file-pdf-fill mr-2"></i>
                    {{ __('messages.export_pdf') }}
                </a>
                @endif
                
                <a 
                    href="{{ route('reports.index') }}" 
                    class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 shadow-sm flex items-center"
                >
                    <i class="bi bi-arrow-counterclockwise mr-2"></i>
                    {{ __('messages.reset') }}
                </a>
            </div>
        </form>
    </div>

    @if($reportData === null)
        <!-- Empty State -->
        <div class="card shadow-2xl text-center py-16 animate-fadeInUp" style="animation-delay: 0.1s;">
            <div class="flex flex-col items-center">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mb-6">
                    <i class="bi bi-graph-up text-6xl text-indigo-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ __('messages.ready_to_generate') }}</h3>
                <p class="text-gray-600 mb-8 max-w-md">{{ __('messages.select_date_range') }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 w-full max-w-3xl">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl">
                        <i class="bi bi-cash-stack text-4xl text-blue-600 mb-3"></i>
                        <h4 class="font-bold text-gray-800 mb-2">{{ __('messages.sales_analytics') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('messages.track_revenue_trends') }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-xl">
                        <i class="bi bi-box-seam text-4xl text-purple-600 mb-3"></i>
                        <h4 class="font-bold text-gray-800 mb-2">{{ __('messages.inventory_status') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('messages.monitor_stock_levels') }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl">
                        <i class="bi bi-trophy text-4xl text-green-600 mb-3"></i>
                        <h4 class="font-bold text-gray-800 mb-2">{{ __('messages.top_products') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('messages.see_best_selling') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Report Data Display -->
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="card shadow-2xl animate-fadeInUp">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="bi bi-receipt text-indigo-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">{{ __('messages.total_orders') }}</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ number_format($reportData['summary']['total_orders']) }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-2xl animate-fadeInUp" style="animation-delay: 0.1s;">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="bi bi-currency-dollar text-green-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">{{ __('messages.total_sales') }}</p>
                        <p class="text-3xl font-extrabold text-gray-900">${{ number_format($reportData['summary']['total_sales'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-2xl animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="bi bi-graph-up-arrow text-blue-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">{{ __('messages.avg_order') }}</p>
                        <p class="text-3xl font-extrabold text-gray-900">${{ number_format($reportData['summary']['average_order_value'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-2xl animate-fadeInUp" style="animation-delay: 0.3s;">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="bi bi-calendar-range text-purple-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-600">{{ __('messages.days') }}</p>
                        <p class="text-3xl font-extrabold text-gray-900">{{ $reportData['summary']['date_range']['days'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($reportData['summary']['total_orders'] == 0)
            <!-- No Data Message -->
            <div class="card shadow-2xl text-center py-12 animate-fadeInUp" style="animation-delay: 0.4s;">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-inbox text-5xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('messages.no_orders_found') }}</h3>
                    <p class="text-gray-600 mb-6 max-w-md">{{ __('messages.no_orders_in_range') }} ({{ $reportData['summary']['date_range']['from'] }} - {{ $reportData['summary']['date_range']['to'] }}).</p>
                    <a href="{{ route('pos.index') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                        <i class="bi bi-cart-plus mr-2"></i>
                        {{ __('messages.create_first_order') }}
                    </a>
                </div>
            </div>
        @else
            <!-- Inventory Summary -->
            <div class="card shadow-2xl mb-6 animate-fadeInUp" style="animation-delay: 0.5s;">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="bi bi-box-seam text-purple-600"></i> {{ __('messages.inventory_overview') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">{{ __('messages.total_value') }}</p>
                                <p class="text-2xl font-extrabold text-gray-900">${{ number_format($reportData['inventory']['total_inventory_value'], 2) }}</p>
                            </div>
                            <i class="bi bi-piggy-bank text-4xl text-blue-600"></i>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">{{ __('messages.low_stock_items') }}</p>
                                <p class="text-2xl font-extrabold text-gray-900">{{ $reportData['inventory']['low_stock']->count() }}</p>
                            </div>
                            <i class="bi bi-exclamation-triangle text-4xl text-orange-600"></i>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">{{ __('messages.out_of_stock') }}</p>
                                <p class="text-2xl font-extrabold text-gray-900">{{ $reportData['inventory']['out_of_stock']->count() }}</p>
                            </div>
                            <i class="bi bi-x-circle text-4xl text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            @if($reportData['top_selling_items']->isNotEmpty())
                <div class="card shadow-2xl mb-6 animate-fadeInUp" style="animation-delay: 0.6s;">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="bi bi-trophy text-green-600"></i> {{ __('messages.top_selling_items') }}
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">{{ __('messages.product') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">{{ __('messages.sku') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">{{ __('messages.qty_sold') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">{{ __('messages.revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['top_selling_items'] as $index => $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 font-bold rounded-full text-sm">{{ $index + 1 }}</span>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $item['name'] }}</td>
                                        <td class="px-6 py-4 text-gray-500 font-mono text-sm">{{ $item['sku'] }}</td>
                                        <td class="px-6 py-4 text-gray-900 font-bold">{{ $item['quantity_sold'] }}</td>
                                        <td class="px-6 py-4 text-green-600 font-bold">${{ number_format($item['revenue'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    @endif
@endsection
