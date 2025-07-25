@php
    $pageTitle = 'Relatórios Financeiros';
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
                                <li class="breadcrumb-item active">Relatórios Financeiros</li>
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

                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Opções de Relatórios</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Relatório Detalhado de Transações</span>
                                            <span class="info-box-number">Visualize todas as receitas e despesas com filtros.</span>
                                            <a href="{{ route('financeiro.relatorios.transacoes') }}" class="btn btn-sm btn-primary mt-2">Gerar Relatório</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-chart-bar"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Relatório de Fluxo de Caixa Mensal</span>
                                            <span class="info-box-number">Análise de entradas e saídas por mês.</span>
                                            <a href="{{ route('financeiro.relatorios.fluxo_caixa') }}" class="btn btn-sm btn-success mt-2">Gerar Relatório</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-chart-pie"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Relatório por Categoria</span>
                                            <span class="info-box-number">Visualize totais de receitas ou despesas agrupados por categoria.</span>
                                            <a href="{{ route('financeiro.relatorios.por_categoria') }}" class="btn btn-sm btn-warning mt-2">Gerar Relatório</a>
                                        </div>
                                    </div>
                                </div>
                                {{-- Adicione mais opções de relatório aqui se necessário --}}
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
</body>
</html>
