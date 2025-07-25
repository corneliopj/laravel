<?php

namespace App\Http\Controllers;

use App\Models\Ave;
use App\Models\Incubacao;
use App\Models\Morte;
use App\Models\TipoAve;
use App\Models\Lote;
use App\Models\PosturaOvo;
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
        $totalOvosIncubados = Incubacao::sum('quantidade_ovos');
        $totalEclodidos = Incubacao::sum('quantidade_eclodidos');
        $taxaEclosao = ($totalOvosIncubados > 0) ? round(($totalEclodidos / $totalOvosIncubados) * 100, 2) : 0;

        // --- MÉTRICAS E GRÁFICOS ---

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

        foreach ($tiposAvesNomes as $id => $nome) {
            $data = [];
            foreach ($labelsEclosoesMes as $monthYear) {
                $data[] = $monthlyData[$monthYear][$id] ?? 0;
            }
            $dataEclosoesPorTipo[] = [
                'label' => $nome,
                'data' => $data
            ];
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
            // Usamos diffInDays(false) para obter a diferença com sinal, para garantir que daysPassed não seja negativo
            // se $agora for anterior a $dataEntrada.
            $daysPassed = $dataEntrada->diffInDays($agora, false);

            // Se daysPassed for negativo, significa que a incubação ainda não começou (agora < dataEntrada)
            if ($daysPassed < 0) {
                $daysPassed = 0;
            }

            // Limitar daysPassed para não exceder a duração total (ou seja, não ir além de 100%)
            $daysPassed = min($daysPassed, $totalDurationDays);


            if ($totalDurationDays > 0) {
                $progressPercentage = ($daysPassed / $totalDurationDays) * 100;
            } else {
                // Se a duração total for 0 (início e fim no mesmo dia), se já passou, 100%, senão 0%
                if ($agora->gte($dataEntrada)) {
                    $progressPercentage = 100;
                }
            }

            // Garante que a percentagem esteja entre 0 e 100
            $progressPercentage = round(max(0, min(100, $progressPercentage)));

            // --- BLOCO DE DEPURACÃO ---
            // Descomente o bloco abaixo para depurar a primeira incubação processada.
            // Isso irá parar a execução e mostrar os valores das variáveis para você.
            // Após a depuração, lembre-se de COMENTAR ou REMOVER este bloco.
          /**  
            if ($incubacao->id === $incubacoesParaTabela->first()->id) { // Depura apenas a primeira incubação
                dd([
                    'ID da Incubação' => $incubacao->id,
                    'Data Entrada DB' => $incubacao->data_entrada_incubadora,
                    'Data Previsão DB' => $incubacao->data_prevista_eclosao,
                    'Carbon::now()' => $agora->format('Y-m-d H:i:s'),
                    'dataEntrada (Parsed)' => $dataEntrada->format('Y-m-d H:i:s'),
                    'dataPrevistaEclosao (Parsed)' => $dataPrevistaEclosao->format('Y-m-d H:i:s'),
                    'totalDurationDays (Total de Dias)' => $totalDurationDays,
                    'daysPassed (Dias Passados Calculados)' => $daysPassed,
                    'progressPercentage (Calculado)' => $progressPercentage,
                    'Status Previsto' => $status, // O status será calculado depois deste dd, mas podemos verificar a lógica
                ]);
            }
         */   
            // --- FIM DO BLOCO DE DEPURACÃO ---


            // Lógica de Determinação do Status
            $status = 'Em andamento';
            $diasParaEclosao = $agora->diffInDays($dataPrevistaEclosao, false); // Diferença de dias com sinal

            if ($dataPrevistaEclosao->isPast() && ($incubacao->quantidade_eclodidos !== null && $incubacao->quantidade_eclodidos > 0)) {
                $status = 'Concluído'; // Previsão passou e já eclodiu algo
            } elseif ($dataPrevistaEclosao->isPast() && ($incubacao->quantidade_eclodidos === null || $incubacao->quantidade_eclodidos === 0)) {
                $status = 'Atrasado'; // Previsão passou e nada eclodiu
            } elseif ($agora->lt($dataEntrada)) { // Se a data atual é antes do início da incubação
                 $status = 'Prevista'; // Incubação ainda não começou
                 $progressPercentage = 0; // Se não começou, progresso é 0
            } elseif ($diasParaEclosao >= 0 && $diasParaEclosao <= 5) {
                $status = 'Finalizando';
            }
            // Para as demais, permanece 'Em andamento'

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

        return view('dashboard.index', compact(
            'totalAvesAtivas',
            'mortesUltimos30Dias',
            'labelsAvesPorTipo',
            'dataAvesPorTipo',
            'totalIncubacoesAtivas',
            'totalOvosIncubados',
            'totalEclodidos',
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
            'selectedLote'
        ));
    }
}
