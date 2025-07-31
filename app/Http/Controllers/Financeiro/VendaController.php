<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Despesa; // Importar o modelo Despesa
use App\Models\Categoria; // Importar o modelo Categoria
use App\Models\Ave;
use App\Models\Plantel; // Importar o modelo Plantel
use App\Models\MovimentacaoPlantel; // Importar o modelo MovimentacaoPlantel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para Auth::user()
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\User; // Importar o modelo User para o relacionamento

class VendaController extends Controller
{
    /**
     * Exibe uma lista de vendas.
     */
    public function index(Request $request)
    {
        $query = Venda::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_venda', '>=', Carbon::parse($request->data_inicio)->startOfDay());
        }

        if ($request->filled('data_fim')) {
            $query->where('data_venda', '<=', Carbon::parse($request->data_fim)->endOfDay());
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('observacoes', 'like', '%' . $searchTerm . '%')
                  ->orWhere('comprador', 'like', '%' . $searchTerm . '%'); // Adicionado busca por comprador
            });
        }

        // Carrega os relacionamentos para a listagem
        $vendas = $query->with(['user', 'despesaComissao', 'vendaItems'])->orderBy('data_venda', 'desc')->paginate(10);

        $statusOptions = [
            'concluida' => 'Concluída',
            'pendente' => 'Pendente',
            'cancelada' => 'Cancelada',
        ];

        return view('financeiro.vendas.index', compact('vendas', 'statusOptions', 'request'));
    }

    /**
     * Mostra o formulário para criar uma nova venda.
     */
    public function create()
    {
        $metodosPagamento = ['Dinheiro' => 'Dinheiro', 'Cartão de Crédito' => 'Cartão de Crédito', 'Cartão de Débito' => 'Cartão de Débito', 'Pix' => 'Pix', 'Transferência Bancária' => 'Transferência Bancária', 'Outro' => 'Outro'];
        
        // Aves disponíveis para venda (ativas e vendáveis)
        $avesDisponiveis = Ave::where('vendavel', true)->where('ativo', true)->with('tipoAve', 'variacao')->get();
        // Plantéis disponíveis (ativos)
        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        return view('financeiro.vendas.create', compact('metodosPagamento', 'avesDisponiveis', 'plantelOptions'));
    }

    /**
     * Armazena uma nova venda no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_venda' => 'required|date|before_or_equal:today',
            'comprador' => 'nullable|string|max:255', // Adicionado ao validate
            'metodo_pagamento' => 'required|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'desconto' => 'nullable|numeric|min:0',
            'comissao_percentual' => 'nullable|numeric|min:0|max:100', // NOVO: Validação para percentual
            'items' => 'required|array|min:1',
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|exists:aves,id',
            'items.*.plantel_id' => 'nullable|exists:plantel,id', // Adicionado validação para plantel_id
            'items.*.quantidade' => 'required|integer|min:1',
            'items.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $valorTotalItens = 0;
            foreach ($request->items as $item) {
                $quantidade = (float) $item['quantidade'];
                $precoUnitario = (float) $item['preco_unitario'];
                $valorTotalItens += ($quantidade * $precoUnitario);
            }

            $desconto = (float) ($request->desconto ?? 0);
            $valorFinalVenda = $valorTotalItens - $desconto;

            if ($valorFinalVenda < 0) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'O valor final da venda não pode ser negativo.');
            }

            $percentualComissao = (float) ($request->comissao_percentual ?? 0);
            $valorComissao = ($valorFinalVenda * $percentualComissao) / 100;

            $vendaData = [
                'data_venda' => $request->data_venda,
                'comprador' => $request->comprador, // Salvar comprador
                'valor_total' => $valorTotalItens,
                'desconto' => $desconto,
                'valor_final' => $valorFinalVenda,
                'metodo_pagamento' => $request->metodo_pagamento,
                'observacoes' => $request->observacoes,
                'status' => 'concluida',
                'reserva_id' => null, // Manter null se não houver lógica de reserva aqui
                'user_id' => null,
                'comissao_percentual' => $percentualComissao,
                'comissao_paga' => false,
                'despesa_id' => null,
            ];

            // Lógica de Comissão
            if ($percentualComissao > 0) {
                $vendedor = Auth::user();
                if ($vendedor) {
                    $vendaData['user_id'] = $vendedor->id;

                    $categoriaComissao = Categoria::firstOrCreate(
                        ['nome' => 'Comissões'],
                        ['descricao' => 'Despesas geradas por comissões de vendas.']
                    );

                    $despesa = Despesa::create([
                        'descricao' => 'Comissão de Venda (ID a ser definido) - Vendedor: ' . $vendedor->name,
                        'valor' => $valorComissao,
                        'data' => Carbon::now(),
                        'categoria_id' => $categoriaComissao->id,
                        'observacoes' => 'Comissão referente à venda (ID a ser definido)',
                    ]);

                    $vendaData['despesa_id'] = $despesa->id;
                    $vendaData['comissao_paga'] = true;
                } else {
                    Log::warning('Tentativa de registrar comissão sem usuário logado para venda.');
                    // Resetar campos de comissão se não houver usuário logado
                    $vendaData['percentual_comissao'] = 0.00;
                    $vendaData['valor_comissao'] = 0.00;
                }
            } else {
                $vendaData['valor_comissao'] = 0.00; // Garantir que seja 0 se não houver percentual
            }

            $venda = Venda::create($vendaData);

            // Atualizar a descrição da despesa com o ID da venda
            if ($venda->despesa_id) {
                $despesa = Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->update([
                        'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . ($venda->user->name ?? 'N/A'),
                        'observacoes' => 'Comissão referente à venda #' . $venda->id,
                    ]);
                }
            }

            // Processar itens da venda
            foreach ($request->items as $itemData) {
                $itemQuantidade = (float) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemTotal = $itemQuantidade * $itemPrecoUnitario;

                $vendaItem = VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $itemData['descricao_item'],
                    'ave_id' => $itemData['ave_id'] ?? null,
                    'plantel_id' => $itemData['plantel_id'] ?? null, // Salvar plantel_id
                    'quantidade' => $itemQuantidade,
                    'preco_unitario' => $itemPrecoUnitario,
                    'valor_total_item' => $itemTotal,
                ]);

                // Lógica de inativação/movimentação
                if ($vendaItem->ave_id) {
                    $ave = Ave::find($vendaItem->ave_id);
                    if ($ave) {
                        $ave->ativo = false;
                        $ave->vendavel = false;
                        $ave->data_inativado = Carbon::now();
                        $ave->save();
                    }
                } elseif ($vendaItem->plantel_id) {
                    $plantel = Plantel::find($vendaItem->plantel_id);
                    if ($plantel) {
                        MovimentacaoPlantel::create([
                            'plantel_id' => $plantel->id,
                            'tipo_movimentacao' => 'saida_venda',
                            'quantidade' => $vendaItem->quantidade,
                            'data_movimentacao' => $venda->data_venda,
                            'observacoes' => 'Venda de ' . $vendaItem->quantidade . ' aves do plantel ' . $plantel->identificacao_grupo . ' (Venda ID: ' . $venda->id . '). Comprador: ' . ($request->comprador ?? 'Não informado'),
                        ]);
                    }
                }
            }

            DB::commit();
            Log::info('Venda criada com sucesso: ' . $venda->id);
            return redirect()->route('financeiro.vendas.show', $venda->id)->with('success', 'Venda registrada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao criar venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de uma venda específica.
     */
    public function show(string $id)
    {
        $venda = Venda::with(['user', 'despesaComissao', 'vendaItems.ave.tipoAve', 'vendaItems.plantel'])->findOrFail($id);
        return view('financeiro.vendas.show', compact('venda'));
    }

    /**
     * Mostra o formulário para editar uma venda existente.
     */
    public function edit(string $id)
    {
        $venda = Venda::with(['user', 'despesaComissao', 'vendaItems.ave.tipoAve', 'vendaItems.plantel'])->findOrFail($id);
        $metodosPagamento = ['Dinheiro' => 'Dinheiro', 'Cartão de Crédito' => 'Cartão de Crédito', 'Cartão de Débito' => 'Cartão de Débito', 'Pix' => 'Pix', 'Transferência Bancária' => 'Transferência Bancária', 'Outro' => 'Outro'];
        $statusOptions = ['concluida' => 'Concluída', 'pendente' => 'Pendente', 'cancelada' => 'Cancelada'];
        
        // Aves que já estão na venda atual devem estar disponíveis
        $avesNaVendaAtual = $venda->vendaItems->pluck('ave_id')->filter()->toArray();

        // Aves que estão ativas e vendáveis, ou que já estão na venda atual
        $avesDisponiveis = Ave::where('vendavel', true)
                               ->where('ativo', true)
                               ->orWhereIn('id', $avesNaVendaAtual) // Inclui aves que já estão nesta venda
                               ->with('tipoAve', 'variacao')
                               ->get();

        $plantelOptions = Plantel::where('ativo', true)->orderBy('identificacao_grupo')->get();

        return view('financeiro.vendas.edit', compact('venda', 'metodosPagamento', 'statusOptions', 'avesDisponiveis', 'plantelOptions'));
    }

    /**
     * Atualiza uma venda existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $venda = Venda::findOrFail($id);

        $request->validate([
            'data_venda' => 'required|date|before_or_equal:today',
            'comprador' => 'nullable|string|max:255', // Adicionado ao validate
            'metodo_pagamento' => 'required|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'desconto' => 'nullable|numeric|min:0',
            'comissao_percentual' => 'nullable|numeric|min:0|max:100', // NOVO: Validação
            'status' => ['required', Rule::in(['concluida', 'pendente', 'cancelada'])],
            'items' => 'required|array|min:1',
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|exists:aves,id',
            'items.*.plantel_id' => 'nullable|exists:plantel,id', // Adicionado validação
            'items.*.quantidade' => 'required|integer|min:1',
            'items.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $valorTotalItens = 0;
            foreach ($request->items as $item) {
                $quantidade = (float) $item['quantidade'];
                $precoUnitario = (float) $item['preco_unitario'];
                $valorTotalItens += ($quantidade * $precoUnitario);
            }

            $desconto = (float) ($request->desconto ?? 0);
            $valorFinalVenda = $valorTotalItens - $desconto;

            if ($valorFinalVenda < 0) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'O valor final da venda não pode ser negativo.');
            }

            $percentualComissao = (float) ($request->comissao_percentual ?? 0);
            $valorComissao = ($valorFinalVenda * $percentualComissao) / 100;

            $vendaData = [
                'data_venda' => $request->data_venda,
                'comprador' => $request->comprador, // Salvar comprador
                'valor_total' => $valorTotalItens,
                'desconto' => $desconto,
                'valor_final' => $valorFinalVenda,
                'metodo_pagamento' => $request->metodo_pagamento,
                'observacoes' => $request->observacoes,
                'status' => $request->status,
                'percentual_comissao' => $percentualComissao,
                'valor_comissao' => $valorComissao, // Salvar valor da comissão
            ];

            $comissaoAtivada = ($percentualComissao > 0);
            $vendedor = Auth::user();

            // Lógica de Comissão
            if ($comissaoAtivada && $vendedor) {
                $vendaData['user_id'] = $vendedor->id;
                $vendaData['comissao_paga'] = true;

                $categoriaComissao = Categoria::firstOrCreate(
                    ['nome' => 'Comissões'],
                    ['descricao' => 'Despesas geradas por comissões de vendas.']
                );

                if ($venda->despesa_id) {
                    $despesa = Despesa::find($venda->despesa_id);
                    if ($despesa) {
                        $despesa->update([
                            'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                            'valor' => $valorComissao,
                            'data' => Carbon::now(),
                            'categoria_id' => $categoriaComissao->id,
                            'observacoes' => 'Comissão referente à venda #' . $venda->id,
                        ]);
                    } else { // Despesa_id existia mas o registro foi apagado
                        $despesa = Despesa::create([
                            'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                            'valor' => $valorComissao,
                            'data' => Carbon::now(),
                            'categoria_id' => $categoriaComissao->id,
                            'observacoes' => 'Comissão referente à venda #' . $venda->id,
                        ]);
                        $vendaData['despesa_id'] = $despesa->id;
                    }
                } else { // Não existia despesa_id
                    $despesa = Despesa::create([
                        'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                        'valor' => $valorComissao,
                        'data' => Carbon::now(),
                        'categoria_id' => $categoriaComissao->id,
                        'observacoes' => 'Comissão referente à venda #' . $venda->id,
                    ]);
                    $vendaData['despesa_id'] = $despesa->id;
                }
            } elseif (!$comissaoAtivada && $venda->despesa_id) { // Comissão desativada e despesa existia
                $despesa = Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->delete();
                }
                $vendaData['user_id'] = null;
                $vendaData['comissao_paga'] = false;
                $vendaData['despesa_id'] = null;
            } elseif (!$comissaoAtivada && !$venda->despesa_id) { // Comissão desativada e despesa não existia
                $vendaData['user_id'] = null;
                $vendaData['comissao_paga'] = false;
                $vendaData['despesa_id'] = null;
            }

            // Reverter o status das aves/plantéis dos itens antigos
            foreach ($venda->vendaItems as $oldItem) {
                if ($oldItem->ave_id) {
                    $ave = Ave::find($oldItem->ave_id);
                    if ($ave) {
                        $ave->ativo = true;
                        $ave->vendavel = true;
                        $ave->data_inativado = null;
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
            // Deletar todos os itens antigos da venda
            $venda->vendaItems()->delete();

            // Processar e criar os novos itens
            foreach ($request->items as $itemData) {
                $itemQuantidade = (float) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemTotal = $itemQuantidade * $itemPrecoUnitario;

                $vendaItem = VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $itemData['descricao_item'],
                    'ave_id' => $itemData['ave_id'] ?? null,
                    'plantel_id' => $itemData['plantel_id'] ?? null,
                    'quantidade' => $itemQuantidade,
                    'preco_unitario' => $itemPrecoUnitario,
                    'valor_total_item' => $itemTotal,
                ]);

                // Lógica de inativação/movimentação para novos itens
                if ($vendaItem->ave_id) {
                    $ave = Ave::find($vendaItem->ave_id);
                    if ($ave) {
                        $ave->ativo = false;
                        $ave->vendavel = false;
                        $ave->data_inativado = Carbon::now();
                        $ave->save();
                    }
                } elseif ($vendaItem->plantel_id) {
                    $plantel = Plantel::find($vendaItem->plantel_id);
                    if ($plantel) {
                        MovimentacaoPlantel::create([
                            'plantel_id' => $plantel->id,
                            'tipo_movimentacao' => 'saida_venda',
                            'quantidade' => $vendaItem->quantidade,
                            'data_movimentacao' => $venda->data_venda,
                            'observacoes' => 'Venda de ' . $vendaItem->quantidade . ' aves do plantel ' . $plantel->identificacao_grupo . ' (Atualização de venda ID: ' . $venda->id . '). Comprador: ' . ($request->comprador ?? 'Não informado'),
                        ]);
                    }
                }
            }

            $venda->update($vendaData);

            // Lógica para reverter status de aves se a venda for cancelada
            if ($venda->isDirty('status')) {
                if ($venda->status == 'cancelada' && $venda->getOriginal('status') != 'cancelada') {
                    foreach ($venda->vendaItems as $item) {
                        if ($item->ave_id) {
                            $ave = Ave::find($item->ave_id);
                            if ($ave) {
                                $ave->ativo = true;
                                $ave->vendavel = true;
                                $ave->data_inativado = null;
                                $ave->save();
                            }
                        } elseif ($item->plantel_id) {
                             MovimentacaoPlantel::create([
                                'plantel_id' => $item->plantel_id,
                                'tipo_movimentacao' => 'entrada',
                                'quantidade' => $item->quantidade,
                                'data_movimentacao' => Carbon::now(),
                                'observacoes' => 'Reversão de venda (ID Venda: ' . $venda->id . ') devido ao cancelamento.',
                            ]);
                        }
                    }
                } elseif ($venda->status == 'concluida' && $venda->getOriginal('status') == 'cancelada') {
                    foreach ($venda->vendaItems as $item) {
                        if ($item->ave_id) {
                            $ave = Ave::find($item->ave_id);
                            if ($ave) {
                                $ave->ativo = false;
                                $ave->vendavel = false;
                                $ave->data_inativado = Carbon::now();
                                $ave->save();
                            }
                        } elseif ($item->plantel_id) {
                            MovimentacaoPlantel::create([
                                'plantel_id' => $item->plantel_id,
                                'tipo_movimentacao' => 'saida_venda',
                                'quantidade' => $item->quantidade,
                                'data_movimentacao' => Carbon::now(),
                                'observacoes' => 'Re-aplicação de venda (ID Venda: ' . $venda->id . ') devido à reativação.',
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            Log::info('Venda atualizada com sucesso: ' . $venda->id);
            return redirect()->route('financeiro.vendas.show', $venda->id)->with('success', 'Venda atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma venda do banco de dados.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $venda = Venda::findOrFail($id);

            // Se houver uma despesa de comissão, excluí-la
            if ($venda->despesa_id) {
                $despesa = Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->delete();
                }
            }

            // Reverter o status das aves/plantéis associados
            foreach ($venda->vendaItems as $item) {
                if ($item->ave_id) {
                    $ave = Ave::find($item->ave_id);
                    if ($ave) {
                        $ave->ativo = true;
                        $ave->vendavel = true;
                        $ave->data_inativado = null;
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
            // Excluir os itens da venda
            $venda->vendaItems()->delete();

            // Excluir a venda
            $venda->delete();
            DB::commit();
            Log::info('Venda excluída com sucesso: ' . $venda->id);
            return redirect()->route('financeiro.vendas.index')->with('success', 'Venda excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir venda: " . $e->getMessage() . " - Linha: " . $e->getLine() . " - Arquivo: " . $e->getFile());
            return redirect()->back()->with('error', 'Erro ao excluir venda: ' . $e->getMessage());
        }
    }

    /**
     * Busca aves disponíveis para venda via AJAX para o PDV.
     */
    public function searchAvesForSale(Request $request)
    {
        $query = $request->get('q');
        Log::debug("searchAvesForSale: Query recebida: '{$query}'");

        if (empty($query)) {
            Log::debug("searchAvesForSale: Query vazia, retornando array vazio.");
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

        Log::debug('searchAvesForSale: Resultados retornados: ' . json_encode($results->toArray()));
        return response()->json($results);
    }
}
