@php
    $pageTitle = 'Validar Certidão de Ave';
@endphp

{{-- Inclui o partial head (contém os meta tags, estilos CSS, etc.) --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')

    {{-- Não inclui o sidebar, pois é uma página para usuário externo --}}

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Validar Certidão</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">Validar Certidão</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 offset-md-3"> {{-- Centraliza o formulário --}}
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Verificar Certidão</h3>
                            </div>
                            <form action="{{ route('certidao.validar.post') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    {{-- Mensagens de sucesso/erro --}}
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="matricula">Matrícula da Ave</label>
                                        <input type="text" class="form-control @error('matricula') is-invalid @enderror" id="matricula" name="matricula" value="{{ old('matricula') }}" required>
                                        @error('matricula')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="codigo_validacao">Código de Validação</label>
                                        <input type="text" class="form-control @error('codigo_validacao') is-invalid @enderror" id="codigo_validacao" name="codigo_validacao" value="{{ old('codigo_validacao') }}" required>
                                        <small class="form-text text-muted">Formato: XXXXX-YYYYY</small>
                                        @error('codigo_validacao')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Validar Certidão</button>
                                    <a href="{{ url('/') }}" class="btn btn-secondary">Voltar para Home</a>
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
