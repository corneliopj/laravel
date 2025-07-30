<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Ave;
use App\Models\Plantel;
use App\Models\MovimentacaoPlantel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class VendaController extends Controller
{
    /**
     * Exibe uma listagem de vendas.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) // Adicionado Request para acessar filtros
    {
        $query = Venda::query()->with(['items.ave.tipoAve', 'items.plantel']);

        // Lógica de filtro (copiada da sua index.blade.php original)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->filled('data_inicio')) {
            $query->where('data_venda', '>=', Carbon::parse($request->data_inicio)->startOfDay());
        }
        if ($request->filled('data_fim')) {
            $query->where('data_venda', '<=', Carbon::parse($request->data_fim)->endOfDay());
        }

        $vendas = $query->orderBy('data_venda', 'desc')->paginate(15);

        // NOVO: Definir opções de status para o filtro
        $statusOptions = [
            'concluida' => 'Concluída',
            'pendente' => 'Pendente',
            'cancelada' => 'Cancelada',
        ];

        return view('financeiro.vendas.index', compact('vendas', 'statusOptions', 'request')); // Passar statusOptions e request para a view
    }

    /**
     * Mostra o formulário para registrar uma nova venda.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $avesDisponiveis = Ave::where('ativo', true)->orderBy('matricula')->get();
        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        $metodosPagamento = [
            'Dinheiro' => 'Dinheiro',
            'Cartão de Crédito' => 'Cartão de Crédito',
            'Cartão de Débito' => 'Cartão de Débito',
            'Pix' => 'Pix',
            'Transferência Bancária' => 'Transferência Bancária',
            'Outro' => 'Outro',
        ];

        return view('financeiro.vendas.create', compact('avesDisponiveis', 'plantelOptions', 'metodosPagamento'));
    }

    /**
     * Armazena um novo registro de venda e seus itens no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_venda' => 'required|date|before_or_equal:today',
            'comprador' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'metodo_pagamento' => 'nullable|string|max:255',
            'desconto' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.tipo_item' => ['required', Rule::in(['individual', 'plantel', 'generico'])],
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|required_if:items.*.tipo_item,individual|exists:aves,id',
            'items.*.plantel_id' => 'nullable|required_if:items.*.tipo_item,plantel|exists:plantel,id',
            'items.*.quantidade' => 'required|integer|min:1',
            'items.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $valorTotalItens = 0;
            $desconto = (float) $request->desconto ?? 0;

            $venda = Venda::create([
                'data_venda' => $request->data_venda,
                'comprador' => $request->comprador,
                'observacoes' => $request->observacoes,
                'metodo_pagamento' => $request->metodo_pagamento,
                'desconto' => $desconto,
                'valor_total' => 0,
                'valor_final' => 0,
                'status' => 'concluida',
            ]);

            foreach ($request->items as $itemData) {
                $itemQuantidade = (int) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemValorTotal = $itemQuantidade * $itemPrecoUnitario;

                $valorTotalItens += $itemValorTotal;

                if ($itemData['tipo_item'] == 'individual') {
                    $ave = Ave::findOrFail($itemData['ave_id']);
                    if (!$ave->ativo) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', 'A ave ' . $ave->matricula . ' não está ativa e não pode ser vendida.');
                    }
                    $ave->ativo = false;
                    $ave->vendavel = false;
                    $ave->save();

                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'descricao_item' => $itemData['descricao_item'],
                        'ave_id' => $ave->id,
                        'quantidade' => 1,
                        'preco_unitario' => $itemPrecoUnitario,
                        'valor_total_item' => $itemPrecoUnitario,
                    ]);

                } elseif ($itemData['tipo_item'] == 'plantel') {
                    $plantel = Plantel::findOrFail($itemData['plantel_id']);

                    if ($itemQuantidade > $plantel->quantidade_atual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', 'A quantidade de aves (' . $itemQuantidade . ') excede a quantidade atual do plantel ' . $plantel->identificacao_grupo . ' (' . $plantel->quantidade_atual . ').');
                    }

                    MovimentacaoPlantel::create([
                        'plantel_id' => $plantel->id,
                        'tipo_movimentacao' => 'saida_venda',
                        'quantidade' => $itemQuantidade,
                        'data_movimentacao' => $request->data_venda,
                        'observacoes' => 'Venda de ' . $itemQuantidade . ' aves do plantel ' . $plantel->identificacao_grupo . '. Comprador: ' . ($request->comprador ?? 'Não informado'),
                    ]);

                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'descricao_item' => $itemData['descricao_item'],
                        'plantel_id' => $plantel->id,
                        'quantidade' => $itemQuantidade,
                        'preco_unitario' => $itemPrecoUnitario,
                        'valor_total_item' => $itemValorTotal,
                    ]);

                } else { // Tipo genérico
                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'descricao_item' => $itemData['descricao_item'],
                        'quantidade' => $itemQuantidade,
                        'preco_unitario' => $itemPrecoUnitario,
                        'valor_total_item' => $itemValorTotal,
                    ]);
                }
            }

            $venda->valor_total = $valorTotalItens;
            $venda->valor_final = $valorTotalItens - $desconto;
            if ($venda->valor_final < 0) {
                $venda->valor_final = 0;
            }
            $venda->save();

            DB::commit();
            return redirect()->route('financeiro.vendas.index')->with('success', 'Venda registrada com sucesso! ID: ' . $venda->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao registrar venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de um registro de venda.
     *
     * @param  \App\Models\Venda  $venda
     * @return \Illuminate\View\View
     */
    public function show(Venda $venda)
    {
        $venda->load(['items.ave.tipoAve', 'items.plantel']);
        return view('financeiro.vendas.show', compact('venda'));
    }

    /**
     * Mostra o formulário para editar um registro de venda existente.
     *
     * @param  \App\Models\Venda  $venda
     * @return \Illuminate\View\View
     */
    public function edit(Venda $venda)
    {
        $venda->load(['items.ave', 'items.plantel']);

        $avesDisponiveis = Ave::where('ativo', true)->orderBy('matricula')->get();
        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        $metodosPagamento = [
            'Dinheiro' => 'Dinheiro',
            'Cartão de Crédito' => 'Cartão de Crédito',
            'Cartão de Débito' => 'Cartão de Débito',
            'Pix' => 'Pix',
            'Transferência Bancária' => 'Transferência Bancária',
            'Outro' => 'Outro',
        ];

        $statusOptions = [
            'concluida' => 'Concluída',
            'pendente' => 'Pendente',
            'cancelada' => 'Cancelada',
        ];

        return view('financeiro.vendas.edit', compact('venda', 'avesDisponiveis', 'plantelOptions', 'metodosPagamento', 'statusOptions'));
    }

    /**
     * Atualiza um registro de venda existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Venda  $venda
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Venda $venda)
    {
        $request->validate([
            'data_venda' => 'required|date|before_or_equal:today',
            'comprador' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'metodo_pagamento' => 'nullable|string|max:255',
            'desconto' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['concluida', 'pendente', 'cancelada'])],
            'items' => 'required|array|min:1',
            'items.*.tipo_item' => ['required', Rule::in(['individual', 'plantel', 'generico'])],
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|required_if:items.*.tipo_item,individual|exists:aves,id',
            'items.*.plantel_id' => 'nullable|required_if:items.*.tipo_item,plantel|exists:plantel,id',
            'items.*.quantidade' => 'required|integer|min:1',
            'items.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            foreach ($venda->items as $oldItem) {
                if ($oldItem->ave_id) {
                    $ave = Ave::find($oldItem->ave_id);
                    if ($ave) {
                        $ave->ativo = true;
                        $ave->vendavel = true;
                        $ave->save();
                    }
                } elseif ($oldItem->plantel_id) {
                    MovimentacaoPlantel::create([
                        'plantel_id' => $oldItem->plantel_id,
                        'tipo_movimentacao' => 'entrada',
                        'quantidade' => $oldItem->quantidade,
                        'data_movimentacao' => Carbon::now(),
                        'observacoes' => 'Ajuste de reversão de venda (ID Venda: ' . $venda->id . ') devido a edição.',
                    ]);
                }
            }

            $venda->items()->delete();

            $valorTotalItens = 0;
            $desconto = (float) $request->desconto ?? 0;

            foreach ($request->items as $itemData) {
                $itemQuantidade = (int) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemValorTotal = $itemQuantidade * $itemPrecoUnitario;

                $valorTotalItens += $itemValorTotal;

                if ($itemData['tipo_item'] == 'individual') {
                    $ave = Ave::findOrFail($itemData['ave_id']);
                    if (!$ave->ativo) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', 'A ave ' . $ave->matricula . ' não está ativa e não pode ser vendida.');
                    }
                    $ave->ativo = false;
                    $ave->vendavel = false;
                    $ave->save();

                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'descricao_item' => $itemData['descricao_item'],
                        'ave_id' => $ave->id,
                        'quantidade' => 1,
                        'preco_unitario' => $itemPrecoUnitario,
                        'valor_total_item' => $itemPrecoUnitario,
                    ]);

                } elseif ($itemData['tipo_item'] == 'plantel') {
                    $plantel = Plantel::findOrFail($itemData['plantel_id']);

                    if ($itemQuantidade > $plantel->quantidade_atual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', 'A quantidade de aves (' . $itemQuantidade . ') excede a quantidade atual do plantel ' . $plantel->identificacao_grupo . ' (' . $plantel->quantidade_atual . ').');
                    }

                    MovimentacaoPlantel::create([
                        'plantel_id' => $plantel->id,
                        'tipo_movimentacao' => 'saida_venda',
                        'quantidade' => $itemQuantidade,
                        'data_movimentacao' => $request->data_venda,
                        'observacoes' => 'Venda de ' . $itemQuantidade . ' aves do plantel ' . $plantel->identificacao_grupo . ' (Atualização de venda ID: ' . $venda->id . '). Comprador: ' . ($request->comprador ?? 'Não informado'),
                    ]);

                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'descricao_item' => $itemData['descricao_item'],
                        'plantel_id' => $plantel->id,
                        'quantidade' => $itemQuantidade,
                        'preco_unitario' => $itemPrecoUnitario,
                        'valor_total_item' => $itemValorTotal,
                    ]);

                } else { // Tipo genérico
                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'descricao_item' => $itemData['descricao_item'],
                        'quantidade' => $itemQuantidade,
                        'preco_unitario' => $itemPrecoUnitario,
                        'valor_total_item' => $itemValorTotal,
                    ]);
                }
            }

            $venda->update([
                'data_venda' => $request->data_venda,
                'comprador' => $request->comprador,
                'observacoes' => $request->observacoes,
                'metodo_pagamento' => $request->metodo_pagamento,
                'desconto' => $desconto,
                'status' => $request->status,
                'valor_total' => $valorTotalItens,
                'valor_final' => $valorTotalItens - $desconto,
            ]);
            if ($venda->valor_final < 0) {
                $venda->valor_final = 0;
            }

            DB::commit();
            return redirect()->route('financeiro.vendas.index')->with('success', 'Venda atualizada com sucesso! ID: ' . $venda->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove um registro de venda do banco de dados.
     *
     * @param  \App\Models\Venda  $venda
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Venda $venda)
    {
        DB::beginTransaction();
        try {
            foreach ($venda->items as $item) {
                if ($item->ave_id) {
                    $ave = Ave::find($item->ave_id);
                    if ($ave) {
                        $ave->ativo = true;
                        $ave->vendavel = true;
                        $ave->save();
                    }
                } elseif ($item->plantel_id) {
                    MovimentacaoPlantel::create([
                        'plantel_id' => $item->plantel_id,
                        'tipo_movimentacao' => 'entrada',
                        'quantidade' => $item->quantidade,
                        'data_movimentacao' => Carbon::now(),
                        'observacoes' => 'Reversão de venda (ID Venda: ' . $venda->id . ') devido à exclusão do registro.',
                    ]);
                }
            }

            $venda->delete();

            DB::commit();
            return redirect()->route('financeiro.vendas.index')->with('success', 'Registro de venda excluído com sucesso e status revertido!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir registro de venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->with('error', 'Erro ao excluir registro de venda: ' . $e->getMessage());
        }
    }

    /**
     * Busca aves disponíveis para venda via AJAX (para o Select2).
     * Este método pode ser adaptado ou removido se a busca for integrada diretamente no formulário de itens.
     */
    public function searchAvesForSale(Request $request)
    {
        $query = $request->get('q');
        if (empty($query)) {
            return response()->json([]);
        }

        $aves = Ave::where('vendavel', true)
                    ->where('ativo', true)
                    ->where('matricula', 'like', '%' . $query . '%')
                    ->with('tipoAve', 'variacao')
                    ->limit(10)
                    ->get();

        $results = $aves->map(function($ave) {
            $tipoAveNome = $ave->tipoAve->nome ?? 'N/A';
            $variacaoNome = $ave->variacao->nome ?? 'N/A';
            return [
                'id' => $ave->id,
                'text' => "{$ave->matricula} ({$tipoAveNome} - {$variacaoNome})",
                'preco_sugerido' => number_format($ave->preco_sugerido ?? 0.00, 2, '.', ''),
            ];
        });

        return response()->json($results);
    }
}
