@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';
$shopSettings = \App\Models\ShopSettings::current();
@endphp

<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Lumi POS</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E🛒%3C/text%3E%3C/svg%3E">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Alpine.js for Interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    
    <style>
        /* Import Bilingual Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        /* Font Assignment Based on Language */
        [dir="rtl"] * {
            font-family: 'Tajawal', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        [dir="ltr"] * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
            letter-spacing: -0.01em;
        }
        
        /* Loading Spinner */
        .spinner {
            border: 3px solid rgba(99, 102, 241, 0.1);
            border-radius: 50%;
            border-top: 3px solid #6366f1;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Form Loading State */
        .form-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }
        
        .form-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid rgba(99, 102, 241, 0.2);
            border-radius: 50%;
            border-top: 4px solid #6366f1;
            animation: spin 0.8s linear infinite;
            z-index: 1000;
        }
        
        /* Card Animations */
        .stat-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: fadeInUp 0.6s ease-out;
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Stagger animation for cards */
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        
        /* Modal Animations */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: #fff;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideInUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Button Loading */
        .btn-loading {
            position: relative;
            pointer-events: none;
            color: transparent !important;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            animation: spin 0.6s linear infinite;
        }
        
        /* DataTables Customization */
        .dataTables_wrapper {
            padding: 20px;
        }
        
        .dataTables_filter input {
            border: 2px solid #e5e7eb !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            margin-left: 8px !important;
            transition: all 0.3s ease;
        }
        
        .dataTables_filter input:focus {
            border-color: #6366f1 !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        table.dataTable tbody tr:hover {
            background-color: #f9fafb !important;
        }
        
        /* Barcode Scanner Input */
        .barcode-input {
            position: relative;
        }
        
        .barcode-input input {
            padding-left: 48px;
        }
        
        .barcode-input::before {
            content: '\F148';
            font-family: 'bootstrap-icons';
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            font-size: 20px;
            z-index: 10;
        }
        
        .barcode-scanner-active {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        /* Enhanced Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Enhanced Cards */
        .card {
            padding: 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
        
        /* Navigation Enhancement */
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .nav-link:hover::before,
        .nav-link.active::before {
            transform: translateX(0);
        }
        
        /* Success/Error Animations */
        .alert-animate {
            animation: slideInRight 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Table Row Hover */
        tbody tr {
            transition: all 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: #f9fafb;
            transform: scale(1.01);
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                        @if($shopSettings->logo_url)
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl shadow-lg transform group-hover:scale-110 transition-transform duration-300 overflow-hidden bg-white">
                                <img src="{{ $shopSettings->logo_url }}" alt="Logo" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-xl shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                <i class="bi bi-cart-check-fill text-white text-xl"></i>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-xl font-extrabold text-gradient">{{ $shopSettings->shop_name_localized }}</h1>
                            <p class="text-xs text-gray-500 font-medium">{{ __('messages.app_subtitle') }}</p>
                        </div>
                    </a>
                </div>

                <!-- Barcode Scanner Input (Global) -->
                <div class="hidden md:block flex-1 max-w-md mx-8">
                    <div class="barcode-input relative">
                        <input 
                            type="text" 
                            id="globalBarcodeScanner"
                            placeholder="{{ __('messages.scan_barcode') }}"
                            class="w-full px-4 py-2 pl-12 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-300"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Language Switcher -->
                    <div class="flex items-center space-x-2 bg-gray-100 rounded-xl p-1">
                        <a href="{{ route('lang.switch', 'en') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ app()->getLocale() == 'en' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            EN
                        </a>
                        <a href="{{ route('lang.switch', 'ar') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ app()->getLocale() == 'ar' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            عربي
                        </a>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">{{ substr(session('admin_name', 'A'), 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ session('admin_name', 'Admin') }}</p>
                            <p class="text-xs text-gray-500">{{ session('admin_email') }}</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button 
                            type="submit" 
                            class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                        >
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="hidden sm:inline font-medium">{{ __('messages.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-2xl hidden lg:block">
            <div class="h-full overflow-y-auto py-6">
                <nav class="space-y-1 px-3">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <i class="bi bi-speedometer2 text-xl"></i>
                        <span class="font-semibold">{{ __('messages.nav.dashboard') }}</span>
                    </a>
                    
                    <a href="{{ route('categories.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('categories.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <i class="bi bi-folder text-xl"></i>
                        <span class="font-semibold">{{ __('messages.nav.categories') }}</span>
                    </a>
                    
                    <a href="{{ route('items.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('items.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <i class="bi bi-box-seam text-xl"></i>
                        <span class="font-semibold">{{ __('messages.nav.items') }}</span>
                    </a>
                    
                    <a href="{{ route('orders.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('orders.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <i class="bi bi-receipt text-xl"></i>
                        <span class="font-semibold">{{ __('messages.nav.orders') }}</span>
                    </a>
                    
                    <a href="{{ route('pos.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('pos.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <i class="bi bi-cart3 text-xl"></i>
                        <span class="font-semibold">{{ __('messages.nav.pos') }}</span>
                    </a>
                    
                    <div class="pt-6 mt-6 border-t border-gray-200">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('messages.nav.analytics') }}</p>
                        
                        <a href="{{ route('months.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('months.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                            <i class="bi bi-calendar-month text-xl"></i>
                            <span class="font-semibold">{{ __('messages.nav.monthly_overview') }}</span>
                        </a>
                        
                        <a href="{{ route('days.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('days.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                            <i class="bi bi-calendar3 text-xl"></i>
                            <span class="font-semibold">{{ __('messages.nav.days_history') }}</span>
                        </a>
                        
                        <a href="{{ route('reports.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                            <i class="bi bi-bar-chart-line text-xl"></i>
                            <span class="font-semibold">{{ __('messages.nav.reports') }}</span>
                        </a>
                        
                        <a href="{{ route('day.status') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('day.status') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                            <i class="bi bi-calendar-week text-xl"></i>
                            <span class="font-semibold">{{ __('messages.nav.daily_sessions') }}</span>
                        </a>
                    </div>

                    <!-- Settings Section -->
                    <div class="pt-6 mt-6 border-t border-gray-200">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('messages.nav.settings') }}</p>
                        
                        <a href="{{ route('settings.shop.index') }}" class="nav-link flex items-center space-x-3 px-4 py-3 text-gray-700 {{ request()->routeIs('settings.shop.*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700' : '' }} rounded-xl hover:bg-gray-50 transition-all duration-200">
                            <i class="bi bi-shop text-xl"></i>
                            <span class="font-semibold">{{ __('messages.nav.shop_settings') }}</span>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Item Quick View Modal -->
    <div id="itemQuickViewModal" class="modal">
        <div class="modal-content">
            <div id="modalContentLoader" class="p-8 text-center">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-500">{{ __('messages.loading') }}</p>
            </div>
            <div id="modalContentBody" style="display: none;">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    
    <!-- Printer Support -->
    <script>
        // Printer configuration from backend
        window.printerConfig = @json(config('printer'));
    </script>
    <script src="{{ asset('js/printer.js') }}"></script>
    
    <script>
        // Global Barcode Scanner
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeInput = document.getElementById('globalBarcodeScanner');
            let barcodeBuffer = '';
            let lastKeyTime = Date.now();
            
            if (barcodeInput) {
                // Listen for rapid keystrokes (barcode scanner behavior)
                document.addEventListener('keypress', function(e) {
                    const currentTime = Date.now();
                    
                    // If time between keystrokes is < 50ms, it's likely a scanner
                    if (currentTime - lastKeyTime < 50) {
                        e.preventDefault();
                        barcodeBuffer += e.key;
                        barcodeInput.value = barcodeBuffer;
                        barcodeInput.classList.add('barcode-scanner-active');
                    } else {
                        barcodeBuffer = e.key;
                    }
                    
                    lastKeyTime = currentTime;
                    
                    // Auto-search after Enter or after 100ms of no input
                    if (e.key === 'Enter' && barcodeBuffer.length > 3) {
                        e.preventDefault();
                        searchByBarcode(barcodeBuffer);
                        barcodeBuffer = '';
                        barcodeInput.classList.remove('barcode-scanner-active');
                    }
                });
                
                // Manual search on input
                barcodeInput.addEventListener('change', function() {
                    if (this.value.length > 0) {
                        searchByBarcode(this.value);
                    }
                });
            }
            
            function searchByBarcode(barcode) {
                console.log('Searching for barcode:', barcode);
                // You can implement AJAX search here
                // For now, we'll show an alert
                const cleanBarcode = barcode.trim();
                if (cleanBarcode) {
                    // Try to find item in current page or redirect to items page with search
                    window.location.href = '/admin/items?search=' + encodeURIComponent(cleanBarcode);
                }
            }
            
            // Form Submit Loading States
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.classList.contains('btn-loading')) {
                        submitBtn.classList.add('btn-loading');
                        this.classList.add('form-loading');
                    }
                });
            });
            
            // Initialize DataTables for all tables with class 'data-table'
            if ($.fn.DataTable) {
                $('.data-table').each(function() {
                    // Destroy existing instance if any
                    if ($.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable().destroy();
                    }
                    
                    // Initialize with error handling
                    try {
                        $(this).DataTable({
                            responsive: true,
                            pageLength: 25,
                            autoWidth: false,
                            language: {
                                search: "Search:",
                                lengthMenu: "Show _MENU_ entries",
                                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                                infoEmpty: "Showing 0 to 0 of 0 entries",
                                infoFiltered: "(filtered from _MAX_ total entries)",
                                zeroRecords: "No matching records found",
                                emptyTable: "No data available in table",
                                paginate: {
                                    first: "First",
                                    last: "Last",
                                    next: "Next",
                                    previous: "Previous"
                                }
                            },
                            columnDefs: [
                                { targets: '_all', orderable: true }
                            ]
                        });
                    } catch (error) {
                        console.error('DataTable initialization error:', error);
                        console.log('Table:', this);
                    }
                });
            }
            
            // Modal Functions
            window.openModal = function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            };
            
            window.closeModal = function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            };
            
            // Close modal on outside click
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal(this.id);
                    }
                });
            });
            
            // Item Quick View
            window.quickViewItem = function(itemId) {
                openModal('itemQuickViewModal');
                document.getElementById('modalContentLoader').style.display = 'block';
                document.getElementById('modalContentBody').style.display = 'none';
                
                // Simulate AJAX load (replace with actual AJAX call)
                setTimeout(() => {
                    document.getElementById('modalContentLoader').style.display = 'none';
                    document.getElementById('modalContentBody').style.display = 'block';
                    document.getElementById('modalContentBody').innerHTML = `
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-2xl font-bold text-gray-800">Item #${itemId}</h2>
                                <button onclick="closeModal('itemQuickViewModal')" class="text-gray-400 hover:text-gray-600">
                                    <i class="bi bi-x-lg text-2xl"></i>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <p class="text-gray-600">Loading item details...</p>
                                <a href="/admin/items/${itemId}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    View Full Details
                                </a>
                            </div>
                        </div>
                    `;
                }, 500);
            };
        });
    </script>
    
    <!-- PDF Desktop Handler for PHP Desktop -->
    <script src="{{ asset('js/pdf-desktop-handler.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
