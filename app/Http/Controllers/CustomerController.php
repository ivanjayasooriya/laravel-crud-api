<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Http\Resources\CustomerResource;

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
        $customers = $this->customerService->getAllCustomers();

        return CustomerResource::collection($customers);
    }

    // POST: Create a new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = $this->customerService->createCustomer($validated);

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    // GET: Retrieve a specific customer
    public function show(string $id)
    {
        $customer = $this->customerService->getCustomerById((int) $id);

        return new CustomerResource($customer);
    }

    // PUT/PATCH: Update an existing customer
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $id,
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
        ]);

        $customer = $this->customerService->updateCustomer(
            (int) $id,
            $validated
        );

        return new CustomerResource($customer);
    }

    // DELETE: Remove a customer
    public function destroy(string $id)
    {
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