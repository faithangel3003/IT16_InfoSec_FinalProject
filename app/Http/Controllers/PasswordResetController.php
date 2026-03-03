<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('login.forgot-password');
    }

    /**
     * Process forgot password request - Step 1: Verify email
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        // Check if there's a recent unexpired request
        $existingRequest = PasswordResetRequest::where('email', $request->email)
            ->where('expires_at', '>', now())
            ->where('verified', false)
            ->first();

        if ($existingRequest && $existingRequest->hasTooManyAttempts()) {
            return back()->withErrors(['email' => 'Too many attempts. Please try again later.']);
        }

        // Check if user has a security question set up
        if (!$user->hasSecurityQuestion()) {
            return back()->withErrors(['email' => 'You have not set up a security question. Please contact an administrator to reset your password.']);
        }

        // Use the user's stored security question
        $questions = PasswordResetRequest::getSecurityQuestions();
        $questionKey = $user->security_question;
        
        // Create new request
        $resetRequest = PasswordResetRequest::create([
            'email' => $request->email,
            'token' => PasswordResetRequest::generateToken(),
            'security_question' => $questionKey,
            'security_answer_hash' => $user->security_answer_hash, // Store the user's answer hash for verification
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        SystemLog::log(
            'password_reset.requested',
            'Password reset requested for: ' . $request->email,
            'security',
            'info',
            null,
            null,
            $request->ip()
        );

        return view('login.verify-security', [
            'token' => $resetRequest->token,
            'email' => $request->email,
            'question' => $questions[$questionKey] ?? 'Security Question',
            'questionKey' => $questionKey,
        ]);
    }

    /**
     * Verify security answer - Step 2
     */
    public function verifySecurityAnswer(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'security_answer' => 'required|string|min:2',
        ]);

        $resetRequest = PasswordResetRequest::where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$resetRequest) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Invalid reset request. Please try again.']);
        }

        if ($resetRequest->isExpired()) {
            $resetRequest->delete();
            return redirect()->route('password.forgot')->withErrors(['email' => 'Reset request has expired. Please try again.']);
        }

        if ($resetRequest->hasTooManyAttempts()) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Too many failed attempts. Please try again later.']);
        }

        // Get the user to verify the security answer
        $user = User::where('email', $request->email)->first();
        
        if (!$user || !$user->hasSecurityQuestion()) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Unable to verify security answer. Please contact an administrator.']);
        }

        // Verify the answer against the user's stored security answer
        if (!$user->verifySecurityAnswer($request->security_answer)) {
            $resetRequest->incrementAttempts();
            return back()->withErrors(['security_answer' => 'Incorrect answer. Please try again.'])->withInput(['token' => $request->token, 'email' => $request->email]);
        }

        // Mark as verified
        $resetRequest->markVerified();

        SystemLog::log(
            'password_reset.verified',
            'Password reset security verified for: ' . $request->email,
            'security',
            'info',
            null,
            null,
            $request->ip()
        );

        return view('login.reset-password', [
            'token' => $resetRequest->token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset password - Step 3
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase, one lowercase, one number, and one special character.',
        ]);

        $resetRequest = PasswordResetRequest::where('token', $request->token)
            ->where('email', $request->email)
            ->where('verified', true)
            ->first();

        if (!$resetRequest) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Invalid or unverified reset request.']);
        }

        if ($resetRequest->isExpired()) {
            $resetRequest->delete();
            return redirect()->route('password.forgot')->withErrors(['email' => 'Reset request has expired.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'User not found.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        // Delete the reset request
        $resetRequest->delete();

        // Clear any other pending requests for this email
        PasswordResetRequest::where('email', $request->email)->delete();

        SystemLog::log(
            'password_reset.completed',
            'Password reset completed for: ' . $request->email,
            'security',
            'info',
            $user->id,
            $user->name,
            $request->ip()
        );

        return redirect()->route('login')->with('success', 'Password has been reset successfully. Please login with your new password.');
    }

    /**
     * API endpoint for checking email exists
     */
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $exists = User::where('email', $request->email)->exists();
        
        return response()->json(['exists' => $exists]);
    }
}
