@php
    $pageTitle = 'Listar Reservas';
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listar Reservas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reservas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
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

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros de Reserva</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('financeiro.reservas.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Todos</option>
                                            @foreach ($statusOptions as $key => $value)
                                                <option value="{{ $key }}" {{ $request->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="data_inicio">Data Início</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ $request->data_inicio }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="data_fim">Data Fim</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ $request->data_fim }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                                    <a href="{{ route('financeiro.reservas.index') }}" class="btn btn-secondary">Limpar Filtros</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Reservas Registradas</h3>
                        <div class="card-tools">
                            <a href="{{ route('financeiro.reservas.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Nova Reserva
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Cliente</th>
                                        <th>Data Reserva</th>
                                        <th>Valor Total</th>
                                        <th>Pagamento Parcial</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reservas as $reserva)
                                        <tr>
                                            <td>{{ $reserva->numero_reserva }}</td>
                                            <td>{{ $reserva->nome_cliente ?? 'N/A' }}</td>
                                            <td>{{ $reserva->data_reserva->format('d/m/Y') }}</td>
                                            <td>R$ {{ number_format($reserva->valor_total, 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($reserva->pagamento_parcial, 2, ',', '.') }}</td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $reserva->status == 'pendente' ? 'warning' :
                                                    ($reserva->status == 'confirmada' ? 'info' :
                                                    ($reserva->status == 'cancelada' ? 'danger' :
                                                    ($reserva->status == 'convertida_venda' ? 'success' : 'secondary')))
                                                }}">
                                                    {{ $statusOptions[$reserva->status] ?? $reserva->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('financeiro.reservas.show', $reserva->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('financeiro.reservas.edit', $reserva->id) }}" class="btn btn-primary btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if ($reserva->status != 'convertida_venda' && $reserva->status != 'cancelada')
                                                    <form action="{{ route('financeiro.reservas.convertToVenda', $reserva->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Converter para Venda" onclick="return confirm('Tem certeza que deseja converter esta reserva em uma venda? Esta ação é irreversível e irá inativar as aves associadas.')">
                                                            <i class="fas fa-cash-register"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('financeiro.reservas.destroy', $reserva->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta reserva? As aves associadas serão liberadas.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma reserva encontrada.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $reservas->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('layouts.partials.footer')
</div>

@include('layouts.partials.scripts')
