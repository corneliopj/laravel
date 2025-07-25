<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaItem extends Model
{
    use HasFactory;

    protected $table = 'reserva_items'; // Nome da tabela no banco de dados

    protected $fillable = [
        'reserva_id',
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
     * Define a relação com a reserva à qual o item pertence.
     * Um item de reserva pertence a uma reserva.
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    /**
     * Define a relação com a ave, se o item for uma ave específica.
     * Um item de reserva pode pertencer a uma ave (opcional).
     */
    public function ave()
    {
        return $this->belongsTo(Ave::class, 'ave_id');
    }
}
