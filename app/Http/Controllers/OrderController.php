<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

use App\Models\Order;

class OrderController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // GET: Retrieve all orders
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $user = request()->user();

        if ($user->isCustomer()) {
            $orders = $this->orderService->getOrdersForCustomer($user->customer->id);
        } else {
            $orders = $this->orderService->getAllOrders();
        }

        return OrderResource::collection($orders);
    }

    // POST: Create a new order
    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);

        $data = $request->validated();

        if ($request->user()?->isCustomer()) {
            $data['customer_id'] = $request->user()->customer_id;
        }

        $order = $this->orderService->createOrder(
            $data
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    // GET: Retrieve a specific order
    public function show(string $id)
    {
        $this->authorize('view', $order);

        $order = $this->orderService->getOrderById((int) $id);

        return new OrderResource($order);
    }

    // PUT/PATCH: Update an existing order
    public function update(UpdateOrderRequest $request, string $id)
    {
        $this->authorize('update', $order);

        $order = $this->orderService->updateOrder(
            (int) $id,
            $request->validated()
        );

        return new OrderResource($order);
    }

    // DELETE: Delete an order
    public function destroy(string $id)
    {
        $this->authorize('delete', $order);

        $this->orderService->deleteOrder((int) $id);

        return response()->json([
            'message' => 'Order deleted successfully.',
        ], 200);
    }
}