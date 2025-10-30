@extends('layouts.admin')

@section('title', __('pos.title'))

@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50" 
     x-data="posSystem()"
     @add-to-cart.window="addToCart($event.detail)"
     @update-cart-qty.window="updateCartQuantity($event.detail.id, $event.detail.quantity)"
     @remove-from-cart.window="removeFromCart($event.detail.id)">

    <!-- Top Bar -->
    <div class="bg-white shadow-lg border-b-4 border-indigo-600 sticky top-0 z-40">
        <div class="max-w-[1920px] mx-auto px-4 py-4">
            <div class="flex items-center justify-between gap-4">
                <!-- Logo & Title -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="bi bi-shop text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            @lang('pos.title')
                        </h1>
                        <p class="text-sm text-gray-600">
                            @lang('pos.welcome'), {{ auth()->user()->name ?? 'Admin' }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <!-- Held Orders Button -->
                    <button @click="showHeldOrders = true"
                            type="button"
                            class="relative px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="bi bi-pause-circle text-xl"></i>
                        <span class="hidden md:inline font-semibold">@lang('pos.held_orders')</span>
                        <span x-show="heldOrders.length > 0" 
                              x-text="heldOrders.length"
                              class="absolute -top-2 -{{ $isRtl ? 'left' : 'right' }}-2 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"></span>
                    </button>

                    <!-- Settings Button -->
                    <a href="{{ route('settings.index') }}" 
                       class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="bi bi-gear-fill text-xl"></i>
                        <span class="hidden md:inline font-semibold">@lang('pos.settings')</span>
                    </a>

                    <!-- Language Switcher -->
                    <x-pos.language-switcher />

                    <!-- Back to Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="bi bi-grid text-xl"></i>
                        <span class="hidden md:inline font-semibold">@lang('pos.dashboard')</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(isset($error))
        <!-- Error Message -->
        <div class="max-w-[1920px] mx-auto px-4 py-6">
            <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-6 shadow-lg">
                <div class="flex items-start gap-4">
                    <i class="bi bi-exclamation-triangle-fill text-red-500 text-3xl flex-shrink-0"></i>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800 text-lg mb-2">@lang('pos.error')</h3>
                        <p class="text-red-700 mb-4">{{ $error }}</p>
                        <a href="{{ route('day.status') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-md">
                            <i class="bi bi-calendar-check"></i>
                            @lang('pos.open_day')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($items) && $items->count() > 0)
        <!-- Main POS Interface -->
        <div class="max-w-[1920px] mx-auto px-4 py-6">
            
            <!-- Keyboard Shortcuts -->
            <div class="mb-6">
                <x-pos.keyboard-hint />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Products Section (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Search & Filter Bar -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                        <div class="flex flex-col gap-4">
                            <!-- Search Input -->
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-1 relative">
                                    <i class="bi bi-search absolute {{ $isRtl ? 'right-4' : 'left-4' }} top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                                    <input type="text"
                                           x-model="searchQuery"
                                           @keydown.f2.prevent="$el.focus()"
                                           @input="searchProducts()"
                                           class="w-full {{ $isRtl ? 'pr-12 pl-4' : 'pl-12 pr-4' }} py-4 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-0 text-lg"
                                           placeholder="@lang('pos.search_placeholder')">
                                </div>

                                <!-- Reset Filter Button -->
                                <button @click="selectedCategory = ''; filterProducts()"
                                        x-show="selectedCategory !== ''"
                                        type="button"
                                        class="px-6 py-4 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold transition-all flex items-center gap-2">
                                    <i class="bi bi-x-circle"></i>
                                    <span class="hidden md:inline">Clear Filter</span>
                                </button>
                            </div>

                            <!-- Category Pills Filter -->
                            <div class="flex flex-wrap gap-2">
                                <button @click="selectedCategory = ''; filterProducts()"
                                        type="button"
                                        :class="selectedCategory === '' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                        class="px-4 py-2 rounded-full font-semibold transition-all flex items-center gap-2 shadow-md">
                                    <i class="bi bi-grid-fill"></i>
                                    All Items
                                </button>
                                @foreach($items->pluck('category')->unique('id')->sortBy('name') as $category)
                                    <button @click="selectedCategory = '{{ $category->id }}'; filterProducts()"
                                            type="button"
                                            :class="selectedCategory === '{{ $category->id }}' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                            class="px-4 py-2 rounded-full font-semibold transition-all flex items-center gap-2 shadow-md">
                                        <i class="bi bi-folder-fill"></i>
                                        {{ $category->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Most Ordered Items Section -->
                    @if(isset($mostOrderedItems) && $mostOrderedItems->count() > 0)
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-xl p-6 border-2 border-amber-300">
                        <h2 class="text-xl font-bold text-amber-900 mb-4 flex items-center gap-3">
                            <i class="bi bi-fire text-orange-600 text-2xl"></i>
                            🔥 Most Ordered Items
                            <span class="text-sm font-normal text-amber-700">(Top {{ $mostOrderedItems->count() }})</span>
                        </h2>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3">
                            @foreach($mostOrderedItems as $popularItem)
                                <button @click="addToCart({
                                    id: {{ $popularItem->id }},
                                    name: '{{ addslashes($popularItem->name) }}',
                                    price: {{ $popularItem->price }},
                                    maxStock: {{ $popularItem->available_stock ?? 0 }},
                                    sku: '{{ $popularItem->sku }}',
                                    category: '{{ $popularItem->category->name ?? 'N/A' }}'
                                })"
                                        type="button"
                                        class="bg-white hover:bg-gradient-to-br hover:from-amber-100 hover:to-orange-100 border-2 border-amber-300 hover:border-orange-500 rounded-xl p-3 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-1">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center">
                                            <i class="bi bi-star-fill text-white text-xl"></i>
                                        </div>
                                        <p class="font-bold text-gray-800 text-sm mb-1 truncate">{{ $popularItem->name }}</p>
                                        <p class="text-xs text-gray-600 mb-2">{{ $popularItem->category->name ?? 'N/A' }}</p>
                                        <p class="text-lg font-bold text-indigo-600">${{ number_format($popularItem->price, 2) }}</p>
                                        <p class="text-xs text-amber-700 font-semibold mt-1">
                                            <i class="bi bi-graph-up-arrow"></i>
                                            {{ $popularItem->total_ordered ?? 0 }} sold
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Products Grid -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <i class="bi bi-box-seam text-indigo-600 text-2xl"></i>
                            @lang('pos.available_items')
                            <span x-text="`(${filteredItems.length})`" class="text-gray-500"></span>
                        </h2>

                        <!-- Loading State -->
                        <div x-show="isLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            <div class="animate-pulse bg-gray-200 rounded-2xl h-80" x-show="isLoading"></div>
                            <div class="animate-pulse bg-gray-200 rounded-2xl h-80" x-show="isLoading"></div>
                            <div class="animate-pulse bg-gray-200 rounded-2xl h-80" x-show="isLoading"></div>
                        </div>

                        <!-- Products Grid -->
                        <div x-show="!isLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 max-h-[calc(100vh-400px)] overflow-y-auto pr-2">
                            <template x-for="item in filteredItems" :key="item.id">
                                <div>
                                    @foreach($items as $item)
                                        <div x-show="filteredItems.find(i => i.id === {{ $item->id }})">
                                            <x-pos.product-card :product="$item" />
                                        </div>
                                    @endforeach
                                </div>
                            </template>
                        </div>

                        <!-- No Results -->
                        <div x-show="!isLoading && filteredItems.length === 0" 
                             class="text-center py-16">
                            <i class="bi bi-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg font-semibold">@lang('pos.no_results')</p>
                        </div>
                    </div>
                </div>

                <!-- Cart Section (1/3 width) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 sticky top-24 overflow-hidden">
                        
                        <!-- Cart Header -->
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                            <div class="flex items-center justify-between text-white">
                                <div class="flex items-center gap-3">
                                    <i class="bi bi-cart3 text-2xl"></i>
                                    <div>
                                        <h2 class="text-xl font-bold">@lang('pos.cart')</h2>
                                        <p class="text-sm opacity-90" x-text="`${Object.keys(cart).length} ${Object.keys(cart).length === 1 ? '@lang('pos.item')' : '@lang('pos.items')'}`"></p>
                                    </div>
                                </div>
                                <button @click="clearCart()" 
                                        x-show="Object.keys(cart).length > 0"
                                        class="px-3 py-1 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition-all">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Cart Items -->
                        <div class="p-4 space-y-3 max-h-[400px] overflow-y-auto">
                            <!-- Empty Cart -->
                            <div x-show="Object.keys(cart).length === 0" class="text-center py-12">
                                <i class="bi bi-cart-x text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 font-semibold">@lang('pos.cart_empty')</p>
                                <p class="text-sm text-gray-400">@lang('pos.cart_empty_message')</p>
                            </div>

                            <!-- Cart Items List -->
                            <template x-for="(item, id) in cart" :key="id">
                                <div>
                                    <x-pos.cart-item :item="['id' => 0, 'name' => '', 'price' => 0, 'quantity' => 0, 'maxStock' => 0, 'sku' => '', 'category' => '']" x-bind:item="item" />
                                </div>
                            </template>
                        </div>

                        <!-- Cart Summary -->
                        <div x-show="Object.keys(cart).length > 0" class="border-t-2 border-gray-200 p-6 bg-gray-50">
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-gray-700">
                                    <span>@lang('pos.total_items'):</span>
                                    <span class="font-semibold" x-text="totalItems"></span>
                                </div>
                                <div class="flex justify-between text-2xl font-bold text-indigo-600">
                                    <span>@lang('pos.total'):</span>
                                    <span x-text="`$${totalAmount.toFixed(2)}`"></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <!-- Checkout Button -->
                                <button @click="proceedToCheckout()"
                                        @keydown.f4.prevent="proceedToCheckout()"
                                        type="button"
                                        class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 text-lg">
                                    <i class="bi bi-check-circle text-2xl"></i>
                                    @lang('pos.proceed_to_checkout')
                                </button>

                                <!-- Hold Order Button -->
                                <button @click="holdOrder()"
                                        @keydown.ctrl.h.prevent="holdOrder()"
                                        type="button"
                                        class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                    <i class="bi bi-pause-circle"></i>
                                    @lang('pos.hold_order')
                                </button>

                                <!-- Clear Cart Button -->
                                <button @click="clearCart()"
                                        type="button"
                                        class="w-full py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                    <i class="bi bi-x-circle"></i>
                                    @lang('pos.clear_cart')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Modal -->
        <x-pos.modal title="@lang('pos.checkout')" size="lg">
            <form @submit.prevent="completeOrder()" class="space-y-6">
                
                <!-- Order Summary -->
                <div class="bg-indigo-50 rounded-xl p-6 border-2 border-indigo-200">
                    <h3 class="font-bold text-lg text-indigo-900 mb-4">@lang('pos.order_notes')</h3>
                    <div class="space-y-2">
                        <template x-for="(item, id) in cart" :key="id">
                            <div class="flex justify-between text-sm">
                                <span x-text="`${item.quantity}x ${item.name}`"></span>
                                <span class="font-semibold" x-text="`$${(item.price * item.quantity).toFixed(2)}`"></span>
                            </div>
                        </template>
                        <div class="border-t-2 border-indigo-300 pt-2 mt-2 flex justify-between text-lg font-bold text-indigo-900">
                            <span>@lang('pos.total'):</span>
                            <span x-text="`$${totalAmount.toFixed(2)}`"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-4">
                        @lang('pos.payment_method') <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <x-pos.payment-method method="cash" selected="true" />
                        <x-pos.payment-method method="card" />
                        <x-pos.payment-method method="mobile_payment" />
                    </div>
                </div>

                <!-- Order Notes -->
                <div>
                    <label for="orderNotes" class="block text-sm font-bold text-gray-700 mb-2">
                        @lang('pos.order_notes') (@lang('pos.optional'))
                    </label>
                    <textarea id="orderNotes"
                              x-model="orderNotes"
                              rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-0 resize-none"
                              placeholder="@lang('pos.add_notes')"></textarea>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="button"
                            @click="$dispatch('close-modal')"
                            class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-xl transition-all">
                        @lang('pos.cancel')
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all">
                        @lang('pos.complete_payment')
                    </button>
                </div>
            </form>
        </x-pos.modal>

        <!-- Held Orders Modal -->
        <div x-show="showHeldOrders"
             @keydown.escape.window="showHeldOrders = false"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="showHeldOrders = false" class="fixed inset-0 bg-black opacity-50"></div>
                <div @click.stop class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                        <div class="flex items-center justify-between text-white">
                            <h2 class="text-2xl font-bold flex items-center gap-3">
                                <i class="bi bi-pause-circle"></i>
                                @lang('pos.held_orders')
                            </h2>
                            <button @click="showHeldOrders = false" class="hover:bg-white hover:bg-opacity-20 rounded-lg p-2">
                                <i class="bi bi-x-lg text-xl"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 max-h-[60vh] overflow-y-auto">
                        <div x-show="heldOrders.length === 0" class="text-center py-12">
                            <i class="bi bi-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 font-semibold">@lang('pos.no_held_orders')</p>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(order, index) in heldOrders" :key="index">
                                <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <p class="font-bold text-gray-900" x-text="`@lang('pos.order_id') #${index + 1}`"></p>
                                            <p class="text-sm text-gray-600" x-text="`@lang('pos.held_at'): ${order.heldAt}`"></p>
                                        </div>
                                        <button @click="resumeOrder(index)" 
                                                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition-all">
                                            <i class="bi bi-play-circle {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                                            @lang('pos.resume')
                                        </button>
                                    </div>
                                    <div class="space-y-1">
                                        <template x-for="(item, id) in order.cart" :key="id">
                                            <div class="flex justify-between text-sm">
                                                <span x-text="`${item.quantity}x ${item.name}`"></span>
                                                <span class="font-semibold" x-text="`$${(item.price * item.quantity).toFixed(2)}`"></span>
                                            </div>
                                        </template>
                                        <div class="border-t border-amber-300 pt-1 mt-1 flex justify-between font-bold">
                                            <span>@lang('pos.total'):</span>
                                            <span x-text="`$${order.total.toFixed(2)}`"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<form id="checkoutForm" method="POST" action="{{ route('orders.store') }}" style="display: none;">
    @csrf
    <input type="hidden" name="notes" id="formNotes">
    <input type="hidden" name="payment_method" id="formPaymentMethod">
    <!-- items[] will be dynamically added via JavaScript -->
</form>

@push('scripts')
<script>
function posSystem() {
    return {
        cart: {},
        searchQuery: '',
        selectedCategory: '',
        isLoading: false,
        orderNotes: '',
        showHeldOrders: false,
        heldOrders: JSON.parse(localStorage.getItem('heldOrders') || '[]'),
        allItems: @json($items ?? []),
        filteredItems: @json($items ?? []),

        init() {
            this.filteredItems = this.allItems;
            this.loadCart();
            this.setupKeyboardShortcuts();
        },

        loadCart() {
            const saved = localStorage.getItem('posCart');
            if (saved) {
                this.cart = JSON.parse(saved);
            }
        },

        saveCart() {
            localStorage.setItem('posCart', JSON.stringify(this.cart));
        },

        addToCart(product) {
            const { id, name, price, quantity, maxStock, sku, category } = product;
            
            if (quantity <= 0) return;

            if (this.cart[id]) {
                const newQty = this.cart[id].quantity + quantity;
                if (newQty > maxStock) {
                    this.showNotification('@lang('pos.max_stock_reached')', 'warning');
                    return;
                }
                this.cart[id].quantity = newQty;
            } else {
                this.cart[id] = { id, name, price, quantity, maxStock, sku, category };
            }

            this.saveCart();
            this.playBeep();
            this.showNotification('@lang('pos.item_added')', 'success');
        },

        updateCartQuantity(id, quantity) {
            if (this.cart[id]) {
                if (quantity <= 0) {
                    this.removeFromCart(id);
                } else if (quantity <= this.cart[id].maxStock) {
                    this.cart[id].quantity = quantity;
                    this.saveCart();
                } else {
                    this.showNotification('@lang('pos.max_stock_reached')', 'warning');
                }
            }
        },

        removeFromCart(id) {
            if (confirm('@lang('pos.confirm_delete')')) {
                delete this.cart[id];
                this.saveCart();
                this.showNotification('@lang('pos.item_removed')', 'info');
            }
        },

        clearCart() {
            if (confirm('@lang('pos.confirm_clear_cart')')) {
                this.cart = {};
                this.saveCart();
                this.showNotification('@lang('pos.cart_cleared')', 'info');
            }
        },

        holdOrder() {
            if (Object.keys(this.cart).length === 0) return;

            const order = {
                cart: { ...this.cart },
                total: this.totalAmount,
                heldAt: new Date().toLocaleString()
            };

            this.heldOrders.push(order);
            localStorage.setItem('heldOrders', JSON.stringify(this.heldOrders));
            
            this.clearCart();
            this.showNotification('@lang('pos.order_held')', 'success');
        },

        resumeOrder(index) {
            const order = this.heldOrders[index];
            this.cart = { ...order.cart };
            this.saveCart();
            
            this.heldOrders.splice(index, 1);
            localStorage.setItem('heldOrders', JSON.stringify(this.heldOrders));
            
            this.showHeldOrders = false;
            this.showNotification('@lang('pos.order_resumed')', 'success');
        },

        proceedToCheckout() {
            if (Object.keys(this.cart).length === 0) return;
            this.$dispatch('open-modal', { name: '@lang('pos.checkout')' });
        },

        completeOrder() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            if (!paymentMethod) {
                alert('@lang('pos.select_payment_method')');
                return;
            }

            console.log('=== Order Submission Debug ===');
            console.log('Cart:', this.cart);

            // Clear existing inputs
            const form = document.getElementById('checkoutForm');
            const existingInputs = form.querySelectorAll('input[name^="items["]');
            existingInputs.forEach(input => input.remove());

            // Prepare form data - Create proper array structure
            const items = [];
            Object.values(this.cart).forEach(item => {
                items.push({
                    item_id: item.id,
                    quantity: item.quantity
                });
            });

            console.log('Items to submit:', items);

            // Add items as proper form inputs
            items.forEach((item, index) => {
                const itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = `items[${index}][item_id]`;
                itemIdInput.value = item.item_id;
                form.appendChild(itemIdInput);

                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = `items[${index}][quantity]`;
                quantityInput.value = item.quantity;
                form.appendChild(quantityInput);

                console.log(`Added: items[${index}][item_id]=${item.item_id}, items[${index}][quantity]=${item.quantity}`);
            });

            document.getElementById('formNotes').value = this.orderNotes;
            document.getElementById('formPaymentMethod').value = paymentMethod.value;

            console.log('Form data:');
            console.log('  Notes:', this.orderNotes);
            console.log('  Payment Method:', paymentMethod.value);
            console.log('  Total items:', items.length);
            console.log('Form action:', form.action);
            console.log('Submitting form...');
            
            // Submit form
            form.submit();
        },

        searchProducts() {
            this.filterProducts();
        },

        filterProducts() {
            let filtered = this.allItems;

            // Search filter
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(item => 
                    item.name.toLowerCase().includes(query) ||
                    item.sku.toLowerCase().includes(query) ||
                    item.category.name.toLowerCase().includes(query)
                );
            }

            // Category filter
            if (this.selectedCategory) {
                filtered = filtered.filter(item => 
                    item.category.id == this.selectedCategory
                );
            }

            this.filteredItems = filtered;
        },

        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // F2 - Focus search
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.querySelector('input[x-model="searchQuery"]')?.focus();
                }
                
                // F4 - Checkout
                if (e.key === 'F4') {
                    e.preventDefault();
                    this.proceedToCheckout();
                }
                
                // Ctrl+H - Hold order
                if (e.ctrlKey && e.key === 'h') {
                    e.preventDefault();
                    this.holdOrder();
                }
                
                // Ctrl+K - Clear cart
                if (e.ctrlKey && e.key === 'k') {
                    e.preventDefault();
                    this.clearCart();
                }
            });
        },

        playBeep() {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSx+zPLTgjMGHW7A7+OZSA0PVK3o7aBSEgxIo+L0wWwhBi17y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQ==');
            audio.play().catch(() => {});
        },

        showNotification(message, type = 'info') {
            // Simple notification - you can enhance this
            console.log(`${type.toUpperCase()}: ${message}`);
        },

        get totalItems() {
            return Object.values(this.cart).reduce((sum, item) => sum + item.quantity, 0);
        },

        get totalAmount() {
            return Object.values(this.cart).reduce((sum, item) => sum + (item.price * item.quantity), 0);
        }
    }
}
</script>
@endpush
@endsection
