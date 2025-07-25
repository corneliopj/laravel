@php
    $pageTitle = 'Editar Ave';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- CONTEÚDO PRINCIPAL DA PÁGINA --}}
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Editar Ave: {{ $ave->matricula }}</h1> {{-- Título da página --}}
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('aves.index') }}">Aves</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6"> {{-- Reduzindo a largura do formulário para 6 colunas --}}
                        <div class="card card-primary"> {{-- Mantendo o card-primary para cor --}}
                            <div class="card-header">
                                <h3 class="card-title">Dados da Ave</h3>
                            </div>
                            <div class="card-body">
                                {{-- Mensagens de sucesso/erro --}}
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form action="{{ route('aves.update', $ave->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="matricula" class="form-label">Matrícula</label>
                                        <input type="text" class="form-control form-control-sm @error('matricula') is-invalid @enderror" id="matricula" name="matricula" value="{{ old('matricula', $ave->matricula) }}" required>
                                        @error('matricula')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="tipo_ave_id" class="form-label">Tipo de Ave</label>
                                        <select class="form-select form-select-sm @error('tipo_ave_id') is-invalid @enderror" id="tipo_ave_id" name="tipo_ave_id" required>
                                            <option value="">Selecione o Tipo de Ave</option>
                                            @foreach ($tiposAves as $tipoAve)
                                                <option value="{{ $tipoAve->id }}" {{ old('tipo_ave_id', $ave->tipo_ave_id) == $tipoAve->id ? 'selected' : '' }}>
                                                    {{ $tipoAve->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo_ave_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="variacao_id" class="form-label">Variação</label>
                                        <select class="form-select form-select-sm @error('variacao_id') is-invalid @enderror" id="variacao_id" name="variacao_id">
                                            <option value="">Selecione a Variação (Opcional)</option>
                                            @foreach ($variacoes as $variacao)
                                                <option value="{{ $variacao->id }}" {{ old('variacao_id', $ave->variacao_id) == $variacao->id ? 'selected' : '' }}>
                                                    {{ $variacao->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('variacao_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="lote_id" class="form-label">Lote</label>
                                        <select class="form-select form-select-sm @error('lote_id') is-invalid @enderror" id="lote_id" name="lote_id">
                                            <option value="">Selecione o Lote (Opcional)</option>
                                            @foreach ($lotes as $lote)
                                                <option value="{{ $lote->id }}" {{ old('lote_id', $ave->lote_id) == $lote->id ? 'selected' : '' }}>
                                                    {{ $lote->identificacao_lote }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('lote_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="incubacao_id" class="form-label">Incubação</label>
                                        <select class="form-select form-select-sm @error('incubacao_id') is-invalid @enderror" id="incubacao_id" name="incubacao_id">
                                            <option value="">Selecione a Incubação (Opcional)</option>
                                            @foreach ($incubacoes as $incubacao)
                                                <option value="{{ $incubacao->id }}" {{ old('incubacao_id', $ave->incubacao_id) == $incubacao->id ? 'selected' : '' }}>
                                                    {{ $incubacao->codigo_incubacao ?? $incubacao->id }} {{-- Ajuste se 'codigo_incubacao' não existir --}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('incubacao_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="data_eclosao" class="form-label">Data de Eclosão</label>
                                        <input type="date" class="form-control form-control-sm @error('data_eclosao') is-invalid @enderror" id="data_eclosao" name="data_eclosao" value="{{ old('data_eclosao', $ave->data_eclosao ? \Carbon\Carbon::parse($ave->data_eclosao)->format('Y-m-d') : '') }}" required>
                                        @error('data_eclosao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="sexo" class="form-label">Sexo</label>
                                        <select class="form-select form-select-sm @error('sexo') is-invalid @enderror" id="sexo" name="sexo" required>
                                            <option value="">Selecione o Sexo</option>
                                            <option value="Macho" {{ old('sexo', $ave->sexo) == 'Macho' ? 'selected' : '' }}>Macho</option>
                                            <option value="Femea" {{ old('sexo', $ave->sexo) == 'Femea' ? 'selected' : '' }}>Fêmea</option>
                                            <option value="Indefinido" {{ old('sexo', $ave->sexo) == 'Indefinido' ? 'selected' : '' }}>Não identificado</option>
                                        </select>
                                        @error('sexo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input @error('vendavel') is-invalid @enderror" id="vendavel" name="vendavel" value="1" {{ old('vendavel', $ave->vendavel) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vendavel">Vendável</label>
                                        @error('vendavel')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="foto" class="form-label">Foto</label>
                                        @if ($ave->foto_path)
                                            <div class="mb-2">
                                                <img src="{{ asset($ave->foto_path) }}" alt="Foto Atual" style="max-width: 150px; border-radius: 8px;">
                                                <div class="form-check mt-2">
                                                    <input type="checkbox" class="form-check-input" id="remover_foto_atual" name="remover_foto_atual" value="1">
                                                    <label class="form-check-label" for="remover_foto_atual">Remover foto atual</label>
                                                </div>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm @error('foto') is-invalid @enderror" id="foto" name="foto">
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer"> {{-- Botões no footer do card --}}
                                    <button type="submit" class="btn btn-primary">Atualizar Ave</button>
                                    <a href="{{ route('aves.index') }}" class="btn btn-secondary">Voltar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- FIM DO CONTEÚDO PRINCIPAL DA PÁGINA --}}
@include('layouts.partials.scripts')
    {{-- Inclui o partial footer --}}
    @include('layouts.partials.footer')
</div>


