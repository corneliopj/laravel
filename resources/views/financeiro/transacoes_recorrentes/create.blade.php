@php
    $pageTitle = 'Criar Transação Recorrente';
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
                        <h1 class="m-0">Criar Transação Recorrente</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.transacoes_recorrentes.index') }}">Transações Recorrentes</a></li>
                            <li class="breadcrumb-item active">Criar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Nova Transação Recorrente</h3>
                            </div>
                            <form action="{{ route('financeiro.transacoes_recorrentes.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="description">Descrição</label>
                                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" id="description" value="{{ old('description') }}" required>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="value">Valor</label>
                                        <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" id="value" step="0.01" min="0.01" value="{{ old('value') }}" required>
                                        @error('value')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    {{-- Campo Tipo (movido para antes da Categoria) --}}
                                    <div class="form-group">
                                        <label for="type">Tipo</label>
                                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                            <option value="">Selecione o tipo</option>
                                            <option value="receita" {{ old('type') == 'receita' ? 'selected' : '' }}>Receita</option>
                                            <option value="despesa" {{ old('type') == 'despesa' ? 'selected' : '' }}>Despesa</option>
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    {{-- Campo Categoria (agora será filtrado via JS) --}}
                                    <div class="form-group">
                                        <label for="category_id">Categoria</label>
                                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                            <option value="">Selecione uma categoria</option>
                                            {{-- Opções serão preenchidas via JavaScript --}}
                                        </select>
                                        @error('category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="frequency">Frequência</label>
                                        <select name="frequency" id="frequency" class="form-control @error('frequency') is-invalid @enderror" required>
                                            <option value="">Selecione a frequência</option>
                                            @foreach ($frequencias as $key => $value)
                                                <option value="{{ $key }}" {{ old('frequency') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('frequency')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="start_date">Data de Início</label>
                                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="end_date">Data de Fim (Opcional)</label>
                                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" value="{{ old('end_date') }}">
                                        @error('end_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Transação Recorrente</button>
                                    <a href="{{ route('financeiro.transacoes_recorrentes.index') }}" class="btn btn-secondary">Cancelar</a>
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

    <script>
        $(document).ready(function() {
            // Armazena todas as categorias passadas pelo controlador
            const allCategories = @json($categorias);
            const typeSelect = $('#type');
            const categorySelect = $('#category_id');
            const oldCategoryId = "{{ old('category_id') }}"; // Valor antigo da categoria, se houver

            function filterCategories() {
                const selectedType = typeSelect.val();
                categorySelect.empty(); // Limpa as opções atuais

                // Adiciona a opção padrão
                categorySelect.append($('<option>', {
                    value: '',
                    text: 'Selecione uma categoria'
                }));

                // Filtra e adiciona as categorias relevantes
                allCategories.forEach(function(categoria) {
                    if (selectedType === '' || categoria.tipo === selectedType) {
                        const option = $('<option>', {
                            value: categoria.id,
                            text: categoria.nome // REMOVIDO: (' + capitalizeFirstLetter(categoria.tipo) + ')'
                        });
                        categorySelect.append(option);
                    }
                });

                // Tenta selecionar o valor antigo, se existir
                if (oldCategoryId) {
                    categorySelect.val(oldCategoryId);
                }
            }

            // REMOVIDO: Função auxiliar para capitalizar a primeira letra (não mais necessária)
            // function capitalizeFirstLetter(string) {
            //     return string.charAt(0).toUpperCase() + string.slice(1);
            // }

            // Event listener para o dropdown de Tipo
            typeSelect.on('change', filterCategories);

            // Chama a função ao carregar a página para definir o estado inicial
            filterCategories();
        });
    </script>
</div>
