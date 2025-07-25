<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Morte; // NOVO: Importa o modelo Morte

class Ave extends Model
{
    use HasFactory, SoftDeletes;

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
    public function tipoAve()
    {
        return $this->belongsTo(TipoAve::class, 'tipo_ave_id');
    }

    /**
     * Uma ave pertence a uma variação.
     */
    public function variacao()
    {
        return $this->belongsTo(Variacao::class, 'variacao_id');
    }

    /**
     * Uma ave pertence a um lote.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    /**
     * Uma ave pertence a uma incubação.
     */
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
    }

    /**
     * Uma ave pode ter várias ocorrências de morte registradas.
     * (Isso permite que a tabela 'mortes' seja um log de eventos de morte,
     * onde cada registro de morte se refere a uma ave. Embora uma ave só morra uma vez,
     * a estrutura de dados pode permitir múltiplos registros para fins de auditoria ou
     * se 'morte' representa um evento de "registro de morte" em vez do estado final da ave.)
     * Se uma ave só pode ter UMA morte registrada, o relacionamento correto seria hasOne.
     * No entanto, o erro indica que o código está procurando por 'mortes' (plural),
     * então definimos como hasMany para resolver o erro imediato.
     */
    public function mortes()
    {
        return $this->hasMany(Morte::class, 'ave_id');
    }
}
