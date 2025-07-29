@php
    $pageTitle = 'Detalhes da Movimentação de Plantel';
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
                        <h1>Detalhes da Movimentação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('plantel.index') }}">Plantéis</a></li>
                            @if ($movimentacaoPlantel->plantel)
                                <li class="breadcrumb-item"><a href="{{ route('plantel.show', $movimentacaoPlantel->plantel->id) }}">Detalhes do Plantel</a></li>
                            @endif
                            <li class="breadcrumb-item active">Detalhes da Movimentação</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Informações da Movimentação</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID:</label>
                                    <p>{{ $movimentacaoPlantel->id }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Plantel:</label>
                                    <p>
                                        @if ($movimentacaoPlantel->plantel)
                                            <a href="{{ route('plantel.show', $movimentacaoPlantel->plantel->id) }}">{{ $movimentacaoPlantel->plantel->identificacao_grupo }}</a>
                                            (Qtd. Atual: {{ $movimentacaoPlantel->plantel->quantidade_atual }})
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Movimentação:</label>
                                    <p>{{ ucfirst(str_replace('_', ' ', $movimentacaoPlantel->tipo_movimentacao)) }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade:</label>
                                    <p>{{ $movimentacaoPlantel->quantidade }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Data da Movimentação:</label>
                                    <p>{{ $movimentacaoPlantel->data_movimentacao->format('d/m/Y') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p>{{ $movimentacaoPlantel->observacoes ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Criado em:</label>
                                    <p>{{ $movimentacaoPlantel->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p>{{ $movimentacaoPlantel->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="{{ route('movimentacoes-plantel.edit', $movimentacaoPlantel->id) }}" class="btn btn-warning">Editar</a>
                                <a href="{{ route('movimentacoes-plantel.index', ['plantel_id' => $movimentacaoPlantel->plantel_id]) }}" class="btn btn-secondary">Voltar às Movimentações</a>
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
