<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Morte extends Model
{
    use HasFactory;

    protected $table = 'mortes'; // Assegura o nome correto da tabela

    protected $fillable = [
        'ave_id', 
        'plantel_id', 
        'quantidade_mortes_plantel', 
        'data_morte',
        'causa',
        'observacoes',
    ];

    protected $casts = [
        'data_morte' => 'date',
        'quantidade_mortes_plantel' => 'integer',
    ];

    /**
     * Relacionamento: Uma morte pode pertencer a uma Ave individual.
     */
    public function ave()
    {
        return $this->belongsTo(Ave::class, 'ave_id');
    }

    /**
     * Relacionamento: Uma morte pode pertencer a um Plantel agrupado.
     */
    public function plantel()
    {
        return $this->belongsTo(Plantel::class, 'plantel_id');
    }
}
