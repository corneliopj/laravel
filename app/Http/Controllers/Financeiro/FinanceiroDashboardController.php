<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Receita; // Ajustado: Modelos na raiz App\Models
use App\Models\Despesa; // Ajustado: Modelos na raiz App\Models
use App\Models\Categoria; // Ajustado: Modelos na raiz App\Models
use App\Models\Venda; // Ajustado: Modelos na raiz App\Models
use App\Models\Reserva; // Ajustado: Modelos na raiz App\Models
use App\Models\User; // Necessário para comissão (se aplicável, com base na sua lógica real)
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Para obter o usuário logado

class FinanceiroDashboardController extends Controller
{
    /**
     * Exibe o dashboard financeiro principal.
     */
    public function index(Request $request)
    {
        // Define o ano e mês padrão para os filtros
        $ano = $request->input('ano', Carbon::now()->year);
        $mes = $request->input('mes', Carbon::now()->month);

        // Dados de resumo mensal para o dashboard
        // CORRIGIDO: Usando 'data' ao invés de 'data_receita'
        $receitasMes = Receita::whereYear('data', $ano)
                                ->whereMonth('data', $mes)
                                ->sum('valor');

        // CORRIGIDO: Usando 'data' ao invés de 'data_despesa'
        $despesasMes = Despesa::whereYear('data', $ano)
                                ->whereMonth('data', $mes)
                                ->sum('valor');

        $saldoMes = $receitasMes - $despesasMes;

        // Saldo total acumulado (geral)
        // CORRIGIDO: Usando 'data'
        $saldoTotal = Receita::sum('valor') - Despesa::sum('valor');

        // Comissões do usuário logado para o mês/ano selecionado
        // CORRIGIDO: A lógica de comissão precisa ser revisada, pois 'responsavel_id' não existe.
        // Por enquanto, vamos manter um valor padrão de 0 para evitar erros,
        // ou você pode integrar a lógica de comissão vinda diretamente das Vendas,
        // se a comissão for gerada no ato da venda e registrada lá.
        // Se a comissão é uma despesa, mas não tem 'responsavel_id', precisa de outro campo para vincular ao usuário.
        // Ex: Se o 'User' que está logado pode ser um 'Vendedor' e a comissão é uma 'Despesa'
        // associada a uma venda feita por ele (e não diretamente na despesa em si).
        $comissaoAcumuladaMes = 0; // Valor padrão para evitar erro.
        // PARA IMPLEMENTAR: Se a comissão é uma despesa, mas sem 'responsavel_id',
        // como ela é vinculada ao usuário? Pode ser por um campo 'vendedor_id' na despesa,
        // ou a comissão é uma 'Receita Negativa' ou um campo calculado nas vendas.
        // Para a sua estrutura atual, se não há 'responsavel_id' na Despesa,
        // a lógica abaixo não funcionará.
        /*
        if (Auth::check()) {
            $user = Auth::user();
            $categoriaComissao = Categoria::where('nome', 'Comissões')
                                          ->where('tipo', 'despesa')
                                          ->first();

            if ($categoriaComissao) {
                // ESTA PARTE CAUSARIA ERRO DEVIDO A 'responsavel_id'
                // $comissaoAcumuladaMes = Despesa::where('responsavel_id', $user->id)
                //                                 ->where('categoria_id', $categoriaComissao->id)
                //                                 ->whereYear('data', $ano)
                //                                 ->whereMonth('data', $mes)
                //                                 ->sum('valor');
            }
        }
        */
        // Se a comissão é registrada em 'Despesa' mas sem 'responsavel_id',
        // você precisará de uma forma diferente de associá-la ao usuário.
        // Se o Contracheque já gerencia isso, pode ser que a comissão
        // seja calculada e acessada via o ContrachequeController.

        // Dados para Gráficos
        $dadosGraficoBarras = $this->getDadosGraficoBarras($ano);
        $dadosGraficoPizza = $this->getDadosGraficoPizza($mes, $ano);
        $dadosGraficoLinha = $this->getDadosGraficoLinha($ano);

        return view('financeiro.dashboard', compact(
            'receitasMes', 'despesasMes', 'saldoMes', 'saldoTotal',
            'comissaoAcumuladaMes', 'ano', 'mes',
            'dadosGraficoBarras', 'dadosGraficoPizza', 'dadosGraficoLinha'
        ));
    }

