@php
    $pageTitle = 'Dashboard de Relatórios Financeiros';
@endphp

@include('layouts.partials.head')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('layouts.partials.navbar')
        @include('layouts.partials.sidebar')

        <div class="content-wrapper px-4 py-2">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark"><i class="fas fa-chart-line mr-2"></i>{{ $pageTitle }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Relatórios</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    
                    {{-- KPIs Summary Cards --}}
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>R$ {{ number_format($kpis['faturamento_total_atual'], 2, ',', '.') }}</h3>
                                    <p>Faturamento Anual ({{ $anoAtual }})</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="small-box-footer">
                                    Variação: 
                                    <span class="badge {{ $kpis['variacao_faturamento']['tipo'] == 'positiva' ? 'badge-dark' : 'badge-danger' }}">
                                        {{ $kpis['variacao_faturamento']['tipo'] == 'positiva' ? '+' : '-' }}{{ $kpis['variacao_faturamento']['percentual'] }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>R$ {{ number_format($kpis['despesa_total_atual'], 2, ',', '.') }}</h3>
                                    <p>Despesas Totais ({{ $anoAtual }})</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="small-box-footer">
                                    Total de saídas registradas
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box {{ $kpis['liquidez_total_atual'] >= 0 ? 'bg-info' : 'bg-warning' }}">
                                <div class="inner">
                                    <h3>R$ {{ number_format($kpis['liquidez_total_atual'], 2, ',', '.') }}</h3>
                                    <p>Liquidez Acumulada</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-piggy-bank"></i>
                                </div>
                                <div class="small-box-footer">
                                    Saldo Líquido do Ano
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $anoAtual }}</h3>
                                    <p>Ano de Referência</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <form action="{{ route('financeiro.relatorios.index') }}" method="GET" class="small-box-footer" id="yearFilterForm">
                                    <select name="ano" class="form-control form-control-sm bg-primary border-0 text-white" onchange="document.getElementById('yearFilterForm').submit()">
                                        @for ($i = date('Y'); $i >= 2020; $i--)
                                            <option value="{{ $i }}" {{ $anoAtual == $i ? 'selected' : '' }}>Mudar para {{ $i }}</option>
                                        @endfor
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Charts Row --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Comparativo de Operação Mensal</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart">
                                        <canvas id="comparativoChart" style="min-height: 350px; height: 350px; max-height: 400px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="row text-center">
                                        <div class="col-sm-3 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header text-success">Faturamento</h5>
                                                <span class="description-text">ANO ATUAL</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header text-muted">Faturamento</h5>
                                                <span class="description-text">ANO ANTERIOR</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header text-danger">Despesas</h5>
                                                <span class="description-text">ANO ATUAL</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="description-block">
                                                <h5 class="description-header text-info">Liquidez</h5>
                                                <span class="description-text">ANO ATUAL</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <a href="{{ route('financeiro.relatorios.transacoes') }}" class="card card-link text-center border-left-primary elevation-1">
                                <div class="card-body">
                                    <i class="fas fa-list fa-2x text-primary mb-2"></i>
                                    <h5>Transações Detalhadas</h5>
                                    <p class="text-muted small">Filtros por data e tipo de operação</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('financeiro.relatorios.fluxo_caixa') }}" class="card card-link text-center border-left-success elevation-1">
                                <div class="card-body">
                                    <i class="fas fa-exchange-alt fa-2x text-success mb-2"></i>
                                    <h5>Fluxo de Caixa</h5>
                                    <p class="text-muted small">Análise periódica de saldo</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('financeiro.relatorios.por_categoria') }}" class="card card-link text-center border-left-warning elevation-1">
                                <div class="card-body">
                                    <i class="fas fa-tags fa-2x text-warning mb-2"></i>
                                    <h5>Por Categoria</h5>
                                    <p class="text-muted small">Distribuição percentual de gastos</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.partials.footer')
    </div>

    <script src="https://adminlte.io/themes/v3/plugins/jquery/jquery.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://adminlte.io/themes/v3/dist/js/adminlte.min.js?v=3.2.0"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('comparativoChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dadosAnoAtual['labels']) !!},
                    datasets: [
                        {
                            label: 'Faturamento {{ $anoAtual }}',
                            data: {!! json_encode($dadosAnoAtual['faturamento']) !!},
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            order: 2
                        },
                        {
                            label: 'Faturamento {{ $anoAnterior }}',
                            data: {!! json_encode($dadosAnoAnterior['faturamento']) !!},
                            backgroundColor: 'rgba(210, 214, 222, 0.6)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            order: 3
                        },
                        {
                            label: 'Despesas {{ $anoAtual }}',
                            data: {!! json_encode($dadosAnoAtual['despesas']) !!},
                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            order: 2
                        },
                        {
                            label: 'Liquidez {{ $anoAtual }}',
                            data: {!! json_encode($dadosAnoAtual['liquidez']) !!},
                            type: 'line',
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .card-link {
            transition: transform 0.2s, box-shadow 0.2s;
            color: inherit;
            text-decoration: none;
        }
        .card-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }
        .border-left-primary { border-left: 5px solid #007bff; }
        .border-left-success { border-left: 5px solid #28a745; }
        .border-left-warning { border-left: 5px solid #ffc107; }
    </style>
</body>
</html>
