@extends('dashboard')

@section('title', 'Incident Report - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .report-page { padding: 20px 30px; }
        
        .report-title {
            color: #1e2a47;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .report-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .report-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn-report {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        
        .btn-report:hover {
            background: #c8a858;
            color: #fff;
            border-color: #c8a858;
        }
        
        .btn-report.primary {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        
        .btn-report.primary:hover {
            background: #c82333;
            color: #fff;
        }
        
        /* Date Filter */
        .date-filter-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: end;
            gap: 15px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .date-group {
            flex: 1;
            max-width: 200px;
        }
        
        .date-group label {
            display: block;
            color: #6c757d;
            font-size: 0.65rem;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .date-group input {
            width: 100%;
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 10px 14px;
            border-radius: 8px;
        }
        
        .date-group input:focus {
            border-color: #c8a858;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.2);
            outline: none;
        }
        
        .btn-generate {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.85rem;
        }
        
        .btn-generate:hover {
            background: #c82333;
        }
        
        /* Executive Summary */
        .summary-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .summary-card h3 {
            color: #1e2a47;
            font-size: 1.1rem;
            margin-bottom: 16px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .summary-stat {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        
        .summary-stat h4 {
            font-size: 2rem;
            margin: 0;
            color: #dc3545;
        }
        
        .summary-stat.success h4 { color: #228b22; }
        .summary-stat.warning h4 { color: #b8860b; }
        .summary-stat.info h4 { color: #117a8b; }
        
        .summary-stat p {
            color: #6c757d;
            margin: 8px 0 0 0;
            font-size: 0.8rem;
        }
        
        /* Severity Breakdown */
        .severity-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .severity-section h3 {
            color: #1e2a47;
            font-size: 1.1rem;
            margin-bottom: 16px;
        }
        
        .severity-bars {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .severity-bar-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .severity-label {
            width: 80px;
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        .severity-bar-wrapper {
            flex: 1;
            background: #e9ecef;
            border-radius: 6px;
            height: 26px;
            overflow: hidden;
        }
        
        .severity-bar {
            height: 100%;
            border-radius: 6px;
            display: flex;
            align-items: center;
            padding-left: 10px;
            color: #fff;
            font-weight: 600;
            font-size: 0.8rem;
            transition: width 0.5s ease;
        }
        
        .severity-bar.critical { background: #dc3545; }
        .severity-bar.high { background: #fd7e14; }
        .severity-bar.medium { background: #ffc107; color: #495057; }
        .severity-bar.low { background: #28a745; }
        
        .severity-count {
            width: 45px;
            text-align: right;
            color: #495057;
            font-weight: 600;
        }
        
        /* Type Breakdown */
        .type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        
        .type-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 16px;
            border: 1px solid #e9ecef;
        }
        
        .type-card h5 {
            color: #a08038;
            margin: 0 0 4px 0;
            font-size: 0.8rem;
        }
        
        .type-card p {
            color: #1e2a47;
            font-size: 1.3rem;
            margin: 0;
            font-weight: 700;
        }
        
        /* Trend Chart */
        .trend-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .trend-section h3 {
            color: #1e2a47;
            font-size: 1.1rem;
            margin-bottom: 16px;
        }
        
        .trend-chart-container {
            height: 220px;
        }
        
        @media (max-width: 992px) {
            .summary-stats { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 576px) {
            .summary-stats { grid-template-columns: 1fr; }
            .date-filter-card { flex-direction: column; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="report-page">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="report-title">Incident Report</h1>
            <p class="report-subtitle">Security incident summary for {{ $startDate }} to {{ $endDate }}</p>
        </div>
        <div class="report-actions">
            <a href="{{ route('incidents.index') }}" class="btn-report">Back to Incidents</a>
            <button onclick="window.print()" class="btn-report primary">Print Report</button>
        </div>
    </div>

    <!-- Date Filter -->
    <form action="{{ route('incidents.report') }}" method="GET" class="date-filter-card">
        <div class="date-group">
            <label>START DATE</label>
            <input type="date" name="start_date" value="{{ $startDate }}">
        </div>
        <div class="date-group">
            <label>END DATE</label>
            <input type="date" name="end_date" value="{{ $endDate }}">
        </div>
        <button type="submit" class="btn-generate">Generate Report</button>
    </form>

    <!-- Executive Summary -->
    <div class="summary-card">
        <h3>Executive Summary</h3>
        <div class="summary-stats">
            <div class="summary-stat">
                <h4>{{ $totalIncidents }}</h4>
                <p>Total Incidents</p>
            </div>
            <div class="summary-stat success">
                <h4>{{ $resolved }}</h4>
                <p>Resolved</p>
            </div>
            <div class="summary-stat warning">
                <h4>{{ $resolutionRate }}%</h4>
                <p>Resolution Rate</p>
            </div>
            <div class="summary-stat info">
                <h4>{{ $avgResolution ? round($avgResolution) : 'N/A' }}</h4>
                <p>Avg Resolution (min)</p>
            </div>
        </div>
    </div>

    <!-- Incidents by Severity -->
    <div class="severity-section">
        <h3>Incidents by Severity</h3>
        @php
            $maxSeverity = max(array_values($bySeverity)) ?: 1;
        @endphp
        <div class="severity-bars">
            <div class="severity-bar-item">
                <span class="severity-label">Critical</span>
                <div class="severity-bar-wrapper">
                    <div class="severity-bar critical" style="width: {{ ($bySeverity['critical'] / $maxSeverity) * 100 }}%">
                        @if($bySeverity['critical'] > 0) {{ $bySeverity['critical'] }} @endif
                    </div>
                </div>
                <span class="severity-count">{{ $bySeverity['critical'] }}</span>
            </div>
            <div class="severity-bar-item">
                <span class="severity-label">High</span>
                <div class="severity-bar-wrapper">
                    <div class="severity-bar high" style="width: {{ ($bySeverity['high'] / $maxSeverity) * 100 }}%">
                        @if($bySeverity['high'] > 0) {{ $bySeverity['high'] }} @endif
                    </div>
                </div>
                <span class="severity-count">{{ $bySeverity['high'] }}</span>
            </div>
            <div class="severity-bar-item">
                <span class="severity-label">Medium</span>
                <div class="severity-bar-wrapper">
                    <div class="severity-bar medium" style="width: {{ ($bySeverity['medium'] / $maxSeverity) * 100 }}%">
                        @if($bySeverity['medium'] > 0) {{ $bySeverity['medium'] }} @endif
                    </div>
                </div>
                <span class="severity-count">{{ $bySeverity['medium'] }}</span>
            </div>
            <div class="severity-bar-item">
                <span class="severity-label">Low</span>
                <div class="severity-bar-wrapper">
                    <div class="severity-bar low" style="width: {{ ($bySeverity['low'] / $maxSeverity) * 100 }}%">
                        @if($bySeverity['low'] > 0) {{ $bySeverity['low'] }} @endif
                    </div>
                </div>
                <span class="severity-count">{{ $bySeverity['low'] }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Incidents by Type -->
            <div class="severity-section">
                <h3>Incidents by Type</h3>
                <div class="type-grid">
                    @php
                        $typeLabels = [
                            'unauthorized_access' => 'Unauthorized Access',
                            'brute_force' => 'Brute Force',
                            'suspicious_activity' => 'Suspicious Activity',
                            'data_breach' => 'Data Breach',
                            'system_error' => 'System Error',
                            'policy_violation' => 'Policy Violation',
                            'other' => 'Other',
                        ];
                    @endphp
                    @foreach($typeLabels as $key => $label)
                        @if(isset($byType[$key]) || true)
                            <div class="type-card">
                                <h5>{{ $label }}</h5>
                                <p>{{ $byType[$key] ?? 0 }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Daily Trend -->
            <div class="trend-section">
                <h3>Daily Incident Trend</h3>
                <div class="trend-chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const trendLabels = @json(array_keys($dailyTrend));
    const trendData = @json(array_values($dailyTrend));

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Incidents',
                data: trendData,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#dc3545',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: '#888' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: '#888', stepSize: 1 }
                }
            }
        }
    });
</script>
@endsection
