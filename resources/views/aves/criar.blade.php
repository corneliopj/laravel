@php
    $pageTitle = 'Adicionar Nova Ave';
@endphp

{{-- Inclui o partial head --}}
@include('layouts.partials.head')

<div class="wrapper">
    {{-- O preloader pode ser mantido como está ou movido para um partial se for comum a várias páginas --}}
    <div class="preloader flex-column justify-content-center align-items-center" style="height: 0px;">
        <img class="animation__shake" src="{{ asset('img/logo.png') }}" alt="AdminLTELogo" height="60" width="60" style="display: none;">
    </div>
    {{-- Inclui o partial navbar --}}
    @include('layouts.partials.navbar')
    {{-- Inclui o partial sidebar --}}
    @include('layouts.partials.sidebar')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Adicionar Nova Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('aves.index') }}">Aves</a></li>
                            <li class="breadcrumb-item active">Adicionar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
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

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados da Ave</h3>
                            </div>
                            {{-- NOVO: Adicionado enctype="multipart/form-data" para upload de arquivos --}}
                            <form action="{{ route('aves.store') }}" method="post" enctype="multipart/form-data">
                                @csrf {{-- Token CSRF para segurança --}}
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="matricula">Matrícula</label>
                                        <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Digite a matrícula da ave" value="{{ old('matricula') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_ave_id">Tipo de Ave</label>
                                        <select class="form-control" id="tipo_ave_id" name="tipo_ave_id" required>
                                            <option value="">Selecione o tipo de ave</option>
                                            @forelse ($tiposAves as $tipo)
                                                <option value="{{ $tipo->id }}" {{ old('tipo_ave_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                            @empty
                                                <option value="">Nenhum tipo de ave disponível</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    {{-- CAMPO: Variação --}}
                                    <div class="form-group">
                                        <label for="variacao_id">Variação</label>
                                        <select class="form-control" id="variacao_id" name="variacao_id">
                                            <option value="">Selecione a variação (opcional)</option>
                                            {{-- As variações serão filtradas por JavaScript com base no tipo de ave --}}
                                            @forelse ($variacoes as $variacao)
                                                <option value="{{ $variacao->id }}" data-tipo-ave-id="{{ $variacao->tipo_ave_id }}" {{ old('variacao_id') == $variacao->id ? 'selected' : '' }}>{{ $variacao->nome }}</option>
                                            @empty
                                                <option value="">Nenhuma variação disponível</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="lote_id">Lote</label>
                                        <select class="form-control" id="lote_id" name="lote_id">
                                            <option value="">Selecione o lote (opcional)</option>
                                            @forelse ($lotes as $lote)
                                                <option value="{{ $lote->id }}" {{ old('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->identificacao_lote }}</option>
                                            @empty
                                                <option value="">Nenhum lote disponível</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="incubacao_id">Incubação (Origem)</label>
                                        <select class="form-control" id="incubacao_id" name="incubacao_id">
                                            <option value="">Selecione a incubação de origem (opcional)</option>
                                            @forelse ($incubacoes as $incubacao)
                                                <option value="{{ $incubacao->id }}" {{ old('incubacao_id') == $incubacao->id ? 'selected' : '' }}>Lote: {{ $incubacao->loteOvos->identificacao_lote ?? 'N/A' }} - Entrada: {{ $incubacao->data_entrada_incubadora->format('d/m/Y') }}</option>
                                            @empty
                                                <option value="">Nenhuma incubação disponível</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="data_eclosao">Data de Eclosão</label>
                                        <input type="date" class="form-control" id="data_eclosao" name="data_eclosao" required value="{{ old('data_eclosao') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="sexo">Sexo</label>
                                        <select class="form-control" id="sexo" name="sexo">
                                            <option value="Indefinido" {{ old('sexo') == 'Indefinido' ? 'selected' : '' }}>Não identificado</option>
                                            <option value="Macho" {{ old('sexo') == 'Macho' ? 'selected' : '' }}>Macho</option>
                                            <option value="Femea" {{ old('sexo') == 'Femea' ? 'selected' : '' }}>Fêmea</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="vendavel" name="vendavel" value="1" {{ old('vendavel') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="vendavel">Vendável</label>
                                        </div>
                                    </div>
                                    {{-- NOVO CAMPO: Upload de Foto --}}
                                    <div class="form-group">
                                        <label for="foto">Foto da Ave (Opcional)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                                                <label class="custom-file-label" for="foto">Escolha um arquivo</label>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Formatos aceitos: Imagem. Tamanho máximo: 5MB. Será redimensionada para 500x500px (máx. 1MB).</small>
                                        @error('foto')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Ave</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoAveSelect = document.getElementById('tipo_ave_id');
            const variacaoSelect = document.getElementById('variacao_id');
            const allVariacaoOptions = Array.from(variacaoSelect.options); // Converte para array para facilitar a manipulação

            function filterVariacoes() {
                const selectedTipoAveId = tipoAveSelect.value;
                
                // Limpa as opções atuais do select de variação, exceto a primeira (opção "Selecione...")
                variacaoSelect.innerHTML = '';
                variacaoSelect.appendChild(allVariacaoOptions[0]); // Adiciona a opção padrão de volta

                allVariacaoOptions.forEach(option => {
                    if (option.value === "") return; // Ignora a opção padrão

                    const tipoAveIdForOption = option.dataset.tipoAveId;

                    if (selectedTipoAveId === "" || tipoAveIdForOption === selectedTipoAveId) {
                        variacaoSelect.appendChild(option);
                    }
                });

                // Tenta pré-selecionar a variação se houver um old('variacao_id')
                const oldVariacaoId = "{{ old('variacao_id') }}";
                if (oldVariacaoId) {
                    const foundOption = allVariacaoOptions.find(option => option.value === oldVariacaoId);
                    if (foundOption && (foundOption.dataset.tipoAveId === selectedTipoAveId || selectedTipoAveId === "")) {
                        variacaoSelect.value = oldVariacaoId;
                    } else {
                        variacaoSelect.value = ""; // Limpa se a variação antiga não for compatível
                    }
                } else {
                    variacaoSelect.value = ""; // Garante que nada está selecionado se não houver old value
                }
            }

            // Adiciona o ouvinte de evento para o select de Tipo de Ave
            tipoAveSelect.addEventListener('change', filterVariacoes);

            // Chama a função uma vez ao carregar a página para aplicar o filtro inicial
            filterVariacoes();

            // Script para exibir o nome do arquivo selecionado no input custom-file
            document.getElementById('foto').addEventListener('change', function(e) {
                var fileName = e.target.files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        });
    </script>
</div>
