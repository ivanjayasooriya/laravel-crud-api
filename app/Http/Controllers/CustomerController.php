<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

use App\Models\Customer;

class CustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    // GET: Retrieve all customers
    public function index()
    {
        $this->authorize('viewAny', Customer::class);

        $customers = $this->customerService->getAllCustomers();

        return CustomerResource::collection($customers);
    }

    // POST: Create a new customer
    public function store(StoreCustomerRequest $request)
    {
        $this->authorize('create', Customer::class);

        $customer = $this->customerService->createCustomer(
            $request->validated()
        );

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    // GET: Retrieve a specific customer
    public function show(string $id)
    {
        $this->authorize('view', $customer);

        $customer = $this->customerService->getCustomerById((int) $id);

        return new CustomerResource($customer);
    }

    // PUT/PATCH: Update an existing customer
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $this->authorize('update', Customer::class);

        $customer = $this->customerService->updateCustomer(
            (int) $id,
            $request->validated()
        );

        return new CustomerResource($customer);
    }

    // DELETE: Remove a customer
    public function destroy(string $id)
    {
        $this->authorize('delete', Customer::class);
        
        $deleted = $this->customerService->deleteCustomer((int) $id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Cannot delete customer with existing orders.',
            ], 409);
        }

        return response()->json([
            'message' => 'Customer deleted successfully.',
        ], 200);
    }
}