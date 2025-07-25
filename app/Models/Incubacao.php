<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incubacao extends Model
{
    use HasFactory;

    protected $table = 'incubacoes'; // O nome correto da sua tabela

    protected $fillable = [
        'lote_ovos_id',
        'tipo_ave_id',
        'data_entrada_incubadora',
        'data_prevista_eclosao',
        'quantidade_ovos',
        'quantidade_eclodidos',
        'observacoes',
        'ativo',
        'postura_ovo_id',
        'chocadeira',             // NOVO: Adicionado chocadeira
        'quantidade_inferteis',   // NOVO: Adicionado quantidade_inferteis
        'quantidade_infectados',  // NOVO: Adicionado quantidade_infectados
        'quantidade_mortos',      // NOVO: Adicionado quantidade_mortos
    ];

    protected $casts = [
        'data_entrada_incubadora' => 'date',
        'data_prevista_eclosao' => 'date',
        'quantidade_inferteis' => 'integer',   // NOVO: Cast para inteiro
        'quantidade_infectados' => 'integer',  // NOVO: Cast para inteiro
        'quantidade_mortos' => 'integer',      // NOVO: Cast para inteiro
        'ativo' => 'boolean',
    ];

    /**
     * Get the lote that owns the incubacao.
     * Mapeia lote_ovos_id no DB para a relação 'lote'.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_ovos_id');
    }

    /**
     * Get the tipoAve that owns the incubacao.
     */
    public function tipoAve()
    {
        return $this->belongsTo(TipoAve::class, 'tipo_ave_id');
    }

    /**
     * Get the posturaOvo that owns the incubacao.
     */
    public function posturaOvo()
    {
        return $this->belongsTo(PosturaOvo::class, 'postura_ovo_id');
    }

    /**
     * Get the aves for the incubacao.
     * Uma incubação pode gerar muitas aves.
     */
    public function aves()
    {
        return $this->hasMany(Ave::class, 'incubacao_id');
    }
}

