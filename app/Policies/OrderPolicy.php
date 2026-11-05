<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff()
            || ($user->isCustomer() && $user->customer !== null); // Allow admins, staff, and customers with a customer record to view orders
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin() || $user->isStaff()) {
            return true; // Allow admins and staff to view any order
        }

        return $user->isCustomer()
            && $user->customer !== null
            && $order->customer_id === $user->customer->id; // Allow customers to view their own orders
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin()
            || $user->isStaff()
            || ($user->isCustomer() && $user->customer !== null); // Allow admins, staff, and customers with a customer record to create orders
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isStaff(); // Allow admins and staff to update orders
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin(); // Allow admins to delete orders
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
