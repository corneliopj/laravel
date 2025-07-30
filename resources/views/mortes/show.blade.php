@php
    $pageTitle = 'Detalhes da Morte';
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
                        <h1>Detalhes da Morte: #{{ $morte->id }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('mortes.index') }}">Mortes</a></li>
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
                                <h3 class="card-title">Informações da Morte</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID:</label>
                                    <p>{{ $morte->id }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Registro:</label>
                                    <p>
                                        @if ($morte->ave_id)
                                            Ave Individual
                                        @elseif ($morte->plantel_id)
                                            Plantel Agrupado
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                @if ($morte->ave_id)
                                    <div class="form-group">
                                        <label>Ave:</label>
                                        <p>
                                            <a href="{{ route('aves.show', $morte->ave_id) }}">{{ $morte->ave->matricula ?? 'Ave Removida' }}</a>
                                            ({{ $morte->ave->tipoAve->nome ?? 'N/A' }})
                                        </p>
                                    </div>
                                @elseif ($morte->plantel_id)
                                    <div class="form-group">
                                        <label>Plantel:</label>
                                        <p>
                                            <a href="{{ route('plantel.show', $morte->plantel_id) }}">{{ $morte->plantel->identificacao_grupo ?? 'Plantel Removido' }}</a>
                                            (Qtd. Atual: {{ $morte->plantel->quantidade_atual ?? 'N/A' }})
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Quantidade de Aves Mortas no Plantel:</label>
                                        <p>{{ $morte->quantidade_mortes_plantel }}</p>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>Data da Morte:</label>
                                    <p>{{ $morte->data_morte->format('d/m/Y') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Causa da Morte:</label>
                                    <p>{{ $morte->causa_morte ?? 'Não informada' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p>{{ $morte->observacoes ?? 'N/A' }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Registrado em:</label>
                                    <p>{{ $morte->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p>{{ $morte->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="{{ route('mortes.edit', $morte->id) }}" class="btn btn-warning">Editar</a>
                                <a href="{{ route('mortes.index') }}" class="btn btn-secondary">Voltar à Lista</a>
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
