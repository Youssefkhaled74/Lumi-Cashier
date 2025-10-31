@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('orders.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
                <span class="font-medium">Back to Orders</span>
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-sm text-gray-500">Created on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <x-print-button 
                :orderId="$order->id" 
                label="Print Receipt" 
                icon="bi-printer" 
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center space-x-2"
                :autoPrint="$order->is_completed && !session('printed_order_' . $order->id)"
            />
            <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center space-x-2">
                <i class="bi bi-file-pdf"></i>
                <span>Download Invoice</span>
            </a>
            @if($order->is_completed)
                <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center space-x-2" onclick="return confirm('{{ __('messages.confirm_cancel_order') }}')">
                        <i class="bi bi-x-circle"></i>
                        <span>Cancel Order</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in flex items-center">
            <i class="bi bi-check-circle-fill mr-3 text-2xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in flex items-center">
            <i class="bi bi-exclamation-circle-fill mr-3 text-2xl"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Order Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <h3 class="text-sm font-medium text-gray-500 uppercase mb-3 flex items-center">
                <i class="bi bi-info-circle mr-2"></i>
                Order Status
            </h3>
            @if($order->is_completed)
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    <i class="bi bi-check-circle mr-2"></i> Completed
                </span>
            @elseif($order->is_cancelled)
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    <i class="bi bi-x-circle mr-2"></i> Cancelled
                </span>
            @else
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    <i class="bi bi-clock mr-2"></i> Pending
                </span>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <h3 class="text-sm font-medium text-gray-500 uppercase mb-3 flex items-center">
                <i class="bi bi-calendar-check mr-2"></i>
                Business Day
            </h3>
            @if($order->day)
                <p class="text-lg font-semibold text-gray-900">Day #{{ $order->day->id }}</p>
                <p class="text-sm text-gray-500">{{ $order->day->date->format('F d, Y') }}</p>
            @else
                <p class="text-lg font-semibold text-gray-400">No Day Assigned</p>
                <p class="text-sm text-gray-400">This order is not linked to any business day</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <h3 class="text-sm font-medium text-gray-500 uppercase mb-3 flex items-center">
                <i class="bi bi-currency-dollar mr-2"></i>
                Order Total
            </h3>
            <p class="text-3xl font-bold text-green-600">${{ number_format($order->total, 2) }}</p>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="bi bi-box-seam mr-3 text-2xl"></i>
                Order Items
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-box mr-1"></i>Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-folder mr-1"></i>Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-upc-scan mr-1"></i>Barcode</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-currency-dollar mr-1"></i>Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-hash mr-1"></i>Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-currency-dollar mr-1"></i>Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $item->itemUnit?->item?->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">SKU: {{ $item->itemUnit?->item?->sku ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $item->itemUnit?->item?->category?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded inline-block">{{ $item->itemUnit?->barcode ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                ${{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 text-right">
                                ${{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50">
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900 uppercase">
                            Total:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-lg font-bold text-indigo-600">
                            ${{ number_format($order->total, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($order->notes)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center">
                <i class="bi bi-file-text mr-2"></i>
                Order Notes
            </h3>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Dispatch order completion event for auto-print
document.addEventListener('DOMContentLoaded', function() {
    @if($order->is_completed && !session('printed_order_' . $order->id))
        // Trigger auto-print event
        setTimeout(() => {
            document.dispatchEvent(new CustomEvent('orderCompleted', {
                detail: { 
                    orderId: {{ $order->id }},
                    total: {{ $order->total }},
                    timestamp: new Date().toISOString()
                }
            }));
        }, 500);
        
        // Mark as printed in session to avoid reprinting on page refresh
        @php
            session(['printed_order_' . $order->id => true]);
        @endphp
    @endif
});
</script>
@endpush
@endsection

