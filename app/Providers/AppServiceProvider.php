<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Implementations\CustomerRepository;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Implementations\ItemRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
