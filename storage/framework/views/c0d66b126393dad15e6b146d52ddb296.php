<?php
    $pageTitle = 'Editar Venda #' . $venda->id;
?>



<?php $__env->startSection('content'); ?>
<section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Venda #<?php echo e($venda->id); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.vendas.index')); ?>">Vendas</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form action="<?php echo e(route('financeiro.vendas.update', $venda->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Dados da Venda</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Comprador</label>
                                        <input type="text" name="comprador" id="comprador" class="form-control" list="compradores" required value="<?php echo e(old('comprador', $venda->comprador)); ?>">
                                        <datalist id="compradores">
                                            <?php $__currentLoopData = $compradores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comprador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($comprador); ?>">
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </datalist>
                                        <?php $__errorArgs = ['comprador'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Data da Venda</label>
                                        <input type="datetime-local" name="data_venda" class="form-control" value="<?php echo e(old('data_venda', $venda->data_venda->format('Y-m-d\TH:i'))); ?>" required>
                                        <?php $__errorArgs = ['data_venda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Método de Pagamento</label>
                                        <select name="metodo_pagamento" class="form-control" required>
                                            <option value="">Selecione</option>
                                            <?php $__currentLoopData = $metodosPagamento; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e(old('metodo_pagamento', $venda->metodo_pagamento) == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['metodo_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e(old('status', $venda->status) == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="observacoes" class="form-control" rows="2"><?php echo e(old('observacoes', $venda->observacoes)); ?></textarea>
                                        <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                    <?php if(old('itens')): ?>
                                        <?php $__currentLoopData = old('itens'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo $__env->make('financeiro.vendas.partials.item_row', [
                                                'index' => $index,
                                                'item' => (object) $item,
                                                'avesDisponiveis' => $avesDisponiveis,
                                                'plantelOptions' => $plantelOptions,
                                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <?php $__currentLoopData = $venda->vendaItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo $__env->make('financeiro.vendas.partials.item_row', [
                                                'index' => $index,
                                                'item' => $item,
                                                'avesDisponiveis' => $avesDisponiveis,
                                                'plantelOptions' => $plantelOptions,
                                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <button type="button" id="add_item_btn" class="btn btn-info">
                                        <i class="fas fa-plus"></i> Adicionar Item
                                    </button>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Desconto (R$)</label>
                                                <input type="number" name="desconto" id="desconto" class="form-control" value="<?php echo e(old('desconto', $venda->desconto)); ?>" min="0" step="0.01">
                                                <?php $__errorArgs = ['desconto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Comissão (%)</label>
                                                <input type="number" name="comissao_percentual" id="comissao_percentual" class="form-control" value="<?php echo e(old('comissao_percentual', $venda->comissao_percentual)); ?>" min="0" max="100" step="0.01">
                                                <?php $__errorArgs = ['comissao_percentual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="text-danger"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 align-self-center">
                                            <h4>Total: <span id="total-venda">R$ <?php echo e(number_format($venda->valor_final, 2, ',', '.')); ?></span></h4>
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
                            <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// O mesmo script do create.blade.php, mas com inicialização de valores
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = <?php echo e(old('itens') ? count(old('itens')) : count($venda->vendaItems)); ?>;
    const itemsContainer = document.getElementById('items_container');
    const addItemBtn = document.getElementById('add_item_btn');
    const descontoInput = document.getElementById('desconto');

    // Função para inicializar uma linha de item
    function initializeItemRow(rowElement) {
        const tipoItemRadios = rowElement.querySelectorAll('input[type="radio"]');
        const divAveId = rowElement.querySelector('[id^="div_ave_id_"]');
        const divPlantelId = rowElement.querySelector('[id^="div_plantel_id_"]');
        const quantidadeInput = rowElement.querySelector('input[name$="[quantidade]"]');
        const precoInput = rowElement.querySelector('input[name$="[preco_unitario]"]');

        function toggleItemFields() {
            const checkedRadio = rowElement.querySelector('input[type="radio"]:checked');

            // Se nenhum rádio estiver selecionado (ex: nova linha ou item mal configurado),
            // define um estado padrão e sai para evitar o erro.
            if (!checkedRadio) {
                if (divAveId) divAveId.style.display = 'none';
                if (divPlantelId) divPlantelId.style.display = 'none';
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
            } else { // 'outro'
                divAveId.style.display = 'none';
                divPlantelId.style.display = 'none';
                quantidadeInput.readOnly = false;
            }
            updateTotals(); // Atualiza os totais quando o tipo de item muda
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
            <?php echo $__env->make('financeiro.vendas.partials.item_row', [
                'index' => 'ITEM_INDEX_PLACEHOLDER',
                'item' => null,
                'avesDisponiveis' => $avesDisponiveis,
                'plantelOptions' => $plantelOptions,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/financeiro/vendas/edit.blade.php ENDPATH**/ ?>