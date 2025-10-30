@extends('layouts.admin')

@section('title', $monthName . ' - Monthly Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-4 mb-2">
                <a href="{{ route('months.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="bi bi-arrow-left text-xl text-gray-600"></i>
                </a>
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                        <i class="bi bi-calendar-month text-white text-2xl"></i>
                    </div>
                    <span>{{ $monthName }}</span>
                </h1>
            </div>
            <p class="text-gray-500">Detailed statistics and daily breakdown</p>
        </div>
    </div>

    <!-- Month Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales -->
        <div class="stat-card card bg-gradient-to-br from-green-500 to-emerald-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Total Sales</p>
                    <h3 class="text-3xl font-extrabold">${{ number_format($monthStats['total_sales'], 2) }}</h3>
                    <p class="text-green-100 text-xs mt-2">Avg: ${{ number_format($monthStats['average_daily_sales'], 2) }}/day</p>
                </div>
                <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-sm">
                    <i class="bi bi-currency-dollar text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="stat-card card bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Orders</p>
                    <h3 class="text-3xl font-extrabold">{{ $monthStats['total_orders'] }}</h3>
                    <p class="text-blue-100 text-xs mt-2">{{ $monthStats['completed_orders'] }} completed</p>
                </div>
                <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-sm">
                    <i class="bi bi-receipt text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Days -->
        <div class="stat-card card bg-gradient-to-br from-purple-500 to-pink-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Total Days</p>
                    <h3 class="text-3xl font-extrabold">{{ $monthStats['total_days'] }}</h3>
                    <p class="text-purple-100 text-xs mt-2">{{ $monthStats['closed_days'] }} closed</p>
                </div>
                <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-sm">
                    <i class="bi bi-calendar3 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Open Days -->
        <div class="stat-card card bg-gradient-to-br from-orange-500 to-red-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Open Days</p>
                    <h3 class="text-3xl font-extrabold">{{ $monthStats['open_days'] }}</h3>
                    <p class="text-orange-100 text-xs mt-2">Currently active</p>
                </div>
                <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-sm">
                    <i class="bi bi-calendar-check text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Days Table -->
    <div class="card">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Daily Breakdown</h2>
            <span class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl font-semibold text-sm">
                {{ $days->count() }} Days
            </span>
        </div>

        @if($days->count() > 0)
        <div class="overflow-x-auto">
            <table id="daysTable" class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Day</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Sales</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Opened At</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Closed At</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($days as $day)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <i class="bi bi-calendar-event text-indigo-600"></i>
                                <span class="font-semibold text-gray-900">{{ $day->date->format('Y-m-d') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $day->date->format('l') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($day->closed_at)
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="bi bi-check-circle me-1"></i> Closed
                            </span>
                            @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 animate-pulse">
                                <i class="bi bi-circle-fill me-1"></i> Open
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-gray-900">{{ $day->total_orders }}</span>
                                <span class="text-xs text-gray-500">({{ $day->completed_orders }} completed)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-green-600">${{ number_format($day->total_sales, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $day->opened_at ? $day->opened_at->format('h:i A') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $day->closed_at ? $day->closed_at->format('h:i A') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('day.summary') }}?date={{ $day->date->format('Y-m-d') }}" class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm font-medium">
                                <i class="bi bi-eye me-1"></i>
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="bi bi-calendar-x text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No days recorded for this month</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    try {
        if ($.fn.DataTable.isDataTable('#daysTable')) {
            $('#daysTable').DataTable().destroy();
        }
        
        $('#daysTable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 31,
            language: {
                search: "Search days:",
                lengthMenu: "Show _MENU_ days per page",
                info: "Showing _START_ to _END_ of _TOTAL_ days",
                emptyTable: "No days recorded for this month"
            }
        });
    } catch (error) {
        console.error('DataTables initialization error:', error);
    }
});
</script>
@endpush
@endsection
