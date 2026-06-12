<?php

namespace App\Models;

// Importa a classe Authenticatable para funcionalidades de autenticação
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * As colunas que podem ser preenchidas massivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // Adicionado: permite preenchimento massivo para is_admin
    ];

    /**
     * As colunas que devem ser ocultadas para serialização.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * As colunas que devem ser convertidas para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean', // Adicionado: converte is_admin para booleano
    ];
}
