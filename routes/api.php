<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VendaApiController;
use App\Http\Controllers\Api\ConsultaApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Usando um Middleware customizado simples para validar o api_token do banco
Route::middleware(function ($request, $next) {
    $token = $request->header('X-API-TOKEN') ?? $request->query('token');
    
    if (!$token) {
        return response()->json(['error' => 'Token não fornecido'], 401);
    }

    $user = \App\Models\User::where('api_token', $token)->first();

    if (!$user) {
        return response()->json(['error' => 'Token inválido'], 401);
    }

    // Autentica o usuário para a requisição atual
// auth()->login($user); // Se necessário para outras partes do sistema
    
    return $next($request);
})->group(function () {
    // Vendas Rápidas
    Route::post('vendas/rapida', [VendaApiController::class, 'store']);
    
    // Consultas Rápidas
    Route::get('consultas/ultima-venda', [ConsultaApiController::class, 'ultimaVenda']);
    Route::get('consultas/saldo-funcionario', [ConsultaApiController::class, 'saldoFuncionaria']);
});