    /**
     * Prepara os dados para o gráfico de barras (Receitas vs. Despesas por Mês).
     * @param int $ano
     * @return array
     */
    private function getDadosGraficoBarras(int $ano): array
    {
        $receitasPorMes = Receita::select(
                DB::raw('MONTH(data) as mes'), // CORRIGIDO: Usando 'data'
                DB::raw('SUM(valor) as total_receita')
            )
            ->whereYear('data', $ano) // CORRIGIDO: Usando 'data'
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_receita', 'mes')
            ->toArray();

        $despesasPorMes = Despesa::select(
                DB::raw('MONTH(data) as mes'), // CORRIGIDO: Usando 'data'
                DB::raw('SUM(valor) as total_despesa')
            )
            ->whereYear('data', $ano) // CORRIGIDO: Usando 'data'
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_despesa', 'mes')
            ->toArray();

        $meses = [];
        $receitas = [];
        $despesas = [];

        for ($i = 1; $i <= 12; $i++) {
            $mesNome = Carbon::create(null, $i, 1)->monthName;
            $meses[] = ucfirst($mesNome); // Capitaliza o nome do mês

            $receitas[] = $receitasPorMes[$i] ?? 0;
            $despesas[] = $despesasPorMes[$i] ?? 0;
        }

        return [
            'labels' => $meses,
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'data' => $receitas,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Despesas',
                    'data' => $despesas,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepara os dados para o gráfico de pizza (Distribuição de Despesas por Categoria).
     * @param int $mes
     * @param int $ano
     * @return array
     */
    private function getDadosGraficoPizza(int $mes, int $ano): array
    {
        $despesasPorCategoria = Despesa::select(
                'categorias.nome as categoria_nome',
                DB::raw('SUM(despesas.valor) as total_gasto')
            )
            ->join('categorias', 'despesas.categoria_id', '=', 'categorias.id')
            ->whereYear('despesas.data', $ano) // CORRIGIDO: Usando 'data'
            ->whereMonth('despesas.data', $mes) // CORRIGIDO: Usando 'data'
            ->groupBy('categorias.nome')
            ->orderByDesc('total_gasto')
            ->get();

        $labels = $despesasPorCategoria->pluck('categoria_nome')->toArray();
        $data = $despesasPorCategoria->pluck('total_gasto')->toArray();
        $backgroundColors = [];
        foreach ($labels as $key => $label) {
            $backgroundColors[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.6)';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => '#fff',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Prepara os dados para o gráfico de linha (Evolução do Saldo Acumulado).
     * @param int $ano
     * @return array
     */
    private function getDadosGraficoLinha(int $ano): array
    {
        $saldoAcumulado = [];
        // CORRIGIDO: Usando 'data'
        $saldoAtual = $this->getSaldoAcumuladoAteDezembroDoAnoAnterior($ano); // Saldo até o final do ano anterior

        for ($mes = 1; $mes <= 12; $mes++) {
            // CORRIGIDO: Usando 'data'
            $receitasMes = Receita::whereYear('data', $ano)
                                ->whereMonth('data', $mes)
                                ->sum('valor');

            // CORRIGIDO: Usando 'data'
            $despesasMes = Despesa::whereYear('data', $ano)
                                ->whereMonth('data', $mes)
                                ->sum('valor');

            $saldoDoMes = $receitasMes - $despesasMes;
            $saldoAtual += $saldoDoMes;
            $saldoAcumulado[] = $saldoAtual;
        }

        $meses = [];
        for ($i = 1; $i <= 12; $i++) {
            $mesNome = Carbon::create(null, $i, 1)->monthName;
            $meses[] = ucfirst($mesNome);
        }

        return [
            'labels' => $meses,
            'datasets' => [
                [
                    'label' => 'Saldo Acumulado',
                    'data' => $saldoAcumulado,
                    'fill' => false,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'tension' => 0.1,
                    'pointRadius' => 5,
                    'pointBackgroundColor' => 'rgba(54, 162, 235, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverRadius' => 7,
                    'pointHoverBackgroundColor' => 'rgba(255, 206, 86, 1)',
                    'pointHoverBorderColor' => 'rgba(255, 206, 86, 1)',
                ]
            ]
        ];
    }

    /**
     * Retorna o saldo acumulado até dezembro do ano anterior.
     * @param int $ano
     * @return float
     */
    private function getSaldoAcumuladoAteDezembroDoAnoAnterior(int $ano): float
    {
        $anoAnterior = $ano - 1;
        // CORRIGIDO: Usando 'data'
        $receitasAnoAnterior = Receita::whereYear('data', '<=', $anoAnterior)->sum('valor');
        // CORRIGIDO: Usando 'data'
        $despesasAnoAnterior = Despesa::whereYear('data', '<=', $anoAnterior)->sum('valor');

        return $receitasAnoAnterior - $despesasAnoAnterior;
    }

    /**
     * Exibe a página de índice para os relatórios financeiros.
     */
    public function relatoriosIndex()
    {
        return view('financeiro.relatorios.index');
    }

    /**
     * Exibe um relatório de todas as transações (Receitas e Despesas).
     */
    public function transacoes(Request $request)
    {
        $queryReceitas = Receita::query();
        $queryDespesas = Despesa::query();

        // Filtros de data
        if ($request->filled('data_inicio')) {
            $queryReceitas->where('data', '>=', Carbon::parse($request->data_inicio)->startOfDay()); // CORRIGIDO
            $queryDespesas->where('data', '>=', Carbon::parse($request->data_inicio)->startOfDay()); // CORRIGIDO
        }
        if ($request->filled('data_fim')) {
            $queryReceitas->where('data', '<=', Carbon::parse($request->data_fim)->endOfDay()); // CORRIGIDO
            $queryDespesas->where('data', '<=', Carbon::parse($request->data_fim)->endOfDay()); // CORRIGIDO
        }
        // Filtro por categoria para Receitas
        if ($request->filled('categoria_receita_id') && $request->categoria_receita_id != 'all') {
            $queryReceitas->where('categoria_id', $request->categoria_receita_id);
        }
        // Filtro por categoria para Despesas
        if ($request->filled('categoria_despesa_id') && $request->categoria_despesa_id != 'all') {
            $queryDespesas->where('categoria_id', $request->categoria_despesa_id);
        }


        // Recupera receitas e despesas
        $receitas = $queryReceitas->with('categoria')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'tipo' => 'Receita',
                'descricao' => $item->descricao,
                'valor' => $item->valor,
                'data' => $item->data, // CORRIGIDO
                'categoria' => $item->categoria->nome ?? 'N/A',
            ];
        });

        $despesas = $queryDespesas->with('categoria')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'tipo' => 'Despesa',
                'descricao' => $item->descricao,
                'valor' => $item->valor,
                'data' => $item->data, // CORRIGIDO
                'categoria' => $item->categoria->nome ?? 'N/A',
            ];
        });

        // Combina e ordena todas as transações por data
        $transacoes = $receitas->merge($despesas)->sortByDesc('data');

        // Calcula totais
        $totalReceitas = $receitas->sum('valor');
        $totalDespesas = $despesas->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        // Categorias para o filtro
        $categorias = Categoria::all();

        return view('financeiro.relatorios.transacoes', compact('transacoes', 'totalReceitas', 'totalDespesas', 'saldo', 'request', 'categorias'));
    }

    /**
     * Exibe um relatório de fluxo de caixa.
     */
    public function fluxoCaixa(Request $request)
    {
        // Define o ano e mês padrão para os filtros
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->toDateString());
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->toDateString());

        $dataInicio = Carbon::parse($dataInicio)->startOfDay();
        $dataFim = Carbon::parse($dataFim)->endOfDay();

        $fluxoCaixa = [];
        $saldoAcumulado = 0; // Você pode buscar o saldo inicial se necessário

        // Loop pelos dias no período selecionado
        for ($data = clone $dataInicio; $data->lte($dataFim); $data->addDay()) {
            // CORRIGIDO: Usando 'data'
            $receitasDoDia = Receita::whereDate('data', $data)->sum('valor');
            // CORRIGIDO: Usando 'data'
            $despesasDoDia = Despesa::whereDate('data', $data)->sum('valor');
            $saldoDoDia = $receitasDoDia - $despesasDoDia;
            $saldoAcumulado += $saldoDoDia;

            $fluxoCaixa[] = [
                'data' => $data->format('Y-m-d'),
                'receitas' => $receitasDoDia,
                'despesas' => $despesasDoDia,
                'saldo_dia' => $saldoDoDia,
                'saldo_acumulado' => $saldoAcumulado,
            ];
        }

        // Dados para o gráfico de fluxo de caixa (Combo Bar/Line)
        $labels = array_column($fluxoCaixa, 'data');
        $receitasData = array_column($fluxoCaixa, 'receitas');
        $despesasData = array_column($fluxoCaixa, 'despesas');
        $saldoAcumuladoData = array_column($fluxoCaixa, 'saldo_acumulado');

        $dadosGraficoFluxoCaixa = [
            'labels' => $labels,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Receitas Diárias',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'data' => $receitasData,
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'bar',
                    'label' => 'Despesas Diárias',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'data' => $despesasData,
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'line',
                    'label' => 'Saldo Acumulado',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 2,
                    'fill' => false,
                    'data' => $saldoAcumuladoData,
                    'yAxisID' => 'y1',
                    'pointRadius' => 5,
                    'pointBackgroundColor' => 'rgb(54, 162, 235)',
                ]
            ]
        ];


        return view('financeiro.relatorios.fluxo_caixa', compact('fluxoCaixa', 'dataInicio', 'dataFim', 'request', 'dadosGraficoFluxoCaixa'));
    }

    /**
     * Exibe um relatório de transações agrupadas por categoria.
     */
    public function relatorioPorCategoria(Request $request)
    {
        $tipo = $request->input('tipo', 'ambos');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        $queryCategorias = Categoria::query();

        if ($tipo == 'receita') {
            $queryCategorias->where('tipo', 'receita');
        } elseif ($tipo == 'despesa') {
            $queryCategorias->where('tipo', 'despesa');
        }

        $categorias = $queryCategorias->get();
        $dadosPorCategoria = [];

        foreach ($categorias as $categoria) {
            $totalValor = 0;

            if ($categoria->tipo == 'receita' || $tipo == 'ambos' || $tipo == 'receita') {
                $receitasQuery = Receita::where('categoria_id', $categoria->id);
                if ($dataInicio) $receitasQuery->where('data', '>=', Carbon::parse($dataInicio)->startOfDay()); // CORRIGIDO
                if ($dataFim) $receitasQuery->where('data', '<=', Carbon::parse($dataFim)->endOfDay()); // CORRIGIDO
                if ($tipo == 'receita' || $tipo == 'ambos') {
                     $totalValor += $receitasQuery->sum('valor');
                }
            }

            if ($categoria->tipo == 'despesa' || $tipo == 'ambos' || $tipo == 'despesa') {
                $despesasQuery = Despesa::where('categoria_id', $categoria->id);
                if ($dataInicio) $despesasQuery->where('data', '>=', Carbon::parse($dataInicio)->startOfDay()); // CORRIGIDO
                if ($dataFim) $despesasQuery->where('data', '<=', Carbon::parse($dataFim)->endOfDay()); // CORRIGIDO
                if ($tipo == 'despesa' || $tipo == 'ambos') {
                    $totalValor -= $despesasQuery->sum('valor');
                }
            }

            if ($totalValor != 0) {
                $dadosPorCategoria[] = [
                    'categoria' => $categoria->nome,
                    'tipo' => $categoria->tipo,
                    'total_valor' => $totalValor,
                ];
            }
        }

        $labels = array_column($dadosPorCategoria, 'categoria');
        $data = array_map('abs', array_column($dadosPorCategoria, 'total_valor'));
        $backgroundColors = [];
        foreach ($labels as $key => $label) {
            $backgroundColors[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.6)';
        }

        $dadosGraficoPorCategoria = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => '#fff',
                    'borderWidth' => 1
                ]
            ]
        ];

        return view('financeiro.relatorios.por_categoria', compact('dadosPorCategoria', 'tipo', 'dataInicio', 'dataFim', 'request', 'dadosGraficoPorCategoria', 'categorias'));
    }

    /**
     * Obter dados comparativos de períodos
     */
    private function getDadosComparativo(int $ano, int $mes): array
    {
        // Período atual
        $receitasAtual = Receita::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        $despesasAtual = Despesa::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        $saldoAtual = $receitasAtual - $despesasAtual;
        
        // Mês anterior
        $dataAnterior = Carbon::create($ano, $mes, 1)->subMonth();
        $receitasAnterior = Receita::whereYear('data', $dataAnterior->year)
            ->whereMonth('data', $dataAnterior->month)
            ->sum('valor');
        
        $despesasAnterior = Despesa::whereYear('data', $dataAnterior->year)
            ->whereMonth('data', $dataAnterior->month)
            ->sum('valor');
        
        $saldoAnterior = $receitasAnterior - $despesasAnterior;
        
        // Mesmo mês do ano anterior
        $anoAnterior = $ano - 1;
        $receitasAnoAnterior = Receita::whereYear('data', $anoAnterior)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        $despesasAnoAnterior = Despesa::whereYear('data', $anoAnterior)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        $saldoAnoAnterior = $receitasAnoAnterior - $despesasAnoAnterior;
        
        // Calcular variações
        $variacaoReceitasMes = $this->calcularVariacao($receitasAtual, $receitasAnterior);
        $variacaoDespesasMes = $this->calcularVariacao($despesasAtual, $despesasAnterior);
        $variacaoSaldoMes = $this->calcularVariacao($saldoAtual, $saldoAnterior);
        
        $variacaoReceitasAno = $this->calcularVariacao($receitasAtual, $receitasAnoAnterior);
        $variacaoDespesasAno = $this->calcularVariacao($despesasAtual, $despesasAnoAnterior);
        $variacaoSaldoAno = $this->calcularVariacao($saldoAtual, $saldoAnoAnterior);
        
        return [
            'periodo_atual' => [
                'label' => Carbon::create($ano, $mes, 1)->format('M/Y'),
                'receitas' => $receitasAtual,
                'despesas' => $despesasAtual,
                'saldo' => $saldoAtual
            ],
            'periodo_anterior' => [
                'label' => $dataAnterior->format('M/Y'),
                'receitas' => $receitasAnterior,
                'despesas' => $despesasAnterior,
                'saldo' => $saldoAnterior
            ],
            'ano_anterior' => [
                'label' => Carbon::create($anoAnterior, $mes, 1)->format('M/Y'),
                'receitas' => $receitasAnoAnterior,
                'despesas' => $despesasAnoAnterior,
                'saldo' => $saldoAnoAnterior
            ],
            'variacoes_mes_anterior' => [
                'receitas' => $variacaoReceitasMes,
                'despesas' => $variacaoDespesasMes,
                'saldo' => $variacaoSaldoMes
            ],
            'variacoes_ano_anterior' => [
                'receitas' => $variacaoReceitasAno,
                'despesas' => $variacaoDespesasAno,
                'saldo' => $variacaoSaldoAno
            ]
        ];
    }

    /**
     * Calcular variação percentual
     */
    private function calcularVariacao(float $atual, float $anterior): array
    {
        if ($anterior == 0) {
            $percentual = $atual > 0 ? 100 : 0;
            $tipo = $atual >= 0 ? 'positiva' : 'negativa';
        } else {
            $percentual = abs((($atual - $anterior) / $anterior) * 100);
            $tipo = $atual >= $anterior ? 'positiva' : 'negativa';
        }
        
        return [
            'percentual' => round($percentual, 1),
            'tipo' => $tipo,
            'valor_absoluto' => $atual - $anterior
        ];
    }

    /**
     * Obter top 5 despesas do mês
     */
    private function getTop5Despesas(int $ano, int $mes): array
    {
        $despesas = Despesa::select('descricao', 'categoria_id')
            ->selectRaw('SUM(valor) as total_valor')
            ->selectRaw('COUNT(*) as quantidade_transacoes')
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->with('categoria')
            ->groupBy('descricao', 'categoria_id')
            ->orderByDesc('total_valor')
            ->limit(5)
            ->get();
        
        $totalPeriodo = Despesa::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        $despesasFormatadas = $despesas->map(function($despesa) use ($totalPeriodo) {
            $percentual = $totalPeriodo > 0 ? ($despesa->total_valor / $totalPeriodo) * 100 : 0;
            
            return [
                'descricao' => $despesa->descricao,
                'categoria' => $despesa->categoria->nome ?? 'N/A',
                'valor' => $despesa->total_valor,
                'quantidade' => $despesa->quantidade_transacoes,
                'percentual' => round($percentual, 1)
            ];
        });
        
        return [
            'despesas' => $despesasFormatadas->toArray(),
            'total_periodo' => $totalPeriodo
        ];
    }

    /**
     * Obter top 5 receitas do mês
     */
    private function getTop5Receitas(int $ano, int $mes): array
    {
        $receitas = Receita::select('descricao', 'categoria_id')
            ->selectRaw('SUM(valor) as total_valor')
            ->selectRaw('COUNT(*) as quantidade_transacoes')
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->with('categoria')
            ->groupBy('descricao', 'categoria_id')
            ->orderByDesc('total_valor')
            ->limit(5)
            ->get();
        
        $totalPeriodo = Receita::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        $receitasFormatadas = $receitas->map(function($receita) use ($totalPeriodo) {
            $percentual = $totalPeriodo > 0 ? ($receita->total_valor / $totalPeriodo) * 100 : 0;
            
            return [
                'descricao' => $receita->descricao,
                'categoria' => $receita->categoria->nome ?? 'N/A',
                'valor' => $receita->total_valor,
                'quantidade' => $receita->quantidade_transacoes,
                'percentual' => round($percentual, 1)
            ];
        });
        
        return [
            'receitas' => $receitasFormatadas->toArray(),
            'total_periodo' => $totalPeriodo
        ];
    }
}