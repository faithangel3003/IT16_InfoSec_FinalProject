<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    /**
     * Display the archive listing with KPIs
     */
    public function index(Request $request)
    {
        $entityType = $request->get('entity_type');
        $deletedBy = $request->get('deleted_by');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = $request->get('per_page', 15);

        $query = Archive::query()->orderBy('deleted_at', 'desc');

        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        if ($deletedBy) {
            $query->where('deleted_by', $deletedBy);
        }
        if ($dateFrom) {
            $query->whereDate('deleted_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('deleted_at', '<=', $dateTo);
        }

        $archives = $query->paginate($perPage)->withQueryString();

        // KPIs
        $totalArchived = Archive::count();
        $archivedToday = Archive::whereDate('deleted_at', Carbon::today())->count();
        $archivedThisWeek = Archive::where('deleted_at', '>=', Carbon::now()->startOfWeek())->count();
        $archivedThisMonth = Archive::where('deleted_at', '>=', Carbon::now()->startOfMonth())->count();

        // By entity type
        $byEntityType = Archive::selectRaw('entity_type, COUNT(*) as count')
            ->groupBy('entity_type')
            ->orderBy('count', 'desc')
            ->get();

        // By role who deleted
        $byRole = Archive::selectRaw('deleted_by_role, COUNT(*) as count')
            ->groupBy('deleted_by_role')
            ->orderBy('count', 'desc')
            ->get();

        // Recent deletions (last 7 days trend)
        $dailyTrend = Archive::selectRaw('DATE(deleted_at) as date, COUNT(*) as count')
            ->where('deleted_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top deleters
        $topDeleters = Archive::selectRaw('deleted_by_name, deleted_by_role, COUNT(*) as count')
            ->groupBy('deleted_by', 'deleted_by_name', 'deleted_by_role')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Get entity types for filter
        $entityTypes = Archive::select('entity_type')->distinct()->pluck('entity_type');

        return view('archives.index', compact(
            'archives',
            'totalArchived',
            'archivedToday',
            'archivedThisWeek',
            'archivedThisMonth',
            'byEntityType',
            'byRole',
            'dailyTrend',
            'topDeleters',
            'entityTypes',
            'entityType',
            'deletedBy',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * View archived item details
     */
    public function show($id)
    {
        $archive = Archive::findOrFail($id);
        return view('archives.show', compact('archive'));
    }

    /**
     * Get archive statistics for profile view
     */
    public static function getStats()
    {
        return [
            'total' => Archive::count(),
            'today' => Archive::whereDate('deleted_at', Carbon::today())->count(),
            'this_week' => Archive::where('deleted_at', '>=', Carbon::now()->startOfWeek())->count(),
            'this_month' => Archive::where('deleted_at', '>=', Carbon::now()->startOfMonth())->count(),
            'by_type' => Archive::selectRaw('entity_type, COUNT(*) as count')
                ->groupBy('entity_type')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}
