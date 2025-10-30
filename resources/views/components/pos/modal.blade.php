@props(['title', 'show' => false, 'size' => 'md'])

@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';

$sizes = [
    'sm' => 'max-w-md',
    'md' => 'max-w-2xl',
    'lg' => 'max-w-4xl',
    'xl' => 'max-w-6xl',
    'full' => 'max-w-7xl',
];

$maxWidth = $sizes[$size] ?? 'max-w-2xl';
@endphp

<div x-data="{ open: {{ $show ? 'true' : 'false' }} }"
     x-show="open"
     @open-modal.window="if($event.detail.name === '{{ $title }}') open = true"
     @close-modal.window="open = false"
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             @click.stop
             class="relative bg-white rounded-2xl shadow-2xl {{ $maxWidth }} w-full overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        {{ $title }}
                    </h2>
                    <button @click="open = false" 
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                {{ $slot }}
            </div>

            <!-- Modal Footer (if provided) -->
            @isset($footer)
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $footer }}
            </div>
            @endisset
        </div>
    </div>
</div>
