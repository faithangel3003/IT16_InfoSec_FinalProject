@extends('dashboard')

@section('title', 'Item Categories - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2>ITEM CATEGORIES</h2>
    
    <div class="page-filter-bar">
        <form action="{{ route('inventory.itemctgry') }}" method="GET" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Search categories..." value="{{ request('search') }}" />
            <button type="submit" class="btn-search">Search</button>
        </form>
        <div class="filter-right">
            <button class="btn-action-primary" onclick="toggleModal('addCategoryModal', 'open')">
                <i class="bi bi-plus-circle"></i> Add Category
            </button>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="supplier-modal hidden" id="addCategoryModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Add Item Category
            </div>
            <div class="modal-body">
                <form action="{{ route('inventory.itemctgry.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter category name" required>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add">Add Category</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('addCategoryModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="supplier-modal hidden" id="editCategoryModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Edit Item Category
            </div>
            <div class="modal-body">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" placeholder="Enter category name" required>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-update">Update Category</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('editCategoryModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="page-table-card">
        <table class="page-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-edit" onclick="openEditModal('{{ $category->itemctgry_id }}', '{{ addslashes($category->name) }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('inventory.itemctgry.destroy', $category->itemctgry_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @include('partials.pagination', ['paginator' => $categories])
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
    
    function openEditModal(categoryId, categoryName) {
        // Set form action URL
        const form = document.getElementById('editCategoryForm');
        form.action = '{{ url("inventory/item-categories") }}/' + categoryId;
        
        // Populate form fields
        document.getElementById('edit_name').value = categoryName;
        
        // Open modal
        toggleModal('editCategoryModal', 'open');
    }
</script>
@endsection

