<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    // Define o nome da tabela se ele não for o plural do nome do modelo (Laravel tenta adivinhar 'lotes' para 'Lote')
    protected $table = 'lotes';

    // Define as colunas que podem ser preenchidas massivamente
    protected $fillable = [
        'identificacao_lote',
        'observacoes',
        'ativo',
    ];

    // Define os tipos de dados para colunas específicas
    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Define as relações do modelo

    // Relação com Ave (Um lote pode ter muitas aves)
    public function aves()
    {
        return $this->hasMany(Ave::class);
    }

    // Relação com Incubacao (Um lote pode ter muitas incubações de ovos)
    public function incubacoes()
    {
        return $this->hasMany(Incubacao::class, 'lote_ovos_id');
    }
}
