@php
    $pageTitle = 'Vendas';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Vendas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.dashboard') }}">Financeiro</a></li>
                            <li class="breadcrumb-item active">Vendas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
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
                                <h3 class="card-title">Lista de Vendas</h3>
                                <div class="card-tools">
                                    <a href="{{ route('financeiro.vendas.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Venda (PDV)
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('financeiro.vendas.index') }}" method="GET" class="form-inline mb-3">
                                    <div class="form-group mr-3">
                                        <label for="status" class="mr-2">Status:</label>
                                        <select name="status" id="status" class="form-control form-control-sm">
                                            <option value="">Todos</option>
                                            @foreach($statusOptions as $key => $value)
                                                <option value="{{ $key }}" {{ $request->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="data_inicio" class="mr-2">Data Início:</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control form-control-sm" value="{{ $request->data_inicio }}">
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="data_fim" class="mr-2">Data Fim:</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control form-control-sm" value="{{ $request->data_fim }}">
                                    </div>
                                    <button type="submit" class="btn btn-info btn-sm mr-2">Filtrar</button>
                                    <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary btn-sm">Limpar Filtros</a>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Data Venda</th>
                                                <th>Valor Final</th>
                                                <th>Vendedor</th>
                                                <th>Comissão</th> {{-- Título da coluna --}}
                                                <th>Status</th>
                                                <th style="width: 150px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($vendas as $venda)
                                                <tr>
                                                    <td>{{ $venda->id }}</td>
                                                    <td>{{ $venda->data_venda->format('d/m/Y H:i') }}</td>
                                                    <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                                                    <td>{{ $venda->user->name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($venda->comissao_paga && $venda->comissao_percentual > 0)
                                                            R$ {{ number_format($venda->valor_final * ($venda->comissao_percentual / 100), 2, ',', '.') }}
                                                        @else
                                                            R$ 0,00
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $badgeClass = '';
                                                            switch ($venda->status) {
                                                                case 'concluida': $badgeClass = 'badge-success'; break;
                                                                case 'pendente': $badgeClass = 'badge-warning'; break;
                                                                case 'cancelada': $badgeClass = 'badge-danger'; break;
                                                                default: $badgeClass = 'badge-secondary'; break;
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($venda->status) }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('financeiro.vendas.show', $venda->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('financeiro.vendas.edit', $venda->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('financeiro.vendas.destroy', $venda->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta venda? Esta ação reativará as aves vendidas.');">
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
                                                    <td colspan="7" class="text-center">Nenhuma venda encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $vendas->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
