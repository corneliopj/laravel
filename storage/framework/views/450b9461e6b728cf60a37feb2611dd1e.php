<?php
    $pageTitle = 'Editar Venda';
?>


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Inclui Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Inclui AdminLTE Select2 customizações (se houver) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap4-theme/1.0.0/select2-bootstrap4.min.css">

<div class="wrapper">
    
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Editar Venda #<?php echo e($venda->id); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.vendas.index')); ?>">Vendas</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Editar Venda #<?php echo e($venda->id); ?></h3>
                            </div>
                            <form action="<?php echo e(route('financeiro.vendas.update', $venda->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="card-body">
                                    <?php if(session('success')): ?>
                                        <div class="alert alert-success">
                                            <?php echo e(session('success')); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if(session('error')): ?>
                                        <div class="alert alert-danger">
                                            <?php echo e(session('error')); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-danger">
                                            <ul>
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="data_venda">Data da Venda</label>
                                            <input type="date" name="data_venda" class="form-control <?php $__errorArgs = ['data_venda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_venda" value="<?php echo e(old('data_venda', $venda->data_venda->format('Y-m-d'))); ?>" required>
                                            <?php $__errorArgs = ['data_venda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="metodo_pagamento">Método de Pagamento</label>
                                            <select name="metodo_pagamento" id="metodo_pagamento" class="form-control <?php $__errorArgs = ['metodo_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <option value="">Selecione</option>
                                                <?php $__currentLoopData = $metodosPagamento; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metodo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($metodo); ?>" <?php echo e(old('metodo_pagamento', $venda->metodo_pagamento) == $metodo ? 'selected' : ''); ?>><?php echo e($metodo); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['metodo_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="desconto">Desconto (R$)</label>
                                            <input type="number" name="desconto" class="form-control <?php $__errorArgs = ['desconto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="desconto" value="<?php echo e(old('desconto', $venda->desconto)); ?>" min="0" step="0.01">
                                            <?php $__errorArgs = ['desconto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status da Venda</label>
                                        <select name="status" id="status" class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e(old('status', $venda->status) == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea name="observacoes" class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="observacoes" rows="3"><?php echo e(old('observacoes', $venda->observacoes)); ?></textarea>
                                        <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="com_comissao" name="com_comissao" <?php echo e(old('com_comissao', $venda->comissao_paga) ? 'checked' : ''); ?>>
                                            <label class="custom-control-label" for="com_comissao">Gerar Comissão para o Vendedor (<?php echo e(Auth::user()->name ?? 'Usuário Logado'); ?>)</label>
                                        </div>
                                    </div>

                                    <?php if($venda->user): ?>
                                        <div class="form-group">
                                            <p><strong>Vendedor:</strong> <?php echo e($venda->user->name); ?></p>
                                            <p><strong>Percentual de Comissão:</strong> <?php echo e(number_format($venda->comissao_percentual, 2, ',', '.')); ?>%</p>
                                            <?php if($venda->despesaComissao): ?>
                                                <p><strong>Despesa de Comissão Gerada:</strong>
                                                    <a href="<?php echo e(route('financeiro.despesas.show', $venda->despesaComissao->id)); ?>">Ver Despesa #<?php echo e($venda->despesaComissao->id); ?></a> (R$ <?php echo e(number_format($venda->despesaComissao->valor, 2, ',', '.')); ?>)
                                                </p>
                                            <?php else: ?>
                                                <p><strong>Despesa de Comissão:</strong> Não gerada ou removida.</p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <hr>
                                    <h4>Itens da Venda</h4>

                                    <div class="form-group">
                                        <label for="search_ave">Adicionar Ave (Matrícula / Tipo)</label>
                                        <select class="form-control select2" id="search_ave" style="width: 100%;">
                                            <option value="">Busque uma ave...</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="descricao_item_manual">Adicionar Item Manualmente (Descrição)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="descricao_item_manual" placeholder="Ex: Ração, Gaiola, etc.">
                                            <input type="number" class="form-control ml-2" id="preco_item_manual" placeholder="Preço Unitário" min="0.01" step="0.01">
                                            <input type="number" class="form-control ml-2" id="quantidade_item_manual" placeholder="Qtd" value="1" min="1">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info" id="add_item_manual">Adicionar Item</button>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div id="items-hidden-container">
                                        
                                    </div>

                                    <table class="table table-bordered table-hover mt-3" id="items_table">
                                        <thead>
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Qtd</th>
                                                <th>Preço Unit.</th>
                                                <th>Total</th>
                                                <th style="width: 50px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-right">Subtotal:</th>
                                                <th id="subtotal_display">R$ 0,00</th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">Desconto:</th>
                                                <th id="desconto_display">R$ 0,00</th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">Valor Final:</th>
                                                <th id="valor_final_display">R$ 0,00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Atualizar Venda</button>
                                    <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        $(document).ready(function() {
            console.log("jQuery is loaded:", typeof jQuery != 'undefined'); // Debugging: Check jQuery load
            let itemIndex = 0;
            let subtotal = 0;

            // Dados dos itens existentes (da venda ou old input em caso de validação)
            // Prioriza old('items') se houver, caso contrário, usa os itens da venda
            const initialItemsData = <?php echo json_encode(old('items', $venda->vendaItems->toArray()), 512) ?>;

            // Função para inicializar Select2
            function initializeSelect2(selector) {
                $(selector).select2({
                    placeholder: 'Busque uma ave pela matrícula ou tipo...',
                    minimumInputLength: 1,
                    language: {
                        inputTooShort: function() { return "Por favor, digite 1 ou mais caracteres para buscar."; },
                        noResults: function() { return "Nenhum resultado encontrado."; },
                        searching: function() { return "Buscando..."; }
                    },
                    ajax: {
                        url: '<?php echo e(route('financeiro.vendas.searchAvesForSale')); ?>',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) { return { q: params.term }; },
                        processResults: function(data) { return { results: data }; },
                        cache: true
                    },
                    templateResult: function(data) {
                        if (data.loading) return data.text;
                        return `${data.matricula} (${data.tipo_ave})`;
                    },
                    templateSelection: function(data) {
                        return data.matricula || data.text;
                    }
                });
            }

            // Inicializa Select2 para o campo de busca de ave
            initializeSelect2('#search_ave');

            // Adiciona item selecionado do Select2 ou item manual à tabela e inputs hidden
            $('#search_ave').on('select2:select', function(e) {
                const data = e.params.data;
                const aveId = data.id;
                const descricao = `${data.matricula} (${data.tipo_ave})`;
                const precoUnitario = parseFloat(data.preco_sugerido || 0).toFixed(2);
                addItemToFormAndTable(descricao, 1, precoUnitario, aveId);
                $(this).val(null).trigger('change');
            });

            $('#add_item_manual').on('click', function() {
                const descricao = $('#descricao_item_manual').val().trim();
                const precoUnitario = parseFloat($('#preco_item_manual').val());
                const quantidade = parseInt($('#quantidade_item_manual').val());

                if (descricao && !isNaN(precoUnitario) && precoUnitario > 0 && !isNaN(quantidade) && quantidade > 0) {
                    addItemToFormAndTable(descricao, quantidade, precoUnitario.toFixed(2), null);
                    $('#descricao_item_manual').val('');
                    $('#preco_item_manual').val('');
                    $('#quantidade_item_manual').val('1');
                } else {
                    Swal.fire({ // Usando SweetAlert2 para mensagens
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Por favor, preencha a descrição, quantidade e preço unitário válidos para o item manual.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });

            // Função principal para adicionar item ao formulário (inputs hidden) e à tabela de exibição
            function addItemToFormAndTable(descricao, quantidade, precoUnitario, aveId, existingItemIndex = null) {
                // Se um índice existente for fornecido, usa-o. Caso contrário, usa o itemIndex atual e o incrementa.
                const currentIndex = existingItemIndex !== null ? existingItemIndex : itemIndex++;
                const totalItem = (quantidade * parseFloat(precoUnitario)).toFixed(2);

                // Adiciona inputs hidden
                const hiddenInputsHtml = `
                    <div class="item-hidden-group" data-item-index="${currentIndex}">
                        <input type="hidden" name="items[${currentIndex}][descricao_item]" value="${descricao}">
                        <input type="hidden" name="items[${currentIndex}][quantidade]" value="${quantidade}" class="hidden-quantidade">
                        <input type="hidden" name="items[${currentIndex}][preco_unitario]" value="${precoUnitario}" class="hidden-preco-unitario">
                        <input type="hidden" name="items[${currentIndex}][valor_total_item]" value="${totalItem}" class="hidden-valor-total-item">
                        ${aveId ? `<input type="hidden" name="items[${currentIndex}][ave_id]" value="${aveId}">` : ''}
                    </div>
                `;
                $('#items-hidden-container').append(hiddenInputsHtml);

                // Adiciona linha à tabela visual
                const newRow = `
                    <tr data-item-index="${currentIndex}">
                        <td>${descricao}</td>
                        <td><input type="number" class="form-control item-quantidade" value="${quantidade}" min="1" style="width: 80px;"></td>
                        <td><input type="number" class="form-control item-preco-unitario" value="${precoUnitario}" min="0.01" step="0.01" style="width: 100px;"></td>
                        <td class="item-total-display">R$ ${totalItem.replace('.', ',')}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#items_table tbody').append(newRow);

                updateTotals();
            }

            // Remove item da tabela e dos inputs hidden (usando delegação de eventos)
            $(document).on('click', '.remove-item', function() {
                const row = $(this).closest('tr');
                const indexToRemove = row.data('item-index');
                $(`#items-hidden-container .item-hidden-group[data-item-index="${indexToRemove}"]`).remove();
                row.remove();
                updateTotals();
            });

            // Atualiza o total do item e os inputs hidden ao mudar quantidade ou preço unitário (usando delegação de eventos)
            $(document).on('input', '.item-quantidade, .item-preco-unitario', function() {
                const row = $(this).closest('tr');
                const quantidade = parseFloat(row.find('.item-quantidade').val()) || 0;
                const precoUnitario = parseFloat(row.find('.item-preco-unitario').val()) || 0;
                const totalItem = (quantidade * precoUnitario).toFixed(2);

                row.find('.item-total-display').text(`R$ ${totalItem.replace('.', ',')}`);

                // Atualiza os inputs hidden correspondentes
                const index = row.data('item-index');
                $(`#items-hidden-container .item-hidden-group[data-item-index="${index}"] .hidden-quantidade`).val(quantidade);
                $(`#items-hidden-container .item-hidden-group[data-item-index="${index}"] .hidden-preco-unitario`).val(precoUnitario);
                $(`#items-hidden-container .item-hidden-group[data-item-index="${index}"] .hidden-valor-total-item`).val(totalItem);

                updateTotals();
            });

            // Atualiza os totais da venda (subtotal, desconto, valor final)
            function updateTotals() {
                subtotal = 0;
                // Soma os valores dos inputs hidden para o subtotal
                $('#items-hidden-container .hidden-valor-total-item').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                const desconto = parseFloat($('#desconto').val()) || 0;
                let valorFinal = subtotal - desconto;

                if (valorFinal < 0) {
                    valorFinal = 0; // Garante que o valor final não seja negativo
                }

                $('#subtotal_display').text(`R$ ${subtotal.toFixed(2).replace('.', ',')}`);
                $('#desconto_display').text(`R$ ${desconto.toFixed(2).replace('.', ',')}`);
                $('#valor_final_display').text(`R$ ${valorFinal.toFixed(2).replace('.', ',')}`);
            }

            // Atualiza totais quando o desconto muda
            $('#desconto').on('input', function() {
                updateTotals();
            });

            // Inicializa o formulário com os itens existentes da venda
            initialItemsData.forEach(function(item) {
                addItemToFormAndTable(
                    item.descricao_item,
                    parseInt(item.quantidade),
                    parseFloat(item.preco_unitario),
                    item.ave_id || null
                );
            });

            // Garante que os totais são calculados na carga da página
            updateTotals();
        });
    </script>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/vendas/edit.blade.php ENDPATH**/ ?>