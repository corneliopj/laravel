<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Ave;
use App\Models\Plantel;
use App\Models\MovimentacaoPlantel;
use App\Models\Categoria;
use App\Models\Despesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VendaApiController extends Controller
{
    /**
     * Registra uma nova venda via API.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'required', // Pode ser cliente_id ou nome
            'data' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'metodo' => 'required|string',
            'observacao' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.ave_id' => 'nullable|exists:aves,id',
            'itens.*.quantidade' => 'required|numeric|min:1',
            'itens.*.preco' => 'required|numeric|min:0',
        ]);

        $cliente = $request->cliente;
        $dataVenda = $request->data;
        $metodoPagamento = $request->metodo;
        $observacoes = $request->observacao;
        
        // Calculamos o valor total a partir dos itens para garantir consistência
        $valorTotal = collect($request->itens)->sum(function ($item) {
            return $item['quantidade'] * $item['preco'];
        });
        
        // Se o 'valor' enviado for diferente do total dos itens, podemos usar o 'valor' como valor final (com desconto/acréscimo)
        $valorFinal = $request->valor;
        $desconto = $valorTotal - $valorFinal;

        $vendedor = auth()->user();
        $comissaoPercentual = 15.0;
        $valorComissao = ($valorFinal * $comissaoPercentual) / 100;

        DB::beginTransaction();
        try {
            $venda = Venda::create([
                'comprador' => $cliente,
                'data_venda' => $dataVenda,
                'metodo_pagamento' => $metodoPagamento,
                'valor_total' => $valorTotal,
                'desconto' => $desconto,
                'valor_final' => $valorFinal,
                'observacoes' => $observacoes,
                'status' => 'concluida',
                'user_id' => $vendedor ? $vendedor->id : null,
                'comissao_percentual' => $comissaoPercentual,
                'comissao_paga' => true,
            ]);

            foreach ($request->itens as $item) {
                VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $item['ave_id'] ? 'Ave ID: ' . $item['ave_id'] : 'Item Genérico',
                    'ave_id' => $item['ave_id'] ?? null,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                    'valor_total_item' => $item['quantidade'] * $item['preco'],
                ]);

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
            }

            if ($vendedor && $valorComissao > 0) {
                $categoriaComissao = Categoria::firstOrCreate(
                    ['nome' => 'Comissões'],
                    ['descricao' => 'Despesas geradas por comissões de vendas.']
                );

                $despesaComissao = Despesa::create([
                    'descricao' => 'Comissão de Venda #' . $venda->id . ' - Vendedor: ' . $vendedor->name,
                    'valor' => $valorComissao,
                    'data' => now(),
                    'categoria_id' => $categoriaComissao->id,
                    'observacoes' => 'Comissão de ' . $comissaoPercentual . '% referente à venda #' . $venda->id,
                ]);

                $venda->update(['despesa_id' => $despesaComissao->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'venda_id' => $venda->id,
                'valor_final' => $valorFinal
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar venda via API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar venda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna a última venda realizada.
     */
    public function last()
    {
        $venda = Venda::with('vendaItems')->orderBy('id', 'desc')->first();

        if (!$venda) {
            return response()->json(['message' => 'Nenhuma venda encontrada.'], 404);
        }

        return response()->json([
            'id' => $venda->id,
            'comprador' => $venda->comprador,
            'data' => $venda->data_venda,
            'valor_final' => $venda->valor_final,
            'metodo' => $venda->metodo_pagamento,
            'itens' => $venda->vendaItems->map(function($item) {
                return [
                    'descricao' => $item->descricao_item,
                    'quantidade' => $item->quantidade,
                    'preco' => $item->preco_unitario,
                    'total' => $item->valor_total_item
                ];
            })
        ]);
    }
}
