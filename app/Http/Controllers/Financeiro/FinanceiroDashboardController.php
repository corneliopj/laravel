<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Categoria;
use App\Models\Venda;
use App\Models\Reserva;
use App\Models\User; // Necessário para comissão
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
        $receitasMes = Receita::whereYear('data', $ano)
                                ->whereMonth('data', $mes)
                                ->sum('valor');

        $despesasMes = Despesa::whereYear('data', $ano)
                                ->whereMonth('data', $mes)
                                ->sum('valor');

        $saldoMes = $receitasMes - $despesasMes;

        // Saldo total acumulado (geral)
        $saldoTotal = Receita::sum('valor') - Despesa::sum('valor');

        // Comissões do usuário logado para o mês/ano selecionado
        $comissaoAcumuladaMes = 0;
        if (Auth::check()) {
            $user = Auth::user();
            // Supondo que as comissões são despesas vinculadas ao usuário como 'responsavel_id'
            // E que há uma categoria específica para comissões
            $categoriaComissao = Categoria::where('nome', 'Comissões')
                                          ->where('tipo', 'despesa')
                                          ->first();

            if ($categoriaComissao) {
                $comissaoAcumuladaMes = Despesa::where('responsavel_id', $user->id)
                                                ->where('categoria_id', $categoriaComissao->id)
                                                ->whereYear('data_despesa', $ano)
                                                ->whereMonth('data_despesa', $mes)
                                                ->sum('valor');
            }
        }

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
                DB::raw('MONTH(data_receita) as mes'),
                DB::raw('SUM(valor) as total_receita')
            )
            ->whereYear('data_receita', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_receita', 'mes')
            ->toArray();

        $despesasPorMes = Despesa::select(
                DB::raw('MONTH(data_despesa) as mes'),
                DB::raw('SUM(valor) as total_despesa')
            )
            ->whereYear('data_despesa', $ano)
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
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)', // Cor padrão, será sobrescrita por gradiente no JS
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Despesas',
                    'data' => $despesas,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)', // Cor padrão
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
            ->whereYear('data_despesa', $ano)
            ->whereMonth('data_despesa', $mes)
            ->groupBy('categorias.nome')
            ->orderByDesc('total_gasto')
            ->get();

        $labels = $despesasPorCategoria->pluck('categoria_nome')->toArray();
        $data = $despesasPorCategoria->pluck('total_gasto')->toArray();
        $backgroundColors = [];
        // Gerar cores aleatórias para o Chart.js. Os gradientes serão aplicados no JS.
        foreach ($labels as $key => $label) {
            $backgroundColors[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.6)';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors, // Cores padrão, gradientes no JS
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
        $saldoAtual = $this->getSaldoAcumuladoAteDezembroDoAnoAnterior($ano); // Saldo até o final do ano anterior

        for ($mes = 1; $mes <= 12; $mes++) {
            $receitasMes = Receita::whereYear('data_receita', $ano)
                                ->whereMonth('data_receita', $mes)
                                ->sum('valor');

            $despesasMes = Despesa::whereYear('data_despesa', $ano)
                                ->whereMonth('data_despesa', $mes)
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
                    'fill' => false, // Não preenche a área abaixo da linha
                    'borderColor' => 'rgba(54, 162, 235, 1)', // Cor padrão, gradiente no JS
                    'tension' => 0.1, // Suaviza a linha
                    'pointRadius' => 5, // Tamanho dos pontos
                    'pointBackgroundColor' => 'rgba(54, 162, 235, 1)', // Cor dos pontos
                    'pointBorderColor' => '#fff',
                    'pointHoverRadius' => 7, // Aumenta o ponto ao passar o mouse
                    'pointHoverBackgroundColor' => 'rgba(255, 206, 86, 1)', // Cor do ponto ao passar o mouse
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
        $receitasAnoAnterior = Receita::whereYear('data_receita', '<=', $anoAnterior)->sum('valor');
        $despesasAnoAnterior = Despesa::whereYear('data_despesa', '<=', $anoAnterior)->sum('valor');

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
            $queryReceitas->where('data_receita', '>=', Carbon::parse($request->data_inicio)->startOfDay());
            $queryDespesas->where('data_despesa', '>=', Carbon::parse($request->data_inicio)->startOfDay());
        }
        if ($request->filled('data_fim')) {
            $queryReceitas->where('data_receita', '<=', Carbon::parse($request->data_fim)->endOfDay());
            $queryDespesas->where('data_despesa', '<=', Carbon::parse($request->data_fim)->endOfDay());
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
                'data' => $item->data_receita,
                'categoria' => $item->categoria->nome ?? 'N/A',
            ];
        });

        $despesas = $queryDespesas->with('categoria')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'tipo' => 'Despesa',
                'descricao' => $item->descricao,
                'valor' => $item->valor,
                'data' => $item->data_despesa,
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
            $receitasDoDia = Receita::whereDate('data_receita', $data)->sum('valor');
            $despesasDoDia = Despesa::whereDate('data_despesa', $data)->sum('valor');
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
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)', // Cor padrão para barras
                    'data' => $receitasData,
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'bar',
                    'label' => 'Despesas Diárias',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)', // Cor padrão para barras
                    'data' => $despesasData,
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'line',
                    'label' => 'Saldo Acumulado',
                    'borderColor' => 'rgb(54, 162, 235)', // Cor padrão para linha
                    'borderWidth' => 2,
                    'fill' => false,
                    'data' => $saldoAcumuladoData,
                    'yAxisID' => 'y1', // Eixo Y secundário para o saldo acumulado, se desejar
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
        // Define o tipo de transação a ser filtrada (receita, despesa ou ambos)
        $tipo = $request->input('tipo', 'ambos'); // 'receita', 'despesa', 'ambos'
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
                if ($dataInicio) $receitasQuery->where('data_receita', '>=', Carbon::parse($dataInicio)->startOfDay());
                if ($dataFim) $receitasQuery->where('data_receita', '<=', Carbon::parse($dataFim)->endOfDay());
                if ($tipo == 'receita' || $tipo == 'ambos') {
                     $totalValor += $receitasQuery->sum('valor');
                }
            }

            if ($categoria->tipo == 'despesa' || $tipo == 'ambos' || $tipo == 'despesa') {
                $despesasQuery = Despesa::where('categoria_id', $categoria->id);
                if ($dataInicio) $despesasQuery->where('data_despesa', '>=', Carbon::parse($dataInicio)->startOfDay());
                if ($dataFim) $despesasQuery->where('data_despesa', '<=', Carbon::parse($dataFim)->endOfDay());
                if ($tipo == 'despesa' || $tipo == 'ambos') {
                    $totalValor -= $despesasQuery->sum('valor'); // Despesas são subtraídas
                }
            }

            if ($totalValor != 0) { // Incluir apenas categorias com valor
                $dadosPorCategoria[] = [
                    'categoria' => $categoria->nome,
                    'tipo' => $categoria->tipo,
                    'total_valor' => $totalValor,
                ];
            }
        }

        // Preparar dados para o gráfico (Doughnut ou Polar Area)
        $labels = array_column($dadosPorCategoria, 'categoria');
        $data = array_map('abs', array_column($dadosPorCategoria, 'total_valor')); // Usar valor absoluto para o gráfico
        $backgroundColors = [];
        foreach ($labels as $key => $label) {
            // Gerar cores aleatórias para o Chart.js. Os gradientes serão aplicados no JS.
            $backgroundColors[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.6)';
        }

        $dadosGraficoPorCategoria = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors, // Cores padrão, gradientes no JS
                    'borderColor' => '#fff',
                    'borderWidth' => 1
                ]
            ]
        ];

        return view('financeiro.relatorios.por_categoria', compact('dadosPorCategoria', 'tipo', 'dataInicio', 'dataFim', 'request', 'dadosGraficoPorCategoria', 'categorias'));
    }
}
