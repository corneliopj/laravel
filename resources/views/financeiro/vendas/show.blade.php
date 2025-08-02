@php
    $pageTitle = 'Detalhes da Venda #' . $venda->id;
@endphp

@include('layouts.partials.head')

<style>
    .nota-fiscal {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .cabecalho {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }
    .logo {
        max-width: 150px;
        margin-bottom: 10px;
    }
    .detalhes-venda {
        margin-bottom: 30px;
    }
    .table-itens {
        width: 100%;
        margin-bottom: 20px;
    }
    .table-itens th {
        background: #f8f9fa;
        text-align: left;
        padding: 8px;
    }
    .table-itens td {
        padding: 8px;
        border-bottom: 1px solid #eee;
    }
    .totais {
        text-align: right;
        margin-top: 20px;
    }
</style>

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $pageTitle }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.vendas.index') }}">Vendas</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="nota-fiscal">
                    <div class="cabecalho">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
                        <h2>Criatório Coroné & Agente Resolve - MEI</h2>
                        <p>CNPJ 19.173.619/0001-26</p>
                        <p>Rua Belo Horizonte, 2634 - Centro - Santa Luzia d' Oeste - RO, CEP 76.950-000</p>
                        <h3>NOTA DE VENDA - MEI</h3>
                    </div>

                    <div class="detalhes-venda">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Número:</strong> {{ $venda->id }}</p>
                                <p><strong>Data:</strong> {{ $venda->data_venda->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Comprador:</strong> {{ $venda->comprador }}</p>
                                <p><strong>Status:</strong> 
                                    @if ($venda->status == 'concluida')
                                        <span class="badge badge-success">Concluída</span>
                                    @elseif ($venda->status == 'pendente')
                                        <span class="badge badge-warning">Pendente</span>
                                    @else
                                        <span class="badge badge-danger">Cancelada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <table class="table-itens">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Descrição</th>
                                <th>Qtd</th>
                                <th>Valor Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venda->itens as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item['descricao'] }}</td>
                                <td>{{ $item['quantidade'] }}</td>
                                <td>R$ {{ number_format($item['preco_unitario'], 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="totais">
                        <p><strong>Subtotal:</strong> R$ {{ number_format($venda->valor_final + $venda->desconto, 2, ',', '.') }}</p>
                        <p><strong>Desconto:</strong> R$ {{ number_format($venda->desconto, 2, ',', '.') }}</p>
                        <h4><strong>Total:</strong> R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</h4>
                    </div>

                    <div class="observacoes mt-4">
                        <p><strong>Observações:</strong></p>
                        <p>{{ $venda->observacoes ?? 'Nenhuma observação registrada.' }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary">
                            Voltar
                        </a>
                        <a href="{{ route('financeiro.vendas.edit', $venda->id) }}" class="btn btn-primary">
                            Editar
                        </a>
                        <button onclick="window.print()" class="btn btn-success">
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@include('layouts.partials.scripts')