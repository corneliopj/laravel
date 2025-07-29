@php
    $pageTitle = 'Editar Plantel';
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
                        <h1>Editar Plantel: {{ $plantel->identificacao_grupo }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('plantel.index') }}">Plantéis</a></li>
                            <li class="breadcrumb-item active">Editar</li>
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
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Editar Dados do Plantel</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="{{ route('plantel.update', $plantel->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="tipo_ave_id">Tipo de Ave</label>
                                        <select name="tipo_ave_id" id="tipo_ave_id" class="form-control @error('tipo_ave_id') is-invalid @enderror" required>
                                            <option value="">Selecione o Tipo de Ave</option>
                                            @foreach($tiposAves as $tipo)
                                                <option value="{{ $tipo->id }}" {{ old('tipo_ave_id', $plantel->tipo_ave_id) == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipo_ave_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="identificacao_grupo">Identificação do Grupo</label>
                                        <input type="text" name="identificacao_grupo" id="identificacao_grupo" class="form-control @error('identificacao_grupo') is-invalid @enderror" value="{{ old('identificacao_grupo', $plantel->identificacao_grupo) }}" placeholder="Ex: Codornas Q1 2025" required>
                                        @error('identificacao_grupo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="data_formacao">Data de Formação</label>
                                        <input type="date" name="data_formacao" id="data_formacao" class="form-control @error('data_formacao') is-invalid @enderror" value="{{ old('data_formacao', $plantel->data_formacao->format('Y-m-d')) }}" required>
                                        @error('data_formacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- A quantidade inicial não é editável diretamente aqui, pois é controlada por movimentações --}}
                                    <div class="form-group">
                                        <label for="ativo">Ativo</label>
                                        <div class="form-check">
                                            <input type="checkbox" name="ativo" id="ativo" class="form-check-input" value="1" {{ old('ativo', $plantel->ativo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ativo">Plantel está ativo</label>
                                        </div>
                                        @error('ativo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Notas sobre o plantel...">{{ old('observacoes', $plantel->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Atualizar Plantel</button>
                                    <a href="{{ route('plantel.show', $plantel->id) }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
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
