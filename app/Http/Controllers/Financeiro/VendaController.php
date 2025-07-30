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
    public function index()
    {
        // Carrega as vendas, incluindo os itens e seus relacionamentos com Ave e Plantel
        $vendas = Venda::with(['items.ave.tipoAve', 'items.plantel'])->orderBy('data_venda', 'desc')->paginate(15);
        return view('financeiro.vendas.index', compact('vendas'));
    }

    /**
     * Mostra o formulário para registrar uma nova venda.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Aves ativas para seleção nos itens (apenas as que podem ser vendidas individualmente)
        $avesDisponiveis = Ave::where('ativo', true)->orderBy('matricula')->get();
        // Plantéis ativos para seleção nos itens
        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        // NOVO: Definir métodos de pagamento
        $metodosPagamento = [
            'Dinheiro' => 'Dinheiro',
            'Cartão de Crédito' => 'Cartão de Crédito',
            'Cartão de Débito' => 'Cartão de Débito',
            'Pix' => 'Pix',
            'Transferência Bancária' => 'Transferência Bancária',
            'Outro' => 'Outro',
        ];

        return view('financeiro.vendas.create', compact('avesDisponiveis', 'plantelOptions', 'metodosPagamento')); // Passar para a view
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
            'metodo_pagamento' => 'nullable|string|max:255', // Adicionado validação
            'desconto' => 'nullable|numeric|min:0', // Adicionado validação
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
            $valorTotalItens = 0; // Soma dos itens sem desconto
            $desconto = (float) $request->desconto ?? 0;

            // Primeiro, cria a venda principal para ter um ID
            $venda = Venda::create([
                'data_venda' => $request->data_venda,
                'comprador' => $request->comprador,
                'observacoes' => $request->observacoes,
                'metodo_pagamento' => $request->metodo_pagamento, // Salvar método de pagamento
                'desconto' => $desconto, // Salvar desconto
                'valor_total' => 0, // Será atualizado após processar os itens
                'valor_final' => 0, // Será atualizado após processar os itens
                'status' => 'concluida', // Status padrão para vendas diretas
            ]);

            foreach ($request->items as $itemData) {
                $itemQuantidade = (int) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemValorTotal = $itemQuantidade * $itemPrecoUnitario;

                // Acumula o valor total dos itens
                $valorTotalItens += $itemValorTotal;

                // Lógica de manipulação de estoque/status
                if ($itemData['tipo_item'] == 'individual') {
                    $ave = Ave::findOrFail($itemData['ave_id']);
                    if (!$ave->ativo) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', 'A ave ' . $ave->matricula . ' não está ativa e não pode ser vendida.');
                    }
                    $ave->ativo = false; // Inativa a ave
                    $ave->vendavel = false; // Marca como não vendável
                    $ave->save();

                    // Cria o item de venda
                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'descricao_item' => $itemData['descricao_item'],
                        'ave_id' => $ave->id,
                        'quantidade' => 1, // Sempre 1 para ave individual
                        'preco_unitario' => $itemPrecoUnitario,
                        'valor_total_item' => $itemPrecoUnitario,
                    ]);

                } elseif ($itemData['tipo_item'] == 'plantel') {
                    $plantel = Plantel::findOrFail($itemData['plantel_id']);

                    if ($itemQuantidade > $plantel->quantidade_atual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', 'A quantidade de aves (' . $itemQuantidade . ') excede a quantidade atual do plantel ' . $plantel->identificacao_grupo . ' (' . $plantel->quantidade_atual . ').');
                    }

                    // Cria movimentação de saída para o plantel
                    MovimentacaoPlantel::create([
                        'plantel_id' => $plantel->id,
                        'tipo_movimentacao' => 'saida_venda',
                        'quantidade' => $itemQuantidade,
                        'data_movimentacao' => $request->data_venda,
                        'observacoes' => 'Venda de ' . $itemQuantidade . ' aves do plantel ' . $plantel->identificacao_grupo . '. Comprador: ' . ($request->comprador ?? 'Não informado'),
                    ]);

                    // Cria o item de venda
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

            // Atualiza o valor total e final da venda principal
            $venda->valor_total = $valorTotalItens;
            $venda->valor_final = $valorTotalItens - $desconto;
            if ($venda->valor_final < 0) {
                $venda->valor_final = 0; // Garante que o valor final não seja negativo
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

        // NOVO: Definir métodos de pagamento
        $metodosPagamento = [
            'Dinheiro' => 'Dinheiro',
            'Cartão de Crédito' => 'Cartão de Crédito',
            'Cartão de Débito' => 'Cartão de Débito',
            'Pix' => 'Pix',
            'Transferência Bancária' => 'Transferência Bancária',
            'Outro' => 'Outro',
        ];

        // NOVO: Definir opções de status
        $statusOptions = [
            'concluida' => 'Concluída',
            'pendente' => 'Pendente',
            'cancelada' => 'Cancelada',
        ];

        return view('financeiro.vendas.edit', compact('venda', 'avesDisponiveis', 'plantelOptions', 'metodosPagamento', 'statusOptions')); // Passar para a view
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
            'metodo_pagamento' => 'nullable|string|max:255', // Adicionado validação
            'desconto' => 'nullable|numeric|min:0', // Adicionado validação
            'status' => ['required', Rule::in(['concluida', 'pendente', 'cancelada'])], // Adicionado validação
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
            // 1. Reverter o status das aves/plantéis dos itens antigos
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

            // 2. Deletar todos os itens antigos da venda
            $venda->items()->delete();

            $valorTotalItens = 0;
            $desconto = (float) $request->desconto ?? 0;

            // 3. Processar e criar os novos itens
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

            // 4. Atualiza a venda principal
            $venda->update([
                'data_venda' => $request->data_venda,
                'comprador' => $request->comprador,
                'observacoes' => $request->observacoes,
                'metodo_pagamento' => $request->metodo_pagamento, // Atualizar método de pagamento
                'desconto' => $desconto, // Atualizar desconto
                'status' => $request->status, // Atualizar status
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
