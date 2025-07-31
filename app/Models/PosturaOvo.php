<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosturaOvo extends Model
{
    use HasFactory;

    protected $table = 'posturas_ovos';

    protected $fillable = [
        'acasalamento_id',
        'data_inicio_postura', // Nome correto da coluna de data
        'data_fim_postura',
        'quantidade_ovos',
        'observacoes',
        'encerrada', // Campo para indicar se a postura está ativa/encerrada
    ];

    protected $casts = [
        'data_inicio_postura' => 'date', // Cast para o nome correto da coluna
        'data_fim_postura' => 'date',
        'quantidade_ovos' => 'integer',
        'encerrada' => 'boolean', // Cast para boolean
    ];

    /**
     * Get the Acasalamento that owns the PosturaOvo.
     */
    public function acasalamento()
    {
        return $this->belongsTo(Acasalamento::class);
    }

    /**
     * Get the Incubacoes for the PosturaOvo.
     */
    public function incubacoes()
    {
        return $this->hasMany(Incubacao::class, 'postura_ovo_id');
    }

    /**
     * Define um escopo para retornar apenas posturas de ovos ativas (não encerradas).
     */
    public function scopeAtivas($query)
    {
        return $query->where('encerrada', false);
    }
}
