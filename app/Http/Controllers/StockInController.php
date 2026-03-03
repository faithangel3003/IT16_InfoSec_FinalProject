<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Report; 
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        // Fetch all stock-in records with related items
        $stockIns = StockIn::with('item', 'supplier')->paginate($perPage)->withQueryString();

        $suppliers = Supplier::all();

        // Fetch all items for the dropdown in the add stock-in form
        $items = Item::all();

        return view('stock_in.index', compact('stockIns', 'items', 'suppliers', 'perPage'));
    }

    public function create()
    {
        // Fetch all items for the dropdown in the add stock-in form
        $items = Item::all();

        return view('stock_in.create', compact('items'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'item_id' => 'required|exists:items,item_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'quantity' => 'required|integer|min:1',
            'stockin_date' => 'required|date',
        ]);

        // Fetch the item to get its price
        $item = Item::findOrFail($validated['item_id']);
        $price = $item->price;
        $totalPrice = $price * $validated['quantity'];

        // Create the stock-in record
        StockIn::create([
            'item_id' => $validated['item_id'],
            'supplier_id' => $validated['supplier_id'],
            'quantity' => $validated['quantity'],
            'price' => $price,
            'total_price' => $totalPrice,
            'stockin_date' => $validated['stockin_date'],
        ]);

        // Update the item's in_stock value
        $item->increment('in_stock', $validated['quantity']);

        Report::create([
            'activity' => 'Added Stock-In record for item: ' . $item->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('stock_in.index')->with('success', 'Stock-In record added successfully!');
    }

    public function edit($id)
    {
        // Fetch the stock-in record and related data
        $stockIn = StockIn::findOrFail($id);
        $items = Item::all();

        return view('stock_in.edit', compact('stockIn', 'items'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'item_id' => 'required|exists:items,item_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'quantity' => 'required|integer|min:1',
            'stockin_date' => 'required|date',
        ]);

        // Fetch the stock-in record and related item
        $stockIn = StockIn::findOrFail($id);
        $item = Item::findOrFail($validated['item_id']);

        // Revert the previous stock-in quantity from the item's in_stock
        $item->decrement('in_stock', $stockIn->quantity);

        // Update the stock-in record
        $price = $item->price;
        $totalPrice = $price * $validated['quantity'];
        $stockIn->update([
            'item_id' => $validated['item_id'],
            'supplier_id' => $validated['supplier_id'],
            'quantity' => $validated['quantity'],
            'price' => $price,
            'total_price' => $totalPrice,
            'stockin_date' => $validated['stockin_date'],
        ]);

        // Update the item's in_stock value with the new quantity
        $item->increment('in_stock', $validated['quantity']);

        Report::create([
            'activity' => 'Updated Stock-In record for item: ' . $item->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('stock_in.index')->with('success', 'Stock-In record updated successfully!');
    }

    public function destroy($id)
    {
        // Fetch the stock-in record and related item
        $stockIn = StockIn::findOrFail($id);
        $item = Item::findOrFail($stockIn->item_id);

        // Revert the stock-in quantity from the item's in_stock
        $item->decrement('in_stock', $stockIn->quantity);

        // Delete the stock-in record
        $stockIn->delete();

        Report::create([
            'activity' => 'Deleted Stock-In record for item: ' . $item->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('stock_in.index')->with('success', 'Stock-In record deleted successfully!');
    }
}