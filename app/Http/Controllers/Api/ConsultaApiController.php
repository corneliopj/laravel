<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venda;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\User;
use Carbon\Carbon;

class ConsultaApiController extends Controller
{
    public function ultimaVenda()
    {
        $venda = Venda::with(['cliente', 'vendaItems.ave'])->latest()->first();

        if (!$venda) {
            return response()->json(['message' => 'Nenhuma venda encontrada.'], 404);
        }

        return response()->json([
            'data' => $venda->data_venda,
            'cliente' => $venda->cliente->nome_cliente ?? $venda->comprador ?? 'Não informado',
            'valor' => $venda->valor_total,
            'itens' => $venda->vendaItems->map(fn($item) => $item->descricao_item ?? 'N/A')
        ]);
    }

    public function saldoFuncionario(Request $request)
    {
        try {
            $request->validate(['nome' => 'required|string']);
            
            $user = User::where('name', 'like', '%' . $request->nome . '%')->first();
            if (!$user) {
                return response()->json(['error' => 'Funcionário não encontrado.'], 404);
            }

            $inicioMes = Carbon::now()->startOfMonth();
            $fimMes = Carbon::now()->endOfMonth();

            // 1. Lançamentos do Contracheque
            $creditos = \App\Models\Contracheque::where('user_id', $user->id)
                                ->whereBetween('data', [$inicioMes, $fimMes])
                                ->where('tipo_lancamento', 'Positivo')
                                ->sum('valor');

            $debitos = \App\Models\Contracheque::where('user_id', $user->id)
                                ->whereBetween('data', [$inicioMes, $fimMes])
                                ->where('tipo_lancamento', 'Negativo')
                                ->sum('valor');

            // 2. Soma de Comissões de Vendas no mês
            // Segue a lógica do ContrachequeController: usa o valor da despesa de comissão vinculada
            $vendasComComissao = \App\Models\Venda::where('user_id', $user->id)
                                ->where('comissao_paga', true)
                                ->whereBetween('data_venda', [$inicioMes, $fimMes])
                                ->with('despesaComissao')
                                ->get();

            $comissoes = $vendasComComissao->sum(function($venda) {
                return $venda->despesaComissao ? $venda->despesaComissao->valor : 0;
            });

            $saldo = ($creditos - $debitos) + $comissoes;

            return response()->json([
                'funcionario' => $user->name,
                'periodo' => $inicioMes->format('M/Y'),
                'detalhes' => [
                    'contracheque_creditos' => $creditos,
                    'contracheque_debitos' => $debitos,
                    'comissoes_vendas' => $comissoes,
                ],
                'saldo_total' => $saldo
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro de Banco: ' . $e->getMessage()], 500);
        }
    }
}
