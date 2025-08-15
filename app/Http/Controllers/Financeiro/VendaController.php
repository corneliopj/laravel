<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\Ave;
use App\Models\Plantel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class VendaController extends Controller
{
    /**
     * Exibe a lista de vendas com filtros
     */
    public function index(Request $request)
    {
        $query = Venda::query();
        
        // Filtros
        if ($request->filled('data_inicio')) {
            $query->where('data_venda', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->where('data_venda', '<=', $request->data_fim);
        }
        
        if ($request->filled('comprador')) {
            $query->where('comprador', 'like', '%'.$request->comprador.'%');
        }
        
        // Ordenação
        switch ($request->ordenar) {
            case 'antigas':
                $query->orderBy('data_venda', 'asc');
                break;
            case 'valor_maior':
                $query->orderBy('valor_final', 'desc');
                break;
            case 'valor_menor':
                $query->orderBy('valor_final', 'asc');
                break;
            default: // recentes
                $query->orderBy('data_venda', 'desc');
        }
        
        $vendas = $query->paginate(10);
        $compradores = Venda::distinct()->pluck('comprador')->toArray();
        
        return view('financeiro.vendas.index', compact('vendas', 'compradores'));
    }

    /**
     * Mostra o formulário para criar nova venda
     */
    public function create()
    {
        // Lista de compradores para autocomplete
        $compradores = Venda::distinct()->pluck('comprador')->toArray();
        
        // Métodos de pagamento disponíveis
        $metodosPagamento = [
            'dinheiro' => 'Dinheiro',
            'cartao' => 'Cartão',
            'transferencia' => 'Transferência',
            'pix' => 'PIX'
        ];
        
        // Aves disponíveis para venda
        $avesDisponiveis = Ave::where('vendavel', true)
                         ->where('ativo', true)
                         ->with('tipoAve', 'variacao')
                         ->get();
        
        // Plantéis disponíveis
        $plantelOptions = Plantel::where('ativo', true)
                           ->orderBy('identificacao_grupo')
                           ->get();
        
        return view('financeiro.vendas.create', compact(
            'compradores', 
            'metodosPagamento',
            'avesDisponiveis',
            'plantelOptions'
        ));
    }

    /**
     * Armazena uma nova venda no banco de dados
     */
  public function store(Request $request)
{
    // Validação dos dados
    $request->validate([
        'comprador' => 'required',
        'data_venda' => 'required|date',
        'metodo_pagamento' => 'required',
        'itens' => 'required|array|min:1',
        'itens.*.descricao_item' => 'required', // Corrigido para descricao_item
        'itens.*.quantidade' => 'required|numeric|min:1',
        'itens.*.preco_unitario' => 'required|numeric|min:0.01',
    ]);

    // Calcula o valor total dos itens
    $valorTotal = collect($request->itens)->sum(function ($item) {
        return $item['quantidade'] * $item['preco_unitario'];
    });

    // Aplica desconto
    $desconto = $request->desconto ?? 0;
    $valorFinal = $valorTotal - $desconto;

    // Configura comissão de 15%
    $comissaoPercentual = 15.0; // Nome da variável ajustado para comissao_percentual
    $valorComissao = ($valorFinal * $comissaoPercentual) / 100;

    // Usuário logado (vendedor)
    $vendedor = auth()->user();

    // Inicia transação para garantir integridade dos dados
    DB::beginTransaction();
    try {
        // Cria a venda
        $venda = Venda::create([
            'comprador' => $request->comprador,
            'data_venda' => $request->data_venda,
            'metodo_pagamento' => $request->metodo_pagamento,
            'valor_total' => $valorTotal,
            'desconto' => $desconto,
            'valor_final' => $valorFinal,
            'observacoes' => $request->observacoes,
            'status' => 'concluida',
            'user_id' => $vendedor ? $vendedor->id : null,
            'comissao_percentual' => $comissaoPercentual, // Nome da coluna corrigido
            'comissao_paga' => true,
        ]);

        // Cria os itens da venda
        foreach ($request->itens as $item) {
            $vendaItem = \App\Models\VendaItem::create([
                'venda_id' => $venda->id,
                'descricao_item' => $item['descricao_item'],
                'ave_id' => $item['ave_id'] ?? null,
                'plantel_id' => $item['plantel_id'] ?? null,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'valor_total_item' => $item['quantidade'] * $item['preco_unitario'],
            ]);

            // Atualiza status das aves vendidas
            if (isset($item['ave_id'])) {
                $ave = Ave::find($item['ave_id']);
                if ($ave) {
                    $ave->update([
                        'ativo' => false,
                        'vendavel' => false,
                        'data_inativado' => now(),
                    ]);
                }
            }

            // Registra movimentação do plantel se aplicável
            if (isset($item['plantel_id'])) {
                \App\Models\MovimentacaoPlantel::create([
                    'plantel_id' => $item['plantel_id'],
                    'tipo_movimentacao' => 'saida_venda',
                    'quantidade' => $item['quantidade'],
                    'data_movimentacao' => $request->data_venda,
                    'observacoes' => 'Venda #' . $venda->id . ' - Comprador: ' . $request->comprador,
                ]);
            }
        }

        // Cria despesa de comissão se houver vendedor e comissão > 0
        if ($vendedor && $valorComissao > 0) {
            $categoriaComissao = \App\Models\Categoria::firstOrCreate(
                ['nome' => 'Comissões'],
                ['descricao' => 'Despesas geradas por comissões de vendas.']
            );

            $despesaComissao = \App\Models\Despesa::create([
                'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                'valor' => $valorComissao,
                'data' => now(),
                'categoria_id' => $categoriaComissao->id,
                'observacoes' => 'Comissão de ' . $comissaoPercentual . '% referente à venda #' . $venda->id,
            ]);

            // Vincula a despesa de comissão à venda
            $venda->update(['despesa_id' => $despesaComissao->id]);
        }

        DB::commit();

        return redirect()->route('financeiro.vendas.show', $venda->id)
            ->with('success', 'Venda registrada com sucesso! Comissão de ' . $comissaoPercentual . '% (R$ ' . number_format($valorComissao, 2, ',', '.') . ') gerada automaticamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erro ao criar venda: ' . $e->getMessage());

        return redirect()->back()
            ->withInput()
            ->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
    }
}

    /**
     * Exibe os detalhes de uma venda específica
     */
    public function show($id)
    {
        $venda = Venda::with(['vendaItems.ave', 'vendaItems.plantel', 'user'])->findOrFail($id);
        return view('financeiro.vendas.show', compact('venda'));
    }

    /**
     * Mostra o formulário para editar uma venda existente
     */
    public function edit($id)
    {
        $venda = Venda::with(['user', 'despesaComissao', 'vendaItems.ave.tipoAve', 'vendaItems.plantel'])->findOrFail($id);
        $metodosPagamento = [
            'dinheiro' => 'Dinheiro',
            'cartao' => 'Cartão',
            'transferencia' => 'Transferência',
            'pix' => 'PIX'
        ];
        $statusOptions = ['concluida' => 'Concluída', 'pendente' => 'Pendente', 'cancelada' => 'Cancelada'];
        
        // Aves que já estão na venda atual devem estar disponíveis
        $avesNaVendaAtual = $venda->vendaItems->pluck('ave_id')->filter()->toArray();
        $compradores = Venda::distinct()->pluck('comprador')->toArray(); 

        // Aves disponíveis para seleção
        $avesDisponiveis = Ave::where(function($query) use ($avesNaVendaAtual) {
                                $query->where('vendavel', true)
                                      ->where('ativo', true)
                                      ->orWhereIn('id', $avesNaVendaAtual);
                            })
                            ->with('tipoAve', 'variacao')
                            ->get();

        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        return view('financeiro.vendas.edit', compact(
            'venda',
            'compradores', 
            'metodosPagamento', 
            'statusOptions', 
            'avesDisponiveis', 
            'plantelOptions'
        ));
    }

    /**
     * Atualiza uma venda existente no banco de dados
     */
    public function update(Request $request, $id)
    {
        $venda = Venda::findOrFail($id);

        // Validação dos dados
        $request->validate([
            'data_venda' => 'required|date|before_or_equal:today',
            'comprador' => 'nullable|string|max:255',
            'metodo_pagamento' => 'required|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'desconto' => 'nullable|numeric|min:0',
            'comissao_percentual' => 'nullable|numeric|min:0|max:100',
            'status' => ['required', Rule::in(['concluida', 'pendente', 'cancelada'])],
            'itens' => 'required|array|min:1',
            'itens.*.descricao_item' => 'required|string|max:255',
            'itens.*.ave_id' => 'nullable|exists:aves,id',
            'itens.*.plantel_id' => 'nullable|exists:plantel,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            // Calcula o valor total dos itens
            $valorTotalItens = collect($request->itens)->sum(function($item) {
                return $item['quantidade'] * $item['preco_unitario'];
            });
            
            $desconto = $request->desconto ?? 0;
            $valorFinalVenda = $valorTotalItens - $desconto;

            if ($valorFinalVenda < 0) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'O valor final da venda não pode ser negativo.');
            }

            // Calcula comissão
            $percentualComissao = $request->comissao_percentual ?? 0;
            $valorComissao = ($valorFinalVenda * $percentualComissao) / 100;

            // Prepara dados para atualização da venda
            $vendaData = [
                'data_venda' => $request->data_venda,
                'comprador' => $request->comprador,
                'valor_total' => $valorTotalItens,
                'desconto' => $desconto,
                'valor_final' => $valorFinalVenda,
                'metodo_pagamento' => $request->metodo_pagamento,
                'observacoes' => $request->observacoes,
                'status' => $request->status,
                'percentual_comissao' => $percentualComissao,
                'valor_comissao' => $valorComissao,
            ];

            // Lógica de comissão
            $comissaoAtivada = ($percentualComissao > 0);
            $vendedor = auth()->user();

            if ($comissaoAtivada && $vendedor) {
                $vendaData['user_id'] = $vendedor->id;
                $vendaData['comissao_paga'] = true;

                $categoriaComissao = \App\Models\Categoria::firstOrCreate(
                    ['nome' => 'Comissões'],
                    ['descricao' => 'Despesas geradas por comissões de vendas.']
                );

                if ($venda->despesa_id) {
                    $despesa = \App\Models\Despesa::find($venda->despesa_id);
                    if ($despesa) {
                        $despesa->update([
                            'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                            'valor' => $valorComissao,
                            'data' => now(),
                            'categoria_id' => $categoriaComissao->id,
                            'observacoes' => 'Comissão referente à venda #' . $venda->id,
                        ]);
                    }
                } else {
                    $despesa = \App\Models\Despesa::create([
                        'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                        'valor' => $valorComissao,
                        'data' => now(),
                        'categoria_id' => $categoriaComissao->id,
                        'observacoes' => 'Comissão referente à venda #' . $venda->id,
                    ]);
                    $vendaData['despesa_id'] = $despesa->id;
                }
            } elseif ($venda->despesa_id) {
                $despesa = \App\Models\Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->delete();
                }
                $vendaData['user_id'] = null;
                $vendaData['comissao_paga'] = false;
                $vendaData['despesa_id'] = null;
            }

            // Reverte status dos itens antigos
            foreach ($venda->vendaItems as $oldItem) {
                if ($oldItem->ave_id) {
                    $ave = Ave::find($oldItem->ave_id);
                    if ($ave) {
                        $ave->update([
                            'ativo' => true,
                            'vendavel' => true,
                            'data_inativado' => null
                        ]);
                    }
                } elseif ($oldItem->plantel_id) {
                    \App\Models\MovimentacaoPlantel::create([
                        'plantel_id' => $oldItem->plantel_id,
                        'tipo_movimentacao' => 'entrada',
                        'quantidade' => $oldItem->quantidade,
                        'data_movimentacao' => now(),
                        'observacoes' => 'Ajuste de reversão de venda (ID Venda: ' . $venda->id . ') devido a edição.',
                    ]);
                }
            }

            // Remove itens antigos
            $venda->vendaItems()->delete();

            // Cria novos itens
            foreach ($request->itens as $itemData) {
                $itemTotal = $itemData['quantidade'] * $itemData['preco_unitario'];

                $vendaItem = \App\Models\VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $itemData['descricao_item'],
                    'ave_id' => $itemData['ave_id'] ?? null,
                    'plantel_id' => $itemData['plantel_id'] ?? null,
                    'quantidade' => $itemData['quantidade'],
                    'preco_unitario' => $itemData['preco_unitario'],
                    'valor_total_item' => $itemTotal,
                ]);

                // Atualiza status dos novos itens
                if ($vendaItem->ave_id) {
                    $ave = Ave::find($vendaItem->ave_id);
                    if ($ave) {
                        $ave->update([
                            'ativo' => false,
                            'vendavel' => false,
                            'data_inativado' => now()
                        ]);
                    }
                } elseif ($vendaItem->plantel_id) {
                    \App\Models\MovimentacaoPlantel::create([
                        'plantel_id' => $vendaItem->plantel_id,
                        'tipo_movimentacao' => 'saida_venda',
                        'quantidade' => $vendaItem->quantidade,
                        'data_movimentacao' => $venda->data_venda,
                        'observacoes' => 'Venda de ' . $vendaItem->quantidade . ' aves do plantel ' . $vendaItem->plantel->identificacao_grupo . ' (Atualização de venda ID: ' . $venda->id . ')',
                    ]);
                }
            }

            // Atualiza venda
            $venda->update($vendaData);

            // Lógica para cancelamento/reversão
            if ($venda->wasChanged('status')) {
                if ($venda->status == 'cancelada') {
                    foreach ($venda->vendaItems as $item) {
                        if ($item->ave_id) {
                            $ave = Ave::find($item->ave_id);
                            if ($ave) {
                                $ave->update([
                                    'ativo' => true,
                                    'vendavel' => true,
                                    'data_inativado' => null
                                ]);
                            }
                        } elseif ($item->plantel_id) {
                            \App\Models\MovimentacaoPlantel::create([
                                'plantel_id' => $item->plantel_id,
                                'tipo_movimentacao' => 'entrada',
                                'quantidade' => $item->quantidade,
                                'data_movimentacao' => now(),
                                'observacoes' => 'Reversão de venda (ID Venda: ' . $venda->id . ') devido ao cancelamento.',
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('financeiro.vendas.show', $venda->id)
                ->with('success', 'Venda atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar venda: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma venda do banco de dados
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $venda = Venda::findOrFail($id);

            // Remove despesa de comissão se existir
            if ($venda->despesa_id) {
                $despesa = \App\Models\Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->delete();
                }
            }

            // Reverte status dos itens
            foreach ($venda->vendaItems as $item) {
                if ($item->ave_id) {
                    $ave = Ave::find($item->ave_id);
                    if ($ave) {
                        $ave->update([
                            'ativo' => true,
                            'vendavel' => true,
                            'data_inativado' => null
                        ]);
                    }
                } elseif ($item->plantel_id) {
                    \App\Models\MovimentacaoPlantel::create([
                        'plantel_id' => $item->plantel_id,
                        'tipo_movimentacao' => 'entrada',
                        'quantidade' => $item->quantidade,
                        'data_movimentacao' => now(),
                        'observacoes' => 'Reversão de venda (ID Venda: ' . $venda->id . ') devido à exclusão.',
                    ]);
                }
            }

            // Remove itens e depois a venda
            $venda->vendaItems()->delete();
            $venda->delete();

            DB::commit();
            return redirect()->route('financeiro.vendas.index')
                ->with('success', 'Venda excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir venda: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao excluir venda: ' . $e->getMessage());
        }
    }

    /**
     * Busca aves disponíveis para venda via AJAX
     */
    public function searchAvesForSale(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $aves = Ave::where('vendavel', true)
                ->where('ativo', true)
                ->where(function($q) use ($query) {
                    $q->where('matricula', 'like', '%' . $query . '%')
                      ->orWhereHas('tipoAve', function($q2) use ($query) {
                          $q2->where('nome', 'like', '%' . $query . '%');
                      });
                })
                ->with('tipoAve', 'variacao')
                ->limit(10)
                ->get();

        $results = $aves->map(function($ave) {
            $tipoAveNome = $ave->tipoAve->nome ?? 'N/A';
            $variacaoNome = $ave->variacao->nome ?? 'N/A';

            return [
                'id' => $ave->id,
                'matricula' => $ave->matricula,
                'tipo_ave' => $tipoAveNome,
                'variacao' => $variacaoNome,
                'preco_sugerido' => number_format($ave->preco_sugerido ?? 0.00, 2, '.', ''),
                'text' => "{$ave->matricula} ({$tipoAveNome})",
            ];
        });

        return response()->json($results);
    }
}