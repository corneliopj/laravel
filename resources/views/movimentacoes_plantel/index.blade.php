@php
    $pageTitle = 'Listagem de Movimentações de Plantel';
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
                        <h1>
                            @if ($plantel)
                                Movimentações para Plantel: {{ $plantel->identificacao_grupo }}
                            @else
                                Listagem de Movimentações de Plantel
                            @endif
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('plantel.index') }}">Plantéis</a></li>
                            @if ($plantel)
                                <li class="breadcrumb-item"><a href="{{ route('plantel.show', $plantel->id) }}">Detalhes do Plantel</a></li>
                            @endif
                            <li class="breadcrumb-item active">Movimentações</li>
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
                                <h3 class="card-title">Movimentações Registradas</h3>
                                <div class="card-tools">
                                    <a href="{{ route('movimentacoes-plantel.create', ['plantel_id' => $plantel->id ?? '']) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Movimentação
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Plantel</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                                <th>Data</th>
                                                <th>Observações</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($movimentacoes as $movimentacao)
                                                <tr>
                                                    <td>{{ $movimentacao->id }}</td>
                                                    <td>
                                                        @if ($movimentacao->plantel)
                                                            <a href="{{ route('plantel.show', $movimentacao->plantel->id) }}">{{ $movimentacao->plantel->identificacao_grupo }}</a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $movimentacao->tipo_movimentacao)) }}</td>
                                                    <td>{{ $movimentacao->quantidade }}</td>
                                                    <td>{{ $movimentacao->data_movimentacao->format('d/m/Y') }}</td>
                                                    <td>{{ $movimentacao->observacoes ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('movimentacoes-plantel.show', $movimentacao->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('movimentacoes-plantel.edit', $movimentacao->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('movimentacoes-plantel.destroy', $movimentacao->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta movimentação?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Nenhuma movimentação encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                {{ $movimentacoes->links('pagination::bootstrap-4') }} {{-- Adiciona links de paginação --}}
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
