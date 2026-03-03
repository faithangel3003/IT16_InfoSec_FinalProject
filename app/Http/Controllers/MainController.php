<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Item;
use App\Models\Room;
use App\Models\Report;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index(Request $request)
    {
        // Basic counts
        $totalStockIn = StockIn::count();
        $totalStockOut = StockOut::count();
        $totalItems = Item::count();
        $totalRooms = Room::count();
        $emptyRooms = Room::where('status', 'empty')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        
        // Low stock items (less than 25)
        $lowStockCount = Item::where('in_stock', '<', 25)->where('in_stock', '>', 0)->count();
        $outOfStockCount = Item::where('in_stock', 0)->count();
        
        // Inventory value (estimated)
        $inventoryValue = Item::selectRaw('SUM(in_stock * price) as total')->first()->total ?? 0;
        
        // Monthly stock in value
        $monthlyStockInValue = StockIn::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');
        
        // Monthly stock out quantity
        $monthlyStockOutQty = StockOut::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('quantity');
        
        // Top supplier this month
        $topSupplier = StockIn::select('supplier_id', DB::raw('COUNT(*) as count'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('supplier_id')
            ->orderByDesc('count')
            ->with('supplier')
            ->first();
        
        // Room occupancy rate
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        
        // Daily stock activity (7 days) for line chart
        $dailyStockIn = [];
        $dailyStockOut = [];
        $dailyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels[] = $date->format('m-d');
            $dailyStockIn[] = StockIn::whereDate('created_at', $date)->count();
            $dailyStockOut[] = StockOut::whereDate('created_at', $date)->count();
        }
        
        // Monthly activity (6 months) for bar chart
        $monthlyActivity = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('y-m');
            $monthlyActivity[] = StockIn::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }
        
        // Top items used (most stock out)
        $topItemsUsed = StockOut::select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('item')
            ->get();
        
        // Items needing restock
        $itemsNeedingRestock = Item::where('in_stock', '<', 25)->orderBy('in_stock')->take(10)->get();
        
        // Recent activities
        $recentActivities = Report::latest()->take(5)->get();
        
        // Active users today
        $activeUsersToday = LoginLog::whereDate('login_at', Carbon::today())
            ->where('status', 'success')
            ->distinct('user_id')
            ->count('user_id');

        return view('dashboard.index', compact(
            'totalStockIn',
            'totalStockOut',
            'totalItems',
            'totalRooms',
            'emptyRooms',
            'occupiedRooms',
            'lowStockCount',
            'outOfStockCount',
            'inventoryValue',
            'monthlyStockInValue',
            'monthlyStockOutQty',
            'topSupplier',
            'occupancyRate',
            'dailyStockIn',
            'dailyStockOut',
            'dailyLabels',
            'monthlyActivity',
            'monthlyLabels',
            'topItemsUsed',
            'itemsNeedingRestock',
            'recentActivities',
            'activeUsersToday'
        ));
    }
}