<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\Room;
use App\Models\ReturnedItem;
use App\Models\Report;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InventoryReportsController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'all');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

        $data = $this->getReportData($reportType, $dateFrom, $dateTo);

        return view('inventory_reports.index', array_merge($data, [
            'reportType' => $reportType,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]));
    }

    public function exportPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'all');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

        $data = $this->getReportData($reportType, $dateFrom, $dateTo);

        $reportTitles = [
            'all' => 'Complete Inventory Report',
            'inventory_summary' => 'Inventory Summary Report',
            'stock_movement' => 'Stock Movement Report',
            'low_stock' => 'Low Stock Alert Report',
            'supplier_summary' => 'Supplier Summary Report',
            'room_inventory' => 'Room Inventory Report',
            'category_breakdown' => 'Category Breakdown Report',
        ];

        $pdf = Pdf::loadView('inventory_reports.pdf', array_merge($data, [
            'reportType' => $reportType,
            'reportTitle' => $reportTitles[$reportType] ?? 'Inventory Report',
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => Carbon::now()->format('Y-m-d H:i:s'),
        ]));

        $filename = str_replace(' ', '_', strtolower($reportTitles[$reportType] ?? 'report')) . '_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    private function getReportData($reportType, $dateFrom, $dateTo)
    {
        $data = [];

        // If 'all' is selected, fetch all report data
        if ($reportType === 'all') {
            // Inventory Summary
            $data['items'] = Item::with('category')->get();
            $data['totalItems'] = Item::count();
            $data['totalStock'] = Item::sum('in_stock');
            $data['totalValue'] = Item::selectRaw('SUM(in_stock * price) as total')->first()->total ?? 0;
            $data['categories'] = ItemCategory::withCount('items')->get();
            
            // Stock Movement
            $data['stockIns'] = StockIn::with('item', 'supplier')
                ->whereBetween('stockin_date', [$dateFrom, $dateTo])
                ->orderBy('stockin_date', 'desc')
                ->get();
            $data['stockOuts'] = StockOut::with('item', 'room')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->get();
            $data['totalStockIn'] = $data['stockIns']->sum('quantity');
            $data['totalStockOut'] = $data['stockOuts']->sum('quantity');
            
            // Low Stock
            $data['lowStockItems'] = Item::where('in_stock', '<=', 10)
                ->with('category')
                ->orderBy('in_stock', 'asc')
                ->get();
            $data['outOfStockItems'] = Item::where('in_stock', 0)
                ->with('category')
                ->get();
            
            // Supplier Summary
            $data['suppliers'] = Supplier::all()->map(function ($supplier) {
                $stockIns = StockIn::where('supplier_id', $supplier->supplier_id)->get();
                $supplier->stockInCount = $stockIns->count();
                $supplier->totalQuantitySupplied = $stockIns->sum('quantity');
                return $supplier;
            });
            
            // Room Inventory
            $data['rooms'] = Room::with(['items', 'type'])->get();
            $data['totalRooms'] = Room::count();
            $data['occupiedRooms'] = Room::where('status', 'occupied')->count();
            $data['emptyRooms'] = Room::where('status', 'empty')->count();
            
            // Category Breakdown (enhanced)
            $data['categoriesBreakdown'] = ItemCategory::with('items')
                ->withCount('items')
                ->get()
                ->map(function ($category) {
                    $category->totalStock = $category->items->sum('in_stock');
                    $category->totalValue = $category->items->sum(function ($item) {
                        return $item->in_stock * $item->price;
                    });
                    return $category;
                });
            
            return $data;
        }

        switch ($reportType) {
            case 'inventory_summary':
                $data['items'] = Item::with('category')->get();
                $data['totalItems'] = Item::count();
                $data['totalStock'] = Item::sum('in_stock');
                $data['totalValue'] = Item::selectRaw('SUM(in_stock * price) as total')->first()->total ?? 0;
                $data['categories'] = ItemCategory::withCount('items')->get();
                break;

            case 'stock_movement':
                $data['stockIns'] = StockIn::with('item', 'supplier')
                    ->whereBetween('stockin_date', [$dateFrom, $dateTo])
                    ->orderBy('stockin_date', 'desc')
                    ->get();
                $data['stockOuts'] = StockOut::with('item', 'room')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->orderBy('created_at', 'desc')
                    ->get();
                $data['totalStockIn'] = $data['stockIns']->sum('quantity');
                $data['totalStockOut'] = $data['stockOuts']->sum('quantity');
                break;

            case 'low_stock':
                $data['lowStockItems'] = Item::where('in_stock', '<=', 10)
                    ->with('category')
                    ->orderBy('in_stock', 'asc')
                    ->get();
                $data['outOfStockItems'] = Item::where('in_stock', 0)
                    ->with('category')
                    ->get();
                break;

            case 'supplier_summary':
                // Get suppliers with their stock-in data
                $data['suppliers'] = Supplier::all()->map(function ($supplier) {
                    $stockIns = StockIn::where('supplier_id', $supplier->supplier_id)->get();
                    $supplier->stockInCount = $stockIns->count();
                    $supplier->totalQuantitySupplied = $stockIns->sum('quantity');
                    return $supplier;
                });
                break;

            case 'room_inventory':
                $data['rooms'] = Room::with(['items', 'roomType'])->get();
                $data['totalRooms'] = Room::count();
                $data['occupiedRooms'] = Room::where('status', 'occupied')->count();
                $data['emptyRooms'] = Room::where('status', 'empty')->count();
                break;

            case 'category_breakdown':
                $data['categories'] = ItemCategory::with('items')
                    ->withCount('items')
                    ->get()
                    ->map(function ($category) {
                        $category->totalStock = $category->items->sum('in_stock');
                        $category->totalValue = $category->items->sum(function ($item) {
                            return $item->in_stock * $item->price;
                        });
                        return $category;
                    });
                break;
        }

        return $data;
    }
}
