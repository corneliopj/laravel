<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Suino extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricula',
        'sexo',
        'vendavel',
        'ativo',
        'data_inativado',
        'data_venda',
    ];
}
