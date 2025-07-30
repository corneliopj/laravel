    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use App\Models\VendaItem;
    use App\Models\Reserva;

    class Venda extends Model
    {
        use HasFactory;

        protected $table = 'vendas';

        protected $fillable = [
            'data_venda',
            'valor_total',
            'desconto',
            'valor_final',
            'metodo_pagamento',
            'observacoes',
            'status',
            'reserva_id',
            'comprador',
        ];

        protected $casts = [
            'data_venda' => 'datetime',
            'valor_total' => 'decimal:2',
            'desconto' => 'decimal:2',
            'valor_final' => 'decimal:2',
        ];

        public function items()
        {
            return $this->hasMany(VendaItem::class, 'venda_id');
        }

        public function reserva()
        {
            return $this->belongsTo(Reserva::class, 'reserva_id');
        }
    }
    