@extends('dashboard')

@section('title', 'Inventory - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2>INVENTORY</h2>
    
    <div class="page-filter-bar">
        <form action="{{ route('inventory.index') }}" method="GET" class="filter-left">
            <select name="category_filter" class="search-input" style="min-width: 150px;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->itemctgry_id }}" {{ request('category_filter') == $category->itemctgry_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" class="search-input" placeholder="Search items..." value="{{ request('search') }}" />
            <button type="submit" class="btn-search">Search</button>
        </form>
        <div class="filter-right">
            <button class="btn-action-primary" onclick="toggleModal('addItemModal', 'open')">
                <i class="bi bi-plus-circle"></i> Add Item
            </button>
        </div>
    </div>
    
    <!-- Add Item Modal -->
    <div class="supplier-modal hidden" id="addItemModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Add Item
            </div>
            <div class="modal-body">
                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter item name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Item Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->itemctgry_id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Enter price" required>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add">Add Item</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('addItemModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="supplier-modal hidden" id="editItemModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Edit Item
            </div>
            <div class="modal-body">
                <form id="editItemForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_item_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="edit_item_name" name="name" placeholder="Enter item name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category_id" class="form-label">Item Category</label>
                        <select class="form-control" id="edit_category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->itemctgry_id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="edit_price" name="price" placeholder="Enter price" required>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-update">Update Item</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('editItemModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock-Out Modal -->
    <div class="supplier-modal hidden" id="stockOutModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Stock Out Item
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="stockOutForm">
                    @csrf
                    <input type="hidden" id="stock_out_item_id" name="item_id">
                    <div class="mb-3">
                        <label for="stock_out_item_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="stock_out_item_name" name="item_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="stock_out_quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="stock_out_quantity" name="quantity" min="1" required>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add">Move to Stock-Out</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('stockOutModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="page-table-card">
        <table class="page-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>In Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->category->name }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td>
                                @if ($item->in_stock > 0)
                                    {{ $item->in_stock }}
                                @else
                                    <span class="badge badge-empty">No Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-small-black" 
                                        onclick="openStockOutModal('{{ $item->item_id }}', '{{ $item->name }}', {{ $item->in_stock }})">
                                        Move to Stock-Out
                                    </button>
                                    <button type="button" class="btn btn-edit" 
                                        onclick="openEditItemModal('{{ $item->item_id }}', '{{ addslashes($item->name) }}', '{{ $item->category_id }}', '{{ $item->price }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('inventory.destroy', $item->item_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @include('partials.pagination', ['paginator' => $items])
    </div>
</div>
<br>
<div class="page-table-card mt-4">
    <div style="padding: 15px 20px; border-bottom: 1px solid #dee2e6;">
        <h4 style="margin: 0; color: #1e2a47; font-weight: 600;">Returned Items</h4>
    </div>
    <table class="page-table">
        <thead>
            <tr>
                <th>Returned Item Name</th>
                <th>Quantity</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returnedItems as $returnedItem)
                <tr id="returned-item-{{ $returnedItem->item_id }}">
                    <td>{{ $returnedItem->item->name }}</td>
                    <td id="quantity-{{ $returnedItem->item_id }}">{{ $returnedItem->quantity }}</td>
                    <td style="width: 50%;">{{ $returnedItem->reason }}</td>
                    <td> 
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-md btn-small-black" 
                                onclick="openStockOutModal('{{ $returnedItem->item_id }}', '{{ $returnedItem->item->name }}', {{ $returnedItem->quantity }})">
                                Move to Stock-Out
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No returned items found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.pagination', ['paginator' => $returnedItems])
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
    
    function openStockOutModal(itemId, itemName, maxQty) {
        document.getElementById('stock_out_item_id').value = itemId;
        document.getElementById('stock_out_item_name').value = itemName;
        document.getElementById('stock_out_quantity').max = maxQty;
        document.getElementById('stock_out_quantity').value = 1;
        document.getElementById('stockOutForm').action = '/inventory/' + itemId + '/stock-out';
        toggleModal('stockOutModal', 'open');
    }
    
    function openEditItemModal(itemId, name, categoryId, price) {
        const form = document.getElementById('editItemForm');
        form.action = '{{ url("inventory") }}/' + itemId;
        
        document.getElementById('edit_item_name').value = name;
        document.getElementById('edit_category_id').value = categoryId;
        document.getElementById('edit_price').value = price;
        
        toggleModal('editItemModal', 'open');
    }
</script>
@endsection