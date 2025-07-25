<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa a trait SoftDeletes

class Receita extends Model
{
    use HasFactory, SoftDeletes; // Usa a trait SoftDeletes

    protected $table = 'receitas'; // Nome da tabela no banco de dados

    protected $fillable = [
        'descricao',
        'valor',
        'categoria_id',
        'data',
        'observacoes', // NOVO: Adicionado campo observacoes
    ];

    protected $casts = [
        'valor' => 'decimal:2', // Converte o valor para decimal com 2 casas decimais
        'data' => 'date',       // Converte a data para um objeto Carbon
    ];

    /**
     * Uma receita pertence a uma categoria.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}