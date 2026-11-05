<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    private OrderRepositoryInterface $orderRepository;

    private ItemRepositoryInterface $itemRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->itemRepository = $itemRepository;
    }

    // Retrieve all orders
    public function getAllOrders(): LengthAwarePaginator
    {
        return $this->orderRepository->getAll();
    }

    // Retrieve an order by ID
    public function getOrderById(int $id): Order
    {
        return $this->orderRepository->findById($id);
    }

    // Create a new order
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            // Create the order first
            $order = $this->orderRepository->create([
                'customer_id' => $data['customer_id'],
                'order_date' => $data['order_date'],
                'status' => $data['status'] ?? 'pending',
                'total_amount' => 0,
            ]);

            $totalInCents = 0;

            // Process each item in the order
            foreach ($data['items'] as $orderItem) {

                // Retrieve item and lock its database row
                $item = $this->itemRepository->findByIdForUpdate(
                    $orderItem['item_id']
                );

                $quantity = $orderItem['quantity'];

                // Check available stock
                if ($item->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Insufficient stock for item: {$item->name}. "
                            . "Available stock: {$item->stock}, "
                            . "requested: {$quantity}.",
                        ],
                    ]);
                }

                // Convert the price to cents to calculate accurately
                $unitPrice = number_format(
                    (float) $item->price,
                    2,
                    '.',
                    ''
                );

                [$whole, $fraction] = explode('.', $unitPrice);

                $unitPriceInCents = ((int) $whole * 100) + (int) $fraction;

                $subtotalInCents = $unitPriceInCents * $quantity;

                $totalInCents += $subtotalInCents;

                // Attach item to order
                $this->orderRepository->attachItem(
                    $order,
                    $item->id,
                    $quantity,
                    $unitPrice
                );

                // Reduce available stock
                $this->itemRepository->decrementStock(
                    $item,
                    $quantity
                );
            }

            // Convert total cents back to a decimal string
            $totalAmount = intdiv($totalInCents, 100)
                . '.'
                . str_pad((string) ($totalInCents % 100), 2, '0', STR_PAD_LEFT);

            // Update the order total
            $this->orderRepository->update($order, [
                'total_amount' => $totalAmount,
            ]);

            return $this->orderRepository->findById($order->id);
        });
    }

    // Update an existing order
    public function updateOrder(int $id, array $data): Order
    {
        $order = $this->orderRepository->findById($id);

        $this->orderRepository->update($order, $data);

        return $this->orderRepository->findById($id);
    }

    // Delete an order and restore item stock
    public function deleteOrder(int $id): void
    {
        DB::transaction(function () use ($id) {

            // Retrieve and lock the order
            $order = $this->orderRepository->findByIdForUpdate($id);

            // Restore stock for every item in the order
            foreach ($order->items as $item) {

                $lockedItem = $this->itemRepository->findByIdForUpdate(
                    $item->id
                );

                $this->itemRepository->incrementStock(
                    $lockedItem,
                    (int) $item->pivot->quantity
                );
            }

            // Delete the order
            $this->orderRepository->delete($order);
        });
    }
}