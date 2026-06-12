<?php
    $pageTitle = 'Listagem de Suínos';
?>



<?php $__env->startSection('content'); ?>
<section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listagem de Suínos</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Suínos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Gerenciar Suínos</h3>
                        <div class="card-tools">
                            <a href="<?php echo e(route('suinos.create')); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Novo Suíno
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Matrícula</th>
                                        <th>Sexo</th>
                                        <th>Vendável</th>
                                        <th>Status</th>
                                        <th style="width: 150px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $suinos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suino): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($suino->id); ?></td>
                                            <td><?php echo e($suino->matricula); ?></td>
                                            <td><?php echo e($suino->sexo); ?></td>
                                            <td><?php echo e($suino->vendavel ? 'Sim' : 'Não'); ?></td>
                                            <td>
                                                <?php if($suino->ativo): ?>
                                                    <span class="badge badge-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('suinos.show', $suino->id)); ?>" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('suinos.edit', $suino->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('suinos.destroy', $suino->id)); ?>" method="POST" style="display:inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhum suíno encontrado</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <?php echo e($suinos->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
            </div>
        </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/suinos/index.blade.php ENDPATH**/ ?>