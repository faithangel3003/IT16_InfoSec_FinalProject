<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            margin: 50px 40px 60px 40px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background: #2c3e50;
            color: white;
        }
        
        .header h1 {
            color: white;
            font-size: 22px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header p {
            color: #d4a745;
            font-size: 12px;
        }
        
        .header-underline {
            width: 60px;
            height: 3px;
            background: #d4a745;
            margin: 10px auto 0;
        }
        
        .report-info {
            margin-bottom: 20px;
            padding: 15px 20px;
            background: #f8f9fa;
            display: table;
            width: 100%;
        }
        
        .report-info-col {
            display: table-cell;
            width: 50%;
        }
        
        .report-info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .report-info p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .section-title {
            color: #333;
            padding: 8px 0 8px 15px;
            margin: 20px 0 15px 0;
            font-size: 13px;
            font-weight: bold;
            border-left: 4px solid #d4a745;
            background: #f8f9fa;
        }
        
        .section-subtitle {
            font-weight: bold;
            font-size: 12px;
            color: #333;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        
        .summary-card {
            display: table-cell;
            text-align: center;
            padding: 15px 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }
        
        .summary-card h3 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .summary-card p {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .summary-card-success { border-top: 3px solid #17a2b8; }
        .summary-card-success h3 { color: #17a2b8; }
        .summary-card-warning { border-top: 3px solid #d4a745; }
        .summary-card-warning h3 { color: #d4a745; }
        .summary-card-danger { border-top: 3px solid #dc3545; }
        .summary-card-danger h3 { color: #dc3545; }
        .summary-card-primary { border-top: 3px solid #007bff; }
        .summary-card-primary h3 { color: #007bff; }
        .summary-card-info { border-top: 3px solid #17a2b8; }
        .summary-card-info h3 { color: #17a2b8; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        table th {
            background: #343a40;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .badge-success { background: #17a2b8; color: white; }
        .badge-warning { background: #d4a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-neutral { background: #6c757d; color: white; }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 11px;
        }
        
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TriadCo Hotel Inventory System</h1>
        <p>{{ $reportTitle }}</p>
        <div class="header-underline"></div>
    </div>
    
    <div class="report-info">
        <div class="report-info-col">
            <div class="report-info-label">Report Period</div>
            <div>{{ $dateFrom }} to {{ $dateTo }}</div>
        </div>
        <div class="report-info-col">
            <div class="report-info-label">Generated At</div>
            <div>{{ $generatedAt }}</div>
        </div>
    </div>

    @if($reportType == 'all')
        <!-- COMPLETE INVENTORY REPORT -->
        
        <div class="section-title">Inventory Summary</div>
        <div class="summary-cards">
            <div class="summary-card summary-card-primary">
                <h3>{{ $totalItems }}</h3>
                <p>Total Items</p>
            </div>
            <div class="summary-card summary-card-success">
                <h3>{{ number_format($totalStock) }}</h3>
                <p>Total Stock</p>
            </div>
            <div class="summary-card summary-card-warning">
                <h3>₱{{ number_format($totalValue, 2) }}</h3>
                <p>Total Value</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th class="text-center">Stock</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            @if($item->in_stock == 0)
                                <span class="badge badge-danger">{{ $item->in_stock }}</span>
                            @elseif($item->in_stock <= 10)
                                <span class="badge badge-warning">{{ $item->in_stock }}</span>
                            @else
                                {{ $item->in_stock }}
                            @endif
                        </td>
                        <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                        <td class="text-right font-bold">₱{{ number_format($item->in_stock * $item->price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-break"></div>

        <div class="section-title">Stock Movement</div>
        <div class="summary-cards">
            <div class="summary-card summary-card-success">
                <h3>{{ number_format($totalStockIn) }}</h3>
                <p>Stock In</p>
            </div>
            <div class="summary-card summary-card-danger">
                <h3>{{ number_format($totalStockOut) }}</h3>
                <p>Stock Out</p>
            </div>
        </div>

        <p class="section-subtitle">Stock In Records</p>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Supplier</th>
                    <th class="text-center">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockIns as $stockIn)
                    <tr>
                        <td>{{ $stockIn->stockin_date }}</td>
                        <td>{{ $stockIn->item->name ?? 'N/A' }}</td>
                        <td>{{ $stockIn->supplier->name ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge badge-success">+{{ $stockIn->quantity }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No stock-in records.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p class="section-subtitle">Stock Out Records</p>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Room</th>
                    <th class="text-center">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOuts as $stockOut)
                    <tr>
                        <td>{{ $stockOut->created_at->format('Y-m-d') }}</td>
                        <td>{{ $stockOut->item->name ?? 'N/A' }}</td>
                        <td>{{ $stockOut->room->room_number ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge badge-danger">-{{ $stockOut->quantity }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No stock-out records.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-break"></div>
        <div class="section-title">Low Stock Alert</div>
        <div class="summary-cards">
            <div class="summary-card summary-card-danger">
                <h3>{{ $outOfStockItems->count() }}</h3>
                <p>Out of Stock</p>
            </div>
            <div class="summary-card summary-card-warning">
                <h3>{{ $lowStockItems->count() }}</h3>
                <p>Low Stock</p>
            </div>
        </div>

        @if($lowStockItems->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->in_stock }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item->in_stock == 0 ? 'badge-danger' : 'badge-warning' }}">
                                {{ $item->in_stock == 0 ? 'Out of Stock' : 'Low Stock' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="text-align: center; color: #17a2b8;">All items are well stocked!</p>
        @endif

        <div class="section-title">Supplier Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Contact Number</th>
                    <th class="text-center">Deliveries</th>
                    <th class="text-center">Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_person }}</td>
                        <td>{{ $supplier->number }}</td>
                        <td class="text-center">{{ $supplier->stockInCount }}</td>
                        <td class="text-center font-bold">{{ number_format($supplier->totalQuantitySupplied) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No suppliers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-break"></div>

        <div class="section-title">Room Inventory</div>
        <div class="summary-cards">
            <div class="summary-card summary-card-primary">
                <h3>{{ $totalRooms }}</h3>
                <p>Total Rooms</p>
            </div>
            <div class="summary-card summary-card-success">
                <h3>{{ $occupiedRooms }}</h3>
                <p>Occupied</p>
            </div>
            <div class="summary-card summary-card-warning">
                <h3>{{ $emptyRooms }}</h3>
                <p>Empty</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Items Assigned</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ $room->type->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $room->status == 'occupied' ? 'badge-success' : 'badge-neutral' }}">
                                {{ ucfirst($room->status) }}
                            </span>
                        </td>
                        <td class="text-center">{{ $room->items->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No rooms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Category Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th class="text-center">Items Count</th>
                    <th class="text-center">Total Stock</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categoriesBreakdown as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-center">{{ $category->items_count }}</td>
                        <td class="text-center">{{ number_format($category->totalStock) }}</td>
                        <td class="text-right font-bold">₱{{ number_format($category->totalValue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'inventory_summary')
        <div class="section-title">Inventory Summary</div>
        <div class="summary-cards">
            <div class="summary-card summary-card-primary">
                <h3>{{ $totalItems }}</h3>
                <p>Total Items</p>
            </div>
            <div class="summary-card summary-card-success">
                <h3>{{ number_format($totalStock) }}</h3>
                <p>Total Stock</p>
            </div>
            <div class="summary-card summary-card-warning">
                <h3>₱{{ number_format($totalValue, 2) }}</h3>
                <p>Total Value</p>
            </div>
        </div>

        <p class="section-subtitle">Item Details</p>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th class="text-center">Stock</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            @if($item->in_stock == 0)
                                <span class="badge badge-danger">{{ $item->in_stock }}</span>
                            @elseif($item->in_stock <= 10)
                                <span class="badge badge-warning">{{ $item->in_stock }}</span>
                            @else
                                {{ $item->in_stock }}
                            @endif
                        </td>
                        <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                        <td class="text-right font-bold">₱{{ number_format($item->in_stock * $item->price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'stock_movement')
        <div class="section-title">Stock Movement</div>
        <div class="summary-cards">
            <div class="summary-card summary-card-success">
                <h3>{{ number_format($totalStockIn) }}</h3>
                <p>Stock In</p>
            </div>
            <div class="summary-card summary-card-danger">
                <h3>{{ number_format($totalStockOut) }}</h3>
                <p>Stock Out</p>
            </div>
        </div>

        <p class="section-subtitle">Stock In Records</p>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Supplier</th>
                    <th class="text-center">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockIns as $stockIn)
                    <tr>
                        <td>{{ $stockIn->stockin_date }}</td>
                        <td>{{ $stockIn->item->name ?? 'N/A' }}</td>
                        <td>{{ $stockIn->supplier->name ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge badge-success">+{{ $stockIn->quantity }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No stock-in records.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p class="section-subtitle">Stock Out Records</p>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Room</th>
                    <th class="text-center">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOuts as $stockOut)
                    <tr>
                        <td>{{ $stockOut->created_at->format('Y-m-d') }}</td>
                        <td>{{ $stockOut->item->name ?? 'N/A' }}</td>
                        <td>{{ $stockOut->room->room_number ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge badge-danger">-{{ $stockOut->quantity }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No stock-out records.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'low_stock')
        <div class="section-title">Low Stock Alert</div>
        
        <div class="alert alert-warning">
            Items with 10 or fewer units in stock require immediate attention.
        </div>
        
        <div class="summary-cards">
            <div class="summary-card summary-card-danger">
                <h3>{{ $outOfStockItems->count() }}</h3>
                <p>Out of Stock</p>
            </div>
            <div class="summary-card summary-card-warning">
                <h3>{{ $lowStockItems->count() }}</h3>
                <p>Low Stock</p>
            </div>
        </div>

        <p class="section-subtitle">Out of Stock Items</p>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th class="text-center">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outOfStockItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge badge-danger">{{ $item->in_stock }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No out of stock items!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p class="section-subtitle">Low Stock Items</p>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th class="text-center">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStockItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge {{ $item->in_stock == 0 ? 'badge-danger' : 'badge-warning' }}">{{ $item->in_stock }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">All items are well stocked!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'supplier_summary')
        <div class="section-title">Supplier Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Contact Number</th>
                    <th class="text-center">Deliveries</th>
                    <th class="text-center">Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_person }}</td>
                        <td>{{ $supplier->number }}</td>
                        <td class="text-center">{{ $supplier->stockInCount }}</td>
                        <td class="text-center font-bold">{{ number_format($supplier->totalQuantitySupplied) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No suppliers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'room_inventory')
        <div class="section-title">Room Inventory</div>
        <div class="summary-cards">
            <div class="summary-card summary-card-primary">
                <h3>{{ $totalRooms }}</h3>
                <p>Total Rooms</p>
            </div>
            <div class="summary-card summary-card-success">
                <h3>{{ $occupiedRooms }}</h3>
                <p>Occupied</p>
            </div>
            <div class="summary-card summary-card-warning">
                <h3>{{ $emptyRooms }}</h3>
                <p>Empty</p>
            </div>
        </div>

        <p class="section-subtitle">Room Details</p>
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Items Assigned</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ $room->type->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $room->status == 'occupied' ? 'badge-success' : 'badge-neutral' }}">
                                {{ ucfirst($room->status) }}
                            </span>
                        </td>
                        <td class="text-center">{{ $room->items->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No rooms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'category_breakdown')
        <div class="section-title">Category Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th class="text-center">Items Count</th>
                    <th class="text-center">Total Stock</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-center">{{ $category->items_count }}</td>
                        <td class="text-center">{{ number_format($category->totalStock) }}</td>
                        <td class="text-right font-bold">₱{{ number_format($category->totalValue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        TriadCo Hotel Inventory System | {{ $reportTitle }} | Generated: {{ $generatedAt }} | Page {PAGE_NUM} of {PAGE_COUNT}
    </div>
</body>
</html>