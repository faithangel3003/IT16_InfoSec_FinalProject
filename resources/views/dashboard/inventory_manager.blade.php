@extends('dashboard')

@section('title', 'Inventory Dashboard - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .inv-dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .inv-dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .inv-dashboard-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .inv-dashboard-title i {
            color: #c8a858;
        }
        
        .inv-date-badge {
            background: linear-gradient(135deg, #1e2a47 0%, #2d3a5c 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Modern Stats Cards */
        .inv-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .inv-stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .inv-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .inv-stat-card.gold::before { background: linear-gradient(180deg, #c8a858 0%, #e0c078 100%); }
        .inv-stat-card.navy::before { background: linear-gradient(180deg, #1e2a47 0%, #2d3a5c 100%); }
        .inv-stat-card.success::before { background: linear-gradient(180deg, #28a745 0%, #34ce57 100%); }
        .inv-stat-card.danger::before { background: linear-gradient(180deg, #dc3545 0%, #e85c68 100%); }
        .inv-stat-card.warning::before { background: linear-gradient(180deg, #ffc107 0%, #ffcd39 100%); }
        .inv-stat-card.info::before { background: linear-gradient(180deg, #17a2b8 0%, #3ab5c6 100%); }
        
        .inv-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .inv-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        
        .inv-stat-icon.gold { background: rgba(200, 168, 88, 0.15); color: #c8a858; }
        .inv-stat-icon.navy { background: rgba(30, 42, 71, 0.1); color: #1e2a47; }
        .inv-stat-icon.success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .inv-stat-icon.danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .inv-stat-icon.warning { background: rgba(255, 193, 7, 0.2); color: #d39e00; }
        .inv-stat-icon.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        
        .inv-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        
        .inv-stat-value.gold { color: #c8a858; }
        .inv-stat-value.danger { color: #dc3545; }
        .inv-stat-value.success { color: #28a745; }
        
        .inv-stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .inv-stat-subtitle {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        /* Alert Cards Row */
        .inv-alert-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 768px) {
            .inv-alert-grid { grid-template-columns: 1fr; }
        }
        
        .inv-alert-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .inv-alert-card.danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, white 100%);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .inv-alert-card.warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.08) 0%, white 100%);
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .inv-alert-card.success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, white 100%);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .inv-alert-value {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
        }
        
        .inv-alert-value.danger { color: #dc3545; }
        .inv-alert-value.warning { color: #d39e00; }
        .inv-alert-value.success { color: #28a745; }
        
        .inv-alert-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .inv-alert-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        /* Chart Cards */
        .inv-chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .inv-chart-grid { grid-template-columns: 1fr; }
        }
        
        .inv-chart-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .inv-chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .inv-chart-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .inv-chart-title i {
            color: #c8a858;
            font-size: 1.1rem;
        }
        
        .inv-chart-canvas {
            height: 280px;
            position: relative;
        }
        
        /* Data Tables */
        .inv-table-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .inv-table-grid { grid-template-columns: 1fr; }
        }
        
        .inv-table-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .inv-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .inv-table-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .inv-table-title i {
            color: #c8a858;
        }
        
        .inv-data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .inv-data-table thead th {
            background: #f8f9fa;
            padding: 12px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .inv-data-table thead th:first-child {
            border-radius: 8px 0 0 0;
        }
        
        .inv-data-table thead th:last-child {
            border-radius: 0 8px 0 0;
        }
        
        .inv-data-table tbody td {
            padding: 14px 16px;
            font-size: 0.875rem;
            color: #374151;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .inv-data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .inv-data-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .inv-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .inv-badge.danger {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .inv-badge.warning {
            background: rgba(255, 193, 7, 0.2);
            color: #d39e00;
        }
        
        .inv-badge.success {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        /* Small Stats Row */
        .inv-mini-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 768px) {
            .inv-mini-stats { grid-template-columns: repeat(2, 1fr); }
        }
        
        .inv-mini-card {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .inv-mini-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        
        .inv-mini-content {
            flex: 1;
        }
        
        .inv-mini-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e2a47;
        }
        
        .inv-mini-label {
            font-size: 0.7rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        /* Top Items List */
        .inv-top-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .inv-top-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        
        .inv-top-item:hover {
            background: #f0f1f3;
        }
        
        .inv-top-rank {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            background: #1e2a47;
            color: white;
        }
        
        .inv-top-rank.gold { background: #c8a858; }
        
        .inv-top-info {
            flex: 1;
        }
        
        .inv-top-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e2a47;
        }
        
        .inv-top-meta {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        .inv-top-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #c8a858;
        }
        
        .inv-scrollable {
            max-height: 280px;
            overflow-y: auto;
        }
        
        .inv-scrollable::-webkit-scrollbar {
            width: 6px;
        }
        
        .inv-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .inv-scrollable::-webkit-scrollbar-thumb {
            background: #c8a858;
            border-radius: 3px;
        }
        
        /* Pagination */
        .inv-pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 16px;
        }
        
        .inv-page-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            background: #f0f0f0;
            color: #6c757d;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .inv-page-btn:hover {
            background: #e0e0e0;
        }
        
        .inv-page-btn.active {
            background: #c8a858;
            color: white;
        }
    </style>
@endsection

@section('content')
<div class="container py-4 inv-dashboard-container">
    <!-- Header -->
    <div class="inv-dashboard-header">
        <h2 class="inv-dashboard-title">
            <i class="bi bi-boxes"></i> Inventory Dashboard
        </h2>
        <span class="inv-date-badge">
            <i class="bi bi-calendar3"></i> {{ now()->format('F d, Y') }}
        </span>
    </div>

    <!-- Main Stats Row -->
    <div class="inv-stats-grid">
        <div class="inv-stat-card gold">
            <div class="inv-stat-icon gold">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="inv-stat-value gold">₱{{ number_format($monthlyStockInValue, 0) }}</div>
            <div class="inv-stat-label">Stock In Value</div>
            <div class="inv-stat-subtitle">{{ $monthlyStockInQty }} items this month</div>
        </div>
        
        <div class="inv-stat-card navy">
            <div class="inv-stat-icon navy">
                <i class="bi bi-box-arrow-up"></i>
            </div>
            <div class="inv-stat-value">{{ number_format($monthlyStockOutQty) }}</div>
            <div class="inv-stat-label">Stock Out</div>
            <div class="inv-stat-subtitle">Items distributed</div>
        </div>
        
        <div class="inv-stat-card info">
            <div class="inv-stat-icon info">
                <i class="bi bi-inboxes"></i>
            </div>
            <div class="inv-stat-value">{{ $totalItems }}</div>
            <div class="inv-stat-label">Total Items</div>
            <div class="inv-stat-subtitle">{{ number_format($totalInStock) }} units in stock</div>
        </div>
        
        <div class="inv-stat-card warning">
            <div class="inv-stat-icon warning">
                <i class="bi bi-tags"></i>
            </div>
            <div class="inv-stat-value">{{ $totalCategories }}</div>
            <div class="inv-stat-label">Categories</div>
            <div class="inv-stat-subtitle">Item categories</div>
        </div>
        
        <div class="inv-stat-card success">
            <div class="inv-stat-icon success">
                <i class="bi bi-truck"></i>
            </div>
            <div class="inv-stat-value">{{ $totalSuppliers }}</div>
            <div class="inv-stat-label">Suppliers</div>
            <div class="inv-stat-subtitle">Active partners</div>
        </div>
    </div>

    <!-- Alert Cards -->
    <div class="inv-alert-grid">
        <div class="inv-alert-card danger">
            <div class="inv-alert-value danger">{{ $lowStockCount }}</div>
            <div class="inv-alert-label">Low Stock Items</div>
            <div class="inv-alert-hint"><i class="bi bi-exclamation-triangle"></i> Below 25 pieces</div>
        </div>
        
        <div class="inv-alert-card warning">
            <div class="inv-alert-value warning">{{ $outOfStockCount }}</div>
            <div class="inv-alert-label">Out of Stock</div>
            <div class="inv-alert-hint"><i class="bi bi-x-circle"></i> Need immediate restock</div>
        </div>
        
        <div class="inv-alert-card success">
            <div class="inv-alert-value success">₱{{ number_format($inventoryValue, 0) }}</div>
            <div class="inv-alert-label">Inventory Value</div>
            <div class="inv-alert-hint"><i class="bi bi-calculator"></i> Est. total value</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="inv-chart-grid">
        <div class="inv-chart-card">
            <div class="inv-chart-header">
                <h5 class="inv-chart-title"><i class="bi bi-graph-up"></i> Stock Activity (7 Days)</h5>
            </div>
            <div class="inv-chart-canvas">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
        
        <div class="inv-chart-card">
            <div class="inv-chart-header">
                <h5 class="inv-chart-title"><i class="bi bi-bar-chart"></i> Monthly Comparison (6 Mo)</h5>
            </div>
            <div class="inv-chart-canvas">
                <canvas id="monthlyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Mini Stats Row -->
    <div class="inv-mini-stats">
        <div class="inv-mini-card">
            <div class="inv-mini-icon" style="background: rgba(40, 167, 69, 0.15); color: #28a745;">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="inv-mini-content">
                <div class="inv-mini-value">{{ $inStockCount }}</div>
                <div class="inv-mini-label">In Stock (≥25)</div>
            </div>
        </div>
        
        <div class="inv-mini-card">
            <div class="inv-mini-icon" style="background: rgba(255, 193, 7, 0.2); color: #d39e00;">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <div class="inv-mini-content">
                <div class="inv-mini-value">{{ $lowStockCount }}</div>
                <div class="inv-mini-label">Low Stock (&lt;25)</div>
            </div>
        </div>
        
        <div class="inv-mini-card">
            <div class="inv-mini-icon" style="background: rgba(220, 53, 69, 0.15); color: #dc3545;">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="inv-mini-content">
                <div class="inv-mini-value">{{ $outOfStockCount }}</div>
                <div class="inv-mini-label">Out of Stock</div>
            </div>
        </div>
        
        <div class="inv-mini-card">
            <div class="inv-mini-icon" style="background: rgba(200, 168, 88, 0.15); color: #c8a858;">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="inv-mini-content">
                <div class="inv-mini-value">{{ $returnedItemsCount }}</div>
                <div class="inv-mini-label">Returned Items</div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="inv-chart-grid">
        <div class="inv-chart-card">
            <div class="inv-chart-header">
                <h5 class="inv-chart-title"><i class="bi bi-pie-chart"></i> Items by Category</h5>
            </div>
            <div class="inv-chart-canvas">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        
        <div class="inv-chart-card">
            <div class="inv-chart-header">
                <h5 class="inv-chart-title"><i class="bi bi-graph-down"></i> Stock Status</h5>
            </div>
            <div class="inv-chart-canvas">
                <canvas id="stockStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="inv-table-grid">
        <!-- Items Needing Restock -->
        <div class="inv-table-card">
            <div class="inv-table-header">
                <h5 class="inv-table-title"><i class="bi bi-exclamation-triangle-fill"></i> Items to Restock</h5>
            </div>
            <div class="inv-scrollable">
                <table class="inv-data-table" id="restockTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemsNeedingRestock as $item)
                            <tr class="paginated-row">
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td>{{ $item->in_stock }}</td>
                                <td>
                                    @if($item->in_stock == 0)
                                        <span class="inv-badge danger">Out of Stock</span>
                                    @else
                                        <span class="inv-badge warning">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle text-success"></i> All items sufficiently stocked
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="inv-pagination" id="restockTablePagination"></div>
        </div>

        <!-- Recent Stock In -->
        <div class="inv-table-card">
            <div class="inv-table-header">
                <h5 class="inv-table-title"><i class="bi bi-box-arrow-in-down"></i> Recent Stock In</h5>
            </div>
            <div class="inv-scrollable">
                <table class="inv-data-table" id="stockInTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStockIn as $stockIn)
                            <tr class="paginated-row">
                                <td><strong>{{ $stockIn->item->name ?? 'N/A' }}</strong></td>
                                <td>{{ Str::limit($stockIn->supplier->name ?? 'N/A', 15) }}</td>
                                <td><span class="inv-badge success">+{{ $stockIn->quantity }}</span></td>
                                <td>{{ $stockIn->created_at->format('M d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No recent stock in records</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="inv-pagination" id="stockInTablePagination"></div>
        </div>
    </div>

    <!-- Top Items & Suppliers -->
    <div class="inv-table-grid">
        <!-- Top Items Distributed -->
        <div class="inv-table-card">
            <div class="inv-table-header">
                <h5 class="inv-table-title"><i class="bi bi-trophy"></i> Top Items Distributed</h5>
            </div>
            <div class="inv-top-list" id="topItemsList">
                @forelse($topItemsUsed as $index => $itemUsed)
                    <div class="inv-top-item paginated-row">
                        <div class="inv-top-rank {{ $index === 0 ? 'gold' : '' }}">{{ $index + 1 }}</div>
                        <div class="inv-top-info">
                            <div class="inv-top-name">{{ $itemUsed->item->name ?? 'N/A' }}</div>
                            <div class="inv-top-meta">ID: {{ $itemUsed->item->item_id ?? 'N/A' }}</div>
                        </div>
                        <div class="inv-top-value">{{ $itemUsed->total_qty }} pcs</div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3">No distribution data available</p>
                @endforelse
            </div>
            <div class="inv-pagination" id="topItemsListPagination"></div>
        </div>

        <!-- Top Suppliers -->
        <div class="inv-table-card">
            <div class="inv-table-header">
                <h5 class="inv-table-title"><i class="bi bi-building"></i> Top Suppliers</h5>
            </div>
            <div class="inv-top-list" id="topSuppliersList">
                @forelse($topSuppliers as $index => $supplierData)
                    <div class="inv-top-item paginated-row">
                        <div class="inv-top-rank {{ $index === 0 ? 'gold' : '' }}">{{ $index + 1 }}</div>
                        <div class="inv-top-info">
                            <div class="inv-top-name">{{ $supplierData->supplier->name ?? 'N/A' }}</div>
                            <div class="inv-top-meta">₱{{ number_format($supplierData->total_value ?? 0, 0) }} total value</div>
                        </div>
                        <div class="inv-top-value">{{ $supplierData->delivery_count }} deliveries</div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3">No supplier data available</p>
                @endforelse
            </div>
            <div class="inv-pagination" id="topSuppliersListPagination"></div>
        </div>
    </div>
</div>

<script>
    // Daily Activity Line Chart
    const dailyCtx = document.getElementById('dailyActivityChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyLabels) !!},
            datasets: [{
                label: 'Stock In',
                data: {!! json_encode($dailyStockIn) !!},
                borderColor: '#c8a858',
                backgroundColor: 'rgba(200, 168, 88, 0.15)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#c8a858',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }, {
                label: 'Stock Out',
                data: {!! json_encode($dailyStockOut) !!},
                borderColor: '#1e2a47',
                backgroundColor: 'rgba(30, 42, 71, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#1e2a47',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        color: '#1e2a47', 
                        font: { size: 12, weight: '500' },
                        padding: 20,
                        usePointStyle: true
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#6c757d', font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }
                },
                x: {
                    ticks: { color: '#6c757d', font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    // Monthly Activity Bar Chart
    const monthlyCtx = document.getElementById('monthlyActivityChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyLabels) !!},
            datasets: [{
                label: 'Stock In',
                data: {!! json_encode($monthlyStockInData) !!},
                backgroundColor: 'rgba(200, 168, 88, 0.8)',
                borderColor: '#c8a858',
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 24
            }, {
                label: 'Stock Out',
                data: {!! json_encode($monthlyStockOutData) !!},
                backgroundColor: 'rgba(30, 42, 71, 0.8)',
                borderColor: '#1e2a47',
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        color: '#1e2a47', 
                        font: { size: 12, weight: '500' },
                        padding: 20,
                        usePointStyle: true
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#6c757d', font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }
                },
                x: {
                    ticks: { color: '#6c757d', font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    // Items by Category Doughnut Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryLabels) !!},
            datasets: [{
                data: {!! json_encode($categoryData) !!},
                backgroundColor: [
                    '#c8a858',
                    '#1e2a47',
                    '#28a745',
                    '#dc3545',
                    '#17a2b8',
                    '#6c757d',
                    '#ffc107',
                    '#6610f2'
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        color: '#1e2a47', 
                        font: { size: 11 },
                        boxWidth: 14,
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Stock Status Doughnut Chart
    const stockStatusCtx = document.getElementById('stockStatusChart').getContext('2d');
    new Chart(stockStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock (≥25)', 'Low Stock (<25)', 'Out of Stock'],
            datasets: [{
                data: [{{ $inStockCount }}, {{ $lowStockCount }}, {{ $outOfStockCount }}],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        color: '#1e2a47', 
                        font: { size: 11 },
                        boxWidth: 14,
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Pagination Function
    function initPagination(containerId, paginationId, itemsPerPage = 3) {
        const container = document.getElementById(containerId);
        const paginationContainer = document.getElementById(paginationId);
        
        if (!container || !paginationContainer) return;
        
        // For tables, get rows from tbody; for lists, get direct children with .paginated-row
        let items;
        const tbody = container.querySelector('tbody');
        if (tbody) {
            items = tbody.querySelectorAll('tr.paginated-row');
        } else {
            items = container.querySelectorAll('.paginated-row');
        }
        
        // Always show pagination, even with few items
        if (items.length === 0) {
            paginationContainer.style.display = 'none';
            return;
        }
        
        const totalPages = Math.max(1, Math.ceil(items.length / itemsPerPage));
        let currentPage = 1;
        
        function showPage(page) {
            items.forEach((item, index) => {
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                item.style.display = (index >= start && index < end) ? '' : 'none';
            });
            
            // Update pagination buttons
            paginationContainer.innerHTML = '';
            
            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'inv-page-btn';
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
            nextBtn.className = 'inv-page-btn';
            nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
            nextBtn.disabled = page === totalPages;
            nextBtn.onclick = () => { if (page < totalPages) showPage(page + 1); };
            paginationContainer.appendChild(nextBtn);
            
            currentPage = page;
        }
        
        showPage(1);
    }
    
    // Initialize pagination for tables (3 items per page)
    initPagination('restockTable', 'restockTablePagination', 3);
    initPagination('stockInTable', 'stockInTablePagination', 3);
    initPagination('topItemsList', 'topItemsListPagination', 3);
    initPagination('topSuppliersList', 'topSuppliersListPagination', 3);
</script>
@endsection
