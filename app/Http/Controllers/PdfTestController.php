<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfTestController extends Controller
{
    /**
     * Test Arabic PDF rendering
     */
    public function testArabic()
    {
        $html = '<!doctype html><html><head><meta charset="utf-8"><style>body{font-family:"DejaVu Sans", "Tahoma", sans-serif; direction: rtl; text-align: right;}</style></head><body><p style="font-size:18px">مرحبا بالعالم — Arabic rendering test</p></body></html>';

        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
        ])->loadHTML($html);

        return $pdf->stream('test-ar.pdf');
    }
}
