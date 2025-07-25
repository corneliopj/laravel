<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Se você usa soft deletes em Venda

class Venda extends Model
{
    use HasFactory; // Se você usa soft deletes, adicione SoftDeletes aqui: use HasFactory, SoftDeletes;

    protected $table = 'vendas'; // Certifique-se de que o nome da tabela está correto

    protected $fillable = [
        'numero_venda',
        'data_venda',
        'valor_total',
        'desconto',
        'valor_final',
        'metodo_pagamento', // Alterado de 'forma_pagamento' para 'metodo_pagamento' para consistência
        'observacoes',
        'status',
        'reserva_id',
        'user_id', // NOVO: ID do usuário (vendedor)
        'comissao_percentual', // NOVO: Percentual de comissão
        'comissao_paga', // NOVO: Flag para comissão paga
        'despesa_id', // NOVO: ID da despesa de comissão gerada
    ];

    protected $casts = [
        'data_venda' => 'datetime',
        'valor_total' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_final' => 'decimal:2',
        'comissao_percentual' => 'decimal:2', // NOVO: Cast para decimal
        'comissao_paga' => 'boolean', // NOVO: Cast para boolean
    ];

    /**
     * Define a relação com os itens da venda.
     * Uma venda pode ter muitos itens.
     */
    public function vendaItems() // Renomeado para evitar conflito com 'items' em outras classes
    {
        return $this->hasMany(VendaItem::class, 'venda_id');
    }

    /**
     * Define a relação com a reserva de onde esta venda pode ter sido originada.
     * Uma venda pode pertencer a uma reserva (opcional).
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    /**
     * Define a relação com o usuário (vendedor) que realizou a venda.
     * Uma venda pertence a um usuário (opcional, se user_id for nullable).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define a relação com a despesa de comissão gerada por esta venda.
     * Uma venda pode ter uma despesa de comissão associada (opcional).
     */
    public function despesaComissao()
    {
        return $this->belongsTo(Despesa::class, 'despesa_id');
    }
}
