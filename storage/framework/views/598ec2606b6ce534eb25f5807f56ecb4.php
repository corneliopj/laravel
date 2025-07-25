<div class="card card-outline card-info mt-3 item-row" data-index="<?php echo e($index); ?>">
    <div class="card-header">
        <h3 class="card-title">Item #<?php echo e((int)$index + 1); ?></h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool remove-item" title="Remover Item">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="items_<?php echo e($index); ?>_ave_id">Ave (Opcional)</label>
                <select class="form-control select2-ave <?php $__errorArgs = ['items.' . $index . '.ave_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="item-ave-id-<?php echo e($index); ?>"
                        name="items[<?php echo e($index); ?>][ave_id]">
                    <option value="">Selecione uma ave</option>
                    
                    <?php if(isset($item['ave_id']) && $item['ave_id']): ?>
                        <?php
                            // Tenta encontrar a ave na lista de avesDisponiveis
                            $selectedAve = $avesDisponiveis->firstWhere('id', $item['ave_id']);
                        ?>
                        <?php if($selectedAve): ?>
                            <option value="<?php echo e($selectedAve->id); ?>" selected>
                                <?php echo e($selectedAve->matricula); ?> (<?php echo e($selectedAve->tipoAve->nome ?? 'N/A'); ?>)
                            </option>
                        <?php else: ?>
                            
                            <option value="<?php echo e($item['ave_id']); ?>" selected>
                                Ave ID: <?php echo e($item['ave_id']); ?> (Não disponível para seleção)
                            </option>
                        <?php endif; ?>
                    <?php endif; ?>
                </select>
                <?php $__errorArgs = ['items.' . $index . '.ave_id'];
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
            <div class="form-group col-md-6">
                <label for="items_<?php echo e($index); ?>_descricao_item">Descrição do Item <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php $__errorArgs = ['items.' . $index . '.descricao_item'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       id="items_<?php echo e($index); ?>_descricao_item"
                       name="items[<?php echo e($index); ?>][descricao_item]"
                       value="<?php echo e(old('items.' . $index . '.descricao_item', $item['descricao_item'] ?? '')); ?>"
                       placeholder="Ex: Ave, Ovo, Serviço" required>
                <?php $__errorArgs = ['items.' . $index . '.descricao_item'];
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
        <div class="row">
            <div class="form-group col-md-4">
                <label for="items_<?php echo e($index); ?>_quantidade">Quantidade <span class="text-danger">*</span></label>
                <input type="number" class="form-control <?php $__errorArgs = ['items.' . $index . '.quantidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       id="items_<?php echo e($index); ?>_quantidade"
                       name="items[<?php echo e($index); ?>][quantidade]"
                       value="<?php echo e(old('items.' . $index . '.quantidade', $item['quantidade'] ?? 1)); ?>" min="1" required>
                <?php $__errorArgs = ['items.' . $index . '.quantidade'];
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
                <label for="items_<?php echo e($index); ?>_preco_unitario">Preço Unitário <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['items.' . $index . '.preco_unitario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       id="items_<?php echo e($index); ?>_preco_unitario"
                       name="items[<?php echo e($index); ?>][preco_unitario]"
                       value="<?php echo e(old('items.' . $index . '.preco_unitario', $item['preco_unitario'] ?? 0.00)); ?>" min="0.01" required>
                <?php $__errorArgs = ['items.' . $index . '.preco_unitario'];
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
                <label>Total do Item</label>
                <p class="form-control-static item-total-display">R$ <?php echo e(number_format(((float)($item['quantidade'] ?? 0)) * ((float)($item['preco_unitario'] ?? 0)), 2, ',', '.')); ?></p>
                <input type="hidden" name="items[<?php echo e($index); ?>][valor_total_item]" value="<?php echo e(old('items.' . $index . '.valor_total_item', ((float)($item['quantidade'] ?? 0)) * ((float)($item['preco_unitario'] ?? 0)))); ?>">
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/vendas/partials/item_form_fields.blade.php ENDPATH**/ ?>