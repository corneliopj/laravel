<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Despesa;
use App\Models\Categoria;
use App\Models\Ave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

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
                  ->orWhere('observacoes', 'like', '%' . $searchTerm . '%');
            });
        }

        $vendas = $query->with(['user', 'despesaComissao'])->orderBy('data_venda', 'desc')->paginate(10);

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
        $metodosPagamento = ['Dinheiro', 'Cartão de Crédito', 'Cartão de Débito', 'Pix', 'Transferência Bancária'];
        $avesDisponiveis = Ave::where('vendavel', true)->where('ativo', true)->with('tipoAve', 'variacao')->get();

        return view('financeiro.vendas.pdv', compact('metodosPagamento', 'avesDisponiveis'));
    }

    /**
     * Armazena uma nova venda no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_venda' => 'required|date|before_or_equal:today',
            'metodo_pagamento' => 'required|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'desconto' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|exists:aves,id',
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

            $vendaData = [
                'data_venda' => $request->data_venda,
                'valor_total' => $valorTotalItens,
                'desconto' => $desconto,
                'valor_final' => $valorFinalVenda,
                'metodo_pagamento' => $request->metodo_pagamento,
                'observacoes' => $request->observacoes,
                'status' => 'concluida',
                'reserva_id' => null,
                'user_id' => null,
                'comissao_percentual' => 0.00,
                'comissao_paga' => false,
                'despesa_id' => null,
            ];

            if ($request->has('com_comissao') && $request->input('com_comissao') == 'on') {
                $vendedor = Auth::user();
                if ($vendedor) {
                    $vendaData['user_id'] = $vendedor->id;
                    $vendaData['comissao_percentual'] = 15.00;

                    $valorComissao = $valorFinalVenda * 0.15;

                    $categoriaComissao = Categoria::firstOrCreate(
                        ['nome' => 'Comissões'],
                        ['descricao' => 'Despesas geradas por comissões de vendas.']
                    );

                    $despesa = Despesa::create([
                        'descricao' => 'Comissão de Venda (ID a ser definido) - Vendedor: ' . $vendedor->name,
                        'valor' => $valorComissao,
                        'data' => Carbon::now(), // CORRIGIDO: Usando 'data' em vez de 'data_despesa'
                        'categoria_id' => $categoriaComissao->id,
                        'observacoes' => 'Comissão referente à venda (ID a ser definido)',
                    ]);

                    $vendaData['despesa_id'] = $despesa->id;
                    $vendaData['comissao_paga'] = true;
                } else {
                    Log::warning('Tentativa de registrar comissão sem usuário logado para venda.');
                }
            }

            $venda = Venda::create($vendaData);

            if ($venda->despesa_id) {
                $despesa = Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->update([
                        'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . ($venda->user->name ?? 'N/A'),
                        'observacoes' => 'Comissão referente à venda #' . $venda->id,
                    ]);
                }
            }

            foreach ($request->items as $itemData) {
                $itemQuantidade = (float) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemTotal = $itemQuantidade * $itemPrecoUnitario;

                $vendaItem = VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $itemData['descricao_item'],
                    'ave_id' => $itemData['ave_id'] ?? null,
                    'quantidade' => $itemQuantidade,
                    'preco_unitario' => $itemPrecoUnitario,
                    'valor_total_item' => $itemTotal,
                ]);

                if ($vendaItem->ave_id) {
                    $ave = Ave::find($vendaItem->ave_id);
                    if ($ave) {
                        $ave->ativo = false;
                        $ave->vendavel = false;
                        $ave->data_inativado = Carbon::now();
                        $ave->save();
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
        $venda = Venda::with(['user', 'despesaComissao', 'vendaItems.ave.tipoAve'])->findOrFail($id);
        return view('financeiro.vendas.show', compact('venda'));
    }

    /**
     * Mostra o formulário para editar uma venda existente.
     */
    public function edit(string $id)
    {
        $venda = Venda::with(['user', 'despesaComissao', 'vendaItems.ave.tipoAve'])->findOrFail($id);
        $metodosPagamento = ['Dinheiro', 'Cartão de Crédito', 'Cartão de Débito', 'Pix', 'Transferência Bancária'];
        $statusOptions = ['concluida' => 'Concluída', 'pendente' => 'Pendente', 'cancelada' => 'Cancelada'];
        
        $avesEmOutrasVendas = VendaItem::whereHas('venda', function($q) use ($venda) {
            $q->where('id', '!=', $venda->id);
        })->pluck('ave_id')->filter()->toArray();

        $avesNaVendaAtual = $venda->vendaItems->pluck('ave_id')->filter()->toArray();

        $avesDisponiveis = Ave::where('vendavel', true)
                               ->where('ativo', true)
                               ->whereNotIn('id', $avesEmOutrasVendas)
                               ->orWhereIn('id', $avesNaVendaAtual)
                               ->with('tipoAve', 'variacao')
                               ->get();

        return view('financeiro.vendas.edit', compact('venda', 'metodosPagamento', 'statusOptions', 'avesDisponiveis'));
    }

    /**
     * Atualiza uma venda existente no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $venda = Venda::findOrFail($id);

        $request->validate([
            'data_venda' => 'required|date|before_or_equal:today',
            'metodo_pagamento' => 'required|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'desconto' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['concluida', 'pendente', 'cancelada'])],
            'items' => 'required|array|min:1',
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|exists:aves,id',
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

            $vendaData = [
                'data_venda' => $request->data_venda,
                'valor_total' => $valorTotalItens,
                'desconto' => $desconto,
                'valor_final' => $valorFinalVenda,
                'metodo_pagamento' => $request->metodo_pagamento,
                'observacoes' => $request->observacoes,
                'status' => $request->status,
            ];

            $comComissaoMarcado = $request->has('com_comissao') && $request->input('com_comissao') == 'on';
            $vendedor = Auth::user();

            if ($comComissaoMarcado && $vendedor) {
                $vendaData['user_id'] = $vendedor->id;
                $vendaData['comissao_percentual'] = 15.00;

                $valorComissao = $valorFinalVenda * 0.15;

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
                            'data' => Carbon::now(), // CORRIGIDO: Usando 'data' em vez de 'data_despesa'
                            'categoria_id' => $categoriaComissao->id,
                            'observacoes' => 'Comissão referente à venda #' . $venda->id,
                        ]);
                    } else {
                        $despesa = Despesa::create([
                            'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                            'valor' => $valorComissao,
                            'data' => Carbon::now(), // CORRIGIDO: Usando 'data' em vez de 'data_despesa'
                            'categoria_id' => $categoriaComissao->id,
                            'observacoes' => 'Comissão referente à venda #' . $venda->id,
                        ]);
                        $vendaData['despesa_id'] = $despesa->id;
                    }
                } else {
                    $despesa = Despesa::create([
                        'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                        'valor' => $valorComissao,
                        'data' => Carbon::now(), // CORRIGIDO: Usando 'data' em vez de 'data_despesa'
                        'categoria_id' => $categoriaComissao->id,
                        'observacoes' => 'Comissão referente à venda #' . $venda->id,
                    ]);
                    $vendaData['despesa_id'] = $despesa->id;
                }
                $vendaData['comissao_paga'] = true;

            } elseif (!$comComissaoMarcado && $venda->despesa_id) {
                $despesa = Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->delete();
                }
                $vendaData['user_id'] = null;
                $vendaData['comissao_percentual'] = 0.00;
                $vendaData['comissao_paga'] = false;
                $vendaData['despesa_id'] = null;
            } elseif (!$comComissaoMarcado && !$venda->despesa_id) {
                $vendaData['user_id'] = null;
                $vendaData['comissao_percentual'] = 0.00;
                $vendaData['comissao_paga'] = false;
                $vendaData['despesa_id'] = null;
            }

            foreach ($venda->vendaItems as $oldItem) {
                if ($oldItem->ave_id) {
                    $ave = Ave::find($oldItem->ave_id);
                    if ($ave) {
                        $ave->ativo = true;
                        $ave->vendavel = true;
                        $ave->data_inativado = null;
                        $ave->save();
                    }
                }
            }
            $venda->vendaItems()->delete();

            foreach ($request->items as $itemData) {
                $itemQuantidade = (float) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemTotal = $itemQuantidade * $itemPrecoUnitario;

                $vendaItem = VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $itemData['descricao_item'],
                    'ave_id' => $itemData['ave_id'] ?? null,
                    'quantidade' => $itemQuantidade,
                    'preco_unitario' => $itemPrecoUnitario,
                    'valor_total_item' => $itemTotal,
                ]);

                if ($vendaItem->ave_id) {
                    $ave = Ave::find($vendaItem->ave_id);
                    if ($ave) {
                        $ave->ativo = false;
                        $ave->vendavel = false;
                        $ave->data_inativado = Carbon::now();
                        $ave->save();
                    }
                }
            }

            $venda->update($vendaData);

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

            if ($venda->despesa_id) {
                $despesa = Despesa::find($venda->despesa_id);
                if ($despesa) {
                    $despesa->delete();
                }
            }

            foreach ($venda->vendaItems as $item) {
                if ($item->ave_id) {
                    $ave = Ave::find($item->ave_id);
                    if ($ave) {
                        $ave->ativo = true;
                        $ave->vendavel = true;
                        $ave->data_inativado = null;
                        $ave->save();
                    }
                }
            }
            $venda->vendaItems()->delete();

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
                'preco_sugerido' => number_format(0.00, 2, '.', ''),
                'text' => "{$ave->matricula} ({$tipoAveNome})",
            ];
        });

        Log::debug('searchAvesForSale: Resultados retornados: ' . json_encode($results->toArray()));
        return response()->json($results);
    }
}
