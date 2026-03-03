<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Item;
use App\Models\Room;
use App\Models\ReturnedItem;
use App\Models\Report;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $stockOutItems = StockOut::with('item')->paginate($perPage)->withQueryString();
        $rooms = Room::with('type')->orderBy('name')->get();

        return view('stock_out.index', compact('stockOutItems', 'rooms', 'perPage'));
    }

    public function addToStockOut(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::where('item_id', $id)->first();

        if ($item) {
            if ($validated['quantity'] > $item->in_stock) {
                return redirect()->back()->withErrors(['error' => 'Quantity exceeds available stock.']);
            }

            StockOut::create([
                'item_id' => $item->item_id,
                'quantity' => $validated['quantity'],
            ]);

            // Update the remaining quantity in the Items Table
            $item->in_stock -= $validated['quantity'];

            // Save the updated item
            $item->save();
        } else {
            // If not found in Items Table, try the Returned Items Table
            $returnedItem = ReturnedItem::where('item_id', $id)->first();

            if (!$returnedItem) {
                return redirect()->back()->withErrors(['error' => 'Item not found in either inventory or returned items.']);
            }

            // Handle stock-out for Returned Items Table
            if ($validated['quantity'] > $returnedItem->quantity) {
                return redirect()->back()->withErrors(['error' => 'Quantity exceeds available stock in returned items.']);
            }

            // Add the item to the Stock-Out table
            StockOut::create([
                'item_id' => $returnedItem->item_id,
                'quantity' => $validated['quantity'],
            ]);

            // Update the remaining quantity in the Returned Items Table
            $returnedItem->quantity -= $validated['quantity'];

            // If the quantity becomes 0, delete the returned item
            if ($returnedItem->quantity <= 0) {
                $returnedItem->delete();
            } else {
                $returnedItem->save();
            }
        }

        return redirect()->route('inventory.index')->with('success', 'Item successfully moved to Stock-Out.');
    }

    public function finalize(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,room_id',
            'stock_out_id' => 'required|exists:stock_out,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $stockOutItem = StockOut::with('item')->find($request->stock_out_id);

        if (!$stockOutItem) {
            return redirect()->route('stock_out.index')->with('error', 'Item not found in stock-out cart.');
        }

        $quantityToAssign = min($request->quantity, $stockOutItem->quantity);

        $room = Room::find($request->room_id);

        // Attach item to room (or update quantity if already exists)
        $existingPivot = $room->items()->where('room_item.item_id', $stockOutItem->item_id)->first();
        
        if ($existingPivot) {
            $room->items()->updateExistingPivot($stockOutItem->item_id, [
                'quantity' => $existingPivot->pivot->quantity + $quantityToAssign
            ]);
        } else {
            $room->items()->attach($stockOutItem->item_id, [
                'quantity' => $quantityToAssign
            ]);
        }

        $itemName = $stockOutItem->item->name;

        Report::create([
            'activity' => 'Assigned item: ' . $itemName . ' (Qty: ' . $quantityToAssign . ') to Room: ' . $room->name,
            'user_id' => auth()->id(),
        ]);

        // Update or delete stock out item based on remaining quantity
        if ($quantityToAssign >= $stockOutItem->quantity) {
            $stockOutItem->delete();
        } else {
            $stockOutItem->quantity -= $quantityToAssign;
            $stockOutItem->save();
        }

        // Update room status to restocked if it has items
        $room->update(['status' => 'restocked']);

        return redirect()->route('stock_out.index')->with('success', $itemName . ' (Qty: ' . $quantityToAssign . ') has been assigned to ' . $room->name . '.');
    }
}