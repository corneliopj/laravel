<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Vendas API V1
    Route::prefix('v1')->group(function () {
        Route::post('/vendas', [App\Http\Controllers\Api\V1\VendaApiController::class, 'store']);
        Route::get('/vendas/last', [App\Http\Controllers\Api\V1\VendaApiController::class, 'last']);
        
        // Financeiro API V1
        Route::get('/financeiro/saldo', [App\Http\Controllers\Api\V1\FinanceiroApiController::class, 'saldo']);
    });
});
