@php
    $pageTitle = 'Dashboard Principal';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard Principal</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                {{-- Filtros Dinâmicos --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-filter"></i>
                                    Filtros de Período
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body" style="display: none;">
                                <form method="GET" action="{{ route('dashboard') }}" id="filtros-form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ano">Ano:</label>
                                                <select name="ano" id="ano" class="form-control">
                                                    @for ($y = Carbon\Carbon::now()->year; $y >= 2020; $y--)
                                                        <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="trimestre">Trimestre:</label>
                                                <select name="trimestre" id="trimestre" class="form-control">
                                                    <option value="">Todos</option>
                                                    <option value="1" {{ $trimestre == '1' ? 'selected' : '' }}>1º Trimestre</option>
                                                    <option value="2" {{ $trimestre == '2' ? 'selected' : '' }}>2º Trimestre</option>
                                                    <option value="3" {{ $trimestre == '3' ? 'selected' : '' }}>3º Trimestre</option>
                                                    <option value="4" {{ $trimestre == '4' ? 'selected' : '' }}>4º Trimestre</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mes">Mês:</label>
                                                <select name="mes" id="mes" class="form-control">
                                                    <option value="">Todos</option>
                                                    @for ($m = 1; $m <= 12; $m++)
                                                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                                                            {{ Carbon\Carbon::create(null, $m, 1)->monthName }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-sync-alt"></i> Atualizar
                                                </button>
                                                <button type="button" class="btn btn-secondary ml-2" onclick="limparFiltros()">
                                                    <i class="fas fa-eraser"></i> Limpar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    {{-- NOVO: Total Geral de Aves (Individuais + Plantéis) --}}
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalGeralAves }}</h3>
                                <p>Total Geral de Aves</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-paw"></i>
                            </div>
                            <a href="#" class="small-box-footer">Visão Geral <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->

                    {{-- Manter o original de Aves Ativas (individuais) se desejar --}}
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $totalAvesAtivas }}</h3>
                                <p>Aves Individuais Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dove"></i>
                            </div>
                            <a href="{{ route('aves.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->

                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $totalMachos }}</h3>
                                <p>Machos Ativos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-mars"></i>
                            </div>
                            <a href="{{ route('aves.index', ['sexo' => 'Macho']) }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-pink">
                            <div class="inner">
                                <h3>{{ $totalFemeas }}</h3>
                                <p>Fêmeas Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-venus"></i>
                            </div>
                            <a href="{{ route('aves.index', ['sexo' => 'Femea']) }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalAcasalamentosAtivos }}</h3>
                                <p>Acasalamentos Ativos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <a href="{{ route('acasalamentos.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalPosturasAtivas }}</h3>
                                <p>Posturas de Ovos Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-egg"></i>
                            </div>
                            <a href="{{ route('posturas_ovos.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalIncubacoesAtivas }}</h3>
                                <p>Incubações Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-thermometer-half"></i>
                            </div>
                            <a href="{{ route('incubacoes.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $mortesUltimos30Dias }}</h3>
                                <p>Mortes Últimos 30 Dias</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($taxaEclosao, 2) }}<sup style="font-size: 20px">%</sup></h3>
                                <p>Taxa de Eclosão Global</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <a href="{{ route('incubacoes.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                {{-- Seção KPIs Visuais --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Indicadores de Performance (KPIs)
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeTaxaEclosao" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeTaxaFertilidade" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeMelhorChocadeira" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeMediaOvos" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Seção Previsão de Eclosão --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt"></i>
                                    Previsão de Eclosão - Próximos 30 Dias
                                </h3>
                                <div class="card-tools">
                                    <span class="badge badge-warning">{{ count($previsoesEclosao) }} incubações</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(count($previsoesEclosao) > 0)
                                    <div class="row">
                                        @foreach($previsoesEclosao as $previsao)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card card-outline 
                                                    @if($previsao['status'] == 'urgente') card-danger 
                                                    @elseif($previsao['status'] == 'proximo') card-warning 
                                                    @elseif($previsao['status'] == 'atrasado') card-dark 
                                                    @else card-info @endif">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">
                                                            <i class="fas fa-egg"></i>
                                                            {{ $previsao['lote'] }} - {{ $previsao['tipo_ave'] }}
                                                        </h5>
                                                        <div class="card-tools">
                                                            <span class="badge 
                                                                @if($previsao['status'] == 'urgente') badge-danger 
                                                                @elseif($previsao['status'] == 'proximo') badge-warning 
                                                                @elseif($previsao['status'] == 'atrasado') badge-dark 
                                                                @else badge-info @endif">
                                                                @if($previsao['status'] == 'urgente') URGENTE
                                                                @elseif($previsao['status'] == 'proximo') PRÓXIMO
                                                                @elseif($previsao['status'] == 'atrasado') ATRASADO
                                                                @else NORMAL @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <strong>Data Eclosão:</strong><br>
                                                                <span class="text-primary">{{ $previsao['data_eclosao'] }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <strong>Dias Restantes:</strong><br>
                                                                <span class="text-danger">{{ $previsao['dias_restantes'] }} dias</span>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-6">
                                                                <strong>Ovos:</strong><br>
                                                                <span class="text-success">{{ $previsao['quantidade_ovos'] }}</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <strong>Chocadeira:</strong><br>
                                                                <span class="text-info">{{ $previsao['chocadeira'] }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <strong>Progresso da Incubação:</strong>
                                                            <div class="progress mt-1">
                                                                <div class="progress-bar 
                                                                    @if($previsao['progresso'] >= 90) bg-success 
                                                                    @elseif($previsao['progresso'] >= 70) bg-warning 
                                                                    @else bg-info @endif" 
                                                                    role="progressbar" 
                                                                    style="width: {{ $previsao['progresso'] }}%" 
                                                                    aria-valuenow="{{ $previsao['progresso'] }}" 
                                                                    aria-valuemin="0" 
                                                                    aria-valuemax="100">
                                                                    {{ $previsao['progresso'] }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($previsao['temperatura_atual'] > 0 || $previsao['umidade_atual'] > 0)
                                                            <div class="row mt-2">
                                                                <div class="col-6">
                                                                    <small><strong>Temp:</strong> {{ $previsao['temperatura_atual'] }}°C</small>
                                                                </div>
                                                                <div class="col-6">
                                                                    <small><strong>Umidade:</strong> {{ $previsao['umidade_atual'] }}%</small>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-footer">
                                                        <a href="{{ route('incubacoes.show', $previsao['id']) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> Ver Detalhes
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Não há eclosões previstas para os próximos 30 dias.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main row for Charts and Tables -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-7 connectedSortable">
                        <!-- PIE CHART: Aves por Tipo -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie"></i>
                                    Aves Ativas por Tipo
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="avesPorTipoPieChart" height="250"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- LINE CHART: Tendência de Mortes -->
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Tendência de Mortes (Últimos 12 Meses)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="mortesLineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- NOVO: LINE CHART: Taxa de Eclosão de Ovos Viáveis, Mensal -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Taxa de Eclosão de Ovos Viáveis (Mensal)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="eclosionLineChart" height="150"></canvas>
                                </div>
                                <p class="text-muted text-center mt-3">
                                    Total de Ovos: {{ $dadosTaxaEclosaoMensal['metrics']['total_ovos'] }} |
                                    Ovos Viáveis: {{ $dadosTaxaEclosaoMensal['metrics']['ovos_viaveis'] }} |
                                    Ovos Inférteis: {{ $dadosTaxaEclosaoMensal['metrics']['total_inferteis'] }}
                                </p>
                            </div>
                        </div>
                        <!-- /.card -->

                    </section>
                    <!-- /.Left col -->

                    <!-- Right col -->
                    <section class="col-lg-5 connectedSortable">
                        <!-- BAR CHART: Histórico de Eclosões por Tipo de Ave -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar"></i>
                                    Histórico de Eclosões (Últimos 12 Meses)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="eclosoesBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- LINE CHART: Ovos Postos Diariamente -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Ovos Postos Diariamente (Últimos 30 Dias)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="ovosPostosLineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- NOVO: LINE CHART: Ovos Não Eclodidos por Causa (Viáveis), Mensal -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-times-circle"></i>
                                    Ovos Não Eclodidos por Causa (Viáveis), Mensal
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="nonEclosionLineChart" height="250"></canvas>
                                </div>
                                <p class="text-muted text-center mt-3">
                                    Total Infectados: {{ $dadosOvosNaoEclodidosMensal['metrics']['total_infectados'] }} |
                                    Total Mortos: {{ $dadosOvosNaoEclodidosMensal['metrics']['total_mortos'] }}
                                </p>
                            </div>
                        </div>
                        <!-- /.card -->

                    </section>
                    <!-- /.Right col -->
                </div>
                <!-- /.row (main row) -->

                <!-- Row for Chocadeira Bar Chart (full width) -->
                <div class="row">
                    <section class="col-lg-12 connectedSortable">
                        <!-- NOVO: BAR CHART: Desempenho por Chocadeira -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-thermometer-half"></i>
                                    Desempenho por Chocadeira
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="chocadeiraBarChart" height="250"></canvas>
                                </div>
                                @if (!empty($dadosDesempenhoChocadeira['labels']))
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th>Chocadeira</th>
                                                    @foreach($dadosDesempenhoChocadeira['labels'] as $label)
                                                        <th>{{ $label }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Taxa Eclosão (%)</td>
                                                    @foreach($dadosDesempenhoChocadeira['taxas_eclosao'] as $taxa)
                                                        <td>{{ number_format($taxa, 2) }}%</td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- /.card -->
                    </section>
                </div>
                <!-- /.row -->

                <!-- Row for Alerts and Incubation Table -->
                <div class="row">
                    <section class="col-lg-12 connectedSortable">
                        <!-- Alertas e Notificações -->
                        @if (!empty($alertas))
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-bell"></i>
                                        Alertas e Notificações
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @foreach ($alertas as $alerta)
                                        <div class="alert alert-{{ $alerta['type'] }} alert-dismissible fade show" role="alert">
                                            {{ $alerta['message'] }}
                                            @if (isset($alerta['link']))
                                                <a href="{{ $alerta['link'] }}" class="alert-link">Ver detalhes</a>
                                            @endif
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- /.card -->

                        <!-- Tabela de Incubações Ativas -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-egg"></i>
                                    Quadro de Incubações Ativas
                                </h3>
                                <div class="card-tools">
                                    <form action="{{ route('dashboard') }}" method="GET" class="form-inline">
                                        <label for="filter_tipo_ave" class="mr-2">Tipo de Ave:</label>
                                        <select name="tipo_ave_id" id="filter_tipo_ave" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                            <option value="">Todos</option>
                                            @foreach($tiposAves as $tipo)
                                                <option value="{{ $tipo->id }}" {{ $selectedTipoAve == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                            @endforeach
                                        </select>

                                        <label for="filter_lote" class="mr-2">Lote:</label>
                                        <select name="lote_id" id="filter_lote" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                            <option value="">Todos</option>
                                            @foreach($lotes as $lote)
                                                <option value="{{ $lote->id }}" {{ $selectedLote == $lote->id ? 'selected' : '' }}>{{ $lote->identificacao_lote }}</option>
                                            @endforeach
                                        </select>
                                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">Limpar Filtros</a>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Lote</th>
                                                <th>Tipo Ave</th>
                                                <th>Entrada</th>
                                                <th>Previsão Eclosão</th>
                                                <th>Ovos</th>
                                                <th>Progresso</th>
                                                <th>Status</th>
                                                <th style="width: 100px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($incubacoesData as $incubacao)
                                                <tr>
                                                    <td>{{ $incubacao['id'] }}</td>
                                                    <td>{{ $incubacao['lote_nome'] }}</td>
                                                    <td>{{ $incubacao['tipo_ave_nome'] }}</td>
                                                    <td>{{ $incubacao['data_entrada_incubadora'] }}</td>
                                                    <td>{{ $incubacao['data_prevista_eclosao'] }}</td>
                                                    <td>{{ $incubacao['quantidade_ovos'] }}</td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar bg-primary" style="width: {{ $incubacao['progress_percentage'] }}%"></div>
                                                        </div>
                                                        <span class="badge bg-primary">{{ $incubacao['progress_percentage'] }}%</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $badgeClass = '';
                                                            switch ($incubacao['status']) {
                                                                case 'Em andamento': $badgeClass = 'badge-info'; break;
                                                                case 'Finalizando': $badgeClass = 'badge-warning'; break;
                                                                case 'Concluído': $badgeClass = 'badge-success'; break;
                                                                case 'Atrasado': $badgeClass = 'badge-danger'; break;
                                                                case 'Prevista': $badgeClass = 'badge-secondary'; break;
                                                                default: $badgeClass = 'badge-secondary'; break;
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ $incubacao['status'] }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ $incubacao['link_detalhes'] }}" class="btn btn-sm btn-info" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Nenhuma incubação ativa encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </section>
                </div>
                <!-- /.row -->

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
   @include('layouts.partials.scripts')
    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(function () {
        //-------------
        //- PIE CHART: Aves por Tipo -
        //-------------
        var avesPorTipoPieChartCanvas = $('#avesPorTipoPieChart').get(0).getContext('2d');
        var avesPorTipoPieChartData = {
            labels: {!! json_encode($labelsAvesPorTipo) !!},
            datasets: [
                {
                    data: {!! json_encode($dataAvesPorTipo) !!},
                    backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                }
            ]
        };
        var avesPorTipoPieChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                            return previousValue + currentValue;
                        });
                        var currentValue = dataset.data[tooltipItem.index];
                        var percentage = parseFloat((currentValue/total*100).toFixed(2));
                        return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + '%)';
                    }
                }
            }
        };
        new Chart(avesPorTipoPieChartCanvas, {
            type: 'pie',
            data: avesPorTipoPieChartData,
            options: avesPorTipoPieChartOptions
        });

        //-------------
        //- LINE CHART: Tendência de Mortes -
        //-------------
        var mortesLineChartCanvas = $('#mortesLineChart').get(0).getContext('2d');
        var mortesLineChartData = {
            labels: {!! json_encode($labelsMortesMes) !!},
            datasets: [
                {
                    label: 'Número de Mortes',
                    fill: false,
                    borderColor: '#dc3545',
                    data: {!! json_encode($dataMortesMes) !!}
                }
            ]
        };
        var mortesLineChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            }
        };
        new Chart(mortesLineChartCanvas, {
            type: 'line',
            data: mortesLineChartData,
            options: mortesLineChartOptions
        });

        //-----------------------------------------
        //- BAR CHART: Histórico de Eclosões por Tipo de Ave -
        //-----------------------------------------
        var eclosoesBarChartCanvas = $('#eclosoesBarChart').get(0).getContext('2d');
        var eclosoesBarChartData = {
            labels: {!! json_encode($labelsEclosoesMesFormatted) !!},
            datasets: {!! json_encode($dataEclosoesPorTipo) !!}
        };
        var eclosoesBarChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            scales: {
                xAxes: [{
                    stacked: true,
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            }
        };
        new Chart(eclosoesBarChartCanvas, {
            type: 'bar',
            data: eclosoesBarChartData,
            options: eclosoesBarChartOptions
        });

        //-----------------------------------------
        //- LINE CHART: Ovos Postos Diariamente -
        //-----------------------------------------
        var ovosPostosLineChartCanvas = $('#ovosPostosLineChart').get(0).getContext('2d');
        var ovosPostosLineChartData = {
            labels: {!! json_encode($labelsOvosPostos) !!},
            datasets: [
                {
                    label: 'Ovos Postos',
                    fill: false,
                    borderColor: 'rgba(255, 193, 7, 1)',
                    data: {!! json_encode($dataOvosPostos) !!}
                }
            ]
        };
        var ovosPostosLineChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            }
        };
        new Chart(ovosPostosLineChartCanvas, {
            type: 'line',
            data: ovosPostosLineChartData,
            options: ovosPostosLineChartOptions
        });


        //-----------------------------------------------------
        //- NOVO: LINE CHART: Taxa de Eclosão de Ovos Viáveis, Mensal -
        //-----------------------------------------------------
        var eclosionLineChartCanvas = $('#eclosionLineChart').get(0).getContext('2d');
        var eclosionLineChartData = {!! json_encode($dadosTaxaEclosaoMensal) !!};
        var eclosionLineChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += tooltipItem.yLabel + '%';
                        return label;
                    }
                }
            }
        };
        new Chart(eclosionLineChartCanvas, {
            type: 'line',
            data: eclosionLineChartData,
            options: eclosionLineChartOptions
        });

        //-----------------------------------------------------
        //- NOVO: LINE CHART: Ovos Não Eclodidos por Causa (Viáveis), Mensal -
        //-----------------------------------------------------
        var nonEclosionLineChartCanvas = $('#nonEclosionLineChart').get(0).getContext('2d');
        var nonEclosionLineChartData = {!! json_encode($dadosOvosNaoEclodidosMensal) !!};
        var nonEclosionLineChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += tooltipItem.yLabel + ' ovos';
                        return label;
                    }
                }
            }
        };
        new Chart(nonEclosionLineChartCanvas, {
            type: 'line',
            data: nonEclosionLineChartData,
            options: nonEclosionLineChartOptions
        });

        //----------------------------------
        //- NOVO: CHOCADEIRA BAR CHART -
        //----------------------------------
        var chocadeiraBarChartCanvas = $('#chocadeiraBarChart').get(0).getContext('2d');
        var chocadeiraBarChartData = {!! json_encode($dadosDesempenhoChocadeira) !!};
        var chocadeiraBarChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return value + ' ovos';
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += tooltipItem.yLabel + ' ovos';
                        return label;
                    }
                }
            }
        };
        new Chart(chocadeiraBarChartCanvas, {
            type: 'bar',
            data: chocadeiraBarChartData,
            options: chocadeiraBarChartOptions
        });

    });

    // NOVO: Renderização dos KPIs Visuais (Gauges)
    function renderGauge(elementId, value, label, max = 100) {
        const gaugeElement = document.getElementById(elementId);
        if (!gaugeElement) return;

        const gauge = new JustGage({
            id: elementId,
            value: value,
            min: 0,
            max: max,
            label: label,
            pointer: true,
            gaugeWidthScale: 0.6,
            counter: true,
            relativeGaugeSize: true,
            levelColors: [
                "#ff0000",
                "#f9c802",
                "#a9d70b"
            ],
            customSectors: [
                { color: "#ff0000", lo: 0, hi: 50 },
                { color: "#f9c802", lo: 51, hi: 75 },
                { color: "#a9d70b", lo: 76, hi: 100 }
            ],
            valueFontColor: '#343a40',
            labelFontColor: '#6c757d',
            pointerOptions: {
                toplength: -15,
                bottomlength: 10,
                bottomwidth: 12,
                color: '#8e8e93',
                stroke: '#ffffff',
                stroke_width: 3,
                stroke_linecap: 'round'
            },
            shadowOpacity: 0.5,
            shadowSize: 5,
            shadowVerticalOffset: 2
        });
    }

    // Renderizar os gauges com os dados do Laravel
    document.addEventListener('DOMContentLoaded', function() {
        renderGauge('gaugeTaxaEclosao', @json($kpis['taxa_eclosao_30_dias']), 'Taxa Eclosão (30D)');
        renderGauge('gaugeTaxaFertilidade', @json($kpis['taxa_fertilidade']), 'Taxa Fertilidade');
        renderGauge('gaugeMelhorChocadeira', @json($kpis['melhor_chocadeira_eficiencia']), 'Melhor Chocadeira');
        renderGauge('gaugeMediaOvos', @json($kpis['media_ovos_incubacao']), 'Média Ovos/Incub.', 50);
    });

    // Função para limpar os filtros
    function limparFiltros() {
        document.getElementById('ano').value = '';
        document.getElementById('trimestre').value = '';
        document.getElementById('mes').value = '';
        document.getElementById('filtros-form').submit();
    }
</script>

<!-- JustGage library for gauges -->
<script src="https://cdn.jsdelivr.net/npm/raphael@2.3.0/raphael.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/justgage@1.4.0/justgage.js"></script>

@push('styles')
<style>
    .kpi-gauge {
        margin: 0 auto;
        text-align: center;
    }
    .previsao-card {
        transition: transform 0.2s;
    }
    .previsao-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush