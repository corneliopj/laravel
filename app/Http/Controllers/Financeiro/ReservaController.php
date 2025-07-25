<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\ReservaItem;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Ave;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Certifique-se de que o Log está importado

class ReservaController extends Controller
{
    /**
     * Exibe uma lista de todas as reservas.
     */
    public function index(Request $request)
    {
        $query = Reserva::query()->with('items.ave.tipoAve');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('data_inicio')) {
            $query->where('data_reserva', '>=', Carbon::parse($request->data_inicio)->startOfDay());
        }
        if ($request->filled('data_fim')) {
            $query->where('data_reserva', '<=', Carbon::parse($request->data_fim)->endOfDay());
        }

        $reservas = $query->orderBy('data_reserva', 'desc')->paginate(10);

        $statusOptions = ['pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'cancelada' => 'Cancelada', 'convertida_venda' => 'Convertida em Venda'];

        return view('financeiro.reservas.index', compact('reservas', 'statusOptions', 'request'));
    }

    /**
     * Exibe o formulário para criar uma nova reserva.
     */
    public function create()
    {
        $avesEmReserva = ReservaItem::whereHas('reserva', function($q) {
            $q->whereIn('status', ['pendente', 'confirmada']);
        })->pluck('ave_id')->filter()->toArray(); // Adicionado filter() para remover nulos

        $avesDisponiveis = Ave::where('vendavel', true)
                               ->where('ativo', true)
                               ->whereNotIn('id', $avesEmReserva)
                               ->with('tipoAve', 'variacao')
                               ->get();

        return view('financeiro.reservas.create', compact('avesDisponiveis'));
    }

    /**
     * Armazena uma nova reserva e seus itens no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_reserva' => 'required|date|before_or_equal:today',
            'data_prevista_entrega' => 'nullable|date|after_or_equal:data_reserva',
            'data_vencimento_proposta' => 'nullable|date|after_or_equal:data_reserva',
            'nome_cliente' => 'nullable|string|max:255',
            'contato_cliente' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'pagamento_parcial' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|exists:aves,id',
            'items.*.quantidade' => 'required|integer|min:1',
            'items.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $valorTotal = 0;
            foreach ($request->items as $item) {
                // Correção: Garante que quantidade e preco_unitario são floats antes da soma
                $quantidade = (float) $item['quantidade'];
                $precoUnitario = (float) $item['preco_unitario'];
                $valorTotal += ($quantidade * $precoUnitario);
            }

            // Correção: Garante que pagamento_parcial é float
            $pagamentoParcial = (float) ($request->pagamento_parcial ?? 0);

            // Gerar número de reserva único
            do {
                $numeroReserva = 'RES-' . Str::upper(Str::random(6));
            } while (Reserva::where('numero_reserva', $numeroReserva)->exists());

            $reserva = Reserva::create([
                'numero_reserva' => $numeroReserva,
                'data_reserva' => $request->data_reserva,
                'data_prevista_entrega' => $request->data_prevista_entrega,
                'data_vencimento_proposta' => $request->data_vencimento_proposta,
                'valor_total' => $valorTotal,
                'pagamento_parcial' => $pagamentoParcial, // Usa o valor já convertido
                'nome_cliente' => $request->nome_cliente,
                'contato_cliente' => $request->contato_cliente,
                'observacoes' => $request->observacoes,
                'status' => 'pendente',
            ]);

            foreach ($request->items as $itemData) {
                // Correção: Garante que quantidade e preco_unitario são floats antes da soma
                $itemQuantidade = (float) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemTotal = $itemQuantidade * $itemPrecoUnitario;

                $reservaItem = ReservaItem::create([
                    'reserva_id' => $reserva->id,
                    'descricao_item' => $itemData['descricao_item'],
                    'ave_id' => $itemData['ave_id'] ?? null,
                    'quantidade' => $itemQuantidade, // Usa o valor já convertido
                    'preco_unitario' => $itemPrecoUnitario, // Usa o valor já convertido
                    'valor_total_item' => $itemTotal,
                ]);

                if ($reservaItem->ave_id) {
                    $ave = Ave::find($reservaItem->ave_id);
                    if ($ave) {
                        $ave->vendavel = false;
                        $ave->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('financeiro.reservas.index')->with('success', 'Reserva criada com sucesso! Número da Reserva: ' . $numeroReserva);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar reserva: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao criar a reserva: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de uma reserva específica.
     */
    public function show(string $id)
    {
        $reserva = Reserva::with('items.ave.tipoAve', 'vendas')->findOrFail($id);
        return view('financeiro.reservas.show', compact('reserva'));
    }

