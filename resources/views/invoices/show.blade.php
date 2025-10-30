<!DOCTYPE html><!DOCTYPE html>

<html lang="en"><html lang="en">

<head><head>

    <meta charset="UTF-8">    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Receipt #{{ $order->id }}</title>    <title>Receipt #{{ $order->id }}</title>

    <style>    <style>

        * {        * {

            margin: 0;            margin: 0;

            padding: 0;            padding: 0;

            box-sizing: border-box;            box-sizing: border-box;

        }        }



        @page {        @page {

            margin: 0;            margin: 0;

            size: 80mm auto; /* Thermal printer width */            size: 80mm auto; /* Thermal printer width */

        }        }



        body {        body {

            font-family: 'Courier New', 'DejaVu Sans Mono', monospace;            font-family: 'Courier New', 'DejaVu Sans Mono', monospace;

            font-size: 12px;            font-size: 12px;

            color: #000;            color: #000;

            line-height: 1.4;            line-height: 1.4;

            width: 80mm;            width: 80mm;

            margin: 0 auto;            margin: 0 auto;

            padding: 5mm;            padding: 5mm;

        }        }



        .receipt-container {        .receipt-container {

            width: 100%;            width: 100%;

        }        }



        /* Header Section */        /* Header Section */

        .header {        .header {

            text-align: center;            text-align: center;

            border-bottom: 2px dashed #000;            border-bottom: 2px dashed #000;

            padding-bottom: 8px;            padding-bottom: 8px;

            margin-bottom: 10px;            margin-bottom: 10px;

        }        }



        .store-name {        .store-name {

            font-size: 18px;            font-size: 18px;

            font-weight: bold;            font-weight: bold;

            margin-bottom: 3px;            margin-bottom: 3px;

            text-transform: uppercase;            text-transform: uppercase;

        }        }



        .store-info {        .store-info {

            font-size: 10px;            font-size: 10px;

            line-height: 1.3;            line-height: 1.3;

        }        }



        .divider {        .divider {

            border-top: 1px dashed #000;            border-top: 1px dashed #000;

            margin: 8px 0;            margin: 8px 0;

        }        }



        .divider-bold {        .divider-bold {

            border-top: 2px solid #000;            border-top: 2px solid #000;

            margin: 8px 0;            margin: 8px 0;

        }        }



        /* Receipt Info */        /* Receipt Info */

        .receipt-info {        .receipt-info {

            font-size: 11px;            font-size: 11px;

            margin-bottom: 8px;            margin-bottom: 8px;

        }        }



        .receipt-info-line {        .receipt-info-line {

            display: flex;            display: flex;

            justify-content: space-between;            justify-content: space-between;

            margin-bottom: 2px;            margin-bottom: 2px;

        }        }



        .label {        .label {

            font-weight: bold;            font-weight: bold;

        }        }



        /* Items Section */        /* Items Section */

        .items-section {        .items-section {

            margin: 10px 0;            margin: 10px 0;

        }        }



        .item-row {        .item-row {

            margin-bottom: 8px;            margin-bottom: 8px;

            font-size: 11px;            font-size: 11px;

        }        }



        /* Totals Section */        .item-header {

        .totals-section {            font-weight: bold;

            margin-top: 10px;            margin-bottom: 2px;

            padding-top: 8px;        }

            border-top: 1px dashed #000;

        }        .item-details {

            display: flex;

        .total-line {            justify-content: space-between;

            display: flex;            font-size: 10px;

            justify-content: space-between;        }

            margin-bottom: 4px;

            font-size: 11px;        .item-qty-price {

        }            display: flex;

            justify-content: space-between;

        .total-line.grand-total {            margin-top: 2px;

            font-size: 14px;        }

            font-weight: bold;

            margin-top: 6px;        .qty-section {

            padding-top: 6px;            flex: 1;

            border-top: 2px solid #000;        }

        }

        .price-section {

        .total-label {            text-align: right;

            font-weight: bold;            font-weight: bold;

        }        }



        /* Footer */        /* Totals Section */

        .footer {        .totals-section {

            text-align: center;            margin-top: 10px;

            margin-top: 12px;            padding-top: 8px;

            padding-top: 8px;            border-top: 1px dashed #000;

            border-top: 2px dashed #000;        }

            font-size: 10px;

        }        .total-line {

            display: flex;

        .thank-you {            justify-content: space-between;

            font-size: 13px;            margin-bottom: 4px;

            font-weight: bold;            font-size: 11px;

            margin-bottom: 5px;        }

        }

        .total-line.grand-total {

        .barcode-section {            font-size: 14px;

            text-align: center;            font-weight: bold;

            margin: 10px 0;            margin-top: 6px;

        }            padding-top: 6px;

            border-top: 2px solid #000;

        .barcode-img {        }

            max-width: 60mm;

            height: auto;        .total-label {

        }            font-weight: bold;

        }

        .notes-section {

            margin: 8px 0;        /* Footer */

            padding: 5px;        .footer {

            background-color: #f0f0f0;            text-align: center;

            border: 1px solid #ccc;            margin-top: 12px;

            font-size: 10px;            padding-top: 8px;

        }            border-top: 2px dashed #000;

            font-size: 10px;

        .notes-title {        }

            font-weight: bold;

            margin-bottom: 3px;        .thank-you {

        }            font-size: 13px;

            font-weight: bold;

        .bold {            margin-bottom: 5px;

            font-weight: bold;        }

        }

        .barcode-section {

        .small {            text-align: center;

            font-size: 9px;            margin: 10px 0;

        }        }



        .x-small {        .barcode-img {

            font-size: 8px;            max-width: 60mm;

        }            height: auto;

        }

        /* Table-like layout for items */

        .items-table-header {        .notes-section {

            font-weight: bold;            margin: 8px 0;

            display: flex;            padding: 5px;

            justify-content: space-between;            background-color: #f0f0f0;

            border-bottom: 1px solid #000;            border: 1px solid #ccc;

            padding-bottom: 3px;            font-size: 10px;

            margin-bottom: 5px;        }

            font-size: 10px;

        }        .notes-title {

            font-weight: bold;

        .col-item {            margin-bottom: 3px;

            flex: 2;        }

        }

        .center {

        .col-qty {            text-align: center;

            flex: 0.5;        }

            text-align: center;

        }        .bold {

            font-weight: bold;

        .col-price {        }

            flex: 1;

            text-align: right;        .small {

        }            font-size: 9px;

        }

        .col-total {

            flex: 1;        .x-small {

            text-align: right;            font-size: 8px;

        }        }

    </style>

</head>        /* Table-like layout for items */

<body>        .items-table {

    <div class="receipt-container">            width: 100%;

        {{-- Header --}}            font-size: 10px;

        <div class="header">        }

            <div class="store-name">{{ $company['name'] }}</div>

            <div class="store-info">        .items-table-header {

                {{ $company['address'] }}<br>            font-weight: bold;

                {{ $company['city'] }}<br>            display: flex;

                Tel: {{ $company['phone'] }}<br>            justify-content: space-between;

                {{ $company['email'] }}            border-bottom: 1px solid #000;

            </div>            padding-bottom: 3px;

        </div>            margin-bottom: 5px;

        }

        {{-- Receipt Info --}}

        <div class="receipt-info">        .col-item {

            <div class="receipt-info-line">            flex: 2;

                <span class="label">Receipt #:</span>        }

                <span>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>

            </div>        .col-qty {

            <div class="receipt-info-line">            flex: 0.5;

                <span class="label">Date:</span>            text-align: center;

                <span>{{ $order->created_at->format('M d, Y') }}</span>        }

            </div>

            <div class="receipt-info-line">        .col-price {

                <span class="label">Time:</span>            flex: 1;

                <span>{{ $order->created_at->format('h:i A') }}</span>            text-align: right;

            </div>        }

            @if($order->day)

            <div class="receipt-info-line">        .col-total {

                <span class="label">Day #:</span>            flex: 1;

                <span>{{ $order->day->id }}</span>            text-align: right;

            </div>        }

            @endif    </style>

            <div class="receipt-info-line"></head>

                <span class="label">Cashier:</span><body>

                <span>{{ config('cashier.admin.email', 'Admin') }}</span>    <div class="receipt-container">

            </div>        {{-- Header --}}

            @if($order->payment_method)        <div class="header">

            <div class="receipt-info-line">            <div class="store-name">{{ $company['name'] }}</div>

                <span class="label">Payment:</span>            <div class="store-info">

                <span>{{ strtoupper($order->payment_method) }}</span>                {{ $company['address'] }}<br>

            </div>                {{ $company['city'] }}<br>

            @endif                Tel: {{ $company['phone'] }}<br>

        </div>                {{ $company['email'] }}

            </div>

        <div class="divider-bold"></div>        </div>



        {{-- Items --}}        {{-- Receipt Info --}}

        <div class="items-section">        <div class="receipt-info">

            <div class="items-table-header">            <div class="receipt-info-line">

                <div class="col-item">ITEM</div>                <span class="label">Receipt #:</span>

                <div class="col-qty">QTY</div>                <span>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>

                <div class="col-price">PRICE</div>            </div>

                <div class="col-total">TOTAL</div>            <div class="receipt-info-line">

            </div>                <span class="label">Date:</span>

                <span>{{ $order->created_at->format('M d, Y') }}</span>

            @foreach($order->items as $orderItem)            </div>

                @php            <div class="receipt-info-line">

                    $item = $orderItem->itemUnit->item;                <span class="label">Time:</span>

                @endphp                <span>{{ $order->created_at->format('h:i A') }}</span>

                <div class="item-row">            </div>

                    <div style="display: flex; justify-content: space-between;">            @if($order->day)

                        <div class="col-item">{{ $item->name }}</div>            <div class="receipt-info-line">

                        <div class="col-qty" style="text-align: center;">{{ $orderItem->quantity }}</div>                <span class="label">Day #:</span>

                        <div class="col-price" style="text-align: right;">${{ number_format($orderItem->price, 2) }}</div>                <span>{{ $order->day->id }}</span>

                        <div class="col-total" style="text-align: right; font-weight: bold;">${{ number_format($orderItem->total, 2) }}</div>            </div>

                    </div>            @endif

                    @if($orderItem->discount_percentage > 0)            <div class="receipt-info-line">

                    <div class="small" style="margin-left: 5px; color: #666;">                <span class="label">Cashier:</span>

                        Discount: {{ $orderItem->discount_percentage }}% (-${{ number_format($orderItem->discount_amount, 2) }})                <span>{{ config('cashier.admin.email', 'Admin') }}</span>

                    </div>            </div>

                    @endif            @if($order->payment_method)

                </div>            <div class="receipt-info-line">

            @endforeach                <span class="label">Payment:</span>

        </div>                <span>{{ strtoupper($order->payment_method) }}</span>

            </div>

        <div class="divider"></div>            @endif

        </div>

        {{-- Totals --}}

        <div class="totals-section">        <div class="divider-bold"></div>

            <div class="total-line">

                <span class="total-label">Subtotal:</span>        {{-- Items --}}

                <span>${{ number_format($order->subtotal ?? $order->items->sum('total'), 2) }}</span>        <div class="items-section">

            </div>            <div class="items-table-header">

                <div class="col-item">ITEM</div>

            @if($order->discount_percentage > 0)                <div class="col-qty">QTY</div>

            <div class="total-line">                <div class="col-price">PRICE</div>

                <span class="total-label">Discount ({{ $order->discount_percentage }}%):</span>                <div class="col-total">TOTAL</div>

                <span>-${{ number_format($order->discount_amount, 2) }}</span>            </div>

            </div>

            @endif            @foreach($order->items as $orderItem)

                @php

            @if($order->tax_percentage > 0)                    $item = $orderItem->itemUnit->item;

            <div class="total-line">                @endphp

                <span class="total-label">Tax ({{ $order->tax_percentage }}%):</span>                <div class="item-row">

                <span>${{ number_format($order->tax_amount, 2) }}</span>                    <div style="display: flex; justify-content: space-between;">

            </div>                        <div class="col-item">{{ $item->name }}</div>

            @endif                        <div class="col-qty" style="text-align: center;">{{ $orderItem->quantity }}</div>

                        <div class="col-price" style="text-align: right;">${{ number_format($orderItem->price, 2) }}</div>

            <div class="total-line grand-total">                        <div class="col-total" style="text-align: right; font-weight: bold;">${{ number_format($orderItem->total, 2) }}</div>

                <span>TOTAL:</span>                    </div>

                <span>${{ number_format($order->total, 2) }}</span>                    @if($orderItem->discount_percentage > 0)

            </div>                    <div class="small" style="margin-left: 5px; color: #666;">

        </div>                        Discount: {{ $orderItem->discount_percentage }}% (-${{ number_format($orderItem->discount_amount, 2) }})

                    </div>

        {{-- Customer Info --}}                    @endif

        @if($order->customer_name || $order->customer_phone)                </div>

        <div class="divider"></div>            @endforeach

        <div class="receipt-info">        </div>

            <div class="bold" style="margin-bottom: 3px;">CUSTOMER INFO:</div>

            @if($order->customer_name)        <div class="divider"></div>

            <div class="receipt-info-line">

                <span class="label">Name:</span>        {{-- Totals --}}

                <span>{{ $order->customer_name }}</span>        <div class="totals-section">

            </div>            <div class="total-line">

            @endif                <span class="total-label">Subtotal:</span>

            @if($order->customer_phone)                <span>${{ number_format($order->subtotal ?? $order->items->sum('total'), 2) }}</span>

            <div class="receipt-info-line">            </div>

                <span class="label">Phone:</span>

                <span>{{ $order->customer_phone }}</span>            @if($order->discount_percentage > 0)

            </div>            <div class="total-line">

            @endif                <span class="total-label">Discount ({{ $order->discount_percentage }}%):</span>

            @if($order->customer_email)                <span>-${{ number_format($order->discount_amount, 2) }}</span>

            <div class="receipt-info-line small">            </div>

                <span class="label">Email:</span>            @endif

                <span>{{ $order->customer_email }}</span>

            </div>            @if($order->tax_percentage > 0)

            @endif            <div class="total-line">

        </div>                <span class="total-label">Tax ({{ $order->tax_percentage }}%):</span>

        @endif                <span>${{ number_format($order->tax_amount, 2) }}</span>

            </div>

        {{-- Notes --}}            @endif

        @if($order->notes)

        <div class="divider"></div>            <div class="total-line grand-total">

        <div class="notes-section">                <span>TOTAL:</span>

            <div class="notes-title">Notes:</div>                <span>${{ number_format($order->total, 2) }}</span>

            <div>{{ $order->notes }}</div>            </div>

        </div>        </div>

        @endif

        {{-- Customer Info --}}

        {{-- Order Barcode --}}        @if($order->customer_name || $order->customer_phone)

        <div class="divider"></div>        <div class="divider"></div>

        <div class="barcode-section">        <div class="receipt-info">

            @php            <div class="bold" style="margin-bottom: 3px;">CUSTOMER INFO:</div>

                $orderBarcode = 'ORD' . str_pad($order->id, 6, '0', STR_PAD_LEFT);            @if($order->customer_name)

                $barcodeBase64 = \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($orderBarcode, 'C128', 2, 40);            <div class="receipt-info-line">

            @endphp                <span class="label">Name:</span>

            <img src="data:image/png;base64,{{ $barcodeBase64 }}"                 <span>{{ $order->customer_name }}</span>

                 alt="order barcode"             </div>

                 class="barcode-img" />            @endif

            <div class="x-small">{{ $orderBarcode }}</div>            @if($order->customer_phone)

        </div>            <div class="receipt-info-line">

                <span class="label">Phone:</span>

        {{-- Footer --}}                <span>{{ $order->customer_phone }}</span>

        <div class="footer">            </div>

            <div class="thank-you">THANK YOU!</div>            @endif

            <div class="small">Please come again</div>            @if($order->customer_email)

            <div class="divider"></div>            <div class="receipt-info-line small">

            <div class="x-small">                <span class="label">Email:</span>

                Printed: {{ now()->format('M d, Y h:i A') }}<br>                <span>{{ $order->customer_email }}</span>

                Status: {{ strtoupper($order->status) }}            </div>

            </div>            @endif

            <div class="x-small" style="margin-top: 5px;">        </div>

                {{ config('app.name', 'Lumi POS') }} - www.lumipos.com        @endif

            </div>

        </div>        {{-- Notes --}}

    </div>        @if($order->notes)

</body>        <div class="divider"></div>

</html>        <div class="notes-section">

            <div class="notes-title">Notes:</div>
            <div>{{ $order->notes }}</div>
        </div>
        @endif

        {{-- Order Barcode --}}
        <div class="divider"></div>
        <div class="barcode-section">
            @php
                $orderBarcode = 'ORD' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                $barcodeBase64 = \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($orderBarcode, 'C128', 2, 40);
            @endphp
            <img src="data:image/png;base64,{{ $barcodeBase64 }}" 
                 alt="order barcode" 
                 class="barcode-img" />
            <div class="x-small">{{ $orderBarcode }}</div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="thank-you">THANK YOU!</div>
            <div class="small">Please come again</div>
            <div class="divider"></div>
            <div class="x-small">
                Printed: {{ now()->format('M d, Y h:i A') }}<br>
                Status: {{ strtoupper($order->status) }}
            </div>
            <div class="x-small" style="margin-top: 5px;">
                {{ config('app.name', 'Lumi POS') }} - www.lumipos.com
            </div>
        </div>
    </div>
</body>
</html>

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

