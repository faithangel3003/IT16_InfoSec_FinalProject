@extends('dashboard')

@section('title', 'Create Employee - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .password-requirements.valid {
            color: #28a745;
        }
        .password-requirements.invalid {
            color: #dc3545;
        }
        .password-input.invalid {
            border: 2px solid #dc3545 !important;
            background-color: #fff5f5 !important;
        }
        .password-input.valid {
            border: 2px solid #3498db !important;
            background-color: #f0f8ff !important;
        }
    </style>
@endsection

@section('content')
<div class="container py-5 employee-create">
    <div class="glass-card glass-card-wide mx-auto">
        <div class="supplier-modal-header">
            CREATE EMPLOYEE
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
        
        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Employee Account Section -->
            <h4 class="fw-bold text-primary text-center mb-4 mt-4">Employee Account</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Username:</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control password-input" required>
                        <div class="password-requirements" id="password-requirements">
                            Must be at least 12 alphanumeric characters (letters A-Z, a-z and numbers 0-9 only)
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="role" class="form-label">Role:</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="inventory_manager" {{ old('role') == 'inventory_manager' ? 'selected' : '' }}>Inventory Manager</option>
                            <option value="room_manager" {{ old('role') == 'room_manager' ? 'selected' : '' }}>Room Manager</option>
                            <option value="security" {{ old('role') == 'security' ? 'selected' : '' }}>Security</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Employee Information Section -->
            <h4 class="fw-bold text-primary text-center mb-4">Employee Information</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" required>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="contact_number" class="form-label">Contact Number:</label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" value="{{ old('contact_number') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="sss_number" class="form-label">SSS Number:</label>
                        <input type="text" id="sss_number" name="sss_number" class="form-control" value="{{ old('sss_number') }}" required>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="profile_picture" class="form-label">Upload Profile Picture:</label>
                        <input type="file" id="profile_picture" name="profile_picture" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-row mt-4">
                <button type="submit" class="btn-update">Create Employee</button>
                <a href="{{ route('employees.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const requirements = document.getElementById('password-requirements');
    const passwordInput = this;
    const isValid = password.length >= 12 && /^[a-zA-Z0-9]+$/.test(password);
    
    if (password.length === 0) {
        requirements.className = 'password-requirements';
        passwordInput.classList.remove('valid', 'invalid');
    } else if (isValid) {
        requirements.className = 'password-requirements valid';
        requirements.innerHTML = '✓ Password meets requirements';
        passwordInput.classList.remove('invalid');
        passwordInput.classList.add('valid');
    } else {
        requirements.className = 'password-requirements invalid';
        passwordInput.classList.remove('valid');
        passwordInput.classList.add('invalid');
        let message = 'Password must be: ';
        if (password.length < 12) {
            message += 'at least 12 characters';
        }
        if (!/^[a-zA-Z0-9]+$/.test(password)) {
            message += (password.length < 12 ? ' and ' : '') + 'alphanumeric only (no special characters)';
        }
        requirements.innerHTML = message;
    }
});

// Comprehensive form validation before submission
document.querySelector('form').addEventListener('submit', function(e) {
    const errors = [];
    
    // Validate Username
    const username = document.getElementById('name').value.trim();
    if (!username) {
        errors.push('Username is required.');
    } else if (username.length < 3) {
        errors.push('Username must be at least 3 characters.');
    }
    
    // Validate Email
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        errors.push('Email is required.');
    } else if (!emailRegex.test(email)) {
        errors.push('Please enter a valid email address.');
    }
    
    // Validate Password
    const password = document.getElementById('password').value;
    const passwordValid = password.length >= 12 && /^[a-zA-Z0-9]+$/.test(password);
    if (!password) {
        errors.push('Password is required.');
    } else if (password.length < 12) {
        errors.push('Password must be at least 12 characters.');
    } else if (!/^[a-zA-Z0-9]+$/.test(password)) {
        errors.push('Password must contain only alphanumeric characters (letters and numbers).');
    }
    
    // Validate Password Confirmation
    const passwordConfirm = document.getElementById('password_confirmation').value;
    if (!passwordConfirm) {
        errors.push('Password confirmation is required.');
    } else if (password !== passwordConfirm) {
        errors.push('Password confirmation does not match.');
    }
    
    // Validate Role
    const role = document.getElementById('role').value;
    if (!role) {
        errors.push('Please select a role.');
    }
    
    // Validate First Name
    const firstName = document.getElementById('first_name');
    if (firstName && !firstName.value.trim()) {
        errors.push('First name is required.');
    }
    
    // Validate Last Name
    const lastName = document.getElementById('last_name');
    if (lastName && !lastName.value.trim()) {
        errors.push('Last name is required.');
    }
    
    // If there are errors, prevent submission and show modal
    if (errors.length > 0) {
        e.preventDefault();
        
        // Highlight password field if invalid
        if (!passwordValid && password) {
            document.getElementById('password').classList.add('invalid');
        }
        
        // Build error list HTML
        let errorListHTML = errors.map(err => `<li>${err}</li>`).join('');
        
        // Show error modal
        let errorModal = document.getElementById('clientValidationModal');
        if (!errorModal) {
            errorModal = document.createElement('div');
            errorModal.id = 'clientValidationModal';
            errorModal.className = 'validation-errors-modal';
            document.body.appendChild(errorModal);
        }
        
        errorModal.innerHTML = `
            <div class="validation-errors-content">
                <span class="close-validation-modal" onclick="document.getElementById('clientValidationModal').style.display='none'">&times;</span>
                <div class="validation-icon">⚠️</div>
                <h3>Please fix the following errors</h3>
                <ul>${errorListHTML}</ul>
                <button class="validation-close-btn" onclick="document.getElementById('clientValidationModal').style.display='none'">OK</button>
            </div>
        `;
        errorModal.style.display = 'flex';
    }
});
</script>
@endsection
