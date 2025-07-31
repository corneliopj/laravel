@php
    $pageTitle = 'Dashboard Financeiro';
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard Financeiro</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Financeiro</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                {{-- Resumo Geral --}}
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>R$ {{ number_format($receitasMes, 2, ',', '.') }}</h3>
                                <p>Receitas do Mês ({{ Carbon\Carbon::createFromDate($ano, $mes)->monthName }}/{{ $ano }})</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="{{ route('financeiro.receitas.index') }}" class="small-box-footer">Ver Receitas <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>R$ {{ number_format($despesasMes, 2, ',', '.') }}</h3>
                                <p>Despesas do Mês ({{ Carbon\Carbon::createFromDate($ano, $mes)->monthName }}/{{ $ano }})</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-credit-card"></i>
                            </div>
                            <a href="{{ route('financeiro.despesas.index') }}" class="small-box-footer">Ver Despesas <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>R$ {{ number_format($saldoMes, 2, ',', '.') }}</h3>
                                <p>Saldo do Mês ({{ Carbon\Carbon::createFromDate($ano, $mes)->monthName }}/{{ $ano }})</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="{{ route('financeiro.relatorios.fluxo_caixa') }}?data_inicio={{ Carbon\Carbon::createFromDate($ano, $mes, 1)->toDateString() }}&data_fim={{ Carbon\Carbon::createFromDate($ano, $mes)->endOfMonth()->toDateString() }}" class="small-box-footer">Ver Fluxo de Caixa <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>R$ {{ number_format($saldoTotal, 2, ',', '.') }}</h3>
                                <p>Saldo Acumulado Geral</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais Detalhes <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Filtros de Ano/Mês (se necessário, pode ser mais avançado com AJAX) --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Filtros para Gráficos</h3>
                            </div>
                            <form action="{{ route('financeiro.dashboard') }}" method="GET">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ano">Ano:</label>
                                                <select name="ano" id="ano" class="form-control">
                                                    @for ($y = Carbon\Carbon::now()->year; $y >= 2020; $y--)
                                                        <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mes">Mês (para Despesas por Categoria):</label>
                                                <select name="mes" id="mes" class="form-control">
                                                    @for ($m = 1; $m <= 12; $m++)
                                                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create(null, $m, 1)->monthName }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Atualizar Gráficos</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                {{-- Gráficos --}}
                <div class="row">
                    {{-- Gráfico de Barras: Receitas vs. Despesas por Mês --}}
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Receitas vs. Despesas por Mês ({{ $ano }})</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    <small>
                                        Este gráfico de barras mostra a comparação entre as receitas e despesas totais de cada mês ao longo do ano selecionado.
                                        Barras verdes representam as receitas, e barras vermelhas representam as despesas.
                                        Ajuda a identificar picos de ganhos e gastos mensais.
                                    </small>
                                </p>
                                <div class="chart">
                                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Gráfico de Rosca (Doughnut): Distribuição de Despesas por Categoria --}}
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Distribuição de Despesas por Categoria ({{ Carbon\Carbon::createFromDate($ano, $mes)->monthName }}/{{ $ano }})</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    <small>
                                        Este gráfico de rosca ilustra como suas despesas foram distribuídas entre diferentes categorias no mês e ano selecionados.
                                        Cada fatia representa uma categoria, e o tamanho da fatia indica a proporção do gasto total.
                                        Ideal para entender onde a maior parte do dinheiro está a ser gasta.
                                    </small>
                                </p>
                                <div class="chart">
                                    <canvas id="doughnutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Gráfico de Linha: Evolução do Saldo Acumulado --}}
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Evolução do Saldo Acumulado ({{ $ano }})</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    <small>
                                        Este gráfico de linha mostra a trajetória do seu saldo financeiro acumulado ao longo do ano selecionado.
                                        Pontos de dados indicam o saldo ao final de cada mês.
                                        Permite visualizar tendências e a saúde financeira geral ao longo do tempo.
                                    </small>
                                </p>
                                <div class="chart">
                                    <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Comissões (se o usuário for vendedor) --}}
                @auth
                    @if(Auth::user()->isVendedor()) {{-- Supondo um método isVendedor() no modelo User --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="small-box bg-purple">
                                <div class="inner">
                                    <h3>R$ {{ number_format($comissaoAcumuladaMes, 2, ',', '.') }}</h3>
                                    <p>Sua Comissão Acumulada do Mês ({{ Carbon\Carbon::createFromDate($ano, $mes)->monthName }}/{{ $ano }})</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <a href="{{ route('financeiro.contracheques.index') }}" class="small-box-footer">Ver Detalhes da Comissão <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @endif
                @endauth

            </div>
        </div>
    </div>

    @include('layouts.partials.footer')
</div>

@push('scripts')
<script>
    // Função para gerar cores aleatórias para gradientes
    function getRandomColor(alpha = 1) {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // Função para criar gradiente linear
    function createLinearGradient(ctx, chartArea, colorStart, colorEnd) {
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, colorEnd);
        gradient.addColorStop(1, colorStart);
        return gradient;
    }

    // Função para criar gradiente radial (exemplo básico)
    function createRadialGradient(ctx, chartArea, colorStart, colorMid, colorEnd) {
        // Para um gráfico de rosca, o centro é ideal
        const centerX = (chartArea.left + chartArea.right) / 2;
        const centerY = (chartArea.top + chartArea.bottom) / 2;
        const radius = Math.min(chartArea.width, chartArea.height) / 2;
        const gradient = ctx.createRadialGradient(centerX, centerY, 0, centerX, centerY, radius);
        gradient.addColorStop(0, colorStart);
        if (colorMid) gradient.addColorStop(0.5, colorMid);
        gradient.addColorStop(1, colorEnd);
        return gradient;
    }

    // Dados passados do Laravel para o JavaScript
    const dadosGraficoBarras = @json($dadosGraficoBarras);
    const dadosGraficoDoughnut = @json($dadosGraficoPizza); // Renomeado para Doughnut para clareza
    const dadosGraficoLinha = @json($dadosGraficoLinha);

    // Configurações de Animação Global
    const animationOptions = {
        duration: 1500, // Duração da animação em ms
        easing: 'easeOutQuad', // Tipo de easing
        delay: (context) => {
            let delay = 0;
            if (context.type === 'data' && context.mode === 'default' && !context.skipped) {
                delay = context.dataIndex * 150; // Atraso por item do dataset
            }
            return delay;
        },
        loop: false // Loop é mais complexo para datasets e pode ser irritante; deixaremos como false.
                   // Se quiser um loop para algo específico, precisa de lógica customizada.
    };

    // Plugin para gradientes e rótulos centrais (para Doughnut/Polar)
    const chartPlugins = [{
        beforeUpdate(chart) {
            // Aplica gradientes ao chart de barras
            if (chart.canvas.id === 'barChart') {
                chart.data.datasets.forEach((dataset, i) => {
                    const ctx = chart.canvas.getContext('2d');
                    const chartArea = chart.chartArea;
                    if (!chartArea) return; // Gráfico ainda não desenhado

                    if (dataset.label === 'Receitas') {
                        dataset.backgroundColor = createLinearGradient(ctx, chartArea, 'rgba(75, 192, 192, 1)', 'rgba(75, 192, 192, 0.4)');
                    } else if (dataset.label === 'Despesas') {
                        dataset.backgroundColor = createLinearGradient(ctx, chartArea, 'rgba(255, 99, 132, 1)', 'rgba(255, 99, 132, 0.4)');
                    }
                });
            }
            // Aplica gradientes ao chart de rosca
            if (chart.canvas.id === 'doughnutChart') {
                 chart.data.datasets.forEach((dataset) => {
                    const ctx = chart.canvas.getContext('2d');
                    const chartArea = chart.chartArea;
                    if (!chartArea) return;

                    // Para Doughnut, criamos um gradiente para cada segmento
                    // Isso é mais complexo com gradiente radial/linear por segmento,
                    // então para simplificar vamos manter cores sólidas com transparência,
                    // ou gerar gradientes individuais por slice se for estritamente necessário.
                    // Para fins de demonstração, o gradiente radial pode ser aplicado ao todo.
                    dataset.backgroundColor = dataset.backgroundColor.map((color, index) => {
                        const randomColorStart = getRandomColor(0.8);
                        const randomColorEnd = getRandomColor(0.4);
                        return createLinearGradient(ctx, chartArea, randomColorStart, randomColorEnd);
                        // Ou, para radial (mais complexo para cada slice individualmente):
                        // return createRadialGradient(ctx, chartArea, color, getRandomColor(0.8), getRandomColor(0.4));
                    });
                });
            }
            // Aplica gradientes ao chart de linha
            if (chart.canvas.id === 'lineChart') {
                 chart.data.datasets.forEach((dataset) => {
                    const ctx = chart.canvas.getContext('2d');
                    const chartArea = chart.chartArea;
                    if (!chartArea) return;

                    dataset.borderColor = createLinearGradient(ctx, chartArea, 'rgba(54, 162, 235, 1)', 'rgba(153, 102, 255, 1)');
                    dataset.pointBackgroundColor = dataset.borderColor; // Mantém a cor do ponto igual à borda
                });
            }
        },
        // Rótulos centrais para Doughnut (e Polar) - ajuste para texto dentro do Doughnut
        // Este plugin é mais avançado e pode precisar de uma biblioteca externa ou código customizado.
        // Por agora, focaremos nos tooltips ao passar o mouse.
    }];

    // --- Gráfico de Barras (Vertical Bar Chart) ---
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: dadosGraficoBarras,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: animationOptions, // Aplica as opções de animação
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                },
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 }).format(value);
                        }
                    }
                }
            }
        },
        plugins: chartPlugins // Aplica o plugin de gradiente
    });

    // --- Gráfico de Rosca (Doughnut Chart) ---
    const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
    const doughnutChart = new Chart(doughnutCtx, {
        type: 'doughnut',
        data: dadosGraficoDoughnut,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: animationOptions, // Aplica as opções de animação
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const percentage = (context.parsed / total * 100).toFixed(2) + '%';
                                label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed) + ` (${percentage})`;
                            }
                            return label;
                        }
                    }
                },
                legend: {
                    position: 'right', // Posição da legenda
                }
            },
        },
        plugins: chartPlugins // Aplica o plugin de gradiente
    });

    // --- Gráfico de Linha (Line Chart com Point Styling) ---
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(lineCtx, {
        type: 'line',
        data: dadosGraficoLinha,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: animationOptions, // Aplica as opções de animação
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                },
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: false, // Pode não começar em zero para saldos
                    ticks: {
                        callback: function(value, index, values) {
                            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 }).format(value);
                        }
                    }
                }
            }
        },
        plugins: chartPlugins // Aplica o plugin de gradiente
    });

    // Lógica para filtrar o ano/mês (já tratada pelo formulário POST na Blade)
    // Se desejar filtragem AJAX, isso seria um passo futuro.
</script>
@endpush
