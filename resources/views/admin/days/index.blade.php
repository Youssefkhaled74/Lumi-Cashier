@extends('layouts.admin')

@section('title', __('messages.business_days_history'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">{{ __('messages.business_days_history') }}</h2>
            <p class="text-gray-600">{{ __('messages.view_all_sessions') }}</p>
        </div>
        <a href="{{ route('day.status') }}" class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <i class="bi bi-calendar-check text-xl"></i>
            <span class="font-semibold">{{ __('messages.current_day_status') }}</span>
        </a>
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

    <div class="card shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200" style="width:100%">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.date') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.opened_at') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.closed_at') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.duration') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.orders') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.total_sales') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($days as $day)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center shadow-sm">
                                        <i class="bi bi-calendar-event text-2xl text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $day->date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $day->date->format('l') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($day->is_open)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 animate-pulse">
                                        <i class="bi bi-circle-fill mr-1 text-xs"></i> {{ __('messages.open_status') }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800">
                                        <i class="bi bi-check-circle mr-1"></i> {{ __('messages.closed_status') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($day->opened_at)
                                    <div class="text-sm text-gray-900">{{ $day->opened_at->format('h:i A') }}</div>
                                    <div class="text-xs text-gray-500">{{ $day->opened_at->format('M d, Y') }}</div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($day->closed_at)
                                    <div class="text-sm text-gray-900">{{ $day->closed_at->format('h:i A') }}</div>
                                    <div class="text-xs text-gray-500">{{ $day->closed_at->format('M d, Y') }}</div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($day->duration_in_hours)
                                    <div class="text-sm font-semibold text-indigo-600">
                                        <i class="bi bi-clock mr-1"></i>
                                        {{ number_format($day->duration_in_hours, 1) }} hrs
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    <i class="bi bi-receipt mr-1"></i>
                                    {{ $day->total_orders }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600">
                                    ${{ number_format($day->total_sales, 2) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="bi bi-calendar-x text-6xl mb-4"></i>
                                    <p class="text-xl font-semibold mb-2">{{ __('messages.no_business_days_found') }}</p>
                                    <p class="text-sm mb-4">{{ __('messages.open_new_day_to_start') }}</p>
                                    <a href="{{ route('day.status') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                        <i class="bi bi-plus-circle mr-2"></i>
                                        {{ __('messages.go_to_day_status') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($days->hasPages())
        <div class="mt-6">
            {{ $days->links() }}
        </div>
    @endif
@endsection
