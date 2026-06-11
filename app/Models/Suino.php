<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suino extends Model
{
    protected $table = 'suinos';
    protected $fillable = ['matricula', 'sexo', 'vendavel', 'ativo', 'data_inativado', 'data_venda'];
    protected $casts = [
        'vendavel' => 'boolean',
        'ativo' => 'boolean',
        'data_inativado' => 'date',
        'data_venda' => 'date',
    ];
}
