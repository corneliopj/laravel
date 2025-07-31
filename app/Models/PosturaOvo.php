<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosturaOvo extends Model
{
    use HasFactory;

    protected $table = 'posturas_ovos';

    protected $fillable = [
        'lote_id',
        'tipo_ave_id',
        'acasalamento_id',
        'data_postura',
        'quantidade_ovos',
        'observacoes',
        'encerrada', // Reutilizando este campo para status
    ];

    protected $casts = [
        'data_postura' => 'date',
        'quantidade_ovos' => 'integer',
        'encerrada' => 'boolean', // Cast para boolean
    ];

    /**
     * Get the lote that owns the posturaOvo.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    /**
     * Get the tipoAve that owns the posturaOvo.
     */
    public function tipoAve()
    {
        return $this->belongsTo(TipoAve::class, 'tipo_ave_id');
    }

    /**
     * Get the acasalamento that owns the posturaOvo.
     */
    public function acasalamento()
    {
        return $this->belongsTo(Acasalamento::class, 'acasalamento_id');
    }

    /**
     * Define um escopo para retornar apenas posturas de ovos ativas (não encerradas).
     * O nome do método deve começar com 'scope' seguido pelo nome do escopo em CamelCase.
     * Ex: scopeAtivas -> $query->ativas()
     */
    public function scopeAtivas($query)
    {
        return $query->where('encerrada', false);
    }
}
