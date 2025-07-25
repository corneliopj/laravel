<?php

namespace App\Http\Controllers;

use App\Models\Acasalamento;
use App\Models\Ave;
use App\Models\TipoAve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcasalamentoController extends Controller
{
    /**
     * Exibe uma lista de todos os acasalamentos.
     */
    public function index()
    {
        // Carrega os acasalamentos com as relações macho e femea
        $acasalamentos = Acasalamento::with(['macho', 'femea'])->orderBy('data_inicio', 'desc')->get();
        return view('acasalamentos.listar', compact('acasalamentos'));
    }

    /**
     * Exibe o formulário para criar um novo acasalamento.
     */
    public function create()
    {
        // Busca aves machos e fêmeas ativas para os dropdowns
        // Certifique-se de que o campo 'sexo' na sua tabela 'aves' está consistente ('Macho', 'Femea')
        $machos = Ave::where('sexo', 'Macho')->where('ativo', 1)->get();
        $femeas = Ave::where('sexo', 'Femea')->where('ativo', 1)->get();
        
        // Busca todos os tipos de aves para os rádio buttons
        $tiposAves = TipoAve::all(); 

        return view('acasalamentos.criar', compact('machos', 'femeas', 'tiposAves'));
    }

    /**
     * Armazena um novo acasalamento no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'selected_tipo_ave_id' => 'required|exists:tipos_aves,id', // NOVO: Valida o tipo de ave selecionado
            'macho_id' => [
                'required',
                // Garante que o macho selecionado é realmente um macho E da espécie selecionada
                Rule::exists('aves', 'id')->where(function ($query) use ($request) {
                    return $query->where('sexo', 'Macho')
                                 ->where('tipo_ave_id', $request->selected_tipo_ave_id);
                }),
            ],
            'femea_id' => [
                'required',
                // Garante que a fêmea selecionada é realmente uma fêmea E da espécie selecionada
                Rule::exists('aves', 'id')->where(function ($query) use ($request) {
                    return $query->where('sexo', 'Femea')
                                 ->where('tipo_ave_id', $request->selected_tipo_ave_id);
                }),
                'different:macho_id', // Garante que macho e fêmea são aves diferentes
            ],
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'observacoes' => 'nullable|string',
        ], [
            'selected_tipo_ave_id.required' => 'É obrigatório selecionar a espécie do acasalamento.',
            'selected_tipo_ave_id.exists' => 'A espécie selecionada é inválida.',
            'macho_id.required' => 'A ave macho é obrigatória.',
            'macho_id.exists' => 'A ave macho selecionada não existe, não é um macho ou não pertence à espécie selecionada.', // Mensagem mais específica
            'femea_id.required' => 'A ave fêmea é obrigatória.',
            'femea_id.exists' => 'A ave fêmea selecionada não existe, não é uma fêmea ou não pertence à espécie selecionada.', // Mensagem mais específica
            'femea_id.different' => 'A ave fêmea deve ser diferente da ave macho.',
            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_fim.after_or_equal' => 'A data de fim não pode ser anterior à data de início.',
        ]);

        try {
            Acasalamento::create($request->all());
            return redirect()->route('acasalamentos.index')->with('success', 'Acasalamento registado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao registar acasalamento: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao registar o acasalamento: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar um acasalamento existente.
     */
    public function edit(string $id)
    {
        $acasalamento = Acasalamento::find($id);

        if (!$acasalamento) {
            return redirect()->route('acasalamentos.index')->with('error', 'Acasalamento não encontrado.');
        }

        $machos = Ave::where('sexo', 'Macho')->where('ativo', 1)->get();
        $femeas = Ave::where('sexo', 'Femea')->where('ativo', 1)->get();
        $tiposAves = TipoAve::all();

        return view('acasalamentos.editar', compact('acasalamento', 'machos', 'femeas', 'tiposAves'));
    }

    /**
     * Atualiza um acasalamento existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'selected_tipo_ave_id' => 'required|exists:tipos_aves,id', // NOVO: Valida o tipo de ave selecionado
            'macho_id' => [
                'required',
                // Garante que o macho selecionado é realmente um macho E da espécie selecionada
                Rule::exists('aves', 'id')->where(function ($query) use ($request) {
                    return $query->where('sexo', 'Macho')
                                 ->where('tipo_ave_id', $request->selected_tipo_ave_id);
                }),
            ],
            'femea_id' => [
                'required',
                'exists:aves,id',
                // Garante que a fêmea selecionada é realmente uma fêmea E da espécie selecionada
                Rule::exists('aves', 'id')->where(function ($query) use ($request) {
                    return $query->where('sexo', 'Femea')
                                 ->where('tipo_ave_id', $request->selected_tipo_ave_id);
                }),
                'different:macho_id',
            ],
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'observacoes' => 'nullable|string',
        ], [
            'selected_tipo_ave_id.required' => 'É obrigatório selecionar a espécie do acasalamento.',
            'selected_tipo_ave_id.exists' => 'A espécie selecionada é inválida.',
            'macho_id.required' => 'A ave macho é obrigatória.',
            'macho_id.exists' => 'A ave macho selecionada não existe, não é um macho ou não pertence à espécie selecionada.',
            'femea_id.required' => 'A ave fêmea é obrigatória.',
            'femea_id.exists' => 'A ave fêmea selecionada não existe, não é uma fêmea ou não pertence à espécie selecionada.',
            'femea_id.different' => 'A ave fêmea deve ser diferente da ave macho.',
            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_fim.after_or_equal' => 'A data de fim não pode ser anterior à data de início.',
        ]);

        try {
            $acasalamento = Acasalamento::find($id);

            if (!$acasalamento) {
                return redirect()->back()->with('error', 'Acasalamento não encontrado para atualização.');
            }

            $acasalamento->update($request->all());
            return redirect()->route('acasalamentos.index')->with('success', 'Acasalamento atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar acasalamento: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar o acasalamento: ' . $e->getMessage());
        }
    }

    /**
     * Remove um acasalamento do banco de dados.
     */
    public function destroy(string $id)
    {
        try {
            $acasalamento = Acasalamento::find($id);

            if (!$acasalamento) {
                return redirect()->route('acasalamentos.index')->with('error', 'Acasalamento não encontrado para exclusão.');
            }

            $acasalamento->delete();
            return redirect()->route('acasalamentos.index')->with('success', 'Acasalamento excluído com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir acasalamento: ' . $e->getMessage(), ['acasalamento_id' => $id]);
            return redirect()->route('acasalamentos.index')->with('error', 'Erro ao excluir o acasalamento.');
        }
    }
}
