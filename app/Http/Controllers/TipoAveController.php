<?php

namespace App\Http\Controllers;

use App\Models\TipoAve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Importa a classe Rule para validação de unicidade

class TipoAveController extends Controller
{
    /**
     * Exibe uma lista de todos os tipos de aves.
     */
    public function index()
    {
        // Busca todos os tipos de aves, ordenados pelo nome
        $tiposAves = TipoAve::orderBy('nome')->get();
        
        // Retorna a view de listagem de tipos de aves, passando os dados
        return view('tipos_aves.listar', compact('tiposAves'));
    }

    /**
     * Exibe o formulário para criar um novo tipo de ave.
     */
    public function create()
    {
        return view('tipos_aves.criar');
    }

    /**
     * Armazena um novo tipo de ave no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados da requisição
        $request->validate([
            'nome' => 'required|string|max:255|unique:tipos_aves,nome', // Nome obrigatório, string, único
            'ativo' => 'boolean', // Ativo é um booleano (opcional, padrão 1 no DB)
            'tempo_eclosao' => 'nullable|integer|min:1', // NOVO: Tempo de eclosão (inteiro, mínimo 1, opcional)
        ], [
            'nome.required' => 'O nome do tipo de ave é obrigatório.',
            'nome.unique' => 'Este nome de tipo de ave já existe.',
            'tempo_eclosao.integer' => 'O tempo de eclosão deve ser um número inteiro.',
            'tempo_eclosao.min' => 'O tempo de eclosão deve ser no mínimo 1 dia.',
        ]);

        try {
            // Cria o tipo de ave usando o Eloquent ORM
            TipoAve::create([
                'nome' => $request->nome,
                'ativo' => $request->has('ativo') ? 1 : 0, // checkbox
                'tempo_eclosao' => $request->tempo_eclosao, // NOVO: Salva o tempo de eclosão
            ]);

            // Redireciona com mensagem de sucesso
            return redirect()->route('tipos_aves.index')->with('success', 'Tipo de ave criado com sucesso!');

        } catch (\Exception $e) {
            // Log do erro para depuração
            \Log::error('Erro ao criar tipo de ave: ' . $e->getMessage(), ['request_data' => $request->all()]);
            // Redireciona de volta com mensagem de erro
            return redirect()->back()->withInput()->with('error', 'Erro ao criar o tipo de ave: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar um tipo de ave existente.
     */
    public function edit(string $id)
    {
        $tipoAve = TipoAve::find($id); // Busca o tipo de ave pelo ID

        if (!$tipoAve) {
            return redirect()->route('tipos_aves.index')->with('error', 'Tipo de ave não encontrado.');
        }

        return view('tipos_aves.editar', compact('tipoAve'));
    }

    /**
     * Atualiza um tipo de ave existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        // Validação dos dados da requisição
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tipos_aves')->ignore($id), // Nome único, ignorando o ID atual
            ],
            'ativo' => 'boolean',
            'tempo_eclosao' => 'nullable|integer|min:1', // NOVO: Tempo de eclosão (inteiro, mínimo 1, opcional)
        ], [
            'nome.required' => 'O nome do tipo de ave é obrigatório.',
            'nome.unique' => 'Este nome de tipo de ave já existe.',
            'tempo_eclosao.integer' => 'O tempo de eclosão deve ser um número inteiro.',
            'tempo_eclosao.min' => 'O tempo de eclosão deve ser no mínimo 1 dia.',
        ]);

        try {
            $tipoAve = TipoAve::find($id);

            if (!$tipoAve) {
                return redirect()->back()->with('error', 'Tipo de ave não encontrado para atualização.');
            }

            $tipoAve->update([
                'nome' => $request->nome,
                'ativo' => $request->has('ativo') ? 1 : 0,
                'tempo_eclosao' => $request->tempo_eclosao, // NOVO: Atualiza o tempo de eclosão
            ]);

            return redirect()->route('tipos_aves.index')->with('success', 'Tipo de ave atualizado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar tipo de ave: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar o tipo de ave: ' . $e->getMessage());
        }
    }

    /**
     * Remove um tipo de ave do banco de dados (inativação lógica).
     */
    public function destroy(string $id)
    {
        try {
            $tipoAve = TipoAve::find($id);

            if (!$tipoAve) {
                return redirect()->route('tipos_aves.index')->with('error', 'Tipo de ave não encontrado para exclusão.');
            }

            // Verifica se há aves associadas a este tipo antes de inativar
            if ($tipoAve->aves()->count() > 0) {
                return redirect()->route('tipos_aves.index')->with('error', 'Não é possível inativar este tipo de ave, pois existem aves associadas a ele.');
            }

            // Inativação lógica do tipo de ave
            $tipoAve->update([
                'ativo' => 0,
            ]);

            return redirect()->route('tipos_aves.index')->with('success', 'Tipo de ave inativado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao inativar tipo de ave: ' . $e->getMessage(), ['tipo_ave_id' => $id]);
            return redirect()->route('tipos_aves.index')->with('error', 'Erro ao inativar o tipo de ave.');
        }
    }
}
