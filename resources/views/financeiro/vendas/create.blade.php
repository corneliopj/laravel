@php
    $pageTitle = 'Registrar Nova Venda';
@endphp

@extends('layouts.app')

@section('content')
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
                            <li class="breadcrumb-item active">Nova</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('financeiro.vendas.store') }}" method="POST">
                    @csrf
                    {{-- Token de idempotência para prevenir double-submit (double/triple click) --}}
                    <input type="hidden" name="_idempotency_token" id="_idempotency_token" value="{{ md5(uniqid('', true)) }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Dados da Venda</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Comprador</label>
                                        <input type="text" name="comprador" id="comprador" class="form-control" list="compradores" required value="{{ old('comprador') }}">
                                        <datalist id="compradores">
                                            @foreach($compradores as $comprador)
                                                <option value="{{ $comprador }}">
                                            @endforeach
                                        </datalist>
                                        @error('comprador')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Data da Venda</label>
                                        <input type="datetime-local" name="data_venda" class="form-control" value="{{ old('data_venda', now()->format('Y-m-d\TH:i')) }}" required>
                                        @error('data_venda')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Método de Pagamento</label>
                                        <select name="metodo_pagamento" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach($metodosPagamento as $key => $value)
                                                <option value="{{ $key }}" {{ old('metodo_pagamento') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('metodo_pagamento')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes') }}</textarea>
                                        @error('observacoes')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Itens da Venda</h3>
                                </div>
                                <div class="card-body" id="items_container">
                                    @if (old('itens'))
                                        @foreach (old('itens') as $index => $item)
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
                                <div class="card-footer">
                                    <button type="button" id="add_item_btn" class="btn btn-info">
                                        <i class="fas fa-plus"></i> Adicionar Item
                                    </button>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Desconto (R$)</label>
                                                <input type="number" name="desconto" id="desconto" class="form-control" value="{{ old('desconto', 0) }}" min="0" step="0.01">
                                                @error('desconto')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Total: <span id="total-venda">R$ 0,00</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" id="btn-salvar-venda" class="btn btn-success">
                                <i class="fas fa-save"></i> <span class="btn-text">Salvar Venda</span>
                                <span class="btn-loading d-none"><i class="fas fa-spinner fa-spin"></i> Processando...</span>
                            </button>
                            <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary" id="btn-cancelar">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ old('itens') ? count(old('itens')) : 1 }};
    const itemsContainer = document.getElementById('items_container');
    const addItemBtn = document.getElementById('add_item_btn');
    const descontoInput = document.getElementById('desconto');

    // === PROTEÇÃO CONTRA DOUBLE-SUBMIT ===
    const form = document.querySelector('form[action="{{ route('financeiro.vendas.store') }}"]');
    const btnSalvar = document.getElementById('btn-salvar-venda');
    const btnText = btnSalvar ? btnSalvar.querySelector('.btn-text') : null;
    const btnLoading = btnSalvar ? btnSalvar.querySelector('.btn-loading') : null;
    const btnCancelar = document.getElementById('btn-cancelar');
    let formSubmitted = false;

    if (form && btnSalvar) {
        form.addEventListener('submit', function(e) {
            // Se já foi submetido, bloqueia
            if (formSubmitted) {
                e.preventDefault();
                return false;
            }

            // Validação básica client-side antes de bloquear
            const comprador = document.getElementById('comprador')?.value?.trim();
            const dataVenda = document.querySelector('input[name="data_venda"]')?.value;
            const metodoPagamento = document.querySelector('select[name="metodo_pagamento"]')?.value;
            const itemRows = document.querySelectorAll('.item-row');

            if (!comprador || !dataVenda || !metodoPagamento || itemRows.length === 0) {
                // Deixa o HTML5 validation agir
                return true;
            }

            // Verifica se pelo menos um item tem quantidade e preço
            let hasValidItem = false;
            itemRows.forEach(row => {
                const qtd = row.querySelector('input[name$="[quantidade]"]')?.value;
                const preco = row.querySelector('input[name$="[preco_unitario]"]')?.value;
                if (qtd && preco && parseFloat(qtd) > 0 && parseFloat(preco) > 0) {
                    hasValidItem = true;
                }
            });

            if (!hasValidItem) {
                return true; // Deixa validação do servidor agir
            }

            // === BLOQUEIA O FORMULÁRIO ===
            formSubmitted = true;
            btnSalvar.disabled = true;
            if (btnText) btnText.classList.add('d-none');
            if (btnLoading) btnLoading.classList.remove('d-none');
            if (btnCancelar) btnCancelar.style.display = 'none';

            // Adiciona classe visual no form
            form.classList.add('form-submitted');

            // Permite o submit normal continuar
            return true;
        });
    }

    // Função para inicializar uma linha de item
    function initializeItemRow(rowElement) {
        const tipoItemRadios = rowElement.querySelectorAll('input[type="radio"]');
        const divAveId = rowElement.querySelector('[id^="div_ave_id_"]');
        const divPlantelId = rowElement.querySelector('[id^="div_plantel_id_"]');
        const quantidadeInput = rowElement.querySelector('input[name$="[quantidade]"]');
        const precoInput = rowElement.querySelector('input[name$="[preco_unitario]"]');

        function toggleItemFields() {
            // Adiciona verificação para os elementos antes de usá-los
            if (!divAveId || !divPlantelId || !quantidadeInput) {
                console.error("Um ou mais elementos do formulário de item não foram encontrados.", rowElement);
                return;
            }

            const checkedRadio = rowElement.querySelector('input[type="radio"]:checked');
            // Se nenhum rádio estiver selecionado, não faz nada. A chamada inicial de toggleItemFields() cuidará do estado inicial.
            if (!checkedRadio) {
                divAveId.style.display = 'none';
                divPlantelId.style.display = 'none';
                if (quantidadeInput) quantidadeInput.readOnly = false;
                return;
            }
            const selectedValue = checkedRadio.value;

            if (selectedValue === 'individual') {
                divAveId.style.display = 'block';
                divPlantelId.style.display = 'none';
                quantidadeInput.readOnly = true;
                quantidadeInput.value = 1;
            } else if (selectedValue === 'plantel') {
                divAveId.style.display = 'none';
                divPlantelId.style.display = 'block';
                quantidadeInput.readOnly = false;
            } else {
                divAveId.style.display = 'none';
                divPlantelId.style.display = 'none';
                quantidadeInput.readOnly = false;
            }
            // Dispara a atualização de totais ao mudar o tipo, caso a quantidade mude para 1
            updateTotals();
        }

        tipoItemRadios.forEach(radio => {
            radio.addEventListener('change', toggleItemFields);
        });

        toggleItemFields();

        // Adicionar evento para o botão de remover
        const removeBtn = rowElement.querySelector('.remove-item-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                rowElement.remove();
                updateTotals();
            });
        }

        // Adicionar eventos para atualizar totais
        if (quantidadeInput) quantidadeInput.addEventListener('input', updateTotals);
        if (precoInput) precoInput.addEventListener('input', updateTotals);
    }

    // Função para atualizar totais
    function updateTotals() {
        let subtotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const quantidade = parseFloat(row.querySelector('input[name$="[quantidade]"]').value) || 0;
            const preco = parseFloat(row.querySelector('input[name$="[preco_unitario]"]').value) || 0;
            subtotal += quantidade * preco;
        });

        const desconto = parseFloat(descontoInput.value) || 0;
        const total = subtotal - desconto;

        document.getElementById('total-venda').textContent =
            'R$ ' + total.toFixed(2).replace('.', ',');
    }

    // Inicializar linhas existentes
    document.querySelectorAll('.item-row').forEach(row => {
        initializeItemRow(row);
    });

    // Adicionar novo item
    addItemBtn.addEventListener('click', function() {
        const template = `
            @include('financeiro.vendas.partials.item_row', [
                'index' => 'ITEM_INDEX_PLACEHOLDER',
                'item' => null,
                'avesDisponiveis' => $avesDisponiveis,
                'plantelOptions' => $plantelOptions,
            ])
        `.replace(/ITEM_INDEX_PLACEHOLDER/g, itemIndex);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = template.trim();
        const newRow = tempDiv.firstChild;

        itemsContainer.appendChild(newRow);
        initializeItemRow(newRow);

        itemIndex++;
        updateTotals();
    });

    // Evento para desconto
    descontoInput.addEventListener('input', updateTotals);

    // Atualizar totais inicialmente
    updateTotals();
});
</script>
@push('styles')
<style>
    /* Estilos para estado de submissão */
    .form-submitted {
        opacity: 0.85;
        pointer-events: none;
    }
    .form-submitted input,
    .form-submitted select,
    .form-submitted textarea,
    .form-submitted button:not(#btn-salvar-venda) {
        pointer-events: none;
    }
    .btn-loading {
        display: inline-flex !important;
        align-items: center;
        gap: 0.5rem;
    }
    .d-none {
        display: none !important;
    }
</style>
@endpush
@endpush