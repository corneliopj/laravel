<?php

namespace App\Models; // Confirme que este é o namespace correto para o seu modelo Venda

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VendaItem; // Importa o modelo VendaItem
use App\Models\Reserva; // Importa o modelo Reserva, se a venda puder ser de uma reserva

class Venda extends Model
{
    use HasFactory;

    protected $table = 'vendas'; // Assegura o nome correto da tabela

    protected $fillable = [
        'data_venda',
        'valor_total',
        'desconto',
        'valor_final',
        'metodo_pagamento',
        'observacoes',
        'status',
        'reserva_id', // Se a venda puder ser convertida de uma reserva
        'comprador', // Adicionado novamente, pois é um campo da venda
    ];

    protected $casts = [
        'data_venda' => 'datetime',
        'valor_total' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_final' => 'decimal:2',
    ];

    /**
     * Define a relação com os itens da venda.
     * Uma venda pode ter muitos itens.
     */
    public function items()
    {
        return $this->hasMany(VendaItem::class, 'venda_id');
    }

    /**
     * Define a relação com a reserva de onde esta venda foi gerada.
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    // Se você tiver um relacionamento com o usuário que fez a venda (user_id),
    // você pode adicioná-lo aqui novamente, se necessário.
    /*
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    */
}
