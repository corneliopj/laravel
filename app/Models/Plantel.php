<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Plantel extends Model
{
    use HasFactory;

    protected $table = 'plantel'; // Define o nome da tabela explicitamente

    protected $fillable = [
        'tipo_ave_id',
        'identificacao_grupo',
        'data_formacao',
        'quantidade_inicial',
        'ativo',
        'observacoes',
    ];

    protected $casts = [
        'data_formacao' => 'date',
        'ativo' => 'boolean',
    ];

    // Relacionamento com TipoAve
    public function tipoAve()
    {
        return $this->belongsTo(TipoAve::class);
    }

    // Relacionamento com MovimentacaoPlantel
    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoPlantel::class);
    }

    /**
     * Acessor para calcular a quantidade atual de aves no plantel.
     *
     * @return int
     */
    public function getQuantidadeAtualAttribute()
    {
        // Soma as entradas e subtrai as saídas
        $totalEntradas = $this->movimentacoes()->where('tipo_movimentacao', 'entrada')->sum('quantidade');
        $totalSaidas = $this->movimentacoes()
                            ->whereIn('tipo_movimentacao', ['saida_venda', 'saida_morte', 'saida_consumo', 'saida_doacao', 'saida_descarte', 'outros'])
                            ->sum('quantidade');

        return $totalEntradas - $totalSaidas;
    }
}
