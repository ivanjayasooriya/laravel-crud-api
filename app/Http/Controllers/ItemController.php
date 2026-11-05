<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use App\Http\Resources\ItemResource;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;

class ItemController extends Controller
{
    private ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    // GET: Retrieve all items
    public function index()
    {
        $items = $this->itemService->getAllItems();

        return ItemResource::collection($items);
    }

    // POST: Create a new item
    public function store(StoreItemRequest $request)
    {
        $item = $this->itemService->createItem(
            $request->validated()
        );

        return (new ItemResource($item))
            ->response()
            ->setStatusCode(201);
    }

    // GET: Retrieve a specific item
    public function show(string $id)
    {
        $item = $this->itemService->getItemById((int) $id);

        return new ItemResource($item);
    }

    // PUT/PATCH: Update an existing item
    public function update(UpdateItemRequest $request, string $id)
    {
        $item = $this->itemService->updateItem(
            (int) $id,
            $request->validated()
        );

        return new ItemResource($item);
    }

    // DELETE: Remove an item
    public function destroy(string $id)
    {
        $deleted = $this->itemService->deleteItem((int) $id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Cannot delete item with existing orders.',
            ], 409);
        }

        return response()->json([
            'message' => 'Item deleted successfully.',
        ], 200);
    }
}