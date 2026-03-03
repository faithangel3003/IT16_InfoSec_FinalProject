<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\SystemLog;
use App\Models\LoginLog;
use App\Models\IpBlocklist;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SecurityDashboardController extends Controller
{
    /**
     * Display the security dashboard for Security role
     */
    public function index()
    {
        // Incident statistics
        $criticalOpen = Incident::where('severity', 'critical')->where('status', 'open')->count();
        $highOpen = Incident::where('severity', 'high')->where('status', 'open')->count();
        $investigating = Incident::where('status', 'investigating')->count();
        $contained = Incident::where('status', 'contained')->count();
        $resolved = Incident::where('status', 'resolved')->count();
        $totalIncidents = Incident::count();

        // Recent incidents
        $recentIncidents = Incident::orderBy('detected_at', 'desc')
            ->take(5)
            ->get();

        // Recent system logs
        $recentLogs = SystemLog::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Login statistics
        $todayLogins = LoginLog::whereDate('login_at', Carbon::today())->count();
        $failedLogins = LoginLog::where('status', 'failed')
            ->where('login_at', '>=', Carbon::now()->subHours(24))
            ->count();
        
        // Blocked IPs
        $blockedIps = IpBlocklist::where('is_permanent', true)
            ->orWhere('blocked_until', '>', now())
            ->count();

        // Check for locked accounts
        $lockedAccounts = LoginLog::select('user_name')
            ->where('status', 'failed')
            ->where('login_at', '>=', Carbon::now()->subHour())
            ->groupBy('user_name')
            ->havingRaw('COUNT(*) >= 5')
            ->pluck('user_name')
            ->toArray();

        // Threat level calculation
        $threatLevel = 'NORMAL';
        $threatMessage = 'No significant threats detected';
        $threatClass = '';
        
        if ($criticalOpen > 0) {
            $threatLevel = 'CRITICAL';
            $threatMessage = $criticalOpen . ' critical incident(s) require immediate attention';
            $threatClass = 'critical';
        } elseif ($highOpen > 2) {
            $threatLevel = 'ELEVATED';
            $threatMessage = 'Multiple high severity incidents detected';
            $threatClass = 'elevated';
        } elseif ($highOpen > 0 || count($lockedAccounts) > 0) {
            $threatLevel = 'GUARDED';
            $threatMessage = 'Some security concerns require attention';
            $threatClass = 'guarded';
        }

        // Incidents by day for chart (last 7 days)
        $dailyIncidents = Incident::selectRaw('DATE(detected_at) as date, COUNT(*) as count')
            ->where('detected_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Incidents by severity for donut chart
        $bySeverity = Incident::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get();

        return view('dashboard.security', compact(
            'criticalOpen',
            'highOpen',
            'investigating',
            'contained',
            'resolved',
            'totalIncidents',
            'recentIncidents',
            'recentLogs',
            'todayLogins',
            'failedLogins',
            'blockedIps',
            'lockedAccounts',
            'threatLevel',
            'threatMessage',
            'threatClass',
            'dailyIncidents',
            'bySeverity'
        ));
    }
}
