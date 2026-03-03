@extends('dashboard')

@section('title', 'Incident Response Center - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .incident-page { padding: 20px 30px; }
        
        .incident-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .incident-title {
            color: #1e2a47;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .incident-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .incident-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn-incident {
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
        
        .btn-incident:hover {
            background: #c8a858;
            color: #fff;
            border-color: #c8a858;
        }
        
        .btn-incident.report {
            background: #1e2a47;
            border-color: #1e2a47;
            color: #fff;
        }
        
        .btn-incident.report:hover {
            background: #2a3a5c;
            border-color: #2a3a5c;
            color: #fff;
        }
        
        .btn-incident.blocklist {
            background: #c8a858;
            border-color: #c8a858;
            color: #fff;
        }
        
        .btn-incident.blocklist:hover {
            background: #a08038;
            border-color: #a08038;
            color: #fff;
        }
        
        .btn-incident.primary {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        
        .btn-incident.primary:hover {
            background: #c82333;
            border-color: #c82333;
            color: #fff;
        }
        
        /* Threat Level Banner */
        .threat-banner {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 14px 20px;
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
        
        .threat-level-label {
            color: #28a745;
            font-weight: 700;
        }
        
        .threat-banner.elevated .threat-level-label { color: #b8860b; }
        .threat-banner.guarded .threat-level-label { color: #d56308; }
        .threat-banner.critical .threat-level-label { color: #dc3545; }
        
        .threat-message {
            color: #28a745;
            font-weight: 500;
        }
        
        .threat-banner.elevated .threat-message { color: #b8860b; }
        .threat-banner.guarded .threat-message { color: #d56308; }
        .threat-banner.critical .threat-message { color: #dc3545; }
        
        /* Stat Cards */
        .incident-stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .incident-stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 14px 16px;
            text-align: left;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .stat-color-box {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            flex-shrink: 0;
        }
        
        .stat-color-box.critical { background: #dc3545; }
        .stat-color-box.high { background: #fd7e14; }
        .stat-color-box.investigating { background: #ffc107; }
        .stat-color-box.contained { background: #17a2b8; }
        .stat-color-box.resolved { background: #28a745; }
        .stat-color-box.total { background: #6c757d; }
        
        .stat-info h3 {
            color: #1e2a47;
            font-size: 1.5rem;
            margin: 0;
            line-height: 1;
        }
        
        .stat-info p {
            color: #6c757d;
            margin: 4px 0 0 0;
            font-size: 0.7rem;
        }
        
        /* Filters Section */
        .incident-filters {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 12px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .filter-group {
            flex: 1;
            min-width: 130px;
        }
        
        .filter-group label {
            display: block;
            color: #6c757d;
            font-size: 0.65rem;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .filter-group select {
            width: 100%;
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        
        .filter-group select:focus {
            border-color: #c8a858;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.2);
            outline: none;
        }
        
        .btn-filter {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 0.85rem;
        }
        
        .btn-filter:hover {
            background: #c82333;
        }
        
        /* Main Content Row */
        .incident-content-row {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 20px;
        }
        
        /* Incidents Table */
        .incidents-table-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .incidents-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .incidents-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .incidents-table th {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .incidents-table td {
            padding: 12px 14px;
            color: #495057;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.85rem;
        }
        
        .incidents-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .severity-badge, .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .severity-critical { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .severity-high { background: rgba(253, 126, 20, 0.1); color: #d56308; }
        .severity-medium { background: rgba(255, 193, 7, 0.15); color: #b8860b; }
        .severity-low { background: rgba(40, 167, 69, 0.1); color: #228b22; }
        
        .status-open { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .status-investigating { background: rgba(255, 193, 7, 0.15); color: #b8860b; }
        .status-contained { background: rgba(23, 162, 184, 0.1); color: #117a8b; }
        .status-resolved { background: rgba(40, 167, 69, 0.1); color: #228b22; }
        
        .action-btn {
            background: transparent;
            border: 1px solid #c8a858;
            color: #a08038;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            background: #c8a858;
            color: #fff;
        }
        
        /* Locked Accounts Sidebar */
        .locked-accounts-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 16px;
            border: 1px solid rgba(220, 53, 69, 0.2);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .locked-accounts-card h4 {
            color: #dc3545;
            margin: 0 0 12px 0;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .locked-account-item {
            background: rgba(220, 53, 69, 0.08);
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            color: #495057;
            font-size: 0.85rem;
        }
        
        .no-locked {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 15px;
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

        /* Modal */
        .incident-modal {
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

        .incident-modal.active {
            display: flex;
        }

        .incident-modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            max-width: 480px;
            width: 90%;
            border: 1px solid #dee2e6;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .incident-modal-content h3 {
            color: #1e2a47;
            margin-bottom: 18px;
            font-size: 1.1rem;
        }

        .modal-form-group {
            margin-bottom: 16px;
        }

        .modal-form-group label {
            display: block;
            color: #6c757d;
            font-size: 0.8rem;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .modal-form-group select,
        .modal-form-group input,
        .modal-form-group textarea {
            width: 100%;
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .modal-form-group select:focus,
        .modal-form-group input:focus,
        .modal-form-group textarea:focus {
            border-color: #c8a858;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.2);
            outline: none;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-modal-cancel {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #6c757d;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-modal-cancel:hover {
            background: #e9ecef;
        }

        .btn-modal-submit {
            background: #dc3545;
            border: none;
            color: #fff;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        /* Resolution Notes Styles */
        .optional-text {
            font-weight: 400;
            color: #adb5bd;
            font-size: 0.75rem;
        }

        .resolution-hint {
            display: block;
            margin-top: 6px;
            font-size: 0.75rem;
            color: #17a2b8;
            padding: 6px 10px;
            background: rgba(23, 162, 184, 0.1);
            border-radius: 6px;
        }

        .resolution-hint.hidden {
            display: none;
        }

        .resolution-hint i {
            margin-right: 4px;
        }

        .resolution-error {
            display: block;
            margin-top: 8px;
            padding: 8px 12px;
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 6px;
            color: #dc3545;
            font-size: 0.8rem;
        }

        .resolution-error.hidden {
            display: none;
        }

        .resolution-error i {
            margin-right: 4px;
        }

        @media (max-width: 1200px) {
            .incident-stats { grid-template-columns: repeat(3, 1fr); }
            .incident-content-row { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .incident-stats { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
@endsection

@section('content')
<div class="incident-page">
    <!-- Header -->
    <div class="incident-header">
        <div>
            <h1 class="incident-title">INCIDENT RESPONSE CENTER</h1>
            <p class="incident-subtitle">Monitor, investigate, and respond to security incidents</p>
        </div>
        <div class="incident-actions">
            <a href="{{ route('incidents.report') }}" class="btn-incident report">Generate Report</a>
            <a href="{{ route('incidents.blocklist') }}" class="btn-incident blocklist">IP Blocklist</a>
        </div>
    </div>

    <!-- Threat Level Banner -->
    <div class="threat-banner {{ strtolower($threatLevel) }}">
        <span>
            <span style="color: #1e2a47; font-weight: 600;">Current Threat Level: </span>
            <span class="threat-level-label">{{ $threatLevel }}</span>
        </span>
        <span class="threat-message">{{ $threatMessage }}</span>
    </div>

    <!-- Stats Cards -->
    <div class="incident-stats">
        <div class="incident-stat-card">
            <div class="stat-color-box critical"></div>
            <div class="stat-info">
                <h3>{{ $criticalOpen }}</h3>
                <p>Critical Open</p>
            </div>
        </div>
        <div class="incident-stat-card">
            <div class="stat-color-box high"></div>
            <div class="stat-info">
                <h3>{{ $highOpen }}</h3>
                <p>High Severity Open</p>
            </div>
        </div>
        <div class="incident-stat-card">
            <div class="stat-color-box investigating"></div>
            <div class="stat-info">
                <h3>{{ $investigating }}</h3>
                <p>Investigating</p>
            </div>
        </div>
        <div class="incident-stat-card">
            <div class="stat-color-box contained"></div>
            <div class="stat-info">
                <h3>{{ $contained }}</h3>
                <p>Contained</p>
            </div>
        </div>
        <div class="incident-stat-card">
            <div class="stat-color-box resolved"></div>
            <div class="stat-info">
                <h3>{{ $resolved }}</h3>
                <p>Resolved</p>
            </div>
        </div>
        <div class="incident-stat-card">
            <div class="stat-color-box total"></div>
            <div class="stat-info">
                <h3>{{ $totalIncidents }}</h3>
                <p>Total Incidents</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form action="{{ route('incidents.index') }}" method="GET" class="incident-filters">
        <div class="filter-group">
            <label>STATUS</label>
            <select name="status">
                <option value="">All Status</option>
                <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="investigating" {{ $status == 'investigating' ? 'selected' : '' }}>Investigating</option>
                <option value="contained" {{ $status == 'contained' ? 'selected' : '' }}>Contained</option>
                <option value="resolved" {{ $status == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </div>
        <div class="filter-group">
            <label>SEVERITY</label>
            <select name="severity">
                <option value="">All Severity</option>
                <option value="critical" {{ $severity == 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="high" {{ $severity == 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ $severity == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ $severity == 'low' ? 'selected' : '' }}>Low</option>
            </select>
        </div>
        <div class="filter-group">
            <label>TYPE</label>
            <select name="type">
                <option value="">All Types</option>
                <option value="unauthorized_access" {{ $type == 'unauthorized_access' ? 'selected' : '' }}>Unauthorized Access</option>
                <option value="brute_force" {{ $type == 'brute_force' ? 'selected' : '' }}>Brute Force</option>
                <option value="suspicious_activity" {{ $type == 'suspicious_activity' ? 'selected' : '' }}>Suspicious Activity</option>
                <option value="data_breach" {{ $type == 'data_breach' ? 'selected' : '' }}>Data Breach</option>
                <option value="system_error" {{ $type == 'system_error' ? 'selected' : '' }}>System Error</option>
                <option value="policy_violation" {{ $type == 'policy_violation' ? 'selected' : '' }}>Policy Violation</option>
            </select>
        </div>
        <div class="filter-group">
            <label>TIME RANGE</label>
            <select name="time_range">
                <option value="7" {{ $timeRange == '7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $timeRange == '30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $timeRange == '90' ? 'selected' : '' }}>Last 90 days</option>
                <option value="all" {{ $timeRange == 'all' ? 'selected' : '' }}>All Time</option>
            </select>
        </div>
        <button type="submit" class="btn-filter">Filter</button>
    </form>

    <!-- Content Row -->
    <div class="incident-content-row">
        <!-- Incidents Table -->
        <div class="incidents-table-card">
            <table class="incidents-table">
                <thead>
                    <tr>
                        <th>SEVERITY</th>
                        <th>TYPE</th>
                        <th>DESCRIPTION</th>
                        <th>STATUS</th>
                        <th>DETECTED</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $incident)
                        <tr>
                            <td><span class="severity-badge severity-{{ $incident->severity }}">{{ ucfirst($incident->severity) }}</span></td>
                            <td>{{ $incident->type_display }}</td>
                            <td>{{ Str::limit($incident->description, 50) }}</td>
                            <td><span class="status-badge status-{{ $incident->status }}">{{ ucfirst($incident->status) }}</span></td>
                            <td>{{ $incident->detected_at->format('M d, H:i') }}</td>
                            <td>
                                <button class="action-btn" onclick="openStatusModal({{ $incident->id }}, '{{ $incident->status }}')">
                                    Update
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-shield-check"></i>
                                    <p>No incidents found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @include('partials.pagination', ['paginator' => $incidents])
        </div>

        <!-- Locked Accounts Sidebar -->
        <div class="locked-accounts-card">
            <h4><i class="bi bi-lock-fill me-2"></i>Locked Accounts</h4>
            @forelse($lockedAccounts as $account)
                <div class="locked-account-item">
                    <i class="bi bi-person-x me-2"></i>{{ $account }}
                </div>
            @empty
                <p class="no-locked">No locked accounts</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="incident-modal" id="statusModal">
    <div class="incident-modal-content">
        <h3><i class="bi bi-pencil-square me-2"></i>Update Incident Status</h3>
        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-form-group">
                <label>New Status</label>
                <select name="status" id="modalStatus" onchange="toggleResolutionRequired()">
                    <option value="open">Open</option>
                    <option value="investigating">Investigating</option>
                    <option value="contained">Contained</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
            <div class="modal-form-group" id="resolutionNotesGroup">
                <label id="resolutionLabel">Resolution Notes <span class="optional-text">(optional)</span></label>
                <textarea name="resolution_notes" id="resolutionNotes" rows="3" placeholder="Add notes about this status change..."></textarea>
                <small class="resolution-hint hidden" id="resolutionHint">
                    <i class="bi bi-info-circle"></i> Required when marking as Resolved (min. 10 characters)
                </small>
                <div class="resolution-error hidden" id="resolutionError">
                    <i class="bi bi-exclamation-circle"></i> <span id="resolutionErrorText"></span>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeStatusModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit" id="submitStatusBtn">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStatusModal(id, currentStatus) {
        document.getElementById('statusForm').action = '/incidents/' + id + '/status';
        document.getElementById('modalStatus').value = currentStatus;
        document.getElementById('resolutionNotes').value = '';
        document.getElementById('resolutionError').classList.add('hidden');
        toggleResolutionRequired();
        document.getElementById('statusModal').classList.add('active');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.remove('active');
        document.getElementById('resolutionError').classList.add('hidden');
    }

    function toggleResolutionRequired() {
        const status = document.getElementById('modalStatus').value;
        const label = document.getElementById('resolutionLabel');
        const hint = document.getElementById('resolutionHint');
        const textarea = document.getElementById('resolutionNotes');
        const optionalText = label.querySelector('.optional-text');
        
        if (status === 'resolved') {
            if (optionalText) optionalText.textContent = '(required)';
            hint.classList.remove('hidden');
            textarea.setAttribute('required', 'required');
            textarea.placeholder = 'Describe how this incident was resolved (min. 10 characters)...';
        } else {
            if (optionalText) optionalText.textContent = '(optional)';
            hint.classList.add('hidden');
            textarea.removeAttribute('required');
            textarea.placeholder = 'Add notes about this status change...';
        }
    }

    document.getElementById('statusForm').addEventListener('submit', function(e) {
        const status = document.getElementById('modalStatus').value;
        const notes = document.getElementById('resolutionNotes').value.trim();
        const errorDiv = document.getElementById('resolutionError');
        const errorText = document.getElementById('resolutionErrorText');
        
        if (status === 'resolved') {
            if (!notes) {
                e.preventDefault();
                errorText.textContent = 'Resolution notes are required when marking as Resolved.';
                errorDiv.classList.remove('hidden');
                document.getElementById('resolutionNotes').focus();
                return false;
            }
            if (notes.length < 10) {
                e.preventDefault();
                errorText.textContent = 'Resolution notes must be at least 10 characters.';
                errorDiv.classList.remove('hidden');
                document.getElementById('resolutionNotes').focus();
                return false;
            }
        }
        errorDiv.classList.add('hidden');
    });

    document.getElementById('statusModal').addEventListener('click', function(e) {
        if (e.target === this) closeStatusModal();
    });
</script>
@endsection
