# 01 - Project Overview

## 🎯 What is Lumi POS?

Lumi is a **complete Point of Sale (POS) and Inventory Management System** designed for small to medium-sized businesses. It provides a modern, full-featured solution for managing products, inventory, sales, and generating detailed reports.

### Primary Use Cases
- **Retail Stores**: Product sales, inventory tracking, receipt printing
- **Restaurants/Cafes**: Order management, table service, kitchen tickets
- **Small Businesses**: Stock control, sales analytics, customer management
- **Desktop Applications**: Runs as a standalone desktop app (no internet required)

---

## 🏢 Business Context

### Problem It Solves
- Manual inventory tracking is error-prone
- Need for real-time stock visibility
- Professional invoice generation
- Sales analytics for business insights
- Multi-language support for diverse markets
- Offline capability for unreliable internet

### Key Differentiators
- ✅ **Desktop-First**: Runs without internet using PHP Desktop
- ✅ **Bilingual**: Full English/Arabic support with RTL
- ✅ **Thermal Receipts**: Professional 80mm thermal printer receipts
- ✅ **Advanced Discounts**: Admin-verified discount system
- ✅ **Day Sessions**: Business day management for accurate reporting
- ✅ **Clean Architecture**: SOLID principles, easy to extend

---

## 🛠️ Technology Stack

### Backend Framework
```
Laravel 12.x (Latest)
├── PHP 8.2+
├── Eloquent ORM
├── Blade Templating
├── Database: SQLite (default)
└── PDO for database access
```

### Frontend Stack
```
Modern Web Stack
├── Tailwind CSS 4.0 (Utility-first CSS)
├── Alpine.js (Lightweight reactivity)
├── Chart.js (Analytics charts)
└── Blade Components (Reusable UI)
```

### PDF Generation
```
Invoice System
├── barryvdh/laravel-dompdf (PDF generation)
├── mpdf/mpdf (Alternative PDF engine)
├── milon/barcode (Barcode generation)
└── Arabic font support (DejaVu Sans)
```

### Desktop Wrapper
```
PHP Desktop
├── Chromium Embedded Framework
├── Built-in PHP 8.2 CGI server
├── Windows executable (.exe)
└── No installation required
```

---

## 📦 Core Features

### 1. Inventory Management
**What**: Manage products and stock levels  
**Why**: Prevent stockouts and overselling  
**How**: Category-based organization, SKU tracking, barcode generation

- Create/edit/delete categories
- Add items with SKU, price, description
- Bulk stock addition (multiple units at once)
- Real-time stock status (available, sold, damaged)
- Auto-generate unique barcodes
- Low stock alerts

### 2. Point of Sale (POS)
**What**: Process customer orders and sales  
**Why**: Fast checkout, accurate pricing, professional receipts  
**How**: Cart-based interface, payment processing, invoice generation

- Quick product search and selection
- Shopping cart with quantity controls
- Multiple payment methods (cash, card, other)
- Per-item and order-level discounts
- Real-time tax calculation
- Customer information capture
- Instant PDF receipt generation

### 3. Daily Sessions (Day Management)
**What**: Track sales by business day  
**Why**: Accurate daily reporting, cash reconciliation  
**How**: Open/close day workflow with validation

- Open a new business day
- All orders linked to current day
- Prevent closing with pending orders
- Daily sales summary
- Historical day records

### 4. Reports & Analytics
**What**: Sales insights and trends  
**Why**: Data-driven business decisions  
**How**: Charts, graphs, PDF exports

- Daily/weekly/monthly sales trends
- Category-wise sales distribution
- Top-selling items
- Inventory status overview
- Exportable PDF reports

### 5. Bilingual System
**What**: English and Arabic language support  
**Why**: Serve diverse markets (Middle East, etc.)  
**How**: Laravel localization, RTL CSS, custom fonts

- Full UI translation
- Right-to-left (RTL) layout for Arabic
- Language switcher in navigation
- PDF invoices in both languages

