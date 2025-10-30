@extends('layouts.admin')

@section('title', $item->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('items.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
                <span class="font-medium">Back to Items</span>
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ $item->name }}</h1>
        </div>
        <a href="{{ route('items.edit', $item) }}" class="btn-gradient px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
            <i class="bi bi-pencil"></i>
            <span>Edit Item</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in flex items-center">
            <i class="bi bi-check-circle-fill mr-3 text-2xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-1 h-6 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full mr-3"></div>
                    Item Details
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center"><i class="bi bi-box mr-2"></i>Item Name</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $item->name }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center"><i class="bi bi-folder mr-2"></i>Category</h3>
                        <a href="{{ route('categories.show', $item->category) }}" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium hover:bg-purple-200 transition-colors">
                            {{ $item->category->name }}
                        </a>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center"><i class="bi bi-upc mr-2"></i>SKU</h3>
                        <p class="text-lg text-gray-700 font-mono bg-white px-2 py-1 rounded inline-block">{{ $item->sku }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center"><i class="bi bi-currency-dollar mr-2"></i>Price</h3>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($item->price, 2) }}</p>
                    </div>
                    @if($item->description)
                    <div class="md:col-span-2 p-4 bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center"><i class="bi bi-file-text mr-2"></i>Description</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $item->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="bi bi-upc-scan mr-3 text-2xl"></i>
                        Stock Units
                        <span class="ml-3 px-4 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm">{{ $item->units->count() }} units</span>
                    </h2>
                </div>
                
                @if($item->units->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 data-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($item->units->take(10) as $unit)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">#{{ $unit->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $unit->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $unit->status === 'sold' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $unit->status === 'damaged' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($unit->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">${{ number_format($unit->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $unit->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($item->units->count() > 10)
                        <div class="px-6 py-3 bg-gray-50 text-center text-sm text-gray-500">Showing 10 of {{ $item->units->count() }} units</div>
                    @endif
                </div>
                @else
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-4">
                        <i class="bi bi-inbox text-5xl text-indigo-400"></i>
                    </div>
                    <p class="text-xl text-gray-500 mb-4">No stock units available</p>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-bar-chart mr-2 text-indigo-600"></i>
                    Stock Summary
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="bi bi-check-circle text-green-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Available</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">{{ $item->available_stock }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="bi bi-cart-check text-blue-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Sold</span>
                        </div>
                        <span class="text-lg font-bold text-blue-600">{{ $item->sold_stock }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="bi bi-x-circle text-red-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Damaged</span>
                        </div>
                        <span class="text-lg font-bold text-red-600">{{ $item->damaged_stock }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-plus-circle mr-2 text-indigo-600"></i>
                    Add Stock
                </h2>
                <form action="{{ route('items.add-stock', $item) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" id="quantity" name="quantity" min="1" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter quantity">
                    </div>
                    <button type="submit" class="w-full btn-gradient py-3 rounded-lg font-semibold flex items-center justify-center space-x-2">
                        <i class="bi bi-plus-circle"></i>
                        <span>Add Units</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
