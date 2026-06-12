<?php
    $pageTitle = 'Editar Acasalamento';
?>



<?php $__env->startSection('content'); ?>
<div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Editar Acasalamento</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('acasalamentos.index')); ?>">Acasalamentos</a></li>
                            <li class="breadcrumb-item active">Editar</li>
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
                                <h3 class="card-title">Dados do Acasalamento</h3>
                            </div>
                            <form action="<?php echo e(route('acasalamentos.update', $acasalamento->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?> 
                                <?php echo method_field('PUT'); ?> 
                                <div class="card-body">
                                    
                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul>
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <input type="hidden" name="id" id="id" value="<?php echo e(old('id', $acasalamento->id)); ?>">
                                    <div class="form-group">
                                        <label>Macho (Ave)</label>
                                        
                                        <p class="form-control-static"><?php echo e($acasalamento->macho->matricula ?? 'N/A'); ?> (<?php echo e($acasalamento->macho->tipoAve->nome ?? 'N/A'); ?>)</p>
                                        <input type="hidden" name="macho_id" value="<?php echo e($acasalamento->macho_id); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Fêmea (Ave)</label>
                                        
                                        <p class="form-control-static"><?php echo e($acasalamento->femea->matricula ?? 'N/A'); ?> (<?php echo e($acasalamento->femea->tipoAve->nome ?? 'N/A'); ?>)</p>
                                        <input type="hidden" name="femea_id" value="<?php echo e($acasalamento->femea_id); ?>">
                                    </div>

                                    
                                    <div class="form-group">
                                        <label>Espécie do Acasalamento</label>
                                        <p class="form-control-static"><?php echo e($acasalamento->macho->tipoAve->nome ?? 'N/A'); ?></p>
                                        
                                        <input type="hidden" name="selected_tipo_ave_id" value="<?php echo e($acasalamento->macho->tipo_ave_id ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="data_inicio">Data de Início</label>
                                        <input type="date" name="data_inicio" class="form-control <?php $__errorArgs = ['data_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_inicio" value="<?php echo e(old('data_inicio', $acasalamento->data_inicio->format('Y-m-d'))); ?>" required readonly> 
                                        <?php $__errorArgs = ['data_inicio'];
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
                                        <label for="data_fim">Data de Fim (Para Encerrar Acasalamento)</label>
                                        <input type="date" name="data_fim" class="form-control <?php $__errorArgs = ['data_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_fim" value="<?php echo e(old('data_fim', $acasalamento->data_fim ? $acasalamento->data_fim->format('Y-m-d') : '')); ?>">
                                        <?php if(!$acasalamento->data_fim): ?>
                                            <small class="form-text text-warning">Este acasalamento está **em andamento**. Preencha a data de fim para encerrá-lo.</small>
                                        <?php else: ?>
                                            <small class="form-text text-info">Este acasalamento foi encerrado em **<?php echo e($acasalamento->data_fim->format('d/m/Y')); ?>**.</small>
                                        <?php endif; ?>
                                        <?php $__errorArgs = ['data_fim'];
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
unset($__errorArgs, $__bag); ?>" id="observacoes" rows="3"><?php echo e(old('observacoes', $acasalamento->observacoes)); ?></textarea>
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
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                    <a href="<?php echo e(route('acasalamentos.index')); ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/acasalamentos/editar.blade.php ENDPATH**/ ?>