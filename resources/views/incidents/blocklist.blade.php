@extends('dashboard')

@section('title', 'IP Blocklist - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
    <style>
        .blocklist-page { padding: 20px 30px; }
        
        .blocklist-title {
            color: #1e2a47;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .blocklist-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .btn-back {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        
        .btn-back:hover {
            background: #c8a858;
            color: #fff;
            border-color: #c8a858;
        }
        
        .blocklist-content {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
        }
        
        /* Blocked IPs Table */
        .blocklist-table-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .blocklist-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .blocklist-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .blocklist-table th {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .blocklist-table td {
            padding: 12px 16px;
            color: #495057;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.85rem;
        }
        
        .blocklist-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .ip-address {
            font-family: 'Courier New', monospace;
            background: rgba(200, 168, 88, 0.1);
            padding: 4px 8px;
            border-radius: 5px;
            color: #a08038;
        }
        
        .duration-badge {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        
        .duration-badge.permanent {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .btn-unblock {
            background: transparent;
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-unblock:hover {
            background: #dc3545;
            color: #fff;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
            color: #adb5bd;
        }
        
        /* Block IP Form */
        .block-form-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(30, 42, 71, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }
        
        .block-form-card h4 {
            color: #1e2a47;
            margin: 0 0 16px 0;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            color: #6c757d;
            font-size: 0.65rem;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            background: #fff;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #c8a858;
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.2);
            outline: none;
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #adb5bd;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 70px;
        }
        
        .btn-block-ip {
            width: 100%;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        
        .btn-block-ip:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        
        @media (max-width: 992px) {
            .blocklist-content {
                grid-template-columns: 1fr;
            }
            
            .block-form-card {
                order: -1;
            }
        }
    </style>
@endsection

@section('content')
<div class="blocklist-page">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="blocklist-title">IP Blocklist</h1>
            <p class="blocklist-subtitle">Manage blocked IP addresses</p>
        </div>
        <a href="{{ route('incidents.index') }}" class="btn-back">Back to Incidents</a>
    </div>

    @if($errors->any())
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

    <div class="blocklist-content">
        <!-- Blocked IPs Table -->
        <div class="blocklist-table-card">
            <table class="blocklist-table">
                <thead>
                    <tr>
                        <th>IP ADDRESS</th>
                        <th>BLOCKED AT</th>
                        <th>EXPIRES AT</th>
                        <th>REASON</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blockedIps as $block)
                        <tr>
                            <td><span class="ip-address">{{ $block->ip_address }}</span></td>
                            <td>{{ $block->blocked_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($block->duration === 'permanent')
                                    <span class="duration-badge permanent">Permanent</span>
                                @else
                                    {{ $block->expires_at ? $block->expires_at->format('M d, Y H:i') : 'N/A' }}
                                @endif
                            </td>
                            <td>{{ $block->reason ?? '-' }}</td>
                            <td>
                                <form action="{{ route('incidents.unblock', $block->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-unblock" onclick="return confirm('Unblock this IP address?')">
                                        <i class="bi bi-unlock"></i> Unblock
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-shield-check"></i>
                                    <p>No IP addresses are currently blocked</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $blockedIps->links() }}
            </div>
        </div>

        <!-- Block IP Form -->
        <div class="block-form-card">
            <h4><i class="bi bi-shield-x me-2"></i>Block IP Address</h4>
            <form action="{{ route('incidents.block') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>IP ADDRESS</label>
                    <input type="text" name="ip_address" placeholder="e.g., 192.168.1.100" required>
                </div>
                <div class="form-group">
                    <label>DURATION</label>
                    <select name="duration">
                        <option value="24_hours">24 hours</option>
                        <option value="7_days">7 days</option>
                        <option value="30_days">30 days</option>
                        <option value="permanent">Permanent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>REASON</label>
                    <textarea name="reason" placeholder="Reason for blocking this IP"></textarea>
                </div>
                <button type="submit" class="btn-block-ip">Block IP</button>
            </form>
        </div>
    </div>
</div>
@endsection
