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
        // CORRIGIDO: Usando 'data_inicio_postura' e 'quantidade_ovos'
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

        // 5. Tendência de Eclosão (Gráfico de Linha) - Ovos eclodidos por mês
        // CORRIGIDO: Usando 'data_prevista_eclosao'
        $tendenciaEclosao = Incubacao::select(
                                DB::raw('DATE_FORMAT(data_prevista_eclosao, "%Y-%m") as mes_ano'),
                                DB::raw('SUM(quantidade_eclodidos) as total_eclodidos')
                            )
                            ->whereNotNull('data_prevista_eclosao')
                            ->whereBetween('data_prevista_eclosao', [$dataInicio, $dataFim])
                            ->groupBy('mes_ano')
                            ->orderBy('mes_ano')
                            ->get();

        $labelsTendenciaEclosao = $tendenciaEclosao->pluck('mes_ano')->toArray();
        $dadosTendenciaEclosao = $tendenciaEclosao->pluck('total_eclodidos')->toArray();

        // 6. Desempenho de Incubação por Chocadeira (Gráfico de Barras)
        // Este gráfico deve considerar incubações ativas e inativas para um panorama completo
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
        $taxasEclosaoChocadeira = [];

        foreach ($desempenhoChocadeira as $item) {
            $chocadeiraNome = $item->chocadeira ?? 'Não Definida';
            $labelsChocadeira[] = $chocadeiraNome;
            $totalOvosPorChocadeira[] = $item->total_ovos;
            $totalEclodidosPorChocadeira[] = $item->total_eclodidos;

            $ovosViaveis = $item->total_ovos - $item->total_inferteis;
            $taxaEclosao = ($ovosViaveis > 0) ? round(($item->total_eclodidos / $ovosViaveis) * 100, 2) : 0;
            $taxasEclosaoChocadeira[] = $taxaEclosao;
        }

        // 7. Próximas Eclosões (para o Calendário/Lista) - Próximos 30 dias
        // CORRIGIDO: Usando 'data_prevista_eclosao'
        $proximasEclosoes = Incubacao::where('data_prevista_eclosao', '>=', Carbon::now()->startOfDay())
                                    ->where('data_prevista_eclosao', '<=', Carbon::now()->addDays(30)->endOfDay())
                                    ->orderBy('data_prevista_eclosao', 'asc')
                                    ->get();

        // 8. Próximos Acasalamentos (para o Calendário/Lista) - Próximos 30 dias
        // A coluna de data no acasalamentos é 'data_inicio'
        $proximosAcasalamentos = DB::table('acasalamentos')
                                    ->where('data_inicio', '>=', Carbon::now()->startOfDay())
                                    ->where('data_inicio', '<=', Carbon::now()->addDays(30)->endOfDay())
                                    ->orderBy('data_inicio', 'asc')
                                    ->get();

        // 9. Alertas Dinâmicos (Exemplos)
        $alertas = [];

        // Alerta: Incubações próximas do fim (nos próximos 7 dias)
        // CORRIGIDO: Usando 'data_prevista_eclosao'
        $incubacoesProximas = Incubacao::where('data_prevista_eclosao', '>=', Carbon::now()->startOfDay())
                                        ->where('data_prevista_eclosao', '<=', Carbon::now()->addDays(7)->endOfDay())
                                        ->where('ativo', true) // Apenas incubações ativas
                                        ->get();
        if ($incubacoesProximas->count() > 0) {
            foreach ($incubacoesProximas as $incubacao) {
                // CORRIGIDO: Usando 'data_prevista_eclosao'
                $diasRestantes = Carbon::now()->diffInDays($incubacao->data_prevista_eclosao, false);
                $alertas[] = [
                    'type' => 'warning',
                    'message' => "A incubação na chocadeira '{$incubacao->chocadeira}' (ID: {$incubacao->id}) tem eclosão prevista em {$incubacao->data_prevista_eclosao->format('d/m/Y')}. Faltam {$diasRestantes} dias.",
                ];
            }
        }

        // Alerta: Aves sem acasalamento recente (ex: nos últimos 60 dias para aves reprodutoras)
        // Isso exigiria um campo 'data_ultimo_acasalamento' ou uma consulta mais complexa
        // Por simplicidade, vamos apenas adicionar um alerta de exemplo aqui.
        // Você precisaria de lógica para identificar "aves reprodutoras" e sua última atividade.
        // Exemplo:
        // $avesSemAcasalamento = Ave::where('tipo_ave_id', TipoAve::where('nome', 'Reprodutora')->first()->id)
        //                             ->whereDoesntHave('acasalamentos', function ($query) {
        //                                 $query->where('data_inicio', '>=', Carbon::now()->subDays(60));
        //                             })->get();
        // if ($avesSemAcasalamento->count() > 0) {
        //     $alertas[] = [
        //         'type' => 'info',
        //         'message' => "Existem {$avesSemAcasalamento->count()} aves reprodutoras sem acasalamento nos últimos 60 dias.",
        //     ];
        // }


        return view('dashboard', compact(
            'totalAvesAtivas',
            'mortesNoPeriodo',
            'ovosPostosNoPeriodo',
            'labelsAvesPorTipo',
            'dadosAvesPorTipo',
            'labelsTendenciaEclosao',
            'dadosTendenciaEclosao',
            'labelsChocadeira',
            'totalOvosPorChocadeira',
            'totalEclodidosPorChocadeira',
            'taxasEclosaoChocadeira',
            'proximasEclosoes',
            'proximosAcasalamentos',
            'alertas',
            'dataInicio', // Passa as datas para a view para preencher o datepicker
            'dataFim'
        ));
    }
}
