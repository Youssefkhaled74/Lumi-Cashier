# Lumi POS & Inventory Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <strong>A modern, full-featured Point of Sale and Inventory Management System built with Laravel</strong>
</p>

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Architecture](#architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)
- [Admin Credentials](#admin-credentials)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 About

**Lumi** is a complete Point of Sale (POS) and Inventory Management System designed for small to medium-sized businesses. Built with Laravel 11, it provides a clean, modern interface for managing products, inventory, sales, and generating detailed reports.

### Tech Stack

- **Framework:** Laravel 11
- **PHP:** 8.2+
- **Database:** SQLite (easily switchable to MySQL/PostgreSQL)
- **Frontend:** Tailwind CSS, Bootstrap Icons
- **PDF Generation:** barryvdh/laravel-dompdf
- **Barcode Generation:** milon/barcode
- **Charts:** Chart.js

---

## ✨ Features

### 📦 Inventory Management
- **Category Management:** Organize items into categories
- **Item Management:** Complete CRUD operations for products
- **Stock Tracking:** Real-time stock levels with status tracking (available, sold, damaged)
- **Barcode Generation:** Auto-generate unique barcodes for each item
- **SKU Management:** Custom SKU support
- **Bulk Stock Addition:** Add multiple units at once

### 🛒 Point of Sale
- **Quick Checkout:** Fast order creation interface
- **Real-time Stock Validation:** Prevent overselling with live stock checks
- **Order Management:** View, track, and cancel orders
- **PDF Invoices:** Professional printable invoices with barcodes
- **Order History:** Complete transaction records

### 📊 Daily Sessions
- **Day Management:** Open and close business days
- **Sales Tracking:** Track sales per business session
- **Order Validation:** Prevent closing with pending orders
- **Daily Summaries:** Revenue and order count per day

### 📈 Reports & Analytics
- **Sales Analytics:** Daily, weekly, and monthly sales charts
- **Category Distribution:** Visual breakdown by category
- **Top Selling Items:** Track best-performing products
- **Inventory Status:** Low stock alerts and availability overview
- **Export Capabilities:** Generate PDF reports

### 🔐 Authentication & Security
- **Session-based Authentication:** Secure admin access
- **CSRF Protection:** Built-in Laravel security
- **Middleware Protection:** AdminAuth middleware for all routes
- **Input Validation:** FormRequest validation classes

### 🏗️ Architecture (SOLID Principles)
- **Repository Pattern:** Abstraction layer for data access
- **Service Layer:** Business logic separated from controllers
- **FormRequests:** Dedicated validation classes
- **Thin Controllers:** Clean, readable controller methods
- **Dependency Injection:** Constructor-based DI throughout
- **Interface-driven Design:** Contract-based programming

---

## 🏛️ Architecture

The application follows SOLID principles and clean architecture:

```
app/
├── Http/
│   ├── Controllers/          # Thin controllers using services
│   │   ├── CategoryController.php
│   │   ├── ItemController.php
│   │   ├── OrderController.php
│   │   ├── DayController.php
│   │   └── ReportController.php
│   ├── Requests/             # FormRequest validation
│   │   ├── StoreCategoryRequest.php
│   │   ├── StoreItemRequest.php
│   │   ├── StoreOrderRequest.php
│   │   └── Update*.php
│   └── Middleware/
│       └── AdminAuth.php
├── Models/                   # Eloquent models
│   ├── Category.php
│   ├── Item.php
│   ├── ItemUnit.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── Day.php
├── Repositories/
│   ├── Contracts/           # Repository interfaces
│   │   ├── CategoryRepositoryInterface.php
│   │   ├── ItemRepositoryInterface.php
│   │   └── OrderRepositoryInterface.php
│   ├── CategoryRepository.php
│   ├── ItemRepository.php
│   └── OrderRepository.php
├── Services/                 # Business logic layer
│   ├── InventoryService.php
│   ├── OrderService.php
│   └── DayService.php
└── Providers/
    └── RepositoryServiceProvider.php
```

---

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM (for asset compilation)
- SQLite extension enabled

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd Lumi
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies (optional, for development)
npm install
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup

The project uses SQLite by default. Create the database file:

```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### Step 5: Run the Application

```bash
# Start the development server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## ⚙️ Configuration

### Database Configuration

The default database is SQLite. To use MySQL or PostgreSQL, update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lumi_pos
DB_USERNAME=root
DB_PASSWORD=
```

### Company Information

Update company details in `config/cashier.php`:

```php
'company' => [
    'name' => 'Your Company Name',
    'address' => '123 Main Street',
    'city' => 'Your City, State 12345',
    'phone' => '+1 (555) 123-4567',
    'email' => 'info@yourcompany.com',
]
```

### Admin Credentials

Default credentials are set in `config/cashier.php`:

```php
'admin' => [
    'email' => 'admin@cashier.com',
    'password' => 'secret123',
    'name' => 'Admin User',
]
```

**⚠️ IMPORTANT:** Change these credentials in production!

---

## 📖 Usage

### 1. Login

Navigate to `/login` and use the admin credentials:
- **Email:** admin@cashier.com
- **Password:** secret123

### 2. Open a Business Day

Before making any sales:
1. Go to Dashboard
2. Click "Daily Sessions" in sidebar
3. Click "Open Day"

### 3. Manage Categories

1. Navigate to "Categories" in sidebar
2. Click "New Category"
3. Fill in name and description
4. Save

### 4. Add Items

1. Navigate to "Items" in sidebar
2. Click "New Item"
3. Select category
4. Enter item details (name, SKU, price)
5. Set initial stock quantity
6. Barcode is auto-generated if not provided
7. Save

### 5. Make a Sale (POS)

1. Click "Point of Sale" or "New Sale"
2. Select items and quantities
3. Review total
4. Click "Create Order"
5. Download/print invoice

### 6. View Reports

1. Navigate to "Reports & Analytics"
2. Select date range
3. View charts and statistics
4. Export as needed

### 7. Close Business Day

At end of day:
1. Go to "Daily Sessions"
2. Click "Close Day"
3. System shows daily summary

---

## 🧪 Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature
```

### Test Coverage

```bash
php artisan test --coverage
```

---

## 🔑 Admin Credentials

### Default Login

| Field    | Value              |
|----------|--------------------|
| Email    | admin@cashier.com  |
| Password | secret123          |

**Security Note:** Always change default credentials in production environments!

---

## 📸 Screenshots

> Add screenshots of your application here

### Dashboard
![Dashboard](#)

### POS Interface
![POS](#)

### Inventory Management
![Inventory](#)

### Reports
![Reports](#)

---

## 🛠️ Development Commands

### Common Artisan Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Fresh database with seed data
php artisan migrate:fresh --seed

# Generate barcode for item (custom command example)
php artisan app:generate-barcode

# Run tests
php artisan test
```

### Code Quality

```bash
# Run Laravel Pint (code formatter)
./vendor/bin/pint

# Run PHPStan (static analysis)
./vendor/bin/phpstan analyse
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Bootstrap Icons](https://icons.getbootstrap.com) - Icon library
- [Chart.js](https://www.chartjs.org) - JavaScript charting library
- [DomPDF](https://github.com/barryvdh/laravel-dompdf) - PDF generation
- [Barcode](https://github.com/milon/barcode) - Barcode generation

---

## 📞 Support

For support, email admin@cashier.com or open an issue in the repository.

---

**Made with ❤️ using Laravel**


In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
