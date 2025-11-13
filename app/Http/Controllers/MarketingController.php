<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PdfGenerator;

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
        $filename = 'Lumi-Cashier-Marketing-Brochure.pdf';
        return app(PdfGenerator::class)->streamView('marketing.brochure', [], $filename, 'A4', 'portrait');
    }

    /**
     * Download marketing brochure
     */
    public function downloadBrochure()
    {
        $filename = 'Lumi-Cashier-Brochure-' . date('Y-m-d') . '.pdf';
        return app(PdfGenerator::class)->downloadView('marketing.brochure', [], $filename, 'A4', 'portrait');
    }
}
