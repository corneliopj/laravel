@php
    $pageTitle = 'Detalhes da Incubação';
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
                        <h1>Detalhes da Incubação: #{{ $incubacao->id }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('incubacoes.index') }}">Incubações</a></li>
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
                    <div class="col-md-8 offset-md-2">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Informações da Incubação</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID da Incubação:</label>
                                    <p>{{ $incubacao->id }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Ave:</label>
                                    <p>{{ $incubacao->tipoAve->nome ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Lote de Ovos:</label>
                                    <p>{{ $incubacao->lote->identificacao_lote ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Postura de Ovo:</label>
                                    {{-- CORREÇÃO AQUI: Usar optional() e data_inicio_postura --}}
                                    <p>{{ optional($incubacao->posturaOvo)->data_inicio_postura?->format('d/m/Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Data de Entrada na Incubadora:</label>
                                    <p>{{ $incubacao->data_entrada_incubadora->format('d/m/Y') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Data Prevista de Eclosão:</label>
                                    <p>{{ $incubacao->data_prevista_eclosao->format('d/m/Y') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade de Ovos:</label>
                                    <p>{{ $incubacao->quantidade_ovos }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Eclodidos:</label>
                                    <p>{{ $incubacao->quantidade_eclodidos ?? 'Não informado' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Infértil:</label>
                                    <p>{{ $incubacao->quantidade_inferteis ?? 'Não informado' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Infectados:</label>
                                    <p>{{ $incubacao->quantidade_infectados ?? 'Não informado' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Mortos:</label>
                                    <p>{{ $incubacao->quantidade_mortos ?? 'Não informado' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Chocadeira:</label>
                                    <p>{{ $incubacao->chocadeira ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p>{{ $incubacao->observacoes ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Status:</label>
                                    <p>
                                        @if ($incubacao->ativo)
                                            <span class="badge badge-success">Ativo</span>
                                        @else
                                            <span class="badge badge-danger">Inativo</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label>Registrado em:</label>
                                    <p>{{ $incubacao->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p>{{ $incubacao->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="{{ route('incubacoes.edit', $incubacao->id) }}" class="btn btn-warning">Editar</a>
                                <a href="{{ route('incubacoes.index') }}" class="btn btn-secondary">Voltar à Lista</a>
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
