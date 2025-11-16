# 🔤 Arabic Fonts Directory

This directory contains Arabic fonts used for PDF generation.

## 📥 Recommended Fonts to Download

### 1. **Amiri** (Traditional Naskh)
- Download: https://github.com/aliftype/amiri/releases/latest
- License: SIL Open Font License
- Files needed:
  - `Amiri-Regular.ttf`
  - `Amiri-Bold.ttf`

### 2. **Cairo** (Modern Sans-Serif)
- Download: https://fonts.google.com/specimen/Cairo
- License: SIL Open Font License
- Files needed:
  - `Cairo-Regular.ttf`
  - `Cairo-Bold.ttf`

### 3. **Tajawal** (Modern Kufi)
- Download: https://fonts.google.com/specimen/Tajawal
- License: SIL Open Font License
- Files needed:
  - `Tajawal-Regular.ttf`
  - `Tajawal-Bold.ttf`

### 4. **Scheherazade New** (Traditional with Full Diacritics)
- Download: https://software.sil.org/scheherazade/download/
- License: SIL Open Font License
- Files needed:
  - `ScheherazadeNew-Regular.ttf`
  - `ScheherazadeNew-Bold.ttf`

### 5. **Noto Naskh Arabic** (Google Noto Family)
- Download: https://fonts.google.com/noto/specimen/Noto+Naskh+Arabic
- License: SIL Open Font License
- Files needed:
  - `NotoNaskhArabic-Regular.ttf`
  - `NotoNaskhArabic-Bold.ttf`

## 📋 Installation Instructions

1. **Download** the font files from the links above
2. **Extract** the `.ttf` files from the ZIP/archive
3. **Copy** the `.ttf` files to this directory (`public/fonts/`)
4. **Use** the fonts in your PDF Blade templates

## 💡 Usage Example

In your Blade template for PDF:

```html
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url('{{ public_path("fonts/Amiri-Regular.ttf") }}') format('truetype');
            font-weight: normal;
        }
        
        @font-face {
            font-family: 'Amiri';
            src: url('{{ public_path("fonts/Amiri-Bold.ttf") }}') format('truetype');
            font-weight: bold;
        }
        
        body {
            font-family: 'Amiri', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>فاتورة رقم #123</h1>
    <p>مرحباً بكم في نظام لومي</p>
</body>
</html>
```

## 🔍 Default Font

**DejaVu Sans** is built into DomPDF and supports Arabic out of the box.  
If you don't add custom fonts, the system will use DejaVu Sans automatically.

## ⚙️ Configuration

The DomPDF configuration is in `config/dompdf.php`:
- `font_dir`: Points to `storage/fonts` for cached font metrics
- `default_font`: Set to `dejavusans` for Arabic support
- `enable_font_subsetting`: Enabled to reduce PDF file size

## 📚 More Information

See the complete guide: `dev/PDF_ARABIC_COMPLETE_GUIDE.md`

---

**Note**: Font files are **NOT** included in this repository due to licensing.  
Please download them individually from the official sources above.
