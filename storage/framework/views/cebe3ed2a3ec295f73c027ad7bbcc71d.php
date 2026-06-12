<?php
    $pageTitle = 'Detalhes da Movimentação de Plantel';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Detalhes da Movimentação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('plantel.index')); ?>">Plantéis</a></li>
                            <?php if($movimentacaoPlantel->plantel): ?>
                                <li class="breadcrumb-item"><a href="<?php echo e(route('plantel.show', $movimentacaoPlantel->plantel->id)); ?>">Detalhes do Plantel</a></li>
                            <?php endif; ?>
                            <li class="breadcrumb-item active">Detalhes da Movimentação</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Informações da Movimentação</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID:</label>
                                    <p><?php echo e($movimentacaoPlantel->id); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Plantel:</label>
                                    <p>
                                        <?php if($movimentacaoPlantel->plantel): ?>
                                            <a href="<?php echo e(route('plantel.show', $movimentacaoPlantel->plantel->id)); ?>"><?php echo e($movimentacaoPlantel->plantel->identificacao_grupo); ?></a>
                                            (Qtd. Atual: <?php echo e($movimentacaoPlantel->plantel->quantidade_atual); ?>)
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Movimentação:</label>
                                    <p><?php echo e(ucfirst(str_replace('_', ' ', $movimentacaoPlantel->tipo_movimentacao))); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade:</label>
                                    <p><?php echo e($movimentacaoPlantel->quantidade); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Data da Movimentação:</label>
                                    <p><?php echo e($movimentacaoPlantel->data_movimentacao->format('d/m/Y')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p><?php echo e($movimentacaoPlantel->observacoes ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Criado em:</label>
                                    <p><?php echo e($movimentacaoPlantel->created_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p><?php echo e($movimentacaoPlantel->updated_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="<?php echo e(route('movimentacoes-plantel.edit', $movimentacaoPlantel->id)); ?>" class="btn btn-warning">Editar</a>
                                <a href="<?php echo e(route('movimentacoes-plantel.index', ['plantel_id' => $movimentacaoPlantel->plantel_id])); ?>" class="btn btn-secondary">Voltar às Movimentações</a>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/movimentacoes_plantel/show.blade.php ENDPATH**/ ?>