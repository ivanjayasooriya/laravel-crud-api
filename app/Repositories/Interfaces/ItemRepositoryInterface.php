<?php

namespace App\Repositories\Interfaces;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ItemRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function findById(int $id): Item;

    public function create(array $data): Item;

    public function update(Item $item, array $data): Item;

    public function delete(Item $item): void;

    public function hasOrders(Item $item): bool;

    public function findByIdForUpdate(int $id): Item;

    public function decrementStock(Item $item, int $quantity): void;

    public function incrementStock(Item $item, int $quantity): void;
}