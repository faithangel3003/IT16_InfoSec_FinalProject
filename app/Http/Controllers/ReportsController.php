<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Room;
use App\Models\ReturnedItem;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $perPage = $request->input('per_page', 10);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Query for filtering reports
        $query = Report::with('user')->orderBy('created_at', 'desc');

        if ($filter == 'items') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added new item:%')
                    ->orWhere('activity', 'like', 'Updated item:%')
                    ->orWhere('activity', 'like', 'Deleted item:%');
            });
        } elseif ($filter == 'suppliers') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added new supplier:%')
                    ->orWhere('activity', 'like', 'Updated supplier:%')
                    ->orWhere('activity', 'like', 'Deleted supplier:%');
            });
        } elseif ($filter == 'stock_in') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added Stock-In record%')
                    ->orWhere('activity', 'like', 'Updated Stock-In record%')
                    ->orWhere('activity', 'like', 'Deleted Stock-In record%');
            });
        } elseif ($filter == 'stock_out') {
            $query->where('activity', 'like', 'Stocked out item:%');
        } elseif ($filter == 'rooms') {
            $query->where(function($q) {
                $q->where('activity', 'like', '%room%');
            });
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $reports = $query->paginate($perPage)->withQueryString();

        // Statistics for the cards
        $totalActivities = Report::count();
        $todayActivities = Report::whereDate('created_at', today())->count();
        $weekActivities = Report::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthActivities = Report::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $totalStockInThisMonth = StockIn::whereMonth('stockin_date', now()->month)
            ->whereYear('stockin_date', now()->year)
            ->count();

        $totalStockOutThisMonth = Report::where('activity', 'like', 'Stocked out item:%')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalSuppliers = Supplier::count();
        $totalItems = Item::count();
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $emptyRooms = Room::where('status', 'empty')->count();
        $restockedRooms = Room::where('status', 'restocked')->count();
        $totalCategories = Item::distinct('category_id')->count();

        // Advanced statistics
        $topStockedOutItem = StockOut::select('item_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->with('item')
            ->first();

        $topStockedInItem = StockIn::select('item_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->with('item')
            ->first();

        $mostActiveUser = Report::select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user')
            ->first();

        $totalReturnedItems = ReturnedItem::sum('quantity');

        // Activity trends (last 7 days)
        $activityTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $activityTrends[] = [
                'date' => $date->format('M d'),
                'count' => Report::whereDate('created_at', $date)->count()
            ];
        }

        // Activity by type
        $activityByType = [
            'items' => Report::where('activity', 'like', '%item%')->count(),
            'suppliers' => Report::where('activity', 'like', '%supplier%')->count(),
            'stock_in' => Report::where('activity', 'like', '%Stock-In%')->count(),
            'stock_out' => Report::where('activity', 'like', '%Stocked out%')->count(),
            'rooms' => Report::where('activity', 'like', '%room%')->count(),
        ];

        // Recent user activities (top 5 users)
        $userActivities = Report::select('user_id', DB::raw('COUNT(*) as count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Inventory overview
        $lowStockItems = Item::where('in_stock', '<=', 5)->where('in_stock', '>', 0)->count();
        $outOfStockItems = Item::where('in_stock', 0)->count();
        $totalStockValue = Item::sum(DB::raw('price * in_stock'));

        // Recent activities (latest 5)
        $recentActivities = Report::with('user')->latest()->take(5)->get();

        return view('reports.index', compact(
            'reports',
            'totalActivities',
            'todayActivities',
            'weekActivities',
            'monthActivities',
            'totalStockInThisMonth',
            'totalStockOutThisMonth',
            'totalSuppliers',
            'totalItems',
            'totalRooms',
            'occupiedRooms',
            'emptyRooms',
            'restockedRooms',
            'totalCategories',
            'topStockedOutItem',
            'topStockedInItem',
            'mostActiveUser',
            'totalReturnedItems',
            'activityTrends',
            'activityByType',
            'userActivities',
            'lowStockItems',
            'outOfStockItems',
            'totalStockValue',
            'recentActivities',
            'filter',
            'dateFrom',
            'dateTo'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filter = $request->input('filter');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Report::with('user')->orderBy('created_at', 'desc');

        if ($filter == 'items') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added new item:%')
                    ->orWhere('activity', 'like', 'Updated item:%')
                    ->orWhere('activity', 'like', 'Deleted item:%');
            });
        } elseif ($filter == 'suppliers') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added new supplier:%')
                    ->orWhere('activity', 'like', 'Updated supplier:%')
                    ->orWhere('activity', 'like', 'Deleted supplier:%');
            });
        } elseif ($filter == 'stock_in') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added Stock-In record%')
                    ->orWhere('activity', 'like', 'Updated Stock-In record%')
                    ->orWhere('activity', 'like', 'Deleted Stock-In record%');
            });
        } elseif ($filter == 'stock_out') {
            $query->where('activity', 'like', 'Stocked out item:%');
        } elseif ($filter == 'rooms') {
            $query->where('activity', 'like', '%room%');
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $reports = $query->limit(500)->get();

        $stats = [
            'totalActivities' => Report::count(),
            'totalItems' => Item::count(),
            'totalSuppliers' => Supplier::count(),
            'totalRooms' => Room::count(),
        ];

        $pdf = Pdf::loadView('reports.pdf', compact('reports', 'stats', 'filter', 'dateFrom', 'dateTo'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('audit-logs-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $filter = $request->input('filter');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Report::with('user')->orderBy('created_at', 'desc');

        if ($filter == 'items') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added new item:%')
                    ->orWhere('activity', 'like', 'Updated item:%')
                    ->orWhere('activity', 'like', 'Deleted item:%');
            });
        } elseif ($filter == 'suppliers') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added new supplier:%')
                    ->orWhere('activity', 'like', 'Updated supplier:%')
                    ->orWhere('activity', 'like', 'Deleted supplier:%');
            });
        } elseif ($filter == 'stock_in') {
            $query->where(function($q) {
                $q->where('activity', 'like', 'Added Stock-In record%')
                    ->orWhere('activity', 'like', 'Updated Stock-In record%')
                    ->orWhere('activity', 'like', 'Deleted Stock-In record%');
            });
        } elseif ($filter == 'stock_out') {
            $query->where('activity', 'like', 'Stocked out item:%');
        } elseif ($filter == 'rooms') {
            $query->where('activity', 'like', '%room%');
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $reports = $query->limit(1000)->get();

        $filename = 'audit-logs-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($reports) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, ['ID', 'Activity', 'User', 'Date/Time']);
            
            // CSV Data
            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->id,
                    $report->activity,
                    $report->user ? $report->user->name : 'Unknown',
                    $report->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}