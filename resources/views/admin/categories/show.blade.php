@extends('layouts.admin')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('categories.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
                <span class="font-medium">Back to Categories</span>
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                {{ $category->name }}
            </h1>
        </div>
        <a href="{{ route('categories.edit', $category) }}" class="btn-gradient px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
            <i class="bi bi-pencil"></i>
            <span>Edit Category</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in flex items-center">
            <i class="bi bi-check-circle-fill mr-3 text-2xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Category Info Card -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-6 hover:shadow-xl transition-shadow">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <div class="w-1 h-6 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full mr-3"></div>
            Category Details
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl">
                <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center">
                    <i class="bi bi-tag mr-2"></i>
                    Category Name
                </h3>
                <p class="text-2xl font-bold text-gray-900">{{ $category->name }}</p>
            </div>
            <div class="p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl">
                <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center">
                    <i class="bi bi-link-45deg mr-2"></i>
                    Slug
                </h3>
                <p class="text-lg text-gray-700 font-mono">{{ $category->slug }}</p>
            </div>
            @if($category->description)
            <div class="md:col-span-2 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl">
                <h3 class="text-sm font-medium text-gray-500 uppercase mb-2 flex items-center">
                    <i class="bi bi-file-text mr-2"></i>
                    Description
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $category->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Items in this Category -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-indigo-600 to-purple-600">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="bi bi-box-seam mr-3 text-2xl"></i>
                Items in this Category
                <span class="ml-3 px-4 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm">
                    {{ $category->items->count() }} items
                </span>
            </h2>
            <a href="{{ route('items.create') }}?category={{ $category->id }}" class="px-5 py-2.5 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 font-semibold transition-colors shadow-md hover:shadow-lg flex items-center space-x-2">
                <i class="bi bi-plus-circle"></i>
                <span>Add Item</span>
            </a>
        </div>
        
        @if($category->items->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="bi bi-box mr-1"></i> Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="bi bi-upc mr-1"></i> SKU
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="bi bi-currency-dollar mr-1"></i> Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="bi bi-box-seam mr-1"></i> Stock
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="bi bi-gear mr-1"></i> Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($category->items as $item)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $item->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 font-mono bg-gray-100 px-2 py-1 rounded inline-block">
                                    {{ $item->sku }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-green-600">${{ number_format($item->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $item->available_stock > 10 ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $item->available_stock > 0 && $item->available_stock <= 10 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $item->available_stock == 0 ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $item->available_stock }} units
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold flex items-center space-x-1">
                                    <i class="bi bi-eye"></i>
                                    <span>View</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-16 text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-4">
                <i class="bi bi-inbox text-5xl text-indigo-400"></i>
            </div>
            <p class="text-xl text-gray-500 mb-4">No items in this category yet</p>
            <a href="{{ route('items.create') }}?category={{ $category->id }}" class="inline-flex items-center px-6 py-3 btn-gradient text-white rounded-xl hover:shadow-lg transition-all">
                <i class="bi bi-plus-circle mr-2"></i>
                Add First Item
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
