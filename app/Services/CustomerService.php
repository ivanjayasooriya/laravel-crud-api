<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    // Retrieve all customers
    public function getAllCustomers(): LengthAwarePaginator
    {
        Log::info('All customers retrieved');

        return $this->customerRepository->getAll();
    }

    // Retrieve a customer by ID
    public function getCustomerById(int $id): Customer
    {
        Log::info("Customer with ID {$id} retrieved");

        return $this->customerRepository->findById($id);
    }

    // Create a customer
    public function createCustomer(array $data): Customer
    {
        Log::info('Customer created', ['customer' => $data]);

        return $this->customerRepository->create($data);
    }

    // Update a customer
    public function updateCustomer(int $id, array $data): Customer
    {
        $customer = $this->customerRepository->findById($id);
        
        Log::info("Customer with ID {$id} updated", ['customer' => $data]);

        return $this->customerRepository->update($customer, $data);
    }

    // Delete a customer
    // Returns false if the customer has existing orders
    public function deleteCustomer(int $id): bool
    {
        $customer = $this->customerRepository->findById($id);

        if ($this->customerRepository->hasOrders($customer)) {
            Log::info("Customer with ID {$id} has existing orders");

            return false;
        }

        $this->customerRepository->delete($customer);
        
        Log::info("Customer with ID {$id} deleted");

        return true;
    }
}