@extends('dashboard')

@section('title', 'Edit Item - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-5 text-primary">EDIT ITEM</h2>
    <div class="supplier-modal-content mx-auto">
        <div class="supplier-modal-header">
            EDIT ITEM INFORMATION
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
            <!-- Corrected form action to use the update route -->
            <form action="{{ route('inventory.update', ['id' => $item->item_id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Item Name</label>
                    <input type="text" class="form-control form-input" id="name" name="name" 
                           value="{{ old('name', $item->name) }}" 
                           placeholder="Enter item name" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Item Category</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->itemctgry_id }}" 
                                {{ $item->category_id == $category->itemctgry_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control form-input" id="price" name="price" 
                           value="{{ old('price', $item->price) }}" 
                           placeholder="Enter price" required>
                </div>

                <div class="button-row mt-4">
                    <button type="submit" class="btn-update">Update Item</button>
                    <a href="{{ route('inventory.index') }}" class="btn-canceledit">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection