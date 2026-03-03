<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CaptchaController;
use App\Models\LoginLog;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin') {
                return redirect()->route('dashboard'); 
            } elseif (Auth::user()->role === 'inventory_manager') {
                return redirect()->route('inventory.dashboard');
            } elseif (Auth::user()->role === 'room_manager') {
                return redirect()->route('room.dashboard');
            } elseif (Auth::user()->role === 'security') {
                return redirect()->route('security.dashboard');
            } elseif (Auth::user()->role === 'employee') {
                return redirect()->route('inventory.index'); 
            }
        }
        return view('login.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|max:128',
            'captcha_answer' => 'required|numeric|max:999999',
        ]);

        // Verify CAPTCHA first
        if (!CaptchaController::verify($request->captcha_answer)) {
            return back()->withErrors([
                'captcha_answer' => 'Incorrect answer. Please solve the math problem correctly.',
            ])->withInput($request->only('name'));
        }

        // Set credentials for authentication (exclude captcha_answer)
        $credentials = [
            'name' => $request->name,
            'password' => $request->password,
        ];

        // Find user by name first to check lockout status
        $user = User::where('name', $credentials['name'])->first();

        // Check if user exists and is locked
        if ($user && $user->isLocked()) {
            $remainingMinutes = $user->getRemainingLockoutMinutes();
            
            // Log the lockout attempt
            LoginLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed',
                'failure_reason' => 'Account locked',
                'login_at' => now(),
            ]);

            SystemLog::log(
                'user.login_locked',
                'Login attempt on locked account: ' . $user->name . ' (locked for ' . $remainingMinutes . ' more minutes)',
                'security',
                'warning',
                $user->id,
                $user->name,
                $request->ip()
            );

            return back()->withErrors([
                'name' => 'Your account is locked due to too many failed login attempts. Please try again in ' . $remainingMinutes . ' minute(s).',
            ])->withInput();
        }

        if (Auth::attempt($credentials)) {
            // Check if user account is active
            if (Auth::user()->status !== 'active') {
                // Log the inactive account attempt
                LoginLog::create([
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'failed',
                    'failure_reason' => 'Account inactive',
                    'login_at' => now(),
                ]);

                SystemLog::log(
                    'user.login_blocked',
                    'Login blocked for inactive account: ' . Auth::user()->name,
                    'security',
                    'warning',
                    Auth::id(),
                    Auth::user()->name,
                    $request->ip()
                );

                Auth::logout();
                return back()->withErrors([
                    'name' => 'Your account has been deactivated. Please contact the administrator.',
                ])->withInput();
            }

            $request->session()->regenerate();
            
            // Check for failed login attempts notification
            $authUser = Auth::user();
            $failedAttempts = $authUser->failed_login_attempts;
            $lastFailedAt = $authUser->last_failed_login_at;
            $hasUnnotifiedAttempts = $authUser->has_unnotified_failed_attempts;
            
            // Store failed attempts info in session for notification
            if ($hasUnnotifiedAttempts && $failedAttempts > 0) {
                session([
                    'failed_login_notification' => [
                        'attempts' => $failedAttempts,
                        'last_attempt_at' => $lastFailedAt ? Carbon::parse($lastFailedAt)->format('M d, Y h:i A') : null,
                    ]
                ]);
                $authUser->markFailedAttemptsNotified();
            }
            
            // Reset failed login attempts
            $authUser->resetFailedAttempts();
            $authUser->updateLastLogin($request->ip());
            
            session(['first_login' => true]);
            
            // Log successful login
            $loginLog = LoginLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
                'login_at' => now(),
            ]);
            
            // Store login log ID in session for logout tracking
            session(['login_log_id' => $loginLog->id]);

            // Log to System Logs
            SystemLog::log(
                'user.login',
                'User ' . Auth::user()->name . ' logged in successfully',
                'audit',
                'info',
                Auth::id(),
                Auth::user()->name,
                $request->ip()
            );
            
            if (in_array(Auth::user()->role, ['admin', 'super_admin'])) {
                return redirect()->route('dashboard');
            } elseif (Auth::user()->role === 'inventory_manager') {
                return redirect()->route('inventory.dashboard');
            } elseif (Auth::user()->role === 'room_manager') {
                return redirect()->route('room.dashboard');
            } elseif (Auth::user()->role === 'security') {
                return redirect()->route('security.dashboard');
            } elseif (Auth::user()->role === 'employee') {
                return redirect()->route('inventory.index');
            }
        }

        // Handle failed login attempt
        if ($user) {
            $user->incrementFailedAttempts();
            
            // Check if account just got locked
            if ($user->isLocked()) {
                SystemLog::log(
                    'user.account_locked',
                    'Account locked after ' . User::MAX_FAILED_ATTEMPTS . ' failed attempts: ' . $user->name,
                    'security',
                    'critical',
                    $user->id,
                    $user->name,
                    $request->ip()
                );
            }
        }

        // Log failed login attempt
        LoginLog::create([
            'user_id' => $user?->id,
            'user_name' => $request->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed',
            'failure_reason' => 'Invalid credentials',
            'login_at' => now(),
        ]);

        // Log failed attempt to System Logs
        SystemLog::log(
            'user.login_failed',
            'Failed login attempt for user: ' . $request->name . ($user ? ' (attempt ' . $user->failed_login_attempts . '/' . User::MAX_FAILED_ATTEMPTS . ')' : ' (unknown user)'),
            'security',
            'warning',
            $user?->id,
            $request->name,
            $request->ip()
        );

        // Build error message with remaining attempts info
        $errorMessage = 'The provided credentials do not match our records.';
        if ($user) {
            $remainingAttempts = User::MAX_FAILED_ATTEMPTS - $user->failed_login_attempts;
            if ($remainingAttempts > 0 && $remainingAttempts <= 3) {
                $errorMessage .= ' Warning: ' . $remainingAttempts . ' attempt(s) remaining before account lockout.';
            }
        }

        return back()->withErrors([
            'name' => $errorMessage,
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $userName = Auth::user()->name ?? 'Unknown';
        $userId = Auth::id();
        
        // Update login log with logout time and session duration
        $loginLogId = session('login_log_id');
        if ($loginLogId) {
            $loginLog = LoginLog::find($loginLogId);
            if ($loginLog) {
                $loginLog->update([
                    'logout_at' => now(),
                    'session_duration' => now()->diffInSeconds($loginLog->login_at),
                ]);
            }
        }

        // Log to System Logs before logout
        SystemLog::log(
            'user.logout',
            'User ' . $userName . ' logged out',
            'audit',
            'info',
            $userId,
            $userName,
            $request->ip()
        );
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    
    public function viewProfile()
    {
        $user = Auth::user();
        return view('profile.view', compact('user'));
    }

    /**
     * Verify current password for password change
     */
    public function verifyCurrentPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 400);
        }

        // Generate a verification token
        $token = bin2hex(random_bytes(32));
        session(['password_change_token' => $token, 'password_change_verified_at' => now()]);

        SystemLog::log(
            'password.verification_success',
            'Password verification successful for password change',
            'security',
            'info',
            $user->id,
            $user->name,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Password verified successfully.'
        ]);
    }

    /**
     * Change password with strict authentication
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'verification_token' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase, one lowercase, one number, and one special character.',
            'new_password.confirmed' => 'Password confirmation does not match.',
        ]);

        // Verify the token
        $storedToken = session('password_change_token');
        $verifiedAt = session('password_change_verified_at');

        if (!$storedToken || $storedToken !== $request->verification_token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification token. Please verify your password again.'
            ], 400);
        }

        // Check if verification is still valid (5 minutes)
        if (!$verifiedAt || now()->diffInMinutes($verifiedAt) > 5) {
            session()->forget(['password_change_token', 'password_change_verified_at']);
            return response()->json([
                'success' => false,
                'message' => 'Verification expired. Please verify your password again.'
            ], 400);
        }

        $user = Auth::user();

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
            'password_changed_at' => now(),
        ]);

        // Clear the verification token
        session()->forget(['password_change_token', 'password_change_verified_at']);

        SystemLog::log(
            'password.changed',
            'Password changed successfully',
            'security',
            'info',
            $user->id,
            $user->name,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    /**
     * Set or update security question
     */
    public function setSecurityQuestion(Request $request)
    {
        $request->validate([
            'security_question' => 'required|string|in:pet,school,city,book,food,mother',
            'security_answer' => 'required|string|min:2|max:255',
        ]);

        $user = Auth::user();
        
        // Set the security question using the model method
        $user->setSecurityQuestion($request->security_question, $request->security_answer);

        SystemLog::log(
            'security_question.updated',
            'Security question ' . ($user->security_question ? 'updated' : 'set') . ' for user',
            'security',
            'info',
            $user->id,
            $user->name,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Security question saved successfully.'
        ]);
    }
}