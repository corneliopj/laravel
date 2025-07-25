@php
    $pageTitle = 'Registrar Morte de Ave';
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
                        <h1 class="m-0">Registrar Morte de Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('aves.index') }}">Aves</a></li>
                            <li class="breadcrumb-item active">Registrar Morte</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informações da Ave</h3>
                            </div>
                            <div class="card-body">
                                @if (isset($ave))
                                    <p><strong>ID:</strong> {{ $ave->id }}</p>
                                    <p><strong>Matrícula:</strong> {{ $ave->matricula }}</p>
                                    <p><strong>Tipo:</strong> {{ $ave->tipoAve->nome ?? 'N/A' }}</p> {{-- Acessando relação tipoAve --}}
                                @else
                                    <p class="text-danger">Ave não encontrada.</p>
                                @endif
                            </div>
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Registro de Morte</h3>
                            </div>
                            <form action="{{ route('aves.storeDeath') }}" method="post">
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

                                    {{-- Exibe mensagens de erro (flash) --}}
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <input type="hidden" name="ave_id" value="{{ old('ave_id', $ave->id ?? '') }}">

                                    <div class="form-group">
                                        <label for="data_morte">Data da Morte</label>
                                        <input type="date" class="form-control" id="data_morte" name="data_morte" required value="{{ old('data_morte') }}">
                                        {{-- Exibe erro específico para data_morte --}}
                                        @error('data_morte')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="causa">Causa da Morte (Opcional)</label>
                                        <input type="text" class="form-control" id="causa" name="causa" value="{{ old('causa') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes') }}</textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Registrar Morte</button>
                                    <a href="{{ route('aves.index') }}" class="btn btn-secondary">Cancelar</a>
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
{{-- O include de scripts.php foi removido daqui e deve ser tratado dentro de head.blade.php ou footer.blade.php --}}
