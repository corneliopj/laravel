@php
    $pageTitle = 'Registrar Nova Venda';
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Dados da Venda</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Comprador</label>
                                        <input type="text" name="comprador" id="comprador" class="form-control" list="compradores" required>
                                        <datalist id="compradores">
                                            @foreach($compradores as $comprador)
                                                <option value="{{ $comprador }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group">
                                        <label>Data da Venda</label>
                                        <input type="datetime-local" name="data_venda" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Método de Pagamento</label>
                                        <select name="metodo_pagamento" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach($metodosPagamento as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="observacoes" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Itens da Venda</h3>
                                </div>
                                <div class="card-body">
                                    <div id="itens-container">
                                        <!-- Itens serão adicionados aqui via JS -->
                                    </div>
                                    <button type="button" id="adicionar-item" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> Adicionar Item
                                    </button>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Desconto (R$)</label>
                                                <input type="number" name="desconto" id="desconto" class="form-control" value="0" min="0" step="0.01">
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
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Venda
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
document.addEventListener('DOMContentLoaded', function() {
    const itensContainer = document.getElementById('itens-container');
    const btnAdicionar = document.getElementById('adicionar-item');
    let itemCount = 0;

    // Template para um novo item
    function getItemTemplate() {
        itemCount++;
        return `
        <div class="item-venda card mb-2" data-index="${itemCount}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Descrição</label>
                            <input type="text" name="itens[${itemCount}][descricao]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Quantidade</label>
                            <input type="number" name="itens[${itemCount}][quantidade]" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Preço Unitário</label>
                            <input type="number" name="itens[${itemCount}][preco_unitario]" class="form-control preco-unitario" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Total</label>
                            <input type="text" class="form-control item-total" readonly>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remover" style="margin-top: 30px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    }

    // Adiciona um novo item
    btnAdicionar.addEventListener('click', function() {
        itensContainer.insertAdjacentHTML('beforeend', getItemTemplate());
        calcularTotal();
    });

    // Remove um item
    itensContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remover')) {
            e.target.closest('.item-venda').remove();
            calcularTotal();
        }
    });

    // Calcula totais
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.item-venda').forEach(item => {
            const quantidade = parseFloat(item.querySelector('input[name*="[quantidade]"]').value) || 0;
            const preco = parseFloat(item.querySelector('.preco-unitario').value) || 0;
            const itemTotal = quantidade * preco;
            item.querySelector('.item-total').value = 'R$ ' + itemTotal.toFixed(2).replace('.', ',');
            total += itemTotal;
        });

        const desconto = parseFloat(document.getElementById('desconto').value) || 0;
        document.getElementById('total-venda').textContent = 'R$ ' + (total - desconto).toFixed(2).replace('.', ',');
    }

    // Atualiza totais quando valores mudam
    itensContainer.addEventListener('input', function(e) {
        if (e.target.matches('input[name*="[quantidade]"], .preco-unitario')) {
            const item = e.target.closest('.item-venda');
            const quantidade = parseFloat(item.querySelector('input[name*="[quantidade]"]').value) || 0;
            const preco = parseFloat(item.querySelector('.preco-unitario').value) || 0;
            item.querySelector('.item-total').value = 'R$ ' + (quantidade * preco).toFixed(2).replace('.', ',');
            calcularTotal();
        }
    });

    // Atualiza total quando desconto muda
    document.getElementById('desconto').addEventListener('input', calcularTotal);

    // Adiciona o primeiro item automaticamente
    btnAdicionar.click();
});
</script>