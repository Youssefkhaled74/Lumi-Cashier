<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as DomPdfFacade;
use Illuminate\Support\Facades\Log;

class PdfGenerator
{
    /**
     * Stream a view as PDF to the browser. Prefers mPDF for Arabic/RTL.
     */
    public function streamView(string $view, array $data = [], string $filename = 'document.pdf', $paper = 'A4', string $orientation = 'portrait')
    {
        $html = view($view, $data)->render();
        return $this->streamHtml($html, $filename, $paper, $orientation);
    }

    /**
     * Download a view as PDF.
     */
    public function downloadView(string $view, array $data = [], string $filename = 'document.pdf', $paper = 'A4', string $orientation = 'portrait')
    {
        $html = view($view, $data)->render();
        return $this->downloadHtml($html, $filename, $paper, $orientation);
    }

    /**
     * Stream raw HTML as PDF.
     */
    public function streamHtml(string $html, string $filename = 'document.pdf', $paper = 'A4', string $orientation = 'portrait')
    {
        // Sanitize HTML/ensure UTF-8 before handing to mPDF
        $html = $this->sanitizeHtml($html);
        
        // Add RTL wrapper if Arabic locale and HTML doesn't have proper RTL setup
        if (app()->getLocale() === 'ar' && !$this->hasRtlSetup($html)) {
            $html = $this->wrapWithRtl($html);
        }

        // Prefer TCPDF for Arabic content if available (good RTL & shaping support)
        if ((app()->getLocale() === 'ar' || $this->containsArabic($html)) && class_exists(\TCPDF::class)) {
            try {
                $tcpdf = $this->createTcpdfInstance($paper, $orientation);
                // Disable default header/footer
                $tcpdf->setPrintHeader(false);
                $tcpdf->setPrintFooter(false);

                // Register a Unicode TTF font (Tajawal or fallback to DejaVu Sans)
                $font = 'dejavusans';
                $tajawalPath = public_path('fonts/Tajawal-Regular.ttf');
                if (file_exists($tajawalPath) && class_exists(\TCPDF_FONTS::class)) {
                    try {
                        // addTTFfont returns internal font name when successful
                        $added = \TCPDF_FONTS::addTTFfont($tajawalPath, 'TrueTypeUnicode', '', 32);
                        if ($added) {
                            $font = $added;
                        }
                    } catch (\Throwable $e) {
                        // ignore font registration failures, fallback to built-in
                    }
                }

                $tcpdf->setRTL(true);
                $tcpdf->SetAutoPageBreak(true, 0);
                $tcpdf->AddPage($orientation === 'portrait' ? 'P' : 'L');
                $tcpdf->SetFont($font, '', 12);

                // Write HTML and output as string
                $tcpdf->writeHTML($html, true, false, true, false, '');
                $pdfOutput = $tcpdf->Output('', 'S');

                return response($pdfOutput, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                ]);
            } catch (\Throwable $e) {
                Log::warning('PdfGenerator::streamHtml - TCPDF failed, falling back to mPDF/Dompdf', ['error' => $e->getMessage()]);
            }
        }

        // Prefer TCPDF for Arabic content if available
        if ((app()->getLocale() === 'ar' || $this->containsArabic($html)) && class_exists(\TCPDF::class)) {
            try {
                $tcpdf = $this->createTcpdfInstance($paper, $orientation);
                $tcpdf->setPrintHeader(false);
                $tcpdf->setPrintFooter(false);

                $font = 'dejavusans';
                $tajawalPath = public_path('fonts/Tajawal-Regular.ttf');
                if (file_exists($tajawalPath) && class_exists(\TCPDF_FONTS::class)) {
                    try {
                        $added = \TCPDF_FONTS::addTTFfont($tajawalPath, 'TrueTypeUnicode', '', 32);
                        if ($added) {
                            $font = $added;
                        }
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                $tcpdf->setRTL(true);
                $tcpdf->SetAutoPageBreak(true, 0);
                $tcpdf->AddPage($orientation === 'portrait' ? 'P' : 'L');
                $tcpdf->SetFont($font, '', 12);

                $tcpdf->writeHTML($html, true, false, true, false, '');
                $pdfContent = $tcpdf->Output('', 'S');
                return response($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } catch (\Throwable $e) {
                Log::warning('PdfGenerator::downloadHtml - TCPDF failed, falling back to mPDF/Dompdf', ['error' => $e->getMessage()]);
            }
        }

        if (class_exists(\Mpdf\Mpdf::class)) {
            try {
                $mpdf = $this->createMpdfInstance($paper);
                // auto-detect script/lang/font for complex scripts
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;

                if (app()->getLocale() === 'ar') {
                    // ensure RTL rendering
                    $mpdf->SetDirectionality('rtl');
                }

                $mpdf->WriteHTML($html);
                $pdfOutput = $mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN);

                return response($pdfOutput, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                ]);
            } catch (\Throwable $e) {
                Log::warning('PdfGenerator::streamHtml - mPDF failed, falling back to Dompdf', [
                    'error' => $e->getMessage(),
                    'paper' => $paper,
                    'utf8' => mb_check_encoding($html, 'UTF-8'),
                    'html_preview' => mb_substr($html, 0, 500),
                    'exception' => $e->getTraceAsString(),
                ]);
            }
        }

        // Dompdf fallback
        try {
            $pdf = DomPdfFacade::setOptions($this->getDompdfOptions())
                ->loadHTML($html);

            if (is_array($paper)) {
                // Normalize array keys to avoid undefined index when associative arrays are passed
                $vals = array_values($paper);
                // If the paper was given as [left, top, right, bottom] (points), convert to [width, height] in points
                if (count($vals) === 4) {
                    $left = (float) $vals[0];
                    $top = (float) $vals[1];
                    $right = (float) $vals[2];
                    $bottom = (float) $vals[3];
                    $widthPoints = $right - $left;
                    $heightPoints = $bottom - $top;
                    $pdf->setPaper([$widthPoints, $heightPoints], $orientation);
                } else {
                    // For 2-value arrays (width, height) or other shapes, pass through
                    $pdf->setPaper($vals, $orientation);
                }
            } else {
                $pdf->setPaper($paper, $orientation);
            }

            return $pdf->stream($filename);
        } catch (\Throwable $e) {
            Log::error('PdfGenerator::streamHtml - Dompdf also failed', [
                'error' => $e->getMessage(),
                'paper' => $paper,
                'utf8' => mb_check_encoding($html, 'UTF-8'),
                'html_preview' => mb_substr($html, 0, 500),
                'exception' => $e->getTraceAsString(),
            ]);
            abort(500, 'Failed to generate PDF');
        }
    }

