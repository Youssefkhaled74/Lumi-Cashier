# 💰 Discount & Tax System Implementation

## Overview
A comprehensive discount and tax management system with admin verification for high discounts (>5%).

---

## ✨ Features Implemented

### 1. **Discount System**
- ✅ **Per-Item Discounts** - Apply discounts to individual items
- ✅ **Order-Level Discounts** - Apply discounts to entire order
- ✅ **Percentage-Based** - Discounts as percentage (0-100%)
- ✅ **Admin Verification** - Discounts >5% require admin credentials
- ✅ **Automatic Calculation** - Discount amounts calculated automatically

### 2. **Tax System**
- ✅ **Configurable Tax Rate** - Default 15% VAT (customizable)
- ✅ **Applied After Discount** - Tax calculated on discounted amount
- ✅ **Multiple Tax Rates** - Support for different tax percentages per order
- ✅ **Automatic Calculation** - Tax amounts calculated automatically

### 3. **Payment Methods**
- ✅ Cash
- ✅ Card
- ✅ Other

### 4. **Customer Information** (Optional)
- ✅ Customer Name
- ✅ Customer Phone
- ✅ Customer Email

---

## 🗄️ Database Changes

### New Fields in `orders` Table:
```sql
- subtotal (decimal 12,2)           -- Total before discount & tax
- discount_percentage (decimal 5,2) -- Order discount %
- discount_amount (decimal 12,2)    -- Calculated discount amount
- tax_percentage (decimal 5,2)      -- Tax rate %
- tax_amount (decimal 12,2)         -- Calculated tax amount
- payment_method (string)           -- cash/card/other
- customer_name (string, nullable)
- customer_phone (string, nullable)
- customer_email (string, nullable)
```

### New Fields in `order_items` Table:
```sql
- discount_percentage (decimal 5,2) -- Item-level discount %
- discount_amount (decimal 12,2)    -- Calculated item discount
```

---

## 📊 Calculation Flow

### Order Total Calculation:
```
1. Subtotal = Sum of all order items
2. Discount Amount = (Subtotal × Discount %) / 100
3. Amount After Discount = Subtotal - Discount Amount
4. Tax Amount = (Amount After Discount × Tax %) / 100
5. Final Total = Amount After Discount + Tax Amount
```

### Item Total Calculation:
```
1. Base Amount = Quantity × Price
2. Item Discount = (Base Amount × Item Discount %) / 100
3. Item Total = Base Amount - Item Discount
```

---

## 🔐 Admin Verification for High Discounts

### Rules:
- **≤ 5%**: Approved automatically ✅
- **> 5%**: Requires admin credentials ⚠️

### How It Works:
1. User attempts to apply discount > 5%
2. System prompts for admin email & password
3. Credentials verified against `config/cashier.php`
4. If valid, discount is approved
5. If invalid, discount is rejected

### API Endpoint:
```php
POST /admin/orders/verify-discount

Request:
{
    "discount_percentage": 10,
    "admin_email": "admin@cashier.com",
    "admin_password": "secret123"
}

Response (Success):
{
    "success": true,
    "message": "Discount approved by administrator",
    "discount_percentage": 10
}

Response (Failure):
{
    "success": false,
    "message": "Invalid admin credentials"
}
```

---

## ⚙️ Configuration

### File: `config/cashier.php`

```php
// Tax configuration
'tax' => [
    'enabled' => env('TAX_ENABLED', true),
    'default_rate' => env('TAX_DEFAULT_RATE', 15), // Default 15% VAT
    'label' => env('TAX_LABEL', 'VAT'),
],

// Discount configuration
'discount' => [
    'max_without_approval' => 5, // Maximum discount % without admin approval
    'enabled' => true,
],
```

### Environment Variables (`.env`):
```env
TAX_ENABLED=true
TAX_DEFAULT_RATE=15
TAX_LABEL=VAT
```

---

## 🌍 Translations

### English (`resources/lang/en/pos.php`)
```php
'subtotal' => 'Subtotal'
'discount' => 'Discount'
'discount_percentage' => 'Discount (%)'
'discount_amount' => 'Discount Amount'
'apply_discount' => 'Apply Discount'
'remove_discount' => 'Remove Discount'
'discount_requires_admin' => 'Discounts above 5% require admin approval'
'enter_discount' => 'Enter discount percentage'
'tax' => 'Tax'
'tax_percentage' => 'Tax (%)'
'tax_amount' => 'Tax Amount'
'apply_tax' => 'Apply Tax'
'grand_total' => 'Grand Total'
'payment_method' => 'Payment Method'
'customer_name' => 'Customer Name'
'customer_phone' => 'Phone Number'
'customer_email' => 'Email Address'
```

### Arabic (`resources/lang/ar/pos.php`)
```php
'subtotal' => 'المجموع الفرعي'
'discount' => 'الخصم'
'discount_percentage' => 'نسبة الخصم (%)'
'discount_amount' => 'مبلغ الخصم'
'apply_discount' => 'تطبيق الخصم'
'remove_discount' => 'إزالة الخصم'
'discount_requires_admin' => 'الخصومات أكثر من 5٪ تتطلب موافقة المسؤول'
'enter_discount' => 'أدخل نسبة الخصم'
'tax' => 'الضريبة'
'tax_percentage' => 'نسبة الضريبة (%)'
'tax_amount' => 'مبلغ الضريبة'
'apply_tax' => 'تطبيق الضريبة'
'grand_total' => 'الإجمالي النهائي'
'payment_method' => 'طريقة الدفع'
'customer_name' => 'اسم العميل'
'customer_phone' => 'رقم الهاتف'
'customer_email' => 'البريد الإلكتروني'
```

---

## 💻 Model Methods

### Order Model

```php
// Apply discount to order
$order->applyDiscount(10); // 10% discount

// Apply tax to order
$order->applyTax(15); // 15% tax

// Set payment method
$order->setPaymentMethod('card');

// Recalculate totals
$order->calculateTotal();
```

### OrderItem Model

```php
// Discount is automatically calculated on save
$orderItem->discount_percentage = 5;
$orderItem->save(); // discount_amount and total auto-calculated
```

---

## 📝 Usage Example

### Creating an Order with Discount & Tax:

```php
$orderData = [
    'items' => [
        [
            'item_id' => 1,
            'quantity' => 2,
            'discount_percentage' => 5, // 5% item discount
        ],
        [
            'item_id' => 2,
            'quantity' => 1,
            'discount_percentage' => 0, // No discount
        ]
    ],
    'discount_percentage' => 10, // 10% order discount (requires admin)
    'tax_percentage' => 15,      // 15% VAT
    'payment_method' => 'card',
    'customer_name' => 'John Doe',
    'customer_phone' => '+1234567890',
    'customer_email' => 'john@example.com',
    'notes' => 'Priority delivery'
];

$order = $orderService->createOrder($orderData);
```

---

## 🔍 Files Modified/Created

### Created:
1. ✅ `database/migrations/2025_10_30_000001_add_discount_and_tax_to_orders_table.php`
2. ✅ `database/migrations/2025_10_30_000002_add_discount_to_order_items_table.php`
3. ✅ `DISCOUNT_TAX_SYSTEM.md` (this file)

### Modified:
1. ✅ `app/Models/Order.php` - Added discount/tax fields and calculation methods
2. ✅ `app/Models/OrderItem.php` - Added discount fields and auto-calculation
3. ✅ `app/Http/Controllers/OrderController.php` - Added admin verification method
4. ✅ `app/Http/Requests/StoreOrderRequest.php` - Added validation rules
5. ✅ `app/Services/OrderService.php` - Updated order creation logic
6. ✅ `routes/web.php` - Added discount verification route
7. ✅ `config/cashier.php` - Added tax and discount configuration
8. ✅ `resources/lang/en/pos.php` - Added English translations
9. ✅ `resources/lang/ar/pos.php` - Added Arabic translations

---

## 🎯 Next Steps (UI Implementation Recommended)

To complete this feature, you should update the POS/Order creation view to include:

1. **Discount Input Section**
   - Input field for discount percentage
   - Admin credentials modal (shown when >5%)
   - Real-time discount calculation display

2. **Tax Selection**
   - Dropdown or input for tax percentage
   - Default to configured rate
   - Show tax amount calculation

3. **Payment Method Selection**
   - Radio buttons or dropdown
   - Cash, Card, Other options

4. **Customer Information** (Optional)
   - Expandable section
   - Name, Phone, Email fields

5. **Order Summary Display**
   - Subtotal
   - Discount (with percentage)
   - Tax (with percentage)
   - **Grand Total** (prominent display)

6. **JavaScript for Real-Time Calculations**
   - Update totals as discount/tax changes
   - AJAX call to verify admin credentials
   - Visual feedback for approved/rejected discounts

---

## 🚀 Testing

### Test Scenarios:

1. **Low Discount (≤5%)**
   ```
   Apply 3% discount → Should work without admin verification ✅
   ```

2. **High Discount (>5%)**
   ```
   Apply 10% discount → Requires admin credentials
   Enter correct credentials → Discount approved ✅
   Enter wrong credentials → Discount rejected ❌
   ```

3. **Tax Calculation**
   ```
   Subtotal: $100
   Discount 10%: -$10
   After Discount: $90
   Tax 15%: +$13.50
   Total: $103.50 ✅
   ```

4. **Item Discounts**
   ```
   Item 1: $50 with 5% discount = $47.50
   Item 2: $30 with 0% discount = $30.00
   Subtotal: $77.50
   Order Discount 10%: -$7.75
   After Discount: $69.75
   Tax 15%: +$10.46
   Total: $80.21 ✅
   ```

---

## 🎉 Features Complete!

✅ **Discount System** - Per-item and order-level discounts  
✅ **Tax System** - Configurable VAT/Sales Tax  
✅ **Admin Verification** - For discounts >5%  
✅ **Payment Methods** - Cash, Card, Other  
✅ **Customer Info** - Optional customer details  
✅ **Bilingual Support** - English & Arabic  
✅ **Auto Calculations** - All amounts calculated automatically  
✅ **Database Migrations** - Schema updated successfully  

---

**Status**: Backend Complete ✅  
**Next**: Update POS UI to utilize these features
