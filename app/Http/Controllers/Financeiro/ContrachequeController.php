<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Contracheque;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContrachequeController extends Controller
{
    /**
     * Exibe o contracheque do usuário logado para o mês e ano especificados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $mes = $request->input('mes', Carbon::now()->month);
        $ano = $request->input('ano', Carbon::now()->year);
        $userId = Auth::id();

        // Prepara o nome do mês para o título do card na view.
        // Usar getTranslatedMonthName() é mais robusto para localização.
        $nomeMes = Carbon::create(null, $mes, 1)->locale('pt_BR')->getTranslatedMonthName();

        // Prepara a lista de meses para o dropdown do filtro na view.
        $mesesDoAno = [];
        for ($i = 1; $i <= 12; $i++) {
            $mesesDoAno[] = [
                'numero' => $i,
                'nome' => Carbon::create(null, $i, 1)->locale('pt_BR')->getTranslatedMonthName()
            ];
        }
        // Prepara a lista de anos para o dropdown do filtro na view.
        $anoAtual = Carbon::now()->year;
        $anosDisponiveis = range($anoAtual - 5, $anoAtual + 1);

        // 1. Buscar Vendas com Comissões do Mês
        $dataInicioMes = Carbon::createFromDate($ano, $mes, 1)->startOfDay();
        $dataFimMes = Carbon::createFromDate($ano, $mes)->endOfMonth()->endOfDay();

        // Removido 'comprador' do método with(), pois não é um relacionamento.
        $vendasComComissao = Venda::where('user_id', $userId)
            ->where('comissao_paga', true)
            ->whereBetween('data_venda', [$dataInicioMes, $dataFimMes])
            ->with('despesaComissao', 'vendaItems')
            ->get();
        
        // 2. Buscar TODOS os Lançamentos de Contracheque para o usuário e mês/ano
        $contrachequeLancamentos = Contracheque::where('user_id', $userId)
            ->whereMonth('data', $mes)
            ->whereYear('data', $ano)
            ->get();

        // 3. Inicializa o sumário do contracheque
        $contrachequeSumario = [
            'salario' => 0,
            'comissoes' => 0,
            'adiantamento' => 0,
            'cartao_credito' => 0,
            'outros_positivos' => 0,
            'outros_negativos' => 0,
            'valor_bruto' => 0,
            'descontos' => 0,
            'saldo_liquido' => 0,
            'lancamentos_detalhados' => []
        ];

        // 4. Adiciona os lançamentos de comissão primeiro
        foreach ($vendasComComissao as $venda) {
            if ($venda->despesaComissao) {
                $valorComissao = $venda->despesaComissao->valor;
                $contrachequeSumario['comissoes'] += $valorComissao;

                // --- LINHA CORRIGIDA AQUI ---
                // Agora acessa o campo 'comprador' diretamente, sem o relacionamento
                $nomeComprador = $venda->comprador ?? 'Comprador Desconhecido';
                $descricao = 'Comissão da Venda ' . $venda->id . ' / ' . $nomeComprador;
                
                $contrachequeSumario['lancamentos_detalhados'][] = [
                    'data' => $venda->data_venda->format('d/m/Y'),
                    'descricao' => $descricao,
                    'valor' => $valorComissao,
                    'tipo_lancamento' => 'positivo',
                    'venda_id' => $venda->id, // ID para identificar a venda e criar o popover
                    // Detalhes da venda para o popover
                    'venda_detalhes' => [
                        'valor_final' => $venda->valor_final,
                        'observacoes' => $venda->observacoes,
                        'itens' => $venda->vendaItems->map(function ($item) {
                            return [
                                'descricao' => $item->descricao_item,
                                'quantidade' => $item->quantidade,
                                'preco_unitario' => $item->preco_unitario,
                                'valor_total_item' => $item->valor_total_item,
                            ];
                        })
                    ]
                ];
            }
        }

        // 5. Processa cada lançamento do modelo Contracheque para calcular os totais e detalhar
        foreach ($contrachequeLancamentos as $lancamento) {
            $lancamentoDetalhado = [
                'id' => $lancamento->id,
                'descricao' => $lancamento->descricao,
                'valor' => $lancamento->valor,
                'tipo_lancamento' => $lancamento->tipo_lancamento,
                'data' => $lancamento->data->format('d/m/Y')
            ];

            if ($lancamento->tipo_lancamento == 'positivo') {
                if (mb_strtolower($lancamento->descricao) == 'salário' || mb_strtolower($lancamento->descricao) == 'salario') {
                    $contrachequeSumario['salario'] += $lancamento->valor;
                } else {
                    $contrachequeSumario['outros_positivos'] += $lancamento->valor;
                }
            } else { // 'negativo'
                $contrachequeSumario['descontos'] += $lancamento->valor;
                if (mb_strtolower($lancamento->descricao) == 'adiantamento') {
                    $contrachequeSumario['adiantamento'] += $lancamento->valor;
                } elseif (mb_strtolower($lancamento->descricao) == 'cartão de crédito' || mb_strtolower($lancamento->descricao) == 'cartao de credito') {
                    $contrachequeSumario['cartao_credito'] += $lancamento->valor;
                } else {
                    $contrachequeSumario['outros_negativos'] += $lancamento->valor;
                }
            }
            $contrachequeSumario['lancamentos_detalhados'][] = $lancamentoDetalhado;
        }

        // 6. Calcular Valor Bruto, Descontos e Saldo Líquido Finais
        $contrachequeSumario['valor_bruto'] = $contrachequeSumario['salario'] + $contrachequeSumario['comissoes'] + $contrachequeSumario['outros_positivos'];
        $contrachequeSumario['saldo_liquido'] = $contrachequeSumario['valor_bruto'] - $contrachequeSumario['descontos'];


        return view('financeiro.contracheque.index', compact(
            'contrachequeSumario',
            'mes',
            'ano'
        ));
    }

    /**
     * Armazena um novo lançamento de contracheque.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'tipo_lancamento' => 'required|in:positivo,negativo',
            'data' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($request->user_id != Auth::id()) {
            return redirect()->back()->with('error', 'Você não tem permissão para adicionar lançamentos para outro usuário.');
        }

        Contracheque::create([
            'user_id' => $request->user_id,
            'data' => $request->data,
            'tipo_lancamento' => $request->tipo_lancamento,
            'valor' => $request->valor,
            'descricao' => $request->descricao,
        ]);

        return redirect()->route('financeiro.contracheque.index', [
            'mes' => Carbon::parse($request->data)->month,
            'ano' => Carbon::parse($request->data)->year
        ])->with('success', 'Lançamento de contracheque adicionado com sucesso!');
    }

    /**
     * Remove o lançamento de contracheque especificado do armazenamento.
     *
     * @param  \App\Models\Financeiro\Contracheque  $contracheque
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Contracheque $contracheque)
    {
        if ($contracheque->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Você não tem permissão para excluir este lançamento.');
        }

        $contracheque->delete();

        return redirect()->back()->with('success', 'Lançamento de contracheque excluído com sucesso!');
    }
}