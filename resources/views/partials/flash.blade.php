@if(session('success') || session('error') || (isset($errors) && $errors->any()))
    <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-3xl px-4">
        @if(session('success'))
            <div role="status" aria-live="polite" class="alert-animate bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-md">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="bi bi-check-circle-fill text-green-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ __('messages.success') }}</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div role="alert" aria-live="assertive" class="mt-3 alert-animate bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-md">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="bi bi-exclamation-circle-fill text-red-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ __('messages.error') }}</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div role="alert" aria-live="assertive" class="mt-3 alert-animate bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg shadow-md">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="bi bi-exclamation-triangle-fill text-yellow-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ __('messages.warning') }}</p>
                        <ul class="text-sm list-disc ml-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
