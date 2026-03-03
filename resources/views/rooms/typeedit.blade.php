@extends('dashboard')

@section('title', 'Edit Room Type - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-5 text-primary">EDIT ROOM TYPE</h2>
    <div class="supplier-modal-content mx-auto">
        <div class="supplier-modal-header">
            EDIT ROOM TYPE INFORMATION
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
            <form action="{{ route('rooms.type.update', $roomType->roomtype_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Room Type Name</label>
                    <input type="text" class="form-control form-input" id="name" name="name" 
                           value="{{ old('name', $roomType->name) }}" 
                           placeholder="Enter room type name" required>
                </div>

                <div class="button-row mt-4">
                    <button type="submit" class="btn-update">Update Room Type</button>
                    <a href="{{ route('rooms.type') }}" class="btn-canceledit">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection