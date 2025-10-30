<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Report</title>
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
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
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

        .card-value.primary {
            color: #8b5cf6;
        }

        .card-value.info {
            color: #3b82f6;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #8b5cf6;
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

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-purple {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .badge-orange {
            background-color: #ffedd5;
            color: #9a3412;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-green {
            color: #10b981;
            font-weight: bold;
        }

        .text-purple {
            color: #8b5cf6;
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

        .category-details {
            margin-top: 30px;
        }

        .category-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fefefe;
            page-break-inside: avoid;
        }

        .category-header {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .category-name {
            font-size: 14px;
            font-weight: bold;
            color: #8b5cf6;
            margin-bottom: 3px;
        }

        .category-slug {
            font-size: 8px;
            color: #6b7280;
            font-family: monospace;
        }

        .items-list {
            margin-top: 10px;
        }

        .item-row {
            display: table;
            width: 100%;
            padding: 5px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            display: table-cell;
            width: 40%;
            font-size: 9px;
            font-weight: 600;
        }

        .item-sku {
            display: table-cell;
            width: 20%;
            font-size: 8px;
            color: #6b7280;
            font-family: monospace;
        }

        .item-price {
            display: table-cell;
            width: 20%;
            font-size: 9px;
            color: #10b981;
            font-weight: bold;
        }

        .item-stock {
            display: table-cell;
            width: 20%;
            font-size: 9px;
            text-align: right;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📁 Categories Report</h1>
        <p>Comprehensive category analysis with inventory breakdown</p>
        <p style="margin-top: 5px;">Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-title">Total Categories</div>
            <div class="card-value primary">{{ number_format($totalCategories) }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">Total Items</div>
            <div class="card-value info">{{ number_format($totalItems) }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">Inventory Value</div>
            <div class="card-value success">${{ number_format($totalInventoryValue, 2) }}</div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-label">Total Available Units</div>
            <div class="stat-value">{{ number_format($totalAvailableUnits) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Sold Units</div>
            <div class="stat-value">{{ number_format($totalSoldUnits) }}</div>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-label">Average Items per Category</div>
            <div class="stat-value">{{ $totalCategories > 0 ? number_format($totalItems / $totalCategories, 1) : 0 }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Average Category Value</div>
            <div class="stat-value">${{ $totalCategories > 0 ? number_format($totalInventoryValue / $totalCategories, 2) : '0.00' }}</div>
        </div>
    </div>

    <!-- Categories Summary Table -->
    <div class="section-title">📊 Categories Overview</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Category Name</th>
                <th style="width: 20%;">Slug</th>
                <th style="width: 12%;">Items</th>
                <th style="width: 13%;">Available</th>
                <th style="width: 10%;">Sold</th>
                <th style="width: 15%;">Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $index => $category)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">{{ $category->name }}</td>
                    <td style="font-family: monospace; font-size: 8px; color: #6b7280;">{{ $category->slug }}</td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ $category->items_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-success">{{ number_format($category->available_stock) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-orange">{{ number_format($category->sold_stock) }}</span>
                    </td>
                    <td class="text-green text-bold">${{ number_format($category->inventory_value, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #9ca3af;">
                        No categories found
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot style="background: #f9fafb; border-top: 2px solid #8b5cf6;">
            <tr>
                <td colspan="3" class="text-bold" style="text-align: right; padding: 12px 8px;">TOTALS:</td>
                <td class="text-center text-bold">{{ number_format($totalItems) }}</td>
                <td class="text-center text-bold">{{ number_format($totalAvailableUnits) }}</td>
                <td class="text-center text-bold">{{ number_format($totalSoldUnits) }}</td>
                <td class="text-bold" style="color: #10b981; font-size: 11px;">${{ number_format($totalInventoryValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Detailed Category Breakdown -->
    @if($categories->count() > 0)
        <div class="page-break"></div>
        <div class="section-title">📦 Detailed Category Breakdown</div>
        
        <div class="category-details">
            @foreach($categories as $category)
                <div class="category-card">
                    <div class="category-header">
                        <div class="category-name">{{ $category->name }}</div>
                        <div class="category-slug">{{ $category->slug }}</div>
                    </div>

                    <div class="stats-row" style="margin-bottom: 10px;">
                        <div class="stat-box">
                            <div class="stat-label">Total Items</div>
                            <div class="stat-value" style="font-size: 12px;">{{ $category->items_count }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Category Value</div>
                            <div class="stat-value" style="font-size: 12px; color: #10b981;">${{ number_format($category->inventory_value, 2) }}</div>
                        </div>
                    </div>

                    @if($category->description)
                        <div style="padding: 8px; background: #f9fafb; border-radius: 4px; margin-bottom: 10px;">
                            <div style="font-size: 8px; color: #6b7280; margin-bottom: 2px;">Description:</div>
                            <div style="font-size: 9px; color: #374151;">{{ $category->description }}</div>
                        </div>
                    @endif

                    @if($category->items->count() > 0)
                        <div class="items-list">
                            <div style="font-size: 9px; font-weight: bold; color: #6b7280; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb;">
                                Items in this category:
                            </div>
                            @foreach($category->items as $item)
                                @php
                                    $itemAvailable = $item->units()->where('status', 'available')->count();
                                    $itemSold = $item->units()->where('status', 'sold')->count();
                                @endphp
                                <div class="item-row">
                                    <div class="item-name">{{ $item->name }}</div>
                                    <div class="item-sku">{{ $item->sku }}</div>
                                    <div class="item-price">${{ number_format($item->price, 2) }}</div>
                                    <div class="item-stock">
                                        @if($itemAvailable > 0)
                                            <span class="badge badge-success">{{ $itemAvailable }} avail</span>
                                        @else
                                            <span class="badge badge-orange">Out of stock</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding: 15px; text-align: center; color: #9ca3af; font-size: 9px; font-style: italic;">
                            No items in this category
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Lumi Cashier System</strong> - Categories Report</p>
        <p>Generated on {{ now()->format('l, F d, Y \a\t h:i A') }}</p>
        <p style="margin-top: 5px;">© {{ date('Y') }} All Rights Reserved</p>
    </div>
</body>
</html>
