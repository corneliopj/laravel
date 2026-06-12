<?php
    $pageTitle = 'Listagem de Movimentações de Plantel';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            <?php if($plantel): ?>
                                Movimentações para Plantel: <?php echo e($plantel->identificacao_grupo); ?>

                            <?php else: ?>
                                Listagem de Movimentações de Plantel
                            <?php endif; ?>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('plantel.index')); ?>">Plantéis</a></li>
                            <?php if($plantel): ?>
                                <li class="breadcrumb-item"><a href="<?php echo e(route('plantel.show', $plantel->id)); ?>">Detalhes do Plantel</a></li>
                            <?php endif; ?>
                            <li class="breadcrumb-item active">Movimentações</li>
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
                                <h3 class="card-title">Movimentações Registradas</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('movimentacoes-plantel.create', ['plantel_id' => $plantel->id ?? ''])); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Movimentação
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
                                                <th>Plantel</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                                <th>Data</th>
                                                <th>Observações</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $movimentacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimentacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($movimentacao->id); ?></td>
                                                    <td>
                                                        <?php if($movimentacao->plantel): ?>
                                                            <a href="<?php echo e(route('plantel.show', $movimentacao->plantel->id)); ?>"><?php echo e($movimentacao->plantel->identificacao_grupo); ?></a>
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e(ucfirst(str_replace('_', ' ', $movimentacao->tipo_movimentacao))); ?></td>
                                                    <td><?php echo e($movimentacao->quantidade); ?></td>
                                                    <td><?php echo e($movimentacao->data_movimentacao->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($movimentacao->observacoes ?? 'N/A'); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('movimentacoes-plantel.show', $movimentacao->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('movimentacoes-plantel.edit', $movimentacao->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('movimentacoes-plantel.destroy', $movimentacao->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta movimentação?');">
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
                                                    <td colspan="7" class="text-center">Nenhuma movimentação encontrada.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                <?php echo e($movimentacoes->links('pagination::bootstrap-4')); ?> 
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/movimentacoes_plantel/index.blade.php ENDPATH**/ ?>