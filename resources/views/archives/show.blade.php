@extends('dashboard')

@section('title', 'Archive Details')

@section('content')
<div class="archive-detail-container">
    <div class="detail-header">
        <a href="{{ route('archives.index') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back to Archives
        </a>
        <div class="header-info">
            <span class="entity-badge {{ $archive->entity_type }}">
                <i class="bi bi-{{ $archive->entity_icon }}"></i>
                {{ $archive->entity_type_display }}
            </span>
            <h1>Archive Record #{{ $archive->id }}</h1>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Meta Information Card -->
        <div class="detail-card meta-card">
            <h3><i class="bi bi-info-circle"></i> Archive Information</h3>
            <div class="meta-items">
                <div class="meta-item">
                    <span class="meta-label">Archive ID</span>
                    <span class="meta-value">{{ $archive->id }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Entity Type</span>
                    <span class="meta-value">{{ $archive->entity_type_display }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Original Entity ID</span>
                    <span class="meta-value"><code>{{ $archive->entity_id }}</code></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Archived Date</span>
                    <span class="meta-value">{{ $archive->created_at->format('F d, Y \a\t H:i:s') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Deleted By</span>
                    <span class="meta-value">
                        @if($archive->deleter)
                            <span class="user-badge">
                                <i class="bi bi-person-fill"></i>
                                {{ $archive->deleter->name }}
                                <small>({{ ucwords(str_replace('_', ' ', $archive->deleter->role)) }})</small>
                            </span>
                        @else
                            <span class="text-muted">Unknown User</span>
                        @endif
                    </span>
                </div>
                <div class="meta-item full-width">
                    <span class="meta-label">Deletion Reason</span>
                    <span class="meta-value reason-box">{{ $archive->deletion_reason ?? 'No reason provided' }}</span>
                </div>
            </div>
        </div>

        <!-- Entity Data Card -->
        <div class="detail-card data-card">
            <h3><i class="bi bi-database"></i> Archived Entity Data</h3>
            <div class="data-content">
                @if($archive->entity_data)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($archive->entity_data as $key => $value)
                            <tr>
                                <td class="field-name">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td class="field-value">
                                    @if(is_array($value))
                                        <pre class="json-value">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                    @elseif(is_bool($value))
                                        <span class="bool-badge {{ $value ? 'true' : 'false' }}">
                                            {{ $value ? 'Yes' : 'No' }}
                                        </span>
                                    @elseif(is_null($value))
                                        <span class="null-value">NULL</span>
                                    @elseif(strtotime($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value))
                                        <span class="date-value">
                                            <i class="bi bi-calendar3"></i>
                                            {{ \Carbon\Carbon::parse($value)->format('M d, Y H:i') }}
                                        </span>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>No entity data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.archive-detail-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.detail-header {
    margin-bottom: 25px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #f8fafc;
    color: #475569;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    margin-bottom: 15px;
}

.back-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-info h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #1e293b;
}

.entity-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
}

.entity-badge.item { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.entity-badge.employee { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.entity-badge.user { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
.entity-badge.report { background: rgba(249, 115, 22, 0.1); color: #f97316; }
.entity-badge.incident { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.detail-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 20px;
}

.detail-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.detail-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.meta-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.meta-item.full-width {
    border-top: 1px solid #f1f5f9;
    padding-top: 15px;
}

.meta-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
}

.meta-value {
    font-size: 14px;
    color: #1e293b;
}

.meta-value code {
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 13px;
}

.user-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    padding: 6px 10px;
    border-radius: 6px;
}

.user-badge small {
    color: #64748b;
    font-size: 11px;
}

.reason-box {
    background: #fffbeb;
    padding: 12px;
    border-radius: 8px;
    border-left: 3px solid #f59e0b;
    font-size: 13px;
    line-height: 1.5;
    display: block;
}

.data-card {
    max-height: 600px;
    overflow: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f8fafc;
    padding: 10px 15px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
    position: sticky;
    top: 0;
}

.data-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    vertical-align: top;
}

.field-name {
    color: #64748b;
    font-weight: 500;
    width: 180px;
}

.field-value {
    color: #1e293b;
    word-break: break-word;
}

.json-value {
    background: #f8fafc;
    padding: 10px;
    border-radius: 6px;
    font-size: 12px;
    margin: 0;
    white-space: pre-wrap;
    max-width: 400px;
    overflow-x: auto;
}

.bool-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.bool-badge.true { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.bool-badge.false { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.null-value {
    color: #94a3b8;
    font-style: italic;
}

.date-value {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #0ea5e9;
}

.no-data {
    text-align: center;
    padding: 40px;
    color: #94a3b8;
}

.no-data i {
    font-size: 40px;
    margin-bottom: 10px;
    display: block;
}

@media (max-width: 900px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
