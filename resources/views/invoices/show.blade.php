<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        /* Optional: if you place an Arabic-capable TTF in public/fonts (e.g. Tajawal-Regular.ttf), dompdf can load it from the filesystem.
           Create public/fonts and drop the TTF files there. Uncommenting these @font-face rules will make the PDF renderer use them. */
        @if(file_exists(public_path('fonts/Tajawal-Regular.ttf')))
        /* Use HTTP asset URLs so dompdf (with isRemoteEnabled) can fetch fonts when rendering */
        @font-face {
            font-family: 'Tajawal';
            font-style: normal;
            font-weight: 400;
            src: url("{{ asset('fonts/Tajawal-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Tajawal';
            font-style: normal;
            font-weight: 700;
            src: url("{{ asset('fonts/Tajawal-Bold.ttf') }}") format('truetype');
        }
        @endif
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 0; size: 90mm auto; }
    /* Use Tajawal if available, otherwise DejaVu Sans (bundled with dompdf). */
    body { font-family: {{ file_exists(public_path('fonts/Tajawal-Regular.ttf')) ? "'Tajawal', 'DejaVu Sans', 'Tahoma', sans-serif" : "'DejaVu Sans', 'Tahoma', sans-serif" }}; font-size: 10px; color: #000; line-height: 1.4; width: 84mm; margin: 0 auto; padding: 3mm; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; unicode-bidi: embed; }
        .receipt-container { width: 100%; max-width: 84mm; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 6px; margin-bottom: 8px; }
        .logo-img { max-width: 50mm; max-height: 20mm; margin: 0 auto 5px; display: block; }
        .store-name { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .store-info { font-size: 9px; line-height: 1.4; color: #333; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        .divider-bold { border-top: 2px solid #000; margin: 8px 0; }
        .receipt-info { font-size: 9px; margin-bottom: 6px; }
        .info-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .info-table td { padding: 2px 0; overflow: hidden; word-wrap: break-word; }
        .info-table .info-label { font-weight: bold; width: 45%; text-align: left; }
        .info-table .info-value { width: 55%; text-align: right; }
        .items-section { margin: 8px 0; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 10px; table-layout: fixed; }
        .items-table thead { font-weight: bold; border-bottom: 2px solid #000; }
        .items-table th { padding: 3px 1px; font-weight: bold; text-align: left; overflow: hidden; }
        .items-table td { padding: 3px 1px; vertical-align: top; overflow: hidden; word-wrap: break-word; }
        .items-table .col-item { width: 43%; text-align: left; }
        .items-table .col-qty { width: 12%; text-align: center; font-weight: bold; }
        .items-table .col-price { width: 22%; text-align: right; }
        .items-table .col-total { width: 23%; text-align: right; font-weight: bold; }
        .items-table tbody tr { border-bottom: 1px dotted #ccc; }
        .items-table tbody tr:last-child { border-bottom: 2px solid #000; }
        .item-discount { font-size: 8px; color: #555; margin-top: 2px; font-style: italic; display: block; }
        .totals-section { margin-top: 8px; padding-top: 6px; border-top: 1px dashed #000; }
        .totals-table { width: 100%; border-collapse: collapse; font-size: 10px; table-layout: fixed; }
        .totals-table td { padding: 3px 0; overflow: hidden; }
        .totals-table .total-label { font-weight: bold; width: 60%; text-align: left; }
        .totals-table .total-value { width: 40%; text-align: right; font-weight: bold; }
        .grand-total { font-size: 13px; font-weight: bold; margin-top: 6px; padding-top: 6px; border-top: 3px double #000; }
        .grand-total .total-label { font-size: 12px; }
        .grand-total .total-value { font-size: 14px; }
        .customer-section { margin: 8px 0; padding: 5px; background-color: #f5f5f5; border: 1px solid #ddd; }
        .customer-title { font-weight: bold; font-size: 9px; margin-bottom: 4px; text-transform: uppercase; }
        .notes-section { margin: 8px 0; padding: 5px; background-color: #fffbf0; border: 1px solid #e0e0e0; font-size: 9px; }
        .notes-title { font-weight: bold; margin-bottom: 3px; }
        .barcode-section { text-align: center; margin: 10px 0; }
        .barcode-img { max-width: 70mm; height: auto; }
        .barcode-text { font-size: 8px; margin-top: 3px; }
        .footer { text-align: center; margin-top: 10px; padding-top: 8px; border-top: 2px dashed #000; }
        .thank-you { font-size: 13px; font-weight: bold; margin-bottom: 5px; }
        .footer-msg { font-size: 10px; margin-bottom: 6px; }
        .footer-info { font-size: 8px; color: #666; line-height: 1.4; }
    </style>
@php
    $shopSettings = \App\Models\ShopSettings::current();
@endphp
</head>
<body>
<div class="receipt-container">
<div class="header">
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
{{ $company['email'] }}
</div>
</div>
<div class="receipt-info">
<table class="info-table">
<tr><td class="info-label">Receipt #:</td><td class="info-value">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
<tr><td class="info-label">Date:</td><td class="info-value">{{ $order->created_at->format('M d, Y') }}</td></tr>
<tr><td class="info-label">Time:</td><td class="info-value">{{ $order->created_at->format('h:i A') }}</td></tr>
@if($order->day)<tr><td class="info-label">Day #:</td><td class="info-value">{{ $order->day->id }}</td></tr>@endif
<tr><td class="info-label">Cashier:</td><td class="info-value">{{ config('cashier.admin.email', 'Admin') }}</td></tr>
@if($order->payment_method)<tr><td class="info-label">Payment:</td><td class="info-value">{{ strtoupper($order->payment_method) }}</td></tr>@endif
</table>
</div>
<div class="divider-bold"></div>
<div class="items-section">
<table class="items-table">
<thead><tr><th class="col-item">ITEM</th><th class="col-qty">QTY</th><th class="col-price">PRICE</th><th class="col-total">TOTAL</th></tr></thead>
<tbody>
@php
$groupedItems = $order->items->groupBy(function($orderItem) {
    return $orderItem->itemUnit->item->id;
})->map(function($items) {
    $firstItem = $items->first();
    $item = $firstItem->itemUnit->item;
    return (object)[
        'name' => $item->name,
        'quantity' => $items->sum('quantity'),
        'price' => $firstItem->price,
        'total' => $items->sum('total'),
        'discount_percentage' => $items->avg('discount_percentage'),
        'discount_amount' => $items->sum('discount_amount'),
    ];
});
@endphp
@foreach($groupedItems as $item)
<tr>
<td class="col-item">{{ $item->name }}@if($item->discount_percentage > 0)<span class="item-discount">Disc {{ number_format($item->discount_percentage, 0) }}% (-${{ number_format($item->discount_amount, 2) }})</span>@endif</td>
<td class="col-qty">{{ $item->quantity }}</td>
<td class="col-price">${{ number_format($item->price, 2) }}</td>
<td class="col-total">${{ number_format($item->total, 2) }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="totals-section">
<table class="totals-table">
<tr><td class="total-label">Subtotal:</td><td class="total-value">${{ number_format($order->subtotal ?? $order->items->sum('total'), 2) }}</td></tr>
@if($order->discount_percentage > 0)<tr><td class="total-label">Discount ({{ $order->discount_percentage }}%):</td><td class="total-value">-${{ number_format($order->discount_amount, 2) }}</td></tr>@endif
@if($order->tax_percentage > 0)<tr><td class="total-label">Tax ({{ $order->tax_percentage }}%):</td><td class="total-value">${{ number_format($order->tax_amount, 2) }}</td></tr>@endif
<tr class="grand-total"><td class="total-label">TOTAL:</td><td class="total-value">${{ number_format($order->total, 2) }}</td></tr>
</table>
</div>
@if($order->customer_name || $order->customer_phone)
<div class="divider"></div>
<div class="customer-section">
<div class="customer-title">Customer Information</div>
<table class="info-table">
@if($order->customer_name)<tr><td class="info-label">Name:</td><td class="info-value">{{ $order->customer_name }}</td></tr>@endif
@if($order->customer_phone)<tr><td class="info-label">Phone:</td><td class="info-value">{{ $order->customer_phone }}</td></tr>@endif
@if($order->customer_email)<tr><td class="info-label">Email:</td><td class="info-value">{{ $order->customer_email }}</td></tr>@endif
</table>
</div>
@endif
@if($order->notes)
<div class="divider"></div>
<div class="notes-section"><div class="notes-title">Notes:</div><div>{{ $order->notes }}</div></div>
@endif
<div class="divider"></div>
<div class="barcode-section">
@php
$orderBarcode = 'ORD' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
$barcodeBase64 = \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($orderBarcode, 'C128', 2, 40);
@endphp
<img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="order barcode" class="barcode-img" />
<div class="barcode-text">{{ $orderBarcode }}</div>
</div>
<div class="footer">
<div class="thank-you">THANK YOU!</div>
<div class="footer-msg">Please come again</div>
<div class="divider"></div>
<div class="footer-info">Printed: {{ now()->format('M d, Y h:i A') }}<br>Status: {{ strtoupper($order->status) }}<br>{{ config('app.name', 'Lumi POS') }}</div>
</div>
</div>
</body>
</html>