@extends('dashboard')

@section('title', 'Security Dashboard - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .security-dashboard { padding: 20px 30px; }
        
        .security-title {
            color: #1e2a47;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .security-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }
        
        /* Stats Grid - 6 columns */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1400px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        
        .stat-icon.navy { background: rgba(30, 42, 71, 0.1); color: #1e2a47; }
        .stat-icon.success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .stat-icon.danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .stat-icon.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        .stat-icon.warning { background: rgba(255, 193, 7, 0.2); color: #d39e00; }
        .stat-icon.purple { background: rgba(111, 66, 193, 0.15); color: #6f42c1; }
        
        .stat-content { flex: 1; }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            line-height: 1.2;
        }
        
        .stat-value.success { color: #28a745; }
        .stat-value.danger { color: #dc3545; }
        .stat-value.info { color: #17a2b8; }
        .stat-value.warning { color: #d39e00; }
        .stat-value.purple { color: #6f42c1; }
        
        .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        /* Section Grid */
        .section-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1200px) { .section-grid { grid-template-columns: 1fr; } }
        
        .section-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #eee;
            overflow: hidden;
        }
        
        .section-header {
            background: linear-gradient(135deg, #1e2a47 0%, #2c3e5c 100%);
            color: white;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-header.danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .section-header.warning { background: linear-gradient(135deg, #d39e00 0%, #b38600 100%); }
        .section-header.info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }
        .section-header.purple { background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%); }
        
        .section-header i { font-size: 1.1rem; }
        
        .section-body { padding: 0; }
        
        /* Tables */
        .security-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .security-table thead {
            background: #f8f9fa;
        }
        
        .security-table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .security-table td {
            padding: 12px 16px;
            font-size: 0.875rem;
            color: #333;
            border-bottom: 1px solid #eee;
        }
        
        .security-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .security-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .badge-danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .badge-warning { background: rgba(255, 193, 7, 0.2); color: #d39e00; }
        .badge-info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        .badge-purple { background: rgba(111, 66, 193, 0.15); color: #6f42c1; }
        .badge-secondary { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
        
        /* IP Code styling */
        .ip-code {
            background: #f1f3f4;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            font-size: 0.8rem;
            color: #1e2a47;
        }
        
        /* Chart Container */
        .chart-container {
            position: relative;
            height: 250px;
            padding: 20px;
        }
        
        /* Filter Bar */
        .filter-bar {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 16px 20px;
        }
        
        .filter-bar form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-group label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #495057;
            white-space: nowrap;
        }
        
        .filter-group input,
        .filter-group select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            border-color: #1e2a47;
            box-shadow: 0 0 0 3px rgba(30, 42, 71, 0.1);
            outline: none;
        }
        
        .filter-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
        }
        
        .btn-filter {
            background: linear-gradient(135deg, #1e2a47 0%, #2c3e5c 100%);
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-filter:hover {
            background: linear-gradient(135deg, #2c3e5c 0%, #1e2a47 100%);
        }
        
        .btn-reset {
            background: white;
            border: 1px solid #ced4da;
            color: #495057;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .btn-reset:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
        }
        
        /* Mini Stats in Cards */
        .mini-stats {
            display: flex;
            gap: 20px;
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .mini-stat {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .mini-stat-value {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .mini-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        /* Role Pills */
        .role-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px;
        }
        
        .role-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            padding: 10px 16px;
            border-radius: 25px;
            border: 1px solid #eee;
        }
        
        .role-pill-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        
        .role-pill-icon.admin { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .role-pill-icon.inventory { background: rgba(200, 168, 88, 0.2); color: #a08038; }
        .role-pill-icon.room { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        
        .role-pill-content { line-height: 1.2; }
        .role-pill-count { font-size: 1.1rem; font-weight: 700; color: #1e2a47; }
        .role-pill-label { font-size: 0.7rem; color: #6c757d; text-transform: uppercase; }
        
        /* Peak Hours */
        .peak-hours-list {
            padding: 15px 20px;
        }
        
        .peak-hour-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .peak-hour-item:last-child { border-bottom: none; }
        
        .peak-hour-rank {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #1e2a47;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }
        
        .peak-hour-rank.gold { background: #c8a858; }
        .peak-hour-rank.silver { background: #6c757d; }
        .peak-hour-rank.bronze { background: #cd7f32; }
        
        .peak-hour-time {
            flex: 1;
            font-weight: 600;
            color: #1e2a47;
        }
        
        .peak-hour-count {
            font-size: 0.85rem;
            color: #6c757d;
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
            opacity: 0.5;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .filter-actions { margin-left: 0; width: 100%; justify-content: flex-end; }
        }
        @media (max-width: 768px) {
            .filter-bar form { flex-direction: column; align-items: stretch; }
            .filter-group { width: 100%; }
            .filter-group input, .filter-group select { flex: 1; }
            .filter-actions { width: 100%; }
        }
        
        /* Section Title */
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i { color: #c8a858; }
        
        /* Card Pagination */
        .card-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            font-size: 0.8rem;
        }
        
        .card-pagination .per-page-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-pagination .per-page-selector label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .card-pagination .per-page-select {
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            font-size: 0.8rem;
            background: white;
        }
        
        .card-pagination .pagination-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-pagination .page-info {
            color: #6c757d;
        }
        
        .card-pagination .page-btn {
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            color: #1e2a47;
            transition: all 0.2s;
            user-select: none;
        }
        
        .card-pagination .page-btn:hover:not(.disabled) {
            background: #e9ecef;
        }
        
        .card-pagination .page-btn.disabled {
            color: #adb5bd;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
<div class="security-dashboard">
    <h2 class="security-title">SECURITY DASHBOARD</h2>
    <p class="security-subtitle">Monitor login activity, security events, and system access • {{ \Carbon\Carbon::parse($dateFrom)->format('M d') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</p>
    
    <!-- Stats Grid - 6 Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="bi bi-box-arrow-in-right"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalLogins }}</div>
                <div class="stat-label">Total Logins</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value success">{{ $successfulLogins }}</div>
                <div class="stat-label">Successful</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger"><i class="bi bi-x-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value danger">{{ $failedLogins }}</div>
                <div class="stat-label">Failed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info"><i class="bi bi-people"></i></div>
            <div class="stat-content">
                <div class="stat-value info">{{ $activeUsers }}</div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-shield-slash"></i></div>
            <div class="stat-content">
                <div class="stat-value warning">{{ $totalBlockedIps }}</div>
                <div class="stat-label">Blocked IPs</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-eye"></i></div>
            <div class="stat-content">
                <div class="stat-value purple">{{ $unmaskLogCount }}</div>
                <div class="stat-label">Data Access</div>
            </div>
        </div>
    </div>
    
    <!-- Charts & Analytics Section -->
    <div class="section-grid">
        <!-- Login Trends Chart -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-graph-up"></i> Login Trends (Last 7 Days)
            </div>
            <div class="section-body">
                <div class="chart-container">
                    <canvas id="loginTrendsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- User Login Analytics -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-bar-chart"></i> User Login Analytics
            </div>
            <div class="section-body">
                <table class="security-table" id="userLoginTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th style="text-align: center;">Total</th>
                            <th style="text-align: center;">Success</th>
                            <th style="text-align: center;">Failed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userLoginCounts as $userLog)
                            <tr class="paginated-row">
                                <td><strong>{{ $userLog->user_name ?? 'Unknown' }}</strong></td>
                                <td style="text-align: center;">{{ $userLog->login_count }}</td>
                                <td style="text-align: center;"><span class="badge-status badge-success">{{ $userLog->successful_logins }}</span></td>
                                <td style="text-align: center;"><span class="badge-status badge-danger">{{ $userLog->failed_logins }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No login data available</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pagination" id="userLoginPagination"></div>
        </div>
        
        <!-- Recent Failed Attempts -->
        <div class="section-card">
            <div class="section-header danger">
                <i class="bi bi-exclamation-triangle"></i> Recent Failed Login Attempts
            </div>
            <div class="section-body">
                <table class="security-table" id="failedAttemptsTable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>IP Address</th>
                            <th>Reason</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentFailedAttempts as $attempt)
                            <tr class="paginated-row">
                                <td><strong>{{ $attempt->user_name }}</strong></td>
                                <td><span class="ip-code">{{ $attempt->ip_address }}</span></td>
                                <td>{{ $attempt->failure_reason ?? 'Invalid credentials' }}</td>
                                <td>{{ $attempt->login_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-shield-check"></i>
                                    <p>No failed attempts recorded</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pagination" id="failedAttemptsPagination"></div>
        </div>
        
        <!-- Blocked IPs -->
        <div class="section-card">
            <div class="section-header warning">
                <i class="bi bi-shield-slash"></i> Blocked IP Addresses
            </div>
            <div class="section-body">
                <table class="security-table" id="blockedIpsTable">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Reason</th>
                            <th>Duration</th>
                            <th>Blocked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blockedIps as $blocked)
                            <tr class="paginated-row">
                                <td><span class="ip-code">{{ $blocked->ip_address }}</span></td>
                                <td>{{ $blocked->reason ?? 'Security violation' }}</td>
                                <td><span class="badge-status badge-warning">{{ $blocked->duration_display }}</span></td>
                                <td>{{ $blocked->blocked_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-shield-check"></i>
                                    <p>No blocked IPs</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pagination" id="blockedIpsPagination"></div>
        </div>
        
        <!-- System Logs -->
        <div class="section-card">
            <div class="section-header info">
                <i class="bi bi-journal-text"></i> Recent Security Logs
            </div>
            <div class="mini-stats">
                <div class="mini-stat">
                    <div class="mini-stat-value" style="color: #17a2b8;">{{ $securityLogCount }}</div>
                    <div class="mini-stat-label">Security Events</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-value" style="color: #dc3545;">{{ $criticalLogCount }}</div>
                    <div class="mini-stat-label">Critical/Errors</div>
                </div>
            </div>
            <div class="section-body">
                <table class="security-table" id="systemLogsTable">
                    <thead>
                        <tr>
                            <th>Channel</th>
                            <th>Level</th>
                            <th>Action</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSystemLogs as $log)
                            <tr class="paginated-row">
                                <td><span class="badge-status badge-info">{{ ucfirst($log->channel) }}</span></td>
                                <td>
                                    @if($log->level == 'critical' || $log->level == 'error')
                                        <span class="badge-status badge-danger">{{ ucfirst($log->level) }}</span>
                                    @elseif($log->level == 'warning')
                                        <span class="badge-status badge-warning">{{ ucfirst($log->level) }}</span>
                                    @else
                                        <span class="badge-status badge-secondary">{{ ucfirst($log->level) }}</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($log->action, 25) }}</td>
                                <td>{{ $log->user_name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-journal-x"></i>
                                    <p>No security logs</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pagination" id="systemLogsPagination"></div>
        </div>
        
        <!-- Data Access Logs -->
        <div class="section-card">
            <div class="section-header purple">
                <i class="bi bi-eye"></i> Sensitive Data Access Log
            </div>
            <div class="section-body">
                <table class="security-table" id="unmaskLogsTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Data Type</th>
                            <th>Field</th>
                            <th>Accessed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUnmaskLogs as $unmask)
                            <tr class="paginated-row">
                                <td><strong>{{ $unmask->user->name ?? 'Unknown' }}</strong></td>
                                <td><span class="badge-status badge-purple">{{ ucfirst($unmask->entity_type) }}</span></td>
                                <td>{{ $unmask->field_name }}</td>
                                <td>{{ $unmask->unmasked_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-shield-lock"></i>
                                    <p>No data access logs</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-pagination" id="unmaskLogsPagination"></div>
        </div>
    </div>
    
    <!-- User Roles & Peak Hours -->
    <div class="section-grid">
        <!-- User Roles Breakdown -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-person-badge"></i> Users by Role
            </div>
            <div class="role-pills">
                <div class="role-pill">
                    <div class="role-pill-icon admin"><i class="bi bi-shield-lock"></i></div>
                    <div class="role-pill-content">
                        <div class="role-pill-count">{{ $usersByRole['admin'] ?? 0 }}</div>
                        <div class="role-pill-label">Administrators</div>
                    </div>
                </div>
                <div class="role-pill">
                    <div class="role-pill-icon inventory"><i class="bi bi-box-seam"></i></div>
                    <div class="role-pill-content">
                        <div class="role-pill-count">{{ $usersByRole['inventory_manager'] ?? 0 }}</div>
                        <div class="role-pill-label">Inventory Managers</div>
                    </div>
                </div>
                <div class="role-pill">
                    <div class="role-pill-icon room"><i class="bi bi-door-open"></i></div>
                    <div class="role-pill-content">
                        <div class="role-pill-count">{{ $usersByRole['room_manager'] ?? 0 }}</div>
                        <div class="role-pill-label">Room Managers</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Peak Login Hours -->
        <div class="section-card">
            <div class="section-header">
                <i class="bi bi-clock-history"></i> Peak Login Hours (Last 7 Days)
            </div>
            <div class="peak-hours-list">
                @forelse($peakHours as $index => $hour)
                    <div class="peak-hour-item">
                        <div class="peak-hour-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="peak-hour-time">
                            {{ str_pad($hour->hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($hour->hour, 2, '0', STR_PAD_LEFT) }}:59
                        </div>
                        <div class="peak-hour-count">{{ $hour->count }} logins</div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-clock"></i>
                        <p>No peak hour data available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Login History - Full Width -->
    <div class="section-title"><i class="bi bi-clock-history"></i> Complete Login History</div>
    <div class="section-card">
        <div class="filter-bar">
            <form action="{{ route('security.index') }}" method="GET">
                <div class="filter-group">
                    <label>From:</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="filter-group">
                    <label>To:</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}">
                </div>
                <div class="filter-group">
                    <select name="user_id">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="success" {{ $status == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('security.index') }}" class="btn-reset"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </form>
        </div>
        <div class="section-body">
            <table class="security-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Duration</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loginLogs as $log)
                        <tr>
                            <td><strong>{{ $log->user_name }}</strong></td>
                            <td><span class="ip-code">{{ $log->ip_address }}</span></td>
                            <td>
                                @if($log->status == 'success')
                                    <span class="badge-status badge-success"><i class="bi bi-check-circle me-1"></i>Success</span>
                                @else
                                    <span class="badge-status badge-danger"><i class="bi bi-x-circle me-1"></i>Failed</span>
                                @endif
                            </td>
                            <td>{{ $log->login_at->format('M d, Y H:i:s') }}</td>
                            <td>{{ $log->logout_at ? $log->logout_at->format('M d, Y H:i:s') : '-' }}</td>
                            <td>{{ $log->formatted_duration }}</td>
                            <td style="max-width: 200px;" title="{{ $log->user_agent }}">
                                {{ Str::limit($log->user_agent, 35) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>No login logs found for the selected period</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @include('partials.pagination', ['paginator' => $loginLogs])
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Login Trends Chart
    const ctx = document.getElementById('loginTrendsChart').getContext('2d');
    const loginTrends = @json($loginTrends);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: loginTrends.map(item => item.date),
            datasets: [
                {
                    label: 'Successful',
                    data: loginTrends.map(item => item.success),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#28a745'
                },
                {
                    label: 'Failed',
                    data: loginTrends.map(item => item.failed),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#dc3545'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
    
    // Card Pagination Function
    function initCardPagination(tableId, paginationId, defaultPerPage = 5) {
        const table = document.getElementById(tableId);
        const pagination = document.getElementById(paginationId);
        if (!table || !pagination) return;
        
        const rows = table.querySelectorAll('tbody tr.paginated-row');
        const items = Array.from(rows);
        let itemsPerPage = defaultPerPage;
        let currentPage = 1;
        
        function getTotalPages() {
            return Math.max(1, Math.ceil(items.length / itemsPerPage));
        }
        
        function showPage(page) {
            currentPage = page;
            items.forEach((item, index) => {
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                item.style.display = (index >= start && index < end) ? '' : 'none';
            });
            renderPagination();
        }
        
        function changePerPage(value) {
            itemsPerPage = parseInt(value);
            currentPage = 1;
            showPage(1);
        }
        
        function renderPagination() {
            if (items.length === 0) {
                pagination.style.display = 'none';
                return;
            }
            
            pagination.style.display = 'flex';
            const totalPages = getTotalPages();
            
            pagination.innerHTML = `
                <div class="per-page-selector">
                    <label>Show:</label>
                    <select class="per-page-select">
                        <option value="5" ${itemsPerPage === 5 ? 'selected' : ''}>5</option>
                        <option value="10" ${itemsPerPage === 10 ? 'selected' : ''}>10</option>
                        <option value="25" ${itemsPerPage === 25 ? 'selected' : ''}>25</option>
                    </select>
                </div>
                <div class="pagination-controls">
                    <span class="page-btn ${currentPage === 1 ? 'disabled' : ''}" data-action="prev">&lsaquo; Prev</span>
                    <span class="page-info">Page ${currentPage} of ${totalPages}</span>
                    <span class="page-btn ${currentPage === totalPages ? 'disabled' : ''}" data-action="next">Next &rsaquo;</span>
                </div>
            `;
            
            pagination.querySelector('.per-page-select').addEventListener('change', (e) => changePerPage(e.target.value));
            pagination.querySelector('[data-action="prev"]').addEventListener('click', () => { if (currentPage > 1) showPage(currentPage - 1); });
            pagination.querySelector('[data-action="next"]').addEventListener('click', () => { if (currentPage < totalPages) showPage(currentPage + 1); });
        }
        
        showPage(1);
    }
    
    // Initialize pagination for all card tables
    initCardPagination('userLoginTable', 'userLoginPagination', 5);
    initCardPagination('failedAttemptsTable', 'failedAttemptsPagination', 5);
    initCardPagination('blockedIpsTable', 'blockedIpsPagination', 5);
    initCardPagination('systemLogsTable', 'systemLogsPagination', 5);
    initCardPagination('unmaskLogsTable', 'unmaskLogsPagination', 5);
});
</script>
@endsection
