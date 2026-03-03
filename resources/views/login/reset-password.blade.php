<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - TriadCo</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .reset-card {
            display: flex;
            width: 450px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease, slideUp 0.8s ease;
        }
        
        .reset-content {
            flex: 1;
            background-color: #203354;
            color: white;
            padding: 35px 30px;
            display: flex;
            flex-direction: column;
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .reset-header i {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .reset-header h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .reset-header p {
            font-size: 12px;
            color: #94a3b8;
            margin: 8px 0 0 0;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            font-size: 12px;
            margin-bottom: 6px;
            color: #cbd5e1;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            background: #e8ecf0;
            color: #1b2e41;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
        }
        
        .password-requirements {
            background: rgba(100, 116, 139, 0.2);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 11px;
            color: #94a3b8;
        }
        
        .password-requirements ul {
            margin: 8px 0 0 0;
            padding-left: 16px;
        }
        
        .password-requirements li {
            margin-bottom: 4px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        
        .error-msg {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ffb3b3;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        
        .success-badge {
            background: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #a3e4b5;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="reset-card">
            <div class="reset-content">
                <div class="reset-header">
                    <i class="bi bi-check-circle-fill"></i>
                    <h2>Create New Password</h2>
                    <p>Your identity has been verified</p>
                </div>

                <div class="success-badge">
                    <i class="bi bi-shield-fill-check"></i> Identity Verified for {{ $email }}
                </div>

                @if($errors->any())
                    <div class="error-msg">
                        <ul style="margin: 0; padding-left: 16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.reset') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="password-requirements">
                        <strong>Password Requirements:</strong>
                        <ul>
                            <li>Minimum 8 characters</li>
                            <li>At least one uppercase letter (A-Z)</li>
                            <li>At least one lowercase letter (a-z)</li>
                            <li>At least one number (0-9)</li>
                            <li>At least one special character (@$!%*?&)</li>
                        </ul>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter new password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-lg"></i> Reset Password
                    </button>
                </form>
            </div>
        </div>
        <div class="bottom-logo">
            <img src="{{ asset('images/TriadCoLogo.png') }}" alt="TriadCo Logo">
        </div>
    </div>
</body>
</html>
