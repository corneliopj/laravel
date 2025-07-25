@php
    $pageTitle = 'Registrar Novo Acasalamento';
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
                        <h1 class="m-0">Registrar Novo Acasalamento</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('acasalamentos.index') }}">Acasalamentos</a></li>
                            <li class="breadcrumb-item active">Registrar</li>
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
                            <form action="{{ route('acasalamentos.store') }}" method="POST">
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

                                    {{-- NOVO: Seleção de Espécie (Tipo de Ave) via Rádio Buttons --}}
                                    <div class="form-group">
                                        <label>Selecione a Espécie do Acasalamento:</label><br>
                                        <div class="d-flex flex-wrap" id="tipo-ave-radios">
                                            @foreach ($tiposAves as $tipo)
                                                <div class="form-check mr-3">
                                                    <input class="form-check-input" type="radio" name="selected_tipo_ave_id" id="tipo_ave_radio_{{ $tipo->id }}" value="{{ $tipo->id }}" 
                                                        {{ (old('selected_tipo_ave_id') == $tipo->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tipo_ave_radio_{{ $tipo->id }}">
                                                        {{ $tipo->nome }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('selected_tipo_ave_id')
                                            <span class="text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="macho_id">Macho (Ave)</label>
                                        <select name="macho_id" id="macho_id" class="form-control @error('macho_id') is-invalid @enderror" required>
                                            <option value="">-- Selecione o Macho --</option>
                                            @foreach ($machos as $macho)
                                                <option value="{{ $macho->id }}" data-tipo-ave-id="{{ $macho->tipo_ave_id }}" {{ old('macho_id') == $macho->id ? 'selected' : '' }}>
                                                    {{ $macho->matricula }} ({{ $macho->tipoAve->nome ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('macho_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="femea_id">Fêmea (Ave)</label>
                                        <select name="femea_id" id="femea_id" class="form-control @error('femea_id') is-invalid @enderror" required>
                                            <option value="">-- Selecione a Fêmea --</option>
                                            @foreach ($femeas as $femea)
                                                <option value="{{ $femea->id }}" data-tipo-ave-id="{{ $femea->tipo_ave_id }}" {{ old('femea_id') == $femea->id ? 'selected' : '' }}>
                                                    {{ $femea->matricula }} ({{ $femea->tipoAve->nome ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('femea_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="data_inicio">Data de Início</label>
                                        <input type="date" name="data_inicio" class="form-control @error('data_inicio') is-invalid @enderror" id="data_inicio" value="{{ old('data_inicio') }}" required>
                                        @error('data_inicio')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="data_fim">Data de Fim (Opcional)</label>
                                        <input type="date" name="data_fim" class="form-control @error('data_fim') is-invalid @enderror" id="data_fim" value="{{ old('data_fim') }}">
                                        @error('data_fim')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" rows="3">{{ old('observacoes') }}</textarea>
                                        @error('observacoes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Acasalamento</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoAveRadios = document.querySelectorAll('input[name="selected_tipo_ave_id"]');
            const machoSelect = document.getElementById('macho_id');
            const femeaSelect = document.getElementById('femea_id');

            // Armazena todas as opções originais dos selects de macho e fêmea
            const allMachoOptions = Array.from(machoSelect.options);
            const allFemeaOptions = Array.from(femeaSelect.options);

            // Função para filtrar os dropdowns de macho e fêmea
            function filterAvesByTipoAve() {
                let selectedTipoAveId = null;
                tipoAveRadios.forEach(radio => {
                    if (radio.checked) {
                        selectedTipoAveId = radio.value;
                    }
                });

                // Limpa os selects, mantendo a opção padrão "Selecione..."
                machoSelect.innerHTML = '';
                machoSelect.appendChild(allMachoOptions[0].cloneNode(true)); // Clona para não remover do original
                femeaSelect.innerHTML = '';
                femeaSelect.appendChild(allFemeaOptions[0].cloneNode(true));

                // Se nenhuma espécie for selecionada, não adiciona nenhuma ave
                if (!selectedTipoAveId) {
                    return;
                }

                // Filtra e adiciona as aves machos
                allMachoOptions.forEach(option => {
                    if (option.value === "") return; // Ignora a opção padrão

                    if (option.dataset.tipoAveId === selectedTipoAveId) {
                        machoSelect.appendChild(option.cloneNode(true));
                    }
                });

                // Filtra e adiciona as aves fêmeas
                allFemeaOptions.forEach(option => {
                    if (option.value === "") return; // Ignora a opção padrão

                    if (option.dataset.tipoAveId === selectedTipoAveId) {
                        femeaSelect.appendChild(option.cloneNode(true));
                    }
                });

                // Tenta re-selecionar os valores antigos após o filtro
                const oldMachoId = "{{ old('macho_id') }}";
                const oldFemeaId = "{{ old('femea_id') }}";

                if (oldMachoId) {
                    const foundMachoOption = Array.from(machoSelect.options).find(opt => opt.value === oldMachoId);
                    if (foundMachoOption) {
                        machoSelect.value = oldMachoId;
                    } else {
                        machoSelect.value = ""; // Limpa se o valor antigo não estiver mais disponível
                    }
                }
                if (oldFemeaId) {
                    const foundFemeaOption = Array.from(femeaSelect.options).find(opt => opt.value === oldFemeaId);
                    if (foundFemeaOption) {
                        femeaSelect.value = oldFemeaId;
                    } else {
                        femeaSelect.value = ""; // Limpa se o valor antigo não estiver mais disponível
                    }
                }
            }

            // Adiciona ouvintes de evento aos rádio buttons
            tipoAveRadios.forEach(radio => {
                radio.addEventListener('change', filterAvesByTipoAve);
            });

            // Chama a função uma vez ao carregar a página para aplicar o filtro inicial
            filterAvesByTipoAve();
        });
    </script>
</div>
