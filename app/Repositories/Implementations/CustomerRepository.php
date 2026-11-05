<?php

namespace App\Repositories\Implementations;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAll()
    {
        return Customer::latest()->paginate(10);
    }

    public function findById(int $id)
    {
        return Customer::findOrFail($id);
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data)
    {
        $customer = $this->findById($id);
        
        $customer->update($data);
        return $customer;
    }

    public function delete(int $id)
    {
        $customer = $this->findById($id);
        return $customer->delete();  
    }
}