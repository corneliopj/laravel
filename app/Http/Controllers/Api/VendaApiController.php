<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Ave;
use App\Models\Receita;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VendaApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'required|string',
            'item_id' => 'required', // Pode ser ID ou Matrícula
            'valor' => 'required|numeric',
            'metodo' => 'required|string',
            'observacao' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Localizar ou Criar Cliente
            $cliente = Cliente::firstOrCreate(
                ['nome' => $request->cliente],
                ['tipo_pessoa' => 'Física', 'ativo' => 1]
            );

            // 2. Localizar Ave (por ID ou Matrícula)
            $ave = Ave::where('id', $request->item_id)
                      ->orWhere('matricula', $request->item_id)
                      ->first();

            if (!$ave) {
                return response()->json(['error' => 'Ave não encontrada ou matrícula inválida.'], 404);
            }

            if (!$ave->ativo) {
                return response()->json(['error' => 'Esta ave já foi vendida ou está inativa.'], 400);
            }

            // 3. Criar a Venda
            $venda = Venda::create([
                'cliente_id' => $cliente->id,
                'data_venda' => now(),
                'valor_total' => $request->valor,
                'observacoes' => $request->observacao,
                'user_id' => Auth::id() ?? 1, // Fallback para admin se não houver user
            ]);

            // 4. Criar Item da Venda
            VendaItem::create([
                'venda_id' => $venda->id,
                'ave_id' => $ave->id,
                'valor_unitario' => $request->valor,
            ]);

            // 5. Baixa na Ave
            $ave->update(['ativo' => 0, 'data_venda' => now()]);

            // 6. Lançar Receita e Comissão (15%)
            // Receita Total
            Receita::create([
                'data' => now(),
                'valor' => $request->valor,
                'venda_id' => $venda->id,
                'descricao' => "Venda de ave {$ave->matricula}",
                'user_id' => Auth::id() ?? 1,
            ]);

            // Comissão do Vendedor (Exemplo: 15%)
            $comissao = $request->valor * 0.15;
            // Aqui você pode criar uma tabela de comissões, 
            // por enquanto vamos lançar como uma 'despesa' de comissão no financeiro
            // (Ajustar conforme sua tabela de despesas)
            
            return response()->json([
                'success' => true,
                'message' => "Venda registrada com sucesso!",
                'venda_id' => $venda->id,
                'ave' => $ave->matricula
            ]);
        });
    }
}
