# 03 - Database Schema

## 📊 Database Overview

The Lumi POS system uses **SQLite** as the default database (can be switched to MySQL/PostgreSQL). The schema is designed to support a complete POS workflow with inventory tracking, order management, and business day sessions.

---

## 🗂️ Entity Relationship Diagram

```
┌─────────────────┐
│   categories    │
│─────────────────│
│ id              │←─┐
│ name            │  │
│ description     │  │
│ created_at      │  │
│ updated_at      │  │
└─────────────────┘  │
                     │ 1:N
                     │
┌─────────────────┐  │
│     items       │  │
│─────────────────│  │
│ id              │  │
│ category_id     │──┘
│ name            │
│ sku             │
│ description     │
│ price           │
│ barcode         │
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        │
        ↓
┌─────────────────┐       ┌─────────────────┐
│   item_units    │       │      days       │
│─────────────────│       │─────────────────│
│ id              │       │ id              │
│ item_id         │──┐    │ date            │
│ quantity        │  │    │ status          │
│ price           │  │    │ opened_at       │
│ status          │  │    │ closed_at       │
│ order_id        │  │    │ created_at      │
│ created_at      │  │    │ updated_at      │
│ updated_at      │  │    └─────────────────┘
└─────────────────┘  │            │
                     │            │ 1:N
                     │            │
                     │            ↓
                     │    ┌─────────────────┐
                     │    │     orders      │
                     │    │─────────────────│
                     │    │ id              │
                     │    │ day_id          │
                     │    │ subtotal        │
                     │    │ discount_%      │
                     │    │ discount_amt    │
                     │    │ tax_%           │
                     │    │ tax_amt         │
                     │    │ total           │
                     │    │ status          │
                     │    │ payment_method  │
                     │    │ customer_name   │
                     │    │ customer_phone  │
                     │    │ customer_email  │
                     │    │ notes           │
                     │    │ created_at      │
                     │    │ updated_at      │
                     │    │ deleted_at      │
                     │    └─────────────────┘
                     │            │
                     │            │ 1:N
                     │            │
                     │            ↓
                     │    ┌─────────────────┐
                     └───→│  order_items    │
                          │─────────────────│
                          │ id              │
                          │ order_id        │
                          │ item_unit_id    │
                          │ quantity        │
                          │ price           │
                          │ discount_%      │
                          │ discount_amt    │
                          │ total           │
                          │ created_at      │
                          │ updated_at      │
                          └─────────────────┘

┌──────────────────┐
│  shop_settings   │
│──────────────────│
│ id               │
│ shop_name        │
│ tax_percentage   │
│ receipt_footer   │
│ created_at       │
│ updated_at       │
└──────────────────┘
```

---

## 📋 Table Definitions

### 1. categories
**Purpose**: Organize items into groups (e.g., Beverages, Food, Electronics)

| Column      | Type         | Constraints                  | Description                    |
|-------------|--------------|------------------------------|--------------------------------|
| id          | BIGINT       | PRIMARY KEY, AUTO_INCREMENT  | Unique identifier              |
| name        | VARCHAR(255) | NOT NULL                     | Category name                  |
| description | TEXT         | NULLABLE                     | Optional description           |
| created_at  | TIMESTAMP    | NOT NULL                     | Creation timestamp             |
| updated_at  | TIMESTAMP    | NOT NULL                     | Last update timestamp          |
| deleted_at  | TIMESTAMP    | NULLABLE                     | Soft delete timestamp          |

**Indexes**: 
- Primary key on `id`

**Relationships**:
- One-to-many with `items`

**Example Data**:
```sql
id | name         | description
1  | Beverages    | Drinks and refreshments
2  | Food         | Food items and snacks
3  | Electronics  | Electronic devices
```

---

### 2. items
**Purpose**: Products available for sale

| Column      | Type           | Constraints                  | Description                    |
|-------------|----------------|------------------------------|--------------------------------|
| id          | BIGINT         | PRIMARY KEY, AUTO_INCREMENT  | Unique identifier              |
| category_id | BIGINT         | FOREIGN KEY, NOT NULL        | Links to categories.id         |
| name        | VARCHAR(255)   | NOT NULL                     | Item name                      |
| sku         | VARCHAR(255)   | UNIQUE, NULLABLE             | Stock Keeping Unit             |
| description | TEXT           | NULLABLE                     | Item description               |
| price       | DECIMAL(12,2)  | NOT NULL, DEFAULT 0          | Default selling price          |
| barcode     | VARCHAR(255)   | UNIQUE, NULLABLE             | Generated barcode              |
| created_at  | TIMESTAMP      | NOT NULL                     | Creation timestamp             |
| updated_at  | TIMESTAMP      | NOT NULL                     | Last update timestamp          |
| deleted_at  | TIMESTAMP      | NULLABLE                     | Soft delete timestamp          |

