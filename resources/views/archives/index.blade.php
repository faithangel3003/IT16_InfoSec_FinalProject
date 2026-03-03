@extends('dashboard')

@section('title', 'Archive Management')

@section('content')
<div class="archives-container">
    <!-- KPI Stats Section -->
    <div class="archive-stats-grid">
        <div class="archive-stat-card total">
            <div class="stat-icon"><i class="bi bi-archive-fill"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($totalArchived) }}</span>
                <span class="stat-label">Total Archived</span>
            </div>
        </div>
        <div class="archive-stat-card today">
            <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($archivedToday) }}</span>
                <span class="stat-label">Archived Today</span>
            </div>
        </div>
        <div class="archive-stat-card week">
            <div class="stat-icon"><i class="bi bi-calendar-week"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($archivedThisWeek) }}</span>
                <span class="stat-label">This Week</span>
            </div>
        </div>
        <div class="archive-stat-card month">
            <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($archivedThisMonth) }}</span>
                <span class="stat-label">This Month</span>
            </div>
        </div>
    </div>

    <!-- Archives by Type Section -->
    <div class="archive-by-type-section">
        <h3><i class="bi bi-diagram-3"></i> Archives by Type</h3>
        <div class="type-cards">
            @forelse($byEntityType as $item)
            <div class="type-card">
                <i class="bi bi-{{ $item->entity_type === 'item' ? 'box-seam' : ($item->entity_type === 'employee' ? 'person-badge' : ($item->entity_type === 'user' ? 'person-circle' : ($item->entity_type === 'report' ? 'file-earmark-text' : ($item->entity_type === 'incident' ? 'exclamation-triangle' : 'folder')))) }}"></i>
                <span class="type-count">{{ number_format($item->count) }}</span>
                <span class="type-name">{{ ucfirst($item->entity_type) }}s</span>
            </div>
            @empty
            <p class="text-muted">No archived data yet</p>
            @endforelse
        </div>
    </div>

    <!-- Archives by Role Section -->
    <div class="archive-by-role-section">
        <h3><i class="bi bi-people"></i> Archives by User Role</h3>
        <div class="role-bars">
            @php $maxCount = $byRole->max('count') ?: 1; @endphp
            @forelse($byRole as $item)
            <div class="role-bar-item">
                <span class="role-name">{{ ucwords(str_replace('_', ' ', $item->deleted_by_role ?? 'Unknown')) }}</span>
                <div class="role-bar-track">
                    <div class="role-bar-fill" style="width: {{ ($item->count / $maxCount) * 100 }}%"></div>
                </div>
                <span class="role-count">{{ number_format($item->count) }}</span>
            </div>
            @empty
            <p class="text-muted">No data available</p>
            @endforelse
        </div>
    </div>

    <!-- Daily Trend Chart -->
    <div class="archive-trend-section">
        <h3><i class="bi bi-graph-up"></i> Daily Archive Trend (Last 7 Days)</h3>
        <canvas id="archiveTrendChart" height="100"></canvas>
    </div>

    <!-- Main Table Section -->
    <div class="archives-table-section">
        <div class="table-header">
            <h3><i class="bi bi-list-ul"></i> Archive Records</h3>
            <div class="table-filters">
                <form action="{{ route('archives.index') }}" method="GET" class="filter-form">
                    <select name="type" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="item" {{ request('type') == 'item' ? 'selected' : '' }}>Items</option>
                        <option value="employee" {{ request('type') == 'employee' ? 'selected' : '' }}>Employees</option>
                        <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>Users</option>
                        <option value="report" {{ request('type') == 'report' ? 'selected' : '' }}>Reports</option>
                        <option value="incident" {{ request('type') == 'incident' ? 'selected' : '' }}>Incidents</option>
                    </select>
                    <input type="text" name="search" class="filter-input" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit" class="filter-btn"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <table class="archives-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Entity ID</th>
                    <th>Reason</th>
                    <th>Deleted By</th>
                    <th>Date Archived</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($archives as $archive)
                <tr>
                    <td>{{ $archive->id }}</td>
                    <td>
                        <span class="entity-badge {{ $archive->entity_type }}">
                            <i class="bi bi-{{ $archive->entity_icon }}"></i>
                            {{ $archive->entity_type_display }}
                        </span>
                    </td>
                    <td><code>{{ $archive->entity_id }}</code></td>
                    <td>{{ Str::limit($archive->deletion_reason ?? 'No reason provided', 40) }}</td>
                    <td>{{ $archive->deleter->name ?? 'Unknown' }}</td>
                    <td>{{ $archive->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        <a href="{{ route('archives.show', $archive->id) }}" class="action-btn view" title="View Details">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-archive"></i>
                        <p>No archived records found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-pagination">
            {{ $archives->links() }}
        </div>
    </div>
</div>

<style>
.archives-container {
    padding: 20px;
}

.archive-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.archive-stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.2s;
}

.archive-stat-card:hover {
    transform: translateY(-3px);
}

.archive-stat-card .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.archive-stat-card.total .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.archive-stat-card.today .stat-icon { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.archive-stat-card.week .stat-icon { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
.archive-stat-card.month .stat-icon { background: rgba(249, 115, 22, 0.1); color: #f97316; }

.archive-stat-card .stat-info {
    display: flex;
    flex-direction: column;
}

.archive-stat-card .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
}

.archive-stat-card .stat-label {
    font-size: 12px;
    color: #64748b;
}

.archive-by-type-section,
.archive-by-role-section,
.archive-trend-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.archive-by-type-section h3,
.archive-by-role-section h3,
.archive-trend-section h3,
.archives-table-section .table-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 20px 0;
}

.type-cards {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.type-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 15px 25px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    min-width: 100px;
}

.type-card i {
    font-size: 24px;
    color: #3b82f6;
}

.type-card .type-count {
    font-size: 22px;
    font-weight: 700;
    color: #1e293b;
}

.type-card .type-name {
    font-size: 12px;
    color: #64748b;
}

.role-bars {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.role-bar-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.role-bar-item .role-name {
    width: 150px;
    font-size: 13px;
    color: #475569;
    flex-shrink: 0;
}

.role-bar-item .role-bar-track {
    flex: 1;
    height: 24px;
    background: #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
}

.role-bar-item .role-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #c8a858, #e0c078);
    border-radius: 6px;
    transition: width 0.4s ease;
}

.role-bar-item .role-count {
    width: 60px;
    text-align: right;
    font-weight: 600;
    color: #1e293b;
}

.archives-table-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 10px;
}

.filter-select, .filter-input {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
}

.filter-btn {
    padding: 8px 15px;
    background: #203354;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.archives-table {
    width: 100%;
    border-collapse: collapse;
}

.archives-table th {
    background: #f8fafc;
    padding: 12px 15px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
}

.archives-table td {
    padding: 15px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #475569;
}

.archives-table tbody tr:hover {
    background: #fafbfc;
}

.entity-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}

.entity-badge.item { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.entity-badge.employee { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.entity-badge.user { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
.entity-badge.report { background: rgba(249, 115, 22, 0.1); color: #f97316; }
.entity-badge.incident { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.action-btn {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.action-btn.view {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.action-btn.view:hover {
    background: #3b82f6;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 60px 20px !important;
    color: #94a3b8;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
}

.table-pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

@media (max-width: 1200px) {
    .archive-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .archive-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .table-header {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('archiveTrendChart').getContext('2d');
    const dailyData = @json($dailyTrend);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Archives',
                data: dailyData.map(d => d.count),
                borderColor: '#c8a858',
                backgroundColor: 'rgba(200, 168, 88, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#c8a858'
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
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endsection
