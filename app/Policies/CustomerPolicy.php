<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff(); // Allow admins and staff to view any customers
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        if ($user->isAdmin() || $user->isStaff()) {
            return true; // Allow admins and staff to view any customer
        }

        return $user->isCustomer() && $user->id === $customer->user_id; // Allow customers to view their own customer record
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff(); // Allow admins and staff to create customers
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        if ($user->isAdmin() || $user->isStaff()) {
            return true; // Allow admins and staff to update any customer
        }

        return $user->isCustomer() && $user->id === $customer->user_id; // Allow customers to update their own customer record
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->isAdmin(); // Allow admins to delete any customer
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return false;
    }
}
