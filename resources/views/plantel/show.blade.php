@php
    $pageTitle = 'Detalhes do Plantel';
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
                        <h1>Detalhes do Plantel: {{ $plantel->identificacao_grupo }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('plantel.index') }}">Plantéis</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Informações do Plantel</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID:</label>
                                    <p>{{ $plantel->id }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Identificação do Grupo:</label>
                                    <p>{{ $plantel->identificacao_grupo }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Ave:</label>
                                    <p>{{ $plantel->tipoAve->nome ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Data de Formação:</label>
                                    <p>{{ $plantel->data_formacao->format('d/m/Y') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Inicial:</label>
                                    <p>{{ $plantel->quantidade_inicial }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Atual:</label>
                                    <p>{{ $quantidadeAtual }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Ativo:</label>
                                    <p>
                                        @if ($plantel->ativo)
                                            <span class="badge badge-success">Sim</span>
                                        @else
                                            <span class="badge badge-danger">Não</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p>{{ $plantel->observacoes ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Criado em:</label>
                                    <p>{{ $plantel->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p>{{ $plantel->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="{{ route('plantel.edit', $plantel->id) }}" class="btn btn-warning">Editar</a>
                                <a href="{{ route('plantel.index') }}" class="btn btn-secondary">Voltar à Lista</a>
                            </div>
                        </div>
                        <!-- /.card -->

                        <!-- Card de Movimentações do Plantel -->
                        <div class="card card-info mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Histórico de Movimentações</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                                <th>Data</th>
                                                <th>Observações</th>
                                                <th>Criado em</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($plantel->movimentacoes->sortByDesc('data_movimentacao') as $movimentacao)
                                                <tr>
                                                    <td>{{ $movimentacao->id }}</td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $movimentacao->tipo_movimentacao)) }}</td>
                                                    <td>{{ $movimentacao->quantidade }}</td>
                                                    <td>{{ $movimentacao->data_movimentacao->format('d/m/Y') }}</td>
                                                    <td>{{ $movimentacao->observacoes ?? 'N/A' }}</td>
                                                    <td>{{ $movimentacao->created_at->format('d/m/Y H:i:s') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Nenhuma movimentação para este plantel.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                      {{-- ... (seu código existente no plantel/show.blade.php) --}}

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="{{ route('plantel.edit', $plantel->id) }}" class="btn btn-warning">Editar Plantel</a>
                                <a href="{{ route('plantel.index') }}" class="btn btn-secondary">Voltar à Lista</a>
                                {{-- NOVO: Botão para adicionar movimentação --}}
                                <a href="{{ route('plantel.movimentacoes.create', ['plantel' => $plantel->id]) }}" class="btn btn-success float-right">
                                    <i class="fas fa-plus"></i> Adicionar Movimentação
                                </a>
                            </div>
                        </div>
                        <!-- /.card -->

                        {{-- NOVO: Card para listar movimentações específicas do plantel --}}
                        <div class="card card-info mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Histórico de Movimentações</h3>
                                <div class="card-tools">
                                    <a href="{{ route('movimentacoes-plantel.index', ['plantel_id' => $plantel->id]) }}" class="btn btn-tool btn-sm">
                                        <i class="fas fa-list"></i> Ver Todas
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                                <th>Data</th>
                                                <th>Observações</th>
                                                <th>Criado em</th>
                                                <th style="width: 100px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($plantel->movimentacoes->sortByDesc('data_movimentacao') as $movimentacao)
                                                <tr>
                                                    <td>{{ $movimentacao->id }}</td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $movimentacao->tipo_movimentacao)) }}</td>
                                                    <td>{{ $movimentacao->quantidade }}</td>
                                                    <td>{{ $movimentacao->data_movimentacao->format('d/m/Y') }}</td>
                                                    <td>{{ $movimentacao->observacoes ?? 'N/A' }}</td>
                                                    <td>{{ $movimentacao->created_at->format('d/m/Y H:i:s') }}</td>
                                                    <td>
                                                        <a href="{{ route('movimentacoes-plantel.show', $movimentacao->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('movimentacoes-plantel.edit', $movimentacao->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Nenhuma movimentação para este plantel.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
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
