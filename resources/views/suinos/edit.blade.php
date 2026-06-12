@extends('layouts.app')

@section('title', 'Editar Suíno')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Suíno: {{ $suino->matricula }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('suinos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Formulário de Atualização</h3>
                    </div>
                    <form action="{{ route('suinos.update', $suino->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="matricula">Matrícula</label>
                                    <input type="text" name="matricula" class="form-control @error('matricula') is-invalid @enderror" value="{{ old('matricula', $suino->matricula) }}" required>
                                    @error('matricula')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="tipo">Tipo</label>
                                    <select name="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                                        <option value="transitorio" {{ old('tipo', $suino->tipo) == 'transitorio' ? 'selected' : '' }}>Transitório</option>
                                        <option value="matriz" {{ old('tipo', $suino->tipo) == 'matriz' ? 'selected' : '' }}>Matriz</option>
                                    </select>
                                    @error('tipo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="lote_id">Lote</label>
                                    <select name="lote_id" class="form-control @error('lote_id') is-invalid @enderror">
                                        <option value="">Nenhum Lote</option>
                                        @foreach($lotes as $lote)
                                            <option value="{{ $lote->id }}" {{ old('lote_id', $suino->lote_id) == $lote->id ? 'selected' : '' }}>
                                                {{ $lote->identificacao_lote }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lote_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="variacao_id">Variação</label>
                                    <select name="variacao_id" class="form-control @error('variacao_id') is-invalid @enderror">
                                        <option value="">Nenhuma Variação</option>
                                        @foreach($variacoes as $variacao)
                                            <option value="{{ $variacao->id }}" {{ old('variacao_id', $suino->variacao_id) == $variacao->id ? 'selected' : '' }}>
                                                {{ $variacao->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('variacao_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="sexo">Sexo</label>
                                    <select name="sexo" class="form-control @error('sexo') is-invalid @enderror" required>
                                        <option value="Macho" {{ old('sexo', $suino->sexo) == 'Macho' ? 'selected' : '' }}>Macho</option>
                                        <option value="Fêmea" {{ old('sexo', $suino->sexo) == 'Fêmea' ? 'selected' : '' }}>Fêmea</option>
                                    </select>
                                    @error('sexo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="vendavel">Vendável?</label>
                                    <select name="vendavel" class="form-control @error('vendavel') is-invalid @enderror">
                                        <option value="1" {{ old('vendavel', $suino->vendavel) ? 'selected' : '' }}>Sim</option>
                                        <option value="0" {{ old('vendavel', $suino->vendavel) == 0 ? 'selected' : '' }}>Não</option>
                                    </select>
                                    @error('vendavel')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
