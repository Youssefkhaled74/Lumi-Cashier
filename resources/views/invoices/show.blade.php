<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Header Section */
        .header {
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-grid {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 9px;
            color: #666;
            line-height: 1.5;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 8px;
        }

        .invoice-meta {
            font-size: 10px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .status-completed {
            background-color: #D1FAE5;
            color: #065F46;
        }

        /* Info Section */
        .info-section {
            background-color: #F9FAFB;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-col {
            display: table-cell;
            width: 33.33%;
            padding: 5px;
        }

        .info-label {
            font-size: 9px;
            color: #6B7280;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 11px;
            color: #111827;
            font-weight: 500;
        }

        /* Items Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #4F46E5;
            color: white;
        }

        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }

        table th.text-right {
            text-align: right;
        }

        table th.text-center {
            text-align: center;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 10px;
        }

        table tbody tr:last-child td {
            border-bottom: 2px solid #4F46E5;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .item-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 2px;
        }

        .item-sku {
            font-size: 9px;
            color: #6B7280;
        }

        .barcode-img {
            height: 35px;
            margin: 2px 0;
        }

        .barcode-text {
            font-size: 8px;
            color: #6B7280;
            font-family: 'Courier New', monospace;
        }

        /* Totals Section */
        .totals-section {
            float: right;
            width: 280px;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 6px 12px;
            border: none;
            font-size: 11px;
        }

        .total-row {
            background-color: #4F46E5;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        /* Notes */
        .notes {
            clear: both;
            margin-top: 20px;
            padding: 12px;
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            font-size: 10px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
            color: #92400E;
        }

        /* Footer */
        .footer {
            clear: both;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 9px;
            color: #6B7280;
        }

        .footer-highlight {
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Header --}}
        <div class="header">
            <div class="header-grid">
                <div class="header-left">
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="company-info">
                        {{ $company['address'] }}<br>
                        {{ $company['city'] }}<br>
                        Phone: {{ $company['phone'] }} | Email: {{ $company['email'] }}
                    </div>
                </div>
                <div class="header-right">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        <strong>Invoice #:</strong> {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}<br>
                        <strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}
                    </div>
                    <span class="status-badge status-{{ strtolower($order->status) }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Order Information --}}
        <div class="info-section">
            <div class="info-grid">
                <div class="info-col">
                    <div class="info-label">Business Day</div>
                    <div class="info-value">Day #{{ $order->day->id }} - {{ $order->day->date->format('M d, Y') }}</div>
                </div>
                <div class="info-col">
                    <div class="info-label">Cashier</div>
                    <div class="info-value">{{ config('cashier.admin.email', 'Admin') }}</div>
                </div>
                <div class="info-col">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">Cash</div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Item Description</th>
                    <th style="width: 20%" class="text-center">Barcode</th>
                    <th style="width: 15%" class="text-right">Unit Price</th>
                    <th style="width: 10%" class="text-center">Qty</th>
                    <th style="width: 20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedItems = $order->items->groupBy(function($item) {
                        return $item->itemUnit->item->id;
                    });
                @endphp

                @foreach($groupedItems as $itemId => $orderItems)
                    @php
                        $firstItem = $orderItems->first();
                        $item = $firstItem->itemUnit->item;
                        $quantity = $orderItems->count();
                        $unitPrice = $firstItem->price;
                        $total = $orderItems->sum('total');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="item-name">{{ $item->name }}</div>
                            <div class="item-sku">{{ $item->category->name }} | SKU: {{ $item->sku }}</div>
                        </td>
                        <td class="text-center">
                            @if($item->barcode)
                                @php
                                    $barcodeBase64 = \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($item->barcode, 'C128', 1.5, 35);
                                @endphp
                                <img src="data:image/png;base64,{{ $barcodeBase64 }}" 
                                     alt="barcode" 
                                     class="barcode-img" />
                                <div class="barcode-text">{{ $item->barcode }}</div>
                            @else
                                <span class="barcode-text">N/A</span>
                            @endif
                        </td>
                        <td class="text-right">${{ number_format($unitPrice, 2) }}</td>
                        <td class="text-center"><strong>{{ $quantity }}</strong></td>
                        <td class="text-right"><strong>${{ number_format($total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">${{ number_format($order->total, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Tax (0%):</strong></td>
                    <td class="text-right">$0.00</td>
                </tr>
                <tr>
                    <td><strong>Discount:</strong></td>
                    <td class="text-right">$0.00</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>${{ number_format($order->total, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        {{-- Notes --}}
        @if($order->notes)
        <div class="notes">
            <div class="notes-title">Notes:</div>
            <div>{{ $order->notes }}</div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p class="footer-highlight">Thank you for your business!</p>
            <p>This is a computer-generated invoice. For inquiries, please contact {{ $company['email'] }}</p>
            <p style="margin-top: 8px;">Generated: {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>

