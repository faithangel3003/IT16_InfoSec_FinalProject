<?php

namespace App\Http\Controllers;

use App\Models\CredentialVerification;
use App\Models\DataUnmaskLog;
use App\Models\Employee;
use App\Models\LoginLog;
use App\Models\Supplier;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $userId = $request->get('user_id');
        $status = $request->get('status');
        $perPage = $request->get('per_page', 10);

        // Build query
        $query = LoginLog::with('user')
            ->whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $loginLogs = $query->orderBy('login_at', 'desc')->paginate($perPage);

        // Calculate analytics
        $totalLogins = LoginLog::whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count();
        $successfulLogins = LoginLog::whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('status', 'success')->count();
        $failedLogins = LoginLog::whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('status', 'failed')->count();

        // Get unique active users in period
        $activeUsers = LoginLog::whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('status', 'success')
            ->distinct('user_id')
            ->count('user_id');

        // Calculate average session duration
        $avgDuration = LoginLog::whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('status', 'success')
            ->whereNotNull('session_duration')
            ->avg('session_duration');

        // User login count analytics
        $userLoginCounts = LoginLog::selectRaw('user_id, user_name, COUNT(*) as login_count, 
            SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful_logins,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_logins')
            ->whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('user_id', 'user_name')
            ->orderByDesc('login_count')
            ->get();

        // Recent failed login attempts
        $recentFailedAttempts = LoginLog::where('status', 'failed')
            ->orderBy('login_at', 'desc')
            ->take(10)
            ->get();

        // Get all users for filter dropdown
        $users = User::orderBy('name')->get();

        // === ENHANCED SECURITY DATA ===
        
        // IP Blocklist - Active blocked IPs
        $blockedIps = \App\Models\IpBlocklist::where('is_active', true)
            ->orderBy('blocked_at', 'desc')
            ->take(10)
            ->get();
        $totalBlockedIps = \App\Models\IpBlocklist::where('is_active', true)->count();
        
        // System Logs - Recent security-related logs
        $recentSystemLogs = SystemLog::whereIn('channel', ['security', 'audit'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        $securityLogCount = SystemLog::where('channel', 'security')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        $criticalLogCount = SystemLog::whereIn('level', ['critical', 'error'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        // Data Unmask Logs - Sensitive data access
        $recentUnmaskLogs = DataUnmaskLog::with('user')
            ->orderBy('unmasked_at', 'desc')
            ->take(10)
            ->get();
        $unmaskLogCount = DataUnmaskLog::whereBetween('unmasked_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        // Login trends (last 7 days)
        $loginTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $loginTrends[] = [
                'date' => $date->format('M d'),
                'success' => LoginLog::whereDate('login_at', $date->toDateString())
                    ->where('status', 'success')->count(),
                'failed' => LoginLog::whereDate('login_at', $date->toDateString())
                    ->where('status', 'failed')->count(),
            ];
        }
        
        // User role breakdown
        $usersByRole = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
        
        // Peak login hours (last 7 days)
        $peakHours = LoginLog::selectRaw('HOUR(login_at) as hour, COUNT(*) as count')
            ->where('login_at', '>=', Carbon::now()->subDays(7))
            ->where('status', 'success')
            ->groupBy('hour')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        return view('security.index', compact(
            'loginLogs',
            'totalLogins',
            'successfulLogins',
            'failedLogins',
            'activeUsers',
            'avgDuration',
            'userLoginCounts',
            'recentFailedAttempts',
            'users',
            'dateFrom',
            'dateTo',
            'userId',
            'status',
            'perPage',
            // Enhanced data
            'blockedIps',
            'totalBlockedIps',
            'recentSystemLogs',
            'securityLogCount',
            'criticalLogCount',
            'recentUnmaskLogs',
            'unmaskLogCount',
            'loginTrends',
            'usersByRole',
            'peakHours'
        ));
    }

    /**
     * Format seconds to human readable duration
     */
    private function formatDuration($seconds)
    {
        if (!$seconds) return 'N/A';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }
        return sprintf('%ds', $secs);
    }

    /**
     * Verify user credentials for sensitive actions
     */
    public function verifyCredentials(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'action' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        
        if (!Hash::check($request->password, $user->password)) {
            // Log failed verification attempt
            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'credential_verification_failed',
                'message' => "Failed credential verification for action: {$request->action}",
                'ip_address' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please try again.'
            ], 401);
        }

        // Create verification record
        $verification = CredentialVerification::createForAction($user->id, $request->action);

        // Log successful verification
        SystemLog::create([
            'user_id' => $user->id,
            'action' => 'credential_verification_success',
            'message' => "Credential verified for action: {$request->action}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'verification_token' => $verification->token,
            'expires_in' => 5 * 60, // 5 minutes in seconds
            'message' => 'Credentials verified successfully.'
        ]);
    }

    /**
     * Check if user has valid verification for an action
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'action' => 'required|string|max:100',
        ]);

        $hasValid = CredentialVerification::hasValidVerification(
            Auth::id(),
            $request->action
        );

        return response()->json([
            'has_valid_verification' => $hasValid
        ]);
    }

    /**
     * Unmask sensitive data after credential verification
     */
    public function unmaskData(Request $request)
    {
        $request->validate([
            'verification_token' => 'required|string',
            'data_type' => 'required|string|in:phone,email,sss,address,tin,philhealth,pagibig',
            'record_type' => 'required|string|in:employee,supplier,user',
            'record_id' => 'required|integer',
        ]);

        $user = Auth::user();
        
        // Verify token
        $verification = CredentialVerification::where('token', $request->verification_token)
            ->where('user_id', $user->id)
            ->where('verified', true)
            ->first();

        if (!$verification || !$verification->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification. Please verify your credentials again.'
            ], 401);
        }

        // Get the actual data based on record type
        $data = null;
        $model = null;
        
        // Role-based access control for unmasking data
        $userRole = $user->role;
        $allowedRecordTypes = [];
        
        switch ($userRole) {
            case 'admin':
            case 'security':
                // Admin and security can unmask all data types
                $allowedRecordTypes = ['employee', 'supplier', 'user'];
                break;
            case 'inventory_manager':
                // Inventory manager can only unmask supplier data
                $allowedRecordTypes = ['supplier'];
                break;
            default:
                // Other roles cannot unmask any data
                $allowedRecordTypes = [];
                break;
        }
        
        if (!in_array($request->record_type, $allowedRecordTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to unmask this type of data.'
            ], 403);
        }
        
        switch ($request->record_type) {
            case 'employee':
                $model = Employee::find($request->record_id);
                if ($model) {
                    $data = $this->getEmployeeField($model, $request->data_type);
                }
                break;
            case 'supplier':
                $model = Supplier::find($request->record_id);
                if ($model) {
                    $data = $this->getSupplierField($model, $request->data_type);
                }
                break;
            case 'user':
                $model = User::find($request->record_id);
                if ($model) {
                    $data = $this->getUserField($model, $request->data_type);
                }
                break;
        }

        if (!$model || $data === null) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.'
            ], 404);
        }

        // Log the unmask action
        DataUnmaskLog::logUnmask(
            $user->id,
            $request->record_type,
            $request->record_id,
            $request->data_type
        );

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get employee field value
     */
    private function getEmployeeField($employee, $field)
    {
        $mapping = [
            'phone' => 'contact_number',
            'email' => 'email',
            'sss' => 'sss_number',
            'tin' => 'tin_number',
            'philhealth' => 'philhealth_number',
            'pagibig' => 'pagibig_number',
            'address' => 'address',
        ];

        return $employee->{$mapping[$field] ?? $field} ?? null;
    }

    /**
     * Get supplier field value
     */
    private function getSupplierField($supplier, $field)
    {
        $mapping = [
            'phone' => 'number',  // Supplier model uses 'number' for phone
            'email' => 'email',
            'address' => 'address',
        ];

        return $supplier->{$mapping[$field] ?? $field} ?? null;
    }

    /**
     * Get user field value
     */
    private function getUserField($user, $field)
    {
        $mapping = [
            'phone' => 'phone',
            'email' => 'email',
        ];

        return $user->{$mapping[$field] ?? $field} ?? null;
    }

    /**
     * Mask a value based on type
     */
    public static function maskValue($value, $type)
    {
        if (empty($value)) {
            return 'N/A';
        }

        switch ($type) {
            case 'phone':
                // Show last 4 digits: ****-****-1234
                if (strlen($value) >= 4) {
                    return '****-****-' . substr($value, -4);
                }
                return '****';

            case 'email':
                // Show first 2 chars and domain: jo***@example.com
                if (preg_match('/^(.{2})(.*)@(.*)$/', $value, $matches)) {
                    return $matches[1] . '***@' . $matches[3];
                }
                return '***@***.***';

            case 'sss':
            case 'tin':
            case 'philhealth':
            case 'pagibig':
                // Show last 4 digits: **-*******-1234
                if (strlen($value) >= 4) {
                    return '**-*******-' . substr($value, -4);
                }
                return '**-*******-****';

            case 'address':
                // Show only city/province portion
                $parts = explode(',', $value);
                if (count($parts) >= 2) {
                    return '*****, ' . trim(end($parts));
                }
                return '*****';

            default:
                // Generic masking: show first and last 2 chars
                if (strlen($value) > 4) {
                    return substr($value, 0, 2) . '***' . substr($value, -2);
                }
                return '****';
        }
    }

    /**
     * Get activity logs for security dashboard
     */
    public function activityLogs(Request $request)
    {
        $query = SystemLog::with('user')->orderBy('created_at', 'desc');

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', 'LIKE', "%{$request->action}%");
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        return view('security.activity-logs', compact('logs'));
    }

    /**
     * Get data unmask logs
     */
    public function unmaskLogs(Request $request)
    {
        $query = DataUnmaskLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(50);

        return view('security.unmask-logs', compact('logs'));
    }
}
