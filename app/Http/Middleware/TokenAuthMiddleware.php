<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Captura o token do header ou da query string
        $token = $request->header('X-API-TOKEN') ?? $request->query('token');

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        // 2. Verifica o token no banco de dados
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        // 3. Autentica o usuário para a sessão da requisição
        Auth::login($user);

        return $next($request);
    }
}
