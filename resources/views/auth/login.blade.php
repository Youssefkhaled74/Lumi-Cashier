<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Lumi Cashier</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background Shapes */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s infinite ease-in-out;
        }
        
        .shape-1 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            top: -200px;
            left: -200px;
            animation-delay: 0s;
        }
        
        .shape-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #ec4899, #f97316);
            bottom: -150px;
            right: -150px;
            animation-delay: 5s;
        }
        
        .shape-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            top: 50%;
            right: 10%;
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(50px, -50px) scale(1.1); }
            66% { transform: translate(-50px, 50px) scale(0.9); }
        }
        
        /* Login Card Styles */
        .login-container {
            position: relative;
            z-index: 10;
        }
        
        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Logo Animation */
        .logo-container {
            animation: logoFloat 3s infinite ease-in-out;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .logo-glow {
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.5),
                        0 0 80px rgba(139, 92, 246, 0.3);
        }
        
        /* Input Animations */
        .input-group input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
        }
        
        .input-wrapper {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .input-wrapper:focus-within .input-icon {
            color: #3b82f6;
            transform: scale(1.1);
        }
        
        .input-icon {
            transition: all 0.3s ease;
        }
        
        /* Button Styles */
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        /* Alert Animations */
        .alert {
            animation: slideInDown 0.5s ease;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -100%, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        /* Checkbox Custom */
        .checkbox-wrapper input:checked + label::before {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }
        
        /* Password Toggle Button */
        .toggle-password {
            transition: all 0.3s ease;
        }
        
        .toggle-password:hover {
            transform: scale(1.1);
            color: #3b82f6;
        }
        
        /* Glassmorphism Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Shine Effect */
        @keyframes shine {
            0% { background-position: -200%; }
            100% { background-position: 200%; }
        }
        
        .shine {
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.4) 50%, 
                transparent 100%);
            background-size: 200% 100%;
            animation: shine 3s infinite;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 md:p-8">
    <!-- Animated Background Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>
    
    <div class="login-container w-full max-w-md">
        <!-- Logo/Brand -->
        <div class="text-center mb-8">
            <div class="logo-container inline-flex items-center justify-center w-16 h-16 mb-4">
                <div class="relative">
                    <div class="logo-glow absolute inset-0 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 animate-pulse"></div>
                    <div class="relative bg-white rounded-xl p-3 shadow-2xl">
                        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="lumiGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#8b5cf6;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#ec4899;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="url(#lumiGradient)"/>
                            <path d="M2 17L12 22L22 17V7L12 12L2 7V17Z" fill="url(#lumiGradient)" opacity="0.7"/>
                            <circle cx="12" cy="12" r="2" fill="white"/>
                        </svg>
                    </div>
                </div>
            </div>
            <h1 class="text-4xl font-extrabold mb-2">
                <span class="gradient-text">Lumi</span>
            </h1>
            <p class="text-white text-sm font-light tracking-wide glass-effect px-5 py-1.5 rounded-full inline-block">
                Smart Cashier System
            </p>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-2xl p-6 md:p-8">
            <!-- Header -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Welcome Back</h2>
                <p class="text-gray-500 text-sm font-medium">Sign in to access your dashboard</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-xl mr-3"></i>
                        </div>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-circle-fill text-xl mr-3"></i>
                        </div>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div class="input-group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="input-wrapper">
                        <div class="relative">
                            <div class="input-icon absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300 @error('email') border-red-500 @enderror"
                                placeholder="admin@lumi.com"
                                required
                                autofocus
                            >
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center font-medium">
                                <i class="bi bi-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Password Field -->
                <div class="input-group">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="input-wrapper">
                        <div class="relative">
                            <div class="input-icon absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="bi bi-lock-fill"></i>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="w-full pl-10 pr-11 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300 @error('password') border-red-500 @enderror"
                                placeholder="••••••••"
                                required
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600"
                            >
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center font-medium">
                                <i class="bi bi-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                        >
                        <label for="remember" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer">
                            Keep me signed in
                        </label>
                    </div>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="btn-login w-full py-3.5 text-white font-bold rounded-xl shadow-xl flex items-center justify-center space-x-2"
                >
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right-circle-fill"></i>
                </button>
            </form>

            <!-- Footer Info -->
            <div class="mt-6 pt-5 border-t-2 border-gray-100">
                <div class="text-center">
                    <div class="glass-effect px-3 py-2 rounded-lg inline-block">
                        <p class="text-xs text-gray-600 font-medium mb-0.5">
                            <i class="bi bi-shield-check text-blue-600"></i> Default Credentials
                        </p>
                        <p class="text-xs">
                            <span class="font-bold text-gray-800">admin@cashier.com</span>
                            <span class="text-gray-400 mx-1.5">•</span>
                            <span class="font-bold text-gray-800">secret123</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="text-center mt-6">
            <div class="glass-effect px-5 py-2 rounded-full inline-block">
                <p class="text-white text-xs font-medium">
                    <i class="bi bi-c-circle mr-1"></i>
                    {{ date('Y') }} <span class="gradient-text font-bold">Lumi</span> Cashier
                    <span class="mx-1.5">•</span>
                    <span class="text-blue-200">All rights reserved</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash-fill';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye-fill';
            }
        }
        
        // Add loading state to form
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm mr-2"></div> Signing in...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
