<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public QC Endpoints (Exempt from Auth for stress testing speed)
Route::post('/pos/checkout',     [ApiController::class, 'posCheckout']);
Route::post('/wms/mutate',       [ApiController::class, 'wmsMutate']);
// Alias for k6 script consistency
Route::post('/wms/stock-opname', [ApiController::class, 'wmsStockOpname']);

Route::get('/live-stats',        [ApiController::class, 'liveStats']);

// Protected User Route
Route::middleware(['auth:sanctum', 'localization'])->get('/user', function (Request $request) {
    return $request->user();
});
