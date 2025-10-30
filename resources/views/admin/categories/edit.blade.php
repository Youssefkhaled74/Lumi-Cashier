@extends('layouts.admin')

@section('title', 'Edit ' . $category->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('categories.show', $category) }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors">
            <i class="bi bi-arrow-left text-xl"></i>
            <span class="font-medium">Back to Category</span>
        </a>
        <div class="h-6 w-px bg-gray-300"></div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Edit Category
        </h1>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-start">
                <i class="bi bi-exclamation-circle-fill mr-3 mt-0.5 text-2xl"></i>
                <div>
                    <p class="font-semibold mb-2">There were some errors with your submission:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="bi bi-tag mr-2 text-indigo-600"></i>
                        Category Name <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $category->name) }}"
                            required
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="e.g., Electronics, Clothing, Food"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-tag"></i>
                        </div>
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="bi bi-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="bi bi-file-text mr-2 text-indigo-600"></i>
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                        placeholder="Optional description for this category"
                    >{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="bi bi-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('categories.show', $category) }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                        <i class="bi bi-x-circle mr-2"></i>
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="btn-gradient px-8 py-3 rounded-lg font-semibold flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all"
                    >
                        <i class="bi bi-check-circle"></i>
                        <span>Update Category</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
