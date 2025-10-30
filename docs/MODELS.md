# Eloquent Models Documentation

## Overview

This document describes all Eloquent models in the Lumi POS/Inventory system, their relationships, attributes, and key methods.

---

## Models

### 1. Category

**File:** `app/Models/Category.php`

**Description:** Represents product categories for organizing inventory items.

**Fillable Attributes:**
- `name` (string) - Category name
- `slug` (string) - URL-friendly identifier
- `description` (text, nullable) - Category description

**Relationships:**
- `items()` - HasMany → Item (one category has many items)

**Accessors:**
- `total_items` - Returns count of items in this category

**Scopes:**
- `active()` - Only categories that have items

**Features:**
- Soft deletes enabled
- Timestamps (created_at, updated_at)

---

### 2. Item

**File:** `app/Models/Item.php`

**Description:** Represents a product/item in the inventory system.

**Fillable Attributes:**
- `category_id` (foreign key) - References categories table
- `name` (string) - Item name
- `sku` (string, nullable, unique) - Stock Keeping Unit
- `description` (text, nullable) - Item description
- `price` (decimal) - Base price

**Relationships:**
- `category()` - BelongsTo → Category
- `units()` - HasMany → ItemUnit (one item can have multiple units with different barcodes)
- `orderItems()` - HasManyThrough → OrderItem (through ItemUnit)

**Accessors:**
- `total_quantity` - Total quantity across all units
- `inventory_value` - Total value of inventory (sum of quantity * price for all units)
- `is_in_stock` - Boolean indicating if item has stock

**Scopes:**
- `inStock()` - Only items with quantity > 0
- `byCategory($categoryId)` - Filter by category

**Features:**
- Soft deletes enabled
- Timestamps
- Price cast to decimal:2

---

### 3. ItemUnit

**File:** `app/Models/ItemUnit.php`

**Description:** Represents individual units of an item with unique barcodes. Each unit tracks its own quantity and can be sold independently.

**Fillable Attributes:**
- `item_id` (foreign key) - References items table
- `barcode` (string, unique) - Unique barcode for this unit
- `quantity` (integer) - Available quantity for this unit
- `price` (decimal) - Unit price (can differ from base item price)

**Relationships:**
- `item()` - BelongsTo → Item
- `orderItems()` - HasMany → OrderItem

**Accessors:**
- `total_sold` - Total quantity sold through orders
- `available_quantity` - Current available stock (max 0, quantity)
- `is_available` - Boolean indicating if unit has stock

**Methods:**
- `decreaseQuantity($amount)` - Decrease quantity by amount (returns false if insufficient)
- `increaseQuantity($amount)` - Increase quantity by amount

**Scopes:**
- `inStock()` - Only units with quantity > 0
- `byBarcode($barcode)` - Find by barcode

**Features:**
- Soft deletes enabled
- Timestamps
- Quantity cast to integer
- Price cast to decimal:2

---

### 4. Order

**File:** `app/Models/Order.php`

**Description:** Represents a customer order/sale.

**Constants:**
- `STATUS_PENDING` = 'pending'
- `STATUS_COMPLETED` = 'completed'
- `STATUS_CANCELLED` = 'cancelled'

**Fillable Attributes:**
- `day_id` (foreign key, nullable) - References days table
- `total` (decimal) - Order total amount
- `status` (string) - Order status (pending/completed/cancelled)
- `notes` (text, nullable) - Order notes

**Relationships:**
- `items()` / `orderItems()` - HasMany → OrderItem
- `day()` - BelongsTo → Day
- `itemUnits()` - HasManyThrough → ItemUnit

**Accessors:**
- `total_items_count` - Count of distinct items in order
- `total_units_count` - Sum of quantities across all order items
- `is_completed` - Boolean check for completed status
- `is_pending` - Boolean check for pending status
- `is_cancelled` - Boolean check for cancelled status

**Methods:**
- `calculateTotal()` - Recalculate and save order total from order items
- `markAsCompleted()` - Set status to completed
- `markAsCancelled()` - Set status to cancelled

**Scopes:**
- `completed()` - Only completed orders
- `pending()` - Only pending orders
- `forDay($dayId)` - Orders for specific day
- `betweenDates($start, $end)` - Orders in date range

**Features:**
- Soft deletes enabled
- Timestamps
- Total cast to decimal:2
- Default status: pending

---

### 5. OrderItem

**File:** `app/Models/OrderItem.php`

**Description:** Line items within an order, linking orders to specific item units.

**Fillable Attributes:**
- `order_id` (foreign key) - References orders table
- `item_unit_id` (foreign key) - References item_units table
- `quantity` (integer) - Quantity ordered
- `price` (decimal) - Unit price at time of order
- `total` (decimal) - Line total (auto-calculated: quantity * price)

**Relationships:**
- `order()` - BelongsTo → Order
- `itemUnit()` - BelongsTo → ItemUnit
- `item()` - Through itemUnit to Item

**Accessors:**
- `item_name` - Item name through relationships
- `barcode` - Barcode through relationships
- `subtotal` - Alias for total

