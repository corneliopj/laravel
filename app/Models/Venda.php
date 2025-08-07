<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Se você usa soft deletes em Venda

class Venda extends Model
{
    use HasFactory, SoftDeletes; // Adicionado SoftDeletes aqui

    protected $table = 'vendas'; // Certifique-se de que o nome da tabela está correto

    protected $fillable = [
        'numero_venda', // Se você tem este campo na DB
        'data_venda',
        'valor_total',
        'desconto',
        'valor_final',
        'metodo_pagamento',
        'observacoes',
        'status',
        'reserva_id',
        'user_id', // ID do usuário (vendedor)
        'percentual_comissao', // Percentual de comissão
        'comissao_paga', // Flag para comissão paga
        'despesa_id', // ID da despesa de comissão gerada
        'comprador', // Adicionado, pois estava faltando no fillable da sua última versão
    ];

    protected $casts = [
        'data_venda' => 'datetime',
        'valor_total' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_final' => 'decimal:2',
        'percentual_comissao' => 'decimal:2',
        'comissao_paga' => 'boolean',
    ];

    /**
     * Define a relação com os itens da venda.
     * Uma venda pode ter muitos itens.
     */
    public function vendaItems() // Mantido o nome vendaItems conforme seu arquivo
    {
        return $this->hasMany(VendaItem::class, 'venda_id');
    }

    /**
     * Define a relação com a reserva de onde esta venda pode ter sido originada.
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    /**
     * Define a relação com o usuário (vendedor) que realizou a venda.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define a relação com a despesa de comissão gerada por esta venda.
     */
    public function despesaComissao()
    {
        return $this->belongsTo(Despesa::class, 'despesa_id');
    }
}
