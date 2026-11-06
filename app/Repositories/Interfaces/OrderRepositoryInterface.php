<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function getAllForCustomer(int $customerId): LengthAwarePaginator;

    public function findById(int $id): Order;

    public function findByIdForUpdate(int $id): Order;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;

    public function attachItem(
        Order $order,
        int $itemId,
        int $quantity,
        string $unitPrice
    ): void;

    public function delete(Order $order): void;
}