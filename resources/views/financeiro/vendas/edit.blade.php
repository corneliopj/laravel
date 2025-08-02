@php
    $pageTitle = 'Editar Venda #' . $venda->id;
@endphp

@include('layouts.partials.head')

<div class="wrapper">
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Venda #{{ $venda->id }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('financeiro.vendas.index') }}">Vendas</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('financeiro.vendas.update', $venda->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Dados da Venda</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Comprador</label>
                                        <input type="text" name="comprador" id="comprador" class="form-control" list="compradores" required value="{{ old('comprador', $venda->comprador) }}">
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
                                        <input type="datetime-local" name="data_venda" class="form-control" value="{{ old('data_venda', $venda->data_venda->format('Y-m-d\TH:i')) }}" required>
                                        @error('data_venda')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Método de Pagamento</label>
                                        <select name="metodo_pagamento" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach($metodosPagamento as $key => $value)
                                                <option value="{{ $key }}" {{ old('metodo_pagamento', $venda->metodo_pagamento) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('metodo_pagamento')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes', $venda->observacoes) }}</textarea>
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
                                        @foreach ($venda->vendaItems as $index => $item)
                                            @include('financeiro.vendas.partials.item_row', [
                                                'index' => $index,
                                                'item' => $item,
                                                'avesDisponiveis' => $avesDisponiveis,
                                                'plantelOptions' => $plantelOptions,
                                            ])
                                        @endforeach
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
                                                <input type="number" name="desconto" id="desconto" class="form-control" value="{{ old('desconto', $venda->desconto) }}" min="0" step="0.01">
                                                @error('desconto')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Total: <span id="total-venda">R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Atualizar Venda
                            </button>
                            <a href="{{ route('financeiro.vendas.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

@include('layouts.partials.scripts')

<script>
// O mesmo script do create.blade.php, mas com inicialização de valores
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ old('itens') ? count(old('itens')) : count($venda->vendaItems) }};
    const itemsContainer = document.getElementById('items_container');
    const addItemBtn = document.getElementById('add_item_btn');
    const descontoInput = document.getElementById('desconto');

    // Função para inicializar uma linha de item
    function initializeItemRow(rowElement) {
        // ... (mesma função do create.blade.php) ...
    }

    // Função para atualizar totais
    function updateTotals() {
        // ... (mesma função do create.blade.php) ...
    }

    // Inicializar linhas existentes
    document.querySelectorAll('.item-row').forEach(row => {
        initializeItemRow(row);
    });

    // Adicionar novo item
    addItemBtn.addEventListener('click', function() {
        // ... (mesmo código do create.blade.php) ...
    });

    // Evento para desconto
    descontoInput.addEventListener('input', updateTotals);
    
    // Atualizar totais inicialmente
    updateTotals();
});
</script>