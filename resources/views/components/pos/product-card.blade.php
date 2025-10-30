@props(['product'])

@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';
@endphp

<div class="product-card group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-indigo-500 {{ $product->available_stock <= 0 ? 'opacity-60' : '' }}"
     x-data="{ 
         qty: 0,
         maxStock: {{ $product->available_stock }}
     }">
    
    <!-- Stock Badge -->
    @if($product->available_stock <= 0)
        <div class="absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-10">
            <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                @lang('pos.out_of_stock')
            </span>
        </div>
    @elseif($product->available_stock <= 10)
        <div class="absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-10">
            <span class="px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full shadow-lg">
                @lang('pos.low_stock')
            </span>
        </div>
    @endif

    <!-- Product Image Placeholder -->
    <div class="relative h-40 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-indigo-500 opacity-0 group-hover:opacity-10 transition-opacity"></div>
        <i class="bi bi-box-seam text-6xl text-indigo-400 group-hover:text-indigo-600 transition-colors"></i>
    </div>

    <!-- Product Info -->
    <div class="p-4">
        <!-- Category Badge -->
        <div class="mb-2">
            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                {{ $product->category->name }}
            </span>
        </div>

        <!-- Product Name -->
        <h3 class="font-bold text-gray-900 text-lg mb-1 line-clamp-2 group-hover:text-indigo-600 transition-colors" title="{{ $product->name }}">
            {{ $product->name }}
        </h3>

        <!-- SKU -->
        <p class="text-xs text-gray-500 mb-3">
            @lang('pos.sku'): {{ $product->sku }}
        </p>

        <!-- Price & Stock -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-2xl font-bold text-indigo-600">
                    ${{ number_format($product->price, 2) }}
                </p>
            </div>
            <div class="{{ $isRtl ? 'text-left' : 'text-right' }}">
                <p class="text-xs text-gray-500">@lang('pos.stock')</p>
                <p class="text-sm font-semibold {{ $product->available_stock <= 10 ? 'text-amber-600' : 'text-green-600' }}">
                    {{ $product->available_stock }} @lang('pos.units')
                </p>
            </div>
        </div>

        <!-- Quantity Input & Add Button -->
        @if($product->available_stock > 0)
        <div class="flex items-center gap-2">
            <!-- Quantity Controls -->
            <div class="flex items-center border-2 border-gray-300 rounded-lg overflow-hidden flex-1">
                <button type="button" 
                        @click="qty = Math.max(0, qty - 1)"
                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors"
                        :disabled="qty <= 0">
                    <i class="bi bi-dash text-gray-600"></i>
                </button>
                <input type="number" 
                       x-model="qty"
                       min="0"
                       :max="maxStock"
                       class="w-full text-center border-0 focus:ring-0 font-semibold"
                       @input="qty = Math.min(maxStock, Math.max(0, parseInt($event.target.value) || 0))">
                <button type="button" 
                        @click="qty = Math.min(maxStock, qty + 1)"
                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors"
                        :disabled="qty >= maxStock">
                    <i class="bi bi-plus text-gray-600"></i>
                </button>
            </div>

            <!-- Add to Cart Button -->
            <button type="button"
                    @click="$dispatch('add-to-cart', { 
                        id: {{ $product->id }}, 
                        name: '{{ addslashes($product->name) }}', 
                        price: {{ $product->price }}, 
                        quantity: qty,
                        maxStock: maxStock,
                        sku: '{{ $product->sku }}',
                        category: '{{ $product->category->name }}'
                    }); qty = 0;"
                    :disabled="qty <= 0"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                <i class="bi bi-cart-plus text-lg"></i>
            </button>
        </div>

        <!-- Quick Add Button -->
        <button type="button"
                @click="$dispatch('add-to-cart', { 
                    id: {{ $product->id }}, 
                    name: '{{ addslashes($product->name) }}', 
                    price: {{ $product->price }}, 
                    quantity: 1,
                    maxStock: maxStock,
                    sku: '{{ $product->sku }}',
                    category: '{{ $product->category->name }}'
                });"
                class="w-full mt-2 px-4 py-2 bg-white border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg transition-all font-semibold">
                <i class="bi bi-lightning-charge-fill {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                @lang('pos.quick_add')
            </button>
        @else
        <div class="text-center py-3 bg-red-50 rounded-lg">
            <p class="text-red-600 font-semibold text-sm">@lang('pos.out_of_stock')</p>
        </div>
        @endif
    </div>
</div>
