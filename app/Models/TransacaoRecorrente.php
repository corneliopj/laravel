<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransacaoRecorrente extends Model
{
    use HasFactory;

    protected $table = 'transacoes_recorrentes';

    protected $fillable = [
        'descricao',
        'tipo',
        'valor',
        'categoria_id',
        'frequencia',
        'data_inicio',
        'data_fim',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'valor' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento com Categoria
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Escopo para transações ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Escopo para transações de receita
     */
    public function scopeReceitas($query)
    {
        return $query->where('tipo', 'receita');
    }

    /**
     * Escopo para transações de despesa
     */
    public function scopeDespesas($query)
    {
        return $query->where('tipo', 'despesa');
    }

    /**
     * Verificar se a transação deve ser executada em uma data específica
     */
    public function deveExecutarEm(Carbon $data): bool
    {
        $dataInicio = Carbon::parse($this->data_inicio);
        
        // Se a data é anterior ao início da recorrência
        if ($data->lt($dataInicio)) {
            return false;
        }
        
        // Se há data fim e a data é posterior
        if ($this->data_fim && $data->gt(Carbon::parse($this->data_fim))) {
            return false;
        }
        
        // Verificar frequência
        switch ($this->frequencia) {
            case 'diaria':
                return true;
                
            case 'semanal':
                return $data->dayOfWeek == $dataInicio->dayOfWeek;
                
            case 'mensal':
                return $data->day == $dataInicio->day;
                
            case 'anual':
                return $data->month == $dataInicio->month && $data->day == $dataInicio->day;
                
            default:
                return false;
        }
    }

    /**
     * Obter próximas execuções da transação
     */
    public function proximasExecucoes(int $dias = 30): array
    {
        $execucoes = [];
        $dataInicio = Carbon::now()->startOfDay();
        $dataFim = Carbon::now()->addDays($dias)->endOfDay();
        
        for ($data = clone $dataInicio; $data->lte($dataFim); $data->addDay()) {
            if ($this->deveExecutarEm($data)) {
                $execucoes[] = [
                    'data' => $data->format('Y-m-d'),
                    'data_formatada' => $data->format('d/m/Y'),
                    'descricao' => $this->descricao,
                    'valor' => $this->valor,
                    'tipo' => $this->tipo
                ];
            }
        }
        
        return $execucoes;
    }

    /**
     * Calcular valor total projetado para um período
     */
    public function valorProjetado(int $dias = 30): float
    {
        $total = 0;
        $dataInicio = Carbon::now()->startOfDay();
        $dataFim = Carbon::now()->addDays($dias)->endOfDay();
        
        for ($data = clone $dataInicio; $data->lte($dataFim); $data->addDay()) {
            if ($this->deveExecutarEm($data)) {
                $total += $this->valor;
            }
        }
        
        return $total;
    }
}

