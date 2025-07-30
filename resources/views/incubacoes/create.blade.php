@php
    $pageTitle = 'Cadastrar Nova Incubação';
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
                        <h1>Cadastrar Nova Incubação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('incubacoes.index') }}">Incubações</a></li>
                            <li class="breadcrumb-item active">Nova</li>
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
                                <h3 class="card-title">Dados da Nova Incubação</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="{{ route('incubacoes.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="lote_id">Lote de Ovos</label>
                                        <select name="lote_id" id="lote_id" class="form-control @error('lote_id') is-invalid @enderror" required>
                                            <option value="">Selecione o Lote</option>
                                            @foreach($lotes as $lote)
                                                <option value="{{ $lote->id }}" {{ old('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->identificacao_lote }}</option>
                                            @endforeach
                                        </select>
                                        @error('lote_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_ave_id">Tipo de Ave (Ovos)</label>
                                        <select name="tipo_ave_id" id="tipo_ave_id" class="form-control @error('tipo_ave_id') is-invalid @enderror" required>
                                            <option value="">Selecione o Tipo de Ave</option>
                                            @foreach($tiposAve as $tipo)
                                                <option value="{{ $tipo->id }}" {{ old('tipo_ave_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipo_ave_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="chocadeira">Chocadeira</label>
                                        <input type="text" name="chocadeira" id="chocadeira" class="form-control @error('chocadeira') is-invalid @enderror" value="{{ old('chocadeira') }}" placeholder="Nome ou ID da chocadeira" required>
                                        @error('chocadeira')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="quantidade_ovos">Quantidade de Ovos</label>
                                        <input type="number" name="quantidade_ovos" id="quantidade_ovos" class="form-control @error('quantidade_ovos') is-invalid @enderror" value="{{ old('quantidade_ovos') }}" min="1" required>
                                        @error('quantidade_ovos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="data_entrada_incubadora">Data de Entrada na Incubadora</label>
                                        <input type="date" name="data_entrada_incubadora" id="data_entrada_incubadora" class="form-control @error('data_entrada_incubadora') is-invalid @enderror" value="{{ old('data_entrada_incubadora', date('Y-m-d')) }}" required>
                                        @error('data_entrada_incubadora')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="data_prevista_eclosao">Data Prevista de Eclosão</label>
                                        <input type="date" name="data_prevista_eclosao" id="data_prevista_eclosao" class="form-control @error('data_prevista_eclosao') is-invalid @enderror" value="{{ old('data_prevista_eclosao') }}" required>
                                        @error('data_prevista_eclosao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Notas sobre a incubação...">{{ old('observacoes') }}</textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- Nova incubação é sempre ativa por padrão --}}
                                    <input type="hidden" name="ativo" value="1">
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Incubação</button>
                                    <a href="{{ route('incubacoes.index') }}" class="btn btn-secondary">Cancelar</a>
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
