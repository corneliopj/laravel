<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Se você planeja usar soft deletes para variações

class Variacao extends Model
{
    use HasFactory, SoftDeletes; // Adicione SoftDeletes se você quiser

    protected $table = 'variacoes'; // Nome da tabela no banco de dados

    protected $fillable = [
        'nome',
        'tipo_ave_id',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Uma variação pertence a um Tipo de Ave.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoAve()
    {
        return $this->belongsTo(TipoAve::class, 'tipo_ave_id');
    }

    /**
     * Uma variação pode ter muitas aves associadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aves()
    {
        return $this->hasMany(Ave::class, 'variacao_id');
    }
}
