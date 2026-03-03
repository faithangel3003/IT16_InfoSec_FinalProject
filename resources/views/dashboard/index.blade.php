@extends('dashboard')

@section('title', 'Admin Dashboard - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dash-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .dash-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .dash-title i {
            color: #c8a858;
        }
        
        .dash-date-badge {
            background: linear-gradient(135deg, #1e2a47 0%, #2d3a5c 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Modern Stats Cards */
        .dash-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .dash-stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .dash-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .dash-stat-card.gold::before { background: linear-gradient(180deg, #c8a858 0%, #e0c078 100%); }
        .dash-stat-card.navy::before { background: linear-gradient(180deg, #1e2a47 0%, #2d3a5c 100%); }
        .dash-stat-card.success::before { background: linear-gradient(180deg, #28a745 0%, #34ce57 100%); }
        .dash-stat-card.danger::before { background: linear-gradient(180deg, #dc3545 0%, #e85c68 100%); }
        .dash-stat-card.warning::before { background: linear-gradient(180deg, #ffc107 0%, #ffcd39 100%); }
        .dash-stat-card.info::before { background: linear-gradient(180deg, #17a2b8 0%, #3ab5c6 100%); }
        
        .dash-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .dash-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        
        .dash-stat-icon.gold { background: rgba(200, 168, 88, 0.15); color: #c8a858; }
        .dash-stat-icon.navy { background: rgba(30, 42, 71, 0.1); color: #1e2a47; }
        .dash-stat-icon.success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .dash-stat-icon.danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .dash-stat-icon.warning { background: rgba(255, 193, 7, 0.2); color: #d39e00; }
        .dash-stat-icon.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        
        .dash-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        
        .dash-stat-value.gold { color: #c8a858; }
        .dash-stat-value.danger { color: #dc3545; }
        .dash-stat-value.success { color: #28a745; }
        
        .dash-stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .dash-stat-subtitle {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        /* Alert Cards Row */
        .dash-alert-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 768px) {
            .dash-alert-grid { grid-template-columns: 1fr; }
        }
        
        .dash-alert-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .dash-alert-card.danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, white 100%);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .dash-alert-card.warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.08) 0%, white 100%);
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .dash-alert-card.success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, white 100%);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .dash-alert-value {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
        }
        
        .dash-alert-value.danger { color: #dc3545; }
        .dash-alert-value.warning { color: #d39e00; }
        .dash-alert-value.success { color: #28a745; }
        
        .dash-alert-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .dash-alert-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        /* Chart Cards */
        .dash-chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .dash-chart-grid { grid-template-columns: 1fr; }
        }
        
        .dash-chart-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .dash-chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .dash-chart-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .dash-chart-title i {
            color: #c8a858;
            font-size: 1.1rem;
        }
        
        .dash-chart-canvas {
            height: 280px;
            position: relative;
        }
        
        /* Data Tables */
        .dash-table-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .dash-table-grid { grid-template-columns: 1fr; }
        }
        
        .dash-table-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .dash-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .dash-table-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .dash-table-title i {
            color: #c8a858;
        }
        
        .dash-data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .dash-data-table thead th {
            background: #f8f9fa;
            padding: 12px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .dash-data-table thead th:first-child {
            border-radius: 8px 0 0 0;
        }
        
        .dash-data-table thead th:last-child {
            border-radius: 0 8px 0 0;
        }
        
        .dash-data-table tbody td {
            padding: 14px 16px;
            font-size: 0.875rem;
            color: #374151;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .dash-data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .dash-data-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .dash-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .dash-badge.danger {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .dash-badge.warning {
            background: rgba(255, 193, 7, 0.2);
            color: #d39e00;
        }
        
        .dash-badge.success {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        /* Top Items List */
        .dash-top-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .dash-top-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        
        .dash-top-item:hover {
            background: #f0f1f3;
        }
        
        .dash-top-rank {
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
        
        .dash-top-rank.gold { background: #c8a858; }
        
        .dash-top-info {
            flex: 1;
        }
        
        .dash-top-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e2a47;
        }
        
        .dash-top-meta {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        .dash-top-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #c8a858;
        }
        
        .dash-scrollable {
            max-height: 280px;
            overflow-y: auto;
        }
        
        .dash-scrollable::-webkit-scrollbar {
            width: 6px;
        }
        
        .dash-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .dash-scrollable::-webkit-scrollbar-thumb {
            background: #c8a858;
            border-radius: 3px;
        }
        
        /* Activity Items */
        .dash-activity-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .dash-activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 3px solid #c8a858;
        }
        
        .dash-activity-item i {
            color: #c8a858;
            font-size: 1rem;
            margin-top: 2px;
        }
        
        .dash-activity-content {
            flex: 1;
        }
        
        .dash-activity-text {
            font-size: 0.875rem;
            color: #374151;
            display: block;
        }
        
        .dash-activity-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        /* Mini Stats */
        .dash-mini-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 768px) {
            .dash-mini-stats { grid-template-columns: repeat(2, 1fr); }
        }
        
        .dash-mini-card {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .dash-mini-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        
        .dash-mini-content {
            flex: 1;
        }
        
        .dash-mini-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e2a47;
        }
        
        .dash-mini-label {
            font-size: 0.7rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        /* Pagination */
        .dash-pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 16px;
        }
        
        .dash-page-btn {
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
        
        .dash-page-btn:hover {
            background: #e0e0e0;
        }
        
        .dash-page-btn.active {
            background: #c8a858;
            color: white;
        }
        
        .dash-page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
<div class="container py-4 dash-container">
    <!-- Header -->
    <div class="dash-header">
        <h2 class="dash-title">
            <i class="bi bi-speedometer2"></i> Admin Dashboard
        </h2>
        <span class="dash-date-badge">
            <i class="bi bi-calendar3"></i> {{ now()->format('F d, Y') }}
        </span>
    </div>

    <!-- Main Stats Row -->
    <div class="dash-stats-grid">
        <div class="dash-stat-card gold">
            <div class="dash-stat-icon gold">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="dash-stat-value gold">₱{{ number_format($monthlyStockInValue, 0) }}</div>
            <div class="dash-stat-label">Stock In Value</div>
            <div class="dash-stat-subtitle">{{ $totalStockIn }} transactions this month</div>
        </div>
        
        <div class="dash-stat-card navy">
            <div class="dash-stat-icon navy">
                <i class="bi bi-box-arrow-up"></i>
            </div>
            <div class="dash-stat-value">{{ number_format($monthlyStockOutQty) }}</div>
            <div class="dash-stat-label">Stock Out</div>
            <div class="dash-stat-subtitle">Items distributed</div>
        </div>
        
        <div class="dash-stat-card info">
            <div class="dash-stat-icon info">
                <i class="bi bi-inboxes"></i>
            </div>
            <div class="dash-stat-value">{{ $totalItems }}</div>
            <div class="dash-stat-label">Total Items</div>
            <div class="dash-stat-subtitle">In inventory</div>
        </div>
        
        <div class="dash-stat-card warning">
            <div class="dash-stat-icon warning">
                <i class="bi bi-truck"></i>
            </div>
            <div class="dash-stat-value">{{ $topSupplier ? Str::limit($topSupplier->supplier->name ?? '-', 8) : '-' }}</div>
            <div class="dash-stat-label">Top Supplier</div>
            <div class="dash-stat-subtitle">{{ $topSupplier ? $topSupplier->count . ' deliveries' : 'No data' }}</div>
        </div>
        
        <div class="dash-stat-card success">
            <div class="dash-stat-icon success">
                <i class="bi bi-door-open"></i>
            </div>
            <div class="dash-stat-value">{{ $occupancyRate }}%</div>
            <div class="dash-stat-label">Occupancy</div>
            <div class="dash-stat-subtitle">{{ $occupiedRooms }}/{{ $totalRooms }} rooms</div>
        </div>
    </div>

    <!-- Alert Cards -->
    <div class="dash-alert-grid">
        <div class="dash-alert-card danger">
            <div class="dash-alert-value danger">{{ $lowStockCount }}</div>
            <div class="dash-alert-label">Low Stock Items</div>
            <div class="dash-alert-hint"><i class="bi bi-exclamation-triangle"></i> Below 25 pieces</div>
        </div>
        
        <div class="dash-alert-card warning">
            <div class="dash-alert-value warning">{{ $outOfStockCount }}</div>
            <div class="dash-alert-label">Out of Stock</div>
            <div class="dash-alert-hint"><i class="bi bi-x-circle"></i> Need immediate restock</div>
        </div>
        
        <div class="dash-alert-card success">
            <div class="dash-alert-value success">₱{{ number_format($inventoryValue, 0) }}</div>
            <div class="dash-alert-label">Inventory Value</div>
            <div class="dash-alert-hint"><i class="bi bi-calculator"></i> Est. total value</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="dash-chart-grid">
        <div class="dash-chart-card">
            <div class="dash-chart-header">
                <h5 class="dash-chart-title"><i class="bi bi-graph-up"></i> Daily Activity (7 Days)</h5>
            </div>
            <div class="dash-chart-canvas">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
        
        <div class="dash-chart-card">
            <div class="dash-chart-header">
                <h5 class="dash-chart-title"><i class="bi bi-bar-chart"></i> Monthly Stock In (6 Mo)</h5>
            </div>
            <div class="dash-chart-canvas">
                <canvas id="monthlyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Mini Stats Row -->
    <div class="dash-mini-stats">
        <div class="dash-mini-card">
            <div class="dash-mini-icon" style="background: rgba(40, 167, 69, 0.15); color: #28a745;">
                <i class="bi bi-people"></i>
            </div>
            <div class="dash-mini-content">
                <div class="dash-mini-value">{{ $activeUsersToday }}</div>
                <div class="dash-mini-label">Active Users Today</div>
            </div>
        </div>
        
        <div class="dash-mini-card">
            <div class="dash-mini-icon" style="background: rgba(200, 168, 88, 0.15); color: #c8a858;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="dash-mini-content">
                <div class="dash-mini-value">{{ $totalStockIn }}</div>
                <div class="dash-mini-label">Total Stock In</div>
            </div>
        </div>
        
        <div class="dash-mini-card">
            <div class="dash-mini-icon" style="background: rgba(30, 42, 71, 0.1); color: #1e2a47;">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <div class="dash-mini-content">
                <div class="dash-mini-value">{{ $totalStockOut }}</div>
                <div class="dash-mini-label">Total Stock Out</div>
            </div>
        </div>
        
        <div class="dash-mini-card">
            <div class="dash-mini-icon" style="background: rgba(23, 162, 184, 0.15); color: #17a2b8;">
                <i class="bi bi-building"></i>
            </div>
            <div class="dash-mini-content">
                <div class="dash-mini-value">{{ $emptyRooms }}</div>
                <div class="dash-mini-label">Available Rooms</div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="dash-table-grid">
        <!-- Items Needing Restock -->
        <div class="dash-table-card">
            <div class="dash-table-header">
                <h5 class="dash-table-title"><i class="bi bi-exclamation-triangle-fill"></i> Items to Restock</h5>
            </div>
            <div>
                <table class="dash-data-table" id="restockTable">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Name</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemsNeedingRestock as $item)
                            <tr class="paginated-row">
                                <td>{{ $item->item_id }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->in_stock }}</td>
                                <td>
                                    @if($item->in_stock == 0)
                                        <span class="dash-badge danger">Out of Stock</span>
                                    @else
                                        <span class="dash-badge warning">Low Stock</span>
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
            <div class="dash-pagination" id="restockTablePagination"></div>
        </div>

        <!-- Recent Activities -->
        <div class="dash-table-card">
            <div class="dash-table-header">
                <h5 class="dash-table-title"><i class="bi bi-clock-history"></i> Recent Activities</h5>
            </div>
            <div>
                <div class="dash-activity-list" id="activityList">
                    @forelse($recentActivities as $activity)
                        <div class="dash-activity-item paginated-row">
                            <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                            <div class="dash-activity-content">
                                <span class="dash-activity-text">{{ $activity->activity }}</span>
                                <span class="dash-activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No recent activities</p>
                    @endforelse
                </div>
            </div>
            <div class="dash-pagination" id="activityListPagination"></div>
        </div>
    </div>

    <!-- Top Items -->
    <div class="dash-table-grid">
        <div class="dash-table-card">
            <div class="dash-table-header">
                <h5 class="dash-table-title"><i class="bi bi-trophy"></i> Top Items Used</h5>
            </div>
            <div class="dash-top-list" id="topItemsList">
                @forelse($topItemsUsed as $index => $itemUsed)
                    <div class="dash-top-item paginated-row">
                        <div class="dash-top-rank {{ $index === 0 ? 'gold' : '' }}">{{ $index + 1 }}</div>
                        <div class="dash-top-info">
                            <div class="dash-top-name">{{ $itemUsed->item->name ?? 'N/A' }}</div>
                            <div class="dash-top-meta">ID: {{ $itemUsed->item->item_id ?? 'N/A' }}</div>
                        </div>
                        <div class="dash-top-value">{{ $itemUsed->total_qty }} pcs</div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3">No distribution data available</p>
                @endforelse
            </div>
            <div class="dash-pagination" id="topItemsListPagination"></div>
        </div>
        
        <div class="dash-table-card">
            <div class="dash-table-header">
                <h5 class="dash-table-title"><i class="bi bi-door-open-fill"></i> Room Status Overview</h5>
            </div>
            <div>
                <table class="dash-data-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="dash-badge success">Available</span></td>
                            <td><strong>{{ $emptyRooms }}</strong></td>
                            <td>{{ $totalRooms > 0 ? round(($emptyRooms / $totalRooms) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td><span class="dash-badge warning">Occupied</span></td>
                            <td><strong>{{ $occupiedRooms }}</strong></td>
                            <td>{{ $occupancyRate }}%</td>
                        </tr>
                        <tr>
                            <td><strong>Total Rooms</strong></td>
                            <td><strong>{{ $totalRooms }}</strong></td>
                            <td>100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                data: {!! json_encode($monthlyActivity) !!},
                backgroundColor: 'rgba(200, 168, 88, 0.8)',
                borderColor: '#c8a858',
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 32
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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

    // Pagination Function
    function initPagination(containerId, paginationId, itemsPerPage = 5) {
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
            prevBtn.className = 'dash-page-btn';
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
            nextBtn.className = 'dash-page-btn';
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
    initPagination('activityList', 'activityListPagination', 3);
    initPagination('topItemsList', 'topItemsListPagination', 3);
</script>
@endsection