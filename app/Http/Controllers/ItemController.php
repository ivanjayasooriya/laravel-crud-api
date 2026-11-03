<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Resources\ItemResource;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // GET: Retrieve all items
    public function index()
    {
        $items = Item::latest()->paginate(10); // Fetch items with pagination (10 per page)
        return ItemResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     */

    // POST: Create a new item
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ]);

        $item = Item::create($validated);
        return (new ItemResource($item))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */

    // GET: Retrieve a specific item
    public function show(string $id)
    {
        $item = Item::findOrFail($id);
        return new ItemResource($item);
    }

    /**
     * Update the specified resource in storage.
     */

    // PUT/PATCH: Update a specific item
    public function update(Request $request, string $id)
    {
        $item = Item::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0.01',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $item->update($validated);
        return new ItemResource($item);
    }

    /**
     * Remove the specified resource from storage.
     */

    // DELETE: Remove a specific item
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);

        if ($item->orders()->exists()) {
            return response()->json([
                'message' => 'Cannot delete item with existing orders.',
            ], 409); // Return a 409 Conflict status code if the item has associated orders
        }
        
        $item->delete();
        return response()->json([
            'message' => 'Item deleted successfully.',
        ], 200);
    }
}
