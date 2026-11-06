<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Implementations\CustomerRepository;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Implementations\ItemRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Implementations\OrderRepository;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Policies\CustomerPolicy;
use App\Policies\ItemPolicy;
use App\Policies\OrderPolicy;

use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void // Bind repositories
    {
        // Customer Repository binding
        $this->app->bind(
            CustomerRepositoryInterface::class, 
            CustomerRepository::class
        );

        // Item Repository binding
        $this->app->bind(
            ItemRepositoryInterface::class,
            ItemRepository::class
        );

        // Order Repository binding
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void // Register policies
    {
        Gate::policy(Customer::class, CustomerPolicy::class); // Allow admins and staff to view any customer
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
    }
}
