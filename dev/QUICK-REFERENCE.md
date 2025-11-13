# Quick Reference Guide - Lumi POS

## 🚀 Common Commands

### Development
```bash
# Start development server
php artisan serve

# Build assets (development)
npm run dev

# Build assets (production)
npm run build

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database with seeds
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name

# Create seeder
php artisan make:seeder TableSeeder
```

### Code Generation
```bash
# Create model with migration
php artisan make:model ModelName -m

# Create controller
php artisan make:controller ControllerName --resource

# Create FormRequest
php artisan make:request StoreModelRequest

# Create middleware
php artisan make:middleware MiddlewareName

# Create service (manual - no artisan command)
# Create file: app/Services/ServiceName.php
```

---

## 📁 File Locations

### Configuration
- **Admin Credentials**: `config/cashier.php`
- **Database**: `config/database.php`
- **App Settings**: `config/app.php`
- **Environment**: `.env`

### Code
- **Models**: `app/Models/`
- **Controllers**: `app/Http/Controllers/`
- **Services**: `app/Services/`
- **Repositories**: `app/Repositories/`
- **Middleware**: `app/Http/Middleware/`
- **Requests**: `app/Http/Requests/`

### Views
- **Layouts**: `resources/views/layouts/`
- **Admin Views**: `resources/views/admin/`
- **Translations**: `resources/lang/en/` & `resources/lang/ar/`

### Database
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/`
- **SQLite DB**: `database/database.sqlite`

---

## 🔑 Important Classes

### Models
```php
Category        // app/Models/Category.php
Item            // app/Models/Item.php
ItemUnit        // app/Models/ItemUnit.php
Order           // app/Models/Order.php
OrderItem       // app/Models/OrderItem.php
Day             // app/Models/Day.php
ShopSettings    // app/Models/ShopSettings.php
```

### Services
```php
OrderService        // app/Services/OrderService.php
InventoryService    // app/Services/InventoryService.php
DayService          // app/Services/DayService.php
PdfGenerator        // app/Services/PdfGenerator.php
```

### Repositories
```php
CategoryRepository  // app/Repositories/CategoryRepository.php
ItemRepository      // app/Repositories/ItemRepository.php
OrderRepository     // app/Repositories/OrderRepository.php
```

---

## 🎯 Common Tasks

### Adding New Item
1. Navigate to Items → Add New Item
2. Select category
3. Enter name, SKU, price
4. Save
5. Add stock units

### Processing Order (POS)
1. Navigate to Point of Sale
2. Search and add items to cart
3. Adjust quantities if needed
4. Apply discount (if any)
5. Select payment method
6. Add customer info (optional)
7. Create Order
8. Print invoice

### Opening Business Day
1. Navigate to Daily Sessions
2. Click "Open New Day"
3. System auto-closes previous day

### Closing Business Day
1. Navigate to Daily Sessions
2. Ensure no pending orders
3. Click "Close Day"
4. View daily summary

### Viewing Reports
1. Navigate to Reports
2. Select time period (daily/weekly/monthly)
3. View charts and analytics
4. Export to PDF if needed

---

## 🐛 Troubleshooting Quick Fixes

### "Application key not set"
```bash
php artisan key:generate
```

### Database not found
```bash
type nul > database\database.sqlite
php artisan migrate
```

### "Class not found" errors
```bash
composer dump-autoload
```

### Assets not loading
```bash
npm run build
php artisan optimize:clear
```

### Permission errors (storage)
```bash
# Windows (run as Administrator)
icacls storage /grant Everyone:F /T
```

### Routes not working
```bash
php artisan route:clear
php artisan route:cache
```

---

## 📊 Database Quick Reference

### Tables
```
categories      - Product categories
items           - Products/services
item_units      - Individual stock units
days            - Business day sessions
orders          - Customer orders
order_items     - Order line items
shop_settings   - Store configuration
```

### Key Relationships
```
Category → has many Items
Item → has many ItemUnits
Day → has many Orders
Order → has many OrderItems
ItemUnit → has many OrderItems (through sales)
```

---

## 🔐 Default Credentials

**Admin Login**:
- Email: `admin@cashier.com`
- Password: `secret123`

**Location**: `config/cashier.php`

⚠️ **Change in production!**

---

## 🌍 Language Codes

- **English**: `en`
- **Arabic**: `ar`

**Switch in URL**: `/lang/en` or `/lang/ar`

---

## 💰 Price Calculations

### Order Total Formula
```
Subtotal = Sum of all order items
Discount Amount = Subtotal × (Discount % / 100)
Amount After Discount = Subtotal - Discount Amount
Tax Amount = Amount After Discount × (Tax % / 100)
Final Total = Amount After Discount + Tax Amount
```

### Item Line Total
```
Base Amount = Quantity × Price
Item Discount = Base Amount × (Item Discount % / 100)
Line Total = Base Amount - Item Discount
```

---

## 🎨 UI Components

### Tailwind CSS Classes
```css
/* Primary Button */
bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded

/* Success Alert */
bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded

/* Error Alert */
bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded

/* Card */
bg-white rounded-lg shadow p-6

/* Table */
w-full divide-y divide-gray-200
```

### Blade Directives
```blade
@extends('layouts.admin')
@section('content') ... @endsection
@if() ... @endif
@foreach() ... @endforeach
@forelse() ... @empty ... @endforelse
@auth ... @endauth
@guest ... @endguest
{{ $variable }}  /* Escaped output */
{!! $html !!}    /* Unescaped output */
```

---

## 🔍 Useful Eloquent Queries

### Get available stock for item
```php
ItemUnit::where('item_id', $id)
    ->where('status', 'available')
    ->count();
```

### Get today's completed orders
```php
Order::whereHas('day', function ($query) {
        $query->where('status', 'open');
    })
    ->where('status', 'completed')
    ->get();
```

### Get top selling items
```php
Item::withCount(['units' => function ($query) {
        $query->where('status', 'sold');
    }])
    ->orderBy('units_count', 'desc')
    ->take(10)
    ->get();
```

---

## 🎯 Routes Quick Reference

### Main Routes
```
GET  /admin/dashboard        - Dashboard
GET  /admin/categories       - Categories list
GET  /admin/items            - Items list
GET  /admin/orders           - Orders list
GET  /admin/orders/create    - POS interface
GET  /admin/days             - Business days
GET  /admin/reports          - Reports & analytics
```

### Resource Routes Pattern
```
GET    /resource              - index (list)
GET    /resource/create       - create form
POST   /resource              - store (save)
GET    /resource/{id}         - show (detail)
GET    /resource/{id}/edit    - edit form
PUT    /resource/{id}         - update
DELETE /resource/{id}         - destroy
```

---

## 📝 Validation Rules Quick Reference

```php
'required'                  - Must be present
'nullable'                  - Can be null
'string'                    - Must be string
'integer'                   - Must be integer
'numeric'                   - Must be numeric
'email'                     - Must be valid email
'min:5'                     - Minimum value/length
'max:255'                   - Maximum value/length
'unique:table,column'       - Must be unique
'exists:table,column'       - Must exist in table
'in:cash,card,other'        - Must be in list
'array'                     - Must be array
'date'                      - Must be valid date
'decimal:2'                 - Decimal with 2 places
```

---

## 🛠️ Git Workflow

```bash
# Check status
git status

# Stage changes
git add .

# Commit
git commit -m "Description of changes"

# Push to remote
git push origin main

# Pull latest changes
git pull origin main

# Create new branch
git checkout -b feature-name

# Switch branch
git checkout branch-name
```

---

## 📦 Package.json Scripts

```bash
npm run dev      # Start Vite dev server
npm run build    # Build for production
```

---

## 🔧 Config Values

### Default Tax Rate
**File**: `config/cashier.php`
```php
'tax' => [
    'default_rate' => 15,  // 15% VAT
]
```

### Discount Threshold
**File**: `config/cashier.php`
```php
'discount' => [
    'max_without_approval' => 5,  // 5%
]
```

### Currency Symbol
**File**: `config/cashier.php`
```php
'currency' => '$',
```

---

## 📱 Browser Shortcuts (PHP Desktop)

- **F5**: Reload page
- **F12**: Open DevTools
- **Ctrl + Shift + I**: Open DevTools
- **Ctrl + R**: Reload

---

## ✅ Pre-Deployment Checklist

- [ ] Change admin password in `config/cashier.php`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `npm run build`
- [ ] Test all critical features
- [ ] Backup database
- [ ] Update company info in `config/cashier.php`

---

## 📞 Support Resources

- **Laravel Docs**: https://laravel.com/docs
- **Tailwind Docs**: https://tailwindcss.com/docs
- **PHP Desktop**: https://github.com/cztomczak/phpdesktop

---

**Last Updated**: November 2025
