<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Categoria; // Importa o modelo Categoria
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Importa Rule para validação de unicidade

class CategoriaController extends Controller
{
    /**
     * Exibe uma lista de todas as categorias.
     */
    public function index()
    {
        // Busca todas as categorias, ordenadas por tipo e nome
        $categorias = Categoria::orderBy('tipo')->orderBy('nome')->get();
        // Retorna a view de listagem de categorias, passando os dados
        return view('financeiro.categorias.index', compact('categorias'));
    }

    /**
     * Exibe o formulário para criar uma nova categoria.
     */
    public function create()
    {
        return view('financeiro.categorias.create');
    }

    /**
     * Armazena uma nova categoria no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                // Garante que o nome seja único para o mesmo tipo de categoria
                Rule::unique('categorias')->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo);
                }),
            ],
            'tipo' => 'required|in:receita,despesa', // O tipo deve ser 'receita' ou 'despesa'
        ], [
            'nome.unique' => 'Já existe uma categoria com este nome para o tipo selecionado.',
            'tipo.in' => 'O tipo de categoria deve ser "receita" ou "despesa".',
        ]);

        try {
            // Cria a nova categoria com os dados validados
            Categoria::create($request->all());
            return redirect()->route('financeiro.categorias.index')->with('success', 'Categoria criada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar categoria: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao criar a categoria: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar uma categoria existente.
     */
    public function edit(string $id)
    {
        $categoria = Categoria::findOrFail($id); // Encontra a categoria ou falha
        return view('financeiro.categorias.edit', compact('categoria'));
    }

    /**
     * Atualiza uma categoria existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $categoria = Categoria::findOrFail($id); // Encontra a categoria ou falha

        // Validação dos dados de entrada
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                // Garante que o nome seja único para o mesmo tipo, ignorando a categoria atual
                Rule::unique('categorias')->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo);
                })->ignore($categoria->id),
            ],
            'tipo' => 'required|in:receita,despesa',
        ], [
            'nome.unique' => 'Já existe uma categoria com este nome para o tipo selecionado.',
            'tipo.in' => 'O tipo de categoria deve ser "receita" ou "despesa".',
        ]);

        try {
            // Atualiza a categoria com os dados validados
            $categoria->update($request->all());
            return redirect()->route('financeiro.categorias.index')->with('success', 'Categoria atualizada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar categoria: ' . $e->getMessage(), ['categoria_id' => $id, 'request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a categoria: ' . $e->getMessage());
        }
    }

    /**
     * Remove (soft delete) uma categoria do banco de dados.
     */
    public function destroy(string $id)
    {
        try {
            $categoria = Categoria::findOrFail($id); // Encontra a categoria ou falha

            // Verifica se a categoria tem receitas ou despesas associadas
            if ($categoria->receitas()->count() > 0 || $categoria->despesas()->count() > 0) {
                return redirect()->route('financeiro.categorias.index')->with('error', 'Não é possível excluir esta categoria, pois existem receitas ou despesas associadas a ela.');
            }

            // Realiza o soft delete
            $categoria->delete();
            return redirect()->route('financeiro.categorias.index')->with('success', 'Categoria excluída com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir categoria: ' . $e->getMessage(), ['categoria_id' => $id]);
            return redirect()->route('financeiro.categorias.index')->with('error', 'Erro ao excluir a categoria: ' . $e->getMessage());
        }
    }
}

