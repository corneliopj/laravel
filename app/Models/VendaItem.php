<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaItem extends Model
{
    use HasFactory;

    protected $table = 'venda_items';

    protected $fillable = [
        'venda_id',
        'descricao_item',
        'ave_id',
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
     * Um item de venda pertence a uma venda.
     */
    public function venda()
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }

    /**
     * Um item de venda pode estar associado a uma ave específica.
     */
    public function ave()
    {
        return $this->belongsTo(Ave::class, 'ave_id');
    }
}
