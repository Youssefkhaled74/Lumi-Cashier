@props(['method', 'selected' => false])

@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';

$icons = [
    'cash' => 'bi-cash-coin',
    'card' => 'bi-credit-card-2-front',
    'credit_card' => 'bi-credit-card',
    'debit_card' => 'bi-credit-card-2-back',
    'mobile_payment' => 'bi-phone',
    'other' => 'bi-wallet2',
];

$colors = [
    'cash' => 'from-green-500 to-emerald-600',
    'card' => 'from-blue-500 to-indigo-600',
    'credit_card' => 'from-purple-500 to-pink-600',
    'debit_card' => 'from-cyan-500 to-blue-600',
    'mobile_payment' => 'from-orange-500 to-red-600',
    'other' => 'from-gray-500 to-slate-600',
];

$icon = $icons[$method] ?? 'bi-wallet2';
$gradient = $colors[$method] ?? 'from-gray-500 to-slate-600';
@endphp

<label class="payment-method-card cursor-pointer">
    <input type="radio" 
           name="payment_method" 
           value="{{ $method }}" 
           class="hidden peer"
           {{ $selected ? 'checked' : '' }}
           required>
    
    <div class="relative bg-white border-3 border-gray-300 rounded-2xl p-6 transition-all duration-300 hover:shadow-xl peer-checked:border-indigo-600 peer-checked:shadow-2xl peer-checked:scale-105 group">
        <!-- Selected Indicator -->
        <div class="absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} opacity-0 peer-checked:opacity-100 transition-opacity">
            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                <i class="bi bi-check text-white text-sm font-bold"></i>
            </div>
        </div>

        <!-- Icon -->
        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br {{ $gradient }} rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
            <i class="{{ $icon }} text-white text-3xl"></i>
        </div>

        <!-- Label -->
        <p class="text-center font-bold text-gray-900 text-lg peer-checked:text-indigo-600">
            @lang("pos.$method")
        </p>

        <!-- Checkmark Animation -->
        <div class="absolute inset-0 bg-indigo-100 opacity-0 peer-checked:opacity-10 rounded-2xl transition-opacity pointer-events-none"></div>
    </div>
</label>
