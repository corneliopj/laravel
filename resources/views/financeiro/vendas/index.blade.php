@php
    $pageTitle = 'Listagem de Vendas';
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
                        <h1>Listagem de Vendas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Vendas</li>
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
                                <h3 class="card-title">Registros de Venda</h3>
                                <div class="card-tools">
                                    <a href="{{ route('financeiro.vendas.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Registrar Nova Venda
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
                                                <th>Data da Venda</th>
                                                <th>Comprador</th>
                                                <th>Valor Total</th>
                                                <th>Status</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($vendas as $venda)
                                                <tr>
                                                    <td>{{ $venda->id }}</td>
                                                    <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                                                    <td>{{ $venda->comprador ?? 'N/A' }}</td>
                                                    <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td> {{-- Exibir valor_final aqui --}}
                                                    <td>
                                                        @if ($venda->status == 'concluida')
                                                            <span class="badge badge-success">Concluída</span>
                                                        @elseif ($venda->status == 'pendente')
                                                            <span class="badge badge-warning">Pendente</span>
                                                        @else
                                                            <span class="badge badge-danger">Cancelada</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('financeiro.vendas.show', $venda->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('financeiro.vendas.edit', $venda->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('financeiro.vendas.destroy', $venda->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este registro de venda? Todas as aves/plantéis associados serão reativados/revertidos.');">
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
                                                    <td colspan="6" class="text-center">Nenhum registro de venda encontrado.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                {{ $vendas->links('pagination::bootstrap-4') }}
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
