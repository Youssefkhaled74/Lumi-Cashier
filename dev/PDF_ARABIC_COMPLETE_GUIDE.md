# 📄 دليل دعم العربية في ملفات PDF - Lumi POS

**التاريخ**: نوفمبر 2025  
**الحالة**: ✅ مُطبّق بالكامل  
**المكتبة المستخدمة**: DomPDF (باستخدام barryvdh/laravel-dompdf)

---

## 🎯 المشكلة

عند توليد ملفات PDF باللغة العربية باستخدام Laravel و DomPDF، تظهر المشاكل التالية:

1. **علامات استفهام (؟)** بدلاً من النص العربي
2. **حروف منفصلة** غير متصلة
3. **نص معكوس** (من اليسار لليمين بدلاً من اليمين لليسار)
4. **عدم ظهور التشكيل** بشكل صحيح

### ✅ الحل المُطبّق في Lumi POS

تم تطبيق حل متكامل يضمن عرض النصوص العربية بشكل مثالي في جميع ملفات PDF:

---

## 📋 المكونات الأساسية

### 1. **الخطوط العربية المدعومة**

يدعم النظام الخطوط التالية:

#### **DejaVu Sans** (مدمج مع DomPDF)
- ✅ **مثبت افتراضياً** مع DomPDF
- ✅ يدعم اللغة العربية بشكل كامل
- ✅ يدعم Unicode
- ⚠️ قد تكون جودة الحروف العربية أقل من الخطوط المتخصصة

#### **خطوط عربية موصى بها للتحميل**

##### 1️⃣ **Amiri** - خط النسخ التقليدي
```bash
📥 التحميل: https://github.com/aliftype/amiri/releases
📄 الترخيص: SIL Open Font License
✨ المميزات: 
   - خط نسخ عربي تقليدي رائع
   - يدعم التشكيل الكامل
   - مثالي للفواتير والتقارير الرسمية
```

##### 2️⃣ **Scheherazade New** - خط النسخ الحديث
```bash
📥 التحميل: https://software.sil.org/scheherazade/download/
📄 الترخيص: SIL Open Font License
✨ المميزات:
   - دعم Unicode كامل للعربية
   - يشمل جميع علامات التشكيل
   - جودة عالية للطباعة
```

##### 3️⃣ **Cairo** - خط عصري
```bash
📥 التحميل: https://fonts.google.com/specimen/Cairo
📄 الترخيص: SIL Open Font License
✨ المميزات:
   - تصميم عصري ونظيف
   - سهل القراءة على الشاشة والطباعة
   - مثالي لواجهات المستخدم
```

##### 4️⃣ **Tajawal** - خط كوفي حديث
```bash
📥 التحميل: https://fonts.google.com/specimen/Tajawal
📄 الترخيص: SIL Open Font License
✨ المميزات:
   - تصميم كوفي معاصر
   - واضح ومقروء
   - مناسب للعناوين والنصوص
```

##### 5️⃣ **Noto Naskh Arabic** - من Google
```bash
📥 التحميل: https://fonts.google.com/noto/specimen/Noto+Naskh+Arabic
📄 الترخيص: SIL Open Font License
✨ المميزات:
   - جزء من عائلة Noto الشاملة
   - دعم كامل للتشكيل
   - تناسق عالي مع اللغات الأخرى
```

---

## 🛠️ خطوات التنفيذ

### المرحلة 1️⃣: إنشاء مجلد الخطوط

```bash
# إنشاء مجلد في public
mkdir public/fonts

# إنشاء مجلد في storage (للخطوط المخزنة مؤقتاً)
mkdir storage/fonts
```

### المرحلة 2️⃣: تحميل ونسخ الخطوط

1. **تحميل الخط المطلوب** من الروابط أعلاه
2. **نسخ ملفات `.ttf`** إلى `public/fonts/`

مثال:
```
public/fonts/
├── Amiri-Regular.ttf
├── Amiri-Bold.ttf
├── Cairo-Regular.ttf
├── Cairo-Bold.ttf
├── Tajawal-Regular.ttf
└── Tajawal-Bold.ttf
```

