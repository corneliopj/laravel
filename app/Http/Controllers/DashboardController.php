<?php

namespace App\Http\Controllers;

use App\Models\Ave;
use App\Models\Incubacao;
use App\Models\Morte; // CORRIGIDO: De App->Models->Morte para App\Models\Morte
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
        // Define o período padrão: últimos 30 dias ou o período especificado pelo usuário
        $dataInicioPadrao = Carbon::now()->subDays(30)->startOfDay();
        $dataFimPadrao = Carbon::now()->endOfDay();

        $dataInicio = $request->input('data_inicio') ? Carbon::parse($request->input('data_inicio'))->startOfDay() : $dataInicioPadrao;
        $dataFim = $request->input('data_fim') ? Carbon::parse($request->input('data_fim'))->endOfDay() : $dataFimPadrao;

        // 1. Quantidade Total de Aves Ativas (KPI)
        $totalAvesAtivas = Ave::where('ativo', 1)->count();

        // 2. Mortes no Período Selecionado (KPI)
        $mortesNoPeriodo = Morte::whereBetween('data_morte', [$dataInicio, $dataFim])->count();

        // 3. Ovos Postos no Período Selecionado (KPI)
        $ovosPostosNoPeriodo = PosturaOvo::whereBetween('data_inicio_postura', [$dataInicio, $dataFim])->sum('quantidade_ovos');

        // 4. Aves por Tipo (para Gráfico de Pizza)
        $avesPorTipo = Ave::select('tipo_ave_id')
                            ->selectRaw('count(*) as total')
                            ->where('ativo', 1)
                            ->groupBy('tipo_ave_id')
                            ->with('tipoAve')
                            ->get();

        $labelsAvesPorTipo = $avesPorTipo->map(function($item) {
            return $item->tipoAve->nome ?? 'Desconhecido';
        })->toArray();

        $dadosAvesPorTipo = $avesPorTipo->map(function($item) {
            return $item->total;
        })->toArray();

        // 5. Tendência de Eclosão (Gráfico de Linha) - Ovos eclodidos por semana (60 dias)
        $dataInicio60Dias = Carbon::now()->subDays(60)->startOfDay();
        $dataFimAtual = Carbon::now()->endOfDay();

        $tendenciaEclosao = Incubacao::select(
                                DB::raw('YEAR(data_prevista_eclosao) as ano'),
                                DB::raw('WEEK(data_prevista_eclosao, 1) as semana'), // 1 = semana começa no domingo
                                DB::raw('SUM(quantidade_eclodidos) as total_eclodidos')
                            )
                            ->whereNotNull('data_prevista_eclosao')
                            ->whereBetween('data_prevista_eclosao', [$dataInicio60Dias, $dataFimAtual])
                            ->groupBy('ano', 'semana')
                            ->orderBy('ano')
                            ->orderBy('semana')
                            ->get();

        $labelsTendenciaEclosao = [];
        $dadosTendenciaEclosao = [];

        // Preencher semanas vazias para um gráfico contínuo
        $currentWeek = Carbon::parse($dataInicio60Dias);
        while ($currentWeek->lessThanOrEqualTo($dataFimAtual)) {
            $year = $currentWeek->year;
            $week = $currentWeek->weekOfYear; // weekOfYear é mais comum para FullCalendar
            $label = "Semana {$week} / {$year}";
            $labelsTendenciaEclosao[] = $label;

            $found = false;
            foreach ($tendenciaEclosao as $item) {
                if ($item->ano == $year && $item->semana == $week) {
                    $dadosTendenciaEclosao[] = $item->total_eclodidos;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $dadosTendenciaEclosao[] = 0; // Adiciona 0 se não houver dados para a semana
            }
            $currentWeek->addWeek();
        }


        // 6. Desempenho de Incubação por Chocadeira (Gráfico de Barras)
        // Foco na Taxa de Eclosão Percentual
        $desempenhoChocadeira = Incubacao::select(
                                    'chocadeira',
                                    DB::raw('SUM(quantidade_ovos) as total_ovos'),
                                    DB::raw('SUM(quantidade_eclodidos) as total_eclodidos'),
                                    DB::raw('SUM(quantidade_inferteis) as total_inferteis')
                                )
                                ->groupBy('chocadeira')
                                ->get();

        $labelsChocadeira = [];
        $totalOvosPorChocadeira = [];
        $totalEclodidosPorChocadeira = [];
        $taxasEclosaoChocadeira = []; // Array para as taxas de eclosão percentuais

        foreach ($desempenhoChocadeira as $item) {
            $chocadeiraNome = $item->chocadeira ?? 'Não Definida';
            $labelsChocadeira[] = $chocadeiraNome;
            $totalOvosPorChocadeira[] = $item->total_ovos;
            $totalEclodidosPorChocadeira[] = $item->total_eclodidos;

            $ovosViaveis = $item->total_ovos - $item->total_inferteis;
            $taxaEclosao = ($ovosViaveis > 0) ? round(($item->total_eclodidos / $ovosViaveis) * 100, 2) : 0;
            $taxasEclosaoChocadeira[] = $taxaEclosao;
        }

        // Calcula a Taxa de Eclosão Média Geral para o KPI
        $totalOvosGeral = array_sum($totalOvosPorChocadeira);
        $totalEclodidosGeral = array_sum($totalEclodidosPorChocadeira);
        $totalInferteisGeral = array_sum($desempenhoChocadeira->pluck('total_inferteis')->toArray());
        $ovosViaveisGeral = $totalOvosGeral - $totalInferteisGeral;
        $taxaEclosaoMediaGeral = ($ovosViaveisGeral > 0) ? round(($totalEclodidosGeral / $ovosViaveisGeral) * 100, 2) : 0;


        // 7. Incubações Ativas (para a Tabela)
        $incubacoesData = Incubacao::where('ativo', true)
                                    ->with('lote', 'tipoAve')
                                    ->get()
                                    ->map(function ($incubacao) {
                                        $diasPassados = ($incubacao->data_inicio && Carbon::now()->greaterThanOrEqualTo($incubacao->data_inicio))
                                            ? Carbon::parse($incubacao->data_inicio)->diffInDays(Carbon::now())
                                            : 0;
                                        $duracaoTotal = ($incubacao->data_inicio && $incubacao->data_prevista_eclosao)
                                            ? Carbon::parse($incubacao->data_inicio)->diffInDays($incubacao->data_prevista_eclosao)
                                            : 0;

                                        $progressPercentage = ($duracaoTotal > 0) ? round(($diasPassados / $duracaoTotal) * 100, 2) : 0;

                                        $status = 'Em andamento';
                                        if ($incubacao->data_prevista_eclosao && Carbon::now()->greaterThan($incubacao->data_prevista_eclosao) && $incubacao->ativo) {
                                            $status = 'Atrasado';
                                        }
                                        if (!$incubacao->ativo && $incubacao->quantidade_eclodidos > 0) {
                                            $status = 'Concluído';
                                        }
                                        if ($incubacao->data_prevista_eclosao && Carbon::now()->diffInDays($incubacao->data_prevista_eclosao, false) <= 3 && Carbon::now()->lessThanOrEqualTo($incubacao->data_prevista_eclosao) && $incubacao->ativo) {
                                            $status = 'Finalizando';
                                        }

                                        return [
                                            'id' => $incubacao->id,
                                            'lote_nome' => $incubacao->lote->nome ?? 'N/A',
                                            'tipo_ave_nome' => $incubacao->tipoAve->nome ?? 'N/A',
                                            'chocadeira' => $incubacao->chocadeira,
                                            'quantidade_ovos' => $incubacao->quantidade_ovos,
                                            'data_inicio' => $incubacao->data_inicio ? $incubacao->data_inicio->format('d/m/Y') : 'N/A', // Verificação de nulo
                                            'data_prevista_eclosao' => $incubacao->data_prevista_eclosao ? $incubacao->data_prevista_eclosao->format('d/m/Y') : 'N/A', // Verificação de nulo
                                            'progress_percentage' => min(100, max(0, $progressPercentage)), // Garante que o progresso esteja entre 0 e 100
                                            'status' => $status,
                                            'link_detalhes' => route('incubacoes.show', $incubacao->id), // Exemplo de link
                                        ];
                                    });

        // 8. Eventos para o Calendário (FullCalendar) - Próximos 30 dias
        $calendarEvents = [];

        // Adiciona Próximas Eclosões como eventos
        $proximasEclosoes = Incubacao::where('data_prevista_eclosao', '>=', Carbon::now()->startOfDay())
                                    ->where('data_prevista_eclosao', '<=', Carbon::now()->addDays(30)->endOfDay())
                                    ->where('ativo', true)
                                    ->orderBy('data_prevista_eclosao', 'asc')
                                    ->get();

        foreach ($proximasEclosoes as $eclosao) {
            if ($eclosao->data_prevista_eclosao) { // Verificação de nulo
                $calendarEvents[] = [
                    'title' => 'Eclosão: Choc. ' . $eclosao->chocadeira . ' (' . $eclosao->quantidade_ovos . ' ovos)',
                    'start' => $eclosao->data_prevista_eclosao->format('Y-m-d'),
                    'color' => '#28a745', // Verde para eclosões
                    'url' => route('incubacoes.show', $eclosao->id), // Link para detalhes da incubação
                ];
            }
        }

        // Adiciona Próximos Acasalamentos como eventos
        $proximosAcasalamentos = DB::table('acasalamentos')
                                    ->where('data_inicio', '>=', Carbon::now()->startOfDay())
                                    ->where('data_inicio', '<=', Carbon::now()->addDays(30)->endOfDay())
                                    ->orderBy('data_inicio', 'asc')
                                    ->get();

        foreach ($proximosAcasalamentos as $acasalamento) {
            if ($acasalamento->data_inicio) { // Verificação de nulo
                $calendarEvents[] = [
                    'title' => 'Acasalamento: Macho ' . ($acasalamento->macho_id ?? 'N/A') . ' / Fêmea ' . ($acasalamento->femea_id ?? 'N/A'),
                    'start' => Carbon::parse($acasalamento->data_inicio)->format('Y-m-d'),
                    'color' => '#007bff', // Azul para acasalamentos
                    // 'url' => route('acasalamentos.show', $acasalamento->id), // Se houver rota de detalhes
                ];
            }
        }

        // 9. Alertas Dinâmicos (Exemplos)
        $alertas = [];

        // Alerta: Incubações próximas do fim (nos próximos 7 dias)
        $incubacoesProximas = Incubacao::where('data_prevista_eclosao', '>=', Carbon::now()->startOfDay())
                                        ->where('data_prevista_eclosao', '<=', Carbon::now()->addDays(7)->endOfDay())
                                        ->where('ativo', true) // Apenas incubações ativas
                                        ->get();
        if ($incubacoesProximas->count() > 0) {
            foreach ($incubacoesProximas as $incubacao) {
                if ($incubacao->data_prevista_eclosao) { // Verificação de nulo
                    $diasRestantes = Carbon::now()->diffInDays($incubacao->data_prevista_eclosao, false);
                    $alertas[] = [
                        'type' => 'warning',
                        'message' => "A incubação na chocadeira '{$incubacao->chocadeira}' (ID: {$incubacao->id}) tem eclosão prevista em {$incubacao->data_prevista_eclosao->format('d/m/Y')}. Faltam {$diasRestantes} dias.",
                    ];
                }
            }
        }

        return view('dashboard', compact(
            'totalAvesAtivas',
            'mortesNoPeriodo',
            'ovosPostosNoPeriodo',
            'taxaEclosaoMediaGeral', // Novo KPI
            'labelsAvesPorTipo',
            'dadosAvesPorTipo',
            'labelsTendenciaEclosao',
            'dadosTendenciaEclosao',
            'labelsChocadeira',
            'totalOvosPorChocadeira',
            'totalEclodidosPorChocadeira',
            'taxasEclosaoChocadeira', // Taxas percentuais por chocadeira
            'incubacoesData', // Tabela de incubações ativas
            'calendarEvents', // Eventos para o FullCalendar
            'alertas',
            'dataInicio',
            'dataFim'
        ));
    }
}
