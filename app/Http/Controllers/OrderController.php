<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // GET: Retrieve all orders
    public function index()
    {
        $orders = $this->orderService->getAllOrders();

        return OrderResource::collection($orders);
    }

    // POST: Create a new order
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->createOrder(
            $request->validated()
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    // GET: Retrieve a specific order
    public function show(string $id)
    {
        $order = $this->orderService->getOrderById((int) $id);

        return new OrderResource($order);
    }

    // PUT/PATCH: Update an existing order
    public function update(UpdateOrderRequest $request, string $id)
    {
        $order = $this->orderService->updateOrder(
            (int) $id,
            $request->validated()
        );

        return new OrderResource($order);
    }

    // DELETE: Delete an order
    public function destroy(string $id)
    {
        $this->orderService->deleteOrder((int) $id);

        return response()->json([
            'message' => 'Order deleted successfully.',
        ], 200);
    }
}