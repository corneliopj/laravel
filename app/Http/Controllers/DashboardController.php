<?php

namespace App\Http\Controllers;

use App\Models\Ave;
use App\Models\Incubacao;
use App\Models\Morte;
use App\Models\TipoAve;
use App\Models\Lote;
use App\Models\PosturaOvo;
use App\Models\Venda; // Importar o modelo de Venda
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard com um resumo dos dados e o quadro de incubações.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Quantidade Total de Aves Ativas
        $totalAvesAtivas = Ave::where('ativo', 1)->count();

        // 2. Mortes nos Últimos 30 Dias
        $dataLimite30Dias = Carbon::now()->subDays(30);
        $mortesUltimos30Dias = Morte::where('data_morte', '>=', $dataLimite30Dias)->count();

        // 3. Aves por Tipo (para Gráfico de Pizza)
        $avesPorTipo = Ave::select('tipo_ave_id')
                            ->selectRaw('count(*) as total')
                            ->where('ativo', 1)
                            ->groupBy('tipo_ave_id')
                            ->with('tipoAve')
                            ->get();

        $labelsAvesPorTipo = $avesPorTipo->map(function($item) {
            return $item->tipoAve->nome ?? 'Desconhecido';
        });
        $dataAvesPorTipo = $avesPorTipo->map(function($item) {
            return $item->total;
        });

        // 4. Resumo de Incubações Globais
        $totalIncubacoesAtivas = Incubacao::where('ativo', 1)->count();

        // CORREÇÃO: Cálculo da Taxa de Eclosão Global - APENAS COM INCUBACÕES INATIVAS
        $incubacoesInativasParaTaxa = Incubacao::where('ativo', false)->get(); // Busca apenas incubações inativas para este cálculo

        $totalOvosIncubadosInativos = $incubacoesInativasParaTaxa->sum('quantidade_ovos');
        $totalEclodidosInativos = $incubacoesInativasParaTaxa->sum('quantidade_eclodidos');
        $totalInferteisInativos = $incubacoesInativasParaTaxa->sum('quantidade_inferteis');

        // Calcular ovos viáveis para a taxa de eclosão global (apenas inativos)
        $ovosViaveisInativos = $totalOvosIncubadosInativos - $totalInferteisInativos;
        if ($ovosViaveisInativos < 0) { $ovosViaveisInativos = 0; } // Garante que não seja negativo

        $taxaEclosao = ($ovosViaveisInativos > 0) ? round(($totalEclodidosInativos / $ovosViaveisInativos) * 100, 2) : 0;
        // FIM DA CORREÇÃO

        $totalMachos = Ave::where('sexo', 'Macho')->where('ativo', 1)->count();
        $totalFemeas = Ave::where('sexo', 'Femea')->where('ativo', 1)->count();
        $totalAcasalamentosAtivos = \App\Models\Acasalamento::whereNull('data_fim')->count();
        $totalPosturasAtivas = PosturaOvo::where('encerrada', 0)->count();


        // --- MÉTRICAS E GRÁFICOS EXISTENTES ---

        // 5. Gráfico de Tendência de Mortes (Últimos 12 Meses)
        $mortesPorMes = Morte::select(
                                DB::raw('YEAR(data_morte) as ano'),
                                DB::raw('MONTH(data_morte) as mes'),
                                DB::raw('COUNT(*) as total_mortes')
                            )
                            ->where('data_morte', '>=', Carbon::now()->subMonths(12))
                            ->groupBy('ano', 'mes')
                            ->orderBy('ano')
                            ->orderBy('mes')
                            ->get();

        $labelsMortesMes = [];
        $dataMortesMes = [];
        foreach ($mortesPorMes as $item) {
            $labelsMortesMes[] = Carbon::create($item->ano, $item->mes)->format('M/Y');
            $dataMortesMes[] = $item->total_mortes;
        }

        // 6. Gráfico de Histórico de Eclosões (Últimos 12 Meses por Tipo de Ave)
        $eclosoesPorMesTipo = Incubacao::select(
                                DB::raw('YEAR(data_prevista_eclosao) as ano'),
                                DB::raw('MONTH(data_prevista_eclosao) as mes'),
                                'tipo_ave_id',
                                DB::raw('SUM(quantidade_eclodidos) as total_eclodidos')
                            )
                            ->whereNotNull('quantidade_eclodidos')
                            ->where('data_prevista_eclosao', '>=', Carbon::now()->subMonths(12))
                            ->groupBy('ano', 'mes', 'tipo_ave_id')
                            ->orderBy('ano')
                            ->orderBy('mes')
                            ->get();

        $labelsEclosoesMes = [];
        $dataEclosoesPorTipo = [];
        $tiposAvesNomes = TipoAve::pluck('nome', 'id')->toArray();

        $monthlyData = [];
        foreach ($eclosoesPorMesTipo as $item) {
            $monthYear = Carbon::create($item->ano, $item->mes)->format('Y-m');
            if (!in_array($monthYear, $labelsEclosoesMes)) {
                $labelsEclosoesMes[] = $monthYear;
            }
            $monthlyData[$monthYear][$item->tipo_ave_id] = $item->total_eclodidos;
        }

        sort($labelsEclosoesMes);
        $labelsEclosoesMesFormatted = array_map(function($ym) {
            return Carbon::parse($ym)->format('M/Y');
        }, $labelsEclosoesMes);

        // Gerar cores distintas para cada tipo de ave
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#E7E9ED',
            '#A1C9A9', '#F7B7A3', '#D0E0E3', '#C7CEEA', '#F5E6CC', '#B0C4DE', '#F08080',
            '#8A2BE2', '#7FFF00', '#D2B48C', '#4682B4', '#DA70D6', '#ADFF2F', '#F0E68C'
        ];
        $colorIndex = 0;

        foreach ($tiposAvesNomes as $id => $nome) {
            $data = [];
            foreach ($labelsEclosoesMes as $monthYear) {
                $data[] = $monthlyData[$monthYear][$id] ?? 0;
            }
            $dataEclosoesPorTipo[] = [
                'label' => $nome,
                'backgroundColor' => $colors[$colorIndex % count($colors)], // Usar cores predefinidas
                'borderColor' => $colors[$colorIndex % count($colors)],
                'borderWidth' => 1,
                'data' => $data
            ];
            $colorIndex++;
        }

        // 7. Gráfico de Contagem de Ovos Postos (Últimos 30 Dias - Diário)
        $ovosPostosDiario = PosturaOvo::select(
                                DB::raw('DATE(data_inicio_postura) as data'),
                                DB::raw('SUM(quantidade_ovos) as total_ovos')
                            )
                            ->where('data_inicio_postura', '>=', Carbon::now()->subDays(30))
                            ->groupBy('data')
                            ->orderBy('data')
                            ->get();

        $labelsOvosPostos = [];
        $dataOvosPostos = [];
        foreach ($ovosPostosDiario as $item) {
            $labelsOvosPostos[] = Carbon::parse($item->data)->format('d/m');
            $dataOvosPostos[] = $item->total_ovos;
        }

        // 8. Alertas e Notificações (Incubações)
        $alertas = [];
        // Incubações próximas da eclosão (próximos 5 dias)
        $incubacoesProximasEclosao = Incubacao::whereBetween('data_prevista_eclosao', [Carbon::now()->startOfDay(), Carbon::now()->addDays(5)->endOfDay()])
                                                ->where('ativo', 1)
                                                ->where(function($query) {
                                                    $query->whereNull('quantidade_eclodidos')
                                                          ->orWhere('quantidade_eclodidos', 0);
                                                })
                                                ->with('lote', 'tipoAve')
                                                ->get();

        foreach ($incubacoesProximasEclosao as $incubacao) {
            if (Carbon::parse($incubacao->data_prevista_eclosao)->isFuture()) {
                $alertas[] = [
                    'type' => 'warning',
                    'message' => 'Eclosão próxima! Lote: ' . ($incubacao->lote->identificacao_lote ?? 'N/A') . ', Tipo: ' . ($incubacao->tipoAve->nome ?? 'N/A') . ', Ovos: ' . $incubacao->quantidade_ovos . ' em ' . Carbon::parse($incubacao->data_prevista_eclosao)->format('d/m/Y') . '.',
                    'link' => route('incubacoes.show', $incubacao->id)
                ];
            }
        }

        // Incubações atrasadas (data_prevista_eclosao já passou e quantidade_eclodidos é null ou 0)
        $incubacoesAtrasadas = Incubacao::where('data_prevista_eclosao', '<', Carbon::now()->startOfDay())
                                         ->where(function($query) {
                                             $query->whereNull('quantidade_eclodidos')
                                                   ->orWhere('quantidade_eclodidos', 0);
                                         })
                                         ->where('ativo', 1)
                                         ->with('lote', 'tipoAve')
                                         ->get();

        foreach ($incubacoesAtrasadas as $incubacao) {
            $diasAtraso = Carbon::parse($incubacao->data_prevista_eclosao)->diffInDays(Carbon::now());
            $alertas[] = [
                'type' => 'danger',
                'message' => 'Incubação atrasada! Lote: ' . ($incubacao->lote->identificacao_lote ?? 'N/A') . ', Tipo: ' . ($incubacao->tipoAve->nome ?? 'N/A') . ', Previsão em: ' . Carbon::parse($incubacao->data_prevista_eclosao)->format('d/m/Y') . ' (Atraso: ' . $diasAtraso . ' dias).',
                'link' => route('incubacoes.show', $incubacao->id)
            ];
        }


        // 9. Filtros Dinâmicos para Incubações na Tabela Principal
        $selectedTipoAve = $request->query('tipo_ave_id');
        $selectedLote = $request->query('lote_id');

        $queryIncubacoes = Incubacao::where('ativo', 1)
                                     ->with('lote', 'tipoAve');

        if ($selectedTipoAve) {
            $queryIncubacoes->where('tipo_ave_id', $selectedTipoAve);
        }
        if ($selectedLote) {
            $queryIncubacoes->where('lote_ovos_id', $selectedLote);
        }

        $incubacoesParaTabela = $queryIncubacoes->orderBy('data_prevista_eclosao', 'asc')->get();

        $incubacoesData = $incubacoesParaTabela->map(function($incubacao) {
            $dataEntrada = Carbon::parse($incubacao->data_entrada_incubadora);
            $dataPrevistaEclosao = Carbon::parse($incubacao->data_prevista_eclosao);
            $agora = Carbon::now();

            // Lógica de Cálculo do Progresso Proporcional
            $progressPercentage = 0;
            // Duração total da incubação (em dias, sempre positiva)
            $totalDurationDays = $dataEntrada->diffInDays($dataPrevistaEclosao);

            // Dias que se passaram da incubação (a partir da data de entrada até AGORA)
            $daysPassed = $dataEntrada->diffInDays($agora, false);

            if ($daysPassed < 0) {
                $daysPassed = 0;
            }

            $daysPassed = min($daysPassed, $totalDurationDays);

            if ($totalDurationDays > 0) {
                $progressPercentage = ($daysPassed / $totalDurationDays) * 100;
            } else {
                if ($agora->gte($dataEntrada)) {
                    $progressPercentage = 100;
                }
            }

            $progressPercentage = round(max(0, min(100, $progressPercentage)));

            // Lógica de Determinação do Status
            $status = 'Em andamento';
            $diasParaEclosao = $agora->diffInDays($dataPrevistaEclosao, false);

            if ($dataPrevistaEclosao->isPast() && ($incubacao->quantidade_eclodidos !== null && $incubacao->quantidade_eclodidos > 0)) {
                $status = 'Concluído';
            } elseif ($dataPrevistaEclosao->isPast() && ($incubacao->quantidade_eclodidos === null || $incubacao->quantidade_eclodidos === 0)) {
                $status = 'Atrasado';
            } elseif ($agora->lt($dataEntrada)) {
               $status = 'Prevista';
               $progressPercentage = 0;
            } elseif ($diasParaEclosao >= 0 && $diasParaEclosao <= 5) {
                $status = 'Finalizando';
            }

            return [
                'id' => $incubacao->id,
                'lote_nome' => $incubacao->lote->identificacao_lote ?? 'N/A',
                'tipo_ave_nome' => $incubacao->tipoAve->nome ?? 'N/A',
                'data_entrada_incubadora' => $dataEntrada->format('d/m/Y'),
                'data_prevista_eclosao' => $dataPrevistaEclosao->format('d/m/Y'),
                'quantidade_ovos' => $incubacao->quantidade_ovos,
                'progress_percentage' => $progressPercentage,
                'status' => $status,
                'link_detalhes' => route('incubacoes.show', $incubacao->id)
            ];
        });

        // Listas para os filtros
        $tiposAves = TipoAve::orderBy('nome')->get();
        $lotes = Lote::orderBy('identificacao_lote')->get();

        // Dados para os gráficos de Incubação (movidos para cá)
        $dadosTaxaEclosaoMensal = $this->getDadosTaxaEclosaoMensal(); // Chamada para o novo método mensal
        $dadosOvosNaoEclodidosMensal = $this->getDadosOvosNaoEclodidosMensal();
        $dadosDesempenhoChocadeira = $this->getDadosDesempenhoChocadeira();

        return view('dashboard', compact(
            'totalAvesAtivas',
            'mortesUltimos30Dias',
            'labelsAvesPorTipo',
            'dataAvesPorTipo',
            'totalIncubacoesAtivas',
            'taxaEclosao', // Agora reflete apenas inativas
            'labelsMortesMes',
            'dataMortesMes',
            'labelsEclosoesMesFormatted',
            'dataEclosoesPorTipo',
            'labelsOvosPostos',
            'dataOvosPostos',
            'alertas',
            'incubacoesData',
            'tiposAves',
            'lotes',
            'selectedTipoAve',
            'selectedLote',
            'totalMachos',
            'totalFemeas',
            'totalAcasalamentosAtivos',
            'totalPosturasAtivas',
            'dadosTaxaEclosaoMensal',
            'dadosOvosNaoEclodidosMensal',
            'dadosDesempenhoChocadeira'
        ));
    }

    /**
     * Retorna os dados para o gráfico de Taxa de Eclosão Mensal.
     * Agora retorna dados mensais para um gráfico de linha.
     * Considera apenas incubações inativas para o cálculo da taxa.
     */
    private function getDadosTaxaEclosaoMensal()
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
                            ->where('ativo', false) // APENAS INATIVAS PARA ESTE GRÁFICO TAMBÉM
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


        // Gerar todos os meses para os últimos 12 meses para garantir a continuidade
        $currentMonth = Carbon::now()->subMonths(11)->startOfMonth(); // Começa 11 meses atrás para ter 12 meses no total
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
                    'borderColor' => '#28a745', // Verde
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'fill' => true,
                    'data' => $dataTaxaEclosao,
                ],
                [
                    'label' => 'Taxa de Não Eclosão (Viáveis)',
                    'borderColor' => '#dc3545', // Vermelho
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)', // Vermelho claro para preenchimento
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
     * Este gráfico continua focando nas perdas de ovos viáveis.
     * Considera apenas incubações inativas.
     */
    private function getDadosOvosNaoEclodidosMensal()
    {
        $dataLimite12Meses = Carbon::now()->subMonths(12);

        $perdasPorMes = Incubacao::select(
                                DB::raw('YEAR(data_entrada_incubadora) as ano'),
                                DB::raw('MONTH(data_entrada_incubadora) as mes'),
                                DB::raw('SUM(quantidade_infectados) as total_infectados'),
                                DB::raw('SUM(quantidade_mortos) as total_mortos')
                            )
                            ->where('data_entrada_incubadora', '>=', $dataLimite12Meses)
                            ->where('ativo', false) // APENAS INATIVAS PARA ESTE GRÁFICO TAMBÉM
                            ->groupBy('ano', 'mes')
                            ->orderBy('ano')
                            ->orderBy('mes')
                            ->get();

        $labels = [];
        $dataInfectados = [];
        $dataMortos = [];

        // Gerar todos os meses para os últimos 12 meses para garantir a continuidade
        $currentMonth = Carbon::now()->subMonths(11)->startOfMonth(); // Começa 11 meses atrás para ter 12 meses no total
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
                    'borderColor' => '#dc3545', // Vermelho
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)', // Vermelho claro para preenchimento
                    'fill' => true,
                    'data' => $dataInfectados,
                ],
                [
                    'label' => 'Ovos Mortos',
                    'borderColor' => '#6c757d', // Cinza
                    'backgroundColor' => 'rgba(108, 117, 125, 0.2)', // Cinza claro para preenchimento
                    'fill' => true,
                    'data' => $dataMortos,
                ],
            ],
            'metrics' => [ // Manter métricas se necessário em outro lugar
                'total_infectados' => Incubacao::sum('quantidade_infectados'),
                'total_mortos' => Incubacao::sum('quantidade_mortos'),
            ]
        ];
    }

    /**
     * Retorna os dados para o desempenho por chocadeira.
     * Considera apenas incubações inativas.
     */
    private function getDadosDesempenhoChocadeira()
    {
        $desempenho = Incubacao::selectRaw('chocadeira, SUM(quantidade_ovos) as total_ovos, SUM(quantidade_eclodidos) as total_eclodidos, SUM(quantidade_inferteis) as total_inferteis')
                                ->where('ativo', false) // APENAS INATIVAS PARA ESTE GRÁFICO TAMBÉM
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
                    'backgroundColor' => 'rgba(0, 123, 255, 0.7)', // Azul
                    'data' => $totalOvosPorChocadeira,
                ],
                [
                    'label' => 'Ovos Eclodidos',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.7)', // Verde
                    'data' => $totalEclodidosPorChocadeira,
                ],
            ],
            'taxas_eclosao' => $taxasEclosao, // Para exibir a taxa de eclosão em algum lugar
        ];
    }
}
