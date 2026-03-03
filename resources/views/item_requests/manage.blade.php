@extends('dashboard')

@section('title', 'Manage Item Requests - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .request-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 992px) {
            .request-stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 576px) {
            .request-stats-grid { grid-template-columns: 1fr; }
        }
        
        .request-stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #eee;
            text-align: center;
            transition: transform 0.2s;
        }
        
        .request-stat-card:hover {
            transform: translateY(-2px);
        }
        
        .request-stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 15px;
        }
        
        .request-stat-card .stat-icon.warning { background: rgba(255, 193, 7, 0.15); color: #d39e00; }
        .request-stat-card .stat-icon.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        .request-stat-card .stat-icon.success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .request-stat-card .stat-icon.danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        
        .request-stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e2a47;
        }
        
        .request-stat-card .stat-value.warning { color: #d39e00; }
        .request-stat-card .stat-value.info { color: #17a2b8; }
        .request-stat-card .stat-value.success { color: #28a745; }
        .request-stat-card .stat-value.danger { color: #dc3545; }
        
        .request-stat-card .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-pending {
            background: #ffc107;
            color: #000;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-approved {
            background: #17a2b8;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-fulfilled {
            background: #28a745;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-rejected {
            background: #dc3545;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .action-btn-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .btn-approve {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .btn-approve:hover {
            background: #218838;
        }
        
        .btn-reject {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .btn-reject:hover {
            background: #c82333;
        }
        
        .btn-fulfill {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .btn-fulfill:hover {
            background: #138496;
        }
        
        .request-details {
            font-size: 0.85rem;
        }
        
        .request-details .detail-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .stock-warning {
            color: #dc3545;
            font-size: 0.75rem;
            margin-top: 3px;
        }
        
        .request-row-pending {
            background: rgba(255, 193, 7, 0.05);
        }
        
        .request-notes-cell {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .requester-info {
            font-size: 0.85rem;
            color: #495057;
        }
        
        .requester-info .request-date {
            font-size: 0.75rem;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
<div class="container py-5">
    <h2>MANAGE ITEM REQUESTS</h2>

    <!-- Stats Cards -->
    <div class="request-stats-grid">
        <div class="request-stat-card">
            <div class="stat-icon warning"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value warning">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="request-stat-card">
            <div class="stat-icon info"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value info">{{ $stats['approved'] }}</div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="request-stat-card">
            <div class="stat-icon success"><i class="bi bi-box-seam"></i></div>
            <div class="stat-value success">{{ $stats['fulfilled'] }}</div>
            <div class="stat-label">Fulfilled</div>
        </div>
        <div class="request-stat-card">
            <div class="stat-icon danger"><i class="bi bi-x-circle"></i></div>
            <div class="stat-value danger">{{ $stats['rejected'] }}</div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    <div class="page-filter-bar">
        <form action="{{ route('item-requests.manage') }}" method="GET" class="filter-left">
            <select name="status" class="search-input" style="width: auto; min-width: 150px;" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="room_id" class="search-input" style="width: auto; min-width: 150px;" onchange="this.form.submit()">
                <option value="">All Rooms</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->room_id }}" {{ request('room_id') == $room->room_id ? 'selected' : '' }}>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Rejection Modal -->
    <div class="supplier-modal hidden" id="rejectModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Reject Request
            </div>
            <div class="modal-body">
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" placeholder="Please provide a reason for rejecting this request..." required></textarea>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add" style="background: #dc3545;">Reject Request</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('rejectModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="page-table-card">
        <table class="page-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Room</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Requested By</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr class="{{ $request->status === 'pending' ? 'request-row-pending' : '' }}">
                        <td>{{ $request->request_id }}</td>
                        <td>{{ $request->room->name ?? 'N/A' }}</td>
                        <td>
                            <div>{{ $request->item->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $request->item->category->name ?? '' }}</small>
                        </td>
                        <td>{{ $request->quantity }}</td>
                        <td>
                            @if($request->item)
                                <span class="{{ $request->item->in_stock < $request->quantity ? 'text-danger' : 'text-success' }}">
                                    {{ $request->item->in_stock }}
                                </span>
                                @if($request->item->in_stock < $request->quantity && $request->status === 'pending')
                                    <div class="stock-warning"><i class="bi bi-exclamation-triangle"></i> Low stock</div>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge-{{ $request->status }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="requester-info">
                                {{ $request->requester->name ?? 'N/A' }}
                                <div class="request-date">{{ $request->requested_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="request-notes-cell" title="{{ $request->notes }}">
                                {{ $request->notes ? Str::limit($request->notes, 25) : '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-btn-group">
                                @if($request->status === 'pending')
                                    <form action="{{ route('item-requests.approve', $request->request_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-approve" title="Approve">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn-reject" onclick="openRejectModal('{{ $request->request_id }}')" title="Reject">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                @elseif($request->status === 'approved')
                                    <form action="{{ route('item-requests.fulfill', $request->request_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-fulfill" title="Fulfill" onclick="return confirm('Fulfill this request? This will deduct stock and assign items to the room.');">
                                            <i class="bi bi-box-seam"></i> Fulfill
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No item requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @include('partials.pagination', ['paginator' => $requests])
    </div>
</div>

<script>
function openRejectModal(requestId) {
    const form = document.getElementById('rejectForm');
    form.action = `/item-requests/${requestId}/reject`;
    toggleModal('rejectModal', 'open');
}
</script>
@endsection
