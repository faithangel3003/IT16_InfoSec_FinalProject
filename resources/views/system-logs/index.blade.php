@extends('dashboard')

@section('title', 'System Logs - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .logs-page { padding: 20px 30px; }
        
        .logs-title {
            color: #1e2a47;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        
        /* Stats Cards - Compact like screenshot */
        .logs-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .log-stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 18px 20px;
            text-align: center;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .log-stat-card h3 {
            font-size: 1.8rem;
            margin: 0;
            color: #1e2a47;
            font-weight: 700;
        }
        
        .log-stat-card p {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .log-stat-card.security {
            border-left: 4px solid #dc3545;
        }
        
        .log-stat-card.security h3 {
            color: #dc3545;
        }
        
        .log-stat-card.errors {
            border-left: 4px solid #ffc107;
        }
        
        .log-stat-card.errors h3 {
            color: #c8a000;
        }
        
        .log-stat-card.audit {
            border-left: 4px solid #17a2b8;
        }
        
        /* Filters - Light Theme */
        .logs-filters {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 15px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 110px;
        }
        
        .filter-group.search {
            flex: 2;
            min-width: 180px;
        }
        
        .filter-group label {
            display: block;
            color: #6c757d;
            font-size: 0.65rem;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #c8a858;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.2);
            outline: none;
        }
        
        .filter-group input::placeholder {
            color: #adb5bd;
        }
        
        .filter-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-filter {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-filter:hover {
            background: #c82333;
        }
        
        .btn-clear {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-clear:hover {
            background: #c8a858;
            color: #fff;
            border-color: #c8a858;
        }
        
        /* Export Button */
        .btn-export {
            background: #1e2a47;
            color: #fff;
            border: 1px solid #1e2a47;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }
        
        .btn-export:hover {
            background: #c8a858;
            color: #fff;
            border-color: #c8a858;
        }
        
        /* Logs Table - Light Theme */
        .logs-table-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: visible;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .logs-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        
        .logs-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .logs-table th {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .logs-table td {
            padding: 10px 14px;
            color: #495057;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.85rem;
        }
        
        .logs-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .time-cell {
            color: #6c757d;
            white-space: nowrap;
            font-size: 0.8rem;
        }
        
        .channel-badge {
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .channel-audit { background: rgba(200, 168, 88, 0.15); color: #a08038; }
        .channel-security { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .channel-system { background: rgba(23, 162, 184, 0.1); color: #117a8b; }
        .channel-error { background: rgba(253, 126, 20, 0.1); color: #d56308; }
        
        .level-badge {
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .level-info { background: rgba(23, 162, 184, 0.1); color: #117a8b; }
        .level-warning { background: rgba(255, 193, 7, 0.15); color: #b8860b; }
        .level-error { background: rgba(253, 126, 20, 0.1); color: #d56308; }
        .level-critical { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        
        .action-cell {
            font-family: 'Courier New', monospace;
            color: #a08038;
            font-size: 0.8rem;
        }
        
        .message-cell {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .ip-cell {
            font-family: 'Courier New', monospace;
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        .btn-view {
            background: transparent;
            border: none;
            color: #c8a858;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color 0.3s;
            font-weight: 600;
        }
        
        .btn-view:hover {
            color: #17a2b8;
        }
        
        /* Pagination */
        .pagination-wrapper {
            padding: 12px;
            display: flex;
            justify-content: center;
        }

        .page-link {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
        }

        .page-link:hover {
            background: #c8a858;
            color: #fff;
            border-color: #c8a858;
        }

        .page-item.active .page-link {
            background: #c8a858;
            border-color: #c8a858;
            color: #fff;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
            color: #adb5bd;
        }

        /* Modal - Light Theme */
        .log-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(30, 42, 71, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .log-modal.active {
            display: flex;
        }

        .log-modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            max-width: 550px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .log-modal-content h3 {
            color: #1e2a47;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }

        .log-modal-content h3 i {
            color: #c8a858;
        }

        .log-detail-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .log-detail-label {
            width: 90px;
            color: #6c757d;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .log-detail-value {
            flex: 1;
            color: #495057;
            font-size: 0.9rem;
        }

        .log-message-full {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            color: #495057;
            font-size: 0.85rem;
            line-height: 1.6;
            border: 1px solid #e9ecef;
        }

        .btn-close-modal {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 18px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-close-modal:hover {
            background: #c8a858;
            color: #fff;
            border-color: #c8a858;
        }
        
        @media (max-width: 1200px) {
            .logs-stats { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .logs-stats { grid-template-columns: 1fr; }
            .filter-row { flex-direction: column; }
        }
    </style>
@endsection

@section('content')
<div class="logs-page">
    <h1 class="logs-title">SYSTEM LOGS</h1>

    <!-- Stats Cards -->
    <div class="logs-stats">
        <div class="log-stat-card">
            <h3>{{ $totalToday }}</h3>
            <p>Total Today</p>
        </div>
        <div class="log-stat-card security">
            <h3>{{ $securityEvents }}</h3>
            <p>Security Events</p>
        </div>
        <div class="log-stat-card errors">
            <h3>{{ $errors }}</h3>
            <p>Errors</p>
        </div>
        <div class="log-stat-card">
            <h3>{{ $auditEvents }}</h3>
            <p>Audit Events</p>
        </div>
    </div>

    <!-- Filters -->
    <form action="{{ route('system-logs.index') }}" method="GET" class="logs-filters">
        <div class="filter-row">
            <div class="filter-group">
                <label>CHANNEL</label>
                <select name="channel">
                    <option value="">All Channels</option>
                    <option value="audit" {{ $channel == 'audit' ? 'selected' : '' }}>Audit</option>
                    <option value="security" {{ $channel == 'security' ? 'selected' : '' }}>Security</option>
                    <option value="system" {{ $channel == 'system' ? 'selected' : '' }}>System</option>
                    <option value="error" {{ $channel == 'error' ? 'selected' : '' }}>Error</option>
                </select>
            </div>
            <div class="filter-group">
                <label>LEVEL</label>
                <select name="level">
                    <option value="">All Levels</option>
                    <option value="info" {{ $level == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ $level == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="error" {{ $level == 'error' ? 'selected' : '' }}>Error</option>
                    <option value="critical" {{ $level == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="filter-group">
                <label>USER</label>
                <select name="user_id">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>FROM</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="filter-group">
                <label>TO</label>
                <input type="date" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="filter-group search">
                <label>SEARCH</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search message...">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Filter</button>
                <a href="{{ route('system-logs.index') }}" class="btn-clear">Clear</a>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
            <a href="{{ route('system-logs.export', request()->query()) }}" class="btn-export">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </form>

    <!-- Logs Table -->
    <div class="logs-table-card">
        <table class="logs-table">
            <thead>
                <tr>
                    <th>TIME</th>
                    <th>CHANNEL</th>
                    <th>LEVEL</th>
                    <th>ACTION</th>
                    <th>MESSAGE</th>
                    <th>USER</th>
                    <th>IP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="time-cell">{{ $log->created_at->format('M d, H:i:s') }}</td>
                        <td><span class="channel-badge channel-{{ $log->channel }}">{{ strtoupper($log->channel) }}</span></td>
                        <td><span class="level-badge level-{{ $log->level }}">{{ strtoupper($log->level) }}</span></td>
                        <td class="action-cell">{{ $log->action }}</td>
                        <td class="message-cell" title="{{ $log->message }}">{{ Str::limit($log->message, 40) }}</td>
                        <td>{{ $log->user_name ?? 'System' }}</td>
                        <td class="ip-cell">{{ $log->ip_address ?? '-' }}</td>
                        <td>
                            <button class="btn-view" onclick="viewLog({{ $log->id }})">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-journal-text"></i>
                                <p>No logs found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @include('partials.pagination', ['paginator' => $logs])
    </div>
</div>

<!-- Log Detail Modal -->
<div class="log-modal" id="logModal">
    <div class="log-modal-content">
        <h3><i class="bi bi-journal-text"></i> Log Details</h3>
        <div id="logDetails">
            <div class="log-detail-row">
                <span class="log-detail-label">Time</span>
                <span class="log-detail-value" id="logTime">-</span>
            </div>
            <div class="log-detail-row">
                <span class="log-detail-label">Channel</span>
                <span class="log-detail-value" id="logChannel">-</span>
            </div>
            <div class="log-detail-row">
                <span class="log-detail-label">Level</span>
                <span class="log-detail-value" id="logLevel">-</span>
            </div>
            <div class="log-detail-row">
                <span class="log-detail-label">Action</span>
                <span class="log-detail-value" id="logAction">-</span>
            </div>
            <div class="log-detail-row">
                <span class="log-detail-label">User</span>
                <span class="log-detail-value" id="logUser">-</span>
            </div>
            <div class="log-detail-row">
                <span class="log-detail-label">IP Address</span>
                <span class="log-detail-value" id="logIp">-</span>
            </div>
            <div style="margin-top: 20px;">
                <label class="log-detail-label" style="display: block; margin-bottom: 10px;">Message</label>
                <div class="log-message-full" id="logMessage">-</div>
            </div>
        </div>
        <button class="btn-close-modal" onclick="closeLogModal()">Close</button>
    </div>
</div>

<script>
    function viewLog(id) {
        fetch('/system-logs/' + id)
            .then(response => response.json())
            .then(log => {
                document.getElementById('logTime').textContent = new Date(log.created_at).toLocaleString();
                document.getElementById('logChannel').innerHTML = '<span class="channel-badge channel-' + log.channel + '">' + log.channel.toUpperCase() + '</span>';
                document.getElementById('logLevel').innerHTML = '<span class="level-badge level-' + log.level + '">' + log.level.toUpperCase() + '</span>';
                document.getElementById('logAction').textContent = log.action;
                document.getElementById('logUser').textContent = log.user_name || 'System';
                document.getElementById('logIp').textContent = log.ip_address || '-';
                document.getElementById('logMessage').textContent = log.message;
                document.getElementById('logModal').classList.add('active');
            });
    }

    function closeLogModal() {
        document.getElementById('logModal').classList.remove('active');
    }

    document.getElementById('logModal').addEventListener('click', function(e) {
        if (e.target === this) closeLogModal();
    });
</script>
@endsection