### 6. Discount & Tax System
**What**: Flexible pricing with admin controls  
**Why**: Promotional flexibility with oversight  
**How**: Percentage-based discounts, admin verification for high discounts

- Per-item discounts
- Order-level discounts
- Auto-approval for discounts ≤5%
- Admin credentials required for >5%
- Configurable tax rates (default 15% VAT)
- Tax applied after discount

---

## 🏗️ Architecture Overview

### Layered Architecture
```
┌─────────────────────────────────┐
│   Presentation Layer            │
│   (Blade Views, Controllers)    │
├─────────────────────────────────┤
│   Business Logic Layer          │
│   (Services, FormRequests)      │
├─────────────────────────────────┤
│   Data Access Layer             │
│   (Repositories, Eloquent)      │
├─────────────────────────────────┤
│   Database Layer                │
│   (SQLite, Migrations)          │
└─────────────────────────────────┘
```

### Design Patterns

#### 1. Repository Pattern
**Purpose**: Abstraction layer for data access  
**Implementation**: All database queries go through repository interfaces  
**Benefit**: Easy to swap database or add caching

```php
// Interface defines contract
interface OrderRepositoryInterface {
    public function find(int $id): ?Order;
    public function create(array $data): Order;
}

// Implementation handles actual database logic
class OrderRepository implements OrderRepositoryInterface {
    public function find(int $id): ?Order {
        return Order::with('items')->find($id);
    }
}
```

#### 2. Service Layer Pattern
**Purpose**: Encapsulate business logic  
**Implementation**: Controllers delegate to services  
**Benefit**: Reusable logic, testable, single responsibility

```php
class OrderService {
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryService $inventoryService
    ) {}
    
    public function createOrder(array $data): Order {
        // Complex business logic here
        // - Validate stock
        // - Calculate totals
        // - Create order
        // - Update inventory
    }
}
```

#### 3. Form Request Pattern
**Purpose**: Separate validation from controllers  
**Implementation**: Dedicated request classes  
**Benefit**: Clean controllers, reusable validation

```php
class StoreOrderRequest extends FormRequest {
    public function rules(): array {
        return [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            // ... more validation rules
        ];
    }
}
```

### SOLID Principles Application

**S - Single Responsibility**  
Each class has one job: Controllers route, Services process, Repositories query

**O - Open/Closed**  
New payment methods can be added without modifying existing code

**L - Liskov Substitution**  
Any repository implementation can replace another (interface-driven)

**I - Interface Segregation**  
Small, focused interfaces (CategoryRepositoryInterface has only category methods)

**D - Dependency Inversion**  
Controllers depend on interfaces, not concrete implementations

---

## 🗂️ Project Structure

```
www/
├── app/
│   ├── Console/               # Artisan commands
│   ├── Http/
│   │   ├── Controllers/       # Route handlers (thin)
│   │   ├── Middleware/        # Request filters (AdminAuth, SetLocale)
│   │   └── Requests/          # Validation classes
│   ├── Models/                # Eloquent models
│   ├── Repositories/          # Data access layer
│   │   ├── Contracts/         # Repository interfaces
│   │   ├── CategoryRepository.php
│   │   ├── ItemRepository.php
│   │   └── OrderRepository.php
│   ├── Services/              # Business logic
│   │   ├── DayService.php
│   │   ├── InventoryService.php
│   │   ├── OrderService.php
│   │   └── PdfGenerator.php
│   └── Providers/             # Service registration
│
├── config/                    # Configuration files
│   ├── cashier.php           # POS-specific config (admin, tax, company)
│   ├── database.php          # Database connection
│   └── app.php               # Laravel core config
│
├── database/
│   ├── migrations/           # Database schema definitions
│   ├── seeders/              # Sample/test data
│   └── database.sqlite       # SQLite database file
│
├── resources/
│   ├── views/                # Blade templates
│   │   ├── layouts/          # Master layouts
│   │   └── admin/            # Admin panel views
│   ├── lang/                 # Translations
│   │   ├── en/              # English
│   │   └── ar/              # Arabic
│   └── css/                  # Stylesheets
│
├── routes/
│   └── web.php              # All HTTP routes
│
├── public/                   # Web server root
│   ├── index.php            # Entry point
│   └── assets/              # Compiled CSS/JS
│
└── tests/                    # Automated tests
    ├── Feature/             # Integration tests
    └── Unit/                # Unit tests
```

