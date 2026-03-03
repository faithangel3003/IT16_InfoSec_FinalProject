@extends('dashboard')

@section('title', 'Employees - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .password-requirements {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 3px;
        }
        .password-requirements.valid { color: #28a745; }
        .password-requirements.invalid { color: #dc3545; }
        
        /* Modern Toggle Switch */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .toggle-label {
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .toggle-label.active-label {
            color: #28a745;
        }
        .toggle-label.inactive-label {
            color: #adb5bd;
        }
        .toggle-label.active-label.dimmed {
            color: #adb5bd;
        }
        .toggle-label.inactive-label.highlighted {
            color: #6c757d;
        }
        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
            background: #28a745;
            border-radius: 30px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .toggle-switch.inactive {
            background: #e9ecef;
        }
        .toggle-switch .toggle-slider {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .toggle-switch .toggle-slider i {
            font-size: 12px;
        }
        .toggle-switch .toggle-slider .icon-check {
            color: #28a745;
        }
        .toggle-switch .toggle-slider .icon-x {
            color: #6c757d;
            display: none;
        }
        .toggle-switch.inactive .toggle-slider {
            transform: translateX(30px);
        }
        .toggle-switch.inactive .toggle-slider .icon-check {
            display: none;
        }
        .toggle-switch.inactive .toggle-slider .icon-x {
            display: block;
        }
        
        /* Employees Page - Consistent Styling */
        .employees-page { padding: 20px 30px; }
        
        .employees-title {
            color: #1e2a47;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        
        /* KPI Stats Grid */
        .employee-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 1200px) {
            .employee-kpi-grid { grid-template-columns: repeat(3, 1fr); }
        }
        
        @media (max-width: 768px) {
            .employee-kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 480px) {
            .employee-kpi-grid { grid-template-columns: 1fr; }
        }
        
        .employee-kpi-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .employee-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }
        
        .kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        
        .kpi-icon.navy { background: rgba(30, 42, 71, 0.1); color: #1e2a47; }
        .kpi-icon.success { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .kpi-icon.danger { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .kpi-icon.gold { background: rgba(200, 168, 88, 0.15); color: #c8a858; }
        .kpi-icon.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        
        .kpi-content {
            flex: 1;
        }
        
        .kpi-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e2a47;
            line-height: 1.2;
        }
        
        .kpi-value.success { color: #28a745; }
        .kpi-value.danger { color: #dc3545; }
        
        .kpi-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        /* Filter Bar */
        .filter-bar {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-form input {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            min-width: 250px;
        }
        
        .search-form input:focus {
            border-color: #c8a858;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.2);
            outline: none;
        }
        
        .btn-search {
            background: #1e2a47;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-search:hover {
            background: #c8a858;
        }
        
        .btn-add-emp {
            background: #1e2a47;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-add-emp:hover {
            background: #c8a858;
        }
        
        /* Table Card */
        .employees-table-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .employees-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .employees-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .employees-table th {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .employees-table td {
            padding: 10px 14px;
            color: #495057;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.85rem;
            vertical-align: middle;
        }
        
        .employees-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e9ecef;
        }
        
        .role-badge {
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .role-inventory { background: rgba(23, 162, 184, 0.1); color: #117a8b; }
        .role-room { background: rgba(255, 193, 7, 0.15); color: #b8860b; }
        .role-other { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-active { background: rgba(40, 167, 69, 0.1); color: #28a745; }
        .status-inactive { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .btn-action.edit {
            background: #fdf5e6;
            color: #c8a858;
        }
        
        .btn-action.edit:hover {
            background: #f5edd8;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(200, 168, 88, 0.25);
        }
        
        .btn-action.delete {
            background: #fce8eb;
            color: #dc3545;
        }
        
        .btn-action.delete:hover {
            background: #f8d7da;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.25);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
<div class="employees-page">
    <h1 class="employees-title">EMPLOYEES</h1>
    
    <!-- KPI Stats Cards -->
    <div class="employee-kpi-grid">
        <div class="employee-kpi-card">
            <div class="kpi-icon navy"><i class="bi bi-people-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $totalEmployees }}</div>
                <div class="kpi-label">Total Employees</div>
            </div>
        </div>
        <div class="employee-kpi-card">
            <div class="kpi-icon success"><i class="bi bi-person-check-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-value success">{{ $activeEmployees }}</div>
                <div class="kpi-label">Active</div>
            </div>
        </div>
        <div class="employee-kpi-card">
            <div class="kpi-icon danger"><i class="bi bi-person-x-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-value danger">{{ $inactiveEmployees }}</div>
                <div class="kpi-label">Inactive</div>
            </div>
        </div>
        <div class="employee-kpi-card">
            <div class="kpi-icon gold"><i class="bi bi-box-seam-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $inventoryManagers }}</div>
                <div class="kpi-label">Inventory Managers</div>
            </div>
        </div>
        <div class="employee-kpi-card">
            <div class="kpi-icon info"><i class="bi bi-door-open-fill"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $roomManagers }}</div>
                <div class="kpi-label">Room Managers</div>
            </div>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="validation-errors-modal" id="validationErrorsModal">
            <div class="validation-errors-content">
                <div class="validation-errors-icon">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <h3>Please fix the following errors</h3>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="notification-btn" onclick="document.getElementById('validationErrorsModal').classList.remove('active')">
                    <i class="bi bi-check-lg"></i> OK
                </button>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('validationErrorsModal').classList.add('active');
            });
        </script>
    @endif
    
    <div class="filter-bar" style="justify-content: space-between;">
        <form action="{{ route('employees.index') }}" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search employees..." value="{{ request('search') }}" />
            <button type="submit" class="btn-search">Search</button>
        </form>
        <button class="btn-add-emp" onclick="verifyAndAddEmployee()">
            <i class="bi bi-plus-circle"></i> Add Employee
        </button>
    </div>

    <!-- Add Employee Modal -->
    <div class="supplier-modal hidden" id="addEmployeeModal">
        <div class="modal-content" style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
            <div class="supplier-modal-header">
                Add Employee
            </div>
            <div class="modal-body">
                <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Username</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                        <div class="password-requirements" id="password-requirements">Min 12 alphanumeric characters</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="" disabled selected>Select a role</option>
                            <option value="inventory_manager">Inventory Manager</option>
                            <option value="room_manager">Room Manager</option>
                            <option value="security">Security</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="toggle-container">
                            <span class="toggle-label active-label" id="active_label">Active</span>
                            <div class="toggle-switch" id="status_toggle" onclick="toggleStatus()">
                                <div class="toggle-slider">
                                    <i class="bi bi-check-lg icon-check"></i>
                                    <i class="bi bi-x-lg icon-x"></i>
                                </div>
                            </div>
                            <input type="hidden" name="status" id="status_hidden" value="active">
                            <span class="toggle-label inactive-label" id="inactive_label">Inactive</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter first name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter last name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Enter contact number" required>
                    </div>
                    <div class="mb-3">
                        <label for="sss_number" class="form-label">SSS Number</label>
                        <input type="text" class="form-control" id="sss_number" name="sss_number" placeholder="Enter SSS number" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add">Add Employee</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('addEmployeeModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="employees-table-card">
        <table class="employees-table">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Employee Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            @if($employee->profile_picture)
                                <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt="Profile" class="profile-img">
                            @else
                                <div class="profile-img" style="background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person" style="color: #6c757d;"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>
                            <span class="masked-data">
                                <span class="masked-value" id="email-{{ $employee->id }}" 
                                      data-masked-value="{{ \App\Http\Controllers\SecurityController::maskValue($employee->email, 'email') }}">
                                    {{ \App\Http\Controllers\SecurityController::maskValue($employee->email, 'email') }}
                                </span>
                                <button type="button" class="unmask-btn" 
                                        onclick="SecurityUtils.unmaskData('employee', {{ $employee->id }}, 'email', document.getElementById('email-{{ $employee->id }}'))"
                                        title="Click to reveal">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </span>
                        </td>
                        <td>
                            <span class="masked-data">
                                <span class="masked-value" id="phone-{{ $employee->id }}"
                                      data-masked-value="{{ \App\Http\Controllers\SecurityController::maskValue($employee->contact_number, 'phone') }}">
                                    {{ \App\Http\Controllers\SecurityController::maskValue($employee->contact_number, 'phone') }}
                                </span>
                                <button type="button" class="unmask-btn"
                                        onclick="SecurityUtils.unmaskData('employee', {{ $employee->id }}, 'phone', document.getElementById('phone-{{ $employee->id }}'))"
                                        title="Click to reveal">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </span>
                        </td>
                        <td>
                            @if($employee->user->role == 'inventory_manager')
                                <span class="role-badge role-inventory">Inventory Manager</span>
                            @elseif($employee->user->role == 'room_manager')
                                <span class="role-badge role-room">Room Manager</span>
                            @else
                                <span class="role-badge role-other">{{ ucfirst($employee->user->role) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($employee->user->status == 'active')
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                <button type="button" class="btn-action edit" onclick="verifyAndEditEmployee({{ $employee->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this employee?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @include('partials.pagination', ['paginator' => $employees])
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
            if (modalContent) {
                modalContent.classList.add('fade-out');
                setTimeout(() => {
                    modal.classList.remove('show');
                    modal.classList.add('hidden');
                    modalContent.classList.remove('fade-out');
                }, 500);
            } else {
                modal.classList.remove('show');
                modal.classList.add('hidden');
            }
        } else {
            modal.classList.contains('hidden') ? toggleModal(modalId, 'open') : toggleModal(modalId, 'close');
        }
    }

    function toggleStatus() {
        const toggle = document.getElementById('status_toggle');
        const statusInput = document.getElementById('status_hidden');
        const activeLabel = document.getElementById('active_label');
        const inactiveLabel = document.getElementById('inactive_label');
        
        if (toggle.classList.contains('inactive')) {
            // Switch to active
            toggle.classList.remove('inactive');
            statusInput.value = 'active';
            activeLabel.classList.remove('dimmed');
            inactiveLabel.classList.remove('highlighted');
        } else {
            // Switch to inactive
            toggle.classList.add('inactive');
            statusInput.value = 'inactive';
            activeLabel.classList.add('dimmed');
            inactiveLabel.classList.add('highlighted');
        }
    }

    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const requirements = document.getElementById('password-requirements');
        const isValid = password.length >= 12 && /^[a-zA-Z0-9]+$/.test(password);
        
        if (password.length === 0) {
            requirements.className = 'password-requirements';
            requirements.innerHTML = 'Min 12 alphanumeric characters';
        } else if (isValid) {
            requirements.className = 'password-requirements valid';
            requirements.innerHTML = '✓ Password meets requirements';
        } else {
            requirements.className = 'password-requirements invalid';
            requirements.innerHTML = password.length < 12 ? 'Need ' + (12 - password.length) + ' more characters' : 'Alphanumeric only';
        }
    });

    // Credential verification for Add Employee
    function verifyAndAddEmployee() {
        SecurityUtils.verifyCredentials('add_employee', function(token) {
            toggleModal('addEmployeeModal', 'open');
        });
    }

    // Credential verification for Edit Employee
    function verifyAndEditEmployee(employeeId) {
        SecurityUtils.verifyCredentials('edit_employee', function(token) {
            window.location.href = '/employees/' + employeeId + '/edit';
        });
    }
</script>
@endsection