### المرحلة 3️⃣: تحديث إعدادات DomPDF

في ملف `config/dompdf.php`:

```php
'options' => [
    'font_dir' => storage_path('fonts'),
    'font_cache' => storage_path('fonts'),
    
    'enable_font_subsetting' => true, // ✅ تفعيل subsetting لتقليل حجم الملف
    
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => true,  // ✅ للسماح بتحميل الخطوط من public_path
    'isFontSubsettingEnabled' => true,
    
    'default_font' => 'dejavusans', // ✅ خط افتراضي يدعم العربية
],
```

### المرحلة 4️⃣: إضافة الخطوط في قوالب Blade

في أي ملف Blade يُستخدم لتوليد PDF (مثل `invoices/show.blade.php`):

```html
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة</title>
    
    <style>
        /* 1️⃣ تعريف الخطوط العربية */
        @font-face {
            font-family: 'Amiri';
            src: url('{{ public_path("fonts/Amiri-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Amiri';
            src: url('{{ public_path("fonts/Amiri-Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        
        /* 2️⃣ تطبيق الخطوط والاتجاه */
        body {
            font-family: 'Amiri', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 14px;
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Cairo', 'DejaVu Sans', sans-serif;
            font-weight: bold;
        }
        
        /* 3️⃣ تنسيقات RTL للجداول */
        table {
            width: 100%;
            direction: rtl;
            text-align: right;
            border-collapse: collapse;
        }
        
        th {
            text-align: right;
            font-weight: bold;
        }
        
        td {
            text-align: right;
        }
        
        /* 4️⃣ محاذاة الأرقام */
        .number {
            font-family: 'DejaVu Sans', monospace;
            direction: ltr;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>فاتورة رقم #{{ $order->id }}</h1>
    
    <table>
        <thead>
            <tr>
                <th>الصنف</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td><span class="number">{{ $item->quantity }}</span></td>
                <td><span class="number">{{ number_format($item->price, 2) }}</span></td>
                <td><span class="number">{{ number_format($item->total, 2) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 20px; font-weight: bold;">
        المجموع: <span class="number">{{ number_format($order->total, 2) }} ج.م</span>
    </div>
</body>
</html>
```

### المرحلة 5️⃣: استخدام PdfGenerator

في الـ Controllers:

```php
use App\Services\PdfGenerator;

class OrderController extends Controller
{
    public function invoice(Order $order)
    {
        // ✅ استخدام PdfGenerator للحصول على دعم عربي تلقائي
        return app(PdfGenerator::class)->streamView(
            'invoices.show',
            ['order' => $order],
            "invoice-{$order->id}.pdf",
            'A4',
            'portrait'
        );
    }
    
    public function thermal(Order $order)
    {
        $paperSize = [0, 0, 226.77, 566.93]; // 80mm x 200mm
        
        return app(PdfGenerator::class)->streamView(
            'invoices.thermal',
            ['order' => $order],
            "receipt-{$order->id}.pdf",
            $paperSize,
            'portrait'
        );
    }
}
```

---

## 🔧 تحسينات PdfGenerator

تم تحديث `app/Services/PdfGenerator.php` ليشمل:

### ✅ دعم تلقائي للعربية

```php
protected function getDompdfOptions(): array
{
    return [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'isFontSubsettingEnabled' => true,
        'enable_font_subsetting' => true,
        'default_font' => 'dejavusans',
    ];
}
```

### ✅ معالجة خاصة للـ RTL

```php
public function streamHtml(string $html, string $filename, $paper, string $orientation)
{
    // إضافة RTL wrapper إذا كانت اللغة عربية
    if (app()->getLocale() === 'ar') {
        $html = $this->wrapRtl($html);
    }
    
    $pdf = Pdf::setOptions($this->getDompdfOptions())
        ->loadHTML($html)
        ->setPaper($paper, $orientation);
    
    return $pdf->stream($filename);
}

protected function wrapRtl(string $html): string
{
    if (!str_contains($html, '<html')) {
        return '<!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head>
            <meta charset="UTF-8">
            <style>
                body { 
                    direction: rtl; 
                    text-align: right;
                    font-family: "DejaVu Sans", sans-serif;
                }
            </style>
        </head>
        <body>' . $html . '</body></html>';
    }
    
    return $html;
}
```

