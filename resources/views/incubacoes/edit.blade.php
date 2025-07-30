@php
    $pageTitle = 'Editar Incubação';
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
                        <h1>Editar Incubação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('incubacoes.index') }}">Incubações</a></li>
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
                                <h3 class="card-title">Dados da Incubação</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="{{ route('incubacoes.update', $incubacao->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="lote_id">Lote de Ovos</label>
                                        <select name="lote_id" id="lote_id" class="form-control @error('lote_id') is-invalid @enderror" required>
                                            <option value="">Selecione o Lote</option>
                                            @foreach($lotes as $lote)
                                                <option value="{{ $lote->id }}" {{ old('lote_id', $incubacao->lote_id) == $lote->id ? 'selected' : '' }}>{{ $lote->identificacao_lote }}</option>
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
                                            {{-- CORREÇÃO AQUI: $tiposAves para $tiposAve --}}
                                            @foreach($tiposAve as $tipo)
                                                <option value="{{ $tipo->id }}" {{ old('tipo_ave_id', $incubacao->tipo_ave_id) == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipo_ave_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="chocadeira">Chocadeira</label>
                                        <input type="text" name="chocadeira" id="chocadeira" class="form-control @error('chocadeira') is-invalid @enderror" value="{{ old('chocadeira', $incubacao->chocadeira) }}" placeholder="Nome ou ID da chocadeira" required>
                                        @error('chocadeira')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="quantidade_ovos">Quantidade de Ovos</label>
                                        <input type="number" name="quantidade_ovos" id="quantidade_ovos" class="form-control @error('quantidade_ovos') is-invalid @enderror" value="{{ old('quantidade_ovos', $incubacao->quantidade_ovos) }}" min="1" required>
                                        @error('quantidade_ovos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="data_entrada_incubadora">Data de Entrada na Incubadora</label>
                                        <input type="date" name="data_entrada_incubadora" id="data_entrada_incubadora" class="form-control @error('data_entrada_incubadora') is-invalid @enderror" value="{{ old('data_entrada_incubadora', $incubacao->data_entrada_incubadora->format('Y-m-d')) }}" required>
                                        @error('data_entrada_incubadora')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="data_prevista_eclosao">Data Prevista de Eclosão</label>
                                        <input type="date" name="data_prevista_eclosao" id="data_prevista_eclosao" class="form-control @error('data_prevista_eclosao') is-invalid @enderror" value="{{ old('data_prevista_eclosao', $incubacao->data_prevista_eclosao->format('Y-m-d')) }}" required>
                                        @error('data_prevista_eclosao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <hr>
                                    <h5>Resultados da Incubação (Preencher após a eclosão)</h5>
                                    <div class="form-group">
                                        <label for="quantidade_eclodidos">Quantidade de Eclodidos</label>
                                        <input type="number" name="quantidade_eclodidos" id="quantidade_eclodidos" class="form-control @error('quantidade_eclodidos') is-invalid @enderror" value="{{ old('quantidade_eclodidos', $incubacao->quantidade_eclodidos) }}" min="0">
                                        @error('quantidade_eclodidos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="quantidade_inferteis">Quantidade de Inférteis</label>
                                        <input type="number" name="quantidade_inferteis" id="quantidade_inferteis" class="form-control @error('quantidade_inferteis') is-invalid @enderror" value="{{ old('quantidade_inferteis', $incubacao->quantidade_inferteis) }}" min="0">
                                        @error('quantidade_inferteis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="quantidade_mortos">Quantidade de Mortos (no ovo)</label>
                                        <input type="number" name="quantidade_mortos" id="quantidade_mortos" class="form-control @error('quantidade_mortos') is-invalid @enderror" value="{{ old('quantidade_mortos', $incubacao->quantidade_mortos) }}" min="0">
                                        @error('quantidade_mortos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="quantidade_infectados">Quantidade de Infectados</label>
                                        <input type="number" name="quantidade_infectados" id="quantidade_infectados" class="form-control @error('quantidade_infectados') is-invalid @enderror" value="{{ old('quantidade_infectados', $incubacao->quantidade_infectados) }}" min="0">
                                        @error('quantidade_infectados')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Notas sobre a incubação...">{{ old('observacoes', $incubacao->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="ativo">Incubação Ativa</label>
                                        <div class="form-check">
                                            <input type="checkbox" name="ativo" id="ativo" class="form-check-input" value="1" {{ old('ativo', $incubacao->ativo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ativo">Marque se a incubação ainda está em andamento.</label>
                                        </div>
                                        @error('ativo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Atualizar Incubação</button>
                                    <a href="{{ route('incubacoes.show', $incubacao->id) }}" class="btn btn-secondary">Cancelar</a>
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
