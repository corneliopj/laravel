<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contracheque;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceiroApiController extends Controller
{
    /**
     * Retorna o saldo do contracheque do mês atual para um funcionário.
     */
    public function saldo(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
        ]);

        $nome = $request->query('nome');
        
        // Busca o usuário pelo nome (ou parte do nome)
        $user = User::where('name', 'like', '%' . $nome . '%')->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Funcionário não encontrado.'
            ], 404);
        }

        $mes = Carbon::now()->month;
        $ano = Carbon::now()->year;

        // Calculamos o saldo seguindo a lógica do ContrachequeController
        // Lançamentos positivos (Soma)
        $positivos = Contracheque::where('user_id', $user->id)
            ->whereMonth('data', $mes)
            ->whereYear('data', $ano)
            ->where('tipo_lancamento', 'positivo')
            ->sum('valor');

        // Lançamentos negativos (Soma para subtrair)
        $negativos = Contracheque::where('user_id', $user->id)
            ->whereMonth('data', $mes)
            ->whereYear('data', $ano)
            ->where('tipo_lancamento', 'negativo')
            ->sum('valor');

        // O sistema também soma comissões de vendas no contracheque
        // No sistema atual, comissões são calculadas via Venda::where('user_id', $userId)
        // precisamos de acesso ao modelo Venda para somar as comissões do mês.
        
        // Importando Venda aqui para evitar dependências circulares ou organizar
        $comissoes = \App\Models\Venda::where('user_id', $user->id)
            ->where('comissao_paga', true)
            ->whereMonth('data_venda', $mes)
            ->whereYear('data_venda', $ano)
            ->sum('valor_final') * 0.15; // Assumindo 15% fixo conforme VendaController

        $saldoLiquido = $positivos + $comissoes - $negativos;

        return response()->json([
            'success' => true,
            'funcionario' => $user->name,
            'mes' => Carbon::now()->locale('pt_BR')->translatedFormat('F'),
            'ano' => $ano,
            'saldo' => round($saldoLiquido, 2),
            'detalhes' => [
                'lancamentos_positivos' => $positivos,
                'comissoes' => $comissoes,
                'lancamentos_negativos' => $negativos,
            ]
        ]);
    }
}
