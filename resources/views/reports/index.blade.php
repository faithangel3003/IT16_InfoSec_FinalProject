@extends('dashboard')

@section('title', 'Audit Logs - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .audit-page { padding: 20px 30px; }
        
        .audit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .audit-title-section h1 {
            color: #1e2a47;
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0 0 5px 0;
            letter-spacing: 1px;
        }
        
        .audit-title-section p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .audit-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-export {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-export.pdf {
            background: #dc3545;
            color: #fff;
        }
        
        .btn-export.pdf:hover {
            background: #c82333;
        }
        
        .btn-export.csv {
            background: #28a745;
            color: #fff;
        }
        
        .btn-export.csv:hover {
            background: #218838;
        }
        
        /* Stats Grid - KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .kpi-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 20px 25px;
            border: 1px solid rgba(30, 42, 71, 0.08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .kpi-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        
        .kpi-icon.blue { background: rgba(108, 117, 125, 0.12); color: #6c757d; }
        .kpi-icon.green { background: rgba(40, 167, 69, 0.12); color: #28a745; }
        .kpi-icon.gold { background: rgba(253, 193, 7, 0.15); color: #d4a500; }
        .kpi-icon.red { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
        .kpi-icon.purple { background: rgba(111, 66, 193, 0.12); color: #6f42c1; }
        .kpi-icon.orange { background: rgba(253, 126, 20, 0.12); color: #fd7e14; }
        .kpi-icon.cyan { background: rgba(23, 162, 184, 0.12); color: #17a2b8; }
        
        .kpi-content {
            flex: 1;
        }
        
        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e2a47;
            line-height: 1.2;
        }
        
        .kpi-value.green { color: #28a745; }
        .kpi-value.red { color: #dc3545; }
        .kpi-value.purple { color: #6f42c1; }
        
        .kpi-label {
            color: #6c757d;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }
        
        /* Main Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        /* Card Base */
        .panel-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .panel-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(30, 42, 71, 0.1);
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .panel-header h3 {
            color: #1e2a47;
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .panel-header h3 i {
            color: #c8a858;
        }
        
        .panel-body {
            padding: 20px;
        }
        
        /* Chart Section */
        .chart-container {
            height: 250px;
            position: relative;
        }
        
        /* Filter Section */
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 150px;
        }
        
        .filter-group label {
            display: block;
            color: #6c757d;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            font-weight: 600;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #333;
            background: #fff;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #c8a858;
            outline: none;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.15);
        }
        
        .btn-filter {
            padding: 10px 25px;
            background: #1e2a47;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-filter:hover {
            background: #2d3a5c;
        }
        
        .btn-reset {
            padding: 10px 20px;
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-reset:hover {
            background: #e9ecef;
        }
        
        /* Activity Table */
        .activity-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .activity-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .activity-table th {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .activity-table td {
            padding: 12px 15px;
            color: #333;
            border-bottom: 1px solid #eee;
            font-size: 0.85rem;
        }
        
        .activity-table tbody tr:hover {
            background: rgba(200, 168, 88, 0.05);
        }
        
        .activity-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .activity-badge.item { background: rgba(23, 162, 184, 0.15); color: #117a8b; }
        .activity-badge.supplier { background: rgba(111, 66, 193, 0.15); color: #6f42c1; }
        .activity-badge.stock-in { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .activity-badge.stock-out { background: rgba(253, 126, 20, 0.15); color: #d56308; }
        .activity-badge.room { background: rgba(200, 168, 88, 0.15); color: #a08038; }
        .activity-badge.other { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
        
        /* Stats List */
        .stats-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .stats-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .stats-list li:last-child {
            border-bottom: none;
        }
        
        .stats-list .label {
            color: #495057;
            font-size: 0.9rem;
        }
        
        .stats-list .value {
            font-weight: 700;
            color: #1e2a47;
            font-size: 1.1rem;
        }
        
        .stats-list .value.gold { color: #c8a858; }
        .stats-list .value.green { color: #28a745; }
        .stats-list .value.red { color: #dc3545; }
        
        /* User Activity Cards */
        .user-activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .user-activity-item:last-child {
            border-bottom: none;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e2a47 0%, #2d3a5c 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            color: #1e2a47;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        
        .user-role {
            color: #6c757d;
            font-size: 0.75rem;
        }
        
        .user-count {
            background: rgba(200, 168, 88, 0.15);
            color: #a08038;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        /* Activity Type Progress */
        .activity-type-item {
            margin-bottom: 15px;
        }
        
        .activity-type-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        
        .activity-type-label {
            color: #495057;
            font-size: 0.85rem;
        }
        
        .activity-type-value {
            color: #1e2a47;
            font-weight: 600;
        }
        
        .activity-progress {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .activity-progress-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .activity-progress-bar.items { background: #17a2b8; }
        .activity-progress-bar.suppliers { background: #6f42c1; }
        .activity-progress-bar.stock-in { background: #28a745; }
        .activity-progress-bar.stock-out { background: #fd7e14; }
        .activity-progress-bar.rooms { background: #c8a858; }
        
        /* Highlights Grid */
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .highlight-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            border: 1px solid #eee;
        }
        
        .highlight-card label {
            display: block;
            color: #6c757d;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .highlight-card span {
            color: #1e2a47;
            font-size: 1rem;
            font-weight: 600;
        }
        
        /* Pagination */
        .table-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: center;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
            color: #adb5bd;
        }
        
        /* Responsive */
        @media (max-width: 1400px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 1200px) {
            .content-grid { grid-template-columns: 1fr; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .audit-header { flex-direction: column; align-items: flex-start; }
            .kpi-grid { grid-template-columns: 1fr; }
            .filter-row { flex-direction: column; }
            .highlights-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
<div class="audit-page">
    <!-- Header -->
    <div class="audit-header">
        <div class="audit-title-section">
            <h1>AUDIT LOGS</h1>
            <p>Monitor and track all system activities and changes</p>
        </div>
        <div class="audit-actions">
            <a href="{{ route('reports.export.pdf', ['filter' => $filter ?? '', 'date_from' => $dateFrom ?? '', 'date_to' => $dateTo ?? '']) }}" class="btn-export pdf">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('reports.export.csv', ['filter' => $filter ?? '', 'date_from' => $dateFrom ?? '', 'date_to' => $dateTo ?? '']) }}" class="btn-export csv">
                <i class="bi bi-filetype-csv"></i> Export CSV
            </a>
        </div>
    </div>
    
    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue"><i class="bi bi-box-arrow-in-right"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ number_format($totalActivities) }}</div>
                <div class="kpi-label">Total Activities</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon green"><i class="bi bi-check-circle"></i></div>
            <div class="kpi-content">
                <div class="kpi-value green">{{ $todayActivities }}</div>
                <div class="kpi-label">Today</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon red"><i class="bi bi-x-circle"></i></div>
            <div class="kpi-content">
                <div class="kpi-value red">{{ $weekActivities }}</div>
                <div class="kpi-label">This Week</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon cyan"><i class="bi bi-people"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $monthActivities }}</div>
                <div class="kpi-label">This Month</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon gold"><i class="bi bi-shield-x"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $totalItems }}</div>
                <div class="kpi-label">Total Items</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon purple"><i class="bi bi-eye"></i></div>
            <div class="kpi-content">
                <div class="kpi-value purple">{{ $totalRooms }}</div>
                <div class="kpi-label">Total Rooms</div>
            </div>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-section">
        <form action="{{ route('reports.index') }}" method="GET" class="filter-row">
            <div class="filter-group">
                <label>Activity Type</label>
                <select name="filter">
                    <option value="" {{ ($filter ?? '') == '' ? 'selected' : '' }}>All Activities</option>
                    <option value="items" {{ ($filter ?? '') == 'items' ? 'selected' : '' }}>Items</option>
                    <option value="suppliers" {{ ($filter ?? '') == 'suppliers' ? 'selected' : '' }}>Suppliers</option>
                    <option value="stock_in" {{ ($filter ?? '') == 'stock_in' ? 'selected' : '' }}>Stock In</option>
                    <option value="stock_out" {{ ($filter ?? '') == 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                    <option value="rooms" {{ ($filter ?? '') == 'rooms' ? 'selected' : '' }}>Rooms</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}">
            </div>
            <div class="filter-group">
                <label>Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}">
            </div>
            <button type="submit" class="btn-filter">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('reports.index') }}" class="btn-reset">Reset</a>
        </form>
    </div>
    
    <!-- Main Content -->
    <div class="content-grid">
        <!-- Activity Trends Chart -->
        <div class="panel-card">
            <div class="panel-header">
                <h3><i class="bi bi-graph-up"></i> Activity Trends (Last 7 Days)</h3>
            </div>
            <div class="panel-body">
                <div class="chart-container">
                    <canvas id="activityTrendsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Activity By Type -->
        <div class="panel-card">
            <div class="panel-header">
                <h3><i class="bi bi-pie-chart"></i> Activity by Type</h3>
            </div>
            <div class="panel-body">
                @php
                    $maxActivity = max(array_values($activityByType)) ?: 1;
                @endphp
                
                <div class="activity-type-item">
                    <div class="activity-type-header">
                        <span class="activity-type-label">Items</span>
                        <span class="activity-type-value">{{ $activityByType['items'] }}</span>
                    </div>
                    <div class="activity-progress">
                        <div class="activity-progress-bar items" style="width: {{ ($activityByType['items'] / $maxActivity) * 100 }}%;"></div>
                    </div>
                </div>
                
                <div class="activity-type-item">
                    <div class="activity-type-header">
                        <span class="activity-type-label">Suppliers</span>
                        <span class="activity-type-value">{{ $activityByType['suppliers'] }}</span>
                    </div>
                    <div class="activity-progress">
                        <div class="activity-progress-bar suppliers" style="width: {{ ($activityByType['suppliers'] / $maxActivity) * 100 }}%;"></div>
                    </div>
                </div>
                
                <div class="activity-type-item">
                    <div class="activity-type-header">
                        <span class="activity-type-label">Stock In</span>
                        <span class="activity-type-value">{{ $activityByType['stock_in'] }}</span>
                    </div>
                    <div class="activity-progress">
                        <div class="activity-progress-bar stock-in" style="width: {{ ($activityByType['stock_in'] / $maxActivity) * 100 }}%;"></div>
                    </div>
                </div>
                
                <div class="activity-type-item">
                    <div class="activity-type-header">
                        <span class="activity-type-label">Stock Out</span>
                        <span class="activity-type-value">{{ $activityByType['stock_out'] }}</span>
                    </div>
                    <div class="activity-progress">
                        <div class="activity-progress-bar stock-out" style="width: {{ ($activityByType['stock_out'] / $maxActivity) * 100 }}%;"></div>
                    </div>
                </div>
                
                <div class="activity-type-item">
                    <div class="activity-type-header">
                        <span class="activity-type-label">Rooms</span>
                        <span class="activity-type-value">{{ $activityByType['rooms'] }}</span>
                    </div>
                    <div class="activity-progress">
                        <div class="activity-progress-bar rooms" style="width: {{ ($activityByType['rooms'] / $maxActivity) * 100 }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Second Row -->
    <div class="content-grid">
        <!-- Inventory Overview -->
        <div class="panel-card">
            <div class="panel-header">
                <h3><i class="bi bi-box-seam"></i> Inventory Overview</h3>
            </div>
            <div class="panel-body">
                <ul class="stats-list">
                    <li>
                        <span class="label">Total Items in System</span>
                        <span class="value">{{ $totalItems }}</span>
                    </li>
                    <li>
                        <span class="label">Stock-Ins This Month</span>
                        <span class="value green">{{ $totalStockInThisMonth }}</span>
                    </li>
                    <li>
                        <span class="label">Stock-Outs This Month</span>
                        <span class="value gold">{{ $totalStockOutThisMonth }}</span>
                    </li>
                    <li>
                        <span class="label">Low Stock Items (≤5)</span>
                        <span class="value" style="color: #fd7e14;">{{ $lowStockItems }}</span>
                    </li>
                    <li>
                        <span class="label">Out of Stock Items</span>
                        <span class="value red">{{ $outOfStockItems }}</span>
                    </li>
                    <li>
                        <span class="label">Total Returned Items</span>
                        <span class="value">{{ $totalReturnedItems }}</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Top Users -->
        <div class="panel-card">
            <div class="panel-header">
                <h3><i class="bi bi-people"></i> Most Active Users</h3>
            </div>
            <div class="panel-body">
                @forelse($userActivities as $userActivity)
                    <div class="user-activity-item">
                        <div class="user-avatar">
                            {{ strtoupper(substr($userActivity->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ $userActivity->user->name ?? 'Unknown' }}</div>
                            <div class="user-role">{{ ucfirst(str_replace('_', ' ', $userActivity->user->role ?? 'N/A')) }}</div>
                        </div>
                        <span class="user-count">{{ $userActivity->count }} activities</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-person-x"></i>
                        <p>No user activity data</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Third Row -->
    <div class="content-grid">
        <!-- Room Status -->
        <div class="panel-card">
            <div class="panel-header">
                <h3><i class="bi bi-door-closed"></i> Room Status Overview</h3>
            </div>
            <div class="panel-body">
                <div class="highlights-grid">
                    <div class="highlight-card">
                        <label>Total Rooms</label>
                        <span>{{ $totalRooms }}</span>
                    </div>
                    <div class="highlight-card">
                        <label>Occupied</label>
                        <span style="color: #28a745;">{{ $occupiedRooms }}</span>
                    </div>
                    <div class="highlight-card">
                        <label>Empty</label>
                        <span style="color: #dc3545;">{{ $emptyRooms }}</span>
                    </div>
                    <div class="highlight-card">
                        <label>Restocked</label>
                        <span style="color: #17a2b8;">{{ $restockedRooms }}</span>
                    </div>
                    <div class="highlight-card">
                        <label>Total Categories</label>
                        <span>{{ $totalCategories }}</span>
                    </div>
                    <div class="highlight-card">
                        <label>Total Suppliers</label>
                        <span>{{ $totalSuppliers }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Highlights -->
        <div class="panel-card">
            <div class="panel-header">
                <h3><i class="bi bi-star"></i> Highlights</h3>
            </div>
            <div class="panel-body">
                <ul class="stats-list">
                    <li>
                        <span class="label">Top Stocked-Out Item</span>
                        <span class="value gold">{{ $topStockedOutItem->item->name ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="label">Top Stocked-In Item</span>
                        <span class="value green">{{ $topStockedInItem->item->name ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="label">Most Active User</span>
                        <span class="value">{{ $mostActiveUser->user->name ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="label">Total Stock Value</span>
                        <span class="value gold">₱{{ number_format($totalStockValue, 2) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Activity Log Table -->
    <div class="panel-card">
        <div class="panel-header">
            <h3><i class="bi bi-list-ul"></i> Activity Log</h3>
            <span style="color: #6c757d; font-size: 0.85rem;">{{ $reports->total() }} total records</span>
        </div>
        <table class="activity-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Activity</th>
                    <th style="width: 12%;">Type</th>
                    <th style="width: 18%;">User</th>
                    <th style="width: 20%;">Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    @php
                        $activityType = 'other';
                        $activityLower = strtolower($report->activity);
                        if (str_contains($activityLower, 'item')) $activityType = 'item';
                        elseif (str_contains($activityLower, 'supplier')) $activityType = 'supplier';
                        elseif (str_contains($activityLower, 'stock-in')) $activityType = 'stock-in';
                        elseif (str_contains($activityLower, 'stocked out')) $activityType = 'stock-out';
                        elseif (str_contains($activityLower, 'room')) $activityType = 'room';
                    @endphp
                    <tr>
                        <td>{{ Str::limit($report->activity, 70) }}</td>
                        <td>
                            <span class="activity-badge {{ $activityType }}">
                                {{ ucfirst(str_replace('-', ' ', $activityType)) }}
                            </span>
                        </td>
                        <td>{{ $report->user ? $report->user->name : 'System' }}</td>
                        <td>{{ $report->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="bi bi-journal-x"></i>
                                <p>No activity logs found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="table-footer">
            {{ $reports->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Trends Chart
    const trendsCtx = document.getElementById('activityTrendsChart').getContext('2d');
    const trendsData = @json($activityTrends);
    
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: trendsData.map(item => item.date),
            datasets: [{
                label: 'Activities',
                data: trendsData.map(item => item.count),
                borderColor: '#c8a858',
                backgroundColor: 'rgba(200, 168, 88, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#c8a858',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e2a47',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#6c757d'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        color: '#6c757d'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endsection
