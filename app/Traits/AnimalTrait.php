<?php

namespace App\Traits;

use App\Models\Lote;
use App\Models\Variacao;
use App\Models\Morte;

trait AnimalTrait
{
    /**
     * Relacionamento com o Lote.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    /**
     * Relacionamento com a Variação.
     */
    public function variacao()
    {
        return $this->belongsTo(Variacao::class, 'variacao_id');
    }

    /**
     * Relacionamento com os registros de Morte.
     */
    public function mortes()
    {
        // Usamos hasMany para manter o histórico de eventos de morte/auditoria
        return $this->hasMany(Morte::class, 'animal_id');
    }
}
