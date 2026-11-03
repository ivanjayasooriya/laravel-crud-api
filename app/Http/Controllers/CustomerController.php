<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // GET: Retrieve all customers
    public function index()
    {
        $customers = Customer::latest()->paginate(10); // Fetch customers with pagination (10 per page)
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     */

    // POST: Create a new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);
        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201); // Return the created customer with a 201 status code
    }

    /**
     * Display the specified resource.
     */

    // GET: Retrieve a specific customer
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id); // Find the customer by ID or fail
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */

    // PUT: Update an existing customer
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id); // Find the customer by ID or fail
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);
        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     */

    // DELETE: Remove a customer
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return response()->json([ 
            'message' => 'Customer deleted successfully.',
        ], 200); // Return a success message with a 200 status code
    }
}
