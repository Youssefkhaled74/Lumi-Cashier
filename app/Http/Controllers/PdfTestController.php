<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfTestController extends Controller
{
    /**
     * Test Arabic rendering in PDF with DejaVu Sans (built-in font).
     */
    public function testArabic()
    {
        $html = $this->getTestHtml();

        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'enable_font_subsetting' => true,
            'default_font' => 'dejavusans',
        ])->loadHTML($html);

        return $pdf->stream('test-arabic-pdf.pdf');
    }

    /**
     * Generate comprehensive Arabic test HTML.
     */
    protected function getTestHtml(): string
    {
        $date = date('Y-m-d H:i:s');
        
        return <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>اختبار PDF العربي</title>
    <style>
        body {
            font-family: "DejaVu Sans", "Tahoma", sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
            line-height: 1.8;
            font-size: 14px;
        }
        
        h1 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        h2 {
            color: #34495e;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .test-box {
            border: 2px solid #bdc3c7;
            padding: 15px;
            margin: 15px 0;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
        
        .success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            direction: rtl;
        }
        
        th {
            background-color: #3498db;
            color: white;
            padding: 10px;
            text-align: right;
            font-weight: bold;
        }
        
        td {
            border: 1px solid #bdc3c7;
            padding: 8px;
            text-align: right;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .number {
            font-family: "DejaVu Sans", monospace;
            direction: ltr;
            display: inline-block;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #95a5a6;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .highlight {
            background-color: #fff3cd;
            padding: 3px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>اختبار شامل لدعم اللغة العربية في ملفات PDF</h1>
    
    <div class="test-box success">
        <h2>النتيجة</h2>
        <p><strong>إذا ظهر هذا النص بشكل صحيح، فإن دعم العربية يعمل بنجاح!</strong></p>
        <p>الخط المستخدم: <span class="highlight">DejaVu Sans</span> (مدمج مع DomPDF)</p>
    </div>
    
    <div class="test-box">
        <h2>اختبار النص العادي</h2>
        <p>مرحباً بكم في نظام نقاط البيع <strong>لومي (Lumi POS)</strong></p>
        <p>هذا النظام مصمم لإدارة المبيعات والمخزون بكفاءة عالية.</p>
    </div>
    
    <div class="test-box">
        <h2>اختبار الأرقام والتواريخ</h2>
        <p>رقم الفاتورة: <span class="number">#12345</span></p>
        <p>التاريخ: <span class="number">{$date}</span></p>
        <p>المبلغ الإجمالي: <span class="number">1,234.56</span> جنيه مصري</p>
    </div>
    
    <div class="test-box">
        <h2>اختبار الجداول</h2>
        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>قهوة تركي</td>
                    <td><span class="number">2</span></td>
                    <td><span class="number">25.00</span></td>
                    <td><span class="number">50.00</span></td>
                </tr>
                <tr>
                    <td>كرواسون بالشوكولاتة</td>
                    <td><span class="number">3</span></td>
                    <td><span class="number">15.00</span></td>
                    <td><span class="number">45.00</span></td>
                </tr>
                <tr style="background-color: #d4edda; font-weight: bold;">
                    <td colspan="3">المجموع الكلي:</td>
                    <td><span class="number">95.00</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="footer">
        <p><strong>Lumi POS Desktop Application</strong></p>
        <p>نظام نقاط البيع المتكامل | Built with Laravel</p>
        <p>تم إنشاء هذا المستند في: <span class="number">{$date}</span></p>
    </div>
</body>
</html>
HTML;
    }
}
