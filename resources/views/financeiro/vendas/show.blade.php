@php
    $pageTitle = 'Detalhes da Venda';
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
                        <h1 class="m-0">Detalhes da Venda</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.vendas.index') }}">Vendas</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-10">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Venda #{{ $venda->id }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>ID da Venda:</strong> {{ $venda->id }}</p> {{-- Usando ID --}}
                                        <p><strong>Data da Venda:</strong> {{ $venda->data_venda->format('d/m/Y H:i') }}</p>
                                        <p><strong>Método de Pagamento:</strong> {{ $venda->metodo_pagamento ?? 'Não Informado' }}</p>
                                        <p><strong>Status:</strong>
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
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Valor Total (Bruto):</strong> R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</p>
                                        <p><strong>Desconto:</strong> R$ {{ number_format($venda->desconto, 2, ',', '.') }}</p>
                                        <p><strong>Valor Final:</strong> R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p><strong>Observações:</strong> {{ $venda->observacoes ?? 'N/A' }}</p>

                                <hr>
                                <h4>Informações de Comissão</h4>
                                @if ($venda->user)
                                    <p><strong>Vendedor:</strong> {{ $venda->user->name }}</p>
                                    <p><strong>Percentual de Comissão:</strong> {{ number_format($venda->comissao_percentual, 2, ',', '.') }}%</p>
                                    @if ($venda->despesaComissao)
                                        <p><strong>Despesa de Comissão Gerada:</strong>
                                            <a href="{{ route('financeiro.despesas.show', $venda->despesaComissao->id) }}">Ver Despesa #{{ $venda->despesaComissao->id }}</a> (R$ {{ number_format($venda->despesaComissao->valor, 2, ',', '.') }})
                                        </p>
                                    @else
                                        <p><strong>Despesa de Comissão:</strong> Não gerada ou removida.</p>
                                    @endif
                                @else
                                    <p>Nenhuma comissão associada a esta venda.</p>
                                @endif

                                <hr>
                                <h4>Itens da Venda</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Ave (Matrícula)</th>
                                                <th>Qtd</th>
                                                <th>Preço Unit.</th>
                                                <th>Total Item</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($venda->vendaItems as $item)
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
                                                    <td colspan="5" class="text-center">Nenhum item nesta venda.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('financeiro.vendas.edit', $venda->id) }}" class="btn btn-warning">Editar Venda</a>
                                <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary">Voltar para a Lista</a>
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
