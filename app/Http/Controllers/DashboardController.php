<?php

namespace App\Http\Controllers;

use App\Models\Ave;
use App\Models\Incubacao;
use App\Models\Morte;
use App\Models\TipoAve;
use App\Models\Lote;
use App\Models\PosturaOvo;
use App\Models\Venda;
use App\Models\Plantel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Exibe o dashboard com um resumo dos dados e o quadro de incubações.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Quantidade Total de Aves Ativas (Individuais)
        $totalAvesAtivas = Ave::where('ativo', 1)->count();

        // Calcular KPIs de performance com Cache
        $kpis = $this->dashboardService->calcularKPIs();
        
        // Obter previsões de eclosão
        $previsoesEclosao = $this->dashboardService->obterPrevisoesEclosao();

        // 2. Quantidade Total de Aves em Plantéis Agrupados Ativos
        $totalAvesEmPlantelAtivas = Plantel::where('ativo', true)->sum('quantidade_atual');

        // 3. KPI Total Geral de Aves (Suma de individuais e plantéis)
        $totalGeralAves = $totalAvesAtivas + $totalAvesEmPlantelAtivas;

        // 4. Mortes nos Últimos 30 Dias
        $dataLimite30Dias = Carbon::now()->subDays(30);
        $mortesUltimos30Dias = Morte::where('data_morte', '>=', $dataLimite30Dias)->count();

        // 5. Aves por Tipo (para Gráfico de Pizza)
        $avesPorTipo = Ave::select('tipo_ave_id')
                            ->selectRaw('count(*) as total')
                            ->where('ativo', 1)
                            ->groupBy('tipo_ave_id')
                            ->with('tipoAve:id,nome')
                            ->get();

        $labelsAvesPorTipo = $avesPorTipo->map(function($item) {
            return $item->tipoAve->nome ?? 'Desconhecido';
        });
        $dataAvesPorTipo = $avesPorTipo->map(function($item) {
            return $item->total;
        });

        // 6. Resumo de Incubações Globais
        $totalIncubacoesAtivas = Incubacao::where('ativo', 1)->count();

        // Cálculo da Taxa de Eclosão Global - APENAS COM INCUBACÕES INATIVAS
        $totalOvosIncubadosInativos = Incubacao::where('ativo', false)->sum('quantidade_ovos');
        $totalEclodidosInativos = Incubacao::where('ativo', false)->sum('quantidade_eclodidos');
        $totalInferteisInativos = Incubacao::where('ativo', false)->sum('quantidade_inferteis');

        $ovosViaveisInativos = $totalOvosIncubadosInativos - $totalInferteisInativos;
        if ($ovosViaveisInativos < 0) { $ovosViaveisInativos = 0; }

        $taxaEclosao = ($ovosViaveisInativos > 0) ? round(($totalEclodidosInativos / $ovosViaveisInativos) * 100, 2) : 0;

        $totalMachos = Ave::where('sexo', 'Macho')->where('ativo', 1)->count();
        $totalFemeas = Ave::where('sexo', 'Femea')->where('ativo', 1)->count();
        $totalAcasalamentosAtivos = \App\Models\Acasalamento::whereNull('data_fim')->count();
        $totalPosturasAtivas = PosturaOvo::where('encerrada', 0)->count();

        // 7. Gráfico de Tendência de Mortes (Últimos 12 Meses)
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

        // 8. Gráfico de Histórico de Eclosões (Últimos 12 Meses por Tipo de Ave)
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
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor' => $colors[$colorIndex % count($colors)],
                'borderWidth' => 1,
                'data' => $data
            ];
            $colorIndex++;
        }

        // 9. Gráfico de Contagem de Ovos Postos (Últimos 30 Dias - Diário)
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

        // 10. Alertas e Notificações (Incubações)
        $alertas = [];
        $incubacoesProximasEclosao = Incubacao::whereBetween('data_prevista_eclosao', [Carbon::now()->startOfDay(), Carbon::now()->addDays(5)->endOfDay()])
                                                ->where('ativo', 1)
                                                ->where(function($query) {
                                                    $query->whereNull('quantidade_eclodidos')
                                                          ->orWhere('quantidade_eclodidos', 0);
                                                })
                                                ->with(['lote:id,identificacao_lote', 'tipoAve:id,nome'])
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

        $incubacoesAtrasadas = Incubacao::where('data_prevista_eclosao', '<', Carbon::now()->startOfDay())
                                          ->where(function($query) {
                                              $query->whereNull('quantidade_eclodidos')
                                                    ->orWhere('quantidade_eclodidos', 0);
                                          })
                                          ->where('ativo', 1)
                                          ->with(['lote:id,identificacao_lote', 'tipoAve:id,nome'])
                                          ->get();

        foreach ($incubacoesAtrasadas as $incubacao) {
            $diasAtraso = Carbon::parse($incubacao->data_prevista_eclosao)->diffInDays(Carbon::now());
            $alertas[] = [
                'type' => 'danger',
                'message' => 'Incubação atrasada! Lote: ' . ($incubacao->lote->identificacao_lote ?? 'N/A') . ', Tipo: ' . ($incubacao->tipoAve->nome ?? 'N/A') . ', Previsão em: ' . Carbon::parse($incubacao->data_prevista_eclosao)->format('d/m/Y') . ' (Atraso: ' . $diasAtraso . ' dias).',
                'link' => route('incubacoes.show', $incubacao->id)
            ];
        }

        // 11. Filtros Dinâmicos para Incubações na Tabela Principal
        $selectedTipoAve = $request->query('tipo_ave_id');
        $selectedLote = $request->query('lote_id');

        $queryIncubacoes = Incubacao::where('ativo', 1)
                                      ->with(['lote:id,identificacao_lote', 'tipoAve:id,nome']);

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

            $totalDurationDays = $dataEntrada->diffInDays($dataPrevistaEclosao);
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

        $tiposAves = TipoAve::orderBy('nome')->get();
        $lotes = Lote::orderBy('identificacao_lote')->get();

        $dadosTaxaEclosaoMensal = $this->dashboardService->getDadosTaxaEclosaoMensal();
        $dadosOvosNaoEclodidosMensal = $this->dashboardService->getDadosOvosNaoEclodidosMensal();
        $dadosDesempenhoChocadeira = $this->dashboardService->getDadosDesempenhoChocadeira();
		$ano = $request->input('ano', Carbon::now()->year);
		$trimestre = $request->input('trimestre', null);
		$mes = $request->input('mes', null);

        return view('dashboard', compact(
            'totalAvesAtivas',
            'totalGeralAves',
            'mortesUltimos30Dias',
            'labelsAvesPorTipo',
            'dataAvesPorTipo',
            'totalIncubacoesAtivas',
            'taxaEclosao',
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
            'dadosDesempenhoChocadeira',
            'kpis',
			'ano',
			'trimestre',
			'mes',
            'previsoesEclosao'
        ));
    }
}