    /**
     * Download raw HTML as PDF.
     */
    public function downloadHtml(string $html, string $filename = 'document.pdf', $paper = 'A4', string $orientation = 'portrait')
    {
        $html = $this->sanitizeHtml($html);
        
        // Add RTL wrapper if Arabic locale and HTML doesn't have proper RTL setup
        if (app()->getLocale() === 'ar' && !$this->hasRtlSetup($html)) {
            $html = $this->wrapWithRtl($html);
        }

        if (class_exists(\Mpdf\Mpdf::class)) {
            try {
                $mpdf = $this->createMpdfInstance($paper);
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;
                if (app()->getLocale() === 'ar') {
                    $mpdf->SetDirectionality('rtl');
                }

                $mpdf->WriteHTML($html);
                $pdfContent = $mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN);
                return response($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } catch (\Throwable $e) {
                Log::warning('PdfGenerator::downloadHtml - mPDF failed, falling back to Dompdf', [
                    'error' => $e->getMessage(),
                    'paper' => $paper,
                    'utf8' => mb_check_encoding($html, 'UTF-8'),
                    'html_preview' => mb_substr($html, 0, 500),
                    'exception' => $e->getTraceAsString(),
                ]);
            }
        }

        try {
            $pdf = DomPdfFacade::setOptions($this->getDompdfOptions())
                ->loadHTML($html);

            if (is_array($paper)) {
                $vals = array_values($paper);
                if (count($vals) === 4) {
                    $left = (float) $vals[0];
                    $top = (float) $vals[1];
                    $right = (float) $vals[2];
                    $bottom = (float) $vals[3];
                    $widthPoints = $right - $left;
                    $heightPoints = $bottom - $top;
                    $pdf->setPaper([$widthPoints, $heightPoints], $orientation);
                } else {
                    $pdf->setPaper($vals, $orientation);
                }
            } else {
                $pdf->setPaper($paper, $orientation);
            }

            return $pdf->download($filename);
        } catch (\Throwable $e) {
            Log::error('PdfGenerator::downloadHtml - Dompdf also failed', [
                'error' => $e->getMessage(),
                'paper' => $paper,
                'utf8' => mb_check_encoding($html, 'UTF-8'),
                'html_preview' => mb_substr($html, 0, 500),
                'exception' => $e->getTraceAsString(),
            ]);
            abort(500, 'Failed to generate PDF');
        }
    }

    /**
     * Get DomPDF options optimized for Arabic/RTL support.
     */
    protected function getDompdfOptions(): array
    {
        return [
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'enable_font_subsetting' => true,
            'default_font' => 'dejavusans', // DejaVu Sans supports Arabic
        ];
    }

    /**
     * Check if HTML already has RTL setup (dir="rtl" or direction: rtl).
     */
    protected function hasRtlSetup(string $html): bool
    {
        return str_contains($html, 'dir="rtl"') 
            || str_contains($html, "dir='rtl'")
            || str_contains($html, 'direction: rtl')
            || str_contains($html, 'direction:rtl');
    }