**Indexes**:
- Primary key on `id`
- Foreign key on `category_id` → `categories.id` (CASCADE DELETE)
- Unique on `sku`
- Unique on `barcode`

**Relationships**:
- Belongs-to `categories`
- One-to-many with `item_units`

**Example Data**:
```sql
id | category_id | name      | sku      | price  | barcode
1  | 1           | Coffee    | BEV-001  | 5.00   | ITM000001
2  | 2           | Sandwich  | FOOD-001 | 8.50   | ITM000002
```

---

### 3. item_units
**Purpose**: Individual stock units (each row = 1 physical item in inventory)

| Column     | Type           | Constraints                  | Description                    |
|------------|----------------|------------------------------|--------------------------------|
| id         | BIGINT         | PRIMARY KEY, AUTO_INCREMENT  | Unique identifier              |
| item_id    | BIGINT         | FOREIGN KEY, NOT NULL        | Links to items.id              |
| quantity   | INTEGER        | NOT NULL, DEFAULT 1          | Always 1 (one unit)            |
| price      | DECIMAL(12,2)  | NOT NULL                     | Unit price                     |
| status     | VARCHAR(20)    | NOT NULL, DEFAULT 'available'| available/sold/damaged         |
| order_id   | BIGINT         | NULLABLE                     | If sold, links to orders.id    |
| created_at | TIMESTAMP      | NOT NULL                     | Creation timestamp             |
| updated_at | TIMESTAMP      | NOT NULL                     | Last update timestamp          |

**Indexes**:
- Primary key on `id`
- Foreign key on `item_id` → `items.id` (CASCADE DELETE)
- Index on `status` (for fast filtering)

**Status Values**:
- `available` - In stock, ready to sell
- `sold` - Sold to customer (linked to order)
- `damaged` - Damaged, not sellable

**Relationships**:
- Belongs-to `items`
- Belongs-to `orders` (when sold)

**Example Data**:
```sql
id | item_id | quantity | price | status    | order_id
1  | 1       | 1        | 5.00  | sold      | 10
2  | 1       | 1        | 5.00  | available | NULL
3  | 2       | 1        | 8.50  | available | NULL
```

**Design Note**: 
- Instead of tracking `quantity` on items, we create individual `item_units`
- This allows precise tracking of each unit's status and history
- Easier to handle damaged/returned items

---

### 4. days
**Purpose**: Business day sessions for accurate daily reporting

| Column     | Type         | Constraints                  | Description                    |
|------------|--------------|------------------------------|--------------------------------|
| id         | BIGINT       | PRIMARY KEY, AUTO_INCREMENT  | Unique identifier              |
| date       | DATE         | NOT NULL                     | Business day date              |
| status     | VARCHAR(20)  | NOT NULL, DEFAULT 'open'     | open/closed                    |
| opened_at  | TIMESTAMP    | NULLABLE                     | When day was opened            |
| closed_at  | TIMESTAMP    | NULLABLE                     | When day was closed            |
| created_at | TIMESTAMP    | NOT NULL                     | Record creation time           |
| updated_at | TIMESTAMP    | NOT NULL                     | Last update time               |

**Indexes**:
- Primary key on `id`
- Index on `status`
- Index on `date`

**Status Values**:
- `open` - Currently active business day
- `closed` - Day has been closed

**Relationships**:
- One-to-many with `orders`

**Business Rules**:
- Only one day can be "open" at a time
- Cannot close day with pending orders
- All orders must link to a day

**Example Data**:
```sql
id | date       | status | opened_at           | closed_at
1  | 2025-11-01 | closed | 2025-11-01 08:00:00 | 2025-11-01 20:00:00
2  | 2025-11-13 | open   | 2025-11-13 08:30:00 | NULL
```

---

### 5. orders
**Purpose**: Customer orders/transactions

