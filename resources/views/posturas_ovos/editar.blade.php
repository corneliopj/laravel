@php
    $pageTitle = 'Editar Postura de Ovos';
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
                        <h1 class="m-0">Editar Postura de Ovos</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('posturas_ovos.index') }}">Posturas de Ovos</a></li>
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
                                <h3 class="card-title">Dados da Postura</h3>
                            </div>
                            <form action="{{ route('posturas_ovos.update', $posturaOvo->id) }}" method="POST">
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

                                    {{-- Mensagens de sucesso/erro gerais --}}
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

                                    <input type="hidden" name="id" id="id" value="{{ old('id', $posturaOvo->id) }}">

                                    <div class="form-group">
                                        <label for="acasalamento_id">Acasalamento</label>
                                        <select name="acasalamento_id" id="acasalamento_id" class="form-control select2 @error('acasalamento_id') is-invalid @enderror" required>
                                            <option value="">-- Selecione o Acasalamento --</option>
                                            @foreach ($acasalamentos as $acasalamento)
                                                <option value="{{ $acasalamento->id }}" {{ old('acasalamento_id', $posturaOvo->acasalamento_id) == $acasalamento->id ? 'selected' : '' }}>
                                                    Macho: {{ $acasalamento->macho->matricula ?? 'N/A' }} / Fêmea: {{ $acasalamento->femea->matricula ?? 'N/A' }} (Início: {{ $acasalamento->data_inicio->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('acasalamento_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="data_inicio_postura">Data de Início da Postura</label>
                                        <input type="date" name="data_inicio_postura" class="form-control @error('data_inicio_postura') is-invalid @enderror" id="data_inicio_postura" value="{{ old('data_inicio_postura', $posturaOvo->data_inicio_postura->format('Y-m-d')) }}" required>
                                        @error('data_inicio_postura')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="data_fim_postura">Data de Fim da Postura (Opcional)</label>
                                        <input type="date" name="data_fim_postura" class="form-control @error('data_fim_postura') is-invalid @enderror" id="data_fim_postura" value="{{ old('data_fim_postura', $posturaOvo->data_fim_postura ? $posturaOvo->data_fim_postura->format('Y-m-d') : '') }}">
                                        @error('data_fim_postura')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="quantidade_ovos">Quantidade Total de Ovos</label>
                                        <input type="number" name="quantidade_ovos" class="form-control @error('quantidade_ovos') is-invalid @enderror" id="quantidade_ovos" placeholder="Ex: 15" value="{{ old('quantidade_ovos', $posturaOvo->quantidade_ovos) }}" min="0" required>
                                        <small class="form-text text-muted">Esta é a quantidade total de ovos desta postura. Você poderá adicionar mais ovos diariamente na tela de listagem.</small>
                                        @error('quantidade_ovos')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" rows="3">{{ old('observacoes', $posturaOvo->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                    <a href="{{ route('posturas_ovos.index') }}" class="btn btn-secondary">Cancelar</a>
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

{{-- Scripts específicos para esta view --}}
<script>
    $(document).ready(function() {
        // Inicializa o Select2 para o campo de acasalamento
        $('#acasalamento_id').select2({
            theme: 'bootstrap4',
            placeholder: "-- Selecione o Acasalamento --",
            allowClear: true // Permite limpar a seleção
        });
    });
</script>
