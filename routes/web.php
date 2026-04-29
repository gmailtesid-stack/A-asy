<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;

// ─── Auth ─────────────────────────────────────────────────────────────
Route::get('/login',  [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// ─── Authenticated ─────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // ── KASIR (semua role) ────────────────────────────────────────────
    Route::middleware(['role:super_admin,manager,cashier'])->group(function () {
        Route::get('/pos',                         [TransactionController::class, 'posPage'])->name('pos.index');
        Route::post('/pos/checkout',               [TransactionController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/receipt/{transaction}',   [TransactionController::class, 'receipt'])->name('pos.receipt');
    });

    // ── MANAGER + ADMIN ───────────────────────────────────────────────
    Route::middleware(['role:super_admin,manager'])->group(function () {
        Route::get('/reports',                  [ReportController::class, 'index'])->name('reports.index');
        Route::get('/api/reports/live-stats',   [ReportController::class, 'liveStats'])->name('reports.live');

        Route::resource('products',   ProductController::class);
        Route::resource('inventories',\App\Http\Controllers\InventoryController::class);
    });

    // ── SUPER ADMIN ONLY ──────────────────────────────────────────────
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('outlets', \App\Http\Controllers\OutletController::class);
        Route::resource('users',   \App\Http\Controllers\UserController::class);
        Route::get('/reports/all', [ReportController::class, 'allOutlets'])->name('reports.all');
    });
});
