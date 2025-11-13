# 15 - API Endpoints Reference

## 🌐 Routes Overview

All admin routes are protected by the `AdminAuth` middleware and prefixed with `/admin`.

---

## 🔐 Authentication

### Login
```http
GET /login
```
**Purpose**: Display login form  
**Middleware**: `guest`  
**Returns**: Login view

```http
POST /login
```
**Purpose**: Authenticate admin  
**Middleware**: `guest`  
**Request Body**:
```json
{
  "email": "admin@cashier.com",
  "password": "secret123"
}
```
**Success Response**: Redirect to `/admin/dashboard`  
**Error Response**: Back with errors

### Logout
```http
POST /logout
```
**Purpose**: Log out admin  
**Middleware**: `AdminAuth`  
**Success Response**: Redirect to `/login`

---

## 📊 Dashboard

```http
GET /admin
GET /admin/dashboard
```
**Purpose**: Display admin dashboard  
**Middleware**: `AdminAuth`  
**Returns**: Dashboard view with statistics
```php
[
    'todayOrders' => int,
    'todaySales' => float,
    'totalItems' => int,
    'totalCategories' => int
]
```

---

## 📂 Categories

### List Categories
```http
GET /admin/categories
```
**Purpose**: Display all categories  
**Query Parameters**:
- `page` (optional): Page number for pagination  

**Returns**: Paginated category list

### Create Category Form
```http
GET /admin/categories/create
```
**Purpose**: Display category creation form  
**Returns**: Create form view

### Store Category
```http
POST /admin/categories
```
**Purpose**: Create new category  
**Request Body**:
```json
{
  "name": "Electronics",
  "description": "Electronic devices and accessories"
}
```
**Validation Rules**:
- `name`: required, string, max:255
- `description`: nullable, string

**Success Response**: Redirect to category index with success message

### Show Category
```http
GET /admin/categories/{id}
```
**Purpose**: Display single category with items  
**URL Parameters**:
- `id`: Category ID  

**Returns**: Category detail view

### Edit Category Form
```http
GET /admin/categories/{id}/edit
```
**Purpose**: Display category edit form  
**Returns**: Edit form view

### Update Category
```http
PUT/PATCH /admin/categories/{id}
```
**Purpose**: Update existing category  
**Request Body**: Same as create  
**Success Response**: Redirect to category index

### Delete Category
```http
DELETE /admin/categories/{id}
```
**Purpose**: Soft delete category  
**Success Response**: Redirect to category index  
**Note**: CASCADE deletes all related items

### Export Categories PDF
```http
GET /admin/categories/export-pdf
```
**Purpose**: Download categories list as PDF  
**Returns**: PDF file download

---

## 📦 Items (Products)

### List Items
```http
GET /admin/items
```
**Query Parameters**:
- `page` (optional): Page number
- `search` (optional): Search term for name/SKU

**Returns**: Paginated item list with categories and stock

### Create Item Form
```http
GET /admin/items/create
```
**Returns**: Create form with category dropdown

### Store Item
```http
POST /admin/items
```
**Request Body**:
```json
{
  "category_id": 1,
  "name": "Laptop",
  "sku": "ELECT-001",
  "description": "High-performance laptop",
  "price": 999.99
}
```
**Validation Rules**:
- `category_id`: required, exists:categories,id
- `name`: required, string, max:255
- `sku`: nullable, string, unique:items,sku
- `description`: nullable, string
- `price`: required, numeric, min:0

**Success Response**: Redirect to item show page  
**Note**: Auto-generates barcode

### Show Item
```http
GET /admin/items/{id}
```
**Returns**: Item details with stock units

### Edit Item Form
```http
GET /admin/items/{id}/edit
```
**Returns**: Edit form

### Update Item
```http
PUT/PATCH /admin/items/{id}
```
**Request Body**: Same as create  
**Success Response**: Redirect to item show page

### Delete Item
```http
DELETE /admin/items/{id}
```
**Note**: CASCADE deletes all item_units

### Add Stock
```http
POST /admin/items/{id}/add-stock
```
**Purpose**: Add inventory units  
**Request Body**:
```json
{
  "quantity": 50,
  "price": 10.00  // optional, defaults to item price
}
```
**Success Response**: Redirect back with success message

### Export Items PDF
```http
GET /admin/items/export-pdf
```
**Returns**: PDF file with all items

---

## 🛒 Orders

### List Orders
```http
GET /admin/orders
```
**Query Parameters**:
- `page` (optional): Page number
- `status` (optional): Filter by status (pending/completed/cancelled)
- `day_id` (optional): Filter by business day

**Returns**: Paginated order list

### Create Order Form (POS)
```http
GET /admin/orders/create
```
**Purpose**: Point of Sale interface  
**Returns**: POS view with available items

### Store Order
```http
POST /admin/orders
```
**Request Body**:
```json
{
  "items": [
    {
      "item_id": 1,
      "quantity": 2,
      "discount_percentage": 5
    }
  ],
  "discount_percentage": 10,
  "tax_percentage": 15,
  "payment_method": "cash",
  "customer_name": "John Doe",
  "customer_phone": "555-1234",
  "customer_email": "john@example.com",
  "notes": "Extra packaging"
}
```
**Validation Rules**:
- `items`: required, array, min:1
- `items.*.item_id`: required, exists:items,id
- `items.*.quantity`: required, integer, min:1
- `items.*.discount_percentage`: nullable, numeric, min:0, max:100
- `discount_percentage`: nullable, numeric, min:0, max:100
- `tax_percentage`: nullable, numeric, min:0, max:100
- `payment_method`: nullable, in:cash,card,other
- `customer_name`: nullable, string, max:255
- `customer_phone`: nullable, string, max:20
- `customer_email`: nullable, email
- `notes`: nullable, string, max:500

