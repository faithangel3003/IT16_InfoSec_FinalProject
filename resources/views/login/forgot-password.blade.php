<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - TriadCo</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .forgot-card {
            display: flex;
            width: 450px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease, slideUp 0.8s ease;
        }
        
        .forgot-content {
            flex: 1;
            background-color: #203354;
            color: white;
            padding: 35px 30px;
            display: flex;
            flex-direction: column;
        }
        
        .forgot-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .forgot-header i {
            font-size: 48px;
            color: #c8a858;
            margin-bottom: 10px;
        }
        
        .forgot-header h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .forgot-header p {
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
            box-shadow: 0 0 0 3px rgba(200, 168, 88, 0.3);
        }
        
        .form-group input::placeholder {
            color: #64748b;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #c8a858 0%, #e0c078 100%);
            color: #1b2e41;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(200, 168, 88, 0.4);
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s;
        }
        
        .back-link a:hover {
            color: #c8a858;
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
        
        .success-msg {
            background: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #a3e4b5;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="forgot-card">
            <div class="forgot-content">
                <div class="forgot-header">
                    <i class="bi bi-key-fill"></i>
                    <h2>Forgot Password</h2>
                    <p>Enter your email to begin password recovery</p>
                </div>

                @if($errors->any())
                    <div class="error-msg">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('password.verify-email') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your registered email" value="{{ old('email') }}" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-arrow-right-circle"></i> Continue
                    </button>
                </form>

                <div class="back-link">
                    <a href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Back to Login</a>
                </div>
            </div>
        </div>
        <div class="bottom-logo">
            <img src="{{ asset('images/TriadCoLogo.png') }}" alt="TriadCo Logo">
        </div>
    </div>
</body>
</html>
