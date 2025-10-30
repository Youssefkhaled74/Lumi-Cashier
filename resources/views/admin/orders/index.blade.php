@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Orders</h1>
        <a href="{{ route('orders.create') }}" class="btn-gradient px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
            <i class="bi bi-plus-circle"></i>
            <span>New Order</span>
        </a>
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

    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="bi bi-receipt mr-3 text-2xl"></i>
                All Orders
            </h2>
        </div>

        @if($orders->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-hash mr-1"></i>Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-calendar mr-1"></i>Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-calendar-check mr-1"></i>Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-box mr-1"></i>Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-currency-dollar mr-1"></i>Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-info-circle mr-1"></i>Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="bi bi-gear mr-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-indigo-600">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Day #{{ $order->day->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->items->count() }} item(s)</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">${{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->is_completed)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><i class="bi bi-check-circle mr-1"></i>Completed</span>
                                @elseif($order->is_cancelled)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"><i class="bi bi-x-circle mr-1"></i>Cancelled</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="bi bi-clock mr-1"></i>Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('orders.invoice', $order) }}" class="text-green-600 hover:text-green-900" target="_blank"><i class="bi bi-file-pdf"></i></a>
                                @if($order->is_completed)
                                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('messages.confirm_cancel_order') }}')"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-16 text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-4">
                <i class="bi bi-inbox text-5xl text-indigo-400"></i>
            </div>
            <p class="text-xl text-gray-500 mb-4">No orders found</p>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-6 py-3 btn-gradient text-white rounded-xl hover:shadow-lg transition-all">
                <i class="bi bi-plus-circle mr-2"></i>
                Create your first order
            </a>
        </div>
        @endif

        @if($orders->count() > 0)
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
