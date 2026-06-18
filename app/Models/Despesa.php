<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Despesa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'despesas';

    protected $fillable = [
        'descricao',
        'valor',
        'data',
        'categoria_id',
        'observacoes',
        'id_venda',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data' => 'date',
    ];

    /**
     * Define a relação com a categoria da despesa.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Define a relação com a venda que gerou esta despesa (no caso de comissões).
     * Uma despesa pode ser a comissão de uma venda.
     */
    public function vendaComissao()
    {
        return $this->hasOne(Venda::class, 'despesa_id');
    }

    /**
     * Relação inversa: a venda que originou esta despesa (via id_venda).
     */
    public function venda()
    {
        return $this->belongsTo(Venda::class, 'id_venda');
    }
}