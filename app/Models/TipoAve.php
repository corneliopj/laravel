<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAve extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'tipos_aves';

    /**
     * Os atributos que são massivamente atribuíveis.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'ativo',
        'tempo_eclosao', // NOVO: Adicionado tempo_eclosao
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ativo' => 'boolean',
        'tempo_eclosao' => 'integer', // NOVO: Adicionado cast para integer
    ];

    /**
     * Obtém as aves para o tipo de ave.
     */
    public function aves()
    {
        return $this->hasMany(Ave::class);
    }
}
