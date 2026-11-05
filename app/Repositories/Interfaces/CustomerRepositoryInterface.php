<?php

namespace App\Repositories\Interfaces;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function findById(int $id): Customer;

    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;

    public function delete(Customer $customer): void;

    public function hasOrders(Customer $customer): bool;
}