<?php

namespace App\Services;

use App\Models\Incubacao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Calcular KPIs de performance com Cache.
     */
    public function calcularKPIs()
    {
        return Cache::remember('dashboard_kpis', 3600, function () {
            $dataLimite30Dias = Carbon::now()->subDays(30);
            
            // Taxa de Eclosão (últimos 30 dias)
            $totalOvos = Incubacao::where('ativo', false)->where('data_entrada_incubadora', '>=', $dataLimite30Dias)->sum('quantidade_ovos');
            $totalInferteis = Incubacao::where('ativo', false)->where('data_entrada_incubadora', '>=', $dataLimite30Dias)->sum('quantidade_inferteis');
            $totalEclodidos = Incubacao::where('ativo', false)->where('data_entrada_incubadora', '>=', $dataLimite30Dias)->sum('quantidade_eclodidos');
            
            $ovosViaveis = $totalOvos - $totalInferteis;
            $taxaEclosao30Dias = $ovosViaveis > 0 ? ($totalEclodidos / $ovosViaveis) * 100 : 0;
            
            // Taxa de Fertilidade
            $taxaFertilidade = $totalOvos > 0 ? (($totalOvos - $totalInferteis) / $totalOvos) * 100 : 0;
            
            // Eficiência da Melhor Chocadeira
            $eficienciaChocadeiras = Incubacao::select('chocadeira')
                ->selectRaw('AVG(CASE WHEN quantidade_ovos - quantidade_inferteis > 0 THEN (quantidade_eclodidos / (quantidade_ovos - quantidade_inferteis)) * 100 ELSE 0 END) as eficiencia')
                ->where('ativo', false)
                ->where('data_entrada_incubadora', '>=', $dataLimite30Dias)
                ->groupBy('chocadeira')
                ->orderByDesc('eficiencia')
                ->first();
            
            $melhorChocadeiraEficiencia = $eficienciaChocadeiras ? $eficienciaChocadeiras->eficiencia : 0;
            
            // Média de Ovos por Incubação
            $mediaOvosPorIncubacao = Incubacao::where('ativo', false)->where('data_entrada_incubadora', '>=', $dataLimite30Dias)->avg('quantidade_ovos') ?? 0;
            
            return [
                'taxa_eclosao_30_dias' => round($taxaEclosao30Dias, 1),
                'taxa_fertilidade' => round($taxaFertilidade, 1),
                'melhor_chocadeira_eficiencia' => round($melhorChocadeiraEficiencia, 1),
                'media_ovos_incubacao' => round($mediaOvosPorIncubacao, 1)
            ];
        });
    }

    /**
     * Retorna os dados para o gráfico de Taxa de Eclosão Mensal.
     */
    public function getDadosTaxaEclosaoMensal()
    {
        $dataLimite12Meses = Carbon::now()->subMonths(12);

        $eclosaoMensal = Incubacao::select(
                                DB::raw('YEAR(data_entrada_incubadora) as ano'),
                                DB::raw('MONTH(data_entrada_incubadora) as mes'),
                                DB::raw('SUM(quantidade_ovos) as total_ovos_mes'),
                                DB::raw('SUM(quantidade_inferteis) as total_inferteis_mes'),
                                DB::raw('SUM(quantidade_eclodidos) as total_eclodidos_mes')
                            )
                            ->where('data_entrada_incubadora', '>=', $dataLimite12Meses)
                            ->where('ativo', false)
                            ->groupBy('ano', 'mes')
                            ->orderBy('ano')
                            ->orderBy('mes')
                            ->get();

        $labels = [];
        $dataTaxaEclosao = [];
        $dataTaxaNaoEclosao = [];
        $totalOvosGeral = 0;
        $totalInferteisGeral = 0;
        $totalEclodidosGeral = 0;

        $currentMonth = Carbon::now()->subMonths(11)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $monthYear = $currentMonth->format('Y-m');
            $labels[] = $currentMonth->format('M/Y');

            $foundData = $eclosaoMensal->first(function ($item) use ($currentMonth) {
                return $item->ano == $currentMonth->year && $item->mes == $currentMonth->month;
            });

            $totalOvosMes = $foundData->total_ovos_mes ?? 0;
            $totalInferteisMes = $foundData->total_inferteis_mes ?? 0;
            $totalEclodidosMes = $foundData->total_eclodidos_mes ?? 0;

            $ovosViaveisMes = $totalOvosMes - $totalInferteisMes;
            if ($ovosViaveisMes < 0) { $ovosViaveisMes = 0; }
            $ovosNaoEclodidosViaveisMes = $ovosViaveisMes - $totalEclodidosMes;
            if ($ovosNaoEclodidosViaveisMes < 0) { $ovosNaoEclodidosViaveisMes = 0; }

            $taxaEclosaoMes = ($ovosViaveisMes > 0) ? round(($totalEclodidosMes / $ovosViaveisMes) * 100, 2) : 0;
            $taxaNaoEclosaoMes = ($ovosViaveisMes > 0) ? round(($ovosNaoEclodidosViaveisMes / $ovosViaveisMes) * 100, 2) : 0;

            $dataTaxaEclosao[] = $taxaEclosaoMes;
            $dataTaxaNaoEclosao[] = $taxaNaoEclosaoMes;

            $totalOvosGeral += $totalOvosMes;
            $totalInferteisGeral += $totalInferteisMes;
            $totalEclodidosGeral += $totalEclodidosMes;

            $currentMonth->addMonth();
        }

        $ovosViaveisGeral = $totalOvosGeral - $totalInferteisGeral;
        if ($ovosViaveisGeral < 0) { $ovosViaveisGeral = 0; }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Taxa de Eclosão (Viáveis)',
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'fill' => true,
                    'data' => $dataTaxaEclosao,
                ],
                [
                    'label' => 'Taxa de Não Eclosão (Viáveis)',
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'fill' => true,
                    'data' => $dataTaxaNaoEclosao,
                ],
            ],
            'metrics' => [
                'total_ovos' => $totalOvosGeral,
                'total_inferteis' => $totalInferteisGeral,
                'total_eclodidos' => $totalEclodidosGeral,
                'ovos_viaveis' => $ovosViaveisGeral,
            ]
        ];
    }

    /**
     * Retorna os dados para o gráfico de Ovos Não Eclodidos (Infectados, Mortos) por mês.
     */
    public function getDadosOvosNaoEclodidosMensal()
    {
        $dataLimite12Meses = Carbon::now()->subMonths(12);

        $perdasPorMes = Incubacao::select(
                                DB::raw('YEAR(data_entrada_incubadora) as ano'),
                                DB::raw('MONTH(data_entrada_incubadora) as mes'),
                                DB::raw('SUM(quantidade_infectados) as total_infectados'),
                                DB::raw('SUM(quantidade_mortos) as total_mortos')
                            )
                            ->where('data_entrada_incubadora', '>=', $dataLimite12Meses)
                            ->where('ativo', false)
                            ->groupBy('ano', 'mes')
                            ->orderBy('ano')
                            ->orderBy('mes')
                            ->get();

        $labels = [];
        $dataInfectados = [];
        $dataMortos = [];

        $currentMonth = Carbon::now()->subMonths(11)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $monthYear = $currentMonth->format('Y-m');
            $labels[] = $currentMonth->format('M/Y');

            $foundData = $perdasPorMes->first(function ($item) use ($currentMonth) {
                return $item->ano == $currentMonth->year && $item->mes == $currentMonth->month;
            });

            $dataInfectados[] = $foundData->total_infectados ?? 0;
            $dataMortos[] = $foundData->total_mortos ?? 0;

            $currentMonth->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ovos Infectados',
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'fill' => true,
                    'data' => $dataInfectados,
                ],
                [
                    'label' => 'Ovos Mortos',
                    'borderColor' => '#6c757d',
                    'backgroundColor' => 'rgba(108, 117, 125, 0.2)',
                    'fill' => true,
                    'data' => $dataMortos,
                ],
            ],
            'metrics' => [
                'total_infectados' => Incubacao::sum('quantidade_infectados'),
                'total_mortos' => Incubacao::sum('quantidade_mortos'),
            ]
        ];
    }

    /**
     * Retorna os dados para o desempenho por chocadeira.
     */
    public function getDadosDesempenhoChocadeira()
    {
        $desempenho = Incubacao::selectRaw('chocadeira, SUM(quantidade_ovos) as total_ovos, SUM(quantidade_eclodidos) as total_eclodidos, SUM(quantidade_inferteis) as total_inferteis')
                                ->where('ativo', false)
                                ->groupBy('chocadeira')
                                ->get();

        $labels = [];
        $totalOvosPorChocadeira = [];
        $totalEclodidosPorChocadeira = [];
        $taxasEclosao = [];

        foreach ($desempenho as $item) {
            $chocadeiraNome = $item->chocadeira ?? 'Não Definida';
            $labels[] = $chocadeiraNome;
            $totalOvosPorChocadeira[] = $item->total_ovos;
            $totalEclodidosPorChocadeira[] = $item->total_eclodidos;

            $ovosViáveis = $item->total_ovos - $item->total_inferteis;
            $taxaEclosao = ($ovosViáveis > 0) ? round(($item->total_eclodidos / $ovosViáveis) * 100, 2) : 0;
            $taxasEclosao[] = $taxaEclosao;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ovos Colocados',
                    'backgroundColor' => 'rgba(0, 123, 255, 0.7)',
                    'data' => $totalOvosPorChocadeira,
                ],
                [
                    'label' => 'Ovos Eclodidos',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.7)',
                    'data' => $totalEclodidosPorChocadeira,
                ],
            ],
            'taxas_eclosao' => $taxasEclosao,
        ];
    }

    /**
     * Obter previsões de eclosão
     */
    public function obterPrevisoesEclosao()
    {
        $proximasEclosoes = Incubacao::where('ativo', true)
            ->where('data_prevista_eclosao', '>=', Carbon::now())
            ->where('data_prevista_eclosao', '<=', Carbon::now()->addDays(30))
            ->with(['lote', 'tipoAve'])
            ->orderBy('data_prevista_eclosao')
            ->get();
        
        $previsoes = [];
        
        foreach ($proximasEclosoes as $incubacao) {
            $dataEclosao = Carbon::parse($incubacao->data_prevista_eclosao);
            $diasRestantes = Carbon::now()->diffInDays($dataEclosao, false);
            
            // Determinar status
            $status = 'normal';
            if ($diasRestantes <= 2) {
                $status = 'urgente';
            } elseif ($diasRestantes <= 5) {
                $status = 'proximo';
            }
            
            // Verificar se está atrasado
            if ($diasRestantes < 0) {
                $status = 'atrasado';
            }
            
            // Calcular progresso da incubação
            $dataEntrada = Carbon::parse($incubacao->data_entrada_incubadora);
            $totalDias = $dataEntrada->diffInDays($dataEclosao);
            $diasPassados = $dataEntrada->diffInDays(Carbon::now());
            $progresso = $totalDias > 0 ? min(100, ($diasPassados / $totalDias) * 100) : 0;
            
            $previsoes[] = [
                'id' => $incubacao->id,
                'lote' => $incubacao->lote->identificacao_lote ?? 'N/A',
                'tipo_ave' => $incubacao->tipoAve->nome ?? 'N/A',
                'data_eclosao' => $dataEclosao->format('d/m/Y'),
                'dias_restantes' => abs($diasRestantes),
                'quantidade_ovos' => $incubacao->quantidade_ovos,
                'status' => $status,
                'progresso' => round($progresso),
                'chocadeira' => $incubacao->chocadeira ?? 'N/A',
                'temperatura_atual' => $incubacao->temperatura_atual ?? 0,
                'umidade_atual' => $incubacao->umidade_atual ?? 0
            ];
        }
        
        return $previsoes;
    }
}
