<?php

namespace App\Repositories\Implementations;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    // Retrieve all orders with customer and item details
    public function getAll(): LengthAwarePaginator
    {
        return Order::with(['customer', 'items'])
            ->latest()
            ->paginate(10);
    }

    // Retrieve all orders for a specific customer
    public function getAllForCustomer(int $customerId): LengthAwarePaginator
    {
        return Order::with(['customer', 'items'])
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate(10);
    }

    // Find an order by ID
    public function findById(int $id): Order
    {
        return Order::with(['customer', 'items'])
            ->findOrFail($id);
    }

    // Find and lock an order during a transaction
    public function findByIdForUpdate(int $id): Order
    {
        return Order::with('items')
            ->lockForUpdate()
            ->findOrFail($id);
    }

    // Create an order
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    // Update an order
    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->refresh();
    }

    // Attach an item to an order
    public function attachItem(
        Order $order,
        int $itemId,
        int $quantity,
        string $unitPrice
    ): void {
        $order->items()->attach($itemId, [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }

    // Delete an order
    public function delete(Order $order): void
    {
        $order->delete();
    }
}