| Column             | Type           | Constraints                  | Description                    |
|--------------------|----------------|------------------------------|--------------------------------|
| id                 | BIGINT         | PRIMARY KEY, AUTO_INCREMENT  | Unique identifier              |
| day_id             | BIGINT         | FOREIGN KEY, NULLABLE        | Links to days.id               |
| subtotal           | DECIMAL(12,2)  | NOT NULL, DEFAULT 0          | Total before discount/tax      |
| discount_percentage| DECIMAL(5,2)   | NOT NULL, DEFAULT 0          | Order-level discount %         |
| discount_amount    | DECIMAL(12,2)  | NOT NULL, DEFAULT 0          | Calculated discount amount     |
| tax_percentage     | DECIMAL(5,2)   | NOT NULL, DEFAULT 0          | Tax rate %                     |
| tax_amount         | DECIMAL(12,2)  | NOT NULL, DEFAULT 0          | Calculated tax amount          |
| total              | DECIMAL(12,2)  | NOT NULL, DEFAULT 0          | Final total                    |
| status             | VARCHAR(20)    | NOT NULL, DEFAULT 'pending'  | pending/completed/cancelled    |
| payment_method     | VARCHAR(20)    | NOT NULL, DEFAULT 'cash'     | cash/card/other                |
| customer_name      | VARCHAR(255)   | NULLABLE                     | Optional customer name         |
| customer_phone     | VARCHAR(20)    | NULLABLE                     | Optional customer phone        |
| customer_email     | VARCHAR(255)   | NULLABLE                     | Optional customer email        |
| notes              | TEXT           | NULLABLE                     | Order notes                    |
| created_at         | TIMESTAMP      | NOT NULL                     | Order creation time            |
| updated_at         | TIMESTAMP      | NOT NULL                     | Last update time               |
| deleted_at         | TIMESTAMP      | NULLABLE                     | Soft delete time               |

**Indexes**:
- Primary key on `id`
- Foreign key on `day_id` → `days.id` (CASCADE DELETE)
- Index on `status`
- Index on `created_at`

**Status Values**:
- `pending` - Order created but not finalized
- `completed` - Order finalized and paid
- `cancelled` - Order cancelled

**Relationships**:
- Belongs-to `days`
- One-to-many with `order_items`

**Calculation Formula**:
```
subtotal = Sum of all order_items.total
discount_amount = (subtotal × discount_percentage) / 100
amount_after_discount = subtotal - discount_amount
tax_amount = (amount_after_discount × tax_percentage) / 100
total = amount_after_discount + tax_amount
```

**Example Data**:
```sql
id | day_id | subtotal | discount_% | discount_amt | tax_% | tax_amt | total  | status
1  | 2      | 23.00    | 10.00      | 2.30         | 15.00 | 3.11    | 23.81  | completed
```

---

### 6. order_items
**Purpose**: Line items in an order (individual products)

| Column             | Type           | Constraints                  | Description                    |
|--------------------|----------------|------------------------------|--------------------------------|
| id                 | BIGINT         | PRIMARY KEY, AUTO_INCREMENT  | Unique identifier              |
| order_id           | BIGINT         | FOREIGN KEY, NOT NULL        | Links to orders.id             |
| item_unit_id       | BIGINT         | FOREIGN KEY, NOT NULL        | Links to item_units.id         |
| quantity           | INTEGER        | NOT NULL                     | Number of units                |
| price              | DECIMAL(12,2)  | NOT NULL                     | Unit price at time of sale     |
| discount_percentage| DECIMAL(5,2)   | NOT NULL, DEFAULT 0          | Per-item discount %            |
| discount_amount    | DECIMAL(12,2)  | NOT NULL, DEFAULT 0          | Calculated item discount       |
| total              | DECIMAL(12,2)  | NOT NULL                     | Line total                     |
| created_at         | TIMESTAMP      | NOT NULL                     | Creation time                  |
| updated_at         | TIMESTAMP      | NOT NULL                     | Last update time               |

**Indexes**:
- Primary key on `id`
- Foreign key on `order_id` → `orders.id` (CASCADE DELETE)
- Foreign key on `item_unit_id` → `item_units.id` (RESTRICT)

**Relationships**:
- Belongs-to `orders`
- Belongs-to `item_units`

**Calculation Formula**:
```
base_amount = quantity × price
discount_amount = (base_amount × discount_percentage) / 100
total = base_amount - discount_amount
```

**Example Data**:
```sql
id | order_id | item_unit_id | quantity | price | discount_% | discount_amt | total
1  | 1        | 5            | 2        | 5.00  | 0.00       | 0.00         | 10.00
2  | 1        | 12           | 1        | 8.50  | 5.00       | 0.43         | 8.07
```

---

### 7. shop_settings
**Purpose**: Store-wide configuration (single row)

| Column          | Type           | Constraints                  | Description                    |
|-----------------|----------------|------------------------------|--------------------------------|
| id              | BIGINT         | PRIMARY KEY, AUTO_INCREMENT  | Always 1                       |
| shop_name       | VARCHAR(255)   | NULLABLE                     | Store name                     |
| tax_percentage  | DECIMAL(5,2)   | NOT NULL, DEFAULT 15         | Default tax rate               |
| receipt_footer  | TEXT           | NULLABLE                     | Footer text for receipts       |
| created_at      | TIMESTAMP      | NOT NULL                     | Creation time                  |
| updated_at      | TIMESTAMP      | NOT NULL                     | Last update time               |

