<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use App\Events\TransactionCreated;
use App\Listeners\ProcessInventoryReduction;
use App\Listeners\RecordAccountingJournal;
use App\Events\InventoryMoved;
use App\Listeners\LogInventoryMovement;
use App\Listeners\ProcessAccountingForInventory;
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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Singleton: shared instance across the request lifecycle
        $this->app->singleton(\App\Services\InventoryService::class);
        $this->app->singleton(\App\Services\BusinessIntelligenceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        Gate::policy(Inventory::class,   InventoryPolicy::class);
        Gate::policy(Product::class,     ProductPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Outlet::class,      OutletPolicy::class);
        Gate::policy(User::class,        UserPolicy::class);

        // ── VERCEL STORAGE BOOTSTRAP (SHELBY EMERGENCY FIX) ────────────────
        if (env('VERCEL')) {
            $storagePath = '/tmp/storage/framework';
            $storagePaths = [
                $storagePath . '/views',
                $storagePath . '/sessions',
                $storagePath . '/cache',
            ];

            foreach ($storagePaths as $path) {
                if (!is_dir($path)) {
                    @mkdir($path, 0755, true);
                }
            }

            // Paksa Laravel menggunakan jalur /tmp untuk compiled views, sessions, dan cache
            config([
                'view.compiled' => $storagePath . '/views',
                'session.files' => $storagePath . '/sessions',
                'cache.stores.file.path' => $storagePath . '/cache',
            ]);
        }

        // ── ERP Event Listeners ───────────────────────────────────────────
        Event::listen(
            TransactionCreated::class,
            ProcessInventoryReduction::class,
        );
        Event::listen(
            TransactionCreated::class,
            RecordAccountingJournal::class,
        );
        Event::listen(
            TransactionCreated::class,
            \App\Listeners\UpdateCustomerLoyalty::class,
        );
        Event::listen(
            InventoryMoved::class,
            LogInventoryMovement::class,
        );
        Event::listen(
            InventoryMoved::class,
            ProcessAccountingForInventory::class,
        );
    }
}