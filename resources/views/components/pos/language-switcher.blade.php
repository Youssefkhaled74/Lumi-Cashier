@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';
@endphp

<div class="language-switcher">
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" 
                type="button"
                class="flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 rounded-xl hover:border-indigo-500 transition-all shadow-sm hover:shadow-md">
            <i class="bi bi-globe text-indigo-600 text-lg"></i>
            <span class="font-semibold text-gray-700">
                {{ $lang === 'ar' ? 'عربي' : 'English' }}
            </span>
            <i class="bi bi-chevron-down text-gray-500 text-sm" :class="{ 'rotate-180': open }"></i>
        </button>

        <!-- Dropdown -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="absolute {{ $isRtl ? 'left-0' : 'right-0' }} mt-2 w-48 bg-white rounded-xl shadow-2xl border-2 border-gray-200 overflow-hidden z-50">
            
            <!-- English Option -->
            <a href="{{ url('/lang/en') }}" 
               class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 transition-colors {{ $lang === 'en' ? 'bg-indigo-100' : '' }}">
                <span class="text-2xl">🇬🇧</span>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">English</p>
                    <p class="text-xs text-gray-500">English Language</p>
                </div>
                @if($lang === 'en')
                <i class="bi bi-check-circle-fill text-indigo-600"></i>
                @endif
            </a>

            <!-- Arabic Option -->
            <a href="{{ url('/lang/ar') }}" 
               class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 transition-colors {{ $lang === 'ar' ? 'bg-indigo-100' : '' }}">
                <span class="text-2xl">🇸🇦</span>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">العربية</p>
                    <p class="text-xs text-gray-500">اللغة العربية</p>
                </div>
                @if($lang === 'ar')
                <i class="bi bi-check-circle-fill text-indigo-600"></i>
                @endif
            </a>
        </div>
    </div>
</div>
