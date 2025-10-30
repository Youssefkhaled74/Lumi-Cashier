<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Inventory Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1f2937;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            opacity: 0.95;
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
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-right: 10px;
            background: #f9fafb;
        }

        .summary-card:last-child {
            margin-right: 0;
        }

        .card-title {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .card-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }

        .card-value.success {
            color: #10b981;
        }

        .card-value.warning {
            color: #f59e0b;
        }

        .card-value.danger {
            color: #ef4444;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #d1d5db;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #f3f4f6;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-purple {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-green {
            color: #10b981;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }

        .stats-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            background: #fefefe;
        }

        .stat-label {
            font-size: 8px;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📦 Items Inventory Report</h1>
        <p>Complete inventory listing with stock levels and valuations</p>
        <p style="margin-top: 5px;">Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-title">Total Items</div>
            <div class="card-value">{{ number_format($totalItems) }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">Inventory Value</div>
            <div class="card-value success">${{ number_format($totalInventoryValue, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">Available Units</div>
            <div class="card-value">{{ number_format($totalAvailableUnits) }}</div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-label">Total Units Sold</div>
            <div class="stat-value">{{ number_format($totalSoldUnits) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Low Stock Items</div>
            <div class="stat-value warning">{{ $lowStockItems }}</div>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-label">Out of Stock Items</div>
            <div class="stat-value danger">{{ $outOfStockItems }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Average Item Value</div>
            <div class="stat-value">${{ $totalItems > 0 ? number_format($totalInventoryValue / $totalItems, 2) : '0.00' }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="section-title">📋 Complete Items Listing</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Item Name</th>
                <th style="width: 12%;">SKU</th>
                <th style="width: 15%;">Category</th>
                <th style="width: 10%;">Price</th>
                <th style="width: 10%;">Available</th>
                <th style="width: 8%;">Sold</th>
                <th style="width: 15%;">Inventory Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">
                        {{ $item->name }}
                        @if($item->barcode)
                            <br><span style="font-size: 7px; color: #6b7280;">🔲 {{ $item->barcode }}</span>
                        @endif
                    </td>
                    <td style="font-family: monospace;">{{ $item->sku }}</td>
                    <td>
                        <span class="badge badge-purple">{{ $item->category->name ?? 'N/A' }}</span>
                    </td>
                    <td class="text-green">${{ number_format($item->price, 2) }}</td>
                    <td class="text-center">
                        @if($item->available_stock > 10)
                            <span class="badge badge-success">{{ $item->available_stock }}</span>
                        @elseif($item->available_stock > 0)
                            <span class="badge badge-warning">{{ $item->available_stock }}</span>
                        @else
                            <span class="badge badge-danger">{{ $item->available_stock }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ $item->sold_stock }}</span>
                    </td>
                    <td class="text-green text-bold">${{ number_format($item->price * $item->available_stock, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 30px; color: #9ca3af;">
                        No items found in inventory
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot style="background: #f9fafb; border-top: 2px solid #667eea;">
            <tr>
                <td colspan="5" class="text-bold" style="text-align: right; padding: 12px 8px;">TOTAL INVENTORY VALUE:</td>
                <td class="text-center text-bold">{{ number_format($totalAvailableUnits) }}</td>
                <td class="text-center text-bold">{{ number_format($totalSoldUnits) }}</td>
                <td class="text-bold" style="color: #10b981; font-size: 11px;">${{ number_format($totalInventoryValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Stock Status Breakdown -->
    @php
        $inStockItems = $items->filter(fn($item) => $item->available_stock > 10)->count();
        $lowStockCount = $items->filter(fn($item) => $item->available_stock > 0 && $item->available_stock <= 10)->count();
        $outOfStockCount = $items->filter(fn($item) => $item->available_stock == 0)->count();
    @endphp

    <div class="section-title">📊 Stock Status Summary</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Item Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-success">In Stock (>10 units)</span></td>
                <td class="text-center text-bold">{{ $inStockItems }}</td>
                <td class="text-center">{{ $totalItems > 0 ? number_format(($inStockItems / $totalItems) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td><span class="badge badge-warning">Low Stock (1-10 units)</span></td>
                <td class="text-center text-bold">{{ $lowStockCount }}</td>
                <td class="text-center">{{ $totalItems > 0 ? number_format(($lowStockCount / $totalItems) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td><span class="badge badge-danger">Out of Stock</span></td>
                <td class="text-center text-bold">{{ $outOfStockCount }}</td>
                <td class="text-center">{{ $totalItems > 0 ? number_format(($outOfStockCount / $totalItems) * 100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Lumi Cashier System</strong> - Items Inventory Report</p>
        <p>Generated on {{ now()->format('l, F d, Y \a\t h:i A') }}</p>
        <p style="margin-top: 5px;">© {{ date('Y') }} All Rights Reserved</p>
    </div>
</body>
</html>
