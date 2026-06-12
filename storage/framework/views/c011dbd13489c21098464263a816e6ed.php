<?php
    $pageTitle = 'Detalhes do Plantel';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Detalhes do Plantel: <?php echo e($plantel->identificacao_grupo); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('plantel.index')); ?>">Plantéis</a></li>
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
                                <h3 class="card-title">Informações do Plantel</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label>ID:</label>
                                    <p><?php echo e($plantel->id); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Identificação do Grupo:</label>
                                    <p><?php echo e($plantel->identificacao_grupo); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Ave:</label>
                                    <p><?php echo e($plantel->tipoAve->nome ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Data de Formação:</label>
                                    <p><?php echo e($plantel->data_formacao->format('d/m/Y')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Inicial:</label>
                                    <p><?php echo e($plantel->quantidade_inicial); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade Atual:</label>
                                    <p><?php echo e($quantidadeAtual); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Ativo:</label>
                                    <p>
                                        <?php if($plantel->ativo): ?>
                                            <span class="badge badge-success">Sim</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Não</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label>Observações:</label>
                                    <p><?php echo e($plantel->observacoes ?? 'N/A'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Criado em:</label>
                                    <p><?php echo e($plantel->created_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Última Atualização:</label>
                                    <p><?php echo e($plantel->updated_at->format('d/m/Y H:i:s')); ?></p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="<?php echo e(route('plantel.edit', $plantel->id)); ?>" class="btn btn-warning">Editar</a>
                                <a href="<?php echo e(route('plantel.index')); ?>" class="btn btn-secondary">Voltar à Lista</a>
                            </div>
                        </div>
                        <!-- /.card -->

                        <!-- Card de Movimentações do Plantel -->
                        <div class="card card-info mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Histórico de Movimentações</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                                <th>Data</th>
                                                <th>Observações</th>
                                                <th>Criado em</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $plantel->movimentacoes->sortByDesc('data_movimentacao'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimentacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($movimentacao->id); ?></td>
                                                    <td><?php echo e(ucfirst(str_replace('_', ' ', $movimentacao->tipo_movimentacao))); ?></td>
                                                    <td><?php echo e($movimentacao->quantidade); ?></td>
                                                    <td><?php echo e($movimentacao->data_movimentacao->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($movimentacao->observacoes ?? 'N/A'); ?></td>
                                                    <td><?php echo e($movimentacao->created_at->format('d/m/Y H:i:s')); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Nenhuma movimentação para este plantel.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                      

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <a href="<?php echo e(route('plantel.edit', $plantel->id)); ?>" class="btn btn-warning">Editar Plantel</a>
                                <a href="<?php echo e(route('plantel.index')); ?>" class="btn btn-secondary">Voltar à Lista</a>
                                
                                <a href="<?php echo e(route('plantel.movimentacoes.create', ['plantel' => $plantel->id])); ?>" class="btn btn-success float-right">
                                    <i class="fas fa-plus"></i> Adicionar Movimentação
                                </a>
                            </div>
                        </div>
                        <!-- /.card -->

                        
                        <div class="card card-info mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Histórico de Movimentações</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('movimentacoes-plantel.index', ['plantel_id' => $plantel->id])); ?>" class="btn btn-tool btn-sm">
                                        <i class="fas fa-list"></i> Ver Todas
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo</th>
                                                <th>Quantidade</th>
                                                <th>Data</th>
                                                <th>Observações</th>
                                                <th>Criado em</th>
                                                <th style="width: 100px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $plantel->movimentacoes->sortByDesc('data_movimentacao'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimentacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($movimentacao->id); ?></td>
                                                    <td><?php echo e(ucfirst(str_replace('_', ' ', $movimentacao->tipo_movimentacao))); ?></td>
                                                    <td><?php echo e($movimentacao->quantidade); ?></td>
                                                    <td><?php echo e($movimentacao->data_movimentacao->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($movimentacao->observacoes ?? 'N/A'); ?></td>
                                                    <td><?php echo e($movimentacao->created_at->format('d/m/Y H:i:s')); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('movimentacoes-plantel.show', $movimentacao->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('movimentacoes-plantel.edit', $movimentacao->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">Nenhuma movimentação para este plantel.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/plantel/show.blade.php ENDPATH**/ ?>