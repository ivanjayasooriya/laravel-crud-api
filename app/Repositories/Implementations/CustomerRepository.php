<?php

namespace App\Repositories\Implementations;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    // Retrieve all customers with pagination
    public function getAll(): LengthAwarePaginator
    {
        return Customer::latest()->paginate(10);
    }

    // Find a customer by ID
    public function findById(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    // Create a new customer
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    // Update an existing customer
    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->refresh();
    }

    // Delete a customer
    public function delete(Customer $customer): void
    {
        $customer->delete();
    }

    // Check whether the customer has existing orders
    public function hasOrders(Customer $customer): bool
    {
        return $customer->orders()->exists();
    }
}