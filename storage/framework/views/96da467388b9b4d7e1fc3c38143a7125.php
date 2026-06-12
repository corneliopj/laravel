<?php
    $pageTitle = 'Editar Tipo de Ave';
?>



<?php $__env->startSection('content'); ?>
<div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Editar Tipo de Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('tipos_aves.index')); ?>">Tipos de Aves</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6"> 
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados do Tipo de Ave</h3>
                            </div>
                            <form action="<?php echo e(route('tipos_aves.update', $tipoAve->id)); ?>" method="POST">
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

                                    <input type="hidden" name="id" id="id" value="<?php echo e(old('id', $tipoAve->id)); ?>">
                                    <div class="form-group">
                                        <label for="nome">Nome do Tipo de Ave</label>
                                        <input type="text" name="nome" class="form-control <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nome" placeholder="Ex: Galo de Campina, Canário, Bicudo" value="<?php echo e(old('nome', $tipoAve->nome)); ?>" required autofocus>
                                        <?php $__errorArgs = ['nome'];
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
                                        <label for="tempo_eclosao">Tempo de Eclosão (dias)</label>
                                        <input type="number" name="tempo_eclosao" class="form-control <?php $__errorArgs = ['tempo_eclosao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tempo_eclosao" placeholder="Ex: 21 (para galinhas)" value="<?php echo e(old('tempo_eclosao', $tipoAve->tempo_eclosao)); ?>" min="1">
                                        <?php $__errorArgs = ['tempo_eclosao'];
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
                                        <small class="form-text text-muted">Número de dias para a eclosão dos ovos deste tipo de ave.</small>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" <?php echo e(old('ativo', $tipoAve->ativo) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="ativo">Ativo</label>
                                            <small class="form-text text-muted">Marque se este tipo de ave está ativo para uso.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                    <a href="<?php echo e(route('tipos_aves.index')); ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/tipos_aves/editar.blade.php ENDPATH**/ ?>