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
                Log::warning('PdfGenerator::streamHtml - mPDF failed, falling back to Dompdf', ['error' => $e->getMessage()]);
            }
        }

        // Dompdf fallback
        try {
            $pdf = DomPdfFacade::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ])->loadHTML($html);

            if (is_array($paper)) {
                $pdf->setPaper($paper, $orientation);
            } else {
                $pdf->setPaper($paper, $orientation);
            }

            return $pdf->stream($filename);
        } catch (\Throwable $e) {
            Log::error('PdfGenerator::streamHtml - Dompdf also failed', ['error' => $e->getMessage()]);
            abort(500, 'Failed to generate PDF');
        }
    }

    /**
     * Download raw HTML as PDF.
     */
    public function downloadHtml(string $html, string $filename = 'document.pdf', $paper = 'A4', string $orientation = 'portrait')
    {
        $html = $this->sanitizeHtml($html);

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
                Log::warning('PdfGenerator::downloadHtml - mPDF failed, falling back to Dompdf', ['error' => $e->getMessage()]);
            }
        }

        try {
            $pdf = DomPdfFacade::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ])->loadHTML($html);

            if (is_array($paper)) {
                $pdf->setPaper($paper, $orientation);
            } else {
                $pdf->setPaper($paper, $orientation);
            }

            return $pdf->download($filename);
        } catch (\Throwable $e) {
            Log::error('PdfGenerator::downloadHtml - Dompdf also failed', ['error' => $e->getMessage()]);
            abort(500, 'Failed to generate PDF');
        }
    }

    /**
     * Create an mPDF instance with sane defaults for Arabic/UTF-8.
     */
    protected function createMpdfInstance($paper)
    {
        $config = [
            'mode' => 'utf-8',
            'format' => $paper,
            'default_font' => 'dejavusans',
            // Enable hyphenation and complex script handling
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ];

        // If caller passed a simple A4/A5 string, keep as-is; mPDF will accept that
        return new \Mpdf\Mpdf($config);
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
