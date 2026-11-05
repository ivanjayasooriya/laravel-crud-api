<?php

namespace App\Services;

use App\Models\Item;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ItemService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    // Retrieve all items
    public function getAllItems(): LengthAwarePaginator
    {
        return $this->itemRepository->getAll();
    }

    // Retrieve an item by ID
    public function getItemById(int $id): Item
    {
        return $this->itemRepository->findById($id);
    }

    // Create an item
    public function createItem(array $data): Item
    {
        return $this->itemRepository->create($data);
    }

    // Update an item
    public function updateItem(int $id, array $data): Item
    {
        $item = $this->itemRepository->findById($id);

        return $this->itemRepository->update($item, $data);
    }

    // Delete an item
    // Returns false if the item has existing orders
    public function deleteItem(int $id): bool
    {
        $item = $this->itemRepository->findById($id);

        if ($this->itemRepository->hasOrders($item)) {
            return false;
        }

        $this->itemRepository->delete($item);

        return true;
    }
}