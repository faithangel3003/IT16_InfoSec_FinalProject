<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\StockOut;
use App\Models\ReturnedItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoomDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Room Statistics
        $totalRooms = Room::count();
        $emptyRooms = Room::where('status', 'empty')->count();
        $restockedRooms = Room::where('status', 'restocked')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Room Types 
        $roomTypes = RoomType::withCount('rooms')->get();
        $totalRoomTypes = $roomTypes->count();

        // Items needing replacement (from returned_items table)
        $returnedItems = ReturnedItem::with('item')
            ->orderBy('created_at', 'desc')
            ->get();
        $totalReturnedItems = $returnedItems->count();
        $pendingReplacements = ReturnedItem::sum('quantity');

        // Recent Stock Out Activity (last 10)
        $recentStockOuts = StockOut::with('item')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Room Occupancy by Type (for chart)
        $roomsByType = RoomType::withCount([
            'rooms',
            'rooms as occupied_count' => function ($query) {
                $query->where('status', 'occupied');
            },
            'rooms as restocked_count' => function ($query) {
                $query->where('status', 'restocked');
            },
            'rooms as empty_count' => function ($query) {
                $query->where('status', 'empty');
            }
        ])->get();

        $typeLabels = $roomsByType->pluck('name')->toArray();
        $typeOccupied = $roomsByType->pluck('occupied_count')->toArray();
        $typeRestocked = $roomsByType->pluck('restocked_count')->toArray();
        $typeEmpty = $roomsByType->pluck('empty_count')->toArray();

        // Daily stock out activity (7 days)
        $dailyLabels = [];
        $dailyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels[] = $date->format('m-d');
            $dailyActivity[] = StockOut::whereDate('created_at', $date)->count();
        }

        // Rooms with most items
        $roomsWithItems = Room::withCount('items')
            ->with('type')
            ->orderBy('items_count', 'desc')
            ->take(5)
            ->get();

        // Recent returned items (items needing replacement)
        $recentReturns = ReturnedItem::with('item')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.room_manager', compact(
            'totalRooms',
            'emptyRooms',
            'restockedRooms',
            'occupiedRooms',
            'occupancyRate',
            'roomTypes',
            'totalRoomTypes',
            'returnedItems',
            'totalReturnedItems',
            'pendingReplacements',
            'recentStockOuts',
            'typeLabels',
            'typeOccupied',
            'typeRestocked',
            'typeEmpty',
            'dailyLabels',
            'dailyActivity',
            'roomsWithItems',
            'recentReturns'
        ));
    }
}
