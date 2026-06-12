<?php
    $pageTitle = 'Detalhes da Morte';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Detalhes da Morte: #<?php echo e($morte->id); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('mortes.index')); ?>">Mortes</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
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
                                <h3 class="card-title">Informações da Morte</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID:</label>
                                    <p><?php echo e($morte->id); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Registro:</label>
                                    <p>
                                        <?php if($morte->ave_id): ?>
                                            Ave Individual
                                        <?php elseif($morte->plantel_id): ?>
                                            Plantel Agrupado
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <?php if($morte->ave_id): ?>
                                    <div class="form-group">
                                        <label>Ave:</label>
                                        <p>
                                            <a href="<?php echo e(route('aves.show', $morte->ave_id)); ?>"><?php echo e($morte->ave->matricula ?? 'Ave Removida'); ?></a>
                                            (<?php echo e($morte->ave->tipoAve->nome ?? 'N/A'); ?>)
                                        </p>
                                    </div>
                                <?php elseif($morte->plantel_id): ?>
                                    <div class="form-group">
                                        <label>Plantel:</label>
                                        <p>
                                            <a href="<?php echo e(route('plantel.show', $morte->plantel_id)); ?>"><?php echo e($morte->plantel->identificacao_grupo ?? 'Plantel Removido'); ?></a>
                                            (Qtd. Atual: <?php echo e($morte->plantel->quantidade_atual ?? 'N/A'); ?>)
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Quantidade de Aves Mortas no Plantel:</label>
                                        <p><?php echo e($morte->quantidade_mortes_plantel); ?></p>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label>Data da Morte:</label>
                                    <p><?php echo e($morte->data_morte->format('d/m/Y')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Causa da Morte:</label>
                                    <p><?php echo e($morte->causa_morte ?? 'Não informada'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p><?php echo e($morte->observacoes ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Registrado em:</label>
                                    <p><?php echo e($morte->created_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p><?php echo e($morte->updated_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="<?php echo e(route('mortes.edit', $morte->id)); ?>" class="btn btn-warning">Editar</a>
                                <a href="<?php echo e(route('mortes.index')); ?>" class="btn btn-secondary">Voltar à Lista</a>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/mortes/show.blade.php ENDPATH**/ ?>