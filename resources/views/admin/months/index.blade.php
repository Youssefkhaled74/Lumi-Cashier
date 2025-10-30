@extends('layouts.admin')

@section('title', __('messages.monthly_overview'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center space-x-3">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                    <i class="bi bi-calendar-month text-white text-2xl"></i>
                </div>
                <span>{{ __('messages.monthly_overview') }}</span>
            </h1>
            <p class="text-gray-500 mt-2">{{ __('messages.month_statistics') }}</p>
        </div>
    </div>

    @if($months->count() > 0)
    <!-- Months Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($months as $month)
        <div class="card hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <a href="{{ route('months.show', ['year' => $month->year, 'month' => $month->month]) }}" class="block">
                <!-- Month Header -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $month->formatted_date }}</h3>
                        <p class="text-sm text-gray-500">{{ $month->total_days }} days recorded</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl">
                        <i class="bi bi-calendar3 text-indigo-600 text-2xl"></i>
                    </div>
                </div>

                <!-- Day Status -->
                <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 rounded-xl">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $month->closed_days }}</p>
                        <p class="text-xs text-gray-600">Closed Days</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $month->open_days }}</p>
                        <p class="text-xs text-gray-600">Open Days</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-500 rounded-lg">
                                <i class="bi bi-currency-dollar text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Total Sales</p>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($month->total_sales, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-500 rounded-lg">
                                <i class="bi bi-receipt text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Total Orders</p>
                                <p class="text-lg font-bold text-gray-900">{{ $month->total_orders }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-500 rounded-lg">
                                <i class="bi bi-check-circle text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Completed</p>
                                <p class="text-lg font-bold text-gray-900">{{ $month->completed_orders }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Details Button -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-center space-x-2 text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                        <span>View Details</span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="card text-center py-16">
        <div class="inline-block p-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
            <i class="bi bi-calendar-x text-5xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No Months Recorded</h3>
        <p class="text-gray-500 mb-6">Start by opening a day to record monthly statistics</p>
        <a href="{{ route('day.status') }}" class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-lg">
            <i class="bi bi-calendar-plus"></i>
            <span>Manage Daily Sessions</span>
        </a>
    </div>
    @endif
</div>
@endsection
