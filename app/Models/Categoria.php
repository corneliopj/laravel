<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa a trait SoftDeletes

class Categoria extends Model
{
    use HasFactory, SoftDeletes; // Usa a trait SoftDeletes

    protected $table = 'categorias'; // Nome da tabela no banco de dados

    protected $fillable = [
        'nome',
        'tipo', // 'receita' ou 'despesa'
    ];

    protected $casts = [
        'tipo' => 'string', // Garante que o tipo seja tratado como string
    ];

    /**
     * Uma categoria pode ter muitas receitas.
     */
    public function receitas()
    {
        return $this->hasMany(Receita::class, 'categoria_id');
    }

    /**
     * Uma categoria pode ter muitas despesas.
     */
    public function despesas()
    {
        return $this->hasMany(Despesa::class, 'categoria_id');
    }
}

