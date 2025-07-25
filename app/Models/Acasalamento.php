<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acasalamento extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acasalamentos'; // CORRIGIDO: Nome da tabela para 'acasalamentos'

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'macho_id',
        'femea_id',
        'data_inicio',
        'data_fim',
        'observacoes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /**
     * Get the male Ave for the acasalamento.
     */
    public function macho()
    {
        return $this->belongsTo(Ave::class, 'macho_id');
    }

    /**
     * Get the female Ave for the acasalamento.
     */
    public function femea()
    {
        return $this->belongsTo(Ave::class, 'femea_id');
    }

    /**
     * Um acasalamento pode ter muitas posturas de ovos.
     * NOVO: Adicionada a relação inversa para PosturaOvo
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posturasOvos()
    {
        return $this->hasMany(PosturaOvo::class, 'acasalamento_id');
    }
}

