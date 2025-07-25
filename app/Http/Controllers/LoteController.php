<?php

namespace App\Http\Controllers;

use App\Models\Lote; // Importa o Model Lote
use Illuminate\Http\Request; // Importa a classe Request para lidar com requisições HTTP
use Illuminate\Validation\Rule; // Importa a classe Rule para validação de unicidade

class LoteController extends Controller
{
    /**
     * Exibe uma lista de todos os lotes.
     */
    public function index()
    {
        // Busca todos os lotes, ordenados pela identificação do lote
        $lotes = Lote::orderBy('identificacao_lote')->get();
        
        // Retorna a view de listagem de lotes, passando os dados
        return view('lotes.listar', compact('lotes'));
    }

    /**
     * Exibe o formulário para criar um novo lote.
     */
    public function create()
    {
        return view('lotes.criar');
    }

    /**
     * Armazena um novo lote no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados da requisição
        $request->validate([
            'identificacao_lote' => 'required|string|max:255|unique:lotes,identificacao_lote', // Obrigatório, string, único
            'observacoes' => 'nullable|string', // Opcional, texto
            'ativo' => 'boolean', // Ativo é um booleano (opcional, padrão 1 no DB)
        ], [
            'identificacao_lote.required' => 'A identificação do lote é obrigatória.',
            'identificacao_lote.unique' => 'Esta identificação de lote já existe.',
        ]);

        try {
            // Cria o lote usando o Eloquent ORM
            Lote::create([
                'identificacao_lote' => $request->identificacao_lote,
                'observacoes' => $request->observacoes,
                'ativo' => $request->has('ativo') ? 1 : 0, // checkbox
            ]);

            // Redireciona com mensagem de sucesso
            return redirect()->route('lotes.index')->with('success', 'Lote criado com sucesso!');

        } catch (\Exception $e) {
            // Log do erro para depuração
            \Log::error('Erro ao criar lote: ' . $e->getMessage(), ['request_data' => $request->all()]);
            // Redireciona de volta com mensagem de erro
            return redirect()->back()->withInput()->with('error', 'Erro ao criar o lote: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar um lote existente.
     */
    public function edit(string $id)
    {
        $lote = Lote::find($id); // Busca o lote pelo ID

        if (!$lote) {
            return redirect()->route('lotes.index')->with('error', 'Lote não encontrado.');
        }

        return view('lotes.editar', compact('lote'));
    }

    /**
     * Atualiza um lote existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        // Validação dos dados da requisição
        $request->validate([
            'identificacao_lote' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lotes')->ignore($id, 'id'), // Nome único, ignorando o ID atual
            ],
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ], [
            'identificacao_lote.required' => 'A identificação do lote é obrigatória.',
            'identificacao_lote.unique' => 'Esta identificação de lote já existe.',
        ]);

        try {
            $lote = Lote::find($id);

            if (!$lote) {
                return redirect()->back()->with('error', 'Lote não encontrado para atualização.');
            }

            $lote->update([
                'identificacao_lote' => $request->identificacao_lote,
                'observacoes' => $request->observacoes,
                'ativo' => $request->has('ativo') ? 1 : 0,
            ]);

            return redirect()->route('lotes.index')->with('success', 'Lote atualizado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar lote: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar o lote: ' . $e->getMessage());
        }
    }

    /**
     * Remove um lote do banco de dados (inativação lógica).
     */
    public function destroy(string $id)
    {
        try {
            $lote = Lote::find($id);

            if (!$lote) {
                return redirect()->route('lotes.index')->with('error', 'Lote não encontrado para exclusão.');
            }

            // Verifica se há aves ou incubações associadas a este lote antes de inativar
            if ($lote->aves()->count() > 0 || $lote->incubacoes()->count() > 0) {
                return redirect()->route('lotes.index')->with('error', 'Não é possível inativar este lote, pois existem aves ou incubações associadas a ele.');
            }

            // Inativação lógica do lote
            $lote->update([
                'ativo' => 0,
            ]);

            return redirect()->route('lotes.index')->with('success', 'Lote inativado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao inativar lote: ' . $e->getMessage(), ['lote_id' => $id]);
            return redirect()->route('lotes.index')->with('error', 'Erro ao inativar o lote.');
        }
    }
}
