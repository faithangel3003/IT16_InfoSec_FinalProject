<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IpBlocklist;
use App\Models\SystemLog;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        // Get filter values
        $status = $request->get('status');
        $severity = $request->get('severity');
        $type = $request->get('type');
        $timeRange = $request->get('time_range', '7');
        $perPage = $request->get('per_page', 10);

        // Build query
        $query = Incident::query()->orderBy('detected_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }
        if ($severity) {
            $query->where('severity', $severity);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($timeRange !== 'all') {
            $query->where('detected_at', '>=', Carbon::now()->subDays((int)$timeRange));
        }

        $incidents = $query->paginate($perPage)->withQueryString();

        // Stats
        $criticalOpen = Incident::where('severity', 'critical')->where('status', 'open')->count();
        $highOpen = Incident::where('severity', 'high')->where('status', 'open')->count();
        $investigating = Incident::where('status', 'investigating')->count();
        $contained = Incident::where('status', 'contained')->count();
        $resolved = Incident::where('status', 'resolved')->count();
        $totalIncidents = Incident::count();

        // Check for locked accounts (failed logins > 5 in last hour)
        $lockedAccounts = LoginLog::select('user_name')
            ->where('status', 'failed')
            ->where('login_at', '>=', Carbon::now()->subHour())
            ->groupBy('user_name')
            ->havingRaw('COUNT(*) >= 5')
            ->pluck('user_name')
            ->toArray();

        // Determine threat level
        $threatLevel = 'NORMAL';
        $threatMessage = 'No significant threats detected';
        if ($criticalOpen > 0) {
            $threatLevel = 'CRITICAL';
            $threatMessage = $criticalOpen . ' critical incident(s) require immediate attention';
        } elseif ($highOpen > 2) {
            $threatLevel = 'ELEVATED';
            $threatMessage = 'Multiple high severity incidents detected';
        } elseif ($highOpen > 0 || count($lockedAccounts) > 0) {
            $threatLevel = 'GUARDED';
            $threatMessage = 'Some security concerns require attention';
        }

        return view('incidents.index', compact(
            'incidents',
            'criticalOpen',
            'highOpen',
            'investigating',
            'contained',
            'resolved',
            'totalIncidents',
            'lockedAccounts',
            'threatLevel',
            'threatMessage',
            'status',
            'severity',
            'type',
            'timeRange'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'severity' => 'required|in:critical,high,medium,low',
            'type' => 'required|in:unauthorized_access,brute_force,suspicious_activity,data_breach,system_error,policy_violation,other',
            'description' => 'required|string|max:500',
            'ip_address' => 'nullable|ip',
            'affected_resource' => 'nullable|string|max:255',
        ]);

        $incident = Incident::create([
            'severity' => $request->severity,
            'type' => $request->type,
            'description' => $request->description,
            'ip_address' => $request->ip_address,
            'affected_resource' => $request->affected_resource,
            'status' => 'open',
            'detected_at' => Carbon::now(),
        ]);

        SystemLog::log(
            'incident.created',
            "New {$request->severity} incident created: {$request->description}",
            'security',
            $request->severity === 'critical' ? 'critical' : 'warning'
        );

        return redirect()->route('incidents.index')->with('success', 'Incident reported successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|in:open,investigating,contained,resolved',
            'resolution_notes' => 'nullable|string|max:1000',
        ];

        // Require resolution_notes when status is resolved
        if ($request->status === 'resolved') {
            $rules['resolution_notes'] = 'required|string|min:10|max:1000';
        }

        $request->validate($rules, [
            'resolution_notes.required' => 'Resolution description is required when marking as resolved.',
            'resolution_notes.min' => 'Resolution description must be at least 10 characters.',
        ]);

        $incident = Incident::findOrFail($id);
        $oldStatus = $incident->status;
        
        $incident->status = $request->status;
        if ($request->status === 'resolved') {
            $incident->resolved_at = Carbon::now();
            $incident->resolved_by = auth()->id();
        }
        if ($request->resolution_notes) {
            $incident->resolution_notes = $request->resolution_notes;
        }
        $incident->save();

        SystemLog::log(
            'incident.status_changed',
            "Incident #{$id} status changed from {$oldStatus} to {$request->status}",
            'security',
            'info'
        );

        return redirect()->back()->with('success', 'Incident status updated.');
    }

    /**
     * Report an incident from any authenticated user
     */
    public function reportIncident(Request $request)
    {
        $request->validate([
            'severity' => 'required|in:critical,high,medium,low',
            'type' => 'required|in:unauthorized_access,brute_force,suspicious_activity,data_breach,system_error,policy_violation,other',
            'description' => 'required|string|min:10|max:500',
            'affected_resource' => 'nullable|string|max:255',
        ], [
            'description.required' => 'Please describe the incident.',
            'description.min' => 'Description must be at least 10 characters.',
        ]);

        $user = auth()->user();

        $incident = Incident::create([
            'severity' => $request->severity,
            'type' => $request->type,
            'description' => $request->description,
            'ip_address' => $request->ip(),
            'affected_resource' => $request->affected_resource ?? 'User Reported',
            'status' => 'open',
            'detected_at' => Carbon::now(),
            'reported_by' => $user->id,
            'reported_by_name' => $user->name,
            'reported_by_role' => $user->role,
        ]);

        SystemLog::log(
            'incident.user_reported',
            "Incident reported by {$user->name} ({$user->role}): {$request->description}",
            'security',
            $request->severity === 'critical' ? 'critical' : 'warning',
            $user->id,
            $user->name,
            $request->ip()
        );

        return redirect()->back()->with('success', 'Incident reported successfully. Security team has been notified.');
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $query = Incident::whereBetween('detected_at', [$startDate, $endDate . ' 23:59:59']);

        $totalIncidents = (clone $query)->count();
        $resolved = (clone $query)->where('status', 'resolved')->count();
        $resolutionRate = $totalIncidents > 0 ? round(($resolved / $totalIncidents) * 100) : 0;

        // Average resolution time
        $avgResolution = Incident::whereBetween('detected_at', [$startDate, $endDate . ' 23:59:59'])
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, detected_at, resolved_at)) as avg_minutes')
            ->first()->avg_minutes;

        // Incidents by severity
        $bySeverity = [
            'critical' => (clone $query)->where('severity', 'critical')->count(),
            'high' => (clone $query)->where('severity', 'high')->count(),
            'medium' => (clone $query)->where('severity', 'medium')->count(),
            'low' => (clone $query)->where('severity', 'low')->count(),
        ];

        // Incidents by type
        $byType = Incident::whereBetween('detected_at', [$startDate, $endDate . ' 23:59:59'])
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Daily trend
        $dailyTrend = Incident::whereBetween('detected_at', [$startDate, $endDate . ' 23:59:59'])
            ->selectRaw('DATE(detected_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return view('incidents.report', compact(
            'startDate',
            'endDate',
            'totalIncidents',
            'resolved',
            'resolutionRate',
            'avgResolution',
            'bySeverity',
            'byType',
            'dailyTrend'
        ));
    }

    public function blocklist(Request $request)
    {
        // Deactivate expired blocks first
        IpBlocklist::where('is_active', true)
            ->get()
            ->each(function ($block) {
                if ($block->is_expired) {
                    $block->update(['is_active' => false]);
                }
            });

        $blockedIps = IpBlocklist::where('is_active', true)
            ->orderBy('blocked_at', 'desc')
            ->paginate(10);

        return view('incidents.blocklist', compact('blockedIps'));
    }

    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'duration' => 'required|in:24_hours,7_days,30_days,permanent',
            'reason' => 'nullable|string|max:500',
        ]);

        $expiresAt = IpBlocklist::calculateExpiresAt($request->duration);

        IpBlocklist::updateOrCreate(
            ['ip_address' => $request->ip_address],
            [
                'reason' => $request->reason,
                'duration' => $request->duration,
                'blocked_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'blocked_by' => auth()->id(),
                'is_active' => true,
            ]
        );

        SystemLog::log(
            'ip.blocked',
            "IP address {$request->ip_address} has been blocked ({$request->duration})",
            'security',
            'warning'
        );

        return redirect()->route('incidents.blocklist')->with('success', 'IP address blocked successfully.');
    }

    public function unblockIp($id)
    {
        $block = IpBlocklist::findOrFail($id);
        $ip = $block->ip_address;
        $block->update(['is_active' => false]);

        SystemLog::log(
            'ip.unblocked',
            "IP address {$ip} has been unblocked",
            'security',
            'info'
        );

        return redirect()->route('incidents.blocklist')->with('success', 'IP address unblocked.');
    }
}
