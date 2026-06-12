<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Morte; // NOVO: Importa o modelo Morte

class Ave extends Model
{
    use HasFactory, SoftDeletes, \App\Traits\AnimalTrait;


    protected $table = 'aves';

    protected $fillable = [
        'matricula',
        'tipo_ave_id',
        'variacao_id',
        'lote_id',
        'incubacao_id',
        'data_eclosao',
        'sexo',
        'foto_path',
        'codigo_validacao_certidao',
        'vendavel',
        'ativo',
        'data_inativado',
    ];

    protected $casts = [
        'data_eclosao' => 'date',
        'vendavel' => 'boolean',
        'ativo' => 'boolean',
        'data_inativado' => 'datetime',
    ];

    /**
     * Uma ave pertence a um tipo de ave.
     */
    /**
     * Uma ave pertence a um tipo de ave.
     */
    public function tipoAve()
    {
        return $this->belongsTo(TipoAve::class, 'tipo_ave_id');
    }

    public function incubacao()
    {
        return $this->belongsTo(Incubacao::class, 'incubacao_id');
    }

    /**
     * Uma ave pode ser um item em várias vendas.
     */
    public function vendaItems()
    {
        return $this->hasMany(VendaItem::class, 'ave_id');
    }}
