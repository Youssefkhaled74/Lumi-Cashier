# 02 - Setup Guide

## 🚀 Getting Started

This guide will help you set up the Lumi POS system for development on your local machine.

---

## 📋 Prerequisites

### Required Software

#### 1. PHP 8.2 or Higher
```bash
# Check PHP version
php -v

# Required extensions (check with php -m):
- PDO
- pdo_sqlite
- mbstring
- openssl
- tokenizer
- xml
- ctype
- json
- bcmath
- fileinfo
```

#### 2. Composer (PHP Dependency Manager)
```bash
# Download from: https://getcomposer.org/download/
# Verify installation:
composer --version
```

#### 3. Node.js & NPM (Frontend Asset Compilation)
```bash
# Download from: https://nodejs.org/ (LTS version)
# Verify installation:
node -v
npm -v
```

#### 4. Git (Version Control)
```bash
# Download from: https://git-scm.com/
# Verify installation:
git --version
```

---

## 📥 Installation Steps

### Step 1: Clone the Repository
```bash
# Navigate to your projects folder
cd C:\Projects

# Clone the repository
git clone <repository-url> lumi-pos
cd lumi-pos
```

### Step 2: Navigate to Laravel Directory
```bash
# The Laravel app is in the 'www' subfolder
cd www
```

### Step 3: Install PHP Dependencies
```bash
# Install all Composer packages
composer install

# This will install:
# - Laravel Framework 12
# - DomPDF for invoice generation
# - Barcode generator
# - Development tools (PHPUnit, Pint, etc.)
```

### Step 4: Environment Configuration
```bash
# Copy the example environment file
copy .env.example .env

# Or on Mac/Linux:
cp .env.example .env
```

### Step 5: Generate Application Key
```bash
# Generate unique encryption key
php artisan key:generate

# This updates APP_KEY in .env file
```

### Step 6: Database Setup
```bash
# Create SQLite database file
type nul > database\database.sqlite

# Or on Mac/Linux:
touch database/database.sqlite

# Run migrations (create tables)
php artisan migrate

# Seed database with sample data (optional but recommended)
php artisan db:seed
```

### Step 7: Install Frontend Dependencies
```bash
# Install Node packages
npm install

# This installs:
# - Vite (build tool)
# - Tailwind CSS 4
# - Laravel Vite plugin
```

### Step 8: Build Frontend Assets
```bash
# Development build (fast, unminified)
npm run dev

# Or for production build (optimized):
npm run build
```

### Step 9: Start Development Server
```bash
# Option A: Laravel's built-in server
php artisan serve

# Access at: http://localhost:8000

# Option B: PHP Desktop (desktop app mode)
# See "Running as Desktop App" section below
```

---

## 🔧 Configuration

### Environment Variables (.env)

```env
# Application
APP_NAME=Lumi
APP_ENV=local
APP_KEY=base64:xxxxxxxxxxxxx  # Auto-generated
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite - default)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Localization
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Cashier Configuration (config/cashier.php)

```php
return [
    // Admin credentials (hardcoded for desktop app)
    'admin' => [
        'email' => 'admin@cashier.com',
        'password' => 'secret123',  // Change this!
        'name' => 'Administrator',
    ],

    // Default currency
    'currency' => '$',

    // Tax settings
    'tax' => [
        'enabled' => true,
        'default_rate' => 15,  // 15% VAT
        'label' => 'VAT',
    ],

    // Discount settings
    'discount' => [
        'max_without_approval' => 5,  // 5% threshold
        'enabled' => true,
    ],

    // Company info for invoices
    'company' => [
        'name' => 'Lumi POS',
        'address' => '123 Main Street',
        'city' => 'Your City, State 12345',
        'phone' => '+1 (555) 123-4567',
        'email' => 'info@lumipos.com',
        'website' => 'www.lumipos.com',
        'tax_id' => '123456789',
    ],
];
```

**⚠️ Important**: Change the admin password in production!

---

## 🗄️ Database Setup Details

### Migrations Overview
The system will create these tables:

1. **users** - Admin user (currently unused, future feature)
2. **categories** - Product categories (e.g., Beverages, Food)
3. **items** - Products/items for sale
4. **item_units** - Individual stock units (each unit = 1 item)
5. **days** - Business day sessions
6. **orders** - Customer orders
7. **order_items** - Line items in orders
8. **shop_settings** - Store configuration

### Seeders (Sample Data)
Run seeders to populate with test data:

```bash
# Seed all data
php artisan db:seed

# Or seed specific tables:
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ItemSeeder
php artisan db:seed --class=DaySeeder
```

**Sample data includes:**
- 5 categories (Beverages, Food, Electronics, etc.)
- 20+ items with prices and stock
- 1 open business day
- Sample orders

---

## 🖥️ Running as Desktop App

### PHP Desktop Configuration

The project includes PHP Desktop configuration in the root folder:

**File**: `phpdesktop.json`
```json
{
    "main_window": {
        "title": "Laravel Desktop App - POS System",
        "default_size": [1280, 720],
        "minimum_size": [800, 600],
        "center_on_screen": true
    },
    "web_server": {
        "listen_on": ["127.0.0.1", 8080],
        "www_directory": "www/public",
        "index_files": ["index.php"],
        "cgi_interpreter": "php/php-cgi.exe"
    },
    "chrome": {
        "cache_path": "webcache",
        "dev_tools_F12": true  # Enable for debugging
    }
}
```

### Starting Desktop App

```bash
# On Windows, double-click:
Start-Browser.bat

