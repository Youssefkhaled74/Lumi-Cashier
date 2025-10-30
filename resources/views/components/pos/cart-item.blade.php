@props(['item'])

@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';
@endphp

<div class="cart-item bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-all border-2 border-transparent hover:border-indigo-300"
     x-data="{ localQty: {{ $item['quantity'] }} }">
    
    <div class="flex items-start gap-3">
        <!-- Product Icon -->
        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="bi bi-box-seam text-indigo-600 text-xl"></i>
        </div>

        <!-- Product Details -->
        <div class="flex-1 min-w-0">
            <!-- Product Name -->
            <h4 class="font-semibold text-gray-900 text-sm mb-1 truncate" title="{{ $item['name'] }}">
                {{ $item['name'] }}
            </h4>

            <!-- SKU & Category -->
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                <span>{{ $item['sku'] }}</span>
                <span>•</span>
                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full">
                    {{ $item['category'] }}
                </span>
            </div>

            <!-- Price Info -->
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-gray-600">
                    {{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}
                </span>
                <span class="font-bold text-indigo-600">
                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                </span>
            </div>

            <!-- Quantity Controls -->
            <div class="flex items-center gap-2">
                <!-- Decrease -->
                <button type="button"
                        @click="$dispatch('update-cart-qty', { id: {{ $item['id'] }}, quantity: Math.max(1, {{ $item['quantity'] }} - 1) })"
                        class="w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition-all">
                    <i class="bi bi-dash text-gray-700"></i>
                </button>

                <!-- Quantity Display -->
                <div class="flex-1 text-center">
                    <input type="number"
                           x-model="localQty"
                           @change="$dispatch('update-cart-qty', { id: {{ $item['id'] }}, quantity: Math.min({{ $item['maxStock'] }}, Math.max(1, parseInt(localQty) || 1)) })"
                           min="1"
                           max="{{ $item['maxStock'] }}"
                           class="w-full text-center border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-0 font-semibold text-sm py-1">
                </div>

                <!-- Increase -->
                <button type="button"
                        @click="if ({{ $item['quantity'] }} < {{ $item['maxStock'] }}) { $dispatch('update-cart-qty', { id: {{ $item['id'] }}, quantity: {{ $item['quantity'] }} + 1 }) } else { alert('@lang('pos.max_stock_reached')') }"
                        class="w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition-all {{ $item['quantity'] >= $item['maxStock'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <i class="bi bi-plus text-gray-700"></i>
                </button>

                <!-- Remove Button -->
                <button type="button"
                        @click="if(confirm('@lang('pos.confirm_delete')')) { $dispatch('remove-from-cart', { id: {{ $item['id'] }} }) }"
                        class="w-8 h-8 flex items-center justify-center bg-red-50 border-2 border-red-300 rounded-lg hover:bg-red-500 hover:border-red-500 hover:text-white transition-all ml-2">
                    <i class="bi bi-trash text-red-600 hover:text-white"></i>
                </button>
            </div>

            <!-- Stock Warning -->
            @if($item['quantity'] >= $item['maxStock'])
            <p class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                <i class="bi bi-exclamation-triangle-fill"></i>
                @lang('pos.max_stock_reached')
            </p>
            @endif
        </div>
    </div>
</div>
