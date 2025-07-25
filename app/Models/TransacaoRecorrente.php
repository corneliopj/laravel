<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa a trait SoftDeletes
use Carbon\Carbon; // Importa Carbon para manipulação de datas

class TransacaoRecorrente extends Model
{
    use HasFactory, SoftDeletes; // Usa a trait SoftDeletes

    protected $table = 'recurring_transactions'; // Nome da tabela no banco de dados (mantém o nome da tabela em inglês no DB)

    protected $fillable = [
        'description', // Descrição da transação
        'value',       // Valor da transação
        'category_id', // ID da categoria associada
        'type',        // Tipo da transação ('receita' ou 'despesa')
        'frequency',   // Frequência da recorrência ('daily', 'weekly', 'monthly', 'quarterly', 'yearly')
        'start_date',  // Data de início da recorrência
        'end_date',    // Data de fim da recorrência (opcional)
        'next_due_date', // Próxima data de vencimento para geração
        'last_generated_date', // Última data em que a transação foi gerada
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
        'last_generated_date' => 'date',
    ];

    /**
     * Uma transação recorrente pertence a uma categoria.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'category_id');
    }

    /**
     * Gera a próxima data de vencimento com base na frequência.
     *
     * @return \Carbon\Carbon|null
     */
    public function calculateNextDueDate()
    {
        // Se já existe uma próxima data de vencimento, usa ela como base, senão usa a data de início
        $currentDate = $this->next_due_date ?? Carbon::parse($this->start_date);

        if (!$currentDate) {
            return null;
        }

        // Calcula a próxima data com base na frequência
        switch ($this->frequency) {
            case 'daily':
                return $currentDate->addDay();
            case 'weekly':
                return $currentDate->addWeek();
            case 'monthly':
                return $currentDate->addMonth();
            case 'quarterly':
                return $currentDate->addMonths(3);
            case 'yearly':
                return $currentDate->addYear();
            default:
                return null; // Retorna nulo se a frequência for inválida
        }
    }
}
