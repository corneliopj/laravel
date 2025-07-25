<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Categoria;
use App\Models\Venda; // Certifique-se de importar o modelo Venda
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Importar Auth para pegar o usuário logado

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

        // Definir o início e fim do mês/ano para os cálculos
        $dataInicioMes = Carbon::createFromDate($ano, $mes, 1)->startOfDay();
        $dataFimMes = Carbon::createFromDate($ano, $mes)->endOfMonth()->endOfDay();

        // Calcula o saldo total (geral, não filtrado por mês/ano)
        $saldoTotal = Receita::sum('valor') - Despesa::sum('valor');

        // Resumo mensal de receitas e despesas para o mês/ano selecionado
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

        // Dados para o gráfico de linha (Evolução do Saldo)
        $dadosGraficoLinha = $this->getDadosGraficoLinha($ano);

        // NOVO: Acumulado de Comissões do Usuário Logado no Mês Corrente (ou filtrado)
        $comissaoAcumuladaMes = 0;
        if (Auth::check()) { // Verifica se há um usuário logado
            $userId = Auth::id();
            $comissaoAcumuladaMes = Venda::where('user_id', $userId)
                ->where('comissao_paga', true)
                ->whereBetween('data_venda', [$dataInicioMes, $dataFimMes])
                ->with('despesaComissao') // Carrega a despesa de comissão relacionada
                ->get()
                ->sum(function ($venda) {
                    // Soma o valor da despesa de comissão, se existir
                    return $venda->despesaComissao ? $venda->despesaComissao->valor : 0;
                });
        }


        return view('financeiro.dashboard', compact(
            'saldoTotal',
            'receitasMes',
            'despesasMes',
            'saldoMes',
            'dadosGraficoBarras',
            'dadosGraficoPizza',
            'dadosGraficoLinha',
            'mes',
            'ano',
            'comissaoAcumuladaMes' // Adicionada a nova variável
        ));
    }

    /**
     * Retorna os dados para o gráfico de barras (Receitas vs. Despesas por Mês).
     */
    private function getDadosGraficoBarras($ano)
    {
        $receitasPorMes = Receita::selectRaw('MONTH(data) as mes, SUM(valor) as total')
                                ->whereYear('data', $ano)
                                ->groupBy('mes')
                                ->orderBy('mes')
                                ->get()
                                ->keyBy('mes');

        $despesasPorMes = Despesa::selectRaw('MONTH(data) as mes, SUM(valor) as total')
                                ->whereYear('data', $ano)
                                ->groupBy('mes')
                                ->orderBy('mes')
                                ->get()
                                ->keyBy('mes');

        $meses = [];
        $dadosReceitas = [];
        $dadosDespesas = [];

        for ($i = 1; $i <= 12; $i++) {
            $meses[] = Carbon::create(null, $i, 1)->monthName; // Nome do mês
            $dadosReceitas[] = $receitasPorMes->get($i)->total ?? 0;
            $dadosDespesas[] = $despesasPorMes->get($i)->total ?? 0;
        }

        return [
            'labels' => $meses,
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.7)', // Verde
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'data' => $dadosReceitas,
                ],
                [
                    'label' => 'Despesas',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.7)', // Vermelho
                    'borderColor' => 'rgba(220, 53, 69, 1)',
                    'data' => $dadosDespesas,
                ],
            ],
        ];
    }

    /**
     * Retorna os dados para o gráfico de pizza (Distribuição de Despesas por Categoria).
     */
    private function getDadosGraficoPizza($mes, $ano)
    {
        $despesasPorCategoria = Despesa::selectRaw('categoria_id, SUM(valor) as total')
                                        ->whereMonth('data', $mes)
                                        ->whereYear('data', $ano)
                                        ->groupBy('categoria_id')
                                        ->with('categoria')
                                        ->get();

        $labels = [];
        $dados = [];
        $cores = [];

        // Cores predefinidas para o gráfico de pizza
        $defaultColors = [
            '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6c757d', '#fd7e14', '#e83e8c', '#6f42c1', '#20c997'
        ];
        $colorIndex = 0;

        foreach ($despesasPorCategoria as $item) {
            $labels[] = $item->categoria->nome ?? 'Outros';
            $dados[] = $item->total;
            $cores[] = $defaultColors[$colorIndex % count($defaultColors)];
            $colorIndex++;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $dados,
                    'backgroundColor' => $cores,
                ],
            ],
        ];
    }

    /**
     * Retorna os dados para o gráfico de linha (Evolução do Saldo).
     */
    private function getDadosGraficoLinha($ano)
    {
        $saldoMensal = [];
        // Inicializa o saldo acumulado com o saldo do ano anterior até dezembro, ou 0 se não houver
        $saldoAcumuladoAnterior = $this->getSaldoAcumuladoAteDezembroDoAnoAnterior($ano - 1);

        for ($mes = 1; $mes <= 12; $mes++) {
            $receitasMes = Receita::whereMonth('data', $mes)
                                   ->whereYear('data', $ano)
                                   ->sum('valor');
            $despesasMes = Despesa::whereMonth('data', $mes)
                                   ->whereYear('data', $ano)
                                   ->sum('valor');

            // O saldo do mês atual é o saldo acumulado anterior + (receitas do mês - despesas do mês)
            $saldoAcumuladoAnterior += ($receitasMes - $despesasMes);
            $saldoMensal[$mes] = $saldoAcumuladoAnterior;
        }

        $labels = [];
        $dados = [];
        foreach ($saldoMensal as $mes => $saldo) {
            $labels[] = Carbon::create(null, $mes, 1)->monthName;
            $dados[] = $saldo;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Saldo Acumulado',
                    'fill' => false,
                    'borderColor' => '#007bff',
                    'data' => $dados,
                ],
            ],
        ];
    }

    /**
     * Calcula o saldo acumulado até dezembro do ano especificado.
     * Usado para inicializar o gráfico de evolução do saldo.
     */
    private function getSaldoAcumuladoAteDezembroDoAnoAnterior($ano)
    {
        if ($ano < 1900) { // Evita anos muito antigos ou inválidos
            return 0;
        }

        $dataFimAnoAnterior = Carbon::createFromDate($ano, 12, 31)->endOfDay();

        $receitasAteAnoAnterior = Receita::where('data', '<=', $dataFimAnoAnterior)->sum('valor');
        $despesasAteAnoAnterior = Despesa::where('data', '<=', $dataFimAnoAnterior)->sum('valor');

        return $receitasAteAnoAnterior - $despesasAteAnoAnterior;
    }


    // Métodos para relatórios financeiros (mantidos do seu código anterior)
    public function relatoriosIndex()
    {
        return view('financeiro.relatorios.index');
    }

    public function transacoes(Request $request)
    {
        $query = Receita::with('categoria');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->whereBetween('data', [$dataInicio, $dataFim]);
        } elseif ($request->filled('data_inicio')) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $query->where('data', '>=', $dataInicio);
        } elseif ($request->filled('data_fim')) {
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->where('data', '<=', $dataFim);
        }
        $receitas = $query->orderBy('data', 'desc')->get();

        $query = Despesa::with('categoria');
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->whereBetween('data', [$dataInicio, $dataFim]);
        } elseif ($request->filled('data_inicio')) {
            $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
            $query->where('data', '>=', $dataInicio);
        } elseif ($request->filled('data_fim')) {
            $dataFim = Carbon::parse($request->data_fim)->endOfDay();
            $query->where('data', '<=', $dataFim);
        }
        $despesas = $query->orderBy('data', 'desc')->get();

        $categorias = Categoria::orderBy('nome')->get();

        return view('financeiro.relatorios.transacoes', compact('receitas', 'despesas', 'categorias'));
    }

    public function fluxoCaixa(Request $request)
    {
        $dataInicio = Carbon::parse($request->input('data_inicio', Carbon::now()->startOfMonth()));
        $dataFim = Carbon::parse($request->input('data_fim', Carbon::now()->endOfMonth()));

        $receitas = Receita::whereBetween('data', [$dataInicio, $dataFim])->orderBy('data')->get();
        $despesas = Despesa::whereBetween('data', [$dataInicio, $dataFim])->orderBy('data')->get();

        $fluxoCaixa = collect();
        $datasUnicas = $receitas->pluck('data')->merge($despesas->pluck('data'))->unique()->sort();

        $saldoAcumulado = 0;
        foreach ($datasUnicas as $data) {
            $receitasDoDia = $receitas->where('data', $data)->sum('valor');
            $despesasDoDia = $despesas->where('data', $data)->sum('valor');
            $saldoDoDia = $receitasDoDia - $despesasDoDia;
            $saldoAcumulado += $saldoDoDia;

            $fluxoCaixa->push([
                'data' => $data,
                'receitas' => $receitasDoDia,
                'despesas' => $despesasDoDia,
                'saldo_dia' => $saldoDoDia,
                'saldo_acumulado' => $saldoAcumulado,
            ]);
        }

        return view('financeiro.relatorios.fluxo_caixa', compact('fluxoCaixa', 'dataInicio', 'dataFim'));
    }

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
}
