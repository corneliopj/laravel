@php
    $pageTitle = 'Adicionar Nova Variação';
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
                        <h1 class="m-0">Adicionar Nova Variação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('variacoes.index') }}">Variações</a></li>
                            <li class="breadcrumb-item active">Adicionar</li>
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
                                <h3 class="card-title">Dados da Variação</h3>
                            </div>
                            <form action="{{ route('variacoes.store') }}" method="POST">
                                @csrf {{-- Token CSRF para segurança --}}
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

                                    <div class="form-group">
                                        <label for="nome">Nome da Variação</label>
                                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" id="nome" placeholder="Ex: Carijó, Black, Bordô" value="{{ old('nome') }}" required autofocus>
                                        @error('nome')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_ave_id">Tipo de Ave</label>
                                        <select name="tipo_ave_id" id="tipo_ave_id" class="form-control @error('tipo_ave_id') is-invalid @enderror" required>
                                            <option value="">-- Selecione o Tipo de Ave --</option>
                                            @foreach ($tiposAves as $tipo)
                                                <option value="{{ $tipo->id }}" {{ old('tipo_ave_id') == $tipo->id ? 'selected' : '' }}>
                                                    {{ $tipo->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo_ave_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ativo">Ativo</label>
                                            <small class="form-text text-muted">Marque se esta variação está ativa.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Variação</button>
                                    <a href="{{ route('variacoes.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.partials.scripts')
    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>
