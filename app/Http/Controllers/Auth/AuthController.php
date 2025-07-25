<?php

namespace App\Http\Controllers\Auth; // CORRIGIDO: O namespace deve ser App\Http\Controllers\Auth

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Certifique-se de que o modelo User está importado
use Illuminate\Support\Facades\Hash; // Para hashing de senhas
use Illuminate\Validation\ValidationException; // Adicionado para exceções de validação

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa a tentativa de login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail fornecido não é válido.',
            'password.required' => 'O campo senha é obrigatório.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Login realizado com sucesso!');
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Processa o logout do usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login'); // Redireciona para a página de login
    }

    /**
     * Exibe o formulário de registro.
     */
    public function showRegistrationForm()
    {
        // Verifica se o utilizador está autenticado e se é um administrador
        if (Auth::check() && Auth::user()->is_admin) {
            return view('auth.register');
        }

        // Se não for administrador, redireciona para a página inicial com uma mensagem de erro
        return redirect('/')->with('error', 'Apenas administradores podem registar novos utilizadores.');
    }

    /**
     * Processa o registro de um novo usuário.
     */
    public function register(Request $request)
    {
        // Verifica se o utilizador está autenticado e se é um administrador
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect('/')->with('error', 'Apenas administradores podem registar novos utilizadores.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail fornecido não é válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('login')->with('success', 'Registro realizado com sucesso! Faça login para continuar.');
    }
}
