<?php

namespace App\Http\Controllers;

use App\Models\Variacao;
use App\Models\TipoAve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Importa a classe Rule para validação de unicidade

class VariacaoController extends Controller
{
    /**
     * Exibe uma lista de todas as variações.
     */
    public function index()
    {
        $variacoes = Variacao::with('tipoAve')->orderBy('nome')->get();
        return view('variacoes.listar', compact('variacoes'));
    }

    /**
     * Exibe o formulário para criar uma nova variação.
     */
    public function create()
    {
        $tiposAves = TipoAve::where('ativo', 1)->orderBy('nome')->get();
        return view('variacoes.criar', compact('tiposAves'));
    }

    /**
     * Armazena uma nova variação no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados da requisição
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                // AQUI ESTÁ A MUDANÇA: 'unique' com scope para tipo_ave_id
                Rule::unique('variacoes')->where(function ($query) use ($request) {
                    return $query->where('tipo_ave_id', $request->tipo_ave_id);
                }),
            ],
            'tipo_ave_id' => 'required|integer|exists:tipos_aves,id',
            'ativo' => 'boolean',
        ], [
            'nome.required' => 'O nome da variação é obrigatório.',
            'nome.unique' => 'Já existe uma variação com este nome para o tipo de ave selecionado.', // Mensagem mais específica
            'tipo_ave_id.required' => 'O tipo de ave é obrigatório.',
            'tipo_ave_id.exists' => 'O tipo de ave selecionado é inválido.',
        ]);

        try {
            Variacao::create([
                'nome' => $request->nome,
                'tipo_ave_id' => $request->tipo_ave_id,
                'ativo' => $request->has('ativo') ? 1 : 0,
            ]);

            return redirect()->route('variacoes.index')->with('success', 'Variação criada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar variação: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao criar a variação: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar uma variação existente.
     */
    public function edit(string $id)
    {
        $variacao = Variacao::find($id);

        if (!$variacao) {
            return redirect()->route('variacoes.index')->with('error', 'Variação não encontrada.');
        }

        $tiposAves = TipoAve::where('ativo', 1)->orderBy('nome')->get();

        return view('variacoes.editar', compact('variacao', 'tiposAves'));
    }

    /**
     * Atualiza uma variação existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        // Validação dos dados da requisição
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                // AQUI ESTÁ A MUDANÇA: 'unique' com scope para tipo_ave_id, ignorando o ID atual
                Rule::unique('variacoes')->where(function ($query) use ($request) {
                    return $query->where('tipo_ave_id', $request->tipo_ave_id);
                })->ignore($id),
            ],
            'tipo_ave_id' => 'required|integer|exists:tipos_aves,id',
            'ativo' => 'boolean',
        ], [
            'nome.required' => 'O nome da variação é obrigatório.',
            'nome.unique' => 'Já existe uma variação com este nome para o tipo de ave selecionado.', // Mensagem mais específica
            'tipo_ave_id.required' => 'O tipo de ave é obrigatório.',
            'tipo_ave_id.exists' => 'O tipo de ave selecionado é inválido.',
        ]);

        try {
            $variacao = Variacao::find($id);

            if (!$variacao) {
                return redirect()->back()->with('error', 'Variação não encontrada para atualização.');
            }

            $variacao->update([
                'nome' => $request->nome,
                'tipo_ave_id' => $request->tipo_ave_id,
                'ativo' => $request->has('ativo') ? 1 : 0,
            ]);

            return redirect()->route('variacoes.index')->with('success', 'Variação atualizada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar variação: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a variação: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma variação do banco de dados (inativação lógica).
     */
    public function destroy(string $id)
    {
        try {
            $variacao = Variacao::find($id);

            if (!$variacao) {
                return redirect()->route('variacoes.index')->with('error', 'Variação não encontrada para exclusão.');
            }

            // Verifica se há aves ativas associadas a esta variação antes de inativar
            // Consideramos apenas aves ativas, assumindo que aves inativas não impedem a inativação da variação.
            if (Ave::where('variacao_id', $variacao->id)->where('ativo', 1)->count() > 0) {
                return redirect()->route('variacoes.index')->with('error', 'Não é possível inativar esta variação, pois existem aves ATIVAS associadas a ela.');
            }
            
            // Inativação lógica da variação (soft delete)
            $variacao->delete(); // Usa o SoftDeletes

            return redirect()->route('variacoes.index')->with('success', 'Variação inativada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao inativar variação: ' . $e->getMessage(), ['variacao_id' => $id]);
            return redirect()->route('variacoes.index')->with('error', 'Erro ao inativar a variação.');
        }
    }
}
