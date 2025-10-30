@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Create Category</h2>
                <p class="text-gray-600">Add a new category to organize your products</p>
            </div>
            <a href="{{ route('categories.index') }}" class="flex items-center space-x-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-300">
                <i class="bi bi-arrow-left"></i>
                <span>Back to Categories</span>
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert-animate bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-lg">
            <div class="flex items-start">
                <i class="bi bi-exclamation-circle-fill text-xl mr-3 mt-0.5"></i>
                <div>
                    <p class="font-bold mb-2">There were some errors with your submission:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-2xl">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="bi bi-folder text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            required
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 shadow-sm hover:border-purple-300"
                            placeholder="e.g., Electronics, Clothing, Food"
                        >
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
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                        Description
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-4 pointer-events-none">
                            <i class="bi bi-text-left text-gray-400"></i>
                        </div>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 shadow-sm hover:border-purple-300"
                            placeholder="Optional description for this category"
                        >{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="bi bi-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('categories.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 shadow-sm hover:shadow">
                        <i class="bi bi-x-circle mr-2"></i>
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center"
                    >
                        <i class="bi bi-check-circle mr-2"></i>
                        Create Category
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