**Methods:**
- `calculateTotal()` - Calculate line total (quantity * price)

**Features:**
- Soft deletes enabled
- Timestamps
- Auto-calculates `total` on save via boot() method
- Price and total cast to decimal:2
- Quantity cast to integer

---

### 6. Day

**File:** `app/Models/Day.php`

**Description:** Represents a business day session (open/close day tracking for daily sales reporting).

**Fillable Attributes:**
- `date` (date, unique) - Business date
- `opened_at` (datetime, nullable) - When day was opened
- `closed_at` (datetime, nullable) - When day was closed
- `notes` (text, nullable) - Day notes

**Relationships:**
- `orders()` - HasMany → Order
- `completedOrders()` - HasMany → Order (filtered to completed status)

**Accessors:**
- `is_open` - Boolean: day is opened and not yet closed
- `is_closed` - Boolean: day is closed
- `total_sales` - Total sales amount for completed orders
- `total_orders` - Count of completed orders
- `duration_in_hours` - Hours between opened_at and closed_at (or now)

**Methods:**
- `open()` - Open the day (sets opened_at to now)
- `close()` - Close the day (sets closed_at to now)

**Static Methods:**
- `getOrCreateToday()` - Get or create today's day record

**Scopes:**
- `open()` - Only open days
- `closed()` - Only closed days
- `current()` - Current open day for today
- `betweenDates($start, $end)` - Days in date range

**Features:**
- Soft deletes enabled
- Timestamps
- Date casts for date, opened_at, closed_at

---

## Relationship Graph

```
Category
  ↓ (hasMany)
Item
  ↓ (hasMany)
ItemUnit ←──────┐
  ↑              │ (belongsTo)
  │ (belongsTo) │
OrderItem        │
  ↑              │
  │ (hasMany)   │
Order ───────────┘ (hasManyThrough)
  ↑
  │ (hasMany)
Day
```

### Detailed Relationships:

1. **Category → Item** (1:N)
   - One category has many items
   - Each item belongs to one category

2. **Item → ItemUnit** (1:N)
   - One item has many units (each with unique barcode)
   - Each unit belongs to one item

3. **ItemUnit → OrderItem** (1:N)
   - One item unit can appear in many order items
   - Each order item references one item unit

4. **Order → OrderItem** (1:N)
   - One order has many order items (line items)
   - Each order item belongs to one order

5. **Day → Order** (1:N)
   - One day has many orders
   - Each order belongs to one day (nullable)

6. **Item → OrderItem** (1:N through ItemUnit)
   - Items can access their order items through units

7. **Order → ItemUnit** (N:N through OrderItem)
   - Orders access item units through order items

---

## SOLID Principles Applied

### Single Responsibility Principle (SRP)
- Each model focuses on its domain entity
- Business logic separated into methods (e.g., `calculateTotal()`, `decreaseQuantity()`)
- Relationships clearly defined

### Open/Closed Principle (OCP)
- Models are open for extension via scopes, accessors, and mutators
- Traits (HasFactory, SoftDeletes) extend functionality without modification

### Liskov Substitution Principle (LSP)
- All models extend Eloquent Model consistently
- Traits used uniformly across models

### Interface Segregation Principle (ISP)
- Models expose only necessary relationships and methods
- Scopes provide focused query interfaces

### Dependency Inversion Principle (DIP)
- Models depend on Eloquent abstractions
- Repository pattern can be added for data access layer abstraction

---

## Best Practices Implemented

1. **Type Hinting:** All method signatures use proper type hints
2. **Casting:** Proper attribute casting for dates, decimals, integers
3. **Soft Deletes:** All models use soft deletes for data recovery
4. **Scopes:** Query scopes for common filters
5. **Accessors:** Computed attributes for derived data
6. **Constants:** Status constants in Order model
7. **Documentation:** PHPDoc blocks for all methods
8. **Validation:** Foreign key constraints in migrations
9. **Events:** Boot method in OrderItem for auto-calculation
10. **Naming:** Clear, descriptive names following Laravel conventions

---

## Usage Examples

### Creating an Order
```php
$day = Day::getOrCreateToday();
$order = Order::create([
    'day_id' => $day->id,
    'status' => Order::STATUS_PENDING,
]);

$itemUnit = ItemUnit::byBarcode('ABC123')->first();
$orderItem = $order->items()->create([
    'item_unit_id' => $itemUnit->id,
    'quantity' => 2,
    'price' => $itemUnit->price,
]);

$itemUnit->decreaseQuantity(2);
$order->calculateTotal();
$order->markAsCompleted();
```

### Getting Category Items
```php
$category = Category::with('items.units')->find(1);
$inStockItems = $category->items()->inStock()->get();
```

### Daily Sales Report
```php
$day = Day::current()->first();
$totalSales = $day->total_sales;
$orderCount = $day->total_orders;
$orders = $day->completedOrders()->with('items.itemUnit.item')->get();
```

### Inventory Check
```php
$item = Item::with('units')->find(1);
$totalStock = $item->total_quantity;
$inventoryValue = $item->inventory_value;
$isInStock = $item->is_in_stock;
```
