@extends('dashboard')

@section('title', 'Stock-Out - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2>STOCK-OUT</h2>
    
    <div class="page-filter-bar">
        <form action="{{ route('stock_out.index') }}" method="GET" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Search stock-out..." value="{{ request('search') }}" />
            <button type="submit" class="btn-search">Search</button>
        </form>
        <div class="filter-right">
            <button type="button" class="btn-action-primary" onclick="toggleModal('assignRoomModal', 'open')">
                <i class="bi bi-door-open"></i> Assign to Room
            </button>
        </div>
    </div>
    
    <!-- Assign to Room Modal -->
    <div class="supplier-modal hidden" id="assignRoomModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Assign Item to Room
            </div>
            <div class="modal-body">
                <form action="{{ route('stock_out.finalize') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="room_id" class="form-label">Select Room</label>
                        <select class="form-control" id="room_id" name="room_id" required>
                            <option value="" disabled selected>Choose a room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->room_id }}">{{ $room->name }} ({{ $room->type->name ?? 'N/A' }}) - {{ ucfirst($room->status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="stock_out_id" class="form-label">Select Item</label>
                        <select class="form-control" id="stock_out_id" name="stock_out_id" required onchange="updateMaxQuantity(this)">
                            <option value="" disabled selected data-max="0">Choose an item</option>
                            @foreach($stockOutItems as $item)
                                <option value="{{ $item->id }}" data-max="{{ $item->quantity }}">{{ $item->item->name }} (Available: {{ $item->quantity }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="1" value="1" required>
                    </div>
                    
                    <div class="button-row">
                        <button type="submit" class="btn-add">Assign Items</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('assignRoomModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="page-table-card">
        <table class="page-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOutItems as $item)
                    <tr>
                        <td>{{ $item->item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No items in the Stock-Out Cart.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @include('partials.pagination', ['paginator' => $stockOutItems])
    </div>
</div>

<script>
    function toggleModal(modalId, action = 'toggle') {
        const modal = document.getElementById(modalId);
        
        if (action === 'open') {
            modal.classList.remove('hidden');
            modal.classList.add('show');
        } else if (action === 'close') {
            const modalContent = modal.querySelector('.modal-content');
            modalContent.classList.add('fade-out');
            
            setTimeout(() => {
                modal.classList.remove('show');
                modal.classList.add('hidden');
                modalContent.classList.remove('fade-out');
            }, 500);
        } else {
            if (modal.classList.contains('hidden')) {
                toggleModal(modalId, 'open');
            } else {
                toggleModal(modalId, 'close');
            }
        }
    }
    
    function updateMaxQuantity(select) {
        const selectedOption = select.options[select.selectedIndex];
        const maxQty = selectedOption.dataset.max || 1;
        const quantityInput = document.getElementById('quantity');
        quantityInput.max = maxQty;
        quantityInput.value = maxQty;
    }
</script>
@endsection