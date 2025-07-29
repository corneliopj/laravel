<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoPlantel extends Model
{
    use HasFactory;

    protected $table = 'movimentacoes_plantel'; // Define o nome da tabela explicitamente

    protected $fillable = [
        'plantel_id',
        'tipo_movimentacao',
        'quantidade',
        'data_movimentacao',
        'observacoes',
    ];

    protected $casts = [
        'data_movimentacao' => 'date',
    ];

    // Relacionamento com Plantel
    public function plantel()
    {
        return $this->belongsTo(Plantel::class);
    }
}
