<?php
    $pageTitle = 'Detalhes da Incubação';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Detalhes da Incubação: #<?php echo e($incubacao->id); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('incubacoes.index')); ?>">Incubações</a></li>
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
                                <h3 class="card-title">Informações da Incubação</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID da Incubação:</label>
                                    <p><?php echo e($incubacao->id); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Ave:</label>
                                    <p><?php echo e($incubacao->tipoAve->nome ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Lote de Ovos:</label>
                                    <p><?php echo e($incubacao->lote->identificacao_lote ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Postura de Ovo:</label>
                                    
                                    <p><?php echo e(optional($incubacao->posturaOvo)->data_inicio_postura?->format('d/m/Y') ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Data de Entrada na Incubadora:</label>
                                    <p><?php echo e($incubacao->data_entrada_incubadora->format('d/m/Y')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Data Prevista de Eclosão:</label>
                                    <p><?php echo e($incubacao->data_prevista_eclosao->format('d/m/Y')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade de Ovos:</label>
                                    <p><?php echo e($incubacao->quantidade_ovos); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Eclodidos:</label>
                                    <p><?php echo e($incubacao->quantidade_eclodidos ?? 'Não informado'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Infértil:</label>
                                    <p><?php echo e($incubacao->quantidade_inferteis ?? 'Não informado'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Infectados:</label>
                                    <p><?php echo e($incubacao->quantidade_infectados ?? 'Não informado'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Mortos:</label>
                                    <p><?php echo e($incubacao->quantidade_mortos ?? 'Não informado'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Chocadeira:</label>
                                    <p><?php echo e($incubacao->chocadeira ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p><?php echo e($incubacao->observacoes ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Status:</label>
                                    <p>
                                        <?php if($incubacao->ativo): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inativo</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label>Registrado em:</label>
                                    <p><?php echo e($incubacao->created_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p><?php echo e($incubacao->updated_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="<?php echo e(route('incubacoes.edit', $incubacao->id)); ?>" class="btn btn-warning">Editar</a>
                                <a href="<?php echo e(route('incubacoes.index')); ?>" class="btn btn-secondary">Voltar à Lista</a>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/incubacoes/show.blade.php ENDPATH**/ ?>