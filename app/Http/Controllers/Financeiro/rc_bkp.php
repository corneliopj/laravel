<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Receita; // Importa o modelo Receita
use App\Models\Categoria; // Importa o modelo Categoria para dropdowns
use Illuminate\Http\Request;
use Carbon\Carbon; // Para manipulação de datas

class ReceitaController extends Controller
{
    /**
     * Exibe uma lista de todas as receitas com filtros.
     */
    public function index(Request $request)
    {
        $query = Receita::with('categoria'); // Carrega a relação com Categoria

        // Filtro por categoria
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por período de data
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->whereBetween('data', [$dataInicio, $dataFim]);
        } elseif ($request->filled('data_inicio')) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $query->where('data', '>=', $dataInicio);
        } elseif ($request->filled('data_fim')) {
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->where('data', '<=', $dataFim);
        }

        // Ordena as receitas pela data, do mais recente para o mais antigo
        $receitas = $query->orderBy('data', 'desc')->get();

        // Categorias de receita para o filtro
        $categorias = Categoria::where('tipo', 'receita')->orderBy('nome')->get();

        return view('financeiro.receitas.index', compact('receitas', 'categorias'));
    }

    /**
     * Exibe o formulário para criar uma nova receita.
     */
    public function create()
    {
        // Busca apenas categorias do tipo 'receita'
        $categorias = Categoria::where('tipo', 'receita')->orderBy('nome')->get();
        return view('financeiro.receitas.create', compact('categorias'));
    }

    /**
     * Armazena uma nova receita no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01', // Valor deve ser numérico e positivo
            'categoria_id' => 'required|exists:categorias,id', // Categoria deve existir
            'data' => 'required|date', // Data obrigatória
        ]);

        try {
            // Cria a nova receita com os dados validados
            Receita::create($request->all());
            return redirect()->route('financeiro.receitas.index')->with('success', 'Receita registrada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao registrar receita: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar a receita: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar uma receita existente.
     */
    public function edit(string $id)
    {
        $receita = Receita::findOrFail($id); // Encontra a receita ou falha
        $categorias = Categoria::where('tipo', 'receita')->orderBy('nome')->get(); // Categorias de receita
        return view('financeiro.receitas.edit', compact('receita', 'categorias'));
    }

    /**
     * Atualiza uma receita existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $receita = Receita::findOrFail($id); // Encontra a receita ou falha

        // Validação dos dados de entrada
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'categoria_id' => 'required|exists:categorias,id',
            'data' => 'required|date',
        ]);

        try {
            // Atualiza a receita com os dados validados
            $receita->update($request->all());
            return redirect()->route('financeiro.receitas.index')->with('success', 'Receita atualizada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar receita: ' . $e->getMessage(), ['receita_id' => $id, 'request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a receita: ' . $e->getMessage());
        }
    }

    /**
     * Remove (soft delete) uma receita do banco de dados.
     */
    public function destroy(string $id)
    {
        try {
            $receita = Receita::findOrFail($id); // Encontra a receita ou falha
            $receita->delete(); // Realiza o soft delete
            return redirect()->route('financeiro.receitas.index')->with('success', 'Receita excluída com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir receita: ' . $e->getMessage(), ['receita_id' => $id]);
            return redirect()->route('financeiro.receitas.index')->with('error', 'Erro ao excluir a receita: ' . $e->getMessage());
        }
    }
}

