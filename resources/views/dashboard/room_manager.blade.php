@extends('dashboard')

@section('title', 'Room Manager Dashboard - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .room-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .room-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .room-title i {
            color: #c8a858;
        }
        
        .room-date-badge {
            background: linear-gradient(135deg, #1e2a47 0%, #2d3a5c 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Modern Stats Cards */
        .room-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .room-stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .room-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .room-stat-card.gold::before { background: linear-gradient(180deg, #c8a858 0%, #e0c078 100%); }
        .room-stat-card.navy::before { background: linear-gradient(180deg, #1e2a47 0%, #2d3a5c 100%); }
        .room-stat-card.success::before { background: linear-gradient(180deg, #28a745 0%, #34ce57 100%); }
        .room-stat-card.danger::before { background: linear-gradient(180deg, #dc3545 0%, #e85c68 100%); }
        .room-stat-card.warning::before { background: linear-gradient(180deg, #ffc107 0%, #ffcd39 100%); }
        .room-stat-card.info::before { background: linear-gradient(180deg, #17a2b8 0%, #3ab5c6 100%); }
        
        .room-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .room-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        
        .room-stat-icon.gold { background: rgba(200, 168, 88, 0.15); color: #c8a858; }
        .room-stat-icon.navy { background: rgba(30, 42, 71, 0.1); color: #1e2a47; }
        .room-stat-icon.success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .room-stat-icon.danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .room-stat-icon.warning { background: rgba(255, 193, 7, 0.2); color: #d39e00; }
        .room-stat-icon.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        
        .room-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        
        .room-stat-value.gold { color: #c8a858; }
        .room-stat-value.danger { color: #dc3545; }
        .room-stat-value.success { color: #28a745; }
        
        .room-stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .room-stat-subtitle {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        /* Alert Cards Row */
        .room-alert-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 768px) {
            .room-alert-grid { grid-template-columns: 1fr; }
        }
        
        .room-alert-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .room-alert-card.danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, white 100%);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .room-alert-card.warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.08) 0%, white 100%);
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .room-alert-card.success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, white 100%);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .room-alert-value {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
        }
        
        .room-alert-value.danger { color: #dc3545; }
        .room-alert-value.warning { color: #d39e00; }
        .room-alert-value.success { color: #28a745; }
        
        .room-alert-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .room-alert-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        /* Chart Cards */
        .room-chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .room-chart-grid { grid-template-columns: 1fr; }
        }
        
        .room-chart-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .room-chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .room-chart-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .room-chart-title i {
            color: #c8a858;
            font-size: 1.1rem;
        }
        
        .room-chart-canvas {
            height: 280px;
            position: relative;
        }
        
        /* Data Tables */
        .room-table-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .room-table-grid { grid-template-columns: 1fr; }
        }
        
        .room-table-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .room-table-full {
            grid-column: 1 / -1;
        }
        
        .room-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .room-table-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .room-table-title i {
            color: #c8a858;
        }
        
        .room-data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .room-data-table thead th {
            background: #f8f9fa;
            padding: 12px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .room-data-table thead th:first-child {
            border-radius: 8px 0 0 0;
        }
        
        .room-data-table thead th:last-child {
            border-radius: 0 8px 0 0;
        }
        
        .room-data-table tbody td {
            padding: 14px 16px;
            font-size: 0.875rem;
            color: #374151;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .room-data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .room-data-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .room-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .room-badge.danger {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        
        .room-badge.warning {
            background: rgba(255, 193, 7, 0.2);
            color: #d39e00;
        }
        
        .room-badge.success {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        
        /* Top Rooms List */
        .room-top-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .room-top-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        
        .room-top-item:hover {
            background: #f0f1f3;
        }
        
        .room-top-rank {
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
        
        .room-top-rank.gold { background: #c8a858; }
        
        .room-top-info {
            flex: 1;
        }
        
        .room-top-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e2a47;
        }
        
        .room-top-meta {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        .room-top-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #c8a858;
        }
        
        .room-scrollable {
            max-height: 280px;
            overflow-y: auto;
        }
        
        .room-scrollable::-webkit-scrollbar {
            width: 6px;
        }
        
        .room-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .room-scrollable::-webkit-scrollbar-thumb {
            background: #c8a858;
            border-radius: 3px;
        }
        
        /* Activity Items */
        .room-activity-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .room-activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 3px solid #c8a858;
        }
        
        .room-activity-item i {
            color: #c8a858;
            font-size: 1rem;
            margin-top: 2px;
        }
        
        .room-activity-content {
            flex: 1;
        }
        
        .room-activity-text {
            font-size: 0.875rem;
            color: #374151;
            display: block;
        }
        
        .room-activity-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        /* Pagination */
        .room-pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 16px;
        }
        
        .room-page-btn {
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
        
        .room-page-btn:hover {
            background: #e0e0e0;
        }
        
        .room-page-btn.active {
            background: #c8a858;
            color: white;
        }
    </style>
@endsection

@section('content')
<div class="container py-4 room-container">
    <!-- Header -->
    <div class="room-header">
        <h2 class="room-title">
            <i class="bi bi-building"></i> Room Manager Dashboard
        </h2>
        <span class="room-date-badge">
            <i class="bi bi-calendar3"></i> {{ now()->format('F d, Y') }}
        </span>
    </div>

    <!-- Main Stats Row -->
    <div class="room-stats-grid">
        <div class="room-stat-card navy">
            <div class="room-stat-icon navy">
                <i class="bi bi-door-closed"></i>
            </div>
            <div class="room-stat-value">{{ $totalRooms }}</div>
            <div class="room-stat-label">Total Rooms</div>
            <div class="room-stat-subtitle">All rooms in system</div>
        </div>
        
        <div class="room-stat-card success">
            <div class="room-stat-icon success">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="room-stat-value success">{{ $restockedRooms }}</div>
            <div class="room-stat-label">Restocked</div>
            <div class="room-stat-subtitle">Ready for guests</div>
        </div>
        
        <div class="room-stat-card danger">
            <div class="room-stat-icon danger">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="room-stat-value danger">{{ $occupiedRooms }}</div>
            <div class="room-stat-label">Occupied</div>
            <div class="room-stat-subtitle">Guest checked in</div>
        </div>
        
        <div class="room-stat-card warning">
            <div class="room-stat-icon warning">
                <i class="bi bi-door-open"></i>
            </div>
            <div class="room-stat-value">{{ $emptyRooms }}</div>
            <div class="room-stat-label">Empty</div>
            <div class="room-stat-subtitle">Need restocking</div>
        </div>
        
        <div class="room-stat-card info">
            <div class="room-stat-icon info">
                <i class="bi bi-pie-chart"></i>
            </div>
            <div class="room-stat-value">{{ $occupancyRate }}%</div>
            <div class="room-stat-label">Occupancy Rate</div>
            <div class="room-stat-subtitle">Room utilization</div>
        </div>
    </div>

    <!-- Alert Cards -->
    <div class="room-alert-grid">
        <div class="room-alert-card danger">
            <div class="room-alert-value danger">{{ $totalReturnedItems }}</div>
            <div class="room-alert-label">Returned Items</div>
            <div class="room-alert-hint"><i class="bi bi-arrow-return-left"></i> Items reported</div>
        </div>
        
        <div class="room-alert-card warning">
            <div class="room-alert-value warning">{{ $pendingReplacements }}</div>
            <div class="room-alert-label">Need Replacement</div>
            <div class="room-alert-hint"><i class="bi bi-exclamation-triangle"></i> Total quantity</div>
        </div>
        
        <div class="room-alert-card success">
            <div class="room-alert-value success">{{ $totalRooms > 0 ? round(($restockedRooms / $totalRooms) * 100, 1) : 0 }}%</div>
            <div class="room-alert-label">Ready Rate</div>
            <div class="room-alert-hint"><i class="bi bi-check-circle"></i> Rooms ready for guests</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="room-chart-grid">
        <div class="room-chart-card">
            <div class="room-chart-header">
                <h5 class="room-chart-title"><i class="bi bi-bar-chart-fill"></i> Room Occupancy by Type</h5>
            </div>
            <div class="room-chart-canvas">
                <canvas id="roomTypeChart"></canvas>
            </div>
        </div>
        
        <div class="room-chart-card">
            <div class="room-chart-header">
                <h5 class="room-chart-title"><i class="bi bi-graph-up"></i> Daily Activity (7 Days)</h5>
            </div>
            <div class="room-chart-canvas">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="room-table-grid">
        <!-- Items Needing Replacement -->
        <div class="room-table-card">
            <div class="room-table-header">
                <h5 class="room-table-title"><i class="bi bi-exclamation-triangle-fill"></i> Items Needing Replacement</h5>
            </div>
            <div class="room-scrollable">
                <table class="room-data-table" id="replacementTable">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReturns as $return)
                            <tr class="paginated-row">
                                <td>{{ $return->item_id }}</td>
                                <td><strong>{{ $return->item->name ?? 'N/A' }}</strong></td>
                                <td>{{ $return->quantity }}</td>
                                <td>
                                    <span class="room-badge warning">{{ ucfirst($return->reason ?? 'Unknown') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle text-success"></i> No items needing replacement
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="room-pagination" id="replacementTablePagination"></div>
        </div>

        <!-- Recent Stock Out Activities -->
        <div class="room-table-card">
            <div class="room-table-header">
                <h5 class="room-table-title"><i class="bi bi-box-arrow-right"></i> Recent Stock Out</h5>
            </div>
            <div class="room-scrollable">
                <div class="room-activity-list" id="stockOutList">
                    @forelse($recentStockOuts as $stockOut)
                        <div class="room-activity-item paginated-row">
                            <i class="bi bi-box-seam"></i>
                            <div class="room-activity-content">
                                <span class="room-activity-text">
                                    <strong>{{ $stockOut->quantity }}</strong> × {{ $stockOut->item->name ?? 'Item' }}
                                </span>
                                <span class="room-activity-time">{{ $stockOut->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No recent transactions</p>
                    @endforelse
                </div>
            </div>
            <div class="room-pagination" id="stockOutListPagination"></div>
        </div>
    </div>

    <!-- Rooms with Most Items & Room Types Summary -->
    <div class="room-table-grid">
        <div class="room-table-card">
            <div class="room-table-header">
                <h5 class="room-table-title"><i class="bi bi-trophy"></i> Rooms with Most Items</h5>
            </div>
            <div class="room-top-list" id="topRoomsList">
                @forelse($roomsWithItems as $index => $room)
                    <div class="room-top-item paginated-row">
                        <div class="room-top-rank {{ $index === 0 ? 'gold' : '' }}">{{ $index + 1 }}</div>
                        <div class="room-top-info">
                            <div class="room-top-name">Room {{ $room->room_id }}</div>
                            <div class="room-top-meta">{{ $room->type->name ?? 'N/A' }}</div>
                        </div>
                        <div class="room-top-value">{{ $room->items_count }} items</div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3">No data available</p>
                @endforelse
            </div>
            <div class="room-pagination" id="topRoomsListPagination"></div>
        </div>
        
        <div class="room-table-card">
            <div class="room-table-header">
                <h5 class="room-table-title"><i class="bi bi-list-columns-reverse"></i> Room Types Summary</h5>
            </div>
            <div class="room-scrollable">
                <table class="room-data-table" id="roomTypesTable">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Restocked</th>
                            <th>Occupied</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roomTypes as $type)
                            @php
                                $typeRestocked = \App\Models\Room::where('roomtype_id', $type->roomtype_id)->where('status', 'restocked')->count();
                                $typeOccupied = \App\Models\Room::where('roomtype_id', $type->roomtype_id)->where('status', 'occupied')->count();
                                $typeEmpty = \App\Models\Room::where('roomtype_id', $type->roomtype_id)->where('status', 'empty')->count();
                            @endphp
                            <tr class="paginated-row">
                                <td><strong>{{ $type->name }}</strong></td>
                                <td>{{ $type->rooms_count }}</td>
                                <td>{{ $typeRestocked }}</td>
                                <td>{{ $typeOccupied }}</td>
                                <td>
                                    @if($typeRestocked > 0)
                                        <span class="room-badge success">Ready</span>
                                    @elseif($typeEmpty > 0)
                                        <span class="room-badge warning">Need Stock</span>
                                    @else
                                        <span class="room-badge danger">Full</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No room types defined</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="room-pagination" id="roomTypesTablePagination"></div>
        </div>
    </div>
</div>

<script>
    // Room Occupancy by Type Chart (Stacked Bar)
    const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
    new Chart(roomTypeCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($typeLabels) !!},
            datasets: [{
                label: 'Occupied',
                data: {!! json_encode($typeOccupied) !!},
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: '#dc3545',
                borderWidth: 2,
                borderRadius: 8
            }, {
                label: 'Restocked',
                data: {!! json_encode($typeRestocked) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: '#28a745',
                borderWidth: 2,
                borderRadius: 8
            }, {
                label: 'Empty',
                data: {!! json_encode($typeEmpty) !!},
                backgroundColor: 'rgba(108, 117, 125, 0.7)',
                borderColor: '#6c757d',
                borderWidth: 2,
                borderRadius: 8
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
                    stacked: true,
                    ticks: { color: '#6c757d', font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }
                },
                x: {
                    stacked: true,
                    ticks: { color: '#6c757d', font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    // Daily Activity Line Chart
    const dailyCtx = document.getElementById('dailyActivityChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyLabels) !!},
            datasets: [{
                label: 'Room Transactions',
                data: {!! json_encode($dailyActivity) !!},
                borderColor: '#1e2a47',
                backgroundColor: 'rgba(30, 42, 71, 0.15)',
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
            prevBtn.className = 'room-page-btn';
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
            nextBtn.className = 'room-page-btn';
            nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
            nextBtn.disabled = page === totalPages;
            nextBtn.onclick = () => { if (page < totalPages) showPage(page + 1); };
            paginationContainer.appendChild(nextBtn);
            
            currentPage = page;
        }
        
        showPage(1);
    }
    
    // Initialize pagination for tables (3 items per page)
    initPagination('replacementTable', 'replacementTablePagination', 3);
    initPagination('stockOutList', 'stockOutListPagination', 3);
    initPagination('topRoomsList', 'topRoomsListPagination', 3);
    initPagination('roomTypesTable', 'roomTypesTablePagination', 3);
</script>
@endsection
