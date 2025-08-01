@php
    $pageTitle = 'Dashboard Financeiro';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $pageTitle }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                        </ol>
                    </div>
                </div>
            </div>
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
                                <form method="GET" action="{{ route('financeiro.dashboard') }}" id="filtros-form">
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
@php
    $saldoTotal = $saldoTotal ?? ($dadosComparativo['periodo_atual']['saldo'] ?? 0);
@endphp
                {{-- Seção de Comparativo de Períodos --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Comparativo de Períodos
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Comparação com Mês Anterior --}}
                                    <div class="col-md-6">
                                        <h5 class="text-center mb-3">
                                            <i class="fas fa-calendar-minus"></i>
                                            Comparação com Mês Anterior
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Métrica</th>
                                                        <th class="text-center">{{ $dadosComparativo['periodo_atual']['label'] }}</th>
                                                        <th class="text-center">{{ $dadosComparativo['periodo_anterior']['label'] }}</th>
                                                        <th class="text-center">Variação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Receitas</strong></td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['periodo_atual']['receitas'], 2, ',', '.') }}</td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['periodo_anterior']['receitas'], 2, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $dadosComparativo['variacoes_mes_anterior']['receitas']['tipo'] == 'positiva' ? 'success' : 'danger' }}">
                                                                <i class="fas fa-arrow-{{ $dadosComparativo['variacoes_mes_anterior']['receitas']['tipo'] == 'positiva' ? 'up' : 'down' }}"></i>
                                                                {{ $dadosComparativo['variacoes_mes_anterior']['receitas']['percentual'] }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Despesas</strong></td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['periodo_atual']['despesas'], 2, ',', '.') }}</td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['periodo_anterior']['despesas'], 2, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $dadosComparativo['variacoes_mes_anterior']['despesas']['tipo'] == 'positiva' ? 'warning' : 'success' }}">
                                                                <i class="fas fa-arrow-{{ $dadosComparativo['variacoes_mes_anterior']['despesas']['tipo'] == 'positiva' ? 'up' : 'down' }}"></i>
                                                                {{ $dadosComparativo['variacoes_mes_anterior']['despesas']['percentual'] }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr class="table-active">
                                                        <td><strong>Saldo</strong></td>
                                                        <td class="text-right"><strong>R$ {{ number_format($dadosComparativo['periodo_atual']['saldo'], 2, ',', '.') }}</strong></td>
                                                        <td class="text-right"><strong>R$ {{ number_format($dadosComparativo['periodo_anterior']['saldo'], 2, ',', '.') }}</strong></td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $dadosComparativo['variacoes_mes_anterior']['saldo']['tipo'] == 'positiva' ? 'success' : 'danger' }} badge-lg">
                                                                <i class="fas fa-arrow-{{ $dadosComparativo['variacoes_mes_anterior']['saldo']['tipo'] == 'positiva' ? 'up' : 'down' }}"></i>
                                                                {{ $dadosComparativo['variacoes_mes_anterior']['saldo']['percentual'] }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    {{-- Comparação com Ano Anterior --}}
                                    <div class="col-md-6">
                                        <h5 class="text-center mb-3">
                                            <i class="fas fa-calendar-alt"></i>
                                            Comparação com Ano Anterior
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Métrica</th>
                                                        <th class="text-center">{{ $dadosComparativo['periodo_atual']['label'] }}</th>
                                                        <th class="text-center">{{ $dadosComparativo['ano_anterior']['label'] }}</th>
                                                        <th class="text-center">Variação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Receitas</strong></td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['periodo_atual']['receitas'], 2, ',', '.') }}</td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['ano_anterior']['receitas'], 2, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $dadosComparativo['variacoes_ano_anterior']['receitas']['tipo'] == 'positiva' ? 'success' : 'danger' }}">
                                                                <i class="fas fa-arrow-{{ $dadosComparativo['variacoes_ano_anterior']['receitas']['tipo'] == 'positiva' ? 'up' : 'down' }}"></i>
                                                                {{ $dadosComparativo['variacoes_ano_anterior']['receitas']['percentual'] }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Despesas</strong></td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['periodo_atual']['despesas'], 2, ',', '.') }}</td>
                                                        <td class="text-right">R$ {{ number_format($dadosComparativo['ano_anterior']['despesas'], 2, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $dadosComparativo['variacoes_ano_anterior']['despesas']['tipo'] == 'positiva' ? 'warning' : 'success' }}">
                                                                <i class="fas fa-arrow-{{ $dadosComparativo['variacoes_ano_anterior']['despesas']['tipo'] == 'positiva' ? 'up' : 'down' }}"></i>
                                                                {{ $dadosComparativo['variacoes_ano_anterior']['despesas']['percentual'] }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr class="table-active">
                                                        <td><strong>Saldo</strong></td>
                                                        <td class="text-right"><strong>R$ {{ number_format($dadosComparativo['periodo_atual']['saldo'], 2, ',', '.') }}</strong></td>
                                                        <td class="text-right"><strong>R$ {{ number_format($dadosComparativo['ano_anterior']['saldo'], 2, ',', '.') }}</strong></td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $dadosComparativo['variacoes_ano_anterior']['saldo']['tipo'] == 'positiva' ? 'success' : 'danger' }} badge-lg">
                                                                <i class="fas fa-arrow-{{ $dadosComparativo['variacoes_ano_anterior']['saldo']['tipo'] == 'positiva' ? 'up' : 'down' }}"></i>
                                                                {{ $dadosComparativo['variacoes_ano_anterior']['saldo']['percentual'] }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Seção Top 5 Despesas e Receitas --}}
                <div class="row mb-4">
                    {{-- Top 5 Despesas --}}
                    <div class="col-md-6">
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-arrow-down"></i>
                                    Top 5 Maiores Despesas do Mês
                                </h3>
                                <div class="card-tools">
                                    <span class="badge badge-danger">
                                        R$ {{ number_format($top5Despesas['total_periodo'], 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(count($top5Despesas['despesas']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Descrição</th>
                                                    <th>Categoria</th>
                                                    <th class="text-right">Valor</th>
                                                    <th class="text-center">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($top5Despesas['despesas'] as $index => $despesa)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-{{ $index == 0 ? 'danger' : ($index == 1 ? 'warning' : 'secondary') }}">
                                                                {{ $index + 1 }}º
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $despesa['descricao'] }}</strong>
                                                            <br><small class="text-muted">{{ $despesa['quantidade'] }} transações</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-outline-secondary">{{ $despesa['categoria'] }}</span>
                                                        </td>
                                                        <td class="text-right">
                                                            <strong>R$ {{ number_format($despesa['valor'], 2, ',', '.') }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="progress progress-sm">
                                                                <div class="progress-bar bg-danger" style="width: {{ $despesa['percentual'] }}%"></div>
                                                            </div>
                                                            <small>{{ $despesa['percentual'] }}%</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Nenhuma despesa encontrada</h6>
                                        <p class="text-muted small">Não há despesas registradas para este período.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Top 5 Receitas --}}
                    <div class="col-md-6">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-arrow-up"></i>
                                    Top 5 Maiores Receitas do Mês
                                </h3>
                                <div class="card-tools">
                                    <span class="badge badge-success">
                                        R$ {{ number_format($top5Receitas['total_periodo'], 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(count($top5Receitas['receitas']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Descrição</th>
                                                    <th>Categoria</th>
                                                    <th class="text-right">Valor</th>
                                                    <th class="text-center">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($top5Receitas['receitas'] as $index => $receita)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-{{ $index == 0 ? 'success' : ($index == 1 ? 'info' : 'secondary') }}">
                                                                {{ $index + 1 }}º
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $receita['descricao'] }}</strong>
                                                            <br><small class="text-muted">{{ $receita['quantidade'] }} transações</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-outline-secondary">{{ $receita['categoria'] }}</span>
                                                        </td>
                                                        <td class="text-right">
                                                            <strong>R$ {{ number_format($receita['valor'], 2, ',', '.') }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="progress progress-sm">
                                                                <div class="progress-bar bg-success" style="width: {{ $receita['percentual'] }}%"></div>
                                                            </div>
                                                            <small>{{ $receita['percentual'] }}%</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Nenhuma receita encontrada</h6>
                                        <p class="text-muted small">Não há receitas registradas para este período.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráficos existentes --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Receitas e Despesas - Anual</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoLinha" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Balanço Mensal</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoPizza" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Top Categorias - Receitas</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoBarrasReceitas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title">Top Categorias - Despesas</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoBarrasDespesas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
<!-- ./wrapper -->

{{-- Inclui o partial scripts --}}
@include('layouts.partials.scripts')

@push('styles')
<style>
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .progress-sm {
        height: 0.5rem;
    }
    
    .badge-outline-secondary {
        color: #6c757d;
        border: 1px solid #6c757d;
        background-color: transparent;
    }
    
    .card-tools .badge {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Configurações aprimoradas de tooltip para todos os gráficos
    const tooltipConfig = {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        titleColor: '#fff',
        bodyColor: '#fff',
        borderColor: '#fff',
        borderWidth: 1,
        cornerRadius: 6,
        displayColors: true,
        callbacks: {
            title: function(context) {
                return context[0].label || '';
            },
            label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                    label += ': ';
                }
                
                if (context.chart.canvas.id === 'graficoPizza') {
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = ((context.raw / total) * 100).toFixed(1);
                    label += `R$ ${context.raw.toFixed(2).replace('.', ',')} (${percentage}%)`;
                } else {
                    label += `R$ ${context.raw.toFixed(2).replace('.', ',')}`;
                }
                
                return label;
            },
            afterLabel: function(context) {
                if (context.chart.canvas.id === 'graficoLinha') {
                    return ['Clique para ver detalhes do período'];
                }
                return '';
            }
        }
    };

    // Aplicar configuração de tooltip a todos os gráficos
    Chart.defaults.plugins.tooltip = tooltipConfig;

    // Filtros Dinâmicos
    function limparFiltros() {
        document.getElementById('ano').value = '{{ Carbon\Carbon::now()->year }}';
        document.getElementById('mes').value = '';
        document.getElementById('filtros-form').submit();
    }

    // Inicialização dos gráficos
    $(function () {
        // Gráfico de linhas
        var linhaCtx = document.getElementById('graficoLinha').getContext('2d');
        var linhaChart = new Chart(linhaCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dadosGraficoLinha['labels']) !!},
                datasets: {!! json_encode($dadosGraficoLinha['datasets']) !!}
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(2).replace('.', ',');
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de pizza
        var pizzaCtx = document.getElementById('graficoPizza').getContext('2d');
        var pizzaChart = new Chart(pizzaCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($dadosGraficoPizza['labels']) !!},
                datasets: [{
                    data: {!! json_encode($dadosGraficoPizza['data']) !!},
                    backgroundColor: {!! json_encode($dadosGraficoPizza['colors']) !!},
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
            }
        });

        // Gráfico de barras - Receitas
        var barrasReceitasCtx = document.getElementById('graficoBarrasReceitas').getContext('2d');
        var barrasReceitasChart = new Chart(barrasReceitasCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dadosGraficoBarras['receitas']['labels']) !!},
                datasets: [{
                    label: 'Receitas',
                    data: {!! json_encode($dadosGraficoBarras['receitas']['data']) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(2).replace('.', ',');
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de barras - Despesas
        var barrasDespesasCtx = document.getElementById('graficoBarrasDespesas').getContext('2d');
        var barrasDespesasChart = new Chart(barrasDespesasCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dadosGraficoBarras['despesas']['labels']) !!},
                datasets: [{
                    label: 'Despesas',
                    data: {!! json_encode($dadosGraficoBarras['despesas']['data']) !!},
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(2).replace('.', ',');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush