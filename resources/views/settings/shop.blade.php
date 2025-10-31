@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="shopSettings()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            {{ __('messages.shop_settings') }}
        </h1>
        <p class="text-gray-600">
            {{ __('messages.customize_shop_info') }}
        </p>
    </div>

    <!-- رسالة النجاح -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- رسالة الخطأ -->
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <!-- نموذج كلمة المرور -->
    <div x-show="!isUnlocked" class="bg-white rounded-lg shadow-md p-8 max-w-md mx-auto">
        <div class="text-center mb-6">
            <svg class="w-16 h-16 mx-auto text-purple-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('messages.protected_settings') }}</h2>
            <p class="text-gray-600 mt-2">{{ __('messages.enter_password_to_access') }}</p>
        </div>

        <form @submit.prevent="verifyPassword">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">{{ __('messages.password') }}</label>
                <input 
                    type="password" 
                    x-model="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    :disabled="isLoading"
                    required
                >
            </div>

            <div x-show="errorMessage" class="mb-4 text-red-600 text-sm" x-text="errorMessage"></div>

            <button 
                type="submit" 
                class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors disabled:opacity-50"
                :disabled="isLoading"
            >
                <span x-show="!isLoading">{{ __('messages.unlock') }}</span>
                <span x-show="isLoading">{{ __('messages.verifying') }}...</span>
            </button>
        </form>
    </div>

    <!-- نموذج الإعدادات -->
    <div x-show="isUnlocked" x-cloak>
        <form action="{{ route('settings.shop.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8">
            @csrf
            @method('PUT')

            <!-- القسم 1: معلومات المحل -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('messages.shop_information') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- اسم المحل بالعربية -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            {{ __('messages.shop_name_ar') }} <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="shop_name" 
                            value="{{ old('shop_name', $settings->shop_name) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required
                        >
                        @error('shop_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- اسم المحل بالإنجليزية -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            {{ __('messages.shop_name_en') }} <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="shop_name_en" 
                            value="{{ old('shop_name_en', $settings->shop_name_en) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required
                        >
                        @error('shop_name_en')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- رقم الهاتف -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            {{ __('messages.phone') }}
                        </label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $settings->phone) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="+20 XXX XXX XXXX"
                        >
                        @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- العنوان بالعربية -->
                <div class="mt-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        {{ __('messages.address_ar') }}
                    </label>
                    <textarea 
                        name="address" 
                        rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="{{ __('messages.enter_address_ar') }}"
                    >{{ old('address', $settings->address) }}</textarea>
                    @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- العنوان بالإنجليزية -->
                <div class="mt-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        {{ __('messages.address_en') }}
                    </label>
                    <textarea 
                        name="address_en" 
                        rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="{{ __('messages.enter_address_en') }}"
                    >{{ old('address_en', $settings->address_en) }}</textarea>
                    @error('address_en')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- القسم 2: اللوجو -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('messages.shop_logo') }}
                </h3>

                <!-- عرض اللوجو الحالي -->
                @if($settings->logo_path)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">{{ __('messages.current_logo') }}:</p>
                    <div class="flex items-center gap-4">
                        <img src="{{ $settings->logo_url }}" alt="Logo" class="h-24 w-auto border rounded">
                        <button 
                            type="button" 
                            @click="deleteLogo"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors"
                        >
                            {{ __('messages.delete_logo') }}
                        </button>
                    </div>
                </div>
                @endif

                <!-- رفع لوجو جديد -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        {{ $settings->logo_path ? __('messages.change_logo') : __('messages.upload_logo') }}
                    </label>
                    <input 
                        type="file" 
                        name="logo" 
                        accept="image/*"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        @change="previewLogo"
                    >
                    <p class="text-sm text-gray-500 mt-2">
                        {{ __('messages.logo_requirements') }}
                    </p>
                    @error('logo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- معاينة اللوجو -->
                <div x-show="logoPreview" class="mt-4">
                    <p class="text-sm text-gray-600 mb-2">{{ __('messages.preview') }}:</p>
                    <img :src="logoPreview" alt="Preview" class="h-24 w-auto border rounded">
                </div>
            </div>

            <!-- أزرار التحكم -->
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors"
                >
                    {{ __('messages.save_changes') }}
                </button>
                <button 
                    type="button" 
                    @click="window.location.reload()"
                    class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors"
                >
                    {{ __('messages.cancel') }}
                </button>
            </div>
        </form>

        <!-- تنبيه -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h4 class="font-bold text-yellow-800 mb-1">{{ __('messages.important_note') }}</h4>
                    <p class="text-sm text-yellow-700">
                        {{ __('messages.settings_warning') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shopSettings() {
    return {
        isUnlocked: {{ session('shop_settings_unlocked') ? 'true' : 'false' }},
        password: '',
        isLoading: false,
        errorMessage: '',
        logoPreview: null,

        async verifyPassword() {
            this.isLoading = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("settings.shop.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ password: this.password })
                });

                const data = await response.json();

                if (data.success) {
                    this.isUnlocked = true;
                    this.password = '';
                } else {
                    this.errorMessage = data.message;
                }
            } catch (error) {
                this.errorMessage = '{{ __("messages.error_occurred") }}';
            } finally {
                this.isLoading = false;
            }
        },

        previewLogo(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async deleteLogo() {
            if (!confirm('{{ __("messages.confirm_delete_logo") }}')) {
                return;
            }

            try {
                const response = await fetch('{{ route("settings.shop.deleteLogo") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('{{ __("messages.error_occurred") }}');
            }
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
