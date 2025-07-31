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
                                <h3 class="card-title">Registros de Incubação</h3>
                                <div class="card-tools">
                                    <a href="{{ route('incubacoes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Incubação
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
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

                                {{-- Formulário de Filtro --}}
                                <form action="{{ route('incubacoes.index') }}" method="GET" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">Todos</option>
                                                    @foreach($statusOptions as $key => $value)
                                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tipo_ave_id">Tipo de Ave</label>
                                                <select name="tipo_ave_id" id="tipo_ave_id" class="form-control">
                                                    <option value="">Todos</option>
                                                    @foreach($tiposAves as $tipoAve)
                                                        <option value="{{ $tipoAve->id }}" {{ request('tipo_ave_id') == $tipoAve->id ? 'selected' : '' }}>{{ $tipoAve->nome }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_entrada_inicio">Data Entrada (Início)</label>
                                                <input type="date" name="data_entrada_inicio" id="data_entrada_inicio" class="form-control" value="{{ request('data_entrada_inicio') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_entrada_fim">Data Entrada (Fim)</label>
                                                <input type="date" name="data_entrada_fim" id="data_entrada_fim" class="form-control" value="{{ request('data_entrada_fim') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-primary">Filtrar</button>
                                            <a href="{{ route('incubacoes.index') }}" class="btn btn-secondary">Limpar Filtros</a>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo de Ave</th>
                                                <th>Lote de Ovos</th>
                                                <th>Postura de Ovo</th>
                                                <th>Data Entrada</th>
                                                <th>Data Prevista Eclosão</th>
                                                <th>Qtd. Ovos</th>
                                                <th>Qtd. Eclodidos</th>
                                                <th>Chocadeira</th>
                                                <th>Status</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($incubacoes as $incubacao)
                                                <tr>
                                                    <td>{{ $incubacao->id }}</td>
                                                    <td>{{ $incubacao->tipoAve->nome ?? 'N/A' }}</td>
                                                    <td>{{ $incubacao->lote->identificacao_lote ?? 'N/A' }}</td>
                                                    {{-- CORREÇÃO AQUI: Usar operador de coalescência nula --}}
                                                    <td>{{ $incubacao->posturaOvo->data_postura->format('d/m/Y') ?? 'N/A' }}</td>
                                                    <td>{{ $incubacao->data_entrada_incubadora->format('d/m/Y') }}</td>
                                                    <td>{{ $incubacao->data_prevista_eclosao->format('d/m/Y') }}</td>
                                                    <td>{{ $incubacao->quantidade_ovos }}</td>
                                                    <td>{{ $incubacao->quantidade_eclodidos ?? '0' }}</td>
                                                    <td>{{ $incubacao->chocadeira ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($incubacao->ativo)
                                                            <span class="badge badge-success">Ativo</span>
                                                        @else
                                                            <span class="badge badge-danger">Inativo</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('incubacoes.show', $incubacao->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('incubacoes.edit', $incubacao->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('incubacoes.ficha', $incubacao->id) }}" class="btn btn-secondary btn-sm" title="Ficha de Incubação" target="_blank">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        <form action="{{ route('incubacoes.destroy', $incubacao->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja inativar esta incubação?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Inativar">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center">Nenhuma incubação encontrada.</td>
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