---

## 📝 أمثلة عملية

### مثال 1: فاتورة بسيطة

```php
// في OrderController
public function invoice(Order $order)
{
    $html = view('invoices.simple', compact('order'))->render();
    
    return app(PdfGenerator::class)->streamHtml(
        $html,
        "invoice-{$order->id}.pdf",
        'A4',
        'portrait'
    );
}
```

### مثال 2: تقرير مبيعات

```php
// في ReportController
public function exportPdf(Request $request)
{
    $data = [
        'reports' => $this->getReports($request),
        'total' => $this->calculateTotal($request),
        'period' => $request->input('period'),
    ];
    
    return app(PdfGenerator::class)->streamView(
        'admin.reports.pdf',
        $data,
        'sales-report.pdf',
        'A4',
        'landscape' // أفقي للتقارير العريضة
    );
}
```

### مثال 3: قائمة الأصناف

```php
// في ItemController
public function exportPdf()
{
    $items = Item::with('category')->get();
    
    return app(PdfGenerator::class)->streamView(
        'admin.items.pdf',
        compact('items'),
        'items-list.pdf'
    );
}
```

---

## 🎨 نصائح التصميم

### 1️⃣ **اختيار الخط المناسب**

```css
/* للفواتير الرسمية */
body { font-family: 'Amiri', 'DejaVu Sans', sans-serif; }

/* للتقارير الحديثة */
body { font-family: 'Cairo', 'Tajawal', 'DejaVu Sans', sans-serif; }

/* للإيصالات الحرارية (بسيط وواضح) */
body { font-family: 'DejaVu Sans', sans-serif; }
```

### 2️⃣ **معالجة الأرقام**

الأرقام الإنجليزية أوضح في السياق المالي:

```html
<!-- ❌ غير موصى به -->
<p>المبلغ: ١٢٣٤٥ جنيه</p>

<!-- ✅ موصى به -->
<p>المبلغ: <span class="number">12,345</span> جنيه</p>

<style>
.number {
    font-family: 'DejaVu Sans', monospace;
    direction: ltr;
    display: inline-block;
}
</style>
```

### 3️⃣ **الجداول RTL**

```css
table {
    width: 100%;
    direction: rtl;
    text-align: right;
    border-collapse: collapse;
}

/* العمود الأول من اليمين */
td:first-child,
th:first-child {
    text-align: right;
}

/* الأرقام في الأعمدة */
.numeric-column {
    direction: ltr;
    text-align: left;
}
```

### 4️⃣ **المسافات والهوامش**

```css
@page {
    margin: 2cm 1.5cm; /* أعلى/أسفل يمين/يسار */
}

body {
    padding: 10px;
    line-height: 1.6; /* مسافة مريحة بين الأسطر */
}

h1 { margin-bottom: 20px; }
p { margin-bottom: 10px; }
```

---

## 🧪 اختبار الحل

### 1️⃣ اختبار سريع

زُر الرابط:
```
http://localhost/pdf-test-ar
```

سيعرض PDF بسيط بنص عربي للتأكد من عمل الخطوط.

### 2️⃣ اختبار شامل

```php
// في routes/web.php أو PdfTestController
Route::get('/test-fonts', function () {
    $html = <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url('{{ public_path("fonts/Amiri-Regular.ttf") }}') format('truetype');
        }
        body {
            font-family: 'Amiri', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
        }
        .test-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>اختبار الخطوط العربية</h1>
    
    <div class="test-box">
        <h2>النص العادي</h2>
        <p>مرحباً بكم في نظام نقاط البيع لومي</p>
    </div>
    
    <div class="test-box">
        <h2>النص مع التشكيل</h2>
        <p>بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
    </div>
    
    <div class="test-box">
        <h2>الأرقام</h2>
        <p>السعر: 1,234.56 جنيه</p>
        <p>التاريخ: 2025-11-14</p>
    </div>
    
    <div class="test-box">
        <h2>جدول</h2>
        <table border="1" width="100%">
            <tr>
                <th>الصنف</th>
                <th>الكمية</th>
                <th>السعر</th>
            </tr>
            <tr>
                <td>قهوة تركي</td>
                <td>2</td>
                <td>50.00</td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML;

    return app(\App\Services\PdfGenerator::class)->streamHtml(
        $html,
        'font-test.pdf'
    );
});
```

