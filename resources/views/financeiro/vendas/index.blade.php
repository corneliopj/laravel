@php
    $pageTitle = 'Listagem de Vendas';
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
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
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('financeiro.vendas.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Data Início</label>
                                        <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Data Fim</label>
                                        <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Comprador</label>
                                        <input type="text" name="comprador" class="form-control" value="{{ request('comprador') }}" list="compradores">
                                        <datalist id="compradores">
                                            @foreach($compradores as $comprador)
                                                <option value="{{ $comprador }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ordenar por</label>
                                        <select name="ordenar" class="form-control">
                                            <option value="recentes" {{ request('ordenar') == 'recentes' ? 'selected' : '' }}>Mais Recentes</option>
                                            <option value="antigas" {{ request('ordenar') == 'antigas' ? 'selected' : '' }}>Mais Antigas</option>
                                            <option value="valor_maior" {{ request('ordenar') == 'valor_maior' ? 'selected' : '' }}>Maior Valor</option>
                                            <option value="valor_menor" {{ request('ordenar') == 'valor_menor' ? 'selected' : '' }}>Menor Valor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary">Limpar</a>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Comprador</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Observação</th>
                                        <th style="width: 150px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($vendas as $venda)
                                        <tr>
                                            <td>{{ $venda->id }}</td>
                                            <td>{{ $venda->data_venda->format('d/m/Y H:i') }}</td>
                                            <td>{{ $venda->comprador ?? 'N/A' }}</td>
                                            <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                                            <td>
                                                @if ($venda->status == 'concluida')
                                                    <span class="badge badge-success">Concluída</span>
                                                @elseif ($venda->status == 'pendente')
                                                    <span class="badge badge-warning">Pendente</span>
                                                @else
                                                    <span class="badge badge-danger">Cancelada</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($venda->observacoes, 30) }}</td>
                                            <td>
                                                <a href="{{ route('financeiro.vendas.show', $venda->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('financeiro.vendas.edit', $venda->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('financeiro.vendas.destroy', $venda->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma venda encontrada</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $vendas->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@include('layouts.partials.scripts')
@include('layouts.partials.footer')