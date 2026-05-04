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
    Route::get('/dashboard', [\App\Http\Controllers\ReportController::class, 'dashboard'])->name('dashboard');

    // ── KASIR / POS ───────────────────────────────────────────────
    Route::middleware(['role:admin,supervisor,operator'])->group(function () {
        Route::get('/pos',                         [TransactionController::class, 'posPage'])->name('pos.index');
        Route::post('/pos/checkout',               [TransactionController::class, 'checkout'])->name('pos.checkout')->middleware('throttle:checkout');
        Route::get('/pos/receipt/{transaction}',   [TransactionController::class, 'receipt'])->name('pos.receipt');
    });

    // ── LOGISTIK ──────────────────────────────────────────────────
    Route::middleware(['role:admin,supervisor,operator'])->group(function () {
        Route::get('/inbound',              [\App\Http\Controllers\InboundController::class, 'index'])->name('inbound.index');
        Route::get('/inbound/create',       [\App\Http\Controllers\InboundController::class, 'create'])->name('inbound.create');
        Route::post('/inbound',             [\App\Http\Controllers\InboundController::class, 'store'])->name('inbound.store');
        Route::post('/inbound/{po}/approve',[\App\Http\Controllers\InboundController::class, 'approve'])->name('inbound.approve');
        Route::post('/inbound/{po}/confirm',[\App\Http\Controllers\InboundController::class, 'confirm'])->name('inbound.confirm');
        Route::get('/inbound/{po}/receive', [\App\Http\Controllers\InboundController::class, 'receive'])->name('inbound.receive');
        Route::post('/inbound/{po}/receive',[\App\Http\Controllers\InboundController::class, 'storeGrn'])->name('inbound.grn.store');

        Route::get('/outbound',              [\App\Http\Controllers\OutboundController::class, 'index'])->name('outbound.index');
        Route::get('/outbound/create',       [\App\Http\Controllers\OutboundController::class, 'create'])->name('outbound.create');
        Route::post('/outbound',             [\App\Http\Controllers\OutboundController::class, 'store'])->name('outbound.store');
        Route::post('/outbound/{so}/confirm',[\App\Http\Controllers\OutboundController::class, 'confirm'])->name('outbound.confirm');
        Route::get('/outbound/{so}/picking', [\App\Http\Controllers\OutboundController::class, 'picking'])->name('outbound.picking');
        Route::post('/outbound/{so}/picking',[\App\Http\Controllers\OutboundController::class, 'storePicking'])->name('outbound.picking.store');
        Route::post('/outbound/{so}/ship',   [\App\Http\Controllers\OutboundController::class, 'ship'])->name('outbound.ship');
        Route::post('/outbound/{so}/deliver',[\App\Http\Controllers\OutboundController::class, 'deliver'])->name('outbound.deliver');

        Route::get('/inventories/logs',      [\App\Http\Controllers\InventoryController::class, 'logs'])->name('inventories.logs');
        Route::resource('inventories',  \App\Http\Controllers\InventoryController::class);

        // Stock Transfer
        Route::post('/stock_transfers/{stock_transfer}/ship',    [\App\Http\Controllers\StockTransferController::class, 'ship'])->name('stock_transfers.ship');
        Route::post('/stock_transfers/{stock_transfer}/receive', [\App\Http\Controllers\StockTransferController::class, 'receive'])->name('stock_transfers.receive');
        Route::resource('stock_transfers', \App\Http\Controllers\StockTransferController::class);

        // Stock Opname
        Route::post('/stock_opnames/{stock_opname}/approve', [\App\Http\Controllers\StockOpnameController::class, 'approve'])->name('stock_opnames.approve');
        Route::resource('stock_opnames', \App\Http\Controllers\StockOpnameController::class);
    });

    // ── DATA MASTER ───────────────────────────────────────────────
    Route::middleware(['role:admin,supervisor'])->group(function () {
        Route::get('/master',           [\App\Http\Controllers\MasterDataController::class, 'index'])->name('master.index');
        Route::resource('brands',       \App\Http\Controllers\BrandController::class);
        Route::resource('suppliers',    \App\Http\Controllers\SupplierController::class);
        Route::resource('categories',   \App\Http\Controllers\CategoryController::class);
        Route::resource('products',     ProductController::class);
        Route::resource('warehouses',   \App\Http\Controllers\WarehouseController::class);
        Route::resource('locations',    \App\Http\Controllers\LocationController::class);

        // OMS Channels
        Route::get('/channels',          [\App\Http\Controllers\ChannelController::class, 'index'])->name('channels.index');
        Route::post('/channels/{channel}/sync', [\App\Http\Controllers\ChannelController::class, 'sync'])->name('channels.sync');
        Route::post('/channels/{channel}/toggle', [\App\Http\Controllers\ChannelController::class, 'toggleConnection'])->name('channels.toggle');
    });

    // ── LAPORAN ───────────────────────────────────────────────────
    Route::middleware(['role:admin,supervisor'])->group(function () {
        Route::get('/reports',                  [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/wms',              [ReportController::class, 'wms'])->name('reports.wms');
        Route::get('/reports/analytics',        [ReportController::class, 'analytics'])->name('reports.analytics');
        Route::get('/assets/map',               [ReportController::class, 'assetMap'])->name('assets.map');
        Route::get('/api/reports/live-stats',   [ReportController::class, 'liveStats'])->name('reports.live');
        Route::get('/reports/export/sales',     [ReportController::class, 'exportSalesCsv'])->name('reports.export.sales');
    });

    // ── ADMINISTRASI SISTEM ────────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('outlets', \App\Http\Controllers\OutletController::class);
        Route::resource('users',   \App\Http\Controllers\UserController::class);
        Route::get('/reports/all', [ReportController::class, 'allOutlets'])->name('reports.all');
        Route::get('/audit-logs',  [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit_logs.index');
    });

    // ── ERP MODULES (FINANCE, HR, CRM) ───────────────────────────
    Route::middleware(['role:admin,supervisor'])->group(function () {
        // Finance
        Route::get('/finance',                     [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/journals',            [\App\Http\Controllers\FinanceController::class, 'journals'])->name('finance.journals');
        Route::get('/finance/accounts',            [\App\Http\Controllers\FinanceController::class, 'accounts'])->name('finance.accounts');
        Route::get('/finance/reports/profit-loss', [\App\Http\Controllers\FinanceController::class, 'reportProfitLoss'])->name('finance.reports.profit-loss');

        // PHASE 3 — Expense Management
        Route::get('/expenses',                    [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses',                   [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
        Route::post('/expenses/{expense}/approve', [\App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('/expenses/{expense}/reject',  [\App\Http\Controllers\ExpenseController::class, 'reject'])->name('expenses.reject');

        // PHASE 3 — Approval Gateway (polymorphic)
        Route::get('/approvals',                   [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{approval}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{approval}/reject',  [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');

        // PHASE 3 — Business Intelligence
        Route::get('/reports/dead-stock',          [\App\Http\Controllers\ReportController::class, 'deadStock'])->name('reports.dead-stock');
        Route::get('/reports/channel-profitability', [\App\Http\Controllers\ReportController::class, 'channelProfitability'])->name('reports.channel-profitability');
        Route::get('/reports/net-profit',          [\App\Http\Controllers\ReportController::class, 'netProfit'])->name('reports.net-profit');
        Route::get('/reports/reorder-alerts',      [\App\Http\Controllers\ReportController::class, 'reorderAlerts'])->name('reports.reorder-alerts');
        Route::get('/reports/cash-flow',           [\App\Http\Controllers\ReportController::class, 'cashFlow'])->name('reports.cash-flow');

        // PHASE 4 — Returns (RMA)
        Route::post('/returns',                    [\App\Http\Controllers\ReturnController::class, 'store'])->name('returns.store');

        // HR
        Route::get('/hr/commissions',              [\App\Http\Controllers\HRController::class, 'commissions'])->name('hr.commissions');
        Route::resource('employees',               \App\Http\Controllers\EmployeeController::class);
        Route::resource('attendances',             \App\Http\Controllers\AttendanceController::class);

        // CRM
        Route::resource('customers',               \App\Http\Controllers\CustomerController::class);
    });
});

// ── CRON / API (Exempt from CSRF) ──────────────────────────────────
Route::post('/api/cron/stock-check', [\App\Http\Controllers\InventoryController::class, 'checkStock']);
