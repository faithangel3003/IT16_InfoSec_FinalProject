<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        // Get filter values
        $channel = $request->get('channel');
        $level = $request->get('level');
        $userId = $request->get('user_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        // Build query
        $query = SystemLog::query()->orderBy('created_at', 'desc');

        if ($channel) {
            $query->where('channel', $channel);
        }
        if ($level) {
            $query->where('level', $level);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate($perPage)->withQueryString();

        // Stats for today
        $today = Carbon::today();
        $totalToday = SystemLog::whereDate('created_at', $today)->count();
        $securityEvents = SystemLog::whereDate('created_at', $today)->where('channel', 'security')->count();
        $errors = SystemLog::whereDate('created_at', $today)->whereIn('level', ['error', 'critical'])->count();
        $auditEvents = SystemLog::whereDate('created_at', $today)->where('channel', 'audit')->count();

        // Users for filter
        $users = User::orderBy('name')->get();

        return view('system-logs.index', compact(
            'logs',
            'totalToday',
            'securityEvents',
            'errors',
            'auditEvents',
            'users',
            'channel',
            'level',
            'userId',
            'dateFrom',
            'dateTo',
            'search'
        ));
    }

    public function show($id)
    {
        $log = SystemLog::findOrFail($id);
        return response()->json($log);
    }

    public function exportCsv(Request $request)
    {
        $query = SystemLog::query()->orderBy('created_at', 'desc');

        if ($request->get('channel')) {
            $query->where('channel', $request->get('channel'));
        }
        if ($request->get('level')) {
            $query->where('level', $request->get('level'));
        }
        if ($request->get('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->get('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $query->limit(1000)->get();

        $csv = "Time,Channel,Level,Action,Message,User,IP\n";
        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->created_at->format('M d, H:i:s'),
                strtoupper($log->channel),
                strtoupper($log->level),
                $log->action,
                str_replace('"', '""', $log->message),
                $log->user_name ?? 'System',
                $log->ip_address ?? ''
            );
        }

        $filename = 'system_logs_' . date('Y-m-d_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
