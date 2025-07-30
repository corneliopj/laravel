    @php
        $pageTitle = 'Registrar Nova Venda';
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
                            <h1>Registrar Nova Venda</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('financeiro.vendas.index') }}">Vendas</a></li>
                                <li class="breadcrumb-item active">Registrar</li>
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
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Dados da Venda</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form action="{{ route('financeiro.vendas.store') }}" method="POST">
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
                                            <label for="data_venda">Data da Venda</label>
                                            <input type="date" name="data_venda" id="data_venda" class="form-control @error('data_venda') is-invalid @enderror" value="{{ old('data_venda', date('Y-m-d')) }}" required>
                                            @error('data_venda')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="comprador">Comprador</label>
                                            <input type="text" name="comprador" id="comprador" class="form-control @error('comprador') is-invalid @enderror" value="{{ old('comprador') }}" placeholder="Nome do comprador ou empresa">
                                            @error('comprador')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="metodo_pagamento">Método de Pagamento</label>
                                            <select name="metodo_pagamento" id="metodo_pagamento" class="form-control @error('metodo_pagamento') is-invalid @enderror">
                                                <option value="">Selecione</option>
                                                @foreach ($metodosPagamento as $key => $value)
                                                    <option value="{{ $key }}" {{ old('metodo_pagamento') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @error('metodo_pagamento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="desconto">Desconto (R$)</label>
                                            <input type="number" name="desconto" id="desconto" class="form-control @error('desconto') is-invalid @enderror" value="{{ old('desconto', 0) }}" min="0" step="0.01">
                                            @error('desconto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <hr>
                                        <h4>Itens da Venda</h4>
                                        <div id="items_container">
                                            <!-- Itens serão adicionados aqui via JavaScript -->
                                            @if (old('items'))
                                                @foreach (old('items') as $index => $item)
                                                    @include('financeiro.vendas.partials.item_row', [
                                                        'index' => $index,
                                                        'item' => (object) $item,
                                                        'avesDisponiveis' => $avesDisponiveis,
                                                        'plantelOptions' => $plantelOptions,
                                                    ])
                                                @endforeach
                                            @else
                                                @include('financeiro.vendas.partials.item_row', [
                                                    'index' => 0,
                                                    'item' => null,
                                                    'avesDisponiveis' => $avesDisponiveis,
                                                    'plantelOptions' => $plantelOptions,
                                                ])
                                            @endif
                                        </div>

                                        <button type="button" id="add_item_btn" class="btn btn-info btn-sm mb-3">
                                            <i class="fas fa-plus"></i> Adicionar Item
                                        </button>

                                        <div class="form-group">
                                            <label for="observacoes">Observações</label>
                                            <textarea name="observacoes" id="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3" placeholder="Detalhes adicionais sobre a venda...">{{ old('observacoes') }}</textarea>
                                            @error('observacoes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Totais --}}
                                        <div class="row">
                                            <div class="col-md-6 offset-md-6">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Subtotal:</strong>
                                                    <span id="subtotal_display">R$ 0,00</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <strong>Desconto:</strong>
                                                    <span id="desconto_display">R$ 0,00</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <h4><strong>Valor Final:</strong></h4>
                                                    <h4><strong id="valor_final_display">R$ 0,00</strong></h4>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-success">Registrar Venda</button>
                                        <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary">Cancelar</a>
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
                let itemIndex = {{ old('items') ? count(old('items')) : 1 }};
                const itemsContainer = document.getElementById('items_container');
                const addItemBtn = document.getElementById('add_item_btn');
                const descontoInput = document.getElementById('desconto');

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
                                // Exemplo: preencher descrição com a matrícula da ave
                                descricaoItemInput.value = selectedOption.text.split('(')[0].trim();
                                // Se a ave tiver um preço sugerido, você pode buscá-lo aqui
                                // e preencher precoUnitarioInput.value
                            } else {
                                descricaoItemInput.value = '';
                            }
                            updateTotals(); // Recalcula totais ao mudar a ave
                        });
                    }

                    // Adiciona listeners para quantidade e preço unitário para recalcular o total do item e o total da venda
                    quantidadeInput.addEventListener('input', updateTotals);
                    precoUnitarioInput.addEventListener('input', updateTotals);
                }

                // Função para calcular e exibir os totais
                function updateTotals() {
                    let subtotal = 0;
                    // Itera sobre todos os campos de quantidade e preço unitário dos itens
                    itemsContainer.querySelectorAll('.item-row').forEach(rowElement => {
                        const quantidade = parseFloat(rowElement.querySelector('[id^="items_"][id$="_quantidade"]').value) || 0;
                        const precoUnitario = parseFloat(rowElement.querySelector('[id^="items_"][id$="_preco_unitario"]').value) || 0;
                        const itemTotal = quantidade * precoUnitario;
                        subtotal += itemTotal;
                    });

                    const desconto = parseFloat(descontoInput.value) || 0;
                    let valorFinal = subtotal - desconto;

                    if (valorFinal < 0) {
                        valorFinal = 0; // Garante que o valor final não seja negativo
                    }

                    document.getElementById('subtotal_display').innerText = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
                    document.getElementById('desconto_display').innerText = `R$ ${desconto.toFixed(2).replace('.', ',')}`;
                    document.getElementById('valor_final_display').innerText = `R$ ${valorFinal.toFixed(2).replace('.', ',')}`;
                }

                // Inicializa as linhas existentes (se houver old('items') ou itens da venda)
                itemsContainer.querySelectorAll('.item-row').forEach(row => {
                    initializeItemRow(row);
                });

                addItemBtn.addEventListener('click', function () {
                    const template = `
                        @include('financeiro.vendas.partials.item_row', [
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
                        updateTotals(); // Recalcula totais ao remover item
                    });

                    itemIndex++;
                    updateTotals(); // Recalcula totais ao adicionar novo item
                });

                // Adiciona listeners para os botões de remover existentes
                itemsContainer.querySelectorAll('.remove-item-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        btn.closest('.item-row').remove();
                        updateTotals(); // Recalcula totais ao remover item
                    });
                });

                // Listener para o campo de desconto
                descontoInput.addEventListener('input', updateTotals);

                // Garante que os totais são calculados na carga da página
                updateTotals();
            });
        </script>
    </div>
    <!-- ./wrapper -->
    