@extends('dashboard')

@section('title', 'Monthly Reports - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .monthly-reports-page { padding: 30px; }
        
        .monthly-title {
            color: #1e2a47;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }
        
        /* Filter Section */
        .report-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .filter-left {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .filter-select {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            min-width: 180px;
        }
        
        .filter-date {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .btn-generate-pdf {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }
        
        .btn-generate-pdf:hover {
            background: #c82333;
            color: #fff;
        }
        
        /* Summary Section */
        .summary-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .summary-title {
            color: #1e2a47;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #eee;
        }
        
        .card-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }
        
        .card-icon.red { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .card-icon.green { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .card-icon.blue { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        .card-icon.gold { background: rgba(200, 168, 88, 0.15); color: #c8a858; }
        
        .summary-card h3 {
            color: #1e2a47;
            font-size: 1.8rem;
            margin: 0 0 5px 0;
            font-weight: 700;
        }
        
        .summary-card p {
            color: #6c757d;
            margin: 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Breakdown Section */
        .breakdown-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .breakdown-title {
            color: #1e2a47;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .breakdown-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #eee;
        }
        
        .breakdown-card label {
            color: #6c757d;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 8px;
        }
        
        .breakdown-card h4 {
            color: #1e2a47;
            font-size: 1.5rem;
            margin: 0;
            font-weight: 700;
        }
        
        /* Table Styles */
        .dark-table-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .dark-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .dark-table thead {
            background: #f8f9fa;
        }
        
        .dark-table th {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .dark-table td {
            padding: 12px 15px;
            color: #333;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        
        .dark-table tbody tr:hover {
            background: rgba(200, 168, 88, 0.05);
        }
        
        .badge-stock {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .badge-warning { background: rgba(255, 193, 7, 0.15); color: #d39e00; }
        .badge-danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        /* Pagination Styles */
        .table-pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            padding: 16px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        
        .page-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            background: #fff;
            color: #6c757d;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
        }
        
        .page-btn:hover {
            background: #e9ecef;
        }
        
        .page-btn.active {
            background: #c8a858;
            color: white;
            border-color: #c8a858;
        }
        
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        @media (max-width: 1200px) {
            .summary-cards { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .summary-cards { grid-template-columns: 1fr; }
            .report-filter-bar { flex-direction: column; gap: 15px; }
            .breakdown-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
<div class="monthly-reports-page">
    <h1 class="monthly-title">MONTHLY REPORTS</h1>
    
    <!-- Filter Bar -->
    <form action="{{ route('inventory.reports') }}" method="GET" class="report-filter-bar">
        <div class="filter-left">
            <select name="report_type" class="filter-select" onchange="this.form.submit()">
                <option value="all" {{ $reportType == 'all' ? 'selected' : '' }}>All Reports</option>
                <option value="inventory_summary" {{ $reportType == 'inventory_summary' ? 'selected' : '' }}>Inventory Summary</option>
                <option value="stock_movement" {{ $reportType == 'stock_movement' ? 'selected' : '' }}>Stock Movement</option>
                <option value="low_stock" {{ $reportType == 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
                <option value="supplier_summary" {{ $reportType == 'supplier_summary' ? 'selected' : '' }}>Supplier Summary</option>
                <option value="room_inventory" {{ $reportType == 'room_inventory' ? 'selected' : '' }}>Room Inventory</option>
                <option value="category_breakdown" {{ $reportType == 'category_breakdown' ? 'selected' : '' }}>Category Breakdown</option>
            </select>
            <input type="date" name="date_from" class="filter-date" value="{{ $dateFrom }}">
            <input type="date" name="date_to" class="filter-date" value="{{ $dateTo }}">
            <button type="submit" class="filter-select" style="cursor: pointer; background: #c8a858; color: #1e2a47; border-color: #c8a858;">
                <i class="bi bi-arrow-repeat"></i> Generate
            </button>
        </div>
        <a href="{{ route('inventory.reports.pdf', ['report_type' => $reportType, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" 
           class="btn-generate-pdf">
            <i class="bi bi-file-earmark-pdf"></i> Generate PDF
        </a>
    </form>

    @if($reportType == 'all')
        <!-- ALL REPORTS - Complete Overview -->
        
        <!-- Inventory Summary Section -->
        <div class="summary-section">
            <h3 class="summary-title"><i class="bi bi-box-seam"></i> Inventory Summary - {{ \Carbon\Carbon::parse($dateFrom)->format('F Y') }}</h3>
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="card-icon red"><i class="bi bi-currency-dollar"></i></div>
                    <h3>₱{{ number_format($totalValue, 2) }}</h3>
                    <p>Total Inventory Value</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon green"><i class="bi bi-box-seam"></i></div>
                    <h3>{{ number_format($totalItems) }}</h3>
                    <p>Total Items</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon blue"><i class="bi bi-stack"></i></div>
                    <h3>{{ number_format($totalStock) }}</h3>
                    <p>Total Stock</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon gold"><i class="bi bi-tags"></i></div>
                    <h3>{{ $categories->count() }}</h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>

        <div class="dark-table-card">
            <table class="dark-table" id="itemsTable1">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="paginated-row">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-stock {{ $item->in_stock == 0 ? 'badge-danger' : ($item->in_stock <= 10 ? 'badge-warning' : 'badge-success') }}">
                                    {{ $item->in_stock }}
                                </span>
                            </td>
                            <td>₱{{ number_format($item->price, 2) }}</td>
                            <td>₱{{ number_format($item->in_stock * $item->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="itemsPagination1"></div>
        </div>

        <!-- Stock Movement Section -->
        <div class="summary-section">
            <h3 class="summary-title"><i class="bi bi-arrow-left-right"></i> Stock Movement - {{ $dateFrom }} to {{ $dateTo }}</h3>
            <div class="summary-cards" style="grid-template-columns: repeat(2, 1fr);">
                <div class="summary-card">
                    <div class="card-icon green"><i class="bi bi-box-arrow-in-down"></i></div>
                    <h3>{{ number_format($totalStockIn) }}</h3>
                    <p>Total Stock In</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon red"><i class="bi bi-box-arrow-up"></i></div>
                    <h3>{{ number_format($totalStockOut) }}</h3>
                    <p>Total Stock Out</p>
                </div>
            </div>
        </div>

        <div class="breakdown-section">
            <h4 class="breakdown-title">Stock In Records</h4>
            <div class="dark-table-card" style="margin-bottom: 0;">
                <table class="dark-table" id="stockInTable1">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockIns as $stockIn)
                            <tr class="paginated-row">
                                <td>{{ $stockIn->stockin_date }}</td>
                                <td>{{ $stockIn->item->name ?? 'N/A' }}</td>
                                <td>{{ $stockIn->supplier->name ?? 'N/A' }}</td>
                                <td><span class="badge-stock badge-success">+{{ $stockIn->quantity }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">No stock-in records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-pagination" id="stockInPagination1"></div>
            </div>
        </div>

        <div class="breakdown-section">
            <h4 class="breakdown-title">Stock Out Records</h4>
            <div class="dark-table-card" style="margin-bottom: 0;">
                <table class="dark-table" id="stockOutTable1">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Room</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockOuts as $stockOut)
                            <tr class="paginated-row">
                                <td>{{ $stockOut->created_at->format('Y-m-d') }}</td>
                                <td>{{ $stockOut->item->name ?? 'N/A' }}</td>
                                <td>{{ $stockOut->room->room_number ?? 'N/A' }}</td>
                                <td><span class="badge-stock badge-danger">-{{ $stockOut->quantity }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">No stock-out records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-pagination" id="stockOutPagination1"></div>
            </div>
        </div>

        <!-- Low Stock Section -->
        <div class="summary-section">
            <h3 class="summary-title"><i class="bi bi-exclamation-triangle"></i> Low Stock Alert</h3>
            <div class="summary-cards" style="grid-template-columns: repeat(2, 1fr);">
                <div class="summary-card">
                    <div class="card-icon red"><i class="bi bi-x-circle"></i></div>
                    <h3>{{ $outOfStockItems->count() }}</h3>
                    <p>Out of Stock</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon gold"><i class="bi bi-exclamation-triangle"></i></div>
                    <h3>{{ $lowStockItems->count() }}</h3>
                    <p>Low Stock Items</p>
                </div>
            </div>
        </div>

        @if($lowStockItems->count() > 0)
        <div class="dark-table-card">
            <table class="dark-table" id="lowStockTable1">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockItems as $item)
                        <tr class="paginated-row">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                            <td>{{ $item->in_stock }}</td>
                            <td>
                                <span class="badge-stock {{ $item->in_stock == 0 ? 'badge-danger' : 'badge-warning' }}">
                                    {{ $item->in_stock == 0 ? 'Out of Stock' : 'Low Stock' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="table-pagination" id="lowStockPagination1"></div>
        </div>
        @endif

        <!-- Supplier Summary Section -->
        <div class="summary-section">
            <h3 class="summary-title"><i class="bi bi-truck"></i> Supplier Summary</h3>
        </div>
        <div class="dark-table-card">
            <table class="dark-table" id="supplierTable1">
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Contact Number</th>
                        <th>Stock-In Count</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr class="paginated-row">
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>{{ $supplier->number }}</td>
                            <td>{{ $supplier->stockInCount }}</td>
                            <td>{{ number_format($supplier->totalQuantitySupplied) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No suppliers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="supplierPagination1"></div>
        </div>

        <!-- Room Inventory Section -->
        <div class="summary-section">
            <h3 class="summary-title"><i class="bi bi-door-open"></i> Room Inventory</h3>
            <div class="summary-cards" style="grid-template-columns: repeat(3, 1fr);">
                <div class="summary-card">
                    <div class="card-icon blue"><i class="bi bi-building"></i></div>
                    <h3>{{ $totalRooms }}</h3>
                    <p>Total Rooms</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon green"><i class="bi bi-check-circle"></i></div>
                    <h3>{{ $occupiedRooms }}</h3>
                    <p>Occupied</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon gold"><i class="bi bi-circle"></i></div>
                    <h3>{{ $emptyRooms }}</h3>
                    <p>Available</p>
                </div>
            </div>
        </div>
        <div class="dark-table-card">
            <table class="dark-table" id="roomTable1">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Room Type</th>
                        <th>Status</th>
                        <th>Items Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr class="paginated-row">
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->type->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-stock {{ $room->status == 'occupied' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </td>
                            <td>{{ $room->items->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No rooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="roomPagination1"></div>
        </div>

        <!-- Category Breakdown Section -->
        <div class="summary-section">
            <h3 class="summary-title"><i class="bi bi-pie-chart"></i> Category Breakdown</h3>
        </div>
        <div class="dark-table-card">
            <table class="dark-table" id="categoryTable1">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Items Count</th>
                        <th>Total Stock</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoriesBreakdown as $category)
                        <tr class="paginated-row">
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->items_count }}</td>
                            <td>{{ number_format($category->totalStock) }}</td>
                            <td>₱{{ number_format($category->totalValue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="categoryPagination1"></div>
        </div>

    @elseif($reportType == 'inventory_summary')
        <!-- Inventory Summary -->
        <div class="summary-section">
            <h3 class="summary-title">Summary - {{ \Carbon\Carbon::parse($dateFrom)->format('F Y') }}</h3>
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="card-icon red"><i class="bi bi-currency-dollar"></i></div>
                    <h3>₱{{ number_format($totalValue, 2) }}</h3>
                    <p>Total Inventory Value</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon green"><i class="bi bi-box-seam"></i></div>
                    <h3>{{ number_format($totalItems) }}</h3>
                    <p>Total Items</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon blue"><i class="bi bi-stack"></i></div>
                    <h3>{{ number_format($totalStock) }}</h3>
                    <p>Total Stock</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon gold"><i class="bi bi-tags"></i></div>
                    <h3>{{ $categories->count() }}</h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>

        <div class="dark-table-card">
            <table class="dark-table" id="itemsTable2">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="paginated-row">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-stock {{ $item->in_stock == 0 ? 'badge-danger' : ($item->in_stock <= 10 ? 'badge-warning' : 'badge-success') }}">
                                    {{ $item->in_stock }}
                                </span>
                            </td>
                            <td>₱{{ number_format($item->price, 2) }}</td>
                            <td>₱{{ number_format($item->in_stock * $item->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="itemsPagination2"></div>
        </div>

    @elseif($reportType == 'stock_movement')
        <!-- Stock Movement -->
        <div class="summary-section">
            <h3 class="summary-title">Stock Movement - {{ $dateFrom }} to {{ $dateTo }}</h3>
            <div class="summary-cards" style="grid-template-columns: repeat(2, 1fr);">
                <div class="summary-card">
                    <div class="card-icon green"><i class="bi bi-box-arrow-in-down"></i></div>
                    <h3>{{ number_format($totalStockIn) }}</h3>
                    <p>Total Stock In</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon red"><i class="bi bi-box-arrow-up"></i></div>
                    <h3>{{ number_format($totalStockOut) }}</h3>
                    <p>Total Stock Out</p>
                </div>
            </div>
        </div>

        <div class="breakdown-section">
            <h4 class="breakdown-title">Stock In Records</h4>
            <div class="dark-table-card" style="margin-bottom: 0;">
                <table class="dark-table" id="stockInTable2">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockIns as $stockIn)
                            <tr class="paginated-row">
                                <td>{{ $stockIn->stockin_date }}</td>
                                <td>{{ $stockIn->item->name ?? 'N/A' }}</td>
                                <td>{{ $stockIn->supplier->name ?? 'N/A' }}</td>
                                <td><span class="badge-stock badge-success">+{{ $stockIn->quantity }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">No stock-in records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-pagination" id="stockInPagination2"></div>
            </div>
        </div>

        <div class="breakdown-section">
            <h4 class="breakdown-title">Stock Out Records</h4>
            <div class="dark-table-card" style="margin-bottom: 0;">
                <table class="dark-table" id="stockOutTable2">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Room</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockOuts as $stockOut)
                            <tr class="paginated-row">
                                <td>{{ $stockOut->created_at->format('Y-m-d') }}</td>
                                <td>{{ $stockOut->item->name ?? 'N/A' }}</td>
                                <td>{{ $stockOut->room->room_number ?? 'N/A' }}</td>
                                <td><span class="badge-stock badge-danger">-{{ $stockOut->quantity }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">No stock-out records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-pagination" id="stockOutPagination2"></div>
            </div>
        </div>

    @elseif($reportType == 'low_stock')
        <!-- Low Stock Alert -->
        <div class="summary-section">
            <h3 class="summary-title">Low Stock Alert</h3>
            <div class="summary-cards" style="grid-template-columns: repeat(2, 1fr);">
                <div class="summary-card">
                    <div class="card-icon red"><i class="bi bi-exclamation-circle"></i></div>
                    <h3>{{ $outOfStockItems->count() }}</h3>
                    <p>Out of Stock</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon gold"><i class="bi bi-exclamation-triangle"></i></div>
                    <h3>{{ $lowStockItems->count() }}</h3>
                    <p>Low Stock (≤10)</p>
                </div>
            </div>
        </div>

        @if($outOfStockItems->count() > 0)
        <div class="breakdown-section">
            <h4 class="breakdown-title" style="color: #dc3545;">Out of Stock Items</h4>
            <div class="dark-table-card" style="margin-bottom: 0;">
                <table class="dark-table" id="outOfStockTable">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outOfStockItems as $item)
                            <tr class="paginated-row">
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td><span class="badge-stock badge-danger">{{ $item->in_stock }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="table-pagination" id="outOfStockPagination"></div>
            </div>
        </div>
        @endif

        <div class="breakdown-section">
            <h4 class="breakdown-title" style="color: #ffc107;">Low Stock Items</h4>
            <div class="dark-table-card" style="margin-bottom: 0;">
                <table class="dark-table" id="lowStockTable2">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $item)
                            <tr class="paginated-row">
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td><span class="badge-stock {{ $item->in_stock == 0 ? 'badge-danger' : 'badge-warning' }}">{{ $item->in_stock }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty-state" style="color: #28a745;">All items are well stocked!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-pagination" id="lowStockPagination2"></div>
            </div>
        </div>

    @elseif($reportType == 'supplier_summary')
        <!-- Supplier Summary -->
        <div class="summary-section">
            <h3 class="summary-title">Supplier Summary</h3>
        </div>
        <div class="dark-table-card">
            <table class="dark-table" id="supplierTable2">
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Contact Number</th>
                        <th>Stock-In Count</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr class="paginated-row">
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>{{ $supplier->number }}</td>
                            <td>{{ $supplier->stockInCount }}</td>
                            <td>{{ number_format($supplier->totalQuantitySupplied) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No suppliers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="supplierPagination2"></div>
        </div>

    @elseif($reportType == 'room_inventory')
        <!-- Room Inventory -->
        <div class="summary-section">
            <h3 class="summary-title">Room Inventory</h3>
            <div class="summary-cards" style="grid-template-columns: repeat(3, 1fr);">
                <div class="summary-card">
                    <div class="card-icon blue"><i class="bi bi-door-open"></i></div>
                    <h3>{{ $totalRooms }}</h3>
                    <p>Total Rooms</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon green"><i class="bi bi-check-circle"></i></div>
                    <h3>{{ $occupiedRooms }}</h3>
                    <p>Occupied</p>
                </div>
                <div class="summary-card">
                    <div class="card-icon gold"><i class="bi bi-circle"></i></div>
                    <h3>{{ $emptyRooms }}</h3>
                    <p>Empty</p>
                </div>
            </div>
        </div>
        <div class="dark-table-card">
            <table class="dark-table" id="roomTable2">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Room Type</th>
                        <th>Status</th>
                        <th>Items Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr class="paginated-row">
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->type->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-stock {{ $room->status == 'occupied' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </td>
                            <td>{{ $room->items->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No rooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="roomPagination2"></div>
        </div>

    @elseif($reportType == 'category_breakdown')
        <!-- Category Breakdown -->
        <div class="summary-section">
            <h3 class="summary-title">Category Breakdown</h3>
        </div>
        <div class="dark-table-card">
            <table class="dark-table" id="categoryTable2">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Items Count</th>
                        <th>Total Stock</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="paginated-row">
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->items_count }}</td>
                            <td>{{ number_format($category->totalStock) }}</td>
                            <td>₱{{ number_format($category->totalValue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-pagination" id="categoryPagination2"></div>
        </div>
    @endif
</div>

<script>
// Pagination Function
function initPagination(tableId, paginationId, itemsPerPage = 5) {
    const table = document.getElementById(tableId);
    const paginationContainer = document.getElementById(paginationId);
    
    if (!table || !paginationContainer) return;
    
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr.paginated-row');
    
    // Always show pagination, even with few items
    if (rows.length === 0) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    const totalPages = Math.max(1, Math.ceil(rows.length / itemsPerPage));
    
    function showPage(page) {
        rows.forEach((row, index) => {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });
        
        paginationContainer.innerHTML = '';
        
        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'page-btn';
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.disabled = page === 1;
        prevBtn.onclick = () => { if (page > 1) showPage(page - 1); };
        paginationContainer.appendChild(prevBtn);
        
        // Page info text
        const pageInfo = document.createElement('span');
        pageInfo.className = 'page-info';
        pageInfo.textContent = 'Page ' + page + ' of ' + totalPages;
        pageInfo.style.cssText = 'padding: 0 12px; color: #6c757d; font-size: 0.85rem;';
        paginationContainer.appendChild(pageInfo);
        
        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'page-btn';
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.disabled = page === totalPages;
        nextBtn.onclick = () => { if (page < totalPages) showPage(page + 1); };
        paginationContainer.appendChild(nextBtn);
    }
    
    showPage(1);
}

// Initialize pagination for tables (5 items per page)
document.addEventListener('DOMContentLoaded', function() {
    // All Reports section
    initPagination('itemsTable1', 'itemsPagination1', 5);
    initPagination('stockInTable1', 'stockInPagination1', 5);
    initPagination('stockOutTable1', 'stockOutPagination1', 5);
    initPagination('lowStockTable1', 'lowStockPagination1', 5);
    initPagination('supplierTable1', 'supplierPagination1', 5);
    initPagination('roomTable1', 'roomPagination1', 5);
    initPagination('categoryTable1', 'categoryPagination1', 5);
    
    // Individual report sections
    initPagination('itemsTable2', 'itemsPagination2', 5);
    initPagination('stockInTable2', 'stockInPagination2', 5);
    initPagination('stockOutTable2', 'stockOutPagination2', 5);
    initPagination('outOfStockTable', 'outOfStockPagination', 5);
    initPagination('lowStockTable2', 'lowStockPagination2', 5);
    initPagination('supplierTable2', 'supplierPagination2', 5);
    initPagination('roomTable2', 'roomPagination2', 5);
    initPagination('categoryTable2', 'categoryPagination2', 5);
});
</script>
@endsection
