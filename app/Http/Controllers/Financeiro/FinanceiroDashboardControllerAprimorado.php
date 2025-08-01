<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Categoria;
use App\Models\Venda;
use App\Models\Reserva;
use App\Models\User;
use App\Models\TransacaoRecorrente;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FinanceiroDashboardController extends Controller
{
    /**
     * Exibe o dashboard financeiro principal com todas as funcionalidades aprimoradas.
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

        // Dados para Gráficos Originais
        $dadosGraficoBarras = $this->getDadosGraficoBarras($ano);
        $dadosGraficoPizza = $this->getDadosGraficoPizza($mes, $ano);
        $dadosGraficoLinha = $this->getDadosGraficoLinha($ano);

        // NOVO: Dados para Comparativo de Períodos
        $dadosComparativo = $this->getDadosComparativoPeriodos($ano, $mes);

        // NOVO: Top 5 Despesas e Receitas
        $top5Despesas = $this->getTop5Despesas($ano, $mes);
        $top5Receitas = $this->getTop5Receitas($ano, $mes);

        // NOVO: Análise de Tendências
        $analiseTendencias = $this->getAnaliseTendencias($ano, $mes);

        // NOVO: Gráfico de Ponto de Equilíbrio
        $dadosPontoEquilibrio = $this->getDadosPontoEquilibrio($ano, $mes);

        // NOVO: Fluxo de Caixa Futuro
        $fluxoCaixaFuturo = $this->getFluxoCaixaFuturo(90); // 90 dias

        return view('financeiro.dashboard', compact(
            'receitasMes', 'despesasMes', 'saldoMes', 'saldoTotal',
            'comissaoAcumuladaMes', 'ano', 'mes',
            'dadosGraficoBarras', 'dadosGraficoPizza', 'dadosGraficoLinha',
            'dadosComparativo', 'top5Despesas', 'top5Receitas', 'analiseTendencias',
            'dadosPontoEquilibrio', 'fluxoCaixaFuturo'
        ));
    }

    /**
     * Obter dados comparativos de períodos
     */
    private function getDadosComparativoPeriodos(int $ano, int $mes): array
    {
        // Período atual
        $periodoAtual = [
            'inicio' => Carbon::create($ano, $mes, 1)->startOfMonth(),
            'fim' => Carbon::create($ano, $mes, 1)->endOfMonth()
        ];
        
        // Período anterior (mês anterior)
        $periodoAnterior = [
            'inicio' => Carbon::create($ano, $mes, 1)->subMonth()->startOfMonth(),
            'fim' => Carbon::create($ano, $mes, 1)->subMonth()->endOfMonth()
        ];
        
        // Mesmo período do ano anterior
        $periodoAnoAnterior = [
            'inicio' => Carbon::create($ano - 1, $mes, 1)->startOfMonth(),
            'fim' => Carbon::create($ano - 1, $mes, 1)->endOfMonth()
        ];
        
        // Calcular dados do período atual
        $receitasAtual = Receita::whereBetween('data', [$periodoAtual['inicio'], $periodoAtual['fim']])->sum('valor');
        $despesasAtual = Despesa::whereBetween('data', [$periodoAtual['inicio'], $periodoAtual['fim']])->sum('valor');
        $saldoAtual = $receitasAtual - $despesasAtual;
        
        // Calcular dados do período anterior
        $receitasAnterior = Receita::whereBetween('data', [$periodoAnterior['inicio'], $periodoAnterior['fim']])->sum('valor');
        $despesasAnterior = Despesa::whereBetween('data', [$periodoAnterior['inicio'], $periodoAnterior['fim']])->sum('valor');
        $saldoAnterior = $receitasAnterior - $despesasAnterior;
        
        // Calcular dados do ano anterior
        $receitasAnoAnterior = Receita::whereBetween('data', [$periodoAnoAnterior['inicio'], $periodoAnoAnterior['fim']])->sum('valor');
        $despesasAnoAnterior = Despesa::whereBetween('data', [$periodoAnoAnterior['inicio'], $periodoAnoAnterior['fim']])->sum('valor');
        $saldoAnoAnterior = $receitasAnoAnterior - $despesasAnoAnterior;
        
        // Calcular variações percentuais
        $variacaoReceitasMesAnterior = $this->calcularVariacaoPercentual($receitasAtual, $receitasAnterior);
        $variacaoDespesasMesAnterior = $this->calcularVariacaoPercentual($despesasAtual, $despesasAnterior);
        $variacaoSaldoMesAnterior = $this->calcularVariacaoPercentual($saldoAtual, $saldoAnterior);
        
        $variacaoReceitasAnoAnterior = $this->calcularVariacaoPercentual($receitasAtual, $receitasAnoAnterior);
        $variacaoDespesasAnoAnterior = $this->calcularVariacaoPercentual($despesasAtual, $despesasAnoAnterior);
        $variacaoSaldoAnoAnterior = $this->calcularVariacaoPercentual($saldoAtual, $saldoAnoAnterior);
        
        return [
            'periodo_atual' => [
                'receitas' => $receitasAtual,
                'despesas' => $despesasAtual,
                'saldo' => $saldoAtual,
                'label' => Carbon::create($ano, $mes, 1)->format('M/Y')
            ],
            'periodo_anterior' => [
                'receitas' => $receitasAnterior,
                'despesas' => $despesasAnterior,
                'saldo' => $saldoAnterior,
                'label' => Carbon::create($ano, $mes, 1)->subMonth()->format('M/Y')
            ],
            'ano_anterior' => [
                'receitas' => $receitasAnoAnterior,
                'despesas' => $despesasAnoAnterior,
                'saldo' => $saldoAnoAnterior,
                'label' => Carbon::create($ano - 1, $mes, 1)->format('M/Y')
            ],
            'variacoes_mes_anterior' => [
                'receitas' => $variacaoReceitasMesAnterior,
                'despesas' => $variacaoDespesasMesAnterior,
                'saldo' => $variacaoSaldoMesAnterior
            ],
            'variacoes_ano_anterior' => [
                'receitas' => $variacaoReceitasAnoAnterior,
                'despesas' => $variacaoDespesasAnoAnterior,
                'saldo' => $variacaoSaldoAnoAnterior
            ]
        ];
    }
    
    /**
     * Calcular variação percentual entre dois valores
     */
    private function calcularVariacaoPercentual($valorAtual, $valorAnterior): array
    {
        if ($valorAnterior == 0) {
            return [
                'percentual' => $valorAtual > 0 ? 100 : 0,
                'absoluto' => $valorAtual,
                'tipo' => $valorAtual >= 0 ? 'positiva' : 'negativa'
            ];
        }
        
        $variacao = (($valorAtual - $valorAnterior) / abs($valorAnterior)) * 100;
        
        return [
            'percentual' => round($variacao, 2),
            'absoluto' => $valorAtual - $valorAnterior,
            'tipo' => $variacao >= 0 ? 'positiva' : 'negativa'
        ];
    }
    
    /**
     * Obter Top 5 Despesas do período
     */
    private function getTop5Despesas(int $ano, int $mes): array
    {
        $top5Despesas = Despesa::select(
            'descricao',
            DB::raw('SUM(valor) as total_valor'),
            DB::raw('COUNT(*) as quantidade_transacoes'),
            'categoria_id'
        )
        ->with('categoria:id,nome')
        ->whereYear('data', $ano)
        ->whereMonth('data', $mes)
        ->groupBy('descricao', 'categoria_id')
        ->orderBy('total_valor', 'desc')
        ->limit(5)
        ->get();
        
        $totalDespesasPeriodo = Despesa::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        return [
            'despesas' => $top5Despesas->map(function ($despesa) use ($totalDespesasPeriodo) {
                return [
                    'descricao' => $despesa->descricao,
                    'categoria' => $despesa->categoria->nome ?? 'Sem Categoria',
                    'valor' => $despesa->total_valor,
                    'quantidade' => $despesa->quantidade_transacoes,
                    'percentual' => $totalDespesasPeriodo > 0 ? round(($despesa->total_valor / $totalDespesasPeriodo) * 100, 2) : 0
                ];
            }),
            'total_periodo' => $totalDespesasPeriodo
        ];
    }
    
    /**
     * Obter Top 5 Receitas do período
     */
    private function getTop5Receitas(int $ano, int $mes): array
    {
        $top5Receitas = Receita::select(
            'descricao',
            DB::raw('SUM(valor) as total_valor'),
            DB::raw('COUNT(*) as quantidade_transacoes'),
            'categoria_id'
        )
        ->with('categoria:id,nome')
        ->whereYear('data', $ano)
        ->whereMonth('data', $mes)
        ->groupBy('descricao', 'categoria_id')
        ->orderBy('total_valor', 'desc')
        ->limit(5)
        ->get();
        
        $totalReceitasPeriodo = Receita::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        return [
            'receitas' => $top5Receitas->map(function ($receita) use ($totalReceitasPeriodo) {
                return [
                    'descricao' => $receita->descricao,
                    'categoria' => $receita->categoria->nome ?? 'Sem Categoria',
                    'valor' => $receita->total_valor,
                    'quantidade' => $receita->quantidade_transacoes,
                    'percentual' => $totalReceitasPeriodo > 0 ? round(($receita->total_valor / $totalReceitasPeriodo) * 100, 2) : 0
                ];
            }),
            'total_periodo' => $totalReceitasPeriodo
        ];
    }

    /**
     * NOVO: Obter dados para o Gráfico de Ponto de Equilíbrio
     */
    private function getDadosPontoEquilibrio(int $ano, int $mes): array
    {
        // Calcular custos fixos e variáveis
        $custosFixos = $this->calcularCustosFixos($ano, $mes);
        $custosVariaveis = $this->calcularCustosVariaveis($ano, $mes);
        $receitaTotal = Receita::whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        // Calcular ponto de equilíbrio
        $pontoEquilibrio = $custosFixos + $custosVariaveis;
        $margemSeguranca = $receitaTotal - $pontoEquilibrio;
        $percentualMargemSeguranca = $receitaTotal > 0 ? ($margemSeguranca / $receitaTotal) * 100 : 0;
        
        // Status do ponto de equilíbrio
        $status = 'equilibrio';
        $statusTexto = 'No Ponto de Equilíbrio';
        $statusCor = 'warning';
        
        if ($receitaTotal > $pontoEquilibrio) {
            $status = 'lucro';
            $statusTexto = 'Acima do Ponto de Equilíbrio';
            $statusCor = 'success';
        } elseif ($receitaTotal < $pontoEquilibrio) {
            $status = 'prejuizo';
            $statusTexto = 'Abaixo do Ponto de Equilíbrio';
            $statusCor = 'danger';
        }
        
        return [
            'receita_total' => $receitaTotal,
            'custos_fixos' => $custosFixos,
            'custos_variaveis' => $custosVariaveis,
            'ponto_equilibrio' => $pontoEquilibrio,
            'margem_seguranca' => $margemSeguranca,
            'percentual_margem_seguranca' => round($percentualMargemSeguranca, 2),
            'status' => $status,
            'status_texto' => $statusTexto,
            'status_cor' => $statusCor,
            'grafico' => [
                'labels' => ['Custos Fixos', 'Custos Variáveis', 'Receita Total'],
                'datasets' => [
                    [
                        'label' => 'Valores (R$)',
                        'data' => [$custosFixos, $custosVariaveis, $receitaTotal],
                        'backgroundColor' => [
                            'rgba(255, 99, 132, 0.6)',   // Vermelho para custos fixos
                            'rgba(255, 159, 64, 0.6)',   // Laranja para custos variáveis
                            'rgba(75, 192, 192, 0.6)'    // Verde para receita
                        ],
                        'borderColor' => [
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        'borderWidth' => 1
                    ]
                ]
            ]
        ];
    }

    /**
     * Calcular custos fixos (categorias que são consideradas fixas)
     */
    private function calcularCustosFixos(int $ano, int $mes): float
    {
        // Categorias consideradas como custos fixos
        $categoriasCustosFixos = [
            'Aluguel', 'Salários', 'Seguros', 'Financiamentos', 
            'Energia Elétrica', 'Telefone', 'Internet', 'Impostos Fixos'
        ];
        
        return Despesa::join('categorias', 'despesas.categoria_id', '=', 'categorias.id')
            ->whereIn('categorias.nome', $categoriasCustosFixos)
            ->whereYear('despesas.data', $ano)
            ->whereMonth('despesas.data', $mes)
            ->sum('despesas.valor');
    }

    /**
     * Calcular custos variáveis (outras categorias de despesas)
     */
    private function calcularCustosVariaveis(int $ano, int $mes): float
    {
        // Categorias consideradas como custos fixos
        $categoriasCustosFixos = [
            'Aluguel', 'Salários', 'Seguros', 'Financiamentos', 
            'Energia Elétrica', 'Telefone', 'Internet', 'Impostos Fixos'
        ];
        
        return Despesa::join('categorias', 'despesas.categoria_id', '=', 'categorias.id')
            ->whereNotIn('categorias.nome', $categoriasCustosFixos)
            ->whereYear('despesas.data', $ano)
            ->whereMonth('despesas.data', $mes)
            ->sum('despesas.valor');
    }

    /**
     * NOVO: Obter dados para Fluxo de Caixa Futuro
     */
    private function getFluxoCaixaFuturo(int $diasFuturos = 90): array
    {
        $dataInicio = Carbon::now()->startOfDay();
        $dataFim = Carbon::now()->addDays($diasFuturos)->endOfDay();
        
        $fluxoFuturo = [];
        $saldoAtual = $this->getSaldoAtual();
        
        // Buscar transações recorrentes
        $transacoesRecorrentes = TransacaoRecorrente::where('ativo', true)
            ->where('data_inicio', '<=', $dataFim)
            ->where(function($query) use ($dataFim) {
                $query->whereNull('data_fim')
                      ->orWhere('data_fim', '>=', Carbon::now());
            })
            ->get();
        
        // Gerar projeções dia a dia
        for ($data = clone $dataInicio; $data->lte($dataFim); $data->addDay()) {
            $receitasDia = 0;
            $despesasDia = 0;
            $transacoesDia = [];
            
            // Verificar transações recorrentes para este dia
            foreach ($transacoesRecorrentes as $transacao) {
                if ($this->deveExecutarTransacaoRecorrente($transacao, $data)) {
                    if ($transacao->tipo == 'receita') {
                        $receitasDia += $transacao->valor;
                    } else {
                        $despesasDia += $transacao->valor;
                    }
                    
                    $transacoesDia[] = [
                        'descricao' => $transacao->descricao,
                        'tipo' => $transacao->tipo,
                        'valor' => $transacao->valor,
                        'categoria' => $transacao->categoria->nome ?? 'N/A'
                    ];
                }
            }
            
            $saldoDia = $receitasDia - $despesasDia;
            $saldoAtual += $saldoDia;
            
            $fluxoFuturo[] = [
                'data' => $data->format('Y-m-d'),
                'data_formatada' => $data->format('d/m/Y'),
                'receitas' => $receitasDia,
                'despesas' => $despesasDia,
                'saldo_dia' => $saldoDia,
                'saldo_acumulado' => $saldoAtual,
                'transacoes' => $transacoesDia
            ];
        }
        
        // Preparar dados para gráfico
        $labels = array_column($fluxoFuturo, 'data_formatada');
        $receitasData = array_column($fluxoFuturo, 'receitas');
        $despesasData = array_column($fluxoFuturo, 'despesas');
        $saldoAcumuladoData = array_column($fluxoFuturo, 'saldo_acumulado');
        
        // Calcular estatísticas
        $totalReceitasFuturas = array_sum($receitasData);
        $totalDespesasFuturas = array_sum($despesasData);
        $saldoProjetado = $totalReceitasFuturas - $totalDespesasFuturas;
        $menorSaldo = min($saldoAcumuladoData);
        $maiorSaldo = max($saldoAcumuladoData);
        
        return [
            'fluxo_diario' => $fluxoFuturo,
            'total_receitas_futuras' => $totalReceitasFuturas,
            'total_despesas_futuras' => $totalDespesasFuturas,
            'saldo_projetado' => $saldoProjetado,
            'menor_saldo' => $menorSaldo,
            'maior_saldo' => $maiorSaldo,
            'dias_projetados' => $diasFuturos,
            'grafico' => [
                'labels' => array_slice($labels, 0, 30), // Mostrar apenas 30 dias no gráfico
                'datasets' => [
                    [
                        'type' => 'bar',
                        'label' => 'Receitas Projetadas',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                        'data' => array_slice($receitasData, 0, 30),
                        'yAxisID' => 'y',
                    ],
                    [
                        'type' => 'bar',
                        'label' => 'Despesas Projetadas',
                        'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                        'data' => array_slice($despesasData, 0, 30),
                        'yAxisID' => 'y',
                    ],
                    [
                        'type' => 'line',
                        'label' => 'Saldo Acumulado Projetado',
                        'borderColor' => 'rgb(54, 162, 235)',
                        'borderWidth' => 2,
                        'fill' => false,
                        'data' => array_slice($saldoAcumuladoData, 0, 30),
                        'yAxisID' => 'y1',
                        'pointRadius' => 3,
                        'pointBackgroundColor' => 'rgb(54, 162, 235)',
                    ]
                ]
            ]
        ];
    }

    /**
     * Verificar se uma transação recorrente deve ser executada em uma data específica
     */
    private function deveExecutarTransacaoRecorrente($transacao, Carbon $data): bool
    {
        $dataInicio = Carbon::parse($transacao->data_inicio);
        
        // Se a data é anterior ao início da recorrência
        if ($data->lt($dataInicio)) {
            return false;
        }
        
        // Se há data fim e a data é posterior
        if ($transacao->data_fim && $data->gt(Carbon::parse($transacao->data_fim))) {
            return false;
        }
        
        // Verificar frequência
        switch ($transacao->frequencia) {
            case 'diaria':
                return true;
                
            case 'semanal':
                return $data->dayOfWeek == $dataInicio->dayOfWeek;
                
            case 'mensal':
                return $data->day == $dataInicio->day;
                
            case 'anual':
                return $data->month == $dataInicio->month && $data->day == $dataInicio->day;
                
            default:
                return false;
        }
    }

    /**
     * Obter saldo atual
     */
    private function getSaldoAtual(): float
    {
        $totalReceitas = Receita::sum('valor');
        $totalDespesas = Despesa::sum('valor');
        return $totalReceitas - $totalDespesas;
    }

    /**
     * Obter análise de tendências (últimos 6 meses)
     */
    private function getAnaliseTendencias(int $ano, int $mes): array
    {
        $meses = [];
        $receitas = [];
        $despesas = [];
        $saldos = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $dataRef = Carbon::create($ano, $mes, 1)->subMonths($i);
            $mesNome = $dataRef->format('M/y');
            
            $receitaMes = Receita::whereYear('data', $dataRef->year)
                ->whereMonth('data', $dataRef->month)
                ->sum('valor');
                
            $despesaMes = Despesa::whereYear('data', $dataRef->year)
                ->whereMonth('data', $dataRef->month)
                ->sum('valor');
            
            $meses[] = $mesNome;
            $receitas[] = $receitaMes;
            $despesas[] = $despesaMes;
            $saldos[] = $receitaMes - $despesaMes;
        }
        
        // Calcular tendência (crescimento/decrescimento)
        $tendenciaReceitas = $this->calcularTendencia($receitas);
        $tendenciaDespesas = $this->calcularTendencia($despesas);
        $tendenciaSaldos = $this->calcularTendencia($saldos);
        
        return [
            'labels' => $meses,
            'receitas' => $receitas,
            'despesas' => $despesas,
            'saldos' => $saldos,
            'tendencias' => [
                'receitas' => $tendenciaReceitas,
                'despesas' => $tendenciaDespesas,
                'saldos' => $tendenciaSaldos
            ]
        ];
    }
    
    /**
     * Calcular tendência de uma série de dados
     */
    private function calcularTendencia(array $dados): array
    {
        $n = count($dados);
        if ($n < 2) {
            return ['tipo' => 'estavel', 'percentual' => 0];
        }
        
        $primeiro = $dados[0];
        $ultimo = $dados[$n - 1];
        
        if ($primeiro == 0) {
            return [
                'tipo' => $ultimo > 0 ? 'crescimento' : 'estavel',
                'percentual' => 0
            ];
        }
        
        $variacao = (($ultimo - $primeiro) / abs($primeiro)) * 100;
        
        $tipo = 'estavel';
        if ($variacao > 5) {
            $tipo = 'crescimento';
        } elseif ($variacao < -5) {
            $tipo = 'decrescimento';
        }
        
        return [
            'tipo' => $tipo,
            'percentual' => round($variacao, 2)
        ];
    }

    // Métodos originais adaptados
    private function getDadosGraficoBarras(int $ano): array
    {
        $receitasPorMes = Receita::select(
                DB::raw('MONTH(data) as mes'),
                DB::raw('SUM(valor) as total_receita')
            )
            ->whereYear('data', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_receita', 'mes')
            ->toArray();

        $despesasPorMes = Despesa::select(
                DB::raw('MONTH(data) as mes'),
                DB::raw('SUM(valor) as total_despesa')
            )
            ->whereYear('data', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_despesa', 'mes')
            ->toArray();

        $meses = [];
        $receitas = [];
        $despesas = [];

        for ($i = 1; $i <= 12; $i++) {
            $mesNome = Carbon::create(null, $i, 1)->monthName;
            $meses[] = ucfirst($mesNome);

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

    private function getDadosGraficoPizza(int $mes, int $ano): array
    {
        $despesasPorCategoria = Despesa::select(
                'categorias.nome as categoria_nome',
                DB::raw('SUM(despesas.valor) as total_gasto')
            )
            ->join('categorias', 'despesas.categoria_id', '=', 'categorias.id')
            ->whereYear('despesas.data', $ano)
            ->whereMonth('despesas.data', $mes)
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

    private function getDadosGraficoLinha(int $ano): array
    {
        $saldoAcumulado = [];
        $saldoAtual = $this->getSaldoAcumuladoAteDezembroDoAnoAnterior($ano);

        for ($mes = 1; $mes <= 12; $mes++) {
            $receitasMes = Receita::whereYear('data', $ano)
                                ->whereMonth('data', $mes)
                                ->sum('valor');

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

    private function getSaldoAcumuladoAteDezembroDoAnoAnterior(int $ano): float
    {
        $anoAnterior = $ano - 1;
        $receitasAnoAnterior = Receita::whereYear('data', '<=', $anoAnterior)->sum('valor');
        $despesasAnoAnterior = Despesa::whereYear('data', '<=', $anoAnterior)->sum('valor');

        return $receitasAnoAnterior - $despesasAnoAnterior;
    }
}

