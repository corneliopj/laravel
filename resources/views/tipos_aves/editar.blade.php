@php
    $pageTitle = 'Editar Tipo de Ave';
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
                        <h1 class="m-0">Editar Tipo de Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tipos_aves.index') }}">Tipos de Aves</a></li>
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
                                <h3 class="card-title">Dados do Tipo de Ave</h3>
                            </div>
                            <form action="{{ route('tipos_aves.update', $tipoAve->id) }}" method="POST">
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

                                    <input type="hidden" name="id" id="id" value="{{ old('id', $tipoAve->id) }}">
                                    <div class="form-group">
                                        <label for="nome">Nome do Tipo de Ave</label>
                                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" id="nome" placeholder="Ex: Galo de Campina, Canário, Bicudo" value="{{ old('nome', $tipoAve->nome) }}" required autofocus>
                                        @error('nome')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    {{-- NOVO CAMPO: Tempo de Eclosão --}}
                                    <div class="form-group">
                                        <label for="tempo_eclosao">Tempo de Eclosão (dias)</label>
                                        <input type="number" name="tempo_eclosao" class="form-control @error('tempo_eclosao') is-invalid @enderror" id="tempo_eclosao" placeholder="Ex: 21 (para galinhas)" value="{{ old('tempo_eclosao', $tipoAve->tempo_eclosao) }}" min="1">
                                        @error('tempo_eclosao')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">Número de dias para a eclosão dos ovos deste tipo de ave.</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" {{ old('ativo', $tipoAve->ativo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ativo">Ativo</label>
                                            <small class="form-text text-muted">Marque se este tipo de ave está ativo para uso.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                    <a href="{{ route('tipos_aves.index') }}" class="btn btn-secondary">Cancelar</a>
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
