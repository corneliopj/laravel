@php
    $pageTitle = 'Editar Venda';
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Venda: #{{ $venda->id }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.vendas.index') }}">Vendas</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Dados da Venda</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action="{{ route('financeiro.vendas.update', $venda->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="data_venda">Data da Venda</label>
                                        <input type="date" name="data_venda" id="data_venda" class="form-control @error('data_venda') is-invalid @enderror" value="{{ old('data_venda', $venda->data_venda->format('Y-m-d')) }}" required>
                                        @error('data_venda')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="comprador">Comprador</label>
                                        <input type="text" name="comprador" id="comprador" class="form-control @error('comprador') is-invalid @enderror" value="{{ old('comprador', $venda->comprador) }}" placeholder="Nome do comprador ou empresa">
                                        @error('comprador')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr>
                                    <h4>Itens da Venda</h4>
                                    <div id="items_container">
                                        <!-- Itens existentes serão pré-preenchidos aqui -->
                                        @if (old('items'))
                                            @foreach (old('items') as $index => $item)
                                                @include('vendas.partials.item_row', [
                                                    'index' => $index,
                                                    'item' => (object) $item,
                                                    'avesDisponiveis' => $avesDisponiveis,
                                                    'plantelOptions' => $plantelOptions,
                                                ])
                                            @endforeach
                                        @else
                                            @foreach ($venda->items as $index => $item)
                                                @include('financeiro.vendas.partials.item_row', [
                                                    'index' => $index,
                                                    'item' => $item,
                                                    'avesDisponiveis' => $avesDisponiveis,
                                                    'plantelOptions' => $plantelOptions,
                                                ])
                                            @endforeach
                                        @endif
                                    </div>

                                    <button type="button" id="add_item_btn" class="btn btn-info btn-sm mb-3">
                                        <i class="fas fa-plus"></i> Adicionar Item
                                    </button>

                                    <div class="form-group">
                                        <label for="observacoes">Observações</label>
                                        <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Detalhes adicionais sobre a venda...">{{ old('observacoes', $venda->observacoes) }}</textarea>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Atualizar Venda</button>
                                    <a href="{{ route('financeiro.vendas.show', $venda->id) }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('layouts.partials.scripts')
    @include('layouts.partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = {{ old('items') ? count(old('items')) : $venda->items->count() }};
            const itemsContainer = document.getElementById('items_container');
            const addItemBtn = document.getElementById('add_item_btn');

            function initializeItemRow(rowElement) {
                const tipoItemRadios = rowElement.querySelectorAll('input[name$="[tipo_item]"]');
                const divAveId = rowElement.querySelector('[id^="div_ave_id_"]');
                const divPlantelId = rowElement.querySelector('[id^="div_plantel_id_"]');
                const aveIdSelect = rowElement.querySelector('[id^="items_"][id$="_ave_id"]');
                const plantelIdSelect = rowElement.querySelector('[id^="items_"][id$="_plantel_id"]');
                const quantidadeInput = rowElement.querySelector('[id^="items_"][id$="_quantidade"]');
                const descricaoItemInput = rowElement.querySelector('[id^="items_"][id$="_descricao_item"]');
                const precoUnitarioInput = rowElement.querySelector('[id^="items_"][id$="_preco_unitario"]');


                function toggleItemFields() {
                    const selectedTipo = rowElement.querySelector('input[name$="[tipo_item]"]:checked').value;

                    if (selectedTipo === 'individual') {
                        divAveId.style.display = 'block';
                        divPlantelId.style.display = 'none';
                        aveIdSelect.setAttribute('required', 'required');
                        plantelIdSelect.removeAttribute('required');
                        quantidadeInput.value = 1; // Quantidade sempre 1 para ave individual
                        quantidadeInput.setAttribute('readonly', 'readonly');
                    } else if (selectedTipo === 'plantel') {
                        divAveId.style.display = 'none';
                        divPlantelId.style.display = 'block';
                        aveIdSelect.removeAttribute('required');
                        plantelIdSelect.setAttribute('required', 'required');
                        quantidadeInput.removeAttribute('readonly');
                    } else { // generico
                        divAveId.style.display = 'none';
                        divPlantelId.style.display = 'none';
                        aveIdSelect.removeAttribute('required');
                        plantelIdSelect.removeAttribute('required');
                        quantidadeInput.removeAttribute('readonly');
                    }

                    // Campos sempre requeridos, exceto ave_id/plantel_id que são condicionais
                    descricaoItemInput.setAttribute('required', 'required');
                    quantidadeInput.setAttribute('required', 'required');
                    precoUnitarioInput.setAttribute('required', 'required');
                }

                tipoItemRadios.forEach(radio => {
                    radio.addEventListener('change', toggleItemFields);
                });

                // Inicializa o estado dos campos ao carregar a linha
                toggleItemFields();

                // Se for ave individual e já tiver ave_id, preencher descrição e preço (opcional, se tiver esses dados no JS)
                if (aveIdSelect) {
                    aveIdSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        if (selectedOption && selectedOption.value) {
                            // Exemplo: se você tiver um atributo data-preco-sugerido na option
                            // const precoSugerido = selectedOption.dataset.precoSugerido;
                            // if (precoSugerido) {
                            //     precoUnitarioInput.value = precoSugerido;
                            // }
                            // Exemplo: preencher descrição com a matrícula da ave
                            descricaoItemInput.value = selectedOption.text.split('(')[0].trim();
                        } else {
                            descricaoItemInput.value = '';
                            // precoUnitarioInput.value = '';
                        }
                    });
                }
            }

            // Inicializa as linhas existentes (se houver old('items'))
            itemsContainer.querySelectorAll('.item-row').forEach(row => {
                initializeItemRow(row);
            });

            addItemBtn.addEventListener('click', function () {
                const template = `
                    @include('vendas.partials.item_row', [
                        'index' => 'ITEM_INDEX_PLACEHOLDER',
                        'item' => null,
                        'avesDisponiveis' => $avesDisponiveis,
                        'plantelOptions' => $plantelOptions,
                    ])
                `;
                const newRowHtml = template.replace(/ITEM_INDEX_PLACEHOLDER/g, itemIndex);
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = newRowHtml.trim();
                const newRow = tempDiv.firstChild;
                itemsContainer.appendChild(newRow);

                initializeItemRow(newRow); // Inicializa os listeners para a nova linha

                // Adiciona listener para o botão de remover na nova linha
                newRow.querySelector('.remove-item-btn').addEventListener('click', function () {
                    newRow.remove();
                });

                itemIndex++;
            });

            // Adiciona listeners para os botões de remover existentes (se houver old('items'))
            itemsContainer.querySelectorAll('.remove-item-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    btn.closest('.item-row').remove();
                });
            });
        });
    </script>
</div>
<!-- ./wrapper -->
