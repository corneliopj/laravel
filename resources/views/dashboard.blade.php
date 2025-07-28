@php
    $pageTitle = 'Dashboard Principal';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

{{-- Links CDN para FullCalendar --}}
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

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
                <!-- Filtro de Período -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Filtrar Dados</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="filterForm" method="GET" action="{{ route('dashboard') }}">
                                    <div class="form-group">
                                        <label for="date_range">Período:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control float-right" id="date_range" name="date_range">
                                            <input type="hidden" name="data_inicio" id="data_inicio" value="{{ $dataInicio->format('Y-m-d') }}">
                                            <input type="hidden" name="data_fim" id="data_fim" value="{{ $dataFim->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Aplicar Filtro</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas Dinâmicos -->
                <div class="row">
                    <div class="col-md-12">
                        @foreach ($alertas as $alerta)
                            <div class="alert alert-{{ $alerta['type'] }} alert-dismissible fade show" role="alert">
                                {{ $alerta['message'] }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Small boxes (Stat box) - KPIs Destacados -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalAvesAtivas }}</h3>
                                <p>Aves Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dove"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $mortesNoPeriodo }}</h3>
                                <p>Mortes (Período)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $ovosPostosNoPeriodo }}</h3>
                                <p>Ovos Postos (Período)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-egg"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($taxaEclosaoMediaGeral, 2) }}<sup style="font-size: 20px">%</sup></h3>
                                <p>Taxa de Eclosão Média</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais detalhes <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-7 connectedSortable">
                        <!-- Gráfico de Tendência de Eclosão -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Tendência de Eclosão (Ovos Eclodidos)
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="eclosionLineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->

                        <!-- Gráfico de Desempenho por Chocadeira -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Desempenho de Incubação por Chocadeira
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="chocadeiraBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </section>
                    <!-- /.Left col -->

                    <!-- Right col -->
                    <section class="col-lg-5 connectedSortable">
                        <!-- Gráfico de Aves por Tipo -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    Aves por Tipo
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="avesPorTipoPieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- /.card -->

                        <!-- TABLE: Incubações Ativas (Restaurada) -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Incubações Ativas</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table m-0">
                                        <thead>
                                            <tr>
                                                <th>Lote</th>
                                                <th>Tipo</th>
                                                <th>Ovos</th>
                                                <th>Progresso</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($incubacoesData as $incubacao)
                                                <tr>
                                                    <td><a href="{{ $incubacao['link_detalhes'] }}">{{ $incubacao['lote_nome'] }}</a></td>
                                                    <td>{{ $incubacao['tipo_ave_nome'] }}</td>
                                                    <td>{{ $incubacao['quantidade_ovos'] }}</td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-primary" style="width: {{ $incubacao['progress_percentage'] }}%"></div>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $incubacao['progress_percentage'] }}% Concluído
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if ($incubacao['status'] == 'Em andamento')
                                                            <span class="badge badge-info">{{ $incubacao['status'] }}</span>
                                                        @elseif ($incubacao['status'] == 'Finalizando')
                                                            <span class="badge badge-warning">{{ $incubacao['status'] }}</span>
                                                        @elseif ($incubacao['status'] == 'Concluído')
                                                            <span class="badge badge-success">{{ $incubacao['status'] }}</span>
                                                        @elseif ($incubacao['status'] == 'Atrasado')
                                                            <span class="badge badge-danger">{{ $incubacao['status'] }}</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ $incubacao['status'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ $incubacao['link_detalhes'] }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Nenhuma incubação ativa encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                <a href="{{ route('incubacoes.create') }}" class="btn btn-sm btn-info float-left">Nova Incubação</a>
                                <a href="{{ route('incubacoes.index') }}" class="btn btn-sm btn-secondary float-right">Ver Todas</a>
                            </div>
                            <!-- /.card-footer -->
                        </div>
                        <!-- /.card -->

                        <!-- Calendário de Eventos (FullCalendar) -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Calendário de Eventos
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id='calendar'></div>
                            </div>
                        </div>
                        <!-- /.card -->

                    </section>
                    <!-- /.Right col -->
                </div>
                <!-- /.row (main row) -->
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

{{-- Inclui o partial scripts (TODOS OS SCRIPTS GLOBAIS NA ORDEM CORRETA) --}}
@include('layouts.partials.scripts')

