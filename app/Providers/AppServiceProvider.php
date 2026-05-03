<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Outlet;
use App\Models\User;
use App\Policies\InventoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\OutletPolicy;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Inventory::class,   InventoryPolicy::class);
        Gate::policy(Product::class,     ProductPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Outlet::class,      OutletPolicy::class);
        Gate::policy(User::class,        UserPolicy::class);
    }
}