    /**
     * Wrap HTML with RTL configuration if not already present.
     */
    protected function wrapWithRtl(string $html): string
    {
        // If already a complete HTML document, return as-is
        if (str_contains(strtolower($html), '<!doctype') || str_contains(strtolower($html), '<html')) {
            return $html;
        }

        // Wrap simple HTML with RTL structure
        return <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: "DejaVu Sans", "Tahoma", sans-serif;
            direction: rtl;
            text-align: right;
            line-height: 1.6;
        }
        table {
            direction: rtl;
            text-align: right;
        }
        th, td {
            text-align: right;
        }
    </style>
</head>
<body>
{$html}
</body>
</html>
HTML;
    }

    /**
     * Create an mPDF instance with sane defaults for Arabic/UTF-8.
     */
    protected function createMpdfInstance($paper)
    {
        // If caller passed a 4-value array like [left, top, right, bottom] (points), convert to width/height in mm
        if (is_array($paper) && count($paper) === 4) {
            $left = (float) $paper[0];
            $top = (float) $paper[1];
            $right = (float) $paper[2];
            $bottom = (float) $paper[3];
            $widthPoints = $right - $left;
            $heightPoints = $bottom - $top;
            // convert points to mm (1 pt = 0.352777778 mm)
            $widthMm = round($widthPoints * 0.352777778, 4);
            $heightMm = round($heightPoints * 0.352777778, 4);
            $format = [$widthMm, $heightMm];
        } else {
            $format = $paper;
        }

        $config = [
            'mode' => 'utf-8',
            'format' => $format,
            'default_font' => 'dejavusans',
            // Enable hyphenation and complex script handling
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ];

        // If caller passed a simple A4/A5 string, keep as-is; mPDF will accept that
        return new \Mpdf\Mpdf($config);
    }

    /**
     * Create a TCPDF instance configured for UTF-8/RTL output.
     * Accepts string paper sizes (A4) or the same array format used elsewhere.
     */
    protected function createTcpdfInstance($paper, string $orientation = 'portrait')
    {
        // Convert paper to TCPDF format if needed (TCPDF accepts array(width,height) in mm)
        $format = $this->convertPaperForTcpdf($paper);

        $orient = strtoupper(substr($orientation, 0, 1)) === 'L' ? 'L' : 'P';

        // TCPDF constructor: TCPDF($orientation='P',$unit='mm',$format='A4',$unicode=true,$encoding='UTF-8',$diskcache=false,$pdfa=false)
        $tcpdf = new \TCPDF($orient, 'mm', $format, true, 'UTF-8', false);

        // Set document info minimal
        $tcpdf->SetCreator(config('app.name'));
        $tcpdf->SetAuthor(config('app.name'));

        // Make sure the default font can render Arabic (we attempt to set later)
        $tcpdf->setLanguageArray([]);

        return $tcpdf;
    }

    /**
     * Convert our $paper value to a TCPDF-acceptable format.
     * If $paper is an array of 4 coordinates (points), convert to mm width/height.
     */
    protected function convertPaperForTcpdf($paper)
    {
        if (is_array($paper)) {
            // If passed as [left, top, right, bottom] in points (as Dompdf often uses), convert to mm
            if (count($paper) === 4) {
                $left = (float) $paper[0];
                $top = (float) $paper[1];
                $right = (float) $paper[2];
                $bottom = (float) $paper[3];
                $widthPoints = $right - $left;
                $heightPoints = $bottom - $top;
                // 1 point = 0.352777778 mm
                $widthMm = $widthPoints * 0.352777778;
                $heightMm = $heightPoints * 0.352777778;
                return [$widthMm, $heightMm];
            }

            // If already width/height in mm, return as-is
            return $paper;
        }

        return $paper;
    }

    /**
     * Detect presence of Arabic characters in a string.
     */
    protected function containsArabic(string $text): bool
    {
        return (bool) preg_match('/\p{Arabic}/u', $text);
    }

    /**
     * Attempt to sanitize and ensure a UTF-8 string for PDF generation.
     */
    protected function sanitizeHtml(string $html): string
    {
        try {
            if (!mb_check_encoding($html, 'UTF-8')) {
                $html = mb_convert_encoding($html, 'UTF-8', 'auto');
            }

            // Remove control characters that may break HTML parsers
            $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $html);

            $html = @iconv('UTF-8', 'UTF-8//IGNORE', $html);

            if (!mb_check_encoding($html, 'UTF-8')) {
                $tried = ['Windows-1256', 'CP1256', 'ISO-8859-6', 'Windows-1252', 'ISO-8859-1'];
                foreach ($tried as $enc) {
                    $try = @mb_convert_encoding($html, 'UTF-8', $enc);
                    $try = @iconv('UTF-8', 'UTF-8//IGNORE', $try);
                    if ($try !== false && mb_check_encoding($try, 'UTF-8')) {
                        $html = $try;
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('PdfGenerator::sanitizeHtml - sanitization error', ['error' => $e->getMessage()]);
            $html = @iconv('UTF-8', 'UTF-8//IGNORE', $html);
        }

        return $html;
    }
}
