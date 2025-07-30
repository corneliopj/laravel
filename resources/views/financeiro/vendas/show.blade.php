    @php
        $pageTitle = 'Detalhes da Venda';
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
                            <h1>Detalhes da Venda: #{{ $venda->id }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('financeiro.vendas.index') }}">Vendas</a></li>
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
                        <div class="col-md-10 offset-md-1">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Informações da Venda</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>ID da Venda:</label>
                                        <p>{{ $venda->id }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Data da Venda:</label>
                                        <p>{{ $venda->data_venda->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Comprador:</label>
                                        <p>{{ $venda->comprador ?? 'Não informado' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações da Venda:</label>
                                        <p>{{ $venda->observacoes ?? 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Status:</label>
                                        <p>
                                            @if ($venda->status == 'concluida')
                                                <span class="badge badge-success">Concluída</span>
                                            @elseif ($venda->status == 'pendente')
                                                <span class="badge badge-warning">Pendente</span>
                                            @else
                                                <span class="badge badge-danger">Cancelada</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Valor Total da Venda:</label>
                                        <p>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Desconto:</label>
                                        <p>R$ {{ number_format($venda->desconto, 2, ',', '.') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Valor Final da Venda:</label>
                                        <p>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Método de Pagamento:</label>
                                        <p>{{ $venda->metodo_pagamento ?? 'Não informado' }}</p>
                                    </div>

                                    <hr>
                                    <h4>Itens da Venda</h4>
                                    @if ($venda->items->isEmpty())
                                        <p>Nenhum item registrado para esta venda.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Descrição</th>
                                                        <th>Tipo</th>
                                                        <th>Identificação</th>
                                                        <th>Qtd</th>
                                                        <th>Preço Unitário</th>
                                                        <th>Valor Total Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($venda->items as $item)
                                                        <tr>
                                                            <td>{{ $item->descricao_item }}</td>
                                                            <td>
                                                                @if ($item->ave_id)
                                                                    Ave Individual
                                                                @elseif ($item->plantel_id)
                                                                    Plantel
                                                                @else
                                                                    Genérico
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($item->ave_id)
                                                                    <a href="{{ route('aves.show', $item->ave_id) }}">{{ $item->ave->matricula ?? 'Ave Removida' }}</a>
                                                                @elseif ($item->plantel_id)
                                                                    <a href="{{ route('plantel.show', $item->plantel_id) }}">{{ $item->plantel->identificacao_grupo ?? 'Plantel Removido' }}</a>
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->quantidade }}</td>
                                                            <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                                            <td>R$ {{ number_format($item->valor_total_item, 2, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <div class="form-group mt-4">
                                        <label>Registrado em:</label>
                                        <p>{{ $venda->created_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Última Atualização:</label>
                                        <p>{{ $venda->updated_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a href="{{ route('financeiro.vendas.edit', $venda->id) }}" class="btn btn-warning">Editar</a>
                                    <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary">Voltar à Lista</a>
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
    