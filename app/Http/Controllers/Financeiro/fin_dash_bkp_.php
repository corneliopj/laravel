<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceiroDashboardController extends Controller
{
    /**
     * Exibe o dashboard financeiro com relatórios e gráficos.
     */
    public function index(Request $request)
    {
        // Define o período padrão (ex: mês atual)
        $mes = $request->input('mes', Carbon::now()->month);
        $ano = $request->input('ano', Carbon::now()->year);

        // Calcula o saldo total
        $saldoTotal = Receita::sum('valor') - Despesa::sum('valor');

        // Resumo mensal de receitas e despesas
        $receitasMes = Receita::whereMonth('data', $mes)
                               ->whereYear('data', $ano)
                               ->sum('valor');
        $despesasMes = Despesa::whereMonth('data', $mes)
                               ->whereYear('data', $ano)
                               ->sum('valor');
        $saldoMes = $receitasMes - $despesasMes;

        // Dados para o gráfico de barras (Receitas vs. Despesas por Mês)
        $dadosGraficoBarras = $this->getDadosGraficoBarras($ano);

        // Dados para o gráfico de pizza (Distribuição de Despesas por Categoria)
        $dadosGraficoPizza = $this->getDadosGraficoPizza($mes, $ano);

        // Dados para o gráfico de linha (Evolução do Saldo ao Longo do Tempo)
        $dadosGraficoLinha = $this->getDadosGraficoLinha();

        // Categorias para filtros, se necessário
        $categorias = Categoria::all();

        return view('financeiro.dashboard', compact(
            'saldoTotal',
            'receitasMes',
            'despesasMes',
            'saldoMes',
            'dadosGraficoBarras',
            'dadosGraficoPizza',
            'dadosGraficoLinha',
            'categorias',
            'mes',
            'ano'
        ));
    }

    /**
     * Prepara os dados para o gráfico de barras (Receitas vs. Despesas por Mês).
     * @param int $ano
     * @return array
     */
    private function getDadosGraficoBarras(int $ano)
    {
        $meses = [];
        $receitasPorMes = [];
        $despesasPorMes = [];

        for ($i = 1; $i <= 12; $i++) {
            $mesNome = Carbon::create()->month($i)->translatedFormat('M'); // Ex: Jan, Fev
            $meses[] = $mesNome;

            $receitas = Receita::whereYear('data', $ano)
                               ->whereMonth('data', $i)
                               ->sum('valor');
            $despesas = Despesa::whereYear('data', $ano)
                               ->whereMonth('data', $i)
                               ->sum('valor');

            $receitasPorMes[] = $receitas;
            $despesasPorMes[] = $despesas;
        }

        return [
            'labels' => $meses,
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'data' => $receitasPorMes,
                ],
                [
                    'label' => 'Despesas',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => $despesasPorMes,
                ],
            ],
        ];
    }

    /**
     * Prepara os dados para o gráfico de pizza (Distribuição de Despesas por Categoria).
     * @param int $mes
     * @param int $ano
     * @return array
     */
    private function getDadosGraficoPizza(int $mes, int $ano)
    {
        $despesasPorCategoria = Despesa::whereMonth('data', $mes)
                                        ->whereYear('data', $ano)
                                        ->selectRaw('categoria_id, SUM(valor) as total')
                                        ->groupBy('categoria_id')
                                        ->with('categoria')
                                        ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];

        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9900',
            '#C9CBCF', '#8AC926', '#1982C4', '#6A4C93', '#FFCA3A', '#6A8EAE'
        ];
        $colorIndex = 0;

        foreach ($despesasPorCategoria as $despesa) {
            $labels[] = $despesa->categoria->nome ?? 'Sem Categoria';
            $data[] = $despesa->total;
            $backgroundColors[] = $colors[$colorIndex % count($colors)];
            $colorIndex++;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'hoverOffset' => 4
                ]
            ]
        ];
    }

    /**
     * Prepara os dados para o gráfico de linha (Evolução do Saldo ao Longo do Tempo).
     * @return array
     */
    private function getDadosGraficoLinha()
    {
        $periodo = 12; // Últimos 12 meses
        $datas = [];
        $saldos = [];
        $saldoAcumulado = 0;

        // Começa do mês atual e vai para trás
        for ($i = $periodo - 1; $i >= 0; $i--) {
            $dataReferencia = Carbon::now()->subMonths($i);
            $mesAno = $dataReferencia->translatedFormat('M/Y');
            $datas[] = $mesAno;

            $receitasMes = Receita::whereMonth('data', $dataReferencia->month)
                                   ->whereYear('data', $dataReferencia->year)
                                   ->sum('valor');
            $despesasMes = Despesa::whereMonth('data', $dataReferencia->month)
                                   ->whereYear('data', $dataReferencia->year)
                                   ->sum('valor');

            $saldoAcumulado += ($receitasMes - $despesasMes);
            $saldos[] = $saldoAcumulado;
        }

        return [
            'labels' => $datas,
            'datasets' => [
                [
                    'label' => 'Saldo Acumulado',
                    'data' => $saldos,
                    'fill' => false,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1
                ]
            ]
        ];
    }

    /**
     * Exibe a página principal de relatórios financeiros.
     * Este método foi adicionado para a rota 'financeiro.relatorios.index'
     */
    public function relatoriosIndex()
    {
        return view('financeiro.relatorios.index');
    }

    /**
     * Exibe o relatório detalhado de transações (receitas e despesas).
     */
    public function transacoes(Request $request)
    {
        // Busca todas as categorias para o filtro
        $categorias = Categoria::orderBy('nome')->get();

        // Inicializa as coleções de receitas e despesas
        $receitas = collect();
        $despesas = collect();

        // Aplica filtros se existirem
        $queryReceitas = Receita::with('categoria');
        $queryDespesas = Despesa::with('categoria');

        // Filtro por tipo de transação
        if ($request->filled('tipo_transacao')) {
            if ($request->input('tipo_transacao') == 'receita') {
                $despesas = collect(); // Se for só receita, não busca despesa
            } elseif ($request->input('tipo_transacao') == 'despesa') {
                $receitas = collect(); // Se for só despesa, não busca receita
            }
        }

        // Filtro por categoria
        if ($request->filled('categoria_id')) {
            $categoriaId = $request->input('categoria_id');
            // Verifica se a categoria existe e qual é o seu tipo
            $categoria = Categoria::find($categoriaId);

            if ($categoria) {
                if ($categoria->tipo == 'receita') {
                    $queryReceitas->where('categoria_id', $categoriaId);
                    $despesas = collect(); // Se a categoria é de receita, não busca despesas
                } elseif ($categoria->tipo == 'despesa') {
                    $queryDespesas->where('categoria_id', $categoriaId);
                    $receitas = collect(); // Se a categoria é de despesa, não busca receitas
                }
            } else {
                // Se a categoria_id não existir, não aplica filtro ou mostra erro
                // Por simplicidade, não aplicamos filtro se a categoria for inválida
            }
        }

        // Filtro por data de início
        if ($request->filled('data_inicio')) {
            $queryReceitas->where('data', '>=', $request->input('data_inicio'));
            $queryDespesas->where('data', '>=', $request->input('data_inicio'));
        }

        // Filtro por data de fim
        if ($request->filled('data_fim')) {
            $queryReceitas->where('data', '<=', $request->input('data_fim'));
            $queryDespesas->where('data', '<=', $request->input('data_fim'));
        }

        // Executa as consultas se não foram zeradas pelos filtros de tipo/categoria
        if ($receitas->isEmpty() && $request->input('tipo_transacao') !== 'despesa' &&
            (!isset($categoria) || $categoria->tipo !== 'despesa')) { // Ajuste aqui para verificar se $categoria está definida
            $receitas = $queryReceitas->get();
        }

        if ($despesas->isEmpty() && $request->input('tipo_transacao') !== 'receita' &&
            (!isset($categoria) || $categoria->tipo !== 'receita')) { // Ajuste aqui para verificar se $categoria está definida
            $despesas = $queryDespesas->get();
        }

        // Combina as coleções e ordena por data
        $transacoes = $receitas->map(function ($item) {
            $item->type = 'receita';
            return $item;
        })->merge($despesas->map(function ($item) {
            $item->type = 'despesa';
            return $item;
        }))->sortByDesc('data');

        // Paginação manual (se necessário, ou use o paginate do Laravel nas queries)
        $perPage = 10; // Número de itens por página
        $page = $request->get('page', 1); // Página atual, padrão 1
        $offset = ($page * $perPage) - $perPage;

        $transacoesPaginadas = new \Illuminate\Pagination\LengthAwarePaginator(
            $transacoes->slice($offset, $perPage),
            $transacoes->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );


        return view('financeiro.relatorios.transacoes', [
            'transacoes' => $transacoesPaginadas,
            'categorias' => $categorias,
        ]);
    }


    /**
     * Exibe o relatório de fluxo de caixa mensal.
     */
    public function fluxoCaixa()
    {
        $labelsFluxoCaixa = [];
        $dataReceitas = [];
        $dataDespesas = [];
        $dataSaldo = [];
        $fluxoCaixaData = [];

        // Pega os últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $labelsFluxoCaixa[] = $mes->translatedFormat('M/Y'); // Ex: Jan/2023

            // Calcula o total de receitas para o mês
            $totalReceitasMes = Receita::whereYear('data', $mes->year)
                                        ->whereMonth('data', $mes->month)
                                        ->sum('valor');
            $dataReceitas[] = $totalReceitasMes;

            // Calcula o total de despesas para o mês
            $totalDespesasMes = Despesa::whereYear('data', $mes->year)
                                        ->whereMonth('data', $mes->month)
                                        ->sum('valor');
            $dataDespesas[] = $totalDespesasMes;

            // Calcula o saldo mensal
            $saldoMensal = $totalReceitasMes - $totalDespesasMes;
            $dataSaldo[] = $saldoMensal;

            $fluxoCaixaData[] = [
                'mes_ano' => $mes->translatedFormat('F Y'), // Ex: Janeiro 2023
                'total_receitas' => $totalReceitasMes,
                'total_despesas' => $totalDespesasMes,
                'saldo_mensal' => $saldoMensal,
            ];
        }

        // Inverte a ordem para que o mês mais antigo apareça primeiro na tabela
        // (O gráfico já ordena do mais antigo para o mais novo, mas a tabela pode ser útil assim)
        $fluxoCaixaData = array_reverse($fluxoCaixaData);


        return view('financeiro.relatorios.fluxo_caixa', compact(
            'labelsFluxoCaixa',
            'dataReceitas',
            'dataDespesas',
            'dataSaldo',
            'fluxoCaixaData'
        ));
    }

    /**
     * Gera um relatório por categoria.
     */
    public function relatorioPorCategoria(Request $request)
    {
        $tipo = $request->input('tipo', 'receita'); // 'receita' ou 'despesa'
        $dataInicio = Carbon::parse($request->input('data_inicio', Carbon::now()->startOfMonth()));
        $dataFim = Carbon::parse($request->input('data_fim', Carbon::now()->endOfMonth()));

        if ($tipo == 'receita') {
            $dadosPorCategoria = Receita::whereBetween('data', [$dataInicio, $dataFim])
                                        ->selectRaw('categoria_id, SUM(valor) as total')
                                        ->groupBy('categoria_id')
                                        ->with('categoria')
                                        ->get();
        } else { // tipo == 'despesa'
            $dadosPorCategoria = Despesa::whereBetween('data', [$dataInicio, $dataFim])
                                        ->selectRaw('categoria_id, SUM(valor) as total')
                                        ->groupBy('categoria_id')
                                        ->with('categoria')
                                        ->get();
        }

        $totalGeral = $dadosPorCategoria->sum('total');

        return view('financeiro.relatorios.por_categoria', compact(
            'dadosPorCategoria', 'tipo', 'totalGeral', 'dataInicio', 'dataFim'
        ));
    }

    // Métodos para recorrência e anexos serão adicionados em etapas futuras.
    // Métodos para exportação em PDF serão adicionados em etapas futuras.
}