**Success Response**: Redirect to order show page  
**Business Rules**:
- Requires open business day
- Validates stock availability
- Calculates totals automatically
- Decreases inventory

### Show Order
```http
GET /admin/orders/{id}
```
**Query Parameters**:
- `download` (optional): If true, returns PDF invoice

**Returns**: Order details view or PDF

### Download Invoice PDF
```http
GET /admin/orders/{id}?download=true
```
**Returns**: Thermal receipt PDF (80mm format)

### Cancel Order
```http
POST /admin/orders/{id}/cancel
```
**Purpose**: Cancel pending order  
**Success Response**: Redirect to order show  
**Note**: Restores inventory

### Verify Admin for Discount
```http
POST /admin/orders/verify-discount
```
**Purpose**: Verify admin credentials for discounts >5%  
**Request Body**:
```json
{
  "discount_percentage": 10,
  "admin_email": "admin@cashier.com",
  "admin_password": "secret123"
}
```
**Success Response**:
```json
{
  "success": true,
  "message": "Discount approved"
}
```
**Error Response**:
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

---

## 📅 Days (Business Sessions)

### List Days
```http
GET /admin/days
```
**Returns**: All business days (open and closed)

### Open New Day
```http
POST /admin/days/open
```
**Purpose**: Start new business day  
**Success Response**: Redirect to dashboard  
**Business Rules**:
- Only one day can be open
- Auto-closes previous day if open

### Close Day
```http
POST /admin/days/{id}/close
```
**Purpose**: Close current business day  
**Success Response**: Redirect to days index  
**Business Rules**:
- Cannot close with pending orders
- Calculates daily totals

### Show Day
```http
GET /admin/days/{id}
```
**Returns**: Day details with orders and totals

---

## 📈 Reports

### Sales Reports
```http
GET /admin/reports
```
**Returns**: Analytics dashboard with charts

### Daily Sales Report
```http
GET /admin/reports/daily
```
**Returns**: Daily sales breakdown (JSON or view)

### Weekly Sales Report
```http
GET /admin/reports/weekly
```
**Returns**: Last 7 days sales data

### Monthly Sales Report
```http
GET /admin/reports/monthly
```
**Returns**: Current month sales data

### Top Selling Items
```http
GET /admin/reports/top-items
```
**Returns**: Top 10 best-selling products

### Category Distribution
```http
GET /admin/reports/categories
```
**Returns**: Sales by category data

### Export Report PDF
```http
GET /admin/reports/export
```
**Query Parameters**:
- `type`: Report type (daily/weekly/monthly)
- `date`: Specific date (optional)

**Returns**: PDF report

---

## 📆 Monthly Overview

### Show Month
```http
GET /admin/months/{year}/{month}
```
**URL Parameters**:
- `year`: Year (e.g., 2025)
- `month`: Month number (1-12)

**Returns**: Calendar view with daily sales

---

## ⚙️ Settings

### Show Settings
```http
GET /admin/settings
```
**Returns**: Settings form

### Update Settings
```http
PUT/PATCH /admin/settings
```
**Request Body**:
```json
{
  "shop_name": "My Store",
  "tax_percentage": 15,
  "receipt_footer": "Thank you!"
}
```

---

## 🌍 Language Switching

```http
GET /lang/{locale}
```
**URL Parameters**:
- `locale`: Language code (en or ar)

**Purpose**: Switch interface language  
**Success Response**: Redirect back to previous page

---

## 📋 Response Formats

### Success Response (HTML)
```php
return redirect()->route('items.index')
    ->with('success', 'Item created successfully');
```

### Error Response (HTML)
```php
return back()
    ->withInput()
    ->with('error', 'An error occurred');
```

### JSON Response (AJAX)
```json
{
  "success": true,
  "message": "Operation completed",
  "data": { ... }
}
```

---

## 🔒 Middleware

### AdminAuth
**Applied to**: All `/admin/*` routes  
**Purpose**: Verify admin session  
**Redirects to**: `/login` if not authenticated

### SetLocale
**Applied to**: All routes  
**Purpose**: Set application language based on session

---

## 📊 Pagination

All list endpoints return paginated data:

**Query Parameter**: `page` (default: 1)  
**Items per page**: 20  

**Pagination Links**: Available in views via `{{ $items->links() }}`

---

## 🔍 Search & Filters

### Items Search
```http
GET /admin/items?search=laptop
```

### Orders Filter by Status
```http
GET /admin/orders?status=completed
```

### Orders Filter by Day
```http
GET /admin/orders?day_id=5
```

---

## ⚠️ Error Codes

| Code | Meaning                | Example                          |
|------|------------------------|----------------------------------|
| 200  | Success                | Order created                    |
| 302  | Redirect               | After successful form submission |
| 404  | Not Found              | Item with ID 999 doesn't exist   |
| 422  | Validation Failed      | Missing required fields          |
| 500  | Server Error           | Database connection failed       |

---

## 🧪 Testing with cURL

### Login
```bash
curl -X POST http://localhost:8000/login \
  -d "email=admin@cashier.com" \
  -d "password=secret123" \
  -c cookies.txt
```

### Create Category (Authenticated)
```bash
curl -X POST http://localhost:8000/admin/categories \
  -b cookies.txt \
  -d "name=Test Category" \
  -d "description=Test Description"
```

---

## 📝 Notes

- All POST/PUT/DELETE requests require CSRF token
- All admin routes require authentication
- Soft deletes are used (records marked as deleted, not removed)
- JSON responses available for AJAX endpoints
- All timestamps in UTC (convert to local timezone in views)

---

**Next**: [16-FRONTEND-GUIDE.md](./16-FRONTEND-GUIDE.md) - Blade templates and JavaScript
