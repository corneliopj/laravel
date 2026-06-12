
<div class="card card-outline card-secondary item-row mb-3" id="item-row-<?php echo e($index); ?>">
    <div class="card-header">
        <h3 class="card-title">Item #<?php echo e((int)$index + 1); ?></h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool remove-item-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Tipo de Item</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="itens[<?php echo e($index); ?>][tipo_item]" 
                    id="tipo_individual_<?php echo e($index); ?>" value="individual" 
                    <?php echo e((isset($item) && $item->ave_id) ? 'checked' : (old("itens.$index.tipo_item") == 'individual' ? 'checked' : '')); ?>>
                <label class="form-check-label" for="tipo_individual_<?php echo e($index); ?>">Ave Individual</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="itens[<?php echo e($index); ?>][tipo_item]" 
                    id="tipo_plantel_<?php echo e($index); ?>" value="plantel" 
                    <?php echo e((isset($item) && $item->plantel_id) ? 'checked' : (old("itens.$index.tipo_item") == 'plantel' ? 'checked' : '')); ?>>
                <label class="form-check-label" for="tipo_plantel_<?php echo e($index); ?>">Plantel Agrupado</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="itens[<?php echo e($index); ?>][tipo_item]" 
                    id="tipo_generico_<?php echo e($index); ?>" value="generico" 
                    <?php echo e((!isset($item) && !old("itens.$index.tipo_item")) || (old("itens.$index.tipo_item") == 'generico') ? 'checked' : ''); ?>>
                <label class="form-check-label" for="tipo_generico_<?php echo e($index); ?>">Genérico</label>
            </div>
            <?php $__errorArgs = ["itens.$index.tipo_item"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div id="div_ave_id_<?php echo e($index); ?>" class="form-group" 
            style="<?php echo e((isset($item) && $item->ave_id) || old("itens.$index.tipo_item") == 'individual' ? '' : 'display:none;'); ?>">
            <label for="itens_<?php echo e($index); ?>_ave_id">Ave Individual</label>
            <select name="itens[<?php echo e($index); ?>][ave_id]" id="itens_<?php echo e($index); ?>_ave_id" class="form-control">
                <option value="">Selecione uma Ave</option>
                <?php $__currentLoopData = $avesDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ave->id); ?>" 
                        <?php echo e((isset($item) && $item->ave_id == $ave->id) || old("itens.$index.ave_id") == $ave->id ? 'selected' : ''); ?>>
                        <?php echo e($ave->matricula); ?> (<?php echo e($ave->tipoAve->nome ?? 'N/A'); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ["itens.$index.ave_id"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div id="div_plantel_id_<?php echo e($index); ?>" 
            style="<?php echo e((isset($item) && $item->plantel_id) || old("itens.$index.tipo_item") == 'plantel' ? '' : 'display:none;'); ?>">
            <div class="form-group">
                <label for="itens_<?php echo e($index); ?>_plantel_id">Plantel Agrupado</label>
                <select name="itens[<?php echo e($index); ?>][plantel_id]" id="itens_<?php echo e($index); ?>_plantel_id" class="form-control">
                    <option value="">Selecione um Plantel</option>
                    <?php $__currentLoopData = $plantelOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plantel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($plantel->id); ?>" 
                            <?php echo e((isset($item) && $item->plantel_id == $plantel->id) || old("itens.$index.plantel_id") == $plantel->id ? 'selected' : ''); ?>>
                            <?php echo e($plantel->identificacao_grupo); ?> (Qtd. Atual: <?php echo e($plantel->quantidade_atual); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ["itens.$index.plantel_id"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="itens_<?php echo e($index); ?>_descricao_item">Descrição do Item</label>
            <input type="text" name="itens[<?php echo e($index); ?>][descricao_item]" 
                id="itens_<?php echo e($index); ?>_descricao_item" class="form-control" 
                value="<?php echo e(old("itens.$index.descricao_item", $item->descricao_item ?? '')); ?>" 
                placeholder="Ex: Ave, Ovo, Raçao, Codornas" required>
            <?php $__errorArgs = ["itens.$index.descricao_item"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="itens_<?php echo e($index); ?>_quantidade">Quantidade</label>
            <input type="number" name="itens[<?php echo e($index); ?>][quantidade]" 
                id="itens_<?php echo e($index); ?>_quantidade" class="form-control" 
                value="<?php echo e(old("itens.$index.quantidade", $item->quantidade ?? 1)); ?>" 
                min="1" required 
                <?php echo e((isset($item) && $item->ave_id) || old("itens.$index.tipo_item") == 'individual' ? 'readonly' : ''); ?>>
            <?php $__errorArgs = ["itens.$index.quantidade"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="itens_<?php echo e($index); ?>_preco_unitario">Preço Unitário (R$)</label>
            <input type="number" name="itens[<?php echo e($index); ?>][preco_unitario]" 
                id="itens_<?php echo e($index); ?>_preco_unitario" class="form-control" 
                value="<?php echo e(old("itens.$index.preco_unitario", $item->preco_unitario ?? '')); ?>" 
                step="0.01" min="0.01" required>
            <?php $__errorArgs = ["itens.$index.preco_unitario"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/financeiro/vendas/partials/item_row.blade.php ENDPATH**/ ?>