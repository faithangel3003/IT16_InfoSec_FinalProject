<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Identity - TriadCo</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .verify-card {
            display: flex;
            width: 450px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease, slideUp 0.8s ease;
        }
        
        .verify-content {
            flex: 1;
            background-color: #203354;
            color: white;
            padding: 35px 30px;
            display: flex;
            flex-direction: column;
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .verify-header i {
            font-size: 48px;
            color: #c8a858;
            margin-bottom: 10px;
        }
        
        .verify-header h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .verify-header p {
            font-size: 12px;
            color: #94a3b8;
            margin: 8px 0 0 0;
        }
        
        .security-question-box {
            background: rgba(200, 168, 88, 0.15);
            border: 1px solid rgba(200, 168, 88, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .security-question-box label {
            font-size: 11px;
            color: #c8a858;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .security-question-box p {
            font-size: 14px;
            color: white;
            margin: 8px 0 0 0;
            font-weight: 500;
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
        
        .info-badge {
            background: rgba(23, 162, 184, 0.15);
            color: #5bc0de;
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
        <div class="verify-card">
            <div class="verify-content">
                <div class="verify-header">
                    <i class="bi bi-shield-lock-fill"></i>
                    <h2>Verify Your Identity</h2>
                    <p>Please answer the security question</p>
                </div>

                <div class="info-badge">
                    <i class="bi bi-envelope-fill"></i> Verifying: {{ $email }}
                </div>

                @if($errors->any())
                    <div class="error-msg">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('password.verify-security') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="security-question-box">
                        <label>Security Question</label>
                        <p>{{ $question }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="security_answer">Your Answer</label>
                        <input type="text" id="security_answer" name="security_answer" placeholder="Enter your answer" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-circle"></i> Verify & Continue
                    </button>
                </form>

                <div class="back-link">
                    <a href="{{ route('password.forgot') }}"><i class="bi bi-arrow-left"></i> Start Over</a>
                </div>
            </div>
        </div>
        <div class="bottom-logo">
            <img src="{{ asset('images/TriadCoLogo.png') }}" alt="TriadCo Logo">
        </div>
    </div>
</body>
</html>
