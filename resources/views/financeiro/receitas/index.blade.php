@php
    $pageTitle = 'Receitas Financeiras';
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
                                <li class="breadcrumb-item active">Receitas Financeiras</li>
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
                            <h3 class="card-title">Lista de Receitas</h3>
                            <div class="card-tools">
                                <a href="{{ route('financeiro.receitas.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nova Receita
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Formulário de Filtro --}}
                            <form action="{{ route('financeiro.receitas.index') }}" method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_categoria">Filtrar por Categoria:</label>
                                            <select name="categoria_id" id="filter_categoria" class="form-control select2 rounded" style="width: 100%;">
                                                <option value="">Todas as Categorias</option>
                                                @foreach ($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_data_inicio">Data Início:</label>
                                            <input type="date" name="data_inicio" id="filter_data_inicio" class="form-control rounded" value="{{ request('data_inicio') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_data_fim">Data Fim:</label>
                                            <input type="date" name="data_fim" id="filter_data_fim" class="form-control rounded" value="{{ request('data_fim') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary rounded mr-2"><i class="fas fa-filter"></i> Filtrar</button>
                                        <a href="{{ route('financeiro.receitas.index') }}" class="btn btn-secondary rounded"><i class="fas fa-sync-alt"></i> Limpar</a>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Valor</th>
                                        <th>Data</th>
                                        <th>Observações</th> {{-- NOVO: Coluna para Observações --}}
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($receitas as $receita)
                                        <tr>
                                            <td>{{ $receita->id }}</td>
                                            <td>{{ $receita->descricao }}</td>
                                            <td>{{ $receita->categoria->nome ?? 'N/A' }}</td>
                                            <td>R$ {{ number_format($receita->valor, 2, ',', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($receita->data)->format('d/m/Y') }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($receita->observacoes, 50, '...') }}</td> {{-- NOVO: Exibe observações limitadas --}}
                                            <td>
                                                <a href="{{ route('financeiro.receitas.edit', $receita->id) }}" class="btn btn-info btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-delete-{{ $receita->id }}" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                {{-- Modal de Confirmação de Exclusão --}}
                                                <div class="modal fade" id="modal-delete-{{ $receita->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-delete-label-{{ $receita->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modal-delete-label-{{ $receita->id }}">Confirmar Exclusão</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Tem certeza que deseja excluir a receita <strong>{{ $receita->descricao }}</strong> no valor de <strong>R$ {{ number_format($receita->valor, 2, ',', '.') }}</strong>? Esta ação não pode ser desfeita.
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                <form action="{{ route('financeiro.receitas.destroy', $receita->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma receita encontrada.</td> {{-- Colspan ajustado para 7 colunas --}}
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

    <script>
        $(function () {
            // Inicializar Select2 para o filtro de categoria
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
</body>
</html>
