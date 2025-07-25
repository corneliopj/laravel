<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Importa o modelo User

class Contracheque extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'contracheques';

    /**
     * Os atributos que são preenchíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'data',
        'tipo_lancamento',
        'valor',
        'descricao',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'date',
        'valor' => 'decimal:2',
    ];

    /**
     * Obtém o usuário ao qual este contracheque pertence.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
