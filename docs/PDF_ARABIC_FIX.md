PDF Arabic rendering and RTL - Changes and next steps

What I changed

- Added a central PDF generator service: `app/Services/PdfGenerator.php`.
  - Prefers mPDF (if installed) for complex-script/Arabic rendering.
  - Performs UTF-8 sanitization and attempts common encoding conversions.
  - Falls back to Dompdf when mPDF is not available or fails.
- Replaced direct Dompdf usage in these controllers to use the PdfGenerator:
  - `ItemController::exportPdf`
  - `CategoryController::exportPdf`
  - `ReportController::exportPdf`
  - `MarketingController::brochure` / `downloadBrochure`
  - `OrderController::invoice` (now delegates to the PdfGenerator)

Why

- mPDF has stronger out-of-the-box support for Arabic shaping and RTL layout.
- Centralizing PDF generation ensures consistent sanitization and font handling.

Important server-side recommendations (required for best results)

1. Enable the following PHP extensions for the web SAPI:
   - fileinfo (php_fileinfo) — avoids MIME detection issues and is required by some libraries
   - mbstring — improves multibyte handling
   - intl — improves locale/formatting support for some PDF engines

2. Ensure the Laravel storage symlink exists and is writable by the web server:
   - Run: `php artisan storage:link`
   - Ensure `storage/app/public` is writable and `public/storage` points to it.

Fonts

- mPDF bundles DejaVu fonts which support Arabic, so many cases will work without extra fonts.
- For Dompdf (fallback) you should place Arabic-capable TTFs in `public/fonts` and reference them in your blade templates' CSS via `@font-face`.
  - Recommended fonts: Tajawal (Google Fonts), Noto Naskh Arabic, or DejaVu Sans.
  - Example CSS in blade: `@font-face { font-family: 'Tajawal'; src: url('{{ asset('fonts/Tajawal-Regular.ttf') }}'); }
    body { font-family: 'Tajawal', 'DejaVu Sans', sans-serif; direction: rtl; }`

Testing

- Generate PDFs for invoices and reports with example Arabic text to verify shaping and RTL.
- If you still see `????` characters, check file encodings (files and DB content should be UTF-8) and check logs for mPDF errors about invalid UTF-8.

If you'd like, I can:

- Add example Arabic fonts into `public/fonts/` (I can add licensed open fonts like DejaVu or Noto if you confirm) and update invoice/report templates to reference them.
- Add a small diagnostic route that generates a sample Arabic PDF for quick verification.

Logs and debugging

- PDF generation errors are logged (mPDF failures fall back to Dompdf and are logged). Check `storage/logs/laravel.log` for `PdfGenerator` or `mPDF` warnings.

