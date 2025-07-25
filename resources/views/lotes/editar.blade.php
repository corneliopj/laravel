@php
    $pageTitle = 'Editar Lote';
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
                        <h1 class="m-0">Editar Lote</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('lotes.index') }}">Lotes</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6"> {{-- Reduzindo a largura do formulário --}}
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados do Lote</h3>
                            </div>
                            <form action="{{ route('lotes.update', $lote->id) }}" method="POST">
                                @csrf {{-- Token CSRF para segurança --}}
                                @method('PUT') {{-- Método HTTP PUT para atualização RESTful --}}
                                <div class="card-body">
                                    {{-- Exibe mensagens de erro de validação do Laravel --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <input type="hidden" name="id" id="id" value="{{ old('id', $lote->id) }}">
                                    <div class="form-group">
                                        <label for="identificacao_lote">Identificação do Lote</label>
                                        <input type="text" name="identificacao_lote" class="form-control @error('identificacao_lote') is-invalid @enderror" id="identificacao_lote" placeholder="Ex: Lote 2024A, Lote de Canários" value="{{ old('identificacao_lote', $lote->identificacao_lote) }}" required autofocus>
                                        @error('identificacao_lote')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" rows="3">{{ old('observacoes', $lote->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" {{ old('ativo', $lote->ativo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ativo">Ativo</label>
                                            <small class="form-text text-muted">Marque se este lote está ativo.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                    <a href="{{ route('lotes.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
