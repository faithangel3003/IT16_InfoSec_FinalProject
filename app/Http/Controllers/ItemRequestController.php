<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\Item;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemRequestController extends Controller
{
    /**
     * Display item requests for Room Manager
     */
    public function index(Request $request)
    {
        $query = ItemRequest::with(['room', 'item', 'requester', 'processor'])
            ->where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by room
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $requests = $query->paginate(10);
        $rooms = Room::all();
        $items = Item::with('category')->where('in_stock', '>', 0)->get();

        return view('item_requests.index', compact('requests', 'rooms', 'items'));
    }

    /**
     * Store a new item request (Room Manager)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,room_id',
            'item_id' => 'required|exists:items,item_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if item has enough stock
        $item = Item::find($validated['item_id']);
        if ($item->in_stock < $validated['quantity']) {
            return back()->with('error', 'Requested quantity exceeds available stock. Available: ' . $item->in_stock);
        }

        ItemRequest::create([
            'room_id' => $validated['room_id'],
            'item_id' => $validated['item_id'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
            'requested_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Item request submitted successfully.');
    }

    /**
     * Cancel a pending request (Room Manager)
     */
    public function cancel($id)
    {
        $itemRequest = ItemRequest::where('request_id', $id)
            ->where('requested_by', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $itemRequest->delete();

        return back()->with('success', 'Request cancelled successfully.');
    }

    /**
     * Display all item requests for Inventory Manager
     */
    public function manageRequests(Request $request)
    {
        $query = ItemRequest::with(['room', 'item', 'item.category', 'requester', 'processor'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by room
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $requests = $query->paginate(15);
        $rooms = Room::all();
        
        // Get statistics
        $stats = [
            'pending' => ItemRequest::where('status', 'pending')->count(),
            'approved' => ItemRequest::where('status', 'approved')->count(),
            'fulfilled' => ItemRequest::where('status', 'fulfilled')->count(),
            'rejected' => ItemRequest::where('status', 'rejected')->count(),
        ];

        return view('item_requests.manage', compact('requests', 'rooms', 'stats'));
    }

    /**
     * Approve a request (Inventory Manager)
     */
    public function approve($id)
    {
        $itemRequest = ItemRequest::where('request_id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Check stock availability
        $item = Item::find($itemRequest->item_id);
        if ($item->in_stock < $itemRequest->quantity) {
            return back()->with('error', 'Insufficient stock. Available: ' . $item->in_stock);
        }

        $itemRequest->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Request approved. Ready for fulfillment.');
    }

    /**
     * Reject a request (Inventory Manager)
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $itemRequest = ItemRequest::where('request_id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $itemRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Request rejected.');
    }

    /**
     * Fulfill an approved request - deduct stock and assign to room (Inventory Manager)
     */
    public function fulfill($id)
    {
        $itemRequest = ItemRequest::where('request_id', $id)
            ->where('status', 'approved')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $item = Item::find($itemRequest->item_id);
            $room = Room::find($itemRequest->room_id);

            // Check stock availability
            if ($item->in_stock < $itemRequest->quantity) {
                return back()->with('error', 'Insufficient stock. Available: ' . $item->in_stock);
            }

            // Deduct from inventory
            $item->in_stock -= $itemRequest->quantity;
            $item->save();

            // Add to room inventory
            $existingPivot = $room->items()->where('item_id', $item->item_id)->first();
            if ($existingPivot) {
                $room->items()->updateExistingPivot($item->item_id, [
                    'quantity' => $existingPivot->pivot->quantity + $itemRequest->quantity,
                ]);
            } else {
                $room->items()->attach($item->item_id, [
                    'quantity' => $itemRequest->quantity,
                ]);
            }

            // Update request status
            $itemRequest->update([
                'status' => 'fulfilled',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            // Update room status to restocked if it has items and is not occupied by guest
            if ($room->status === 'empty') {
                $room->update(['status' => 'restocked']);
            }

            DB::commit();
            return back()->with('success', 'Request fulfilled. Items assigned to ' . $room->name . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to fulfill request: ' . $e->getMessage());
        }
    }

    /**
     * Get available stock for an item (AJAX)
     */
    public function getItemStock($itemId)
    {
        $item = Item::find($itemId);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json([
            'item_id' => $item->item_id,
            'name' => $item->name,
            'in_stock' => $item->in_stock,
            'price' => $item->price,
        ]);
    }
}
