<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VendaApiController;
use App\Http\Controllers\Api\ConsultaApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Vendas Rápidas
    Route::post('vendas/rapida', [VendaApiController::class, 'store']);
    
    // Consultas Rápidas
    Route::get('consultas/ultima-venda', [ConsultaApiController::class, 'ultimaVenda']);
    Route::get('consultas/saldo-funcionario', [ConsultaApiController::class, 'saldoFuncionaria']);
});
