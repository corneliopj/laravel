<?php

namespace App\Http\Controllers;

use App\Models\MovimentacaoPlantel;
use App\Models\Plantel; // Para listar plantéis no formulário
use Illuminate\Http\Request;
use Carbon\Carbon;

class MovimentacaoPlantelController extends Controller
{
    /**
     * Exibe uma listagem de movimentações de plantel.
     * Pode ser filtrada por plantel_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $plantelId = $request->query('plantel_id');
        $query = MovimentacaoPlantel::with('plantel');

        if ($plantelId) {
            $query->where('plantel_id', $plantelId);
            $plantel = Plantel::find($plantelId); // Para exibir o nome do plantel no título
        } else {
            $plantel = null;
        }

        $movimentacoes = $query->orderBy('data_movimentacao', 'desc')->paginate(15); // Paginação para listas longas

        return view('movimentacoes_plantel.index', compact('movimentacoes', 'plantel'));
    }

    /**
     * Mostra o formulário para criar uma nova movimentação.
     * Pode receber um plantel_id via query string para pré-seleção.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $plantelId = $request->query('plantel_id');
        $plantelPreSelecionado = null;

        if ($plantelId) {
            $plantelPreSelecionado = Plantel::find($plantelId);
        }

        $plantelOptions = Plantel::orderBy('identificacao_grupo')->get(); // Todos os plantéis para o dropdown

        return view('movimentacoes_plantel.create', compact('plantelOptions', 'plantelPreSelecionado'));
    }

    /**
     * Armazena uma nova movimentação no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'plantel_id' => 'required|exists:plantel,id',
            'tipo_movimentacao' => 'required|in:entrada,saida_venda,saida_morte,saida_consumo,saida_doacao,saida_descarte,outros',
            'quantidade' => 'required|integer|min:1',
            'data_movimentacao' => 'required|date',
            'observacoes' => 'nullable|string|max:500',
        ]);

        MovimentacaoPlantel::create($request->all());

        return redirect()->route('plantel.show', $request->input('plantel_id'))->with('success', 'Movimentação registrada com sucesso!');
    }

    /**
     * Exibe os detalhes de uma movimentação específica.
     *
     * @param  \App\Models\MovimentacaoPlantel  $movimentacaoPlantel
     * @return \Illuminate\View\View
     */
    public function show(MovimentacaoPlantel $movimentacaoPlantel)
    {
        $movimentacaoPlantel->load('plantel'); // Carrega o relacionamento com o plantel
        return view('movimentacoes_plantel.show', compact('movimentacaoPlantel'));
    }

    /**
     * Mostra o formulário para editar uma movimentação existente.
     *
     * @param  \App\Models\MovimentacaoPlantel  $movimentacaoPlantel
     * @return \Illuminate\View\View
     */
    public function edit(MovimentacaoPlantel $movimentacaoPlantel)
    {
        $plantelOptions = Plantel::orderBy('identificacao_grupo')->get();
        return view('movimentacoes_plantel.edit', compact('movimentacaoPlantel', 'plantelOptions'));
    }

    /**
     * Atualiza uma movimentação existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MovimentacaoPlantel  $movimentacaoPlantel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, MovimentacaoPlantel $movimentacaoPlantel)
    {
        $request->validate([
            'plantel_id' => 'required|exists:plantel,id',
            'tipo_movimentacao' => 'required|in:entrada,saida_venda,saida_morte,saida_consumo,saida_doacao,saida_descarte,outros',
            'quantidade' => 'required|integer|min:1',
            'data_movimentacao' => 'required|date',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $movimentacaoPlantel->update($request->all());

        return redirect()->route('plantel.show', $request->input('plantel_id'))->with('success', 'Movimentação atualizada com sucesso!');
    }

    /**
     * Remove uma movimentação do banco de dados.
     *
     * @param  \App\Models\MovimentacaoPlantel  $movimentacaoPlantel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MovimentacaoPlantel $movimentacaoPlantel)
    {
        $plantelId = $movimentacaoPlantel->plantel_id; // Guarda o ID do plantel antes de deletar
        $movimentacaoPlantel->delete();

        return redirect()->route('plantel.show', $plantelId)->with('success', 'Movimentação excluída com sucesso!');
    }
}