{{-- FullCalendar JS --}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js'></script> {{-- Localização para Português --}}

{{-- Scripts específicos do Dashboard --}}
<script>
    $(document).ready(function() {
        // Inicializa o Date Range Picker
        $('#date_range').daterangepicker({
            startDate: moment('{{ $dataInicio->format('Y-m-d') }}'),
            endDate: moment('{{ $dataFim->format('Y-m-d') }}'),
            ranges: {
                'Hoje': [moment(), moment()],
                'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'De',
                toLabel: 'Até',
                customRangeLabel: 'Período Personalizado',
                daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                firstDay: 1
            }
        }, function(start, end) {
            $('#data_inicio').val(start.format('YYYY-MM-DD'));
            $('#data_fim').val(end.format('YYYY-MM-DD'));
        });

        // Gráfico de Aves por Tipo (Pizza)
        var avesPorTipoPieChartCanvas = $('#avesPorTipoPieChart').get(0).getContext('2d');
        var avesPorTipoPieData = {
            labels: {!! json_encode($labelsAvesPorTipo) !!},
            datasets: [{
                data: {!! json_encode($dadosAvesPorTipo) !!},
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#6c757d', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2'], // Cores variadas
            }]
        };
        var avesPorTipoPieOptions = {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index] || '';
                        var value = data.datasets[0].data[tooltipItem.index];
                        var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                        var percentage = ((value / total) * 100).toFixed(2);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        };
        new Chart(avesPorTipoPieChartCanvas, {
            type: 'pie',
            data: avesPorTipoPieData,
            options: avesPorTipoPieOptions
        });

        // Gráfico de Tendência de Eclosão (Linha)
        var eclosionLineChartCanvas = $('#eclosionLineChart').get(0).getContext('2d');
        var eclosionLineChartData = {
            labels: {!! json_encode($labelsTendenciaEclosao) !!},
            datasets: [{
                label: 'Ovos Eclodidos',
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: {!! json_encode($dadosTendenciaEclosao) !!}
            }]
        };
        var eclosionLineChartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            datasetFill: false,
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false,
                    },
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
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
        new Chart(eclosionLineChartCanvas, {
            type: 'line',
            data: eclosionLineChartData,
            options: eclosionLineChartOptions
        });

        // Gráfico de Desempenho por Chocadeira (Barras com Linha de Taxa de Eclosão)
        var chocadeiraBarChartCanvas = $('#chocadeiraBarChart').get(0).getContext('2d');
        var chocadeiraBarChartData = {
            labels: {!! json_encode($labelsChocadeira) !!},
            datasets: [{
                label: 'Ovos Colocados',
                backgroundColor: 'rgba(0, 123, 255, 0.7)', // Azul
                data: {!! json_encode($totalOvosPorChocadeira) !!},
                yAxisID: 'y-axis-quantidade' // Eixo Y para quantidade
            }, {
                label: 'Ovos Eclodidos',
                backgroundColor: 'rgba(40, 167, 69, 0.7)', // Verde
                data: {!! json_encode($totalEclodidosPorChocadeira) !!},
                yAxisID: 'y-axis-quantidade' // Eixo Y para quantidade
            }, {
                label: 'Taxa de Eclosão (%)',
                backgroundColor: 'rgba(255, 193, 7, 0.7)', // Amarelo
                data: {!! json_encode($taxasEclosaoChocadeira) !!},
                type: 'line', // Adiciona como linha no gráfico de barras
                yAxisID: 'y-axis-percentual', // Usa um segundo eixo Y para percentual
                borderColor: 'rgba(255, 193, 7, 1)',
                fill: false,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        };
        var chocadeiraBarChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false,
            scales: {
                yAxes: [{
                    id: 'y-axis-quantidade',
                    position: 'left', // Eixo Y para quantidade à esquerda
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            if (Number.isInteger(value)) {
                                return value + ' ovos';
                            }
                        }
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Quantidade de Ovos'
                    }
                }, {
                    id: 'y-axis-percentual',
                    position: 'right', // Eixo Y para percentual à direita
                    ticks: {
                        beginAtZero: true,
                        max: 100, // Taxa de eclosão vai de 0 a 100%
                        callback: function(value, index, values) {
                            return value + '%';
                        }
                    },
                    gridLines: {
                        drawOnChartArea: false, // Não desenha linhas de grade para este eixo
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Taxa de Eclosão (%)'
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
                        // Formata o tooltip com base no tipo de dado (ovos ou percentual)
                        if (data.datasets[tooltipItem.datasetIndex].yAxisID === 'y-axis-percentual') {
                            label += tooltipItem.yLabel + '%';
                        } else {
                            label += tooltipItem.yLabel + ' ovos';
                        }
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

        // Inicializa o FullCalendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', // Visualização inicial: mês em grade
            locale: 'pt-br', // Define o idioma para português do Brasil
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia'
            },
            events: {!! json_encode($calendarEvents) !!}, // Passa os eventos do controller
            eventClick: function(info) {
                // Ao clicar em um evento, redireciona para a URL do evento (se houver)
                if (info.event.url) {
                    window.open(info.event.url);
                    info.jsEvent.preventDefault(); // Previne o comportamento padrão do link
                }
            },
            eventDidMount: function(info) {
                // Adiciona tooltips aos eventos para mais detalhes
                $(info.el).tooltip({
                    title: info.event.title + ' em ' + moment(info.event.start).format('DD/MM/YYYY'),
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render(); // Renderiza o calendário
    });
</script>
