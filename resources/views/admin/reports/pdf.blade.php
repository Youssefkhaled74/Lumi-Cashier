<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - {{ $fromDate }} to {{ $toDate }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            margin: -20px -20px 30px -20px;
            border-radius: 0 0 15px 15px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 11px;
            opacity: 0.9;
        }

        .header-info {
            display: table;
            width: 100%;
            margin-top: 15px;
            border-top: 2px solid rgba(255,255,255,0.3);
            padding-top: 15px;
        }

        .header-info-item {
            display: table-cell;
            width: 33.33%;
        }

        .header-info-label {
            font-size: 9px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-info-value {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }

        /* Summary Cards */
        .summary-section {
            margin-bottom: 25px;
        }

        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
        }

        .summary-card:not(:last-child) {
            margin-right: 10px;
        }

        .summary-card-label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .summary-card-value {
            font-size: 22px;
            font-weight: bold;
            color: #2d3748;
        }

        .summary-card.primary .summary-card-value {
            color: #667eea;
        }

        .summary-card.success .summary-card-value {
            color: #48bb78;
        }

        .summary-card.info .summary-card-value {
            color: #4299e1;
        }

        /* Section Titles */
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 3px solid #667eea;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }

        table thead {
            background: #667eea;
            color: white;
        }

        table thead th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        table tbody tr:nth-child(even) {
            background: #f7fafc;
        }

        table tbody td {
            padding: 8px;
            font-size: 9px;
        }

        table tbody tr:hover {
            background: #edf2f7;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-success {
            color: #48bb78;
        }

        .text-primary {
            color: #667eea;
        }

        .text-danger {
            color: #f56565;
        }

        .text-warning {
            color: #ed8936;
        }

        .text-muted {
            color: #a0aec0;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-danger {
            background: #fed7d7;
            color: #742a2a;
        }

        .badge-warning {
            background: #feebc8;
            color: #744210;
        }

        .badge-info {
            background: #bee3f8;
            color: #2c5282;
        }

        /* Top Selling Items */
        .top-items-grid {
            display: table;
            width: 100%;
        }

        .top-item {
            display: table-row;
        }

        .top-item-rank {
            display: table-cell;
            width: 30px;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #667eea;
        }

        .top-item-details {
            display: table-cell;
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .top-item-name {
            font-weight: bold;
            font-size: 10px;
            color: #2d3748;
        }

        .top-item-sku {
            font-size: 8px;
            color: #718096;
            margin-top: 2px;
        }

        .top-item-stats {
            display: table-cell;
            width: 150px;
            padding: 8px;
            text-align: right;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Inventory Status */
        .inventory-summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .inventory-item {
            display: table-cell;
            width: 33.33%;
            padding: 12px;
            background: #f7fafc;
            border-left: 4px solid #cbd5e0;
            text-align: center;
        }

        .inventory-item.low-stock {
            border-left-color: #ed8936;
            background: #fffaf0;
        }

        .inventory-item.out-stock {
            border-left-color: #f56565;
            background: #fff5f5;
        }

        .inventory-item-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .inventory-item-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #718096;
            letter-spacing: 0.5px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 8px;
            color: #a0aec0;
        }

        .footer-info {
            margin-bottom: 5px;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* No Data Message */
        .no-data {
            text-align: center;
            padding: 40px;
            color: #a0aec0;
        }

        .no-data-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .no-data-text {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 Sales & Inventory Report</h1>
            <p>Comprehensive business analytics and performance metrics</p>
            
            <div class="header-info">
                <div class="header-info-item">
                    <div class="header-info-label">Report Period</div>
                    <div class="header-info-value">{{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</div>
                </div>
                <div class="header-info-item">
                    <div class="header-info-label">Total Days</div>
                    <div class="header-info-value">{{ $reportData['summary']['date_range']['days'] }} Days</div>
                </div>
                <div class="header-info-item">
                    <div class="header-info-label">Generated</div>
                    <div class="header-info-value">{{ $generatedAt->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="summary-section">
            <div class="summary-cards">
                <div class="summary-card primary">
                    <div class="summary-card-label">Total Orders</div>
                    <div class="summary-card-value">{{ number_format($reportData['summary']['total_orders']) }}</div>
                </div>
                <div class="summary-card success" style="margin-left: 10px;">
                    <div class="summary-card-label">Total Revenue</div>
                    <div class="summary-card-value">${{ number_format($reportData['summary']['total_sales'], 2) }}</div>
                </div>
                <div class="summary-card info" style="margin-left: 10px;">
                    <div class="summary-card-label">Average Order</div>
                    <div class="summary-card-value">${{ number_format($reportData['summary']['average_order_value'], 2) }}</div>
                </div>
            </div>
        </div>

        @if($reportData['summary']['total_orders'] > 0)
            <!-- Top Selling Items -->
            @if($reportData['top_selling_items']->isNotEmpty())
            <h2 class="section-title">🏆 Top Selling Products</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 35%">Product Name</th>
                        <th style="width: 20%">SKU</th>
                        <th style="width: 20%" class="text-center">Quantity Sold</th>
                        <th style="width: 20%" class="text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['top_selling_items']->take(10) as $index => $item)
                    <tr>
                        <td class="text-center font-bold text-primary">{{ $index + 1 }}</td>
                        <td class="font-bold">{{ $item['name'] }}</td>
                        <td class="text-muted">{{ $item['sku'] }}</td>
                        <td class="text-center font-bold">{{ $item['quantity_sold'] }}</td>
                        <td class="text-right font-bold text-success">${{ number_format($item['revenue'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <!-- Inventory Status -->
            <h2 class="section-title">📦 Inventory Overview</h2>
            <div class="inventory-summary">
                <div class="inventory-item">
                    <div class="inventory-item-value text-primary">${{ number_format($reportData['inventory']['total_inventory_value'], 2) }}</div>
                    <div class="inventory-item-label">Total Inventory Value</div>
                </div>
                <div class="inventory-item low-stock" style="margin-left: 10px;">
                    <div class="inventory-item-value text-warning">{{ $reportData['inventory']['low_stock']->count() }}</div>
                    <div class="inventory-item-label">Low Stock Items</div>
                </div>
                <div class="inventory-item out-stock" style="margin-left: 10px;">
                    <div class="inventory-item-value text-danger">{{ $reportData['inventory']['out_of_stock']->count() }}</div>
                    <div class="inventory-item-label">Out of Stock</div>
                </div>
            </div>

            <!-- Orders Detail -->
            <div class="page-break"></div>
            
            <h2 class="section-title">📋 Orders Detail</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%">Order #</th>
                        <th style="width: 20%">Date & Time</th>
                        <th style="width: 10%">Day #</th>
                        <th style="width: 15%" class="text-center">Items</th>
                        <th style="width: 20%" class="text-right">Total Amount</th>
                        <th style="width: 25%">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="font-bold text-primary">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td class="text-muted">Day #{{ $order->day_id }}</td>
                        <td class="text-center">{{ $order->items->count() }} item(s)</td>
                        <td class="text-right font-bold text-success">${{ number_format($order->total, 2) }}</td>
                        <td class="text-muted">{{ Str::limit($order->notes ?? 'N/A', 30) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Orders Items Breakdown -->
            <h2 class="section-title">🛒 Order Items Breakdown</h2>
            @foreach($orders->take(50) as $order)
                <div style="margin-bottom: 15px; padding: 10px; background: #f7fafc; border-left: 4px solid #667eea;">
                    <div style="margin-bottom: 8px;">
                        <span class="font-bold">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-muted" style="margin-left: 10px;">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        <span class="font-bold text-success" style="float: right;">${{ number_format($order->total, 2) }}</span>
                    </div>
                    <table style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="width: 50%">Item</th>
                                <th style="width: 15%" class="text-center">Qty</th>
                                <th style="width: 20%" class="text-right">Unit Price</th>
                                <th style="width: 15%" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $orderItem)
                            <tr>
                                <td>{{ $orderItem->itemUnit->item->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $orderItem->quantity }}</td>
                                <td class="text-right">${{ number_format($orderItem->price, 2) }}</td>
                                <td class="text-right font-bold">${{ number_format($orderItem->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

        @else
            <!-- No Data -->
            <div class="no-data">
                <div class="no-data-icon">📊</div>
                <div class="no-data-text">No orders found in the selected date range</div>
                <div class="text-muted" style="margin-top: 8px;">{{ $reportData['summary']['date_range']['from'] }} - {{ $reportData['summary']['date_range']['to'] }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                <strong>Cashier Pro</strong> - Point of Sale System
            </div>
            <div class="footer-info">
                Report generated on {{ $generatedAt->format('l, F j, Y \a\t g:i A') }}
            </div>
            <div class="footer-info">
                © {{ date('Y') }} All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
