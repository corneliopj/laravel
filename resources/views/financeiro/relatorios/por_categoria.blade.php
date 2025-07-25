@php
    $pageTitle = 'Relatório por Categoria (' . ucfirst($tipo) . ')';
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
                                <li class="breadcrumb-item active">Por Categoria</li>
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
                            <h3 class="card-title">Filtros do Relatório por Categoria</h3>
                        </div>
                        <form action="{{ route('financeiro.relatorios.por_categoria') }}" method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="tipo">Tipo:</label>
                                        <select name="tipo" id="tipo" class="form-control select2" style="width: 100%;" onchange="this.form.submit()">
                                            <option value="receita" {{ $tipo == 'receita' ? 'selected' : '' }}>Receita</option>
                                            <option value="despesa" {{ $tipo == 'despesa' ? 'selected' : '' }}>Despesa</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="data_inicio">Data Início:</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ $dataInicio->format('Y-m-d') }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="data_fim">Data Fim:</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ $dataFim->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Aplicar Filtros</button>
                                <a href="{{ route('financeiro.relatorios.por_categoria') }}" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Limpar Filtros</a>
                            </div>
                        </form>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Totais por Categoria</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Categoria</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dadosPorCategoria as $dado)
                                        <tr>
                                            <td>{{ $dado->categoria->nome ?? 'N/A' }}</td>
                                            <td class="{{ $tipo == 'receita' ? 'text-success' : 'text-danger' }}">
                                                R$ {{ number_format($dado->total, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">Nenhum dado encontrado para o período e tipo selecionados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total Geral</th>
                                        <th class="{{ $tipo == 'receita' ? 'text-success' : 'text-danger' }}">
                                            R$ {{ number_format($totalGeral, 2, ',', '.') }}
                                        </th>
                                    </tr>
                                </tfoot>
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

    <script>
        $(function () {
            // Inicializar Select2 para os filtros
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
</body>
</html>
