{{-- filepath: c:\Users\jarma\Documents\TriadCo\TriadCo\resources\views\rooms\edit.blade.php --}}
@extends('dashboard')

@section('title', 'Edit Room - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-5 text-primary">EDIT ROOM</h2>
    <div class="supplier-modal-content mx-auto">
        <div class="supplier-modal-header">
            EDIT ROOM INFORMATION
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
            <form action="{{ route('rooms.update', ['id' => $room->room_id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Room Name</label>
                    <input type="text" class="form-control form-input" id="name" name="name" 
                        value="{{ old('name', $room->name) }}" 
                        placeholder="Enter room name" required>
                </div>

                <div class="mb-3">
                    <label for="roomtype_id" class="form-label">Room Type</label>
                    <select class="form-control item-select" id="roomtype_id" name="roomtype_id" required>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->roomtype_id }}" 
                                {{ $room->roomtype_id == $type->roomtype_id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <br>
                <div class="button-row mt-4">
                    <button type="submit" class="btn-update">Update Room</button>
                    <a href="{{ route('rooms.index') }}" class="btn-canceledit">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection