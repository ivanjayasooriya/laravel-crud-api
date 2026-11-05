<?php

namespace App\Repositories\Implementations;

use App\Models\Item;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ItemRepository implements ItemRepositoryInterface
{
    // Retrieve all items with pagination
    public function getAll(): LengthAwarePaginator
    {
        return Item::latest()->paginate(10);
    }

    // Find an item by ID
    public function findById(int $id): Item
    {
        return Item::findOrFail($id);
    }

    // Create a new item
    public function create(array $data): Item
    {
        return Item::create($data);
    }

    // Update an existing item
    public function update(Item $item, array $data): Item
    {
        $item->update($data);

        return $item->refresh();
    }

    // Delete an item
    public function delete(Item $item): void
    {
        $item->delete();
    }

    // Check whether the item has existing orders
    public function hasOrders(Item $item): bool
    {
        return $item->orders()->exists();
    }
}