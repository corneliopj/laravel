<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaItem extends Model
{
    use HasFactory;

    protected $table = 'venda_items'; // Nome da tabela no banco de dados

    protected $fillable = [
        'venda_id',
        'descricao_item',
        'ave_id',
        'plantel_id', // NOVO: Adicionado plantel_id
        'quantidade',
        'preco_unitario',
        'valor_total_item',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'valor_total_item' => 'decimal:2',
    ];

    /**
     * Define a relação com a venda à qual o item pertence.
     * Um item de venda pertence a uma venda.
     */
    public function venda()
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }

    /**
     * Define a relação com a ave, se o item for uma ave específica.
     * Um item de venda pode pertencer a uma ave (opcional).
     */
    public function ave()
    {
        return $this->belongsTo(Ave::class, 'ave_id');
    }

    /**
     * NOVO: Define a relação com o plantel, se o item for de um plantel.
     * Um item de venda pode pertencer a um plantel (opcional).
     */
    public function plantel()
    {
        return $this->belongsTo(Plantel::class, 'plantel_id');
    }
}