---

## 🔐 Security Features

### Authentication
- **Session-based authentication** (no multi-user, single admin)
- **Hardcoded credentials** in `config/cashier.php` (for desktop app)
- **Middleware protection** on all admin routes
- **CSRF tokens** on all forms

### Input Validation
- **FormRequest classes** for server-side validation
- **Type hinting** on all methods
- **SQL injection protection** via Eloquent ORM
- **XSS protection** via Blade templating

### Access Control
- **AdminAuth middleware** checks session on every request
- **Session validation** against config credentials
- **Automatic logout** on invalid session

---

## 📊 Data Flow Examples

### Creating an Order
```
1. User clicks "Create Order" (POS page)
   ↓
2. OrderController@store receives request
   ↓
3. StoreOrderRequest validates input
   ↓
4. OrderService@createOrder processes:
   - Checks if business day is open (DayService)
   - Validates stock availability (InventoryService)
   - Calculates totals (subtotal, discount, tax)
   - Creates Order record (OrderRepository)
   - Creates OrderItem records
   - Decreases stock (InventoryService)
   ↓
5. Returns Order object to controller
   ↓
6. Controller redirects to order details page
   ↓
7. User can view/print PDF invoice
```

### Generating PDF Invoice
```
1. User clicks "Print Invoice"
   ↓
2. OrderController@show with ?download=true
   ↓
3. PdfGenerator@generateInvoice():
   - Loads order with relationships
   - Loads company settings from config
   - Renders Blade view to HTML
   - Converts HTML to PDF (DomPDF)
   - Adds barcode image
   ↓
4. Returns PDF stream to browser
   ↓
5. Browser opens/downloads PDF
```

---

## 🚀 Deployment Model

### Desktop Application (Primary)
- **Package**: Windows .exe file
- **Runtime**: PHP Desktop (Chromium + PHP 8.2 CGI)
- **Database**: SQLite (single file, no server needed)
- **Distribution**: Single executable + data folder
- **Updates**: Replace executable, keep database

### Web Application (Alternative)
- **Server**: Apache/Nginx with PHP 8.2+
- **Database**: MySQL or PostgreSQL
- **Deployment**: Standard Laravel deployment
- **Requires**: Web server configuration

---

## 📈 Performance Considerations

### Optimizations Implemented
- **Eager loading**: `.with()` to prevent N+1 queries
- **Database indexes**: On foreign keys and frequently queried columns
- **Query scopes**: Reusable query logic in models
- **Lock for update**: Concurrency control on stock operations
- **Pagination**: Large datasets use `paginate()`

### Scalability
- **Current**: Designed for single-location business (<10,000 orders/month)
- **Database**: SQLite suitable up to ~100,000 records
- **Migration path**: Can switch to MySQL/PostgreSQL for larger scale

---

## 🧪 Testing Strategy

### Test Coverage
- **Unit Tests**: Services, repositories, models
- **Feature Tests**: Full request/response cycles
- **Manual Testing**: UI/UX, PDF generation, printer compatibility

### Test Database
- Uses in-memory SQLite for fast test execution
- Factory classes for generating test data
- RefreshDatabase trait for clean state

---

## 📚 Learning Resources

### For New Laravel Developers
- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Laracasts Video Tutorials](https://laracasts.com)
- [Laravel Daily Blog](https://laraveldaily.com)

### For Repository Pattern
- [Repository Pattern in Laravel](https://dev.to/carlomigueldy/getting-started-with-repository-pattern-in-laravel-using-inheritance-and-dependency-injection-2opg)

### For SOLID Principles
- [SOLID Principles in PHP](https://github.com/wataridori/solid-php-example)

---

**Next**: [02-SETUP-GUIDE.md](./02-SETUP-GUIDE.md) - Get the project running locally
