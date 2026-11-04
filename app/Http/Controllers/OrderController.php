<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // GET: Retrieve all orders
    public function index()
    {
        $orders = Order::with(['customer', 'items'])->latest()->paginate(10); // Fetch orders with pagination (10 per page)
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */

    // POST: Create a new order
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => ['sometimes', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|distinct|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'status' => $validated['status'] ?? 'pending',
                'total_amount' => 0, // Will be calculated below
            ]);

            $total = 0;
            foreach ($validated['items'] as $orderItem) {
                $item = Item::where('id', $orderItem['item_id'])->lockForUpdate()->firstOrFail();
                
                $quantity = $orderItem['quantity'];

                if ($item->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for item: {$item->name}. Available stock: {$item->stock}, requested: {$quantity}."],
                    ]);
                }

                $subtotal = $item->price * $quantity;

                $total += $subtotal;

                $order->items()->attach($item->id, [
                    'quantity' => $quantity,
                    'unit_price' => $item->price,
                ]);

                $item->decrement('stock', $quantity);
            }

            $order->update(['total_amount' => $total]);

            return $order;
        });

        return (new OrderResource($order->load(['customer', 'items'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */

    // GET: Retrieve a specific order by ID
    public function show(string $id)
    {
        $order = Order::with(['customer', 'items'])->findOrFail($id);
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */

    // PUT: Update a specific order by ID
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'sometimes|required|exists:customers,id',
            'order_date' => 'sometimes|required|date',
            'status' => ['sometimes', 'required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
        ]);

        $order->update($validated);

        return new OrderResource($order->load(['customer', 'items']));
    }

    /**
     * Remove the specified resource from storage.
     */

    // DELETE: Delete a specific order by ID
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $order = Order::with('items')->lockForUpdate()->findOrFail($id);

            // Restore stock for each item in the order
            foreach ($order->items as $item) {
                $item->increment('stock', $item->pivot->quantity);
            }

            // Delete the order
            $order->delete();
        });

        return response()->json(['message' => 'Order deleted successfully.'], 200);
    }
}
