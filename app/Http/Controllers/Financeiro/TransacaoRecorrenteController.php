<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\TransacaoRecorrente; // Importa o modelo TransacaoRecorrente
use App\Models\Categoria; // Importa o modelo Categoria para dropdowns
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Para validação de regras específicas
use Carbon\Carbon; // Para manipulação de datas

class TransacaoRecorrenteController extends Controller
{
    /**
     * Exibe uma lista de todas as transações recorrentes.
     */
    public function index(Request $request)
    {
        $query = TransacaoRecorrente::with('categoria')->orderBy('next_due_date', 'asc');

        // Filtro por tipo (receita/despesa)
        if ($request->filled('tipo')) {
            $query->where('type', $request->tipo);
        }

        // Filtro por frequência
        if ($request->filled('frequencia')) {
            $query->where('frequency', $request->frequencia);
        }

        $transacoesRecorrentes = $query->get();

        // Para os filtros na view
        $tipos = ['receita' => 'Receita', 'despesa' => 'Despesa'];
        $frequencias = [
            'daily' => 'Diária',
            'weekly' => 'Semanal',
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual'
        ];

        return view('financeiro.transacoes_recorrentes.index', compact(
            'transacoesRecorrentes',
            'tipos',
            'frequencias',
            'request' // Passa o objeto request para manter os filtros selecionados
        ));
    }

    /**
     * Exibe o formulário para criar uma nova transação recorrente.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nome')->get(); // Busca todas as categorias
        $frequencias = [
            'daily' => 'Diária',
            'weekly' => 'Semanal',
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual'
        ];
        return view('financeiro.transacoes_recorrentes.create', compact('categorias', 'frequencias'));
    }

    /**
     * Armazena uma nova transação recorrente no banco de dados.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'value' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categorias,id',
            'type' => ['required', Rule::in(['receita', 'despesa'])],
            'frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $transacaoRecorrente = TransacaoRecorrente::create($validatedData);

            // Define a próxima data de vencimento inicial
            $transacaoRecorrente->next_due_date = Carbon::parse($transacaoRecorrente->start_date);
            $transacaoRecorrente->save();

            return redirect()->route('financeiro.transacoes_recorrentes.index')->with('success', 'Transação recorrente criada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar transação recorrente: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao criar a transação recorrente: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de uma transação recorrente específica.
     */
    public function show(string $id)
    {
        $transacaoRecorrente = TransacaoRecorrente::with('categoria')->findOrFail($id);
        return view('financeiro.transacoes_recorrentes.show', compact('transacaoRecorrente'));
    }

    /**
     * Exibe o formulário para editar uma transação recorrente existente.
     */
    public function edit(string $id)
    {
        $transacaoRecorrente = TransacaoRecorrente::findOrFail($id);
        $categorias = Categoria::orderBy('nome')->get();
        $frequencias = [
            'daily' => 'Diária',
            'weekly' => 'Semanal',
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual'
        ];
        return view('financeiro.transacoes_recorrentes.edit', compact('transacaoRecorrente', 'categorias', 'frequencias'));
    }

    /**
     * Atualiza uma transação recorrente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $transacaoRecorrente = TransacaoRecorrente::findOrFail($id);

        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'value' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categorias,id',
            'type' => ['required', Rule::in(['receita', 'despesa'])],
            'frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $transacaoRecorrente->update($validatedData);

            // Recalcula a próxima data de vencimento se a data de início ou frequência mudarem
            if ($transacaoRecorrente->isDirty('start_date') || $transacaoRecorrente->isDirty('frequency')) {
                $transacaoRecorrente->next_due_date = $transacaoRecorrente->calculateNextDueDate();
                $transacaoRecorrente->save();
            }

            return redirect()->route('financeiro.transacoes_recorrentes.index')->with('success', 'Transação recorrente atualizada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar transação recorrente: ' . $e->getMessage(), ['transacao_recorrente_id' => $id, 'request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a transação recorrente: ' . $e->getMessage());
        }
    }

    /**
     * Remove (soft delete) uma transação recorrente do banco de dados.
     */
    public function destroy(string $id)
    {
        try {
            $transacaoRecorrente = TransacaoRecorrente::findOrFail($id);
            $transacaoRecorrente->delete(); // Realiza o soft delete
            return redirect()->route('financeiro.transacoes_recorrentes.index')->with('success', 'Transação recorrente excluída com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir transação recorrente: ' . $e->getMessage(), ['transacao_recorrente_id' => $id]);
            return redirect()->route('financeiro.transacoes_recorrentes.index')->with('error', 'Erro ao excluir a transação recorrente: ' . $e->getMessage());
        }
    }
}
