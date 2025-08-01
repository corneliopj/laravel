<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Despesa;
use App\Models\Receita;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceiroDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filtros dinâmicos
        $ano = $request->input('ano', Carbon::now()->year);
        $mes = $request->input('mes', Carbon::now()->month);
        
        // Dados para gráficos
        $dadosGraficoLinha = $this->getDadosGraficoLinha($ano, $mes);
        $dadosGraficoBarras = $this->getDadosGraficoBarras($ano, $mes);
        $dadosGraficoPizza = $this->getDadosGraficoPizza($ano, $mes);
        
        // Dados comparativos e rankings
        $dadosComparativo = $this->getDadosComparativo($ano, $mes);
        $top5Despesas = $this->getTop5Despesas($ano, $mes);
        $top5Receitas = $this->getTop5Receitas($ano, $mes);
        
        // Fluxo de caixa futuro (será implementado na FASE 5)
        $fluxoCaixaFuturo = [];
        
        return view('financeiro.dashboard', compact(
            'dadosGraficoLinha',
            'dadosGraficoBarras',
            'dadosGraficoPizza',
            'dadosComparativo',
            'top5Despesas',
            'top5Receitas',
            'fluxoCaixaFuturo',
            'ano',
            'mes'
        ));
    }

    private function getDadosGraficoLinha($ano, $mes)
    {
        // Implementação existente do gráfico de linhas
        $dados = [];
        $meses = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $meses[] = Carbon::create($ano, $i, 1)->format('M');
            $dados['receitas'][] = Receita::whereYear('data', $ano)
                ->whereMonth('data', $i)
                ->sum('valor');
            $dados['despesas'][] = Despesa::whereYear('data', $ano)
                ->whereMonth('data', $i)
                ->sum('valor');
        }
        
        return [
            'labels' => $meses,
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'data' => $dados['receitas'],
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.4
                ],
                [
                    'label' => 'Despesas',
                    'data' => $dados['despesas'],
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'borderColor' => 'rgba(220, 53, 69, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    private function getDadosGraficoBarras($ano, $mes)
    {
        // Implementação existente do gráfico de barras
        $categoriasReceitas = Receita::with('categoria')
            ->selectRaw('categoria_id, SUM(valor) as total')
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->groupBy('categoria_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $categoriasDespesas = Despesa::with('categoria')
            ->selectRaw('categoria_id, SUM(valor) as total')
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->groupBy('categoria_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'receitas' => [
                'labels' => $categoriasReceitas->pluck('categoria.nome')->toArray(),
                'data' => $categoriasReceitas->pluck('total')->toArray()
            ],
            'despesas' => [
                'labels' => $categoriasDespesas->pluck('categoria.nome')->toArray(),
                'data' => $categoriasDespesas->pluck('total')->toArray()
            ]
        ];
    }

    private function getDadosGraficoPizza($ano, $mes)
    {
        // Implementação existente do gráfico de pizza
        $receitas = Receita::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
            
        $despesas = Despesa::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
            
        $saldo = $receitas - $despesas;
        
        return [
            'labels' => ['Receitas', 'Despesas', 'Saldo'],
            'data' => [$receitas, $despesas, $saldo],
            'colors' => ['#28a745', '#dc3545', '#007bff']
        ];
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