@php
$lang = app()->getLocale();
$isRtl = $lang === 'ar';
@endphp

<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('pos.receipt') #{{ $order->id }}</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Inter:wght@400;500;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: {{ $isRtl ? "'Tajawal', sans-serif" : "'Inter', sans-serif" }};
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
            background: white;
            padding: 20px;
        }
        
        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .logo {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }
        
        .order-info {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #999;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .label {
            font-weight: 600;
            color: #333;
        }
        
        .value {
            color: #666;
        }
        
        .items {
            margin-bottom: 15px;
        }
        
        .items-header {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #333;
        }
        
        .item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dotted #ccc;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #666;
        }
        
        .item-price {
            font-weight: 600;
            color: #333;
        }
        
        .totals {
            border-top: 2px dashed #333;
            padding-top: 15px;
            margin-bottom: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            padding-top: 10px;
            border-top: 2px solid #333;
            margin-top: 10px;
        }
        
        .grand-total .label {
            font-size: 16px;
        }
        
        .payment-info {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .footer {
            text-align: center;
            border-top: 2px dashed #333;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .thank-you {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .footer-message {
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }
        
        .barcode {
            text-align: center;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }
        
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            
            body {
                padding: 5mm;
                background: white;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            position: fixed;
            bottom: 20px;
            {{ $isRtl ? 'left' : 'right' }}: 20px;
            background: #4F46E5;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .print-button:hover {
            background: #4338CA;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="logo">🛒</div>
            <div class="company-name">Lumi Store</div>
            <div class="company-info">
                {{ config('app.name', 'Lumi Store') }}<br>
                @lang('pos.tax_invoice')<br>
                Tel: +966 XXX XXXX
            </div>
        </div>

        <!-- Order Information -->
        <div class="order-info">
            <div class="info-row">
                <span class="label">@lang('pos.order_number'):</span>
                <span class="value">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="label">@lang('pos.date'):</span>
                <span class="value">{{ $order->created_at->format('Y-m-d') }}</span>
            </div>
            <div class="info-row">
                <span class="label">@lang('pos.time'):</span>
                <span class="value">{{ $order->created_at->format('H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span class="label">@lang('pos.cashier'):</span>
                <span class="value">{{ $order->user->name ?? 'Admin' }}</span>
            </div>
        </div>

        <!-- Items -->
        <div class="items">
            <div class="items-header">
                @lang('pos.items')
            </div>
            
            @foreach($order->orderItems as $item)
            <div class="item">
                <div class="item-name">{{ $item->item->name }}</div>
                <div class="item-details">
                    <span>
                        {{ $item->quantity }} {{ $item->unit_name }} × ${{ number_format($item->unit_price, 2) }}
                    </span>
                    <span class="item-price">${{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                </div>
                @if($item->item->sku)
                <div style="font-size: 11px; color: #999; margin-top: 2px;">
                    @lang('pos.sku'): {{ $item->item->sku }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span class="label">@lang('pos.subtotal'):</span>
                <span class="value">${{ number_format($order->total_amount, 2) }}</span>
            </div>
            
            @if(isset($order->discount) && $order->discount > 0)
            <div class="total-row">
                <span class="label">@lang('pos.discount'):</span>
                <span class="value">-${{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            
            @if(isset($order->tax) && $order->tax > 0)
            <div class="total-row">
                <span class="label">@lang('pos.vat') (15%):</span>
                <span class="value">${{ number_format($order->tax, 2) }}</span>
            </div>
            @endif
            
            <div class="total-row grand-total">
                <span class="label">@lang('pos.total'):</span>
                <span class="value">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        @if($order->notes)
        <div class="payment-info">
            <div class="info-row">
                <span class="label">@lang('pos.notes'):</span>
            </div>
            <div style="font-size: 12px; margin-top: 5px; color: #666;">
                {{ $order->notes }}
            </div>
        </div>
        @endif

        <!-- Barcode (Order ID) -->
        <div class="barcode">
            <div style="font-size: 24px; letter-spacing: 3px; margin-bottom: 5px;">
                *{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}*
            </div>
            <div style="font-size: 10px; color: #999;">
                {{ $order->created_at->format('Y-m-d H:i:s') }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">@lang('pos.thank_you')</div>
            <div class="footer-message">
                @lang('pos.thank_you_message')<br>
                @lang('pos.visit_again')<br><br>
                @lang('pos.powered_by') Lumi POS System
            </div>
        </div>
    </div>

    <!-- Print Button (hidden on print) -->
    <button class="print-button no-print" onclick="window.print()">
        <i style="margin-{{ $isRtl ? 'left' : 'right' }}: 8px;">🖨️</i>
        @lang('pos.print_receipt')
    </button>

    <script>
        // Auto print on load (optional - comment out if not needed)
        // window.onload = function() {
        //     setTimeout(() => window.print(), 500);
        // }
    </script>
</body>
</html>
