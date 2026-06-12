<?php
    $pageTitle = 'Listagem de Plantéis';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listagem de Plantéis</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Plantéis</li>
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
                                <h3 class="card-title">Plantéis Cadastrados</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('plantel.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Novo Plantel
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
                                                <th>Identificação do Grupo</th>
                                                <th>Tipo de Ave</th>
                                                <th>Data de Formação</th>
                                                <th>Qtd. Inicial</th>
                                                <th>Qtd. Atual</th>
                                                <th>Ativo</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $plantelData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plantel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($plantel['id']); ?></td>
                                                    <td><?php echo e($plantel['identificacao_grupo']); ?></td>
                                                    <td><?php echo e($plantel['tipo_ave_nome']); ?></td>
                                                    <td><?php echo e($plantel['data_formacao']); ?></td>
                                                    <td><?php echo e($plantel['quantidade_inicial']); ?></td>
                                                    <td><?php echo e($plantel['quantidade_atual']); ?></td>
                                                    <td>
                                                        <?php if($plantel['ativo']): ?>
                                                            <span class="badge badge-success">Sim</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Não</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e($plantel['link_detalhes']); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e($plantel['link_editar']); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('plantel.destroy', $plantel['id'])); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este plantel? Todas as movimentações associadas serão removidas.');">
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
                                                    <td colspan="8" class="text-center">Nenhum plantel cadastrado.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/plantel/index.blade.php ENDPATH**/ ?>