**Note**: This table typically contains only one row (singleton pattern)

**Example Data**:
```sql
id | shop_name | tax_percentage | receipt_footer
1  | Lumi POS  | 15.00          | Thank you for your business!
```

---

## 🔗 Relationships Summary

```
Category (1) ─────→ (N) Item
Item (1) ─────────→ (N) ItemUnit
Day (1) ──────────→ (N) Order
Order (1) ────────→ (N) OrderItem
ItemUnit (1) ─────→ (N) OrderItem
```

---

## 📐 Design Decisions

### Why Item Units?
**Problem**: Need to track individual items, not just quantities  
**Solution**: Each physical item gets its own `item_units` record  
**Benefits**:
- Track individual unit status (available/sold/damaged)
- Link specific units to orders
- Handle returns/exchanges easily
- Accurate inventory audit trail

### Why Business Days?
**Problem**: Sales reports need to align with business operations  
**Solution**: Orders linked to business day sessions  
**Benefits**:
- Accurate daily reports (even if business day spans midnight)
- Cash reconciliation per day
- Historical day-by-day analysis
- Prevents reporting confusion

### Why Soft Deletes on Orders?
**Problem**: Accidentally deleted orders lose important data  
**Solution**: Soft delete (deleted_at timestamp)  
**Benefits**:
- Orders never truly deleted
- Can restore if needed
- Audit trail maintained
- Reports remain accurate

---

## 🔍 Common Queries

### Get Available Stock for an Item
```sql
SELECT COUNT(*) 
FROM item_units 
WHERE item_id = ? 
  AND status = 'available';
```

### Get Today's Sales (Current Open Day)
```sql
SELECT SUM(total) 
FROM orders 
WHERE day_id = (
    SELECT id FROM days WHERE status = 'open' LIMIT 1
) AND status = 'completed';
```

### Get Top Selling Items
```sql
SELECT i.name, COUNT(*) as sold_count
FROM order_items oi
JOIN item_units iu ON oi.item_unit_id = iu.id
JOIN items i ON iu.item_id = i.id
GROUP BY i.id
ORDER BY sold_count DESC
LIMIT 10;
```

### Get Low Stock Items (< 10 units)
```sql
SELECT i.name, COUNT(iu.id) as stock
FROM items i
LEFT JOIN item_units iu ON i.id = iu.item_id AND iu.status = 'available'
GROUP BY i.id
HAVING stock < 10
ORDER BY stock ASC;
```

---

## 🔐 Data Integrity

### Foreign Key Constraints

**CASCADE DELETE**:
- `items.category_id` → If category deleted, items also deleted
- `item_units.item_id` → If item deleted, units also deleted
- `orders.day_id` → If day deleted, orders also deleted
- `order_items.order_id` → If order deleted, items also deleted

**RESTRICT**:
- `order_items.item_unit_id` → Cannot delete item_unit if in an order

### Unique Constraints
- `items.sku` - Prevents duplicate SKUs
- `items.barcode` - Ensures unique barcodes

### Check Constraints (Application Level)
- Quantity must be > 0
- Price must be >= 0
- Discount percentage must be 0-100
- Tax percentage must be 0-100
- Only one day can be "open"

---

## 🧹 Database Maintenance

### Regular Tasks

**Backup Database**:
```bash
# SQLite backup
copy database\database.sqlite database\backup_2025-11-13.sqlite

# Or use Laravel command:
php artisan db:backup
```

**Optimize Database**:
```bash
# SQLite optimization
sqlite3 database/database.sqlite "VACUUM;"
```

**Clear Old Soft Deletes** (optional):
```bash
# Force delete orders older than 1 year
php artisan tinker
>>> Order::onlyTrashed()->where('deleted_at', '<', now()->subYear())->forceDelete();
```

---

## 📊 Performance Considerations

### Indexes for Fast Queries
- `item_units.status` - Fast filtering by availability
- `orders.day_id` - Fast daily reports
- `orders.status` - Fast filtering by order status
- `order_items.order_id` - Fast order detail loading

### Query Optimization
- Use eager loading (`.with()`) to prevent N+1 queries
- Index frequently queried foreign keys
- Use `lockForUpdate()` for stock operations (prevent race conditions)

---

**Next**: [04-ARCHITECTURE.md](./04-ARCHITECTURE.md) - Deep dive into application architecture
