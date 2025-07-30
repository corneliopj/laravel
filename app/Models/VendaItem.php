<?php // NADA, absolutamente NADA deve vir antes desta linha.

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
            'plantel_id',
            'quantidade',
            'preco_unitario',
            'valor_total_item',
        ];

        protected $casts = [
            'quantidade' => 'integer',
            'preco_unitario' => 'decimal:2',
            'valor_total_item' => 'decimal:2',
        ];

        public function venda()
        {
            return $this->belongsTo(Venda::class, 'venda_id');
        }

        public function ave()
        {
            return $this->belongsTo(Ave::class, 'ave_id');
        }

        public function plantel()
        {
            return $this->belongsTo(Plantel::class, 'plantel_id');
        }
    }
    