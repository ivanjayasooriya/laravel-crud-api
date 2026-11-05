<?php

namespace App\Services;

use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerService
{
   private CustomerRepositoryInterface $customerRepository;

   // Dependency injection of the CustomerRepositoryInterface
   public function __construct(CustomerRepositoryInterface $customerRepository)
   {
       $this->customerRepository = $customerRepository;
   }

   public function getAllCustomers()
   {
       return $this->customerRepository->getAll();
   }

    public function getCustomerById(int $id)
    {
         return $this->customerRepository->findById($id);
    }

    public function createCustomer(array $data)
    {
        return $this->customerRepository->create($data);
    }

    public function updateCustomer(int $id, array $data)
    {
        return $this->customerRepository->update($id, $data);
    }

    public function deleteCustomer(int $id)
    {
        return $this->customerRepository->delete($id);
    }
}