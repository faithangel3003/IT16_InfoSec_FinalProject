@extends('dashboard')

@section('title', 'Room Types - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2>ROOM TYPES</h2>
    
    <div class="page-filter-bar">
        <form action="{{ route('rooms.type') }}" method="GET" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Search room types..." value="{{ request('search') }}" />
            <button type="submit" class="btn-search">Search</button>
        </form>
        <div class="filter-right">
            <button class="btn-action-secondary" onclick="window.location.href='{{ route('rooms.index') }}'">
                <i class="bi bi-arrow-left"></i> Back
            </button>
            <button class="btn-action-primary" onclick="toggleModal('addRoomTypeModal', 'open')">
                <i class="bi bi-plus-circle"></i> Add Room Type
            </button>
        </div>
    </div>

    <!-- Add Room Type Modal -->
    <div class="supplier-modal hidden" id="addRoomTypeModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Add Room Type
            </div>
            <div class="modal-body">
                <form action="{{ route('rooms.type.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Room Type Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter room type name" required>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add">Add Room Type</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('addRoomTypeModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Room Type Modal -->
    <div class="supplier-modal hidden" id="editRoomTypeModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Edit Room Type
            </div>
            <div class="modal-body">
                <form id="editRoomTypeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Room Type Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" placeholder="Enter room type name" required>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-update">Update Room Type</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('editRoomTypeModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Room Types Table -->
    <div class="page-table-card">
        <table class="page-table">
            <thead>
                <tr>
                    <th>Room Type Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    @forelse($roomTypes as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-edit" onclick="openEditModal('{{ $type->roomtype_id }}', '{{ addslashes($type->name) }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('rooms.type.destroy', $type->roomtype_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this room type?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No room types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @include('partials.pagination', ['paginator' => $roomTypes])
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
    
    function openEditModal(typeId, typeName) {
        const form = document.getElementById('editRoomTypeForm');
        form.action = '{{ url("rooms/types") }}/' + typeId;
        
        document.getElementById('edit_name').value = typeName;
        
        toggleModal('editRoomTypeModal', 'open');
    }
</script>
@endsection