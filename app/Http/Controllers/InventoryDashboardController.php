<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\ReturnedItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Basic counts
        $totalItems = Item::count();
        $totalCategories = ItemCategory::count();
        $totalSuppliers = Supplier::count();
        $totalStockIn = StockIn::count();
        $totalStockOut = StockOut::count();
        
        // Stock status (low stock threshold: 25 pieces)
        $lowStockCount = Item::where('in_stock', '<', 25)->where('in_stock', '>', 0)->count();
        $outOfStockCount = Item::where('in_stock', 0)->count();
        $inStockCount = Item::where('in_stock', '>=', 25)->count();
        
        // Total inventory in stock
        $totalInStock = Item::sum('in_stock');
        
        // Inventory value (estimated)
        $inventoryValue = Item::selectRaw('SUM(in_stock * price) as total')->first()->total ?? 0;
        
        // Monthly stock in value and quantity
        $monthlyStockInValue = StockIn::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');
        
        $monthlyStockInQty = StockIn::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('quantity');
        
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
        
        // Daily stock activity (7 days) for line chart
        $dailyStockIn = [];
        $dailyStockOut = [];
        $dailyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels[] = $date->format('m-d');
            $dailyStockIn[] = StockIn::whereDate('created_at', $date)->sum('quantity');
            $dailyStockOut[] = StockOut::whereDate('created_at', $date)->sum('quantity');
        }
        
        // Monthly activity (6 months) for bar chart
        $monthlyStockInData = [];
        $monthlyStockOutData = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlyStockInData[] = StockIn::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('quantity');
            $monthlyStockOutData[] = StockOut::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('quantity');
        }
        
        // Items by category for pie chart
        $categoriesWithItems = ItemCategory::withCount('items')
            ->having('items_count', '>', 0)
            ->get();
        $categoryLabels = $categoriesWithItems->pluck('name')->toArray();
        $categoryData = $categoriesWithItems->pluck('items_count')->toArray();
        
        // Top items used (most stock out)
        $topItemsUsed = StockOut::select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('item')
            ->get();
        
        // Items needing restock (threshold: 25 pieces)
        $itemsNeedingRestock = Item::where('in_stock', '<', 25)
            ->orderBy('in_stock')
            ->take(10)
            ->with('category')
            ->get();
        
        // Recent stock in transactions
        $recentStockIn = StockIn::with(['item', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Recent stock out transactions
        $recentStockOut = StockOut::with('item')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Top suppliers by delivery count
        $topSuppliers = StockIn::select('supplier_id', DB::raw('COUNT(*) as delivery_count'), DB::raw('SUM(total_price) as total_value'))
            ->groupBy('supplier_id')
            ->orderByDesc('delivery_count')
            ->take(5)
            ->with('supplier')
            ->get();
        
        // Returned items count
        $returnedItemsCount = ReturnedItem::sum('quantity');
        
        return view('dashboard.inventory_manager', compact(
            'totalItems',
            'totalCategories',
            'totalSuppliers',
            'totalStockIn',
            'totalStockOut',
            'lowStockCount',
            'outOfStockCount',
            'inStockCount',
            'totalInStock',
            'inventoryValue',
            'monthlyStockInValue',
            'monthlyStockInQty',
            'monthlyStockOutQty',
            'topSupplier',
            'dailyLabels',
            'dailyStockIn',
            'dailyStockOut',
            'monthlyLabels',
            'monthlyStockInData',
            'monthlyStockOutData',
            'categoryLabels',
            'categoryData',
            'topItemsUsed',
            'itemsNeedingRestock',
            'recentStockIn',
            'recentStockOut',
            'topSuppliers',
            'returnedItemsCount'
        ));
    }
}