    /**
     * Exibe o formulário para editar uma reserva existente.
     */
    public function edit(string $id)
    {
        $reserva = Reserva::with('items.ave')->findOrFail($id);

        $avesEmOutrasReservas = ReservaItem::whereHas('reserva', function($q) use ($reserva) {
            $q->whereIn('status', ['pendente', 'confirmada'])
              ->where('id', '!=', $reserva->id);
        })->pluck('ave_id')->filter()->toArray(); // Adicionado filter() para remover nulos

        // Inclui aves da reserva atual para que elas apareçam pré-selecionadas
        $avesNaReservaAtual = $reserva->items->pluck('ave_id')->filter()->toArray();

        $avesDisponiveis = Ave::where('vendavel', true)
                               ->where('ativo', true)
                               ->whereNotIn('id', $avesEmOutrasReservas)
                               ->orWhereIn('id', $avesNaReservaAtual)
                               ->with('tipoAve', 'variacao')
                               ->get();

        $statusOptions = ['pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'cancelada' => 'Cancelada', 'convertida_venda' => 'Convertida em Venda'];

        return view('financeiro.reservas.edit', compact('reserva', 'avesDisponiveis', 'statusOptions'));
    }

    /**
     * Atualiza uma reserva no banco de dados.
     */
    public function update(Request $request, string $id)
    {
        $reserva = Reserva::findOrFail($id);

        $request->validate([
            'data_reserva' => 'required|date|before_or_equal:today',
            'data_prevista_entrega' => 'nullable|date|after_or_equal:data_reserva',
            'data_vencimento_proposta' => 'nullable|date|after_or_equal:data_reserva',
            'nome_cliente' => 'nullable|string|max:255',
            'contato_cliente' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'pagamento_parcial' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['pendente', 'confirmada', 'cancelada', 'convertida_venda'])],
            'items' => 'required|array|min:1',
            'items.*.descricao_item' => 'required|string|max:255',
            'items.*.ave_id' => 'nullable|exists:aves,id',
            'items.*.quantidade' => 'required|integer|min:1',
            'items.*.preco_unitario' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            // Reverter status das aves dos itens antigos antes de atualizar
            foreach ($reserva->items as $oldItem) {
                if ($oldItem->ave_id) {
                    $ave = Ave::find($oldItem->ave_id);
                    if ($ave) {
                        $ave->vendavel = true;
                        $ave->save();
                    }
                }
            }

            $valorTotal = 0;
            foreach ($request->items as $item) {
                // Correção: Garante que quantidade e preco_unitario são floats antes da soma
                $quantidade = (float) $item['quantidade'];
                $precoUnitario = (float) $item['preco_unitario'];
                $valorTotal += ($quantidade * $precoUnitario);
            }

            // Correção: Garante que pagamento_parcial é float
            $pagamentoParcial = (float) ($request->pagamento_parcial ?? 0);

            $reserva->update([
                'data_reserva' => $request->data_reserva,
                'data_prevista_entrega' => $request->data_prevista_entrega,
                'data_vencimento_proposta' => $request->data_vencimento_proposta,
                'valor_total' => $valorTotal,
                'pagamento_parcial' => $pagamentoParcial, // Usa o valor já convertido
                'nome_cliente' => $request->nome_cliente,
                'contato_cliente' => $request->contato_cliente,
                'observacoes' => $request->observacoes,
                'status' => $request->status,
            ]);

            $reserva->items()->delete();
            foreach ($request->items as $itemData) {
                // Correção: Garante que quantidade e preco_unitario são floats antes da soma
                $itemQuantidade = (float) $itemData['quantidade'];
                $itemPrecoUnitario = (float) $itemData['preco_unitario'];
                $itemTotal = $itemQuantidade * $itemPrecoUnitario;

                $reservaItem = ReservaItem::create([
                    'reserva_id' => $reserva->id,
                    'descricao_item' => $itemData['descricao_item'],
                    'ave_id' => $itemData['ave_id'] ?? null,
                    'quantidade' => $itemQuantidade, // Usa o valor já convertido
                    'preco_unitario' => $itemPrecoUnitario, // Usa o valor já convertido
                    'valor_total_item' => $itemTotal,
                ]);

                if ($reservaItem->ave_id) {
                    $ave = Ave::find($reservaItem->ave_id);
                    if ($ave) {
                        $ave->vendavel = false;
                        $ave->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('financeiro.reservas.index')->with('success', 'Reserva atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar reserva: ' . $e->getMessage(), ['reserva_id' => $id, 'request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a reserva: ' . $e->getMessage());
        }
    }

    /**
     * Remove (soft delete) uma reserva do banco de dados.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $reserva = Reserva::findOrFail($id);

            foreach ($reserva->items as $item) {
                if ($item->ave_id) {
                    $ave = Ave::find($item->ave_id);
                    if ($ave) {
                        $ave->vendavel = true;
                        $ave->save();
                    }
                }
            }

            $reserva->delete();

            DB::commit();
            return redirect()->route('financeiro.reservas.index')->with('success', 'Reserva excluída com sucesso! Aves associadas foram liberadas.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir reserva: ' . $e->getMessage(), ['reserva_id' => $id]);
            return redirect()->route('financeiro.reservas.index')->with('error', 'Erro ao excluir a reserva: ' . $e->getMessage());
        }
    }

    /**
     * Converte uma reserva em uma venda.
     */
    public function convertToVenda(string $id)
    {
        $reserva = Reserva::with('items.ave')->findOrFail($id);

        if ($reserva->status == 'convertida_venda') {
            return redirect()->back()->with('error', 'Esta reserva já foi convertida em venda.');
        }

        DB::beginTransaction();
        try {
            $venda = Venda::create([
                'data_venda' => Carbon::now(),
                'valor_total' => $reserva->valor_total,
                'desconto' => 0,
                'valor_final' => (float) $reserva->valor_total - (float) $reserva->pagamento_parcial, // Garante que a subtração é numérica
                'metodo_pagamento' => 'A Definir',
                'observacoes' => 'Venda gerada a partir da Reserva #' . $reserva->numero_reserva . '. Pagamento parcial recebido: R$ ' . number_format($reserva->pagamento_parcial, 2, ',', '.'),
                'status' => 'pendente',
                'reserva_id' => $reserva->id,
            ]);

            foreach ($reserva->items as $reservaItem) {
                VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $reservaItem->descricao_item,
                    'ave_id' => $reservaItem->ave_id,
                    'quantidade' => $reservaItem->quantidade,
                    'preco_unitario' => $reservaItem->preco_unitario,
                    'valor_total_item' => $reservaItem->valor_total_item,
                ]);

                if ($reservaItem->ave_id) {
                    $ave = Ave::find($reservaItem->ave_id);
                    if ($ave) {
                        $ave->ativo = false;
                        $ave->vendavel = false;
                        $ave->data_inativado = Carbon::now();
                        $ave->save();
                    }
                }
            }

            $reserva->status = 'convertida_venda';
            $reserva->save();

            DB::commit();
            return redirect()->route('financeiro.vendas.show', $venda->id)->with('success', 'Reserva convertida em venda com sucesso! Venda #' . $venda->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao converter reserva em venda: ' . $e->getMessage(), ['reserva_id' => $id]);
            return redirect()->back()->with('error', 'Erro ao converter a reserva em venda: ' . $e->getMessage());
        }
    }

    /**
     * Busca aves disponíveis para reserva via AJAX.
     */
    public function searchAvesForReserva(Request $request)
    {
        $query = $request->get('q');
        Log::debug("searchAvesForReserva: Query recebida: '{$query}'");

        if (empty($query)) {
            Log::debug("searchAvesForReserva: Query vazia, retornando array vazio.");
            return response()->json([]);
        }

        $avesEmReserva = ReservaItem::whereHas('reserva', function($q) {
            $q->whereIn('status', ['pendente', 'confirmada']);
        })->pluck('ave_id')->filter()->toArray();

        $aves = Ave::where('vendavel', true)
                    ->where('ativo', true)
                    ->whereNotIn('id', $avesEmReserva)
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

        Log::debug('searchAvesForReserva: Resultados retornados: ' . json_encode($results->toArray()));
        return response()->json($results);
    }
}
