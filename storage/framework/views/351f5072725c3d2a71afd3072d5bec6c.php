<?php
    $pageTitle = 'Listagem de Mortes';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listagem de Mortes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Mortes</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Registros de Morte</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('mortes.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Registrar Morte
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo</th>
                                                <th>Identificação</th>
                                                <th>Quantidade</th>
                                                <th>Data da Morte</th>
                                                <th>Causa</th>
                                                <th>Observações</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $mortes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $morte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($morte->id); ?></td>
                                                    <td>
                                                        <?php if($morte->ave_id): ?>
                                                            Individual
                                                        <?php elseif($morte->plantel_id): ?>
                                                            Plantel
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($morte->ave_id): ?>
                                                            <a href="<?php echo e(route('aves.show', $morte->ave_id)); ?>"><?php echo e($morte->ave->matricula ?? 'Ave Removida'); ?></a>
                                                        <?php elseif($morte->plantel_id): ?>
                                                            <a href="<?php echo e(route('plantel.show', $morte->plantel_id)); ?>"><?php echo e($morte->plantel->identificacao_grupo ?? 'Plantel Removido'); ?></a>
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($morte->quantidade_mortes_plantel ?? 1); ?></td> 
                                                    <td><?php echo e($morte->data_morte->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($morte->causa_morte ?? 'Não informada'); ?></td>
                                                    <td><?php echo e($morte->observacoes ?? 'N/A'); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('mortes.show', $morte->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('mortes.edit', $morte->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('mortes.destroy', $morte->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este registro de morte? Se for uma ave individual, ela será reativada. Se for de um plantel, a quantidade será revertida.');">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">Nenhum registro de morte encontrado.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                <?php echo e($mortes->links('pagination::bootstrap-4')); ?>

                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/mortes/index.blade.php ENDPATH**/ ?>