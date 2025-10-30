@extends('layouts.admin')

@section('title', 'Create Order')

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
                        <i class="bi bi-box-seam text-indigo-600 mr-3 text-2xl"></i> Available Items
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[600px] overflow-y-auto pr-2">
                        @foreach($items as $item)
                        <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-indigo-400 transition-all hover:shadow-md">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item->name }}</h3>
                                    <p class="text-xs text-gray-500">SKU: {{ $item->sku }}</p>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $item->category->name }}
                                    </span>
                                </div>
                                <div class="text-right ml-2">
                                    <p class="text-lg font-bold text-indigo-600">${{ number_format($item->price, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->available_stock }} available</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="number" id="qty-{{ $item->id }}" min="0" max="{{ $item->available_stock }}" value="0" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" onchange="updateCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, this.value, {{ $item->available_stock }})">
                                <button type="button" onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, {{ $item->available_stock }})" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md shadow-md transition-all"><i class="bi bi-plus-circle"></i> Add</button>
                            </div>
                        </div>
                        @endforeach
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

                    <div class="border-t border-gray-200 pt-4 mb-4">
                        <div class="flex justify-between items-center mb-2 text-gray-700">
                            <span>Total Items:</span>
                            <span id="totalItems" class="font-semibold">0</span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-bold">
                            <span class="text-gray-900">Total:</span>
                            <span id="totalAmount" class="text-indigo-600">$0.00</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="bi bi-file-text mr-2"></i> Order Notes (Optional)
                        </label>
                        <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none" placeholder="Add any notes for this order..."></textarea>
                    </div>

                    <button type="submit" id="submitOrderBtn" disabled class="w-full btn-gradient py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                        <i class="bi bi-check-circle"></i>
                        <span>Create Order</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

<script>
let cart = {};

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
    const input = document.getElementById(`qty-${itemId}`);
    const currentQty = parseInt(input.value) || 0;
    const newQty = Math.min(currentQty + 1, maxStock);
    input.value = newQty;
    updateCart(itemId, itemName, itemPrice, newQty, maxStock);
}

function removeFromCart(itemId) {
    delete cart[itemId];
    document.getElementById(`qty-${itemId}`).value = 0;
    renderCart();
}

function renderCart() {
    const cartItemsEl = document.getElementById('cartItems');
    const totalItemsEl = document.getElementById('totalItems');
    const totalAmountEl = document.getElementById('totalAmount');
    const submitBtn = document.getElementById('submitOrderBtn');
    
    if (Object.keys(cart).length === 0) {
        cartItemsEl.innerHTML = '<p class="text-gray-500 text-sm text-center py-8">No items added yet</p>';
        totalItemsEl.textContent = '0';
        totalAmountEl.textContent = '$0.00';
        submitBtn.disabled = true;
        return;
    }
    
    let html = '';
    let totalQty = 0;
    let totalPrice = 0;
    
    for (const [itemId, item] of Object.entries(cart)) {
        const itemTotal = item.price * item.quantity;
        totalQty += item.quantity;
        totalPrice += itemTotal;
        
        html += `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-gray-900 text-sm">${item.name}</p>
                    <p class="text-xs text-gray-500">${item.quantity} × $${item.price.toFixed(2)}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-indigo-600">$${itemTotal.toFixed(2)}</span>
                    <button type="button" onclick="removeFromCart(${itemId})" class="text-red-500 hover:text-red-700 ml-2">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }
    
    cartItemsEl.innerHTML = html;
    totalItemsEl.textContent = totalQty;
    totalAmountEl.textContent = `$${totalPrice.toFixed(2)}`;
    submitBtn.disabled = false;
}

// Intercept form submission to add proper array structure
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
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
    
    // Now submit the form
    this.submit();
});
</script>
@endsection
