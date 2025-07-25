<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Morte extends Model
{
    use HasFactory;

    // Define o nome da tabela se ele não for o plural do nome do modelo (Laravel tenta adivinhar 'mortes' para 'Morte')
    protected $table = 'mortes';

    // Define as colunas que podem ser preenchidas massivamente
    protected $fillable = [
        'ave_id',
        'data_morte',
        'causa',
        'observacoes',
    ];

    // Define os tipos de dados para colunas específicas
    protected $casts = [
        'data_morte' => 'date',
    ];

    // Define as relações do modelo

    // Relação com Ave (Uma morte pertence a uma ave)
    public function ave()
    {
        return $this->belongsTo(Ave::class);
    }
}
