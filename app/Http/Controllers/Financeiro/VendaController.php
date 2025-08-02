<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\VendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendasController extends Controller
{
    public function index(Request $request)
    {
        $query = Venda::with('vendaItems');
        
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

    public function create()
    {
        $compradores = Venda::distinct()->pluck('comprador')->toArray();
        $metodosPagamento = [
            'dinheiro' => 'Dinheiro',
            'cartao' => 'Cartão',
            'transferencia' => 'Transferência',
            'pix' => 'PIX'
        ];
        
        return view('financeiro.vendas.create', compact('compradores', 'metodosPagamento'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'comprador' => 'required',
            'data_venda' => 'required|date',
            'metodo_pagamento' => 'required',
            'itens' => 'required|array|min:1',
            'itens.*.descricao' => 'required',
            'itens.*.quantidade' => 'required|numeric|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Calcula o valor total
            $valorTotal = collect($request->itens)->sum(function($item) {
                return $item['quantidade'] * $item['preco_unitario'];
            });
            
            $desconto = $request->desconto ?? 0;
            $valorFinal = $valorTotal - $desconto;
            
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
                'user_id' => auth()->id(),
            ]);
            
            // Cria os itens da venda
            foreach ($request->itens as $item) {
                VendaItem::create([
                    'venda_id' => $venda->id,
                    'descricao_item' => $item['descricao'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'valor_total_item' => $item['quantidade'] * $item['preco_unitario'],
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('financeiro.vendas.show', $venda->id)
                ->with('success', 'Venda registrada com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $venda = Venda::with('vendaItems')->findOrFail($id);
        return view('financeiro.vendas.show', compact('venda'));
    }

    // ... (métodos edit, update, destroy)
}