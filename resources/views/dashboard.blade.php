<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - TriadCo')</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="{{ asset('css/system.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="{{ asset('js/security-utils.js') }}" defer></script>
    <script src="{{ asset('js/input-validation.js') }}" defer></script>
    @yield('head')
</head>

<body class="{{ session('first_login') ? 'fade-in' : '' }}">
    @php
        session()->forget('first_login');
        $user = Auth::user();
        $profilePicture = $user->name === 'Admin' ? 'TCAdminProfile.png' : 'default-profile.jpg';
    @endphp

    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo-top">
            <img src="{{ asset('images/TCLogo2.png') }}" alt="TriadCo Logo">
        </div>
        <center>
            <ul>
                @if(auth()->user()->role === 'admin')
                    {{-- Admin Navigation --}}
                    <li><a href="{{ route('dashboard') }}" class="nav-link"><i class="bi bi-activity"></i> Dashboard</a></li>
                    <li><a href="{{ route('inventory.reports') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i> Reports</a></li>
                    <li><a href="{{ route('reports.index') }}" class="nav-link"><i class="bi bi-list-columns"></i> Audit Logs</a></li>
                    <li><a href="{{ route('employees.index') }}" class="nav-link"><i class="bi bi-people-fill"></i> Employees</a></li>
                    <li><a href="{{ route('security.index') }}" class="nav-link"><i class="bi bi-shield-lock"></i> Security</a></li>
                    <li><a href="{{ route('incidents.index') }}" class="nav-link"><i class="bi bi-exclamation-triangle"></i> Incidents</a></li>
                    <li><a href="{{ route('system-logs.index') }}" class="nav-link"><i class="bi bi-journal-text"></i> System Logs</a></li>
                @endif

                @if(auth()->user()->role === 'security')
                    {{-- Security Role Navigation --}}
                    <li><a href="{{ route('security.dashboard') }}" class="nav-link"><i class="bi bi-activity"></i> Dashboard</a></li>
                    <li><a href="{{ route('security.index') }}" class="nav-link"><i class="bi bi-shield-lock"></i> Security</a></li>
                    <li><a href="{{ route('incidents.index') }}" class="nav-link"><i class="bi bi-exclamation-triangle"></i> Incidents</a></li>
                    <li><a href="{{ route('system-logs.index') }}" class="nav-link"><i class="bi bi-journal-text"></i> System Logs</a></li>
                @endif
                
                @if(auth()->user()->role === 'inventory_manager' || auth()->user()->role === 'employee')
                    {{-- Inventory Manager Navigation --}}
                    @if(auth()->user()->role === 'inventory_manager')
                        <li><a href="{{ route('inventory.dashboard') }}" class="nav-link"><i class="bi bi-activity"></i> Dashboard</a></li>
                    @endif
                    <li><a href="{{ route('inventory.index') }}" class="nav-link"><i class="bi bi-inboxes-fill"></i> Inventory</a></li>
                    <li><a href="{{ route('stock_in.index') }}" class="nav-link"><i class="bi bi-box-arrow-in-down"></i> Stock-In</a></li>
                    <li><a href="{{ route('stock_out.index') }}" class="nav-link"><i class="bi bi-box-arrow-up"></i> Stock-Out</a></li>
                    <li><a href="{{ route('inventory.itemctgry') }}" class="nav-link"><i class="bi bi-tags-fill"></i> Item Categories</a></li>
                    <li><a href="{{ route('suppliers.index') }}" class="nav-link"><i class="bi bi-person-fill-down"></i> Suppliers</a></li>
                    @if(auth()->user()->role === 'inventory_manager')
                        <li><a href="{{ route('item-requests.manage') }}" class="nav-link"><i class="bi bi-clipboard-check"></i> Item Requests</a></li>
                    @endif
                @endif

                @if(auth()->user()->role === 'room_manager' || auth()->user()->role === 'employee')
                    {{-- Room Manager Navigation --}}
                    @if(auth()->user()->role === 'room_manager')
                        <li><a href="{{ route('room.dashboard') }}" class="nav-link"><i class="bi bi-activity"></i> Dashboard</a></li>
                    @endif
                    <li><a href="{{ route('rooms.index') }}" class="nav-link"><i class="bi bi-door-open-fill"></i> Rooms</a></li>
                    <li><a href="{{ route('rooms.type') }}" class="nav-link"><i class="bi bi-building"></i> Room Types</a></li>
                    <li><a href="{{ route('item-requests.index') }}" class="nav-link"><i class="bi bi-box-arrow-in-down"></i> Item Requests</a></li>
                @endif
            </ul>
        </center>
        <div class="sidebar-logout">
            <form action="{{ route('logout') }}" method="GET">
                @csrf
                <button type="submit" class="sidebar-logout-btn"><i class="bi bi-box-arrow-left"></i> Log-Out</button>
            </form>
        </div>
    </div>

    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <h1>Triad Corporations Hotel Set-Up</h1>
        <div class="user-profile">
            <span>Welcome, {{ $user->name }}!</span>
            
            <div class="profile-picture" onclick="toggleDropdown()">
                <img src="{{ in_array(Auth::user()->role, ['employee', 'inventory_manager', 'room_manager', 'security']) ? asset('images/TCEmployeeProfile.png') : asset('images/' . $profilePicture) }}" alt="Profile Picture">            </div>
            
            {{-- Report Incident Icon for non-admin/security roles --}}
            @if(!in_array(auth()->user()->role, ['admin', 'security']))
            <button class="report-incident-btn" onclick="toggleModal('reportIncidentModal')" title="Report Security Incident">
                <i class="bi bi-shield-exclamation"></i>
            </button>
            @endif
            
            <div class="dropdown-menu hidden" id="dropdownMenu">
                <button class="dropdown-item" onclick="toggleModal('viewProfileModal')">View Profile</button>
                @if(Auth::user()->role === 'admin')
                <button class="dropdown-item" onclick="window.location.href='{{ route('archives.index') }}'">View Archive</button>
                @endif
            </div>
            <div class="modal hidden" id="viewProfileModal">
                <div class="modal-content profile-modal-content">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="{{ in_array(Auth::user()->role, ['employee', 'inventory_manager', 'room_manager', 'security']) ? asset('images/TCEmployeeProfile.png') : asset('images/' . $profilePicture) }}" alt="Profile Picture">
                        </div>
                        <h2>{{ Auth::user()->name }}</h2>
                        <span class="profile-role-badge">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                    </div>
                    <div class="profile-info">
                        <div class="profile-info-item">
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <label>Email</label>
                                <p>{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <i class="bi bi-calendar-check-fill"></i>
                            <div>
                                <label>Member Since</label>
                                <p>{{ Auth::user()->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="profile-info-item">
                            <i class="bi bi-clock-fill"></i>
                            <div>
                                <label>Last Login</label>
                                <p>{{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->format('M d, Y h:i A') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="btn-change-password" onclick="toggleModal('changePasswordModal', 'open')">
                            <i class="bi bi-key-fill"></i> Change Password
                        </button>
                        <button class="btn-security-question" onclick="toggleModal('securityQuestionModal', 'open')">
                            <i class="bi bi-shield-check"></i> Security Question
                            @if(!Auth::user()->hasSecurityQuestion())
                            <span class="badge-warning">Not Set</span>
                            @endif
                        </button>
                        <button class="close-btn" onclick="toggleModal('viewProfileModal')">Close</button>
                    </div>
                </div>
            </div>

            {{-- Change Password Modal --}}
            <div class="modal hidden" id="changePasswordModal">
                <div class="modal-content password-modal-content">
                    <h2><i class="bi bi-shield-lock-fill"></i> Change Password</h2>
                    <div id="passwordChangeStep1">
                        <p class="password-info">For security, please verify your current password first.</p>
                        <div class="form-group">
                            <label for="current_password_verify">Current Password</label>
                            <input type="password" id="current_password_verify" class="form-control" placeholder="Enter current password">
                        </div>
                        <div id="verifyError" class="error-message hidden"></div>
                        <div class="form-actions">
                            <button type="button" class="btn-primary" onclick="verifyCurrentPassword()">
                                <i class="bi bi-check-circle"></i> Verify
                            </button>
                            <button type="button" class="btn-secondary" onclick="toggleModal('changePasswordModal', 'close')">Cancel</button>
                        </div>
                    </div>
                    <div id="passwordChangeStep2" class="hidden">
                        <p class="password-info success"><i class="bi bi-check-circle-fill"></i> Password verified! Enter your new password.</p>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" class="form-control" placeholder="Min 8 chars, uppercase, lowercase, number, special">
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" id="new_password_confirmation" class="form-control" placeholder="Confirm new password">
                        </div>
                        <div id="changeError" class="error-message hidden"></div>
                        <div class="form-actions">
                            <button type="button" class="btn-primary" onclick="changePassword()">
                                <i class="bi bi-key-fill"></i> Change Password
                            </button>
                            <button type="button" class="btn-secondary" onclick="toggleModal('changePasswordModal', 'close')">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Question Modal --}}
            <div class="modal hidden" id="securityQuestionModal">
                <div class="modal-content password-modal-content">
                    <h2><i class="bi bi-shield-check"></i> Security Question Setup</h2>
                    <p class="password-info">Set up a security question to help recover your account if you forget your password.</p>
                    @if(Auth::user()->hasSecurityQuestion())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You already have a security question set up. Updating it will replace the existing one.
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> You have not set up a security question yet. This is required for password recovery.
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="security_question_select">Select Security Question</label>
                        <select id="security_question_select" class="form-control" required>
                            <option value="">Choose a question...</option>
                            <option value="pet" {{ Auth::user()->security_question === 'pet' ? 'selected' : '' }}>What was the name of your first pet?</option>
                            <option value="school" {{ Auth::user()->security_question === 'school' ? 'selected' : '' }}>What elementary school did you attend?</option>
                            <option value="city" {{ Auth::user()->security_question === 'city' ? 'selected' : '' }}>In what city were you born?</option>
                            <option value="book" {{ Auth::user()->security_question === 'book' ? 'selected' : '' }}>What is your favorite book?</option>
                            <option value="food" {{ Auth::user()->security_question === 'food' ? 'selected' : '' }}>What is your favorite food?</option>
                            <option value="mother" {{ Auth::user()->security_question === 'mother' ? 'selected' : '' }}>What is your mother's maiden name?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="security_answer_input">Your Answer</label>
                        <input type="text" id="security_answer_input" class="form-control" placeholder="Enter your answer (case-insensitive)" required>
                        <small class="text-muted">Remember this answer - you will need it to reset your password.</small>
                    </div>
                    <div class="form-group">
                        <label for="security_answer_confirm">Confirm Answer</label>
                        <input type="text" id="security_answer_confirm" class="form-control" placeholder="Confirm your answer" required>
                    </div>
                    <div id="securityQuestionError" class="error-message hidden"></div>
                    <div class="form-actions">
                        <button type="button" class="btn-primary" onclick="saveSecurityQuestion()">
                            <i class="bi bi-check-circle"></i> Save Security Question
                        </button>
                        <button type="button" class="btn-secondary" onclick="toggleModal('securityQuestionModal', 'close')">Cancel</button>
                    </div>
                </div>
            </div>

            {{-- Report Incident Modal --}}
            @if(!in_array(auth()->user()->role, ['admin', 'security']))
            <div class="modal hidden" id="reportIncidentModal">
                <div class="modal-content report-incident-modal">
                    <h2><i class="bi bi-shield-exclamation"></i> Report Security Incident</h2>
                    <form action="{{ route('incidents.report-incident') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="incident_type">
                                Incident Type *
                                <span class="info-bulb" onclick="toggleIncidentInfo()" title="Click for descriptions">
                                    <i class="bi bi-lightbulb-fill"></i>
                                </span>
                            </label>
                            <select name="type" id="incident_type" class="form-control" required onchange="updateSeverityFromType()">
                                <option value="">Select type...</option>
                                <option value="suspicious_activity" data-severity="medium">Suspicious Activity</option>
                                <option value="unauthorized_access" data-severity="high">Unauthorized Access</option>
                                <option value="policy_violation" data-severity="medium">Policy Violation</option>
                                <option value="system_error" data-severity="medium">System Error</option>
                                <option value="data_breach" data-severity="critical">Potential Data Breach</option>
                                <option value="other" data-severity="low">Other</option>
                            </select>
                            <div class="incident-info-box hidden" id="incidentInfoBox">
                                <div class="incident-info-header">
                                    <i class="bi bi-lightbulb"></i> Incident Type Descriptions
                                    <span class="close-info" onclick="toggleIncidentInfo()">&times;</span>
                                </div>
                                <ul class="incident-info-list">
                                    <li><strong>Suspicious Activity:</strong> Unusual behavior, unknown persons in restricted areas, or abnormal system activity.</li>
                                    <li><strong>Unauthorized Access:</strong> Someone accessing systems/areas without permission, or using another's credentials.</li>
                                    <li><strong>Policy Violation:</strong> Breaking company rules such as sharing passwords or bypassing security measures.</li>
                                    <li><strong>System Error:</strong> Technical issues like system crashes, data corruption, or unexpected malfunctions.</li>
                                    <li><strong>Potential Data Breach:</strong> Suspected exposure or theft of sensitive information.</li>
                                    <li><strong>Other:</strong> Any security concern not covered by the above categories.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="incident_severity">Severity Level *</label>
                            <select name="severity" id="incident_severity" class="form-control" required>
                                <option value="">Select incident type first...</option>
                                <option value="low">Low - Minor concern</option>
                                <option value="medium">Medium - Potential risk</option>
                                <option value="high">High - Significant threat</option>
                                <option value="critical">Critical - Immediate action needed</option>
                            </select>
                            <small class="severity-hint" id="severityHint"></small>
                        </div>
                        <div class="form-group">
                            <label for="incident_description">Description *</label>
                            <textarea name="description" id="incident_description" class="form-control" rows="4" placeholder="Describe what you observed..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="incident_resource">Affected Resource (optional)</label>
                            <input type="text" name="affected_resource" id="incident_resource" class="form-control" placeholder="e.g., Inventory System, Room Dashboard">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-danger"><i class="bi bi-send-fill"></i> Submit Report</button>
                            <button type="button" class="btn-secondary" onclick="toggleModal('reportIncidentModal', 'close')">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if(Auth::user()->role === 'admin')
            <div class="modal hidden" id="createEmployeeModal">
                <div class="modal-content fade-in">
                    <h2>Register Employee</h2>
                    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" style="font-family: 'system-font'; padding: 20px;">
                        @csrf
                        <h3 style="margin-bottom: 10px;">Account Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password:</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <h3 style="margin-top: 20px; margin-bottom: 10px;">Employee Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" id="first_name" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="width: 100%;">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" required style="width: 100%;">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_number">Contact Number:</label>
                                <input type="text" id="contact_number" name="contact_number" required>
                            </div>
                            <div class="form-group">
                                <label for="sss_number">SSS Number:</label>
                                <input type="text" id="sss_number" name="sss_number" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="width: 100%;">
                                <label for="profile_picture">Upload Profile Picture:</label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="width: 100%;">
                            </div>
                        </div>

                        <div class="form-actions" style="text-align: center; margin-top: 20px;">
                            <button type="submit" class="btn-primary">Register Employee</button>
                            <button type="button" class="btn-secondary" onclick="toggleModal('createEmployeeModal', 'close')">Close</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="main-content">
        {{-- Security Alert for Failed Login Attempts --}}
        @if(session('failed_login_warning'))
            <div class="security-alert" id="securityAlert" style="
                background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                border: 1px solid #f5c6cb;
                border-left: 4px solid #dc3545;
                color: #721c24;
                padding: 16px 20px;
                margin: 0 0 20px 0;
                border-radius: 8px;
                display: flex;
                align-items: flex-start;
                gap: 12px;
                box-shadow: 0 2px 8px rgba(220, 53, 69, 0.15);
            ">
                <i class="bi bi-shield-exclamation" style="font-size: 24px; color: #dc3545; flex-shrink: 0;"></i>
                <div style="flex-grow: 1;">
                    <strong style="display: block; margin-bottom: 4px;">Security Alert</strong>
                    <span>{{ session('failed_login_warning') }}</span>
                </div>
                <button onclick="dismissSecurityAlert()" style="
                    background: none;
                    border: none;
                    color: #721c24;
                    font-size: 20px;
                    cursor: pointer;
                    padding: 0;
                    line-height: 1;
                    opacity: 0.7;
                " title="Dismiss">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <script>
                function dismissSecurityAlert() {
                    document.getElementById('securityAlert').style.display = 'none';
                }
            </script>
        @endif
        
        @yield('content') 
    </div>

    <div class="footer">
        <p>&copy; 2025 TriadCo. All rights reserved.</p>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                setTimeout(() => {
                    sidebar.style.transform = 'translateX(0)'; 
                    sidebar.classList.add('sidebar-animation'); 
                }, 10); 
            } else {
                sidebar.classList.remove('sidebar-animation');
                sidebar.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    sidebar.classList.add('hidden'); 
                }, 300); 
            }
        }
    
        function toggleDropdown() {
            const dropdownMenu = document.getElementById('dropdownMenu');
            
            if (dropdownMenu.classList.contains('hidden')) {
                dropdownMenu.classList.remove('hidden');
                setTimeout(() => {
                    dropdownMenu.classList.add('dropdown-animation');
                }, 10); 
            } else {
                dropdownMenu.classList.remove('dropdown-animation');
                setTimeout(() => {
                    dropdownMenu.classList.add('hidden');
                }, 300);
            }
        }
        
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

        function toggleAssignItemsModal(roomId) {
            const modal = document.getElementById('assignItemsModal');
            const form = document.getElementById('assign-items-form');

            // Update the form action with the room ID
            form.action = `/rooms/${roomId}/assign`;

            // Remove the 'hidden' class to display the modal
            modal.classList.remove('hidden');
            modal.classList.add('show');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const itemsContainer = document.getElementById('items-container');
            let rowIndex = 1;

            // Add Row
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-add-row')) {
                e.preventDefault();

                const newRow = document.createElement('div');
                newRow.classList.add('item-row');
                newRow.innerHTML = `
                    <label for="item_id_${rowIndex}" class="assign-item-form-label">Item:</label>
                    <select name="items[${rowIndex}][item_id]" id="item_id_${rowIndex}" class="form-select item-select" required>
                        <option value="" disabled selected>Select Item</option>
                        ${[...document.querySelectorAll('#item_id_0 option')].map(option => option.outerHTML).join('')}
                    </select>
                    <label for="quantity_${rowIndex}" class="assign-item-form-label">Qty:</label>
                    <input type="number" name="items[${rowIndex}][quantity]" id="quantity_${rowIndex}" class="form-control quantity-input" min="1" placeholder="Qty" required disabled>
                    <button type="button" class="btn btn-square btn-remove-row">-</button>
                `;
                itemsContainer.appendChild(newRow);
                rowIndex++;
            }
        });

        // Remove Row
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-remove-row')) {
                e.preventDefault();
                e.target.closest('.item-row').remove();
            }
        });

            // Enable Quantity Input Based on Stock
            document.addEventListener('change', (e) => {
                if (e.target.tagName === 'SELECT' && e.target.classList.contains('form-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const stock = selectedOption.getAttribute('data-stock');
                    const quantityInput = e.target.closest('.item-row').querySelector('.quantity-input');
                    const noStockMessage = e.target.closest('.item-row').querySelector('.no-stock-message');

                    if (stock > 0) {
                        quantityInput.disabled = false;
                        quantityInput.max = stock;
                        noStockMessage.classList.add('d-none');
                    } else {
                        quantityInput.disabled = true;
                        quantityInput.value = '';
                        noStockMessage.classList.remove('d-none');
                    }
                }
            });
        });

        function openReturnItemModal(itemId, maxQuantity) {
            document.getElementById('return_item_id').value = itemId;
            document.getElementById('return_quantity').max = maxQuantity;
            toggleModal('returnItemModal', 'open');
        }

        function openStockOutModal(itemId, itemName, maxQuantity) {
            const form = document.getElementById('stockOutForm');
            form.action = `/inventory/stock-out/${itemId}`;

            document.getElementById('stock_out_item_id').value = itemId;
            document.getElementById('stock_out_item_name').value = itemName;
            const quantityInput = document.getElementById('stock_out_quantity');
            quantityInput.value = 1; 
            quantityInput.max = maxQuantity; 

            toggleModal('stockOutModal', 'open');
        }

        // Password change verification token
        let passwordVerificationToken = null;

        function verifyCurrentPassword() {
            const currentPassword = document.getElementById('current_password_verify').value;
            const errorDiv = document.getElementById('verifyError');
            
            if (!currentPassword) {
                errorDiv.textContent = 'Please enter your current password.';
                errorDiv.classList.remove('hidden');
                return;
            }

            fetch('{{ route("profile.verify-password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ current_password: currentPassword })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    passwordVerificationToken = data.token;
                    document.getElementById('passwordChangeStep1').classList.add('hidden');
                    document.getElementById('passwordChangeStep2').classList.remove('hidden');
                    errorDiv.classList.add('hidden');
                } else {
                    errorDiv.textContent = data.message || 'Verification failed.';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            });
        }

        function changePassword() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;
            const errorDiv = document.getElementById('changeError');

            if (!newPassword || !confirmPassword) {
                errorDiv.textContent = 'Please fill in all fields.';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (newPassword !== confirmPassword) {
                errorDiv.textContent = 'Passwords do not match.';
                errorDiv.classList.remove('hidden');
                return;
            }

            fetch('{{ route("profile.change-password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    verification_token: passwordVerificationToken,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password changed successfully!');
                    toggleModal('changePasswordModal', 'close');
                    // Reset the form
                    document.getElementById('current_password_verify').value = '';
                    document.getElementById('new_password').value = '';
                    document.getElementById('new_password_confirmation').value = '';
                    document.getElementById('passwordChangeStep1').classList.remove('hidden');
                    document.getElementById('passwordChangeStep2').classList.add('hidden');
                    passwordVerificationToken = null;
                } else {
                    errorDiv.textContent = data.message || 'Password change failed.';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            });
        }

        function saveSecurityQuestion() {
            const question = document.getElementById('security_question_select').value;
            const answer = document.getElementById('security_answer_input').value.trim();
            const confirmAnswer = document.getElementById('security_answer_confirm').value.trim();
            const errorDiv = document.getElementById('securityQuestionError');

            if (!question) {
                errorDiv.textContent = 'Please select a security question.';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (!answer || answer.length < 2) {
                errorDiv.textContent = 'Please enter an answer with at least 2 characters.';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (answer.toLowerCase() !== confirmAnswer.toLowerCase()) {
                errorDiv.textContent = 'Answers do not match.';
                errorDiv.classList.remove('hidden');
                return;
            }

            fetch('{{ route("profile.security-question") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    security_question: question,
                    security_answer: answer
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Security question saved successfully!');
                    toggleModal('securityQuestionModal', 'close');
                    // Reset the form
                    document.getElementById('security_answer_input').value = '';
                    document.getElementById('security_answer_confirm').value = '';
                    errorDiv.classList.add('hidden');
                    // Reload page to update UI
                    location.reload();
                } else {
                    errorDiv.textContent = data.message || 'Failed to save security question.';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            });
        }

        // Incident Type and Severity Auto-selection
        function updateSeverityFromType() {
            const typeSelect = document.getElementById('incident_type');
            const severitySelect = document.getElementById('incident_severity');
            const severityHint = document.getElementById('severityHint');
            const selectedOption = typeSelect.options[typeSelect.selectedIndex];
            
            if (selectedOption && selectedOption.dataset.severity) {
                const suggestedSeverity = selectedOption.dataset.severity;
                severitySelect.value = suggestedSeverity;
                
                const severityLabels = {
                    'low': 'Low - Minor concern',
                    'medium': 'Medium - Potential risk',
                    'high': 'High - Significant threat',
                    'critical': 'Critical - Immediate action needed'
                };
                
                severityHint.innerHTML = '<i class="bi bi-info-circle"></i> Auto-selected based on incident type. You can change if needed.';
                severityHint.classList.add('show');
            } else {
                severitySelect.value = '';
                severityHint.classList.remove('show');
            }
        }

        function toggleIncidentInfo() {
            const infoBox = document.getElementById('incidentInfoBox');
            infoBox.classList.toggle('hidden');
        }

        // Global Notification Modal Functions
        function showNotificationModal(type, message, title = null) {
            const modal = document.getElementById('globalNotificationModal');
            const icon = document.getElementById('notificationIcon');
            const titleEl = document.getElementById('notificationTitle');
            const messageEl = document.getElementById('notificationMessage');
            const iconContainer = document.getElementById('notificationIconContainer');
            
            // Set icon and colors based on type
            const config = {
                success: { icon: 'bi-check-circle-fill', title: 'Success', color: '#28a745', bgColor: 'rgba(40, 167, 69, 0.1)' },
                error: { icon: 'bi-x-circle-fill', title: 'Error', color: '#dc3545', bgColor: 'rgba(220, 53, 69, 0.1)' },
                warning: { icon: 'bi-exclamation-triangle-fill', title: 'Warning', color: '#ffc107', bgColor: 'rgba(255, 193, 7, 0.15)' },
                info: { icon: 'bi-info-circle-fill', title: 'Information', color: '#17a2b8', bgColor: 'rgba(23, 162, 184, 0.1)' }
            };
            
            const cfg = config[type] || config.info;
            
            icon.className = 'bi ' + cfg.icon;
            icon.style.color = cfg.color;
            iconContainer.style.background = cfg.bgColor;
            titleEl.textContent = title || cfg.title;
            titleEl.style.color = cfg.color;
            messageEl.textContent = message;
            
            modal.classList.add('active');
            
            // Auto-close after 4 seconds for success messages
            if (type === 'success') {
                setTimeout(() => closeNotificationModal(), 4000);
            }
        }

        function closeNotificationModal() {
            const modal = document.getElementById('globalNotificationModal');
            modal.classList.remove('active');
        }

        // Show session messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showNotificationModal('success', @json(session('success')));
            @endif
            @if(session('error'))
                showNotificationModal('error', @json(session('error')));
            @endif
            @if(session('warning'))
                showNotificationModal('warning', @json(session('warning')));
            @endif
            @if(session('info'))
                showNotificationModal('info', @json(session('info')));
            @endif
        });
        
    </script>

    {{-- Global Notification Modal --}}
    <div class="notification-modal" id="globalNotificationModal">
        <div class="notification-modal-content">
            <div class="notification-icon-container" id="notificationIconContainer">
                <i class="bi bi-check-circle-fill" id="notificationIcon"></i>
            </div>
            <h3 class="notification-title" id="notificationTitle">Success</h3>
            <p class="notification-message" id="notificationMessage"></p>
            <button class="notification-btn" onclick="closeNotificationModal()">
                <i class="bi bi-check-lg"></i> OK
            </button>
        </div>
    </div>
</body>

</html>