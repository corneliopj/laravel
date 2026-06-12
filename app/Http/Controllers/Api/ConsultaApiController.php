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

    public function saldoFuncionaria(Request $request)
    {
        $request->validate(['nome' => 'required|string']);
        
        $user = User::where('name', 'like', '%' . $request->nome . '%')->first();
        if (!$user) {
            return response()->json(['error' => 'Funcionário não encontrado.'], 404);
        }

        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $receitas = Receita::where('user_id', $user->id)
                            ->whereBetween('data', [$inicioMes, $fimMes])
                            ->sum('valor');

        $despesas = Despesa::where('user_id', $user->id)
                            ->whereBetween('data', [$inicioMes, $fimMes])
                            ->sum('valor');

        return response()->json([
            'funcionario' => $user->name,
            'periodo' => $inicioMes->format('M/Y'),
            'receitas' => $receitas,
            'despesas' => $despesas,
            'saldo_liquido' => $receitas - $despesas
        ]);
    }
}
