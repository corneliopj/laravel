<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Morte extends Model
{
    use HasFactory;

    protected $table = 'mortes'; // Assegura o nome correto da tabela

    protected $fillable = [
        'ave_id', // Chave estrangeira para ave individual (pode ser nulo)
        'plantel_id', // Chave estrangeira para plantel agrupado (pode ser nulo)
        'quantidade_mortes_plantel', // Quantidade de aves mortas se for um registro de plantel
        'data_morte',
        'causa_morte',
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
