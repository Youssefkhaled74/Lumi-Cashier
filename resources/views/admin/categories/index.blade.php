@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Categories</h2>
            <p class="text-gray-600">Organize your products into categories</p>
        </div>
        <a href="{{ route('categories.create') }}" class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <i class="bi bi-plus-circle text-xl"></i>
            <span class="font-semibold">New Category</span>
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center shadow-sm">
                                        <i class="bi bi-folder text-2xl text-purple-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $category->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 font-mono">{{ $category->slug }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                    <i class="bi bi-box mr-1"></i> {{ $category->items_count ?? $category->items->count() }} items
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ Str::limit($category->description, 50) ?? 'No description' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('categories.show', $category) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                        <i class="bi bi-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('categories.edit', $category) }}" class="text-purple-600 hover:text-purple-900 transition-colors" title="Edit">
                                        <i class="bi bi-pencil text-lg"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                            <i class="bi bi-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="bi bi-folder-x text-6xl mb-4"></i>
                                    <p class="text-xl font-semibold mb-2">No categories found</p>
                                    <p class="text-sm mb-4">Start by creating your first category</p>
                                    <a href="{{ route('categories.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                        <i class="bi bi-plus-circle mr-2"></i>
                                        Create your first category
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

