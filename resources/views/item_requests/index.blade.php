@extends('dashboard')

@section('title', 'Item Requests - TriadCo')

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
        
        .item-stock-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .item-stock-info.low-stock {
            color: #dc3545;
        }
        
        .request-notes {
            font-size: 0.85rem;
            color: #6c757d;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .rejection-reason {
            font-size: 0.8rem;
            color: #dc3545;
            font-style: italic;
            margin-top: 4px;
        }
    </style>
@endsection

@section('content')
<div class="container py-5">
    <h2>ITEM REQUESTS</h2>

    <!-- Stats Cards -->
    <div class="request-stats-grid">
        <div class="request-stat-card">
            <div class="stat-icon warning"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value">{{ $requests->where('status', 'pending')->count() }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="request-stat-card">
            <div class="stat-icon info"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value">{{ $requests->where('status', 'approved')->count() }}</div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="request-stat-card">
            <div class="stat-icon success"><i class="bi bi-box-seam"></i></div>
            <div class="stat-value">{{ $requests->where('status', 'fulfilled')->count() }}</div>
            <div class="stat-label">Fulfilled</div>
        </div>
        <div class="request-stat-card">
            <div class="stat-icon danger"><i class="bi bi-x-circle"></i></div>
            <div class="stat-value">{{ $requests->where('status', 'rejected')->count() }}</div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    <div class="page-filter-bar">
        <form action="{{ route('item-requests.index') }}" method="GET" class="filter-left">
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
        <div class="filter-right">
            <button type="button" class="btn-action-primary" onclick="toggleModal('newRequestModal', 'open')">
                <i class="bi bi-plus-circle"></i> New Request
            </button>
        </div>
    </div>

    <!-- New Request Modal -->
    <div class="supplier-modal hidden" id="newRequestModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Request Item from Inventory
            </div>
            <div class="modal-body">
                <form action="{{ route('item-requests.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="room_id" class="form-label">Select Room</label>
                        <select class="form-control" id="room_id" name="room_id" required>
                            <option value="" disabled selected>Select a room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->room_id }}">{{ $room->name }} ({{ $room->type->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="item_id" class="form-label">Select Item</label>
                        <select class="form-control" id="item_id" name="item_id" required onchange="updateStockInfo()">
                            <option value="" disabled selected>Select an item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->item_id }}" data-stock="{{ $item->in_stock }}">
                                    {{ $item->name }} ({{ $item->category->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        <div id="stockInfo" class="item-stock-info"></div>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" placeholder="Enter quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Reason for request..."></textarea>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add">Submit Request</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('newRequestModal', 'close')">Cancel</button>
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
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->request_id }}</td>
                        <td>{{ $request->room->name ?? 'N/A' }}</td>
                        <td>{{ $request->item->name ?? 'N/A' }}</td>
                        <td>{{ $request->quantity }}</td>
                        <td>
                            <span class="badge-{{ $request->status }}">
                                {{ ucfirst($request->status) }}
                            </span>
                            @if($request->status === 'rejected' && $request->rejection_reason)
                                <div class="rejection-reason">{{ Str::limit($request->rejection_reason, 50) }}</div>
                            @endif
                        </td>
                        <td>{{ $request->requested_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <span class="request-notes" title="{{ $request->notes }}">
                                {{ $request->notes ? Str::limit($request->notes, 30) : '-' }}
                            </span>
                        </td>
                        <td>
                            @if($request->status === 'pending')
                                <form action="{{ route('item-requests.cancel', $request->request_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Cancel this request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No item requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @include('partials.pagination', ['paginator' => $requests])
    </div>
</div>

<script>
function updateStockInfo() {
    const select = document.getElementById('item_id');
    const stockInfo = document.getElementById('stockInfo');
    const quantityInput = document.getElementById('quantity');
    
    if (select.value) {
        const selectedOption = select.options[select.selectedIndex];
        const stock = selectedOption.dataset.stock;
        
        stockInfo.textContent = `Available stock: ${stock}`;
        stockInfo.classList.toggle('low-stock', stock < 10);
        
        quantityInput.max = stock;
    } else {
        stockInfo.textContent = '';
    }
}
</script>
@endsection
