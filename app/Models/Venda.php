<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendas';

    protected $fillable = [
        'numero_venda',
        'data_venda',
        'valor_total',
        'desconto',
        'valor_final',
        'metodo_pagamento',
        'observacoes',
        'status',
        'reserva_id',
        'user_id',
        'comissao_percentual',
        'comissao_paga',
        'despesa_id',
        'comprador',
    ];

    protected $casts = [
        'data_venda' => 'datetime',
        'valor_total' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_final' => 'decimal:2',
        'comissao_percentual' => 'decimal:2',
        'comissao_paga' => 'boolean',
    ];

    public function vendaItems()
    {
        return $this->hasMany(VendaItem::class, 'venda_id');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function despesaComissao()
    {
        return $this->belongsTo(Despesa::class, 'despesa_id');
    }

    /**
     * Despesas de comissão originadas por esta venda (via id_venda na tabela despesas).
     */
    public function despesasComissao()
    {
        return $this->hasMany(Despesa::class, 'id_venda');
    }
}