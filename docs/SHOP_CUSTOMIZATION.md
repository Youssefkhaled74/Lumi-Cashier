# Shop Customization Feature Documentation

## 📋 نظرة عامة

تم إضافة نظام كامل لتخصيص معلومات المحل في نظام Lumi POS. يسمح هذا النظام للعميل بتخصيص:
- اسم المحل (بالعربية والإنجليزية)
- شعار المحل (Logo)
- رقم الهاتف
- العنوان (بالعربية والإنجليزية)

**🔒 حماية الإعدادات:** النظام محمي بكلمة مرور: `lumi#8080`

---

## 🗂️ الملفات المضافة

### 1. Database Migration
**File:** `database/migrations/2025_10_31_000001_create_shop_settings_table.php`

```php
Schema::create('shop_settings', function (Blueprint $table) {
    $table->id();
    $table->string('shop_name')->default('نظام لومي POS');
    $table->string('shop_name_en')->default('Lumi POS System');
    $table->string('logo_path')->nullable();
    $table->string('phone')->nullable();
    $table->string('address')->nullable();
    $table->string('address_en')->nullable();
    $table->timestamps();
});
```

**القيم الافتراضية:**
- اسم المحل بالعربية: "نظام لومي POS"
- اسم المحل بالإنجليزية: "Lumi POS System"

---

### 2. Model
**File:** `app/Models/ShopSettings.php`

**Features:**
- `current()`: Static method للحصول على الإعدادات الحالية
- `getLogoUrlAttribute()`: Accessor للحصول على رابط اللوجو الكامل
- `getShopNameLocalizedAttribute()`: اسم المحل حسب اللغة الحالية
- `getAddressLocalizedAttribute()`: العنوان حسب اللغة الحالية

**Usage Example:**
```php
$settings = ShopSettings::current();
echo $settings->shop_name_localized; // يطبع الاسم بناءً على اللغة
echo $settings->logo_url; // رابط اللوجو
```

---

### 3. Controller
**File:** `app/Http/Controllers/ShopSettingsController.php`

**Password:** `lumi#8080` (مخزنة في الـ constant: `ADMIN_PASSWORD`)

**Methods:**
1. `index()` - عرض صفحة الإعدادات
2. `verifyPassword(Request)` - التحقق من كلمة المرور
3. `update(Request)` - حفظ التغييرات
4. `deleteLogo()` - حذف اللوجو

**Password Verification Flow:**
```
User enters password → verifyPassword() → 
If correct: session('shop_settings_unlocked', true) → 
Show settings form
```

---

### 4. View
**File:** `resources/views/settings/shop.blade.php`

**Features:**
- نموذج كلمة المرور (مع Alpine.js)
- نموذج التعديل (مخفي حتى التحقق من الباسورد)
- رفع اللوجو مع معاينة
- حذف اللوجو
- حفظ المعلومات

**Alpine.js Component:**
```javascript
function shopSettings() {
    return {
        isUnlocked: false,
        password: '',
        isLoading: false,
        errorMessage: '',
        logoPreview: null,
        
        verifyPassword() { ... },
        previewLogo(event) { ... },
        deleteLogo() { ... }
    }
}
```

---

### 5. Routes
**File:** `routes/web.php`

```php
// Shop Settings (Protected)
Route::get('settings/shop', [ShopSettingsController::class, 'index'])
    ->name('settings.shop.index');
    
Route::post('settings/shop/verify', [ShopSettingsController::class, 'verifyPassword'])
    ->name('settings.shop.verify');
    
Route::put('settings/shop', [ShopSettingsController::class, 'update'])
    ->name('settings.shop.update');
    
Route::delete('settings/shop/logo', [ShopSettingsController::class, 'deleteLogo'])
    ->name('settings.shop.deleteLogo');
```

---

## 🎨 التكامل في النظام

### 1. Navbar (layouts/admin.blade.php)

**التعديلات:**
```php
@php
    $shopSettings = \App\Models\ShopSettings::current();
@endphp

<!-- في الـ navbar -->
@if($shopSettings->logo_url)
    <img src="{{ $shopSettings->logo_url }}" alt="Logo">
@else
    <i class="bi bi-cart-check-fill"></i>
@endif

<h1>{{ $shopSettings->shop_name_localized }}</h1>
```

**النتيجة:**
- اللوجو المخصص يظهر بدلاً من الأيقونة
- اسم المحل المخصص يظهر بدلاً من "كاشير برو"

---

### 2. Reports PDF (admin/reports/pdf.blade.php)

**التعديلات:**
```php
@php
    $shopSettings = \App\Models\ShopSettings::current();
@endphp

<!-- في الـ header -->
<div class="header-top">
    @if($shopSettings->logo_url)
    <div class="header-logo">
        <img src="{{ public_path($shopSettings->logo_path) }}" alt="Logo">
    </div>
    @endif
    <div class="header-title">
        <h1>{{ $shopSettings->shop_name_localized }}</h1>
        <p>📊 Sales & Inventory Report</p>
    </div>
</div>

<!-- في الـ footer -->
<strong>{{ $shopSettings->shop_name_localized }}</strong> - Point of Sale System
```

**النتيجة:**
- التقارير PDF تحمل اللوجو واسم المحل المخصص

---

### 3. Receipt/Invoice (invoices/show.blade.php)

**التعديلات:**
```php
@php
    $shopSettings = \App\Models\ShopSettings::current();
@endphp

<!-- في الـ header -->
@if($shopSettings->logo_url)
<img src="{{ $shopSettings->logo_url }}" alt="Logo" class="logo-img">
@endif

<div class="store-name">{{ $shopSettings->shop_name_localized }}</div>

<div class="store-info">
@if($shopSettings->address_localized)
{{ $shopSettings->address_localized }}<br>
@endif
@if($shopSettings->phone)
Tel: {{ $shopSettings->phone }}<br>
@endif
</div>
```

**CSS Additions:**
```css
.logo-img { 
    max-width: 50mm; 
    max-height: 20mm; 
    margin: 0 auto 5px; 
    display: block; 
}
```

**النتيجة:**
- الفاتورة المطبوعة تحمل اللوجو والمعلومات المخصصة
- حجم اللوجو محدد ليناسب الفواتير الحرارية (80-90mm)

---

## 🌍 Translations

### Arabic (lang/ar/messages.php)
```php
'nav' => [
    'shop_settings' => 'إعدادات المحل',
],

'shop_settings' => 'إعدادات المحل',
'customize_shop_info' => 'تخصيص معلومات المحل والشعار',
'protected_settings' => 'إعدادات محمية',
'enter_password_to_access' => 'أدخل كلمة المرور للوصول إلى الإعدادات',
'password' => 'كلمة المرور',
'unlock' => 'فتح',
// ... المزيد
```

### English (lang/en/messages.php)
```php
'nav' => [
    'shop_settings' => 'Shop Settings',
],

'shop_settings' => 'Shop Settings',
'customize_shop_info' => 'Customize shop information and logo',
'protected_settings' => 'Protected Settings',
'enter_password_to_access' => 'Enter password to access settings',
// ... more
```

---

## 🚀 كيفية الاستخدام

### للمستخدم النهائي (العميل):

1. **الذهاب إلى الإعدادات:**
   - من القائمة الجانبية ← "الإعدادات" ← "إعدادات المحل"
   - أو مباشرة: `/admin/settings/shop`

2. **إدخال كلمة المرور:**
   - كلمة المرور: `lumi#8080`
   - اضغط "فتح"

3. **تعديل المعلومات:**
   - اسم المحل بالعربية (مطلوب)
   - اسم المحل بالإنجليزية (مطلوب)
   - رقم الهاتف (اختياري)
   - العنوان بالعربية (اختياري)
   - العنوان بالإنجليزية (اختياري)

4. **رفع اللوجو:**
   - اضغط "Choose File"
   - اختر صورة (JPG, PNG, GIF, SVG)
   - الحد الأقصى: 2MB
   - سيظهر معاينة للصورة

5. **حفظ التغييرات:**
   - اضغط "حفظ التغييرات"
   - ستظهر رسالة نجاح
   - الإعدادات تُقفل تلقائياً بعد الحفظ

---

### للمطور:

#### تغيير كلمة المرور:

في ملف `app/Http/Controllers/ShopSettingsController.php`:

```php
private const ADMIN_PASSWORD = 'lumi#8080'; // غيّر هنا
```

#### الوصول للإعدادات في أي مكان:

```php
// في Controller
$settings = ShopSettings::current();
$shopName = $settings->shop_name_localized;
$logo = $settings->logo_url;

// في Blade
@php
    $shopSettings = \App\Models\ShopSettings::current();
@endphp

{{ $shopSettings->shop_name }}
{{ $shopSettings->logo_url }}
```

#### Accessors المتاحة:

```php
$settings->logo_url              // رابط اللوجو الكامل
$settings->shop_name_localized   // اسم المحل حسب اللغة
$settings->address_localized     // العنوان حسب اللغة
```

---

## 📁 Storage Structure

```
storage/
└── app/
    └── public/
        └── logos/
            └── {timestamp}_{filename}.{ext}
```

**Public Access:**
```
public/storage/logos/{timestamp}_{filename}.{ext}
```

**Note:** تم عمل symbolic link من `public/storage` إلى `storage/app/public` باستخدام:
```bash
php artisan storage:link
```

---

## ✅ Validation Rules

```php
'shop_name' => 'required|string|max:255',
'shop_name_en' => 'required|string|max:255',
'phone' => 'nullable|string|max:50',
'address' => 'nullable|string|max:500',
'address_en' => 'nullable|string|max:500',
'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
```

---

## 🔐 Security Features

1. **Password Protection:** كل محاولة تعديل محمية بكلمة مرور
2. **Session Lock:** الإعدادات مقفولة بعد الحفظ تلقائياً
3. **File Validation:** التحقق من نوع وحجم الصورة
4. **CSRF Protection:** Laravel's built-in CSRF protection

---

## 🎯 Use Cases

### Case 1: عميل يشتري النظام
```
1. يفتح الإعدادات
2. يدخل الباسورد: lumi#8080
3. يضع اسم محله "سوبر ماركت الحرمين"
4. يرفع لوجو محله
5. يحفظ
→ النظام الآن معنون باسم محله في كل مكان
```

### Case 2: تغيير اللوجو فقط
```
1. يفتح الإعدادات
2. يدخل الباسورد
3. يضغط "حذف الشعار" للوجو القديم
4. يرفع لوجو جديد
5. يحفظ
```

### Case 3: عرض اسم المحل في التقارير
```php
// في أي تقرير PDF
$shopSettings = ShopSettings::current();
echo $shopSettings->shop_name_localized;
// Output: "سوبر ماركت الحرمين" (بناءً على اللغة)
```

---

## 🐛 Troubleshooting

### Problem: الصور لا تظهر
**Solution:**
```bash
php artisan storage:link
```

### Problem: كلمة المرور لا تعمل
**Check:**
```php
// في ShopSettingsController.php
private const ADMIN_PASSWORD = 'lumi#8080';
```

### Problem: Session expired
**Solution:**
- الـ session تنتهي بعد الحفظ تلقائياً (by design)
- أعد إدخال كلمة المرور

### Problem: Logo too large
**Solution:**
- الحد الأقصى: 2MB
- قلل حجم الصورة أو استخدم أداة ضغط

---

## 📊 Database Schema

```sql
CREATE TABLE `shop_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(255) DEFAULT 'نظام لومي POS',
  `shop_name_en` varchar(255) DEFAULT 'Lumi POS System',
  `logo_path` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

---

## 🎨 UI/UX Details

### Password Screen
- قفل أيقونة كبيرة
- رسالة واضحة
- حقل باسورد
- زر "فتح" مع loading state

### Settings Form
- تقسيم واضح للأقسام
- أيقونات تعبيرية
- Validation في الوقت الفعلي
- معاينة اللوجو قبل الحفظ

### Success/Error Messages
- رسائل واضحة بالعربية والإنجليزية
- ألوان مميزة
- Auto-dismiss بعد 5 ثواني

---

## 📝 Notes

1. **Single Record:** الجدول يحتوي على صف واحد فقط (singleton pattern)
2. **Logo Formats:** JPG, PNG, GIF, SVG مدعومة
3. **RTL Support:** النظام يدعم العربية والإنجليزية بشكل كامل
4. **Automatic Lock:** الإعدادات تُقفل تلقائياً بعد الحفظ للأمان
5. **Logo in PDF:** اللوجو يستخدم `public_path()` في PDF لضمان ظهوره

---

## ✨ Future Enhancements (اقتراحات)

1. إضافة ألوان مخصصة للنظام
2. رفع صور إضافية (favicon, email header)
3. إضافة social media links
4. Custom email templates
5. Multi-branch support

---

**Created:** 2025-10-31  
**Version:** 1.0.0  
**Developer:** Copilot  
**Password:** lumi#8080 🔒
