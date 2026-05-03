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

        // Share low stock count to app layout only (Performance Optimization)
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                // Cache the count for 60 seconds to prevent DB hammering on every partial render
                $lowStockCount = \Illuminate\Support\Facades\Cache::remember('low_stock_count_' . auth()->id(), 60, function () {
                    return \App\Models\Inventory::whereColumn('quantity', '<', 'min_quantity')->count();
                });
                $view->with('globalLowStockCount', $lowStockCount);
            }
        });

        // Ensure /tmp storage directories exist on Vercel
        if (env('VERCEL')) {
            $paths = [
                '/tmp/storage/framework/cache/data',
                '/tmp/storage/framework/sessions',
                '/tmp/storage/framework/views',
                '/tmp/storage/app/public',
                '/tmp/storage/app/private',
                '/tmp/storage/logs',
            ];
            foreach ($paths as $path) {
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
            }
        }
    }
}
