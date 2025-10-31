@extends('layouts.admin')

@section('title', 'Printer Settings')

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
                <a href="{{ route('settings.index') }}" 
                   class="flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition-colors">
                    <i class="bi bi-arrow-{{ $isRtl ? 'right' : 'left' }} text-xl"></i>
                    <span class="font-medium">Back to Settings</span>
                </a>
                <div class="h-6 w-px bg-gray-300"></div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <i class="bi bi-printer-fill text-indigo-600"></i>
                    Printer Settings
                </h1>
            </div>
        </div>
        <p class="mt-2 text-gray-600">Configure receipt printer settings, auto-print options, and thermal printer connections</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-6 mb-6 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill text-3xl {{ $isRtl ? 'ml-3' : 'mr-3' }}"></i>
                <div>
                    <strong class="font-bold text-lg">Success</strong>
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
                    <strong class="font-bold text-lg">Error</strong>
                    <p class="mt-1">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Printer Status Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <i class="bi bi-info-circle-fill text-indigo-600 text-2xl"></i>
                    Printer Status
                </h2>

                <div class="space-y-4">
                    <div class="p-4 bg-indigo-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-700">Connection</span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold" id="connectionStatus">
                                Ready
                            </span>
                        </div>
                        <p class="text-sm text-gray-600" id="connectionType">Browser Printing</p>
                    </div>

                    <div class="p-4 bg-purple-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-700">Paper Size</span>
                            <span class="text-indigo-600 font-bold" id="paperSize">80mm</span>
                        </div>
                    </div>

                    <div class="p-4 bg-amber-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-700">Auto-Print</span>
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold" id="autoPrintStatus">
                                Disabled
                            </span>
                        </div>
                    </div>

                    <div class="p-4 bg-green-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-700">Last Print</span>
                            <span class="text-sm text-gray-600" id="lastPrint">Never</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <button 
                        onclick="testPrinter()" 
                        class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3">
                        <i class="bi bi-printer"></i>
                        Test Print
                    </button>

                    <button 
                        onclick="resetPrinterSettings()" 
                        class="w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reset to Defaults
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                    <i class="bi bi-lightning-fill text-amber-600 text-2xl"></i>
                    Quick Actions
                </h2>

                <div class="space-y-3">
                    <a href="{{ route('orders.index') }}" 
                       class="block px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors flex items-center gap-3">
                        <i class="bi bi-receipt-cutoff"></i>
                        <span>View Recent Orders</span>
                    </a>

                    <button 
                        onclick="openPrinterManual()" 
                        class="w-full px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors flex items-center gap-3">
                        <i class="bi bi-book"></i>
                        <span>Printer Setup Guide</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Printer Settings Component -->
        <div class="lg:col-span-2">
            <x-printer-settings />
        </div>

    </div>
</div>

<script>
function testPrinter() {
    if (window.printer) {
        window.printer.testPrint()
            .then(() => {
                showNotification('Test print sent successfully!', 'success');
                updateLastPrint();
            })
            .catch(error => {
                showNotification('Test print failed: ' + error.message, 'error');
            });
    } else {
        showNotification('Printer not initialized. Please refresh the page.', 'error');
    }
}

function resetPrinterSettings() {
    if (confirm('Are you sure you want to reset all printer settings to defaults?')) {
        localStorage.removeItem('printerSettings');
        showNotification('Printer settings reset successfully!', 'success');
        setTimeout(() => location.reload(), 1000);
    }
}

function updateLastPrint() {
    const now = new Date().toLocaleTimeString();
    document.getElementById('lastPrint').textContent = now;
}

function showNotification(message, type) {
    const container = document.querySelector('.max-w-7xl');
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 animate-fade-in ${
        type === 'success' ? 'bg-green-50 border-l-4 border-green-500 text-green-700' : 
        'bg-red-50 border-l-4 border-red-500 text-red-700'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'} text-2xl"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

function openPrinterManual() {
    const manual = `
=== RECEIPT PRINTER SETUP GUIDE ===

1. BROWSER PRINTING (Default)
   - Works with any printer installed on your computer
   - No additional setup required
   - Best for PDF receipts

2. THERMAL PRINTER (Network)
   - Enter your printer's IP address
   - Default port: 9100
   - Supported: Epson TM series, Star TSP series
   - Supports ESC/POS commands

3. THERMAL PRINTER (USB)
   - Click "Connect USB Printer"
   - Select your printer from the list
   - Browser must support WebUSB API
   - Supported: Chrome, Edge (not Firefox/Safari)

4. AUTO-PRINT SETTINGS
   - Enable "Auto-print on order completion"
   - Select when to trigger auto-print
   - Configure number of copies

5. RECEIPT CUSTOMIZATION
   - Toggle barcode printing
   - Show/hide customer information
   - Show/hide order notes
   - Configure footer message

6. TROUBLESHOOTING
   - Check printer is powered on
   - Verify network connection
   - Ensure correct IP address
   - Test print to verify settings
   - Check paper is loaded correctly

For more help, contact support.
    `;
    alert(manual);
}

// Update status indicators on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const settings = JSON.parse(localStorage.getItem('printerSettings') || '{}');
        
        // Update status indicators
        if (settings.autoPrint) {
            document.getElementById('autoPrintStatus').textContent = 'Enabled';
            document.getElementById('autoPrintStatus').className = 'px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold';
        }
        
        if (settings.paperSize) {
            document.getElementById('paperSize').textContent = settings.paperSize;
        }
        
        if (settings.connectionType) {
            const types = {
                'browser': 'Browser Printing',
                'network': 'Network Printer',
                'usb': 'USB Printer',
                'bluetooth': 'Bluetooth Printer'
            };
            document.getElementById('connectionType').textContent = types[settings.connectionType] || 'Browser Printing';
        }
    }, 500);
});
</script>
@endsection