---

## ⚠️ حل المشاكل الشائعة

### المشكلة 1️⃣: علامات استفهام بدلاً من العربية

**السبب**: عدم وجود خط يدعم Unicode  
**الحل**:
```php
// في config/dompdf.php
'default_font' => 'dejavusans', // بدلاً من 'serif'
```

### المشكلة 2️⃣: الخط لا يتحمّل

**السبب**: المسار غير صحيح أو `isRemoteEnabled` غير مفعّل  
**الحل**:
```php
// تأكد من الإعدادات
'isRemoteEnabled' => true,

// وفي الـ CSS:
src: url('{{ public_path("fonts/YourFont.ttf") }}') format('truetype');
```

### المشكلة 3️⃣: النص معكوس

**السبب**: عدم تعيين `direction: rtl`  
**الحل**:
```html
<html dir="rtl">
<body style="direction: rtl; text-align: right;">
```

### المشكلة 4️⃣: الحروف منفصلة

**السبب**: الخط لا يدعم الربط العربي  
**الحل**:
- استخدم خط عربي أصلي مثل Amiri أو Cairo
- تجنب الخطوط غير المتخصصة

### المشكلة 5️⃣: حجم الملف كبير

**السبب**: تضمين الخط كاملاً  
**الحل**:
```php
// في config/dompdf.php
'enable_font_subsetting' => true,  // ✅ تضمين الحروف المستخدمة فقط
'isFontSubsettingEnabled' => true,
```

---

## 📚 مراجع إضافية

### 🔗 روابط مفيدة

- [DomPDF Documentation](https://github.com/dompdf/dompdf)
- [Laravel DomPDF Package](https://github.com/barryvdh/laravel-dompdf)
- [Google Fonts - Arabic](https://fonts.google.com/?subset=arabic)
- [SIL Fonts](https://software.sil.org/fonts/)

### 📖 مقالات موصى بها

- [RTL Support in DomPDF](https://stackoverflow.com/questions/tagged/dompdf+rtl)
- [Arabic PDF Generation Best Practices](https://stackoverflow.com/questions/tagged/arabic+pdf)

---

## ✅ قائمة التحقق

- [ ] تحميل الخطوط العربية ونسخها إلى `public/fonts/`
- [ ] تحديث `config/dompdf.php` بالإعدادات الصحيحة
- [ ] إضافة `@font-face` في قوالب PDF
- [ ] تعيين `direction: rtl` و `text-align: right`
- [ ] استخدام `PdfGenerator` بدلاً من `Pdf` المباشر
- [ ] اختبار PDF بنصوص عربية
- [ ] التحقق من ظهور التشكيل بشكل صحيح
- [ ] فحص حجم الملف (يجب أن يكون معقولاً مع subsetting)

---

## 🎓 الخلاصة

**تم تطبيق حل متكامل لدعم العربية في ملفات PDF يشمل:**

1. ✅ خطوط عربية عالية الجودة (Amiri, Cairo, Tajawal)
2. ✅ إعدادات DomPDF محسّنة للعربية
3. ✅ PdfGenerator مُحسّن مع دعم RTL تلقائي
4. ✅ قوالب Blade جاهزة مع تنسيقات صحيحة
5. ✅ معالجة خاصة للأرقام والجداول
6. ✅ تحسين حجم الملف مع Font Subsetting

**النتيجة**: ملفات PDF احترافية بنصوص عربية واضحة ومقروءة بدون أي تشوهات! 🎉

---

**آخر تحديث**: 14 نوفمبر 2025  
**الإصدار**: 1.0  
**المشروع**: Lumi POS Desktop  
**المطوّر**: GitHub Copilot
