<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConsultaApiController;
use App\Http\Controllers\Api\VendaApiController;

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

// Grupo de Rotas Protegidas pelo Token Simples
Route::middleware(['token.auth'])->group(function () {
    
    // Rotas de Consulta
    Route::get('v1/ultimas-vendas', [ConsultaApiController::class, 'ultimaVenda'])->name('api.v1.ultimaVenda');
    Route::get('v1/saldo-funcionario', [ConsultaApiController::class, 'saldoFuncionario'])->name('api.v1.saldoFuncionario');

    // Rotas de Vendas
    Route::post('v1/vendas', [VendaApiController::class, 'registrarVenda'])->name('api.v1.registrarVenda');
});
