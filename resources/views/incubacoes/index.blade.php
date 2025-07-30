@php
    $pageTitle = 'Listagem de Incubações';
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listagem de Incubações</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Incubações</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Incubações Cadastradas</h3>
                                <div class="card-tools">
                                    <a href="{{ route('incubacoes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Incubação
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('incubacoes.index') }}" method="GET" class="form-inline mb-3">
                                    <label for="status" class="mr-2">Status:</label>
                                    <select name="status" id="status" class="form-control form-control-sm mr-2">
                                        <option value="">Todos</option>
                                        <option value="ativo" {{ $request->status == 'ativo' ? 'selected' : '' }}>Ativas</option>
                                        <option value="inativo" {{ $request->status == 'inativo' ? 'selected' : '' }}>Inativas</option>
                                    </select>

                                    <label for="data_entrada_inicio" class="mr-2">Entrada de:</label>
                                    <input type="date" name="data_entrada_inicio" id="data_entrada_inicio" class="form-control form-control-sm mr-2" value="{{ $request->data_entrada_inicio }}">

                                    <label for="data_entrada_fim" class="mr-2">Entrada até:</label>
                                    <input type="date" name="data_entrada_fim" id="data_entrada_fim" class="form-control form-control-sm mr-2" value="{{ $request->data_entrada_fim }}">

                                    <label for="tipo_ave_id" class="mr-2">Tipo de Ave:</label>
                                    <select name="tipo_ave_id" id="tipo_ave_id" class="form-control form-control-sm mr-2">
                                        <option value="">Todos</option>
                                        @foreach($tiposAve as $tipo)
                                            <option value="{{ $tipo->id }}" {{ $request->tipo_ave_id == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="btn btn-info btn-sm">Filtrar</button>
                                    <a href="{{ route('incubacoes.index') }}" class="btn btn-secondary btn-sm ml-2">Limpar Filtros</a>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Lote Ovos</th>
                                                <th>Tipo Ave</th>
                                                <th>Chocadeira</th>
                                                <th>Qtd. Ovos</th>
                                                <th>Entrada</th>
                                                <th>Previsão Eclosão</th>
                                                <th>Ativo</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($incubacoes as $incubacao)
                                                <tr>
                                                    <td>{{ $incubacao->id }}</td>
                                                    <td>{{ $incubacao->lote->identificacao_lote ?? 'N/A' }}</td>
                                                    <td>{{ $incubacao->tipoAve->nome ?? 'N/A' }}</td>
                                                    <td>{{ $incubacao->chocadeira }}</td>
                                                    <td>{{ $incubacao->quantidade_ovos }}</td>
                                                    <td>{{ $incubacao->data_entrada_incubadora->format('d/m/Y') }}</td>
                                                    <td>{{ $incubacao->data_prevista_eclosao->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if ($incubacao->ativo)
                                                            <span class="badge badge-success">Sim</span>
                                                        @else
                                                            <span class="badge badge-danger">Não</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('incubacoes.show', $incubacao->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('incubacoes.edit', $incubacao->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('incubacoes.destroy', $incubacao->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja inativar esta incubação?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Inativar">
                                                                <i class="fas fa-times-circle"></i> {{-- Ícone para Inativar --}}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Nenhuma incubação encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                {{ $incubacoes->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('layouts.partials.scripts')
    @include('layouts.partials.footer')
</div>
<!-- ./wrapper -->
