<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MarketingController extends Controller
{
    /**
     * Show marketing materials page
     */
    public function index()
    {
        return view('marketing.index');
    }

    /**
     * Generate marketing brochure PDF
     */
    public function brochure()
    {
        $pdf = Pdf::loadView('marketing.brochure')
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->stream('Lumi-Cashier-Marketing-Brochure.pdf');
    }

    /**
     * Download marketing brochure
     */
    public function downloadBrochure()
    {
        $pdf = Pdf::loadView('marketing.brochure')
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->download('Lumi-Cashier-Brochure-' . date('Y-m-d') . '.pdf');
    }
}
