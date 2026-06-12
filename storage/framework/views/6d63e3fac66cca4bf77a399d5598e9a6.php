<?php
    $pageTitle = 'Registrar Morte de Ave';
?>



<?php $__env->startSection('content'); ?>
<div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Registrar Morte de Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('aves.index')); ?>">Aves</a></li>
                            <li class="breadcrumb-item active">Registrar Morte</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informações da Ave</h3>
                            </div>
                            <div class="card-body">
                                <?php if(isset($ave)): ?>
                                    <p><strong>ID:</strong> <?php echo e($ave->id); ?></p>
                                    <p><strong>Matrícula:</strong> <?php echo e($ave->matricula); ?></p>
                                    <p><strong>Tipo:</strong> <?php echo e($ave->tipoAve->nome ?? 'N/A'); ?></p> 
                                <?php else: ?>
                                    <p class="text-danger">Ave não encontrada.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Registro de Morte</h3>
                            </div>
                            <form action="<?php echo e(route('aves.storeDeath')); ?>" method="post">
                                <?php echo csrf_field(); ?> 
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

                                    
                                    <?php if(session('error')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo e(session('error')); ?>

                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <input type="hidden" name="ave_id" value="<?php echo e(old('ave_id', $ave->id ?? '')); ?>">

                                    <div class="form-group">
                                        <label for="data_morte">Data da Morte</label>
                                        <input type="date" class="form-control" id="data_morte" name="data_morte" required value="<?php echo e(old('data_morte')); ?>">
                                        
                                        <?php $__errorArgs = ['data_morte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="causa">Causa da Morte (Opcional)</label>
                                        <input type="text" class="form-control" id="causa" name="causa" value="<?php echo e(old('causa')); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="observacoes">Observações (Opcional)</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo e(old('observacoes')); ?></textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Registrar Morte</button>
                                    <a href="<?php echo e(route('aves.index')); ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/aves/registrar_morte.blade.php ENDPATH**/ ?>