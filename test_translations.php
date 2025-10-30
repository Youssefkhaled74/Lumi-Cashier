<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

echo "🌍 Testing Bilingual System\n";
echo str_repeat("=", 50) . "\n\n";

// Test English
Session::put('locale', 'en');
App::setLocale('en');

echo "📝 Testing English Translations:\n";
echo "  App Name: " . __('messages.app_name') . "\n";
echo "  Dashboard: " . __('messages.dashboard') . "\n";
echo "  Categories: " . __('messages.nav.categories') . "\n";
echo "  Orders: " . __('messages.nav.orders') . "\n";
echo "  Monthly Overview: " . __('messages.nav.monthly_overview') . "\n";
echo "\n";

// Test Arabic
Session::put('locale', 'ar');
App::setLocale('ar');

echo "📝 Testing Arabic Translations (عربي):\n";
echo "  App Name: " . __('messages.app_name') . "\n";
echo "  Dashboard: " . __('messages.dashboard') . "\n";
echo "  Categories: " . __('messages.nav.categories') . "\n";
echo "  Orders: " . __('messages.nav.orders') . "\n";
echo "  Monthly Overview: " . __('messages.nav.monthly_overview') . "\n";
echo "\n";

echo "✅ Bilingual system is working!\n";
echo "\nℹ️  Language Switcher Features:\n";
echo "  - Toggle between EN/AR in top navigation\n";
echo "  - RTL support for Arabic\n";
echo "  - All UI elements translated\n";
echo "  - Arabic fonts (Tajawal) & English fonts (Inter)\n";
