<?php

namespace App\Http\Controllers;

use App\Models\Plantel; // Importa o modelo Plantel
use App\Models\TipoAve; // Importa o modelo TipoAve para o dropdown
use App\Models\MovimentacaoPlantel; // Importa o modelo MovimentacaoPlantel
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Para transações, se necessário

class PlantelController extends Controller
{
    /**
     * Exibe uma listagem de plantéis.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtém todos os plantéis, ordenados por identificação
        // Com o 'with('tipoAve')' para carregar o relacionamento e acessar o nome do tipo de ave
        $plantel = Plantel::with('tipoAve')->orderBy('identificacao_grupo')->get();

        // Para cada plantel, calculamos a quantidade atual usando o acessor do modelo
        // Não é estritamente necessário fazer isso aqui se o acessor já está no modelo,
        // mas é bom para ilustrar que a view terá acesso a 'quantidade_atual'.
        $plantelData = $plantel->map(function($item) {
            return [
                'id' => $item->id,
                'identificacao_grupo' => $item->identificacao_grupo,
                'tipo_ave_nome' => $item->tipoAve->nome ?? 'N/A',
                'data_formacao' => $item->data_formacao->format('d/m/Y'),
                'quantidade_inicial' => $item->quantidade_inicial,
                'quantidade_atual' => $item->quantidade_atual, // Usa o acessor do modelo
                'ativo' => $item->ativo,
                'observacoes' => $item->observacoes,
                'link_detalhes' => route('plantel.show', $item->id),
                'link_editar' => route('plantel.edit', $item->id),
            ];
        });

        return view('plantel.index', compact('plantelData'));
    }

    /**
     * Mostra o formulário para criar um novo plantel.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Obtém todos os tipos de aves para preencher o dropdown
        $tiposAves = TipoAve::orderBy('nome')->get();
        return view('plantel.create', compact('tiposAves'));
    }

    /**
     * Armazena um novo plantel no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'identificacao_grupo' => 'required|string|max:255|unique:plantel,identificacao_grupo',
            'data_formacao' => 'required|date',
            'quantidade_inicial' => 'required|integer|min:1',
            'observacoes' => 'nullable|string',
        ]);

        // Inicia uma transação de banco de dados para garantir a atomicidade
        // Se algo der errado, tudo é revertido.
        DB::beginTransaction();
        try {
            // Cria o novo registro de plantel
            $plantel = Plantel::create([
                'tipo_ave_id' => $request->input('tipo_ave_id'),
                'identificacao_grupo' => $request->input('identificacao_grupo'),
                'data_formacao' => $request->input('data_formacao'),
                'quantidade_inicial' => $request->input('quantidade_inicial'),
                'ativo' => true, // Novo plantel é sempre ativo
                'observacoes' => $request->input('observacoes'),
            ]);

            // Cria a movimentação inicial de 'entrada' para o plantel
            MovimentacaoPlantel::create([
                'plantel_id' => $plantel->id,
                'tipo_movimentacao' => 'entrada',
                'quantidade' => $request->input('quantidade_inicial'),
                'data_movimentacao' => $request->input('data_formacao'), // Data da formação do plantel
                'observacoes' => 'Criação inicial do plantel com ' . $request->input('quantidade_inicial') . ' aves.',
            ]);

            DB::commit(); // Confirma a transação
            return redirect()->route('plantel.index')->with('success', 'Plantel criado com sucesso e movimentação inicial registrada!');

        } catch (\Exception $e) {
            DB::rollBack(); // Reverte a transação em caso de erro
            // Log do erro para depuração
            \Log::error('Erro ao criar plantel e movimentação inicial: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao criar o plantel: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de um plantel específico.
     *
     * @param  \App\Models\Plantel  $plantel
     * @return \Illuminate\View\View
     */
    public function show(Plantel $plantel)
    {
        // Carrega o tipo de ave e as movimentações para exibir os detalhes
        $plantel->load('tipoAve', 'movimentacoes');
        // Calcula a quantidade atual novamente para garantir que está atualizada
        $quantidadeAtual = $plantel->quantidade_atual; // Usa o acessor

        return view('plantel.show', compact('plantel', 'quantidadeAtual'));
    }

    /**
     * Mostra o formulário para editar um plantel existente.
     *
     * @param  \App\Models\Plantel  $plantel
     * @return \Illuminate\View\View
     */
    public function edit(Plantel $plantel)
    {
        $tiposAves = TipoAve::orderBy('nome')->get();
        return view('plantel.edit', compact('plantel', 'tiposAves'));
    }

    /**
     * Atualiza um plantel existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plantel  $plantel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Plantel $plantel)
    {
        $request->validate([
            'tipo_ave_id' => 'required|exists:tipos_aves,id',
            'identificacao_grupo' => 'required|string|max:255|unique:plantel,identificacao_grupo,' . $plantel->id,
            'data_formacao' => 'required|date',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        // A 'quantidade_inicial' não é atualizada diretamente aqui,
        // pois as mudanças de quantidade são feitas via movimentações.
        // Se houver uma alteração na 'quantidade_inicial' no formulário de edição,
        // isso implicaria em uma nova movimentação de ajuste ou correção.
        // Por enquanto, apenas os campos do Plantel são atualizados.

        $plantel->update([
            'tipo_ave_id' => $request->input('tipo_ave_id'),
            'identificacao_grupo' => $request->input('identificacao_grupo'),
            'data_formacao' => $request->input('data_formacao'),
            'ativo' => $request->has('ativo'), // Checkbox
            'observacoes' => $request->input('observacoes'),
        ]);

        return redirect()->route('plantel.index')->with('success', 'Plantel atualizado com sucesso!');
    }

    /**
     * Remove um plantel do banco de dados.
     *
     * @param  \App\Models\Plantel  $plantel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Plantel $plantel)
    {
        // Ao deletar um plantel, todas as suas movimentações serão deletadas em cascata
        // devido à restrição onDelete('cascade') na migração de movimentacoes_plantel.
        $plantel->delete();

        return redirect()->route('plantel.index')->with('success', 'Plantel excluído com sucesso!');
    }
}
