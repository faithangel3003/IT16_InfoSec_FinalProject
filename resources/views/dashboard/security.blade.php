@extends('dashboard')

@section('title', 'Security Dashboard - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sec-container { max-width: 1400px; margin: 0 auto; }
        
        .sec-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .sec-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sec-title i { color: #c8a858; }
        
        .sec-date-badge {
            background: linear-gradient(135deg, #1e2a47 0%, #2d3a5c 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Threat Level Banner */
        .threat-banner {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #28a745;
        }
        
        .threat-banner.elevated { border-left-color: #ffc107; }
        .threat-banner.guarded { border-left-color: #fd7e14; }
        .threat-banner.critical { border-left-color: #dc3545; }
        
        .threat-level-label { color: #28a745; font-weight: 700; font-size: 1.1rem; }
        .threat-banner.elevated .threat-level-label { color: #b8860b; }
        .threat-banner.guarded .threat-level-label { color: #d56308; }
        .threat-banner.critical .threat-level-label { color: #dc3545; }
        
        .threat-message { color: #6c757d; font-size: 0.9rem; }

        /* Stats Grid */
        .sec-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .sec-stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sec-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .sec-stat-card.danger::before { background: linear-gradient(180deg, #dc3545 0%, #e85c68 100%); }
        .sec-stat-card.warning::before { background: linear-gradient(180deg, #fd7e14 0%, #fdbe14 100%); }
        .sec-stat-card.info::before { background: linear-gradient(180deg, #17a2b8 0%, #3ab5c6 100%); }
        .sec-stat-card.success::before { background: linear-gradient(180deg, #28a745 0%, #34ce57 100%); }
        .sec-stat-card.navy::before { background: linear-gradient(180deg, #1e2a47 0%, #2d3a5c 100%); }
        
        .sec-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }
        
        .sec-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 12px;
        }
        
        .sec-stat-icon.danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .sec-stat-icon.warning { background: rgba(253, 126, 20, 0.15); color: #fd7e14; }
        .sec-stat-icon.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        .sec-stat-icon.success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .sec-stat-icon.navy { background: rgba(30, 42, 71, 0.1); color: #1e2a47; }
        
        .sec-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e2a47;
            line-height: 1.2;
        }
        
        .sec-stat-value.danger { color: #dc3545; }
        .sec-stat-value.success { color: #28a745; }
        
        .sec-stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* Content Grid */
        .sec-content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 992px) {
            .sec-content-grid { grid-template-columns: 1fr; }
        }
        
        .sec-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .sec-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .sec-card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .sec-card-title i { color: #c8a858; font-size: 1.1rem; }
        
        .sec-card-link {
            color: #c8a858;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .sec-card-link:hover { color: #1e2a47; }

        /* Incidents List */
        .incident-list { list-style: none; padding: 0; margin: 0; }
        
        .incident-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .incident-item:last-child { border-bottom: none; }
        
        .incident-severity {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }
        
        .incident-severity.critical { background: #dc3545; }
        .incident-severity.high { background: #fd7e14; }
        .incident-severity.medium { background: #ffc107; }
        .incident-severity.low { background: #28a745; }
        
        .incident-content { flex-grow: 1; }
        
        .incident-desc {
            font-size: 0.85rem;
            color: #1e2a47;
            margin: 0 0 4px 0;
        }
        
        .incident-meta {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .incident-status {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .incident-status.open { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .incident-status.investigating { background: rgba(255, 193, 7, 0.2); color: #b8860b; }
        .incident-status.contained { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        .incident-status.resolved { background: rgba(40, 167, 69, 0.15); color: #28a745; }

        /* Log List */
        .log-list { list-style: none; padding: 0; margin: 0; }
        
        .log-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .log-item:last-child { border-bottom: none; }
        
        .log-action {
            font-size: 0.85rem;
            color: #1e2a47;
            margin-bottom: 4px;
        }
        
        .log-time {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Locked Accounts */
        .locked-accounts {
            background: rgba(220, 53, 69, 0.05);
            border-radius: 8px;
            padding: 12px;
        }
        
        .locked-account {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(220, 53, 69, 0.1);
        }
        
        .locked-account:last-child { border-bottom: none; }
        
        .locked-account i { color: #dc3545; }
        .locked-account span { font-size: 0.85rem; color: #dc3545; font-weight: 500; }

        /* Charts */
        .chart-container { height: 200px; position: relative; }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .quick-action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        
        .quick-action-btn.primary {
            background: linear-gradient(135deg, #1e2a47 0%, #2d3a5c 100%);
            color: white;
        }
        
        .quick-action-btn.danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection

@section('content')
<div class="sec-container">
    <div class="sec-header">
        <h1 class="sec-title"><i class="bi bi-shield-lock-fill"></i> Security Dashboard</h1>
        <div class="sec-date-badge">{{ now()->format('F d, Y') }}</div>
    </div>

    <!-- Threat Level Banner -->
    <div class="threat-banner {{ $threatClass }}">
        <div>
            <span class="threat-level-label">THREAT LEVEL: {{ $threatLevel }}</span>
            <p class="threat-message" style="margin: 4px 0 0 0;">{{ $threatMessage }}</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('incidents.index') }}" class="quick-action-btn primary">
                <i class="bi bi-exclamation-triangle"></i> View Incidents
            </a>
            <a href="{{ route('system-logs.index') }}" class="quick-action-btn primary">
                <i class="bi bi-journal-text"></i> System Logs
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="sec-stats-grid">
        <div class="sec-stat-card danger">
            <div class="sec-stat-icon danger"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div class="sec-stat-value danger">{{ $criticalOpen }}</div>
            <div class="sec-stat-label">Critical Open</div>
        </div>
        <div class="sec-stat-card warning">
            <div class="sec-stat-icon warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="sec-stat-value">{{ $highOpen }}</div>
            <div class="sec-stat-label">High Open</div>
        </div>
        <div class="sec-stat-card info">
            <div class="sec-stat-icon info"><i class="bi bi-search"></i></div>
            <div class="sec-stat-value">{{ $investigating }}</div>
            <div class="sec-stat-label">Investigating</div>
        </div>
        <div class="sec-stat-card success">
            <div class="sec-stat-icon success"><i class="bi bi-check-circle-fill"></i></div>
            <div class="sec-stat-value success">{{ $resolved }}</div>
            <div class="sec-stat-label">Resolved</div>
        </div>
        <div class="sec-stat-card navy">
            <div class="sec-stat-icon navy"><i class="bi bi-door-open"></i></div>
            <div class="sec-stat-value">{{ $todayLogins }}</div>
            <div class="sec-stat-label">Logins Today</div>
        </div>
        <div class="sec-stat-card danger">
            <div class="sec-stat-icon danger"><i class="bi bi-x-circle-fill"></i></div>
            <div class="sec-stat-value danger">{{ $failedLogins }}</div>
            <div class="sec-stat-label">Failed (24h)</div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="sec-content-grid">
        <!-- Recent Incidents -->
        <div class="sec-card">
            <div class="sec-card-header">
                <h3 class="sec-card-title"><i class="bi bi-exclamation-triangle"></i> Recent Incidents</h3>
                <a href="{{ route('incidents.index') }}" class="sec-card-link">View All →</a>
            </div>
            @if($recentIncidents->count() > 0)
            <ul class="incident-list">
                @foreach($recentIncidents as $incident)
                <li class="incident-item">
                    <span class="incident-severity {{ $incident->severity }}"></span>
                    <div class="incident-content">
                        <p class="incident-desc">{{ Str::limit($incident->description, 80) }}</p>
                        <span class="incident-meta">
                            {{ $incident->type_display }} • {{ $incident->detected_at->diffForHumans() }}
                            @if($incident->reported_by_name)
                                • Reported by {{ $incident->reported_by_name }}
                            @endif
                        </span>
                    </div>
                    <span class="incident-status {{ $incident->status }}">{{ $incident->status }}</span>
                </li>
                @endforeach
            </ul>
            @else
            <p style="text-align: center; color: #6c757d; padding: 30px;">No incidents recorded</p>
            @endif
        </div>

        <!-- Locked Accounts & Stats -->
        <div>
            <!-- Locked Accounts -->
            <div class="sec-card" style="margin-bottom: 20px;">
                <div class="sec-card-header">
                    <h3 class="sec-card-title"><i class="bi bi-lock-fill"></i> Locked Accounts</h3>
                </div>
                @if(count($lockedAccounts) > 0)
                <div class="locked-accounts">
                    @foreach($lockedAccounts as $account)
                    <div class="locked-account">
                        <i class="bi bi-person-fill-lock"></i>
                        <span>{{ $account }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="text-align: center; color: #28a745; padding: 20px; font-size: 0.9rem;">
                    <i class="bi bi-check-circle-fill"></i> No locked accounts
                </p>
                @endif
            </div>

            <!-- Blocked IPs -->
            <div class="sec-card">
                <div class="sec-card-header">
                    <h3 class="sec-card-title"><i class="bi bi-shield-x"></i> Blocked IPs</h3>
                    <a href="{{ route('incidents.blocklist') }}" class="sec-card-link">Manage →</a>
                </div>
                <div style="text-align: center; padding: 15px;">
                    <div style="font-size: 2rem; font-weight: 700; color: {{ $blockedIps > 0 ? '#dc3545' : '#28a745' }};">{{ $blockedIps }}</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Currently Blocked</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Logs Row -->
    <div class="sec-content-grid">
        <!-- Incident Trend Chart -->
        <div class="sec-card">
            <div class="sec-card-header">
                <h3 class="sec-card-title"><i class="bi bi-graph-up"></i> Incident Trend (7 Days)</h3>
            </div>
            <div class="chart-container">
                <canvas id="incidentTrendChart"></canvas>
            </div>
        </div>

        <!-- Recent System Logs -->
        <div class="sec-card">
            <div class="sec-card-header">
                <h3 class="sec-card-title"><i class="bi bi-journal-text"></i> Recent Logs</h3>
                <a href="{{ route('system-logs.index') }}" class="sec-card-link">View All →</a>
            </div>
            @if($recentLogs->count() > 0)
            <ul class="log-list">
                @foreach($recentLogs as $log)
                <li class="log-item">
                    <div class="log-action">{{ Str::limit($log->description, 50) }}</div>
                    <div class="log-time">{{ $log->created_at->diffForHumans() }}</div>
                </li>
                @endforeach
            </ul>
            @else
            <p style="text-align: center; color: #6c757d; padding: 30px;">No recent logs</p>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Incident Trend Chart
    const trendData = @json($dailyIncidents);
    const labels = trendData.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    const values = trendData.map(d => d.count);

    new Chart(document.getElementById('incidentTrendChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Incidents',
                data: values,
                borderColor: '#c8a858',
                backgroundColor: 'rgba(200, 168, 88, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#c8a858',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
@endsection
