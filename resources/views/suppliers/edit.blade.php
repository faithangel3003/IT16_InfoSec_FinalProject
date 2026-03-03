@extends('dashboard')

@section('title', 'Edit Supplier - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-5 text-primary">EDIT SUPPLIER</h2>
    <div class="supplier-modal-content mx-auto">
        <div class="supplier-modal-header">
            EDIT SUPPLIER INFORMATION
        </div>
        @if ($errors->any())
        <div class="validation-errors-modal" id="validationErrorsModal">
            <div class="validation-errors-content">
                <span class="close-validation-modal" onclick="closeValidationModal()">&times;</span>
                <div class="validation-icon">⚠️</div>
                <h3>Validation Errors</h3>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="validation-close-btn" onclick="closeValidationModal()">OK</button>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('validationErrorsModal').style.display = 'flex';
            });
            function closeValidationModal() {
                document.getElementById('validationErrorsModal').style.display = 'none';
            }
        </script>
        @endif
        <div class="modal-body">
            <form action="{{ route('suppliers.update', $supplier->supplier_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Supplier Name</label>
                    <input type="text" class="form-control form-input" id="name" name="name" 
                           value="{{ old('name', $supplier->name) }}" 
                           placeholder="Enter supplier name" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Supplier Address</label>
                    <input type="text" class="form-control form-input" id="address" name="address" 
                           value="{{ old('address', $supplier->address) }}" 
                           placeholder="Enter address" required>
                </div>

                <div class="mb-3">
                    <label for="number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control form-input" id="number" name="number" 
                           value="{{ old('number', $supplier->number) }}" 
                           placeholder="Enter phone number" required>
                </div>

                <div class="mb-3">
                    <label for="contact_person" class="form-label">Contact Person</label>
                    <input type="text" class="form-control form-input" id="contact_person" name="contact_person" 
                           value="{{ old('contact_person', $supplier->contact_person) }}" 
                           placeholder="Enter contact person" required>
                </div>

                <div class="button-row mt-4">
                    <button type="submit" class="btn-update">Update Supplier</button>
                    <a href="{{ route('suppliers.index') }}" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection