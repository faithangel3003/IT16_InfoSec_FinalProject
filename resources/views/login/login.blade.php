<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TriadCo. Hotel Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .captcha-container {
            margin: 5px 0;
            padding: 6px 10px;
            background: rgba(100, 116, 139, 0.25);
            border-radius: 8px;
        }
        .captcha-question {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }
        .captcha-label {
            color: #cbd5e1;
            font-size: 11px;
            font-weight: 500;
        }
        .captcha-math {
            background: rgba(200, 168, 88, 0.2);
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            letter-spacing: 2px;
        }
        .captcha-refresh {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 14px;
            padding: 3px;
            transition: transform 0.3s, color 0.3s;
        }
        .captcha-refresh:hover {
            transform: rotate(180deg);
            color: #c8a858;
        }
        .captcha-input input {
            width: 100%;
            padding: 5px 10px;
            border: none;
            border-radius: 10px;
            font-size: 12px;
            background: #e8ecf0;
            color: #1b2e41;
        }
        .captcha-input input:focus {
            box-shadow: 0 0 6px rgba(32, 51, 84, 0.4);
            outline: none;
        }
        .captcha-input input::placeholder {
            color: #64748b;
            font-size: 11px;
        }
        .captcha-input input[type="number"]::-webkit-outer-spin-button,
        .captcha-input input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .captcha-input input[type="number"] {
            -moz-appearance: textfield;
        }
        /* Forgot password link */
        .forgot-password-link {
            text-align: center;
            margin-top: 12px;
        }
        .forgot-password-link a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.3s;
        }
        .forgot-password-link a:hover {
            color: #c8a858;
        }
        /* Compact form layout */
        .login-right {
            padding: 25px 30px !important;
        }
        .login-right h2 {
            margin-bottom: 12px !important;
            font-size: 18px !important;
        }
        .login-right label {
            margin-bottom: 3px !important;
        }
        .login-right input[type="text"],
        .login-right input[type="password"] {
            margin-bottom: 10px !important;
            padding: 6px 10px !important;
        }
        .login-right button[type="submit"] {
            margin-top: 5px;
            padding: 8px !important;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-left">
                <img src="{{ asset('images/TriadEmb.png') }}" alt="Hotel" />
            </div>
            <div class="login-right">
                <h2>TriadCo.<br>Hotel Set-Up</h2>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf 

                    <label for="name">Username</label>
                    <input type="text" id="name" name="name" placeholder="Enter username" value="{{ old('name') }}" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>

                    @php
                        $captcha = \App\Http\Controllers\CaptchaController::generate();
                    @endphp
                    <div class="captcha-container">
                        <div class="captcha-question">
                            <span class="captcha-label">Security Check:</span>
                            <span class="captcha-math" id="captcha-question">{{ $captcha['question'] }}</span>
                            <button type="button" class="captcha-refresh" onclick="refreshCaptcha()" title="Get new question">&#x21bb;</button>
                        </div>
                        <div class="captcha-input">
                            <input type="number" id="captcha_answer" name="captcha_answer" placeholder="Enter your answer" required>
                        </div>
                    </div>

                    <button type="submit">Log In</button>
                </form>

                <div class="forgot-password-link">
                    <a href="{{ route('password.forgot') }}">Forgot Password?</a>
                </div>

                @if ($errors->any())
                    <div style="color: #ffb3b3; font-size: 13px; margin-top: 10px;">
                        <ul style="padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <div class="bottom-logo">
            <img src="{{ asset('images/TriadCoLogo.png') }}" alt="TriadCo Logo">
        </div>
    </div>

    <script>
        function refreshCaptcha() {
            fetch('/captcha/refresh', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('captcha-question').textContent = data.question;
                document.getElementById('captcha_answer').value = '';
                document.getElementById('captcha_answer').focus();
            })
            .catch(error => {
                console.error('Error refreshing captcha:', error);
            });
        }
    </script>
</body>
</html>