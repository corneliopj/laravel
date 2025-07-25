<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Despesa; // Importa o modelo Despesa
use App\Models\Categoria; // Importa o modelo Categoria para dropdowns
use Illuminate\Http\Request;
use Carbon\Carbon; // Para manipulação de datas

class DespesaController extends Controller
{
    /**
     * Exibe uma lista de todas as despesas com filtros.
     */
    public function index(Request $request)
    {
        $query = Despesa::with('categoria'); // Carrega a relação com Categoria

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

        // Ordena as despesas pela data, do mais recente para o mais antigo
        $despesas = $query->orderBy('data', 'desc')->get();

        // Categorias de despesa para o filtro
        $categorias = Categoria::where('tipo', 'despesa')->orderBy('nome')->get();

        return view('financeiro.despesas.index', compact('despesas', 'categorias'));
    }

    /**
     * Exibe o formulário para criar uma nova despesa.
     */
    public function create()
    {
        // Busca apenas categorias do tipo 'despesa'
        $categorias = Categoria::where('tipo', 'despesa')->orderBy('nome')->get();
        return view('financeiro.despesas.create', compact('categorias'));
    }

    /**
     * Armazena uma nova despesa no banco de dados.
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
            // Cria a nova despesa com os dados validados
            Despesa::create($request->all());
            return redirect()->route('financeiro.despesas.index')->with('success', 'Despesa registrada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao registrar despesa: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar a despesa: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar uma despesa existente.
     */
    public function edit(string $id)
    {
        $despesa = Despesa::findOrFail($id); // Encontra a despesa ou falha
        $categorias = Categoria::where('tipo', 'despesa')->orderBy('nome')->get(); // Categorias de despesa
        return view('financeiro.despesas.edit', compact('despesa', 'categorias'));
    }

    /**
     * Atualiza uma despesa existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $despesa = Despesa::findOrFail($id); // Encontra a despesa ou falha

        // Validação dos dados de entrada
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'categoria_id' => 'required|exists:categorias,id',
            'data' => 'required|date',
        ]);

        try {
            // Atualiza a despesa com os dados validados
            $despesa->update($request->all());
            return redirect()->route('financeiro.despesas.index')->with('success', 'Despesa atualizada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar despesa: ' . $e->getMessage(), ['despesa_id' => $id, 'request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a despesa: ' . $e->getMessage());
        }
    }

    /**
     * Remove (soft delete) uma despesa do banco de dados.
     */
    public function destroy(string $id)
    {
        try {
            $despesa = Despesa::findOrFail($id); // Encontra a despesa ou falha
            $despesa->delete(); // Realiza o soft delete
            return redirect()->route('financeiro.despesas.index')->with('success', 'Despesa excluída com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir despesa: ' . $e->getMessage(), ['despesa_id' => $id]);
            return redirect()->route('financeiro.despesas.index')->with('error', 'Erro ao excluir a despesa: ' . $e->getMessage());
        }
    }
}

