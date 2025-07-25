@php
    $pageTitle = 'Transações Recorrentes';
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
                        <h1 class="m-0">Transações Recorrentes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Transações Recorrentes</li>
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
                                <h3 class="card-title">Lista de Transações Recorrentes</h3>
                                <div class="card-tools">
                                    <a href="{{ route('financeiro.transacoes_recorrentes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Transação Recorrente
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('financeiro.transacoes_recorrentes.index') }}" method="GET" class="form-inline mb-3">
                                    <div class="form-group mr-3">
                                        <label for="tipo" class="mr-2">Tipo:</label>
                                        <select name="tipo" id="tipo" class="form-control form-control-sm">
                                            <option value="">Todos</option>
                                            @foreach($tipos as $key => $value)
                                                <option value="{{ $key }}" {{ $request->tipo == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="frequencia" class="mr-2">Frequência:</label>
                                        <select name="frequencia" id="frequencia" class="form-control form-control-sm">
                                            <option value="">Todas</option>
                                            @foreach($frequencias as $key => $value)
                                                <option value="{{ $key }}" {{ $request->frequencia == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-sm mr-2">Filtrar</button>
                                    <a href="{{ route('financeiro.transacoes_recorrentes.index') }}" class="btn btn-secondary btn-sm">Limpar Filtros</a>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Descrição</th>
                                                <th>Valor</th>
                                                <th>Categoria</th>
                                                <th>Tipo</th>
                                                <th>Frequência</th>
                                                <th>Início</th>
                                                <th>Fim</th>
                                                <th>Próximo Vencimento</th>
                                                <th>Última Geração</th>
                                                <th style="width: 150px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($transacoesRecorrentes as $transacao)
                                                <tr>
                                                    <td>{{ $transacao->id }}</td>
                                                    <td>{{ $transacao->description }}</td>
                                                    <td>R$ {{ number_format($transacao->value, 2, ',', '.') }}</td>
                                                    <td>{{ $transacao->categoria->nome ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $transacao->type == 'receita' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($transacao->type) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $frequencias[$transacao->frequency] ?? $transacao->frequency }}</td>
                                                    <td>{{ $transacao->start_date->format('d/m/Y') }}</td>
                                                    <td>{{ $transacao->end_date ? $transacao->end_date->format('d/m/Y') : 'N/A' }}</td>
                                                    <td>
                                                        @if ($transacao->next_due_date)
                                                            {{ $transacao->next_due_date->format('d/m/Y') }}
                                                            @if ($transacao->next_due_date->isPast() && !$transacao->end_date?->isPast())
                                                                <span class="badge badge-danger">Atrasado</span>
                                                            @endif
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $transacao->last_generated_date ? $transacao->last_generated_date->format('d/m/Y') : 'Nunca' }}</td>
                                                    <td>
                                                        <a href="{{ route('financeiro.transacoes_recorrentes.show', $transacao->id) }}" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('financeiro.transacoes_recorrentes.edit', $transacao->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('financeiro.transacoes_recorrentes.destroy', $transacao->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta transação recorrente?');">
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
                                                    <td colspan="11" class="text-center">Nenhuma transação recorrente encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
