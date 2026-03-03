@extends('dashboard')

@section('title', 'Edit Employee - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .edit-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .edit-header {
            background: linear-gradient(135deg, #1e2a47 0%, #2d3a5c 100%);
            color: white;
            padding: 24px 32px;
            text-align: center;
        }
        
        .edit-header h3 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .edit-body {
            padding: 32px;
        }
        
        .section-divider {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e2a47;
            margin: 28px 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #c8a858;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-divider:first-child {
            margin-top: 0;
        }
        
        .section-divider i {
            color: #c8a858;
            font-size: 1.1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 16px;
        }
        
        .form-row.single {
            grid-template-columns: 1fr;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            display: block;
            font-size: 0.875rem;
        }
        
        .form-control {
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: #fafafa;
        }
        
        .form-control:focus {
            border-color: #c8a858;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.12);
            background: #fff;
            outline: none;
        }
        
        .form-control::placeholder {
            color: #9ca3af;
        }
        
        .password-hint {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 6px;
        }
        
        .password-hint.valid { color: #059669; }
        .password-hint.invalid { color: #dc2626; }
        
        .status-toggle-wrapper {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
        }
        
        /* Modern Toggle Switch */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toggle-label {
            font-weight: 600;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .toggle-label.active-label {
            color: #9ca3af;
        }

        .toggle-label.inactive-label {
            color: #9ca3af;
        }

        .toggle-container.active .active-label {
            color: #22c55e;
        }

        .toggle-container.inactive .inactive-label {
            color: #ef4444;
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
            cursor: pointer;
        }

        .toggle-slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            border-radius: 30px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0 4px;
        }

        .toggle-container.active .toggle-slider {
            background-color: #22c55e;
        }

        .toggle-container.inactive .toggle-slider {
            background-color: #9ca3af;
        }

        .toggle-knob {
            width: 22px;
            height: 22px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .toggle-container.active .toggle-knob {
            transform: translateX(30px);
        }

        .toggle-container.inactive .toggle-knob {
            transform: translateX(0);
        }

        .toggle-icon {
            font-size: 12px;
            font-weight: bold;
        }

        .toggle-container.active .toggle-icon {
            color: #22c55e;
        }

        .toggle-container.inactive .toggle-icon {
            color: #9ca3af;
        }
        
        .profile-section {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            border: 2px dashed #e5e7eb;
        }
        
        .profile-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e5e7eb;
            flex-shrink: 0;
        }
        
        .profile-upload {
            flex: 1;
        }
        
        .profile-upload small {
            display: block;
            margin-top: 6px;
            color: #6b7280;
            font-size: 0.75rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 14px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }
        
        .btn-save {
            flex: 1;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(34, 197, 94, 0.35);
        }
        
        .btn-cancel {
            background: #f3f4f6;
            color: #4b5563;
            border: 1.5px solid #e5e7eb;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-cancel:hover {
            background: #e5e7eb;
            color: #374151;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 24px;
            border: none;
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .edit-body {
                padding: 20px;
            }
            .action-buttons {
                flex-direction: column-reverse;
            }
            .profile-section {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
@endsection

@section('content')
<div class="container py-4">
    <h2 class="mb-4">EDIT EMPLOYEE</h2>
    
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
    
    <div class="edit-container">
        <div class="edit-card">
            <div class="edit-header">
                <h3><i class="bi bi-person-gear"></i> Edit Employee Details</h3>
            </div>
            
            <div class="edit-body">
                <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Account Section -->
                    <div class="section-divider">
                        <i class="bi bi-shield-lock"></i> Account Details
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $employee->user->name) }}" placeholder="Enter username" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $employee->user->email) }}" placeholder="Enter email address" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">New Password <span style="font-weight: 400; color: #9ca3af;">(optional)</span></label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                            <div class="password-hint" id="password-hint">Min 12 alphanumeric characters</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control" required>
                                <option value="inventory_manager" {{ old('role', $employee->user->role) == 'inventory_manager' ? 'selected' : '' }}>Inventory Manager</option>
                                <option value="room_manager" {{ old('role', $employee->user->role) == 'room_manager' ? 'selected' : '' }}>Room Manager</option>
                                <option value="security" {{ old('role', $employee->user->role) == 'security' ? 'selected' : '' }}>Security</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Status</label>
                            <div class="toggle-container {{ old('status', $employee->user->status) == 'active' ? 'active' : 'inactive' }}" id="statusToggle" onclick="toggleStatus()">
                                <span class="toggle-label active-label">Active</span>
                                <div class="toggle-switch">
                                    <div class="toggle-slider">
                                        <div class="toggle-knob">
                                            <span class="toggle-icon">{{ old('status', $employee->user->status) == 'active' ? '✓' : '✕' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="status" id="statusValue" value="{{ old('status', $employee->user->status) }}">
                                <span class="toggle-label inactive-label">Inactive</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personal Info Section -->
                    <div class="section-divider">
                        <i class="bi bi-person-vcard"></i> Personal Information
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" placeholder="Enter last name" required>
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $employee->address) }}" placeholder="Enter full address" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $employee->contact_number) }}" placeholder="Enter contact number" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">SSS Number</label>
                            <input type="text" name="sss_number" class="form-control" value="{{ old('sss_number', $employee->sss_number) }}" placeholder="Enter SSS number" required>
                        </div>
                    </div>
                    
                    <div class="form-row single">
                        <div class="form-group">
                            <label class="form-label">Profile Picture</label>
                            <div class="profile-section">
                                <img src="{{ asset('storage/' . ($employee->profile_picture ?? 'TCEmployeeProfile.png')) }}" alt="Profile" class="profile-preview" id="profile-preview">
                                <div class="profile-upload">
                                    <input type="file" name="profile_picture" id="profile_input" class="form-control" accept="image/*" onchange="previewImage(this)">
                                    <small>JPG, PNG or GIF (Max 2MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="{{ route('employees.index') }}" class="btn-cancel">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check-lg"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleStatus() {
        const container = document.getElementById('statusToggle');
        const statusValue = document.getElementById('statusValue');
        const icon = container.querySelector('.toggle-icon');
        
        if (container.classList.contains('active')) {
            container.classList.remove('active');
            container.classList.add('inactive');
            statusValue.value = 'inactive';
            icon.textContent = '✕';
        } else {
            container.classList.remove('inactive');
            container.classList.add('active');
            statusValue.value = 'active';
            icon.textContent = '✓';
        }
    }
    
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const hint = document.getElementById('password-hint');
        
        if (password.length === 0) {
            hint.className = 'password-hint';
            hint.textContent = 'Min 12 alphanumeric characters';
        } else if (password.length >= 12 && /^[a-zA-Z0-9]+$/.test(password)) {
            hint.className = 'password-hint valid';
            hint.textContent = '✓ Password meets requirements';
        } else {
            hint.className = 'password-hint invalid';
            hint.textContent = password.length < 12 ? 'Need ' + (12 - password.length) + ' more characters' : 'Alphanumeric only';
        }
    });
</script>
@endsection