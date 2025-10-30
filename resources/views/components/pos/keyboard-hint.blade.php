@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';

$shortcuts = [
    ['key' => 'F2', 'action' => 'search_shortcut', 'icon' => 'bi-search'],
    ['key' => 'F4', 'action' => 'checkout_shortcut', 'icon' => 'bi-cash-coin'],
    ['key' => 'Ctrl+H', 'action' => 'hold_shortcut', 'icon' => 'bi-pause-circle'],
    ['key' => 'Ctrl+K', 'action' => 'clear_shortcut', 'icon' => 'bi-x-circle'],
    ['key' => 'Ctrl+N', 'action' => 'new_order_shortcut', 'icon' => 'bi-plus-circle'],
];
@endphp

<div class="keyboard-hints bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4 border-2 border-indigo-200"
     x-data="{ show: true }">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <i class="bi bi-keyboard text-indigo-600 text-xl"></i>
            <h3 class="font-bold text-gray-900">@lang('pos.shortcuts')</h3>
        </div>
        <button @click="show = !show" class="text-gray-500 hover:text-gray-700">
            <i class="bi" :class="show ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
        </button>
    </div>

    <!-- Shortcuts List -->
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
        @foreach($shortcuts as $shortcut)
        <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 shadow-sm">
            <i class="{{ $shortcut['icon'] }} text-indigo-600"></i>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-mono font-semibold text-gray-900">{{ $shortcut['key'] }}</p>
                <p class="text-xs text-gray-600 truncate">@lang("pos.{$shortcut['action']}")</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
