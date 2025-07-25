@php
    $pageTitle = 'Detalhes da Reserva';
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
                        <h1>Detalhes da Reserva #{{ $reserva->numero_reserva }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.reservas.index') }}">Reservas</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informações da Reserva</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Número da Reserva:</strong> {{ $reserva->numero_reserva }}</p>
                                <p><strong>Data da Reserva:</strong> {{ $reserva->data_reserva->format('d/m/Y') }}</p>
                                <p><strong>Data Prevista de Entrega:</strong> {{ $reserva->data_prevista_entrega ? $reserva->data_prevista_entrega->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Data Vencimento Proposta:</strong> {{ $reserva->data_vencimento_proposta ? $reserva->data_vencimento_proposta->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Valor Total:</strong> R$ {{ number_format($reserva->valor_total, 2, ',', '.') }}</p>
                                <p><strong>Pagamento Parcial:</strong> R$ {{ number_format($reserva->pagamento_parcial, 2, ',', '.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Nome do Cliente:</strong> {{ $reserva->nome_cliente ?? 'N/A' }}</p>
                                <p><strong>Contato do Cliente:</strong> {{ $reserva->contato_cliente ?? 'N/A' }}</p>
                                <p><strong>Status:</strong>
                                    <span class="badge badge-{{
                                        $reserva->status == 'pendente' ? 'warning' :
                                        ($reserva->status == 'confirmada' ? 'info' :
                                        ($reserva->status == 'cancelada' ? 'danger' :
                                        ($reserva->status == 'convertida_venda' ? 'success' : 'secondary')))
                                    }}">
                                        {{ ['pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'cancelada' => 'Cancelada', 'convertida_venda' => 'Convertida em Venda'][$reserva->status] ?? $reserva->status }}
                                    </span>
                                </p>
                                <p><strong>Observações:</strong> {{ $reserva->observacoes ?? 'N/A' }}</p>
                                <p><strong>Criado em:</strong> {{ $reserva->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Última Atualização:</strong> {{ $reserva->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <hr>
                        <h4>Itens da Reserva</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Descrição do Item</th>
                                        <th>Ave (Matrícula)</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unitário</th>
                                        <th>Valor Total Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reserva->items as $item)
                                        <tr>
                                            <td>{{ $item->descricao_item }}</td>
                                            <td>
                                                @if ($item->ave)
                                                    <a href="{{ route('aves.show', $item->ave->id) }}">{{ $item->ave->matricula }} ({{ $item->ave->tipoAve->nome ?? 'N/A' }})</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $item->quantidade }}</td>
                                            <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($item->valor_total_item, 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Nenhum item nesta reserva.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($reserva->vendas->count() > 0)
                            <hr>
                            <h4>Vendas Associadas</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID da Venda</th>
                                            <th>Data da Venda</th>
                                            <th>Valor Final</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reserva->vendas as $venda)
                                            <tr>
                                                <td>{{ $venda->id }}</td>
                                                <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                                                <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                                                <td>{{ $venda->status }}</td>
                                                <td>
                                                    <a href="{{ route('financeiro.vendas.show', $venda->id) }}" class="btn btn-info btn-sm" title="Ver Venda">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                    <div class="card-footer">
                        <a href="{{ route('financeiro.reservas.index') }}" class="btn btn-secondary">Voltar</a>
                        <a href="{{ route('financeiro.reservas.edit', $reserva->id) }}" class="btn btn-primary">Editar</a>
                        @if ($reserva->status != 'convertida_venda' && $reserva->status != 'cancelada')
                            <form action="{{ route('financeiro.reservas.convertToVenda', $reserva->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Tem certeza que deseja converter esta reserva em uma venda? Esta ação é irreversível e irá inativar as aves associadas.')">
                                    <i class="fas fa-cash-register"></i> Converter para Venda
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('layouts.partials.footer')
</div>

@include('layouts.partials.scripts')
