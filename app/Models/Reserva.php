<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa SoftDeletes

class Reserva extends Model
{
    use HasFactory, SoftDeletes; // Usa o trait SoftDeletes

    protected $table = 'reservas'; // Nome da tabela no banco de dados

    protected $fillable = [
        'numero_reserva',
        'data_reserva',
        'data_prevista_entrega',
        'data_vencimento_proposta',
        'valor_total',
        'pagamento_parcial',
        'nome_cliente',
        'contato_cliente',
        'observacoes',
        'status',
    ];

    protected $casts = [
        'data_reserva' => 'datetime',
        'data_prevista_entrega' => 'datetime',
        'data_vencimento_proposta' => 'date',
        'valor_total' => 'decimal:2',
        'pagamento_parcial' => 'decimal:2',
    ];

    /**
     * Define a relação com os itens da reserva.
     * Uma reserva pode ter muitos itens.
     */
    public function items()
    {
        return $this->hasMany(ReservaItem::class, 'reserva_id');
    }

    /**
     * Define a relação com as vendas.
     * Uma reserva pode ser convertida em uma ou mais vendas (se houver essa lógica).
     * Usamos hasMany porque uma reserva pode gerar várias vendas (ex: venda parcial).
     */
    public function vendas()
    {
        return $this->hasMany(Venda::class, 'reserva_id');
    }
}
