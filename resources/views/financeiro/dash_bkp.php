@php
    $pageTitle = 'Dashboard Financeiro';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        {{-- Inclui o partial navbar --}}
        @include('layouts.partials.navbar')
        {{-- Inclui o partial sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- CONTEÚDO PRINCIPAL DA PÁGINA --}}
        <div class="content-wrapper px-4 py-2" style="min-height:797px;">
            {{-- Cabeçalho do Conteúdo --}}
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">{{ $pageTitle }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Financeiro</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conteúdo Principal --}}
            <div class="content">
                <div class="container-fluid">
                    {{-- Mensagens de sucesso/erro (usando o padrão do seu Dashboard) --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        {{-- Saldo Total --}}
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>R$ {{ number_format($saldoTotal, 2, ',', '.') }}</h3>
                                    <p>Saldo Total</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        {{-- Receitas do Mês --}}
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>R$ {{ number_format($receitasMes, 2, ',', '.') }}</h3>
                                    <p>Receitas do Mês ({{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}/{{ $ano }})</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <a href="{{ route('financeiro.receitas.index', ['data_inicio' => \Carbon\Carbon::create($ano, $mes, 1)->format('Y-m-d'), 'data_fim' => \Carbon\Carbon::create($ano, $mes)->endOfMonth()->format('Y-m-d')]) }}" class="small-box-footer">Ver Receitas <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        {{-- Despesas do Mês --}}
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>R$ {{ number_format($despesasMes, 2, ',', '.') }}</h3>
                                    <p>Despesas do Mês ({{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}/{{ $ano }})</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <a href="{{ route('financeiro.despesas.index', ['data_inicio' => \Carbon\Carbon::create($ano, $mes, 1)->format('Y-m-d'), 'data_fim' => \Carbon\Carbon::create($ano, $mes)->endOfMonth()->format('Y-m-d')]) }}" class="small-box-footer">Ver Despesas <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        {{-- Saldo do Mês --}}
                        <div class="col-lg-3 col-6">
                            <div class="small-box {{ $saldoMes >= 0 ? 'bg-primary' : 'bg-warning' }}">
                                <div class="inner">
                                    <h3>R$ {{ number_format($saldoMes, 2, ',', '.') }}</h3>
                                    <p>Saldo do Mês ({{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}/{{ $ano }})</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Filtro de Mês/Ano --}}
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Filtrar por Mês/Ano</h3>
                        </div>
                        <form action="{{ route('financeiro.dashboard') }}" method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="mes">Mês:</label>
                                        <select name="mes" id="mes" class="form-control">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="ano">Ano:</label>
                                        <input type="number" name="ano" id="ano" class="form-control" value="{{ $ano }}" min="2000" max="{{ date('Y') + 5 }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Aplicar Filtro</button>
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        {{-- Gráfico de Barras: Receitas vs. Despesas por Mês --}}
                        <div class="col-md-6">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Receitas vs. Despesas (Anual)</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Gráfico de Pizza: Distribuição de Despesas por Categoria --}}
                        <div class="col-md-6">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Despesas por Categoria ({{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}/{{ $ano }})</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Gráfico de Linha: Evolução do Saldo Acumulado --}}
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Evolução do Saldo Acumulado (Últimos 12 Meses)</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- FIM DO CONTEÚDO PRINCIPAL DA PÁGINA --}}

        {{-- Inclui o partial footer --}}
        @include('layouts.partials.footer')
    </div>
    {{-- Fim do div.wrapper --}}

    {{-- Script do Chart.js (CDN) e código para os gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Plugin para cores dinâmicas no Chart.js (opcional, mas útil para gráficos de linha) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-colorschemes"></script> --}} {{-- Comentado, pois não foi mencionado explicitamente o uso deste plugin --}}

    <script>
        $(function () {
            // Dados para o Gráfico de Barras
            var barChartCanvas = $('#barChart').get(0).getContext('2d');
            var barChartData = @json($dadosGraficoBarras);
            new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Dados para o Gráfico de Pizza
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
            var pieChartData = @json($dadosGraficoPizza);
            new Chart(pieChartCanvas, {
                type: 'pie',
                data: pieChartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            // Dados para o Gráfico de Linha
            var lineChartCanvas = $('#lineChart').get(0).getContext('2d');
            var lineChartData = @json($dadosGraficoLinha);
            new Chart(lineChartCanvas, {
                type: 'line',
                data: lineChartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
