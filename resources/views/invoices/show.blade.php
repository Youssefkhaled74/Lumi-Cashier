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

            size: 90mm auto;            size: 90mm auto;

        }        }



        body {        body {

            font-family: 'Courier New', 'DejaVu Sans Mono', monospace;            font-family: 'Courier New', 'DejaVu Sans Mono', monospace;

            font-size: 10px;            font-size: 10px;

            color: #000;            color: #000;

            line-height: 1.4;            line-height: 1.4;

            width: 84mm;            width: 84mm;

            margin: 0 auto;            margin: 0 auto;

            padding: 3mm;            padding: 3mm;

        }        }



        .receipt-container {        .receipt-container {

            width: 100%;            width: 100%;

            max-width: 84mm;        }

        }

        /* Header */

        /* Header */        .header {

        .header {            text-align: center;

            text-align: center;            border-bottom: 2px dashed #000;

            border-bottom: 2px dashed #000;            padding-bottom: 6px;

            padding-bottom: 6px;            margin-bottom: 6px;

            margin-bottom: 8px;        }

        }

        .store-name {

        .store-name {            font-size: 16px;

            font-size: 16px;            font-weight: bold;

            font-weight: bold;            margin-bottom: 3px;

            margin-bottom: 4px;            letter-spacing: 1px;

            letter-spacing: 1px;        }

        }

        .store-info {

        .store-info {            font-size: 8px;

            font-size: 9px;            line-height: 1.3;

            line-height: 1.4;            color: #333;

            color: #333;        }

        }

        /* Dividers */

        /* Dividers */        .divider {

        .divider {            border-top: 1px dashed #000;

            border-top: 1px dashed #000;            margin: 5px 0;

            margin: 6px 0;        }

        }

        .divider-bold {

        .divider-bold {            border-top: 2px solid #000;

            border-top: 2px solid #000;            margin: 6px 0;

            margin: 8px 0;        }

        }

        .divider-double {

        .divider-double {            border-top: 3px double #000;

            border-top: 3px double #000;            margin: 5px 0;

            margin: 6px 0;        }

        }

        /* Receipt Info */

        /* Receipt Info */        .receipt-info {

        .receipt-info {            font-size: 9px;

            font-size: 9px;            margin-bottom: 5px;

            margin-bottom: 6px;        }

        }

        .info-table {

        .info-table {            width: 100%;

            width: 100%;            border-collapse: collapse;

            border-collapse: collapse;            table-layout: fixed;

            table-layout: fixed;        }

        }

        .info-table td {

        .info-table td {            padding: 2px;

            padding: 2px 0;            overflow: hidden;

            overflow: hidden;            word-wrap: break-word;

            word-wrap: break-word;            font-size: 9px;

        }        }



        .info-table .info-label {        .info-table .info-label {

            font-weight: bold;            font-weight: bold;

            width: 45%;            width: 45%;

            text-align: left;            text-align: left;

        }        }



        .info-table .info-value {        .info-table .info-value {

            width: 55%;            width: 55%;

            text-align: right;            text-align: right;

        }        }



        /* Items Table */        /* Items Table */

        .items-section {        .items-section {

            margin: 8px 0;            margin: 6px 0;

        }        }



        .items-table {        .items-table {

            width: 100%;            width: 100%;

            border-collapse: collapse;            border-collapse: collapse;

            font-size: 10px;            font-size: 10px;

            table-layout: fixed;            table-layout: fixed;

        }        }



        .items-table thead {        .items-table thead {

            font-weight: bold;            font-weight: bold;

            border-bottom: 2px solid #000;            border-bottom: 2px solid #000;

        }        }



        .items-table th {        .items-table th {

            padding: 3px 1px;            padding: 3px 2px;

            font-weight: bold;            font-weight: bold;

            text-align: left;            text-align: left;

            overflow: hidden;            overflow: hidden;

        }            font-size: 9px;

        }

        .items-table td {

            padding: 3px 1px;        .items-table td {

            vertical-align: top;            padding: 3px 2px;

            overflow: hidden;            vertical-align: top;

            word-wrap: break-word;            overflow: hidden;

        }            word-wrap: break-word;

        }

        .items-table .col-item {

            width: 43%;        .items-table .col-item {

            text-align: left;            width: 42%;

        }            text-align: left;

        }

        .items-table .col-qty {

            width: 12%;        .items-table .col-qty {

            text-align: center;            width: 12%;

            font-weight: bold;            text-align: center;

        }            font-weight: bold;

        }

        .items-table .col-price {

            width: 22%;        .items-table .col-price {

            text-align: right;            width: 23%;

        }            text-align: right;

        }

        .items-table .col-total {

            width: 23%;        .items-table .col-total {

            text-align: right;            width: 23%;

            font-weight: bold;            text-align: right;

        }            font-weight: bold;

        }

        .items-table tbody tr {

            border-bottom: 1px dotted #ccc;        .items-table tbody tr {

        }            border-bottom: 1px dotted #ccc;

        }

        .items-table tbody tr:last-child {

            border-bottom: 2px solid #000;        .items-table tbody tr:last-child {

        }            border-bottom: 2px solid #000;

        }

        .item-discount {

            font-size: 8px;        .item-discount {

            color: #555;            font-size: 7px;

            margin-top: 2px;            color: #555;

            font-style: italic;            margin-left: 3px;

            display: block;            font-style: italic;

        }        }



        /* Totals */        /* Totals */

        .totals-section {        .totals-section {

            margin-top: 8px;            margin-top: 6px;

            padding-top: 6px;            padding-top: 5px;

            border-top: 1px dashed #000;            border-top: 1px dashed #000;

        }        }



        .totals-table {        .totals-table {

            width: 100%;            width: 100%;

            border-collapse: collapse;            border-collapse: collapse;

            font-size: 10px;            font-size: 10px;

            table-layout: fixed;            table-layout: fixed;

        }        }



        .totals-table td {        .totals-table td {

            padding: 3px 0;            padding: 3px 2px;

            overflow: hidden;            overflow: hidden;

        }        }



        .totals-table .total-label {        .totals-table .total-label {

            font-weight: bold;            font-weight: bold;

            width: 60%;            width: 60%;

            text-align: left;            text-align: left;

        }        }



        .totals-table .total-value {        .totals-table .total-value {

            width: 40%;            width: 40%;

            text-align: right;            text-align: right;

            font-weight: bold;            font-weight: bold;

        }        }

        

        .grand-total {        .grand-total {

            font-size: 13px;            font-size: 13px;

            font-weight: bold;            font-weight: bold;

            margin-top: 6px;            margin-top: 5px;

            padding-top: 6px;            padding-top: 5px;

            border-top: 3px double #000;            border-top: 3px double #000;

        }        }

                

        .grand-total .total-label {        .grand-total .total-label {

            font-size: 12px;            font-size: 12px;

        }        }

                

        .grand-total .total-value {        .grand-total .total-value {

            font-size: 14px;            font-size: 14px;

        }        }

        .grand-total .total-value {

        /* Customer Info */            font-size: 14px;

        .customer-section {        }

            margin: 8px 0;

            padding: 5px;        /* Customer Info */

            background-color: #f5f5f5;        .customer-section {

            border: 1px solid #ddd;            margin: 6px 0;

        }            padding: 4px;

            background-color: #f5f5f5;

        .customer-title {            border: 1px solid #ddd;

            font-weight: bold;        }

            font-size: 9px;

            margin-bottom: 4px;        .customer-title {

            text-transform: uppercase;            font-weight: bold;

        }            font-size: 9px;

            margin-bottom: 3px;

        /* Notes */            text-transform: uppercase;

        .notes-section {        }

            margin: 8px 0;

            padding: 5px;        /* Notes */

            background-color: #fffbf0;        .notes-section {

            border: 1px solid #e0e0e0;            margin: 6px 0;

            font-size: 9px;            padding: 4px;

        }            background-color: #fffbf0;

            border: 1px solid #e0e0e0;

        .notes-title {            font-size: 8px;

            font-weight: bold;        }

            margin-bottom: 3px;

        }        .notes-title {

            font-weight: bold;

        /* Barcode */            margin-bottom: 2px;

        .barcode-section {        }

            text-align: center;

            margin: 10px 0;        /* Barcode */

        }        .barcode-section {

            text-align: center;

        .barcode-img {            margin: 8px 0;

            max-width: 70mm;        }

            height: auto;

        }        .barcode-img {

            max-width: 60mm;

        .barcode-text {            height: auto;

            font-size: 8px;        }

            margin-top: 3px;

            letter-spacing: 1px;        .barcode-text {

        }            font-size: 7px;

            margin-top: 2px;

        /* Footer */            letter-spacing: 1px;

        .footer {        }

            text-align: center;

            margin-top: 10px;        /* Footer */

            padding-top: 8px;        .footer {

            border-top: 2px dashed #000;            text-align: center;

        }            margin-top: 8px;

            padding-top: 6px;

        .thank-you {            border-top: 2px dashed #000;

            font-size: 13px;        }

            font-weight: bold;

            margin-bottom: 5px;        .thank-you {

            letter-spacing: 1px;            font-size: 12px;

        }            font-weight: bold;

            margin-bottom: 4px;

        .footer-msg {            letter-spacing: 1px;

            font-size: 10px;        }

            margin-bottom: 6px;

        }        .footer-msg {

            font-size: 9px;

        .footer-info {            margin-bottom: 5px;

            font-size: 8px;        }

            color: #666;

            line-height: 1.4;        .footer-info {

        }            font-size: 7px;

            color: #666;

        .bold {            line-height: 1.3;

            font-weight: bold;        }

        }

    </style>        .bold {

</head>            font-weight: bold;

<body>        }

    <div class="receipt-container">    </style>

        {{-- Header --}}</head>

        <div class="header"><body>

            <div class="store-name">{{ $company['name'] }}</div>    <div class="receipt-container">

            <div class="store-info">        {{-- Header --}}

                {{ $company['address'] }}<br>        <div class="header">

                {{ $company['city'] }}<br>            <div class="store-name">{{ $company['name'] }}</div>

                Tel: {{ $company['phone'] }}<br>            <div class="store-info">

                {{ $company['email'] }}                {{ $company['address'] }}<br>

            </div>                {{ $company['city'] }}<br>

        </div>                Tel: {{ $company['phone'] }}<br>

                {{ $company['email'] }}

        {{-- Receipt Info --}}            </div>

        <div class="receipt-info">        </div>

            <table class="info-table">

                <tr>        {{-- Receipt Info --}}

                    <td class="info-label">Receipt #:</td>        <div class="receipt-info">

                    <td class="info-value">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>            <table class="info-table">

                </tr>                <tr>

                <tr>                    <td class="info-label">Receipt #:</td>

                    <td class="info-label">Date:</td>                    <td class="info-value">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>

                    <td class="info-value">{{ $order->created_at->format('M d, Y') }}</td>                </tr>

                </tr>                <tr>

                <tr>                    <td class="info-label">Date:</td>

                    <td class="info-label">Time:</td>                    <td class="info-value">{{ $order->created_at->format('M d, Y') }}</td>

                    <td class="info-value">{{ $order->created_at->format('h:i A') }}</td>                </tr>

                </tr>                <tr>

                @if($order->day)                    <td class="info-label">Time:</td>

                <tr>                    <td class="info-value">{{ $order->created_at->format('h:i A') }}</td>

                    <td class="info-label">Day #:</td>                </tr>

                    <td class="info-value">{{ $order->day->id }}</td>                @if($order->day)

                </tr>                <tr>

                @endif                    <td class="info-label">Day #:</td>

                <tr>                    <td class="info-value">{{ $order->day->id }}</td>

                    <td class="info-label">Cashier:</td>                </tr>

                    <td class="info-value">{{ config('cashier.admin.email', 'Admin') }}</td>                @endif

                </tr>                <tr>

                @if($order->payment_method)                    <td class="info-label">Cashier:</td>

                <tr>                    <td class="info-value">{{ config('cashier.admin.email', 'Admin') }}</td>

                    <td class="info-label">Payment:</td>                </tr>

                    <td class="info-value">{{ strtoupper($order->payment_method) }}</td>                @if($order->payment_method)

                </tr>                <tr>

                @endif                    <td class="info-label">Payment:</td>

            </table>                    <td class="info-value">{{ strtoupper($order->payment_method) }}</td>

        </div>                </tr>

                @endif

        <div class="divider-bold"></div>            </table>

        </div>

        {{-- Items --}}

        <div class="items-section">        <div class="divider-bold"></div>

            <table class="items-table">

                <thead>        {{-- Items --}}

                    <tr>        <div class="items-section">

                        <th class="col-item">ITEM</th>            <table class="items-table">

                        <th class="col-qty">QTY</th>                <thead>

                        <th class="col-price">PRICE</th>                    <tr>

                        <th class="col-total">TOTAL</th>                        <th class="col-item">ITEM</th>

                    </tr>                        <th class="col-qty">QTY</th>

                </thead>                        <th class="col-price">PRICE</th>

                <tbody>                        <th class="col-total">TOTAL</th>

                    @php                    </tr>

                        // Group items by item ID and sum quantities                </thead>

                        $groupedItems = $order->items->groupBy(function($orderItem) {                <tbody>

                            return $orderItem->itemUnit->item->id;                    @php

                        })->map(function($items) {                        // Group items by item ID and sum quantities

                            $firstItem = $items->first();                        $groupedItems = $order->items->groupBy(function($orderItem) {

                            $item = $firstItem->itemUnit->item;                            return $orderItem->itemUnit->item->id;

                            $totalQuantity = $items->sum('quantity');                        })->map(function($items) {

                            $totalAmount = $items->sum('total');                            $firstItem = $items->first();

                            $totalDiscountAmount = $items->sum('discount_amount');                            $item = $firstItem->itemUnit->item;

                            $avgDiscountPercentage = $items->avg('discount_percentage');                            $totalQuantity = $items->sum('quantity');

                                                        $totalAmount = $items->sum('total');

                            return (object)[                            $totalDiscountAmount = $items->sum('discount_amount');

                                'name' => $item->name,                            $avgDiscountPercentage = $items->avg('discount_percentage');

                                'quantity' => $totalQuantity,                            

                                'price' => $firstItem->price,                            return (object)[

                                'total' => $totalAmount,                                'name' => $item->name,

                                'discount_percentage' => $avgDiscountPercentage,                                'quantity' => $totalQuantity,

                                'discount_amount' => $totalDiscountAmount,                                'price' => $firstItem->price,

                            ];                                'total' => $totalAmount,

                        });                                'discount_percentage' => $avgDiscountPercentage,

                    @endphp                                'discount_amount' => $totalDiscountAmount,

                                                ];

                    @foreach($groupedItems as $item)                        });

                        <tr>                    @endphp

                            <td class="col-item">                    

                                {{ $item->name }}                    @foreach($groupedItems as $item)

                                @if($item->discount_percentage > 0)                        <tr>

                                <span class="item-discount">Disc {{ number_format($item->discount_percentage, 0) }}% (-${{ number_format($item->discount_amount, 2) }})</span>                            <td class="col-item">

                                @endif                                {{ $item->name }}

                            </td>                                @if($item->discount_percentage > 0)

                            <td class="col-qty">{{ $item->quantity }}</td>                                <br><span class="item-discount">- Discount {{ number_format($item->discount_percentage, 0) }}% (-${{ number_format($item->discount_amount, 2) }})</span>

                            <td class="col-price">${{ number_format($item->price, 2) }}</td>                                @endif

                            <td class="col-total">${{ number_format($item->total, 2) }}</td>                            </td>

                        </tr>                            <td class="col-qty">{{ $item->quantity }}</td>

                    @endforeach                            <td class="col-price">${{ number_format($item->price, 2) }}</td>

                </tbody>                            <td class="col-total">${{ number_format($item->total, 2) }}</td>

            </table>                        </tr>

        </div>                    @endforeach

                </tbody>

        {{-- Totals --}}            </table>

        <div class="totals-section">        </div>

            <table class="totals-table">        {{-- Totals --}}

                <tr>        <div class="totals-section">

                    <td class="total-label">Subtotal:</td>            <table class="totals-table">

                    <td class="total-value">${{ number_format($order->subtotal ?? $order->items->sum('total'), 2) }}</td>                <tr>

                </tr>                    <td class="total-label">Subtotal:</td>

                    <td class="total-value">${{ number_format($order->subtotal ?? $order->items->sum('total'), 2) }}</td>

                @if($order->discount_percentage > 0)                </tr>

                <tr>

                    <td class="total-label">Discount ({{ $order->discount_percentage }}%):</td>                @if($order->discount_percentage > 0)

                    <td class="total-value">-${{ number_format($order->discount_amount, 2) }}</td>                <tr>

                </tr>                    <td class="total-label">Discount ({{ $order->discount_percentage }}%):</td>

                @endif                    <td class="total-value">-${{ number_format($order->discount_amount, 2) }}</td>

                </tr>

                @if($order->tax_percentage > 0)                @endif

                <tr>

                    <td class="total-label">Tax ({{ $order->tax_percentage }}%):</td>                @if($order->tax_percentage > 0)

                    <td class="total-value">${{ number_format($order->tax_amount, 2) }}</td>                <tr>

                </tr>                    <td class="total-label">Tax ({{ $order->tax_percentage }}%):</td>

                @endif                    <td class="total-value">${{ number_format($order->tax_amount, 2) }}</td>

                </tr>

                <tr class="grand-total">                @endif

                    <td class="total-label">TOTAL:</td>

                    <td class="total-value">${{ number_format($order->total, 2) }}</td>                <tr class="grand-total">

                </tr>                    <td class="total-label">TOTAL:</td>

            </table>                    <td class="total-value">${{ number_format($order->total, 2) }}</td>

        </div>                </tr>

            </table>

        {{-- Customer Info --}}        </div>  <div class="total-label">TOTAL:</div>

        @if($order->customer_name || $order->customer_phone)                <div class="total-value">${{ number_format($order->total, 2) }}</div>

        <div class="divider"></div>            </div>

        <div class="customer-section">        {{-- Customer Info --}}

            <div class="customer-title">Customer Information</div>        @if($order->customer_name || $order->customer_phone)

            <table class="info-table">        <div class="divider"></div>

                @if($order->customer_name)        <div class="customer-section">

                <tr>            <div class="customer-title">Customer Information</div>

                    <td class="info-label">Name:</td>            <table class="info-table">

                    <td class="info-value">{{ $order->customer_name }}</td>                @if($order->customer_name)

                </tr>                <tr>

                @endif                    <td class="info-label">Name:</td>

                @if($order->customer_phone)                    <td class="info-value">{{ $order->customer_name }}</td>

                <tr>                </tr>

                    <td class="info-label">Phone:</td>                @endif

                    <td class="info-value">{{ $order->customer_phone }}</td>                @if($order->customer_phone)

                </tr>                <tr>

                @endif                    <td class="info-label">Phone:</td>

                @if($order->customer_email)                    <td class="info-value">{{ $order->customer_phone }}</td>

                <tr>                </tr>

                    <td class="info-label">Email:</td>                @endif

                    <td class="info-value">{{ $order->customer_email }}</td>                @if($order->customer_email)

                </tr>                <tr>

                @endif                    <td class="info-label">Email:</td>

            </table>                    <td class="info-value">{{ $order->customer_email }}</td>

        </div>                </tr>

        @endif                @endif

            </table>

        {{-- Notes --}}        </div>

        @if($order->notes)        @endifndif

        <div class="divider"></div>        </div>

        <div class="notes-section">        @endif

            <div class="notes-title">Notes:</div>

            <div>{{ $order->notes }}</div>        {{-- Notes --}}

        </div>        @if($order->notes)

        @endif        <div class="divider"></div>

        <div class="notes-section">

        {{-- Barcode --}}            <div class="notes-title">Notes:</div>

        <div class="divider"></div>            <div>{{ $order->notes }}</div>

        <div class="barcode-section">        </div>

            @php        @endif

                $orderBarcode = 'ORD' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

                $barcodeBase64 = \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($orderBarcode, 'C128', 2, 40);        {{-- Barcode --}}

            @endphp        <div class="divider"></div>

            <img src="data:image/png;base64,{{ $barcodeBase64 }}"         <div class="barcode-section">

                 alt="order barcode"             @php

                 class="barcode-img" />                $orderBarcode = 'ORD' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

            <div class="barcode-text">{{ $orderBarcode }}</div>                $barcodeBase64 = \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($orderBarcode, 'C128', 2, 40);

        </div>            @endphp

            <img src="data:image/png;base64,{{ $barcodeBase64 }}" 

        {{-- Footer --}}                 alt="order barcode" 

        <div class="footer">                 class="barcode-img" />

            <div class="thank-you">THANK YOU!</div>            <div class="barcode-text">{{ $orderBarcode }}</div>

            <div class="footer-msg">Please come again</div>        </div>

            <div class="divider"></div>

            <div class="footer-info">        {{-- Footer --}}

                Printed: {{ now()->format('M d, Y h:i A') }}<br>        <div class="footer">

                Status: {{ strtoupper($order->status) }}<br>            <div class="thank-you">THANK YOU!</div>

                {{ config('app.name', 'Lumi POS') }}            <div class="footer-msg">Please come again</div>

            </div>            <div class="divider"></div>

        </div>            <div class="footer-info">

    </div>                Printed: {{ now()->format('M d, Y h:i A') }}<br>

</body>                Status: {{ strtoupper($order->status) }}<br>

</html>                {{ config('app.name', 'Lumi POS') }}

            </div>
        </div>
    </div>
</body>
</html>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="store-name">{{ $company['name'] }}</div>
            <div class="store-info">
                {{ $company['address'] }}<br>
                {{ $company['city'] }}<br>
                Tel: {{ $company['phone'] }}<br>
                {{ $company['email'] }}
            </div>
        </div>

        <div class="receipt-info">
            <div class="receipt-info-line">
                <span class="label">Receipt #:</span>
                <span>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="receipt-info-line">
                <span class="label">Date:</span>
                <span>{{ $order->created_at->format('M d, Y') }}</span>
            </div>
            <div class="receipt-info-line">
                <span class="label">Time:</span>
                <span>{{ $order->created_at->format('h:i A') }}</span>
            </div>
            @if($order->day)
            <div class="receipt-info-line">
                <span class="label">Day #:</span>
                <span>{{ $order->day->id }}</span>
            </div>
            @endif
            <div class="receipt-info-line">
                <span class="label">Cashier:</span>
                <span>{{ config('cashier.admin.email', 'Admin') }}</span>
            </div>
            @if($order->payment_method)
            <div class="receipt-info-line">
                <span class="label">Payment:</span>
                <span>{{ strtoupper($order->payment_method) }}</span>
            </div>
            @endif
        </div>

        <div class="divider-bold"></div>

        <div class="items-section">
            <div class="items-table-header">
                <div class="col-item">ITEM</div>
                <div class="col-qty">QTY</div>
                <div class="col-price">PRICE</div>
                <div class="col-total">TOTAL</div>
            </div>

            @foreach($order->items as $orderItem)
                @php
                    $item = $orderItem->itemUnit->item;
                @endphp
                <div class="item-row">
                    <div style="display: flex; justify-content: space-between;">
                        <div class="col-item">{{ $item->name }}</div>
                        <div class="col-qty" style="text-align: center;">{{ $orderItem->quantity }}</div>
                        <div class="col-price" style="text-align: right;">${{ number_format($orderItem->price, 2) }}</div>
                        <div class="col-total" style="text-align: right; font-weight: bold;">${{ number_format($orderItem->total, 2) }}</div>
                    </div>
                    @if($orderItem->discount_percentage > 0)
                    <div class="small" style="margin-left: 5px; color: #666;">
                        Discount: {{ $orderItem->discount_percentage }}% (-${{ number_format($orderItem->discount_amount, 2) }})
                    </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <div class="totals-section">
            <div class="total-line">
                <span class="total-label">Subtotal:</span>
                <span>${{ number_format($order->subtotal ?? $order->items->sum('total'), 2) }}</span>
            </div>

            @if($order->discount_percentage > 0)
            <div class="total-line">
                <span class="total-label">Discount ({{ $order->discount_percentage }}%):</span>
                <span>-${{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif

            @if($order->tax_percentage > 0)
            <div class="total-line">
                <span class="total-label">Tax ({{ $order->tax_percentage }}%):</span>
                <span>${{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @endif

            <div class="total-line grand-total">
                <span>TOTAL:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        @if($order->customer_name || $order->customer_phone)
        <div class="divider"></div>
        <div class="receipt-info">
            <div class="bold" style="margin-bottom: 3px;">CUSTOMER INFO:</div>
            @if($order->customer_name)
            <div class="receipt-info-line">
                <span class="label">Name:</span>
                <span>{{ $order->customer_name }}</span>
            </div>
            @endif
            @if($order->customer_phone)
            <div class="receipt-info-line">
                <span class="label">Phone:</span>
                <span>{{ $order->customer_phone }}</span>
            </div>
            @endif
            @if($order->customer_email)
            <div class="receipt-info-line small">
                <span class="label">Email:</span>
                <span>{{ $order->customer_email }}</span>
            </div>
            @endif
        </div>
        @endif

        @if($order->notes)
        <div class="divider"></div>
        <div class="notes-section">
            <div class="notes-title">Notes:</div>
            <div>{{ $order->notes }}</div>
        </div>
        @endif

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
