<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - {{ config('app.name', 'Lumi') }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .input-group:focus-within {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                <i class="bi bi-shop text-4xl text-purple-600"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">{{ config('app.name', 'Lumi') }}</h1>
            <p class="text-purple-100">Point of Sale & Inventory System</p>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-2xl p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Welcome Back</h2>
                <p class="text-gray-600">Sign in to your admin account</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="bi bi-check-circle-fill mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="bi bi-exclamation-circle-fill mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div class="input-group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="bi bi-envelope mr-1"></i> Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none transition-colors @error('email') border-red-500 @enderror"
                        placeholder="admin@cashier.com"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1 flex items-center">
                            <i class="bi bi-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="input-group">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="bi bi-lock mr-1"></i> Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:outline-none transition-colors @error('password') border-red-500 @enderror"
                            placeholder="Enter your password"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1 flex items-center">
                            <i class="bi bi-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                    >
                    <label for="remember" class="ml-2 text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="btn-login w-full py-3 text-white font-semibold rounded-lg shadow-lg flex items-center justify-center"
                >
                    <i class="bi bi-box-arrow-in-right mr-2"></i>
                    Sign In
                </button>
            </form>

            <!-- Footer Info -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="text-center text-sm text-gray-600">
                    <i class="bi bi-info-circle mr-1"></i>
                    Default credentials: <strong>admin@cashier.com</strong> / <strong>secret123</strong>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="text-center mt-6 text-white text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Lumi') }}. All rights reserved.</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
