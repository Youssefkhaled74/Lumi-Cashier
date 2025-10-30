@extends('layouts.admin')

@section('title', __('messages.create_order'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('orders.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors">
            <i class="bi bi-arrow-left text-xl"></i>
            <span class="font-medium">Back to Orders</span>
        </a>
        <div class="h-6 w-px bg-gray-300"></div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Create New Order</h1>
    </div>

    @if(isset($error))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 mb-6 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center mb-4">
                <i class="bi bi-exclamation-triangle-fill mr-3 text-3xl"></i>
                <div>
                    <strong class="font-bold text-lg">Error!</strong>
                    <p class="mt-1">{{ $error }}</p>
                </div>
            </div>
            <a href="{{ route('day.status') }}" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl">
                <i class="bi bi-calendar-check mr-2"></i> Manage Business Day
            </a>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in flex items-center">
            <i class="bi bi-exclamation-circle-fill mr-3 text-2xl"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(!isset($error) && $items->count() > 0)
    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Items Selection -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-xl rounded-xl p-6 hover:shadow-2xl transition-shadow">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-box-seam text-indigo-600 mr-3 text-2xl"></i> {{ __('messages.available_items') }}
                    </h2>

                    <!-- Search and Filter Section -->
                    <div class="mb-6 space-y-4">
                        <!-- Search Bar -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" placeholder="{{ __('messages.search_items') }}..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" onkeyup="filterItems()">
                        </div>

                        <!-- Category Filter -->
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="filterByCategory('all')" class="category-filter-btn active px-4 py-2 rounded-lg font-semibold transition-all" data-category="all">
                                <i class="bi bi-grid-fill mr-1"></i> {{ __('messages.all_categories') }}
                            </button>
                            @foreach($categories as $category)
                                <button type="button" onclick="filterByCategory('{{ $category->id }}')" class="category-filter-btn px-4 py-2 rounded-lg font-semibold transition-all" data-category="{{ $category->id }}">
                                    <i class="bi bi-folder mr-1"></i> {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div id="itemsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[600px] overflow-y-auto pr-2">
                        @foreach($items as $item)
                        <div class="item-card border-2 border-gray-200 rounded-xl p-4 hover:border-indigo-400 transition-all hover:shadow-md" data-item-id="{{ $item->id }}" data-item-name="{{ strtolower($item->name) }}" data-item-sku="{{ strtolower($item->sku) }}" data-category-id="{{ $item->category_id }}">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ __('messages.sku') }}: {{ $item->sku }}</p>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $item->category->name }}
                                    </span>
                                </div>
                                <div class="text-right ml-2">
                                    <p class="text-lg font-bold text-indigo-600">${{ number_format($item->price, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->available_stock }} {{ __('messages.available') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="number" id="qty-{{ $item->id }}" min="0" max="{{ $item->available_stock }}" value="0" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" onchange="updateCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, this.value, {{ $item->available_stock }})">
                                <button type="button" onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, {{ $item->available_stock }})" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md shadow-md transition-all">
                                    <i class="bi bi-plus-circle"></i> {{ __('messages.add') }}
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- No Results Message -->
                    <div id="noResults" class="hidden text-center py-8">
                        <i class="bi bi-search text-gray-300 text-5xl mb-3"></i>
                        <p class="text-gray-500 font-medium">{{ __('messages.no_items_found') }}</p>
                        <p class="text-gray-400 text-sm">{{ __('messages.try_different_search') }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-xl rounded-xl p-6 sticky top-6 hover:shadow-2xl transition-shadow">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-cart3 text-indigo-600 mr-3 text-2xl"></i> Order Summary
                    </h2>

                    <div id="cartItems" class="space-y-3 mb-6 max-h-80 overflow-y-auto">
                        <p class="text-gray-500 text-sm text-center py-8">No items added yet</p>
                    </div>

                    <!-- Discount Section -->
                    <div class="mb-4">
                        <label for="discount" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="bi bi-percent mr-2"></i> {{ __('pos.discount') }} (%)
                        </label>
                        <input type="number" id="discount" name="discount_percentage" min="0" max="100" step="0.01" value="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" oninput="calculateTotals()">
                        <p class="text-xs text-gray-500 mt-1">{{ __('pos.discount_note') }}</p>
                    </div>

                    <div class="border-t border-gray-200 pt-4 mb-4 space-y-2">
                        <div class="flex justify-between items-center text-gray-700">
                            <span>{{ __('pos.subtotal') }}:</span>
                            <span id="subtotalAmount" class="font-semibold">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-700">
                            <span>{{ __('pos.discount') }}:</span>
                            <span id="discountAmount" class="font-semibold text-red-600">-$0.00</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-700">
                            <span>{{ __('pos.tax') }} ({{ config('cashier.tax.default_rate', 15) }}%):</span>
                            <span id="taxAmount" class="font-semibold">$0.00</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 flex justify-between items-center text-lg font-bold">
                            <span class="text-gray-900">{{ __('pos.grand_total') }}:</span>
                            <span id="totalAmount" class="text-indigo-600">$0.00</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            <span>{{ __('messages.total_items') }}:</span>
                            <span id="totalItems" class="font-semibold">0</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="bi bi-file-text mr-2"></i> {{ __('messages.order_notes') }} ({{ __('messages.optional') }})
                        </label>
                        <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none" placeholder="{{ __('messages.add_notes') }}"></textarea>
                    </div>

                    <button type="submit" id="submitOrderBtn" disabled class="w-full btn-gradient py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ __('messages.create_order') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

<!-- Admin Verification Modal for Discount >5% -->
<div id="adminModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="bi bi-shield-lock text-amber-500 mr-2 text-2xl"></i>
                Admin Verification Required
            </h3>
            <button type="button" onclick="closeAdminModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        
        <div class="mb-4 p-3 bg-amber-50 border-l-4 border-amber-500 rounded">
            <p class="text-sm text-amber-800">
                <i class="bi bi-exclamation-triangle mr-1"></i>
                Discount exceeds <strong id="maxDiscountText">5%</strong>. Admin approval required.
            </p>
        </div>

        <div id="adminError" class="hidden mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
            <p class="text-sm text-red-800">
                <i class="bi bi-x-circle mr-1"></i>
                Invalid admin credentials. Please try again.
            </p>
        </div>
        
        <div class="space-y-4">
            <div>
                <label for="adminEmail" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="bi bi-envelope mr-1"></i> Admin Email
                </label>
                <input type="email" id="adminEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="admin@cashier.com">
            </div>
            
            <div>
                <label for="adminPassword" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="bi bi-key mr-1"></i> Admin Password
                </label>
                <input type="password" id="adminPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="••••••••">
            </div>
        </div>
        
        <div class="flex space-x-3 mt-6">
            <button type="button" onclick="closeAdminModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                Cancel
            </button>
            <button type="button" onclick="verifyAdmin()" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                Verify & Continue
            </button>
        </div>
    </div>
</div>

<script>
let cart = {};
let currentCategory = 'all';

// Filter items by search query
function filterItems() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const items = document.querySelectorAll('.item-card');
    const noResults = document.getElementById('noResults');
    const container = document.getElementById('itemsContainer');
    let visibleCount = 0;

    items.forEach(item => {
        const itemName = item.dataset.itemName;
        const itemSku = item.dataset.itemSku;
        const categoryId = item.dataset.categoryId;
        
        const matchesSearch = itemName.includes(searchTerm) || itemSku.includes(searchTerm);
        const matchesCategory = currentCategory === 'all' || categoryId === currentCategory;
        
        if (matchesSearch && matchesCategory) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });

    // Show/hide no results message
    if (visibleCount === 0) {
        container.classList.add('hidden');
        noResults.classList.remove('hidden');
    } else {
        container.classList.remove('hidden');
        noResults.classList.add('hidden');
    }
}

// Filter items by category
function filterByCategory(categoryId) {
    currentCategory = categoryId;
    
    // Update active button
    document.querySelectorAll('.category-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');
    
    // Apply filter
    filterItems();
}

function updateCart(itemId, itemName, itemPrice, quantity, maxStock) {
    quantity = parseInt(quantity) || 0;
    
    if (quantity > maxStock) {
        alert(`Only ${maxStock} units available for ${itemName}`);
        document.getElementById(`qty-${itemId}`).value = maxStock;
        quantity = maxStock;
    }
    
    if (quantity > 0) {
        cart[itemId] = { name: itemName, price: itemPrice, quantity: quantity, max: maxStock };
    } else {
        delete cart[itemId];
    }
    
    renderCart();
}

function addToCart(itemId, itemName, itemPrice, maxStock) {
    // If item already in cart, increase quantity
    if (cart[itemId]) {
        const newQty = Math.min(cart[itemId].quantity + 1, maxStock);
        cart[itemId].quantity = newQty;
        document.getElementById(`qty-${itemId}`).value = newQty;
    } else {
        // Add new item to cart with quantity 1
        cart[itemId] = { name: itemName, price: itemPrice, quantity: 1, max: maxStock };
        document.getElementById(`qty-${itemId}`).value = 1;
    }
    renderCart();
}

function increaseQuantity(itemId) {
    if (cart[itemId]) {
        const newQty = Math.min(cart[itemId].quantity + 1, cart[itemId].max);
        if (newQty > cart[itemId].quantity) {
            cart[itemId].quantity = newQty;
            document.getElementById(`qty-${itemId}`).value = newQty;
            renderCart();
        } else {
            alert(`Only ${cart[itemId].max} units available`);
        }
    }
}

function decreaseQuantity(itemId) {
    if (cart[itemId]) {
        const newQty = cart[itemId].quantity - 1;
        if (newQty > 0) {
            cart[itemId].quantity = newQty;
            document.getElementById(`qty-${itemId}`).value = newQty;
            renderCart();
        } else {
            removeFromCart(itemId);
        }
    }
}

function removeFromCart(itemId) {
    delete cart[itemId];
    document.getElementById(`qty-${itemId}`).value = 0;
    renderCart();
}

function renderCart() {
    const cartItemsEl = document.getElementById('cartItems');
    const totalItemsEl = document.getElementById('totalItems');
    const submitBtn = document.getElementById('submitOrderBtn');
    
    if (Object.keys(cart).length === 0) {
        cartItemsEl.innerHTML = '<p class="text-gray-500 text-sm text-center py-8">No items added yet</p>';
        totalItemsEl.textContent = '0';
        calculateTotals();
        submitBtn.disabled = true;
        return;
    }
    
    let html = '';
    let totalQty = 0;
    
    for (const [itemId, item] of Object.entries(cart)) {
        const itemTotal = item.price * item.quantity;
        totalQty += item.quantity;
        
        html += `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex-1">
                    <p class="font-medium text-gray-900 text-sm">${item.name}</p>
                    <p class="text-xs text-gray-500">$${item.price.toFixed(2)} × ${item.quantity}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center bg-white rounded-lg border border-gray-300 shadow-sm">
                        <button type="button" onclick="decreaseQuantity(${itemId})" class="px-2 py-1 text-gray-600 hover:text-indigo-600 hover:bg-gray-50 rounded-l-lg transition-colors">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <span class="px-3 py-1 font-semibold text-sm border-x border-gray-300">${item.quantity}</span>
                        <button type="button" onclick="increaseQuantity(${itemId})" class="px-2 py-1 text-gray-600 hover:text-indigo-600 hover:bg-gray-50 rounded-r-lg transition-colors">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <span class="font-bold text-indigo-600 w-16 text-right">$${itemTotal.toFixed(2)}</span>
                    <button type="button" onclick="removeFromCart(${itemId})" class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }
    
    cartItemsEl.innerHTML = html;
    totalItemsEl.textContent = totalQty;
    calculateTotals();
    submitBtn.disabled = false;
}

function calculateTotals() {
    let subtotal = 0;
    
    // Calculate subtotal
    for (const [itemId, item] of Object.entries(cart)) {
        subtotal += item.price * item.quantity;
    }
    
    // Get discount percentage
    const discountPercentage = parseFloat(document.getElementById('discount').value) || 0;
    const discountAmount = (subtotal * discountPercentage) / 100;
    const afterDiscount = subtotal - discountAmount;
    
    // Calculate tax on discounted amount
    const taxRate = {{ config('cashier.tax.default_rate', 15) }};
    const taxAmount = (afterDiscount * taxRate) / 100;
    
    // Grand total
    const grandTotal = afterDiscount + taxAmount;
    
    // Update display
    document.getElementById('subtotalAmount').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('discountAmount').textContent = `-$${discountAmount.toFixed(2)}`;
    document.getElementById('taxAmount').textContent = `$${taxAmount.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `$${grandTotal.toFixed(2)}`;
}

// Admin verification
let adminVerified = false;
const MAX_DISCOUNT_WITHOUT_APPROVAL = {{ config('cashier.discount.max_without_approval', 5) }};

function openAdminModal() {
    document.getElementById('adminModal').classList.remove('hidden');
    document.getElementById('adminEmail').value = '';
    document.getElementById('adminPassword').value = '';
    document.getElementById('adminError').classList.add('hidden');
    document.getElementById('maxDiscountText').textContent = `${MAX_DISCOUNT_WITHOUT_APPROVAL}%`;
}

function closeAdminModal() {
    document.getElementById('adminModal').classList.add('hidden');
    // Reset discount if not verified
    if (!adminVerified) {
        document.getElementById('discount').value = MAX_DISCOUNT_WITHOUT_APPROVAL;
        calculateTotals();
    }
}

async function verifyAdmin() {
    const email = document.getElementById('adminEmail').value;
    const password = document.getElementById('adminPassword').value;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    
    try {
        const response = await fetch('{{ route('orders.verify-discount') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                admin_email: email,
                admin_password: password,
                discount_percentage: discount
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            adminVerified = true;
            closeAdminModal();
        } else {
            document.getElementById('adminError').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('adminError').classList.remove('hidden');
    }
}

// Check discount on input change
document.getElementById('discount').addEventListener('input', function() {
    const discount = parseFloat(this.value) || 0;
    
    if (discount > MAX_DISCOUNT_WITHOUT_APPROVAL) {
        if (!adminVerified) {
            openAdminModal();
        }
    } else {
        adminVerified = false; // Reset verification for lower discounts
    }
});

// Intercept form submission to add proper array structure
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Check if discount requires admin verification
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    if (discount > MAX_DISCOUNT_WITHOUT_APPROVAL && !adminVerified) {
        openAdminModal();
        return false;
    }
    
    // Remove any existing item inputs
    const existingInputs = this.querySelectorAll('input[name^="items["]');
    existingInputs.forEach(input => input.remove());
    
    // Add proper items array structure
    let index = 0;
    for (const [itemId, item] of Object.entries(cart)) {
        // Create hidden input for item_id
        const itemIdInput = document.createElement('input');
        itemIdInput.type = 'hidden';
        itemIdInput.name = `items[${index}][item_id]`;
        itemIdInput.value = itemId;
        this.appendChild(itemIdInput);
        
        // Create hidden input for quantity
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = `items[${index}][quantity]`;
        quantityInput.value = item.quantity;
        this.appendChild(quantityInput);
        
        index++;
    }
    
    // Add tax percentage
    const taxInput = document.createElement('input');
    taxInput.type = 'hidden';
    taxInput.name = 'tax_percentage';
    taxInput.value = {{ config('cashier.tax.default_rate', 15) }};
    this.appendChild(taxInput);
    
    // Now submit the form
    this.submit();
});
</script>

<style>
.category-filter-btn {
    background-color: #f3f4f6;
    color: #6b7280;
    border: 2px solid transparent;
}

.category-filter-btn:hover {
    background-color: #e5e7eb;
    color: #374151;
}

.category-filter-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}
</style>
@endsection
