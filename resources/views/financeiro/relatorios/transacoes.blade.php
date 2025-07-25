@php
    $pageTitle = 'Relatório Detalhado de Transações';
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
                                <li class="breadcrumb-item active">Transações</li>
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
                            <h3 class="card-title">Filtros do Relatório</h3>
                        </div>
                        <form action="{{ route('financeiro.relatorios.transacoes') }}" method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="tipo_transacao">Tipo de Transação:</label>
                                        <select name="tipo_transacao" id="tipo_transacao" class="form-control select2" style="width: 100%;">
                                            <option value="">Todas</option>
                                            <option value="receita" {{ request('tipo_transacao') == 'receita' ? 'selected' : '' }}>Receita</option>
                                            <option value="despesa" {{ request('tipo_transacao') == 'despesa' ? 'selected' : '' }}>Despesa</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="categoria_id">Categoria:</label>
                                        <select name="categoria_id" id="categoria_id" class="form-control select2" style="width: 100%;">
                                            <option value="">Todas as Categorias</option>
                                            @foreach ($categorias as $categoria)
                                                <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="data_inicio">Data Início:</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="data_fim">Data Fim:</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ request('data_fim') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Aplicar Filtros</button>
                                <a href="{{ route('financeiro.relatorios.transacoes') }}" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Limpar Filtros</a>
                            </div>
                        </form>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Transações Encontradas</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Valor</th>
                                        <th>Data</th>
                                        <th>Observações</th> {{-- Adicionado Observações --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transacoes as $transacao)
                                        <tr>
                                            <td>{{ $transacao->id }}</td>
                                            <td>
                                                @if ($transacao->type == 'receita')
                                                    <span class="badge badge-success">Receita</span>
                                                @else
                                                    <span class="badge badge-danger">Despesa</span>
                                                @endif
                                            </td>
                                            <td>{{ $transacao->descricao }}</td>
                                            <td>{{ $transacao->categoria->nome ?? 'N/A' }}</td>
                                            <td class="{{ $transacao->type == 'receita' ? 'text-success' : 'text-danger' }}">
                                                R$ {{ number_format($transacao->valor, 2, ',', '.') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($transacao->data)->format('d/m/Y') }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($transacao->observacoes, 50, '...') }}</td> {{-- Exibe observações limitadas --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma transação encontrada com os filtros aplicados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $transacoes->links('pagination::bootstrap-4') }} {{-- Se estiver usando paginação --}}
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
