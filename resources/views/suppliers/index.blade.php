@extends('dashboard')

@section('title', 'Suppliers - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2>SUPPLIERS</h2>
    
    <div class="page-filter-bar">
        <form action="{{ route('suppliers.index') }}" method="GET" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Search suppliers..." value="{{ request('search') }}" />
            <button type="submit" class="btn-search">Search</button>
        </form>
        <div class="filter-right">
            <button class="btn-action-primary" onclick="toggleModal('addSupplierModal', 'open')">
                <i class="bi bi-plus-circle"></i> Add Supplier
            </button>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div class="supplier-modal hidden" id="addSupplierModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Supplier Information
            </div>
            <div class="modal-body">
                @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter supplier name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Supplier Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" required>
                    </div>
                    <div class="mb-3">
                        <label for="number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="number" name="number" placeholder="Enter phone number" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Enter contact person" required>
                    </div>

                    <div class="button-row">
                        <button type="submit" class="btn-add">Add Supplier</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('addSupplierModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="supplier-modal hidden" id="editSupplierModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Edit Supplier Information
            </div>
            <div class="modal-body">
                <form id="editSupplierForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" placeholder="Enter supplier name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Supplier Address</label>
                        <input type="text" class="form-control" id="edit_address" name="address" placeholder="Enter address" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="edit_number" name="number" placeholder="Enter phone number" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="edit_contact_person" name="contact_person" placeholder="Enter contact person" required>
                    </div>

                    <div class="button-row">
                        <button type="submit" class="btn-update">Update Supplier</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('editSupplierModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Supplier Table -->
    <div class="page-table-card">
        <table class="page-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Contact Person</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>
                                <span class="masked-data">
                                    <span class="masked-value" id="address-supplier-{{ $supplier->supplier_id }}"
                                          data-masked-value="{{ \App\Http\Controllers\SecurityController::maskValue($supplier->address, 'address') }}">
                                        {{ \App\Http\Controllers\SecurityController::maskValue($supplier->address, 'address') }}
                                    </span>
                                    <button type="button" class="unmask-btn"
                                            onclick="SecurityUtils.unmaskData('supplier', '{{ $supplier->supplier_id }}', 'address', document.getElementById('address-supplier-{{ $supplier->supplier_id }}'))"
                                            title="Click to reveal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </span>
                            </td>
                            <td>
                                <span class="masked-data">
                                    <span class="masked-value" id="phone-supplier-{{ $supplier->supplier_id }}"
                                          data-masked-value="{{ \App\Http\Controllers\SecurityController::maskValue($supplier->number, 'phone') }}">
                                        {{ \App\Http\Controllers\SecurityController::maskValue($supplier->number, 'phone') }}
                                    </span>
                                    <button type="button" class="unmask-btn"
                                            onclick="SecurityUtils.unmaskData('supplier', '{{ $supplier->supplier_id }}', 'phone', document.getElementById('phone-supplier-{{ $supplier->supplier_id }}'))"
                                            title="Click to reveal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </span>
                            </td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-edit" 
                                            onclick="openEditSupplierModal('{{ $supplier->supplier_id }}', '{{ addslashes($supplier->name) }}', '{{ addslashes($supplier->address) }}', '{{ addslashes($supplier->number) }}', '{{ addslashes($supplier->contact_person) }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this supplier?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No suppliers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @include('partials.pagination', ['paginator' => $suppliers])
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
    
    function openEditSupplierModal(supplierId, name, address, number, contactPerson) {
        const form = document.getElementById('editSupplierForm');
        form.action = '{{ url("suppliers") }}/' + supplierId;
        
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_address').value = address;
        document.getElementById('edit_number').value = number;
        document.getElementById('edit_contact_person').value = contactPerson;
        
        toggleModal('editSupplierModal', 'open');
    }
    
    // Auto-open modal if there are validation errors
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            toggleModal('addSupplierModal', 'open');
        });
    @endif
</script>
@endsection