# Or start manually:
cd C:\LaravelDesktop
.\www-server.exe
```

### Desktop App Features
- ✅ Runs without internet
- ✅ No installation needed (portable)
- ✅ SQLite database (single file)
- ✅ Auto-starts local web server
- ✅ F12 for DevTools (debugging)
- ✅ F5 to reload page

---

## 🧪 Verify Installation

### Run Tests
```bash
# Run all tests
php artisan test

# Or run with coverage:
php artisan test --coverage

# Run specific test file:
php artisan test tests/Feature/OrderTest.php
```

### Check Database Connection
```bash
# Open Tinker (Laravel REPL)
php artisan tinker

# Try querying:
>>> App\Models\Category::count()
# Should return number of categories

>>> App\Models\Item::first()
# Should return first item
```

### Access the Application

1. **Login Page**: http://localhost:8000/login
2. **Credentials**: 
   - Email: `admin@cashier.com`
   - Password: `secret123`
3. **Dashboard**: Should show statistics and navigation

---

## 🔍 Troubleshooting Setup

### Issue: "Key not found" Error
**Solution**: Run `php artisan key:generate`

### Issue: Database Connection Error
**Solution**: 
```bash
# Ensure database file exists
type nul > database\database.sqlite

# Run migrations again
php artisan migrate:fresh
```

### Issue: NPM Build Fails
**Solution**:
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

### Issue: Permission Denied on Storage
**Solution**:
```bash
# Give write permissions to storage folders
# On Windows (run as Administrator):
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T

# On Linux/Mac:
chmod -R 775 storage bootstrap/cache
```

### Issue: PDF Generation Not Working
**Solution**:
```bash
# Check if GD extension is enabled
php -m | grep gd

# If missing, enable in php.ini:
extension=gd
```

### Issue: Arabic Text Not Showing in PDFs
**Solution**: 
- DomPDF uses DejaVu Sans font (built-in)
- Ensure `isHtml5ParserEnabled` is true in PDF config
- Check `config/dompdf.php` settings

---

## 📦 Development Tools

### Recommended VS Code Extensions
```
- PHP Intelephense (PHP intelligence)
- Laravel Blade Snippets (Blade syntax)
- Laravel goto view (Navigate to views)
- Tailwind CSS IntelliSense (CSS autocomplete)
- SQLite Viewer (View database)
- GitLens (Git integration)
```

### Useful Artisan Commands
```bash
# Clear all caches
php artisan optimize:clear

# List all routes
php artisan route:list

# Create new migration
php artisan make:migration create_xxx_table

# Create new model with migration
php artisan make:model Product -m

# Create controller
php artisan make:controller ProductController --resource

# Run code formatter
./vendor/bin/pint
```

---

## 🔄 Update Workflow

### Pulling Latest Changes
```bash
# Pull from repository
git pull origin main

# Update dependencies
composer install
npm install

# Run migrations (if any new ones)
php artisan migrate

# Rebuild assets
npm run build

# Clear cache
php artisan optimize:clear
```

---

## 🌍 Multi-Language Setup

### Switching Default Language
In `.env`:
```env
APP_LOCALE=ar  # For Arabic
# or
APP_LOCALE=en  # For English
```

### Adding New Language
```bash
# Create language folder
mkdir resources/lang/fr

# Copy messages file
cp resources/lang/en/messages.php resources/lang/fr/messages.php

# Translate strings in fr/messages.php

# Language will be available in switcher
```

---

## 🖨️ Printer Setup (Thermal Receipts)

### Supported Printers
- 80mm thermal receipt printers
- ESC/POS compatible printers
- Common brands: Epson, Star, Bixolon

### Configuration
1. Install printer drivers
2. Set as default printer in Windows
3. Print test receipt from app
4. Adjust margins in `resources/views/admin/orders/invoice.blade.php` if needed

### Testing Without Printer
- Use "Microsoft Print to PDF" as default printer
- PDFs will save instead of printing
- Useful for development

---

## 🚀 Performance Optimization

### Production Settings
```env
# .env for production
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
```

### Optimize Laravel
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Generate optimized autoloader
composer dump-autoload --optimize
```

---

## 📊 Monitoring & Logs

### Log Files
```
storage/logs/laravel.log  # Application logs
debug.log                 # PHP Desktop logs (root folder)
```

### Viewing Logs
```bash
# Tail Laravel logs (real-time)
php artisan pail

# Or use:
tail -f storage/logs/laravel.log
```

---

## ✅ Setup Checklist

- [ ] PHP 8.2+ installed and verified
- [ ] Composer installed
- [ ] Node.js & NPM installed
- [ ] Repository cloned
- [ ] `.env` file created and configured
- [ ] Application key generated
- [ ] Composer dependencies installed
- [ ] NPM packages installed
- [ ] Database migrated
- [ ] Sample data seeded (optional)
- [ ] Assets compiled (`npm run build`)
- [ ] Development server running
- [ ] Can login successfully
- [ ] Tests pass (`php artisan test`)

---

**Next**: [03-DATABASE-SCHEMA.md](./03-DATABASE-SCHEMA.md) - Understand the database structure
