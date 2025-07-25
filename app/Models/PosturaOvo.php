<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosturaOvo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posturas_ovos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'acasalamento_id',
        'data_inicio_postura',
        'data_fim_postura',
        'quantidade_ovos',
        'observacoes',
        'encerrada', // NOVO: Adicionado campo 'encerrada'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_inicio_postura' => 'date',
        'data_fim_postura' => 'date',
        'encerrada' => 'boolean', // NOVO: Adicionado cast para boolean
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incubacoes()
    {
        return $this->hasMany(Incubacao::class, 'postura_ovo_id');
    }
}
