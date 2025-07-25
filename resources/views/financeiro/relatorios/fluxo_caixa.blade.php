@php
    $pageTitle = 'Relatório de Fluxo de Caixa Mensal';
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
                                <li class="breadcrumb-item"><a href="{{ route('financeiro.relatorios.index') }}">Relatórios Financeiros</a></li>
                                <li class="breadcrumb-item active">Fluxo de Caixa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conteúdo Principal --}}
            <div class="content">
                <div class="container-fluid">
                    {{-- Mensagens de sucesso/erro --}}
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

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Fluxo de Caixa por Mês (Últimos 12 Meses)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="fluxoCaixaChart" style="min-height: 400px; height: 400px; max-height: 400px; max-width: 100%;"></canvas>
                        </div>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Detalhes do Fluxo de Caixa</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Mês/Ano</th>
                                        <th>Total Receitas</th>
                                        <th>Total Despesas</th>
                                        <th>Saldo Mensal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($fluxoCaixaData as $data)
                                        <tr>
                                            <td>{{ $data['mes_ano'] }}</td>
                                            <td class="text-success">R$ {{ number_format($data['total_receitas'], 2, ',', '.') }}</td>
                                            <td class="text-danger">R$ {{ number_format($data['total_despesas'], 2, ',', '.') }}</td>
                                            <td class="{{ $data['saldo_mensal'] >= 0 ? 'text-primary' : 'text-warning' }}">
                                                R$ {{ number_format($data['saldo_mensal'], 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Nenhum dado de fluxo de caixa encontrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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

    {{-- Script do Chart.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(function () {
            var ctx = $('#fluxoCaixaChart').get(0).getContext('2d');
            var fluxoCaixaChart = new Chart(ctx, {
                type: 'bar', // Ou 'line' para uma visão de tendência
                data: {
                    labels: @json($labelsFluxoCaixa), // Ex: ['Jan/2023', 'Fev/2023', ...]
                    datasets: [
                        {
                            label: 'Receitas',
                            backgroundColor: 'rgba(40, 167, 69, 0.8)', // Verde para receitas
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1,
                            data: @json($dataReceitas), // Ex: [1000, 1200, ...]
                        },
                        {
                            label: 'Despesas',
                            backgroundColor: 'rgba(220, 53, 69, 0.8)', // Vermelho para despesas
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1,
                            data: @json($dataDespesas), // Ex: [500, 700, ...]
                        },
                        {
                            label: 'Saldo Mensal',
                            type: 'line', // Adiciona uma linha para o saldo
                            borderColor: 'rgba(0, 123, 255, 1)', // Azul para o saldo
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            fill: true,
                            data: @json($dataSaldo), // Ex: [500, 500, ...]
                            tension: 0.3 // Suaviza a linha
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: false, // Não empilha as barras
                            title: {
                                display: true,
                                text: 'Mês/Ano'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Valor (R$)'
                            },
                            ticks: {
                                callback: function(value, index, ticks) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
</body>
</html>
