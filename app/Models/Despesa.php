<?php

namespace App\Models; // Certifique-se de que o namespace está correto

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Se você usa soft deletes em Despesa

class Despesa extends Model
{
    use HasFactory; // Se você usa soft deletes, adicione SoftDeletes aqui: use HasFactory, SoftDeletes;

    protected $table = 'despesas'; // Certifique-se de que o nome da tabela está correto

    protected $fillable = [
        'descricao',
        'valor',
        'data', // CORRIGIDO: Usando 'data' em vez de 'data_despesa'
        'categoria_id',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data' => 'date', // CORRIGIDO: Usando 'data' em vez de 'data_despesa'
    ];

    /**
     * Define a relação com a categoria da despesa.
     * Uma despesa pertence a uma categoria.
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
}
