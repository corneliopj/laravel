@php
    $pageTitle = 'Editar Acasalamento';
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
                        <h1 class="m-0">Editar Acasalamento</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('acasalamentos.index') }}">Acasalamentos</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8"> {{-- Aumentando a largura do formulário --}}
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados do Acasalamento</h3>
                            </div>
                            <form action="{{ route('acasalamentos.update', $acasalamento->id) }}" method="POST">
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

                                    <input type="hidden" name="id" id="id" value="{{ old('id', $acasalamento->id) }}">
                                    <div class="form-group">
                                        <label>Macho (Ave)</label>
                                        {{-- Apenas exibe o macho, não permite alterar --}}
                                        <p class="form-control-static">{{ $acasalamento->macho->matricula ?? 'N/A' }} ({{ $acasalamento->macho->tipoAve->nome ?? 'N/A' }})</p>
                                        <input type="hidden" name="macho_id" value="{{ $acasalamento->macho_id }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Fêmea (Ave)</label>
                                        {{-- Apenas exibe a fêmea, não permite alterar --}}
                                        <p class="form-control-static">{{ $acasalamento->femea->matricula ?? 'N/A' }} ({{ $acasalamento->femea->tipoAve->nome ?? 'N/A' }})</p>
                                        <input type="hidden" name="femea_id" value="{{ $acasalamento->femea_id }}">
                                    </div>

                                    {{-- Exibe a espécie para referência, não editável --}}
                                    <div class="form-group">
                                        <label>Espécie do Acasalamento</label>
                                        <p class="form-control-static">{{ $acasalamento->macho->tipoAve->nome ?? 'N/A' }}</p>
                                        {{-- Adiciona o tipo_ave_id oculto para validação no backend --}}
                                        <input type="hidden" name="selected_tipo_ave_id" value="{{ $acasalamento->macho->tipo_ave_id ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="data_inicio">Data de Início</label>
                                        <input type="date" name="data_inicio" class="form-control @error('data_inicio') is-invalid @enderror" id="data_inicio" value="{{ old('data_inicio', $acasalamento->data_inicio->format('Y-m-d')) }}" required readonly> {{-- Data de Início geralmente não é editável após registro --}}
                                        @error('data_inicio')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="data_fim">Data de Fim (Para Encerrar Acasalamento)</label>
                                        <input type="date" name="data_fim" class="form-control @error('data_fim') is-invalid @enderror" id="data_fim" value="{{ old('data_fim', $acasalamento->data_fim ? $acasalamento->data_fim->format('Y-m-d') : '') }}">
                                        @if (!$acasalamento->data_fim)
                                            <small class="form-text text-warning">Este acasalamento está **em andamento**. Preencha a data de fim para encerrá-lo.</small>
                                        @else
                                            <small class="form-text text-info">Este acasalamento foi encerrado em **{{ $acasalamento->data_fim->format('d/m/Y') }}**.</small>
                                        @endif
                                        @error('data_fim')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" rows="3">{{ old('observacoes', $acasalamento->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                    <a href="{{ route('acasalamentos.index') }}" class="btn btn-secondary">Cancelar</a>
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
