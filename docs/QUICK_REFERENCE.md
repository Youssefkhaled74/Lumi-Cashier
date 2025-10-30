# Quick Reference: Model Relationships

## Relationship Map

```
┌─────────────┐
│  Category   │
└──────┬──────┘
       │ hasMany
       ▼
┌─────────────┐
│    Item     │
└──────┬──────┘
       │ hasMany
       ▼
┌─────────────┐      ┌──────────────┐
│  ItemUnit   │◄─────┤  OrderItem   │
└──────┬──────┘      └──────┬───────┘
       │                    │ belongsTo
       │                    ▼
       │              ┌──────────────┐
       │              │    Order     │
       │              └──────┬───────┘
       │                     │ belongsTo
       │                     ▼
       │              ┌──────────────┐
       └──────────────┤     Day      │
                      └──────────────┘
```

## Quick Access Patterns

### From Category
```php
$category->items                    // Get all items
$category->items()->inStock()       // Get in-stock items only
$category->total_items              // Count items (accessor)
```

### From Item
```php
$item->category                     // Get parent category
$item->units                        // Get all units
$item->orderItems                   // Get order items (through units)
$item->total_quantity               // Sum of all unit quantities
$item->inventory_value              // Total inventory value
$item->is_in_stock                  // Boolean: has stock
```

### From ItemUnit
```php
$unit->item                         // Get parent item
$unit->orderItems                   // Get orders using this unit
$unit->available_quantity           // Current stock (accessor)
$unit->is_available                 // Boolean: has stock
$unit->decreaseQuantity(5)          // Decrease stock by 5
$unit->increaseQuantity(10)         // Increase stock by 10
```

### From Order
```php
$order->day                         // Get business day
$order->items                       // Get order items (alias: orderItems)
$order->itemUnits                   // Get units (through order items)
$order->total_items_count           // Count of line items
$order->total_units_count           // Sum of quantities
$order->is_completed                // Boolean status check
$order->calculateTotal()            // Recalc total from items
$order->markAsCompleted()           // Set status to completed
```

### From OrderItem
```php
$orderItem->order                   // Get parent order
$orderItem->itemUnit                // Get item unit
$orderItem->item()                  // Get item (through unit)
$orderItem->item_name               // Item name (accessor)
$orderItem->barcode                 // Barcode (accessor)
$orderItem->subtotal                // Line total (accessor)
```

### From Day
```php
$day->orders                        // All orders
$day->completedOrders               // Completed orders only
$day->is_open                       // Boolean: day open
$day->is_closed                     // Boolean: day closed
$day->total_sales                   // Sum of completed order totals
$day->total_orders                  // Count of completed orders
$day->duration_in_hours             // Hours open
$day->open()                        // Open the day
$day->close()                       // Close the day
Day::getOrCreateToday()             // Static: get/create today
```

## Common Query Patterns

### Get items by category
```php
Item::byCategory($categoryId)->get()
Category::find(1)->items
```

### Find in-stock items
```php
Item::inStock()->get()
ItemUnit::inStock()->get()
```

### Find by barcode
```php
ItemUnit::byBarcode('ABC123')->first()
```

### Get today's orders
```php
$day = Day::current()->first();
$orders = $day->completedOrders;
```

### Get completed orders
```php
Order::completed()->get()
Order::completed()->forDay($dayId)->get()
```

### Date range queries
```php
Order::betweenDates('2025-01-01', '2025-12-31')->get()
Day::betweenDates('2025-01-01', '2025-12-31')->get()
```

### Eager loading (avoid N+1)
```php
// Load category with items and units
Category::with('items.units')->get()

// Load item with category and units
Item::with(['category', 'units'])->get()

// Load order with all related data
Order::with([
    'day',
    'items.itemUnit.item.category'
])->get()
```

## Scopes Reference

| Model | Scope | Description |
|-------|-------|-------------|
| Category | `active()` | Categories with items |
| Item | `inStock()` | Items with quantity > 0 |
| Item | `byCategory($id)` | Filter by category |
| ItemUnit | `inStock()` | Units with quantity > 0 |
| ItemUnit | `byBarcode($code)` | Find by barcode |
| Order | `completed()` | Status = completed |
| Order | `pending()` | Status = pending |
| Order | `forDay($id)` | Filter by day |
| Order | `betweenDates($s,$e)` | Date range |
| Day | `open()` | Open days |
| Day | `closed()` | Closed days |
| Day | `current()` | Today's open day |
| Day | `betweenDates($s,$e)` | Date range |

## Constants

### Order Status
```php
Order::STATUS_PENDING     // 'pending'
Order::STATUS_COMPLETED   // 'completed'
Order::STATUS_CANCELLED   // 'cancelled'
```

## Data Types

### Category
- `name` (string)
- `slug` (string)
- `description` (text, nullable)

### Item
- `category_id` (foreign key)
- `name` (string)
- `sku` (string, nullable, unique)
- `description` (text, nullable)
- `price` (decimal:2)

### ItemUnit
- `item_id` (foreign key)
- `barcode` (string, unique)
- `quantity` (integer)
- `price` (decimal:2)

### Order
- `day_id` (foreign key, nullable)
- `total` (decimal:2)
- `status` (string)
- `notes` (text, nullable)

### OrderItem
- `order_id` (foreign key)
- `item_unit_id` (foreign key)
- `quantity` (integer)
- `price` (decimal:2)
- `total` (decimal:2, auto-calculated)

### Day
- `date` (date, unique)
- `opened_at` (datetime, nullable)
- `closed_at` (datetime, nullable)
- `notes` (text, nullable)
