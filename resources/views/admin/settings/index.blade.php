@extends('layouts.admin')

@section('title', __('pos.settings'))

@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition-colors">
                    <i class="bi bi-arrow-{{ $isRtl ? 'right' : 'left' }} text-xl"></i>
                    <span class="font-medium">@lang('pos.back')</span>
                </a>
                <div class="h-6 w-px bg-gray-300"></div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <i class="bi bi-gear-fill text-indigo-600"></i>
                    @lang('pos.settings')
                </h1>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-6 mb-6 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill text-3xl {{ $isRtl ? 'ml-3' : 'mr-3' }}"></i>
                <div>
                    <strong class="font-bold text-lg">@lang('pos.success')</strong>
                    <p class="mt-1">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 mb-6 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle-fill text-3xl {{ $isRtl ? 'ml-3' : 'mr-3' }}"></i>
                <div>
                    <strong class="font-bold text-lg">@lang('pos.error')</strong>
                    <p class="mt-1">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Quick Links Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-indigo-200 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <i class="bi bi-link-45deg text-indigo-600 text-2xl"></i>
                    Quick Links
                </h2>

                <div class="space-y-3">
                    <a href="{{ route('settings.printer') }}" 
                       class="flex items-center justify-between p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-all group">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-printer-fill text-indigo-600 text-2xl"></i>
                            <div>
                                <div class="font-semibold text-gray-900">Printer Settings</div>
                                <div class="text-xs text-gray-600">Configure receipt printing</div>
                            </div>
                        </div>
                        <i class="bi bi-arrow-{{ $isRtl ? 'left' : 'right' }} text-indigo-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-all group">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-speedometer2 text-purple-600 text-2xl"></i>
                            <div>
                                <div class="font-semibold text-gray-900">Dashboard</div>
                                <div class="text-xs text-gray-600">View analytics</div>
                            </div>
                        </div>
                        <i class="bi bi-arrow-{{ $isRtl ? 'left' : 'right' }} text-purple-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('orders.index') }}" 
                       class="flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-all group">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-receipt-cutoff text-green-600 text-2xl"></i>
                            <div>
                                <div class="font-semibold text-gray-900">Recent Orders</div>
                                <div class="text-xs text-gray-600">View order history</div>
                            </div>
                        </div>
                        <i class="bi bi-arrow-{{ $isRtl ? 'left' : 'right' }} text-green-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            
            <!-- Statistics Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <i class="bi bi-bar-chart-fill text-indigo-600 text-2xl"></i>
                    @lang('pos.system_statistics')
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-receipt text-indigo-600 text-2xl"></i>
                            <span class="font-semibold text-gray-700">@lang('pos.total_orders')</span>
                        </div>
                        <span class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_orders']) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-box-seam text-purple-600 text-2xl"></i>
                            <span class="font-semibold text-gray-700">@lang('pos.order_items')</span>
                        </div>
                        <span class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_order_items']) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-amber-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-calendar-check text-amber-600 text-2xl"></i>
                            <span class="font-semibold text-gray-700">@lang('pos.business_days')</span>
                        </div>
                        <span class="text-2xl font-bold text-amber-600">{{ number_format($stats['total_days']) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-box text-green-600 text-2xl"></i>
                            <span class="font-semibold text-gray-700">@lang('pos.total_items')</span>
                        </div>
                        <span class="text-2xl font-bold text-green-600">{{ number_format($stats['total_items']) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-cyan-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-grid text-cyan-600 text-2xl"></i>
                            <span class="font-semibold text-gray-700">@lang('pos.total_categories')</span>
                        </div>
                        <span class="text-2xl font-bold text-cyan-600">{{ number_format($stats['total_categories']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-red-200">
                <h2 class="text-xl font-bold text-red-700 mb-2 flex items-center gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 text-2xl"></i>
                    @lang('pos.danger_zone')
                </h2>
                <p class="text-gray-600 mb-6">@lang('pos.danger_zone_warning')</p>

                <!-- Reset Options -->
                <div class="space-y-4">
                    
                    <!-- Reset All Data -->
                    <div class="border-2 border-red-300 rounded-xl p-6 bg-red-50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-red-900 mb-2 flex items-center gap-2">
                                    <i class="bi bi-trash3-fill"></i>
                                    @lang('pos.reset_all_data')
                                </h3>
                                <p class="text-sm text-red-700 mb-2">
                                    @lang('pos.reset_all_data_description')
                                </p>
                                <ul class="text-xs text-red-600 space-y-1 {{ $isRtl ? 'mr-4' : 'ml-4' }}">
                                    <li class="flex items-center gap-2">
                                        <i class="bi bi-x-circle-fill"></i>
                                        @lang('pos.will_delete_orders')
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="bi bi-x-circle-fill"></i>
                                        @lang('pos.will_delete_order_items')
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="bi bi-x-circle-fill"></i>
                                        @lang('pos.will_delete_days')
                                    </li>
                                    <li class="flex items-center gap-2 text-green-600">
                                        <i class="bi bi-check-circle-fill"></i>
                                        @lang('pos.will_keep_items_categories')
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('settings.reset-data') }}" 
                              x-data="{ confirmed: false, showCredentials: false }">
                            @csrf
                            @method('DELETE')
                            
                            <div class="mb-4 space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="confirmed"
                                           @change="if(!$event.target.checked) showCredentials = false"
                                           class="w-5 h-5 text-red-600 border-red-300 rounded focus:ring-red-500">
                                    <span class="text-sm font-semibold text-red-900">
                                        @lang('pos.i_understand_this_action')
                                    </span>
                                </label>

                                <!-- Admin Credentials (shown when confirmed) -->
                                <div x-show="confirmed" 
                                     x-transition
                                     class="space-y-3 p-4 bg-white rounded-lg border-2 border-red-200">
                                    <p class="text-sm font-bold text-red-900 mb-3 flex items-center gap-2">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        @lang('pos.admin_verification_required')
                                    </p>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-red-900 mb-1">
                                            @lang('pos.admin_email')
                                        </label>
                                        <input type="email" 
                                               name="admin_email" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                               placeholder="admin@example.com">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-red-900 mb-1">
                                            @lang('pos.admin_password')
                                        </label>
                                        <input type="password" 
                                               name="admin_password" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                               placeholder="••••••••">
                                    </div>

                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="showCredentials" 
                                               class="w-5 h-5 text-red-600 border-red-300 rounded focus:ring-red-500">
                                        <span class="text-sm font-semibold text-red-900">
                                            @lang('pos.credentials_verified')
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" 
                                    :disabled="!confirmed || !showCredentials"
                                    :class="(confirmed && showCredentials) ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                                    class="w-full px-6 py-4 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl disabled:opacity-50 flex items-center justify-center gap-3">
                                <i class="bi bi-trash3-fill text-xl"></i>
                                @lang('pos.reset_all_data_now')
                            </button>
                        </form>
                    </div>

                    <!-- Reset Orders Only -->
                    <div class="border-2 border-amber-300 rounded-xl p-6 bg-amber-50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-amber-900 mb-2 flex items-center gap-2">
                                    <i class="bi bi-receipt"></i>
                                    @lang('pos.reset_orders_only')
                                </h3>
                                <p class="text-sm text-amber-700">
                                    @lang('pos.reset_orders_description')
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('settings.reset-specific') }}" 
                              x-data="{ confirmedOrders: false, showCredentialsOrders: false }">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="orders">
                            
                            <div class="mb-4 space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="confirmedOrders"
                                           @change="if(!$event.target.checked) showCredentialsOrders = false"
                                           class="w-5 h-5 text-amber-600 border-amber-300 rounded focus:ring-amber-500">
                                    <span class="text-sm font-semibold text-amber-900">
                                        @lang('pos.confirm_reset_orders')
                                    </span>
                                </label>

                                <!-- Admin Credentials -->
                                <div x-show="confirmedOrders" 
                                     x-transition
                                     class="space-y-3 p-4 bg-white rounded-lg border-2 border-amber-200">
                                    <p class="text-sm font-bold text-amber-900 mb-3 flex items-center gap-2">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        @lang('pos.admin_verification_required')
                                    </p>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-amber-900 mb-1">
                                            @lang('pos.admin_email')
                                        </label>
                                        <input type="email" 
                                               name="admin_email" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                               placeholder="admin@example.com">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-amber-900 mb-1">
                                            @lang('pos.admin_password')
                                        </label>
                                        <input type="password" 
                                               name="admin_password" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                               placeholder="••••••••">
                                    </div>

                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="showCredentialsOrders" 
                                               class="w-5 h-5 text-amber-600 border-amber-300 rounded focus:ring-amber-500">
                                        <span class="text-sm font-semibold text-amber-900">
                                            @lang('pos.credentials_verified')
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    :disabled="!confirmedOrders || !showCredentialsOrders"
                                    :class="(confirmedOrders && showCredentialsOrders) ? 'bg-amber-600 hover:bg-amber-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                                    class="w-full px-6 py-3 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg disabled:opacity-50 flex items-center justify-center gap-3">
                                <i class="bi bi-receipt"></i>
                                @lang('pos.reset_orders')
                            </button>
                        </form>
                    </div>

                    <!-- Reset Days Only -->
                    <div class="border-2 border-blue-300 rounded-xl p-6 bg-blue-50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-blue-900 mb-2 flex items-center gap-2">
                                    <i class="bi bi-calendar-check"></i>
                                    @lang('pos.reset_days_only')
                                </h3>
                                <p class="text-sm text-blue-700">
                                    @lang('pos.reset_days_description')
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('settings.reset-specific') }}" 
                              x-data="{ confirmedDays: false, showCredentialsDays: false }">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="days">
                            
                            <div class="mb-4 space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="confirmedDays"
                                           @change="if(!$event.target.checked) showCredentialsDays = false"
                                           class="w-5 h-5 text-blue-600 border-blue-300 rounded focus:ring-blue-500">
                                    <span class="text-sm font-semibold text-blue-900">
                                        @lang('pos.confirm_reset_days')
                                    </span>
                                </label>

                                <!-- Admin Credentials -->
                                <div x-show="confirmedDays" 
                                     x-transition
                                     class="space-y-3 p-4 bg-white rounded-lg border-2 border-blue-200">
                                    <p class="text-sm font-bold text-blue-900 mb-3 flex items-center gap-2">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        @lang('pos.admin_verification_required')
                                    </p>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-blue-900 mb-1">
                                            @lang('pos.admin_email')
                                        </label>
                                        <input type="email" 
                                               name="admin_email" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="admin@example.com">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-blue-900 mb-1">
                                            @lang('pos.admin_password')
                                        </label>
                                        <input type="password" 
                                               name="admin_password" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="••••••••">
                                    </div>

                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="showCredentialsDays" 
                                               class="w-5 h-5 text-blue-600 border-blue-300 rounded focus:ring-blue-500">
                                        <span class="text-sm font-semibold text-blue-900">
                                            @lang('pos.credentials_verified')
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    :disabled="!confirmedDays || !showCredentialsDays"
                                    :class="(confirmedDays && showCredentialsDays) ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                                    class="w-full px-6 py-3 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg disabled:opacity-50 flex items-center justify-center gap-3">
                                <i class="bi bi-calendar-check"></i>
                                @lang('pos.reset_days')
                            </button>
                        </form>
                    </div>

                    <!-- Reset Items Only -->
                    <div class="border-2 border-green-300 rounded-xl p-6 bg-green-50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-green-900 mb-2 flex items-center gap-2">
                                    <i class="bi bi-box"></i>
                                    @lang('pos.reset_items_only')
                                </h3>
                                <p class="text-sm text-green-700 mb-2">
                                    @lang('pos.reset_items_description')
                                </p>
                                <div class="text-xs text-red-600 font-semibold mt-2 p-2 bg-red-50 rounded border border-red-200">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    @lang('pos.warning_items_in_orders')
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('settings.reset-specific') }}" 
                              x-data="{ confirmedItems: false, showCredentialsItems: false }">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="items">
                            
                            <div class="mb-4 space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="confirmedItems"
                                           @change="if(!$event.target.checked) showCredentialsItems = false"
                                           class="w-5 h-5 text-green-600 border-green-300 rounded focus:ring-green-500">
                                    <span class="text-sm font-semibold text-green-900">
                                        @lang('pos.confirm_reset_items')
                                    </span>
                                </label>

                                <!-- Admin Credentials -->
                                <div x-show="confirmedItems" 
                                     x-transition
                                     class="space-y-3 p-4 bg-white rounded-lg border-2 border-green-200">
                                    <p class="text-sm font-bold text-green-900 mb-3 flex items-center gap-2">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        @lang('pos.admin_verification_required')
                                    </p>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-green-900 mb-1">
                                            @lang('pos.admin_email')
                                        </label>
                                        <input type="email" 
                                               name="admin_email" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                               placeholder="admin@example.com">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-green-900 mb-1">
                                            @lang('pos.admin_password')
                                        </label>
                                        <input type="password" 
                                               name="admin_password" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                               placeholder="••••••••">
                                    </div>

                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="showCredentialsItems" 
                                               class="w-5 h-5 text-green-600 border-green-300 rounded focus:ring-green-500">
                                        <span class="text-sm font-semibold text-green-900">
                                            @lang('pos.credentials_verified')
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    :disabled="!confirmedItems || !showCredentialsItems"
                                    :class="(confirmedItems && showCredentialsItems) ? 'bg-green-600 hover:bg-green-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                                    class="w-full px-6 py-3 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg disabled:opacity-50 flex items-center justify-center gap-3">
                                <i class="bi bi-box"></i>
                                @lang('pos.reset_items')
                            </button>
                        </form>
                    </div>

                    <!-- Reset Categories Only -->
                    <div class="border-2 border-cyan-300 rounded-xl p-6 bg-cyan-50">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-cyan-900 mb-2 flex items-center gap-2">
                                    <i class="bi bi-grid"></i>
                                    @lang('pos.reset_categories_only')
                                </h3>
                                <p class="text-sm text-cyan-700 mb-2">
                                    @lang('pos.reset_categories_description')
                                </p>
                                <div class="text-xs text-red-600 font-semibold mt-2 p-2 bg-red-50 rounded border border-red-200">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    @lang('pos.warning_categories_with_items')
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('settings.reset-specific') }}" 
                              x-data="{ confirmedCategories: false, showCredentialsCategories: false }">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="categories">
                            
                            <div class="mb-4 space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="confirmedCategories"
                                           @change="if(!$event.target.checked) showCredentialsCategories = false"
                                           class="w-5 h-5 text-cyan-600 border-cyan-300 rounded focus:ring-cyan-500">
                                    <span class="text-sm font-semibold text-cyan-900">
                                        @lang('pos.confirm_reset_categories')
                                    </span>
                                </label>

                                <!-- Admin Credentials -->
                                <div x-show="confirmedCategories" 
                                     x-transition
                                     class="space-y-3 p-4 bg-white rounded-lg border-2 border-cyan-200">
                                    <p class="text-sm font-bold text-cyan-900 mb-3 flex items-center gap-2">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        @lang('pos.admin_verification_required')
                                    </p>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-cyan-900 mb-1">
                                            @lang('pos.admin_email')
                                        </label>
                                        <input type="email" 
                                               name="admin_email" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-cyan-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                               placeholder="admin@example.com">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-cyan-900 mb-1">
                                            @lang('pos.admin_password')
                                        </label>
                                        <input type="password" 
                                               name="admin_password" 
                                               required
                                               class="w-full px-4 py-2 border-2 border-cyan-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                               placeholder="••••••••">
                                    </div>

                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="showCredentialsCategories" 
                                               class="w-5 h-5 text-cyan-600 border-cyan-300 rounded focus:ring-cyan-500">
                                        <span class="text-sm font-semibold text-cyan-900">
                                            @lang('pos.credentials_verified')
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    :disabled="!confirmedCategories || !showCredentialsCategories"
                                    :class="(confirmedCategories && showCredentialsCategories) ? 'bg-cyan-600 hover:bg-cyan-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                                    class="w-full px-6 py-3 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg disabled:opacity-50 flex items-center justify-center gap-3">
                                <i class="bi bi-grid"></i>
                                @lang('pos.reset_categories')
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Warning Footer -->
                <div class="mt-6 p-4 bg-yellow-50 border-2 border-yellow-300 rounded-xl">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-yellow-600 text-xl flex-shrink-0 mt-1"></i>
                        <div class="flex-1">
                            <p class="font-bold text-yellow-900 mb-1">@lang('pos.warning')</p>
                            <p class="text-sm text-yellow-800">@lang('pos.reset_warning_message')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
