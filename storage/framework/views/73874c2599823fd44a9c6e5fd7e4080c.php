<?php
    $pageTitle = 'Listagem de Incubações';
?>



<?php $__env->startSection('content'); ?>
<!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listagem de Incubações</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Incubações</li>
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
                                <h3 class="card-title">Registros de Incubação</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('incubacoes.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Incubação
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                
                                <?php if(session('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo e(session('success')); ?>

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

                                
                                <form action="<?php echo e(route('incubacoes.index')); ?>" method="GET" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">Todos</option>
                                                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tipo_ave_id">Tipo de Ave</label>
                                                <select name="tipo_ave_id" id="tipo_ave_id" class="form-control">
                                                    <option value="">Todos</option>
                                                    <?php $__currentLoopData = $tiposAves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipoAve): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($tipoAve->id); ?>" <?php echo e(request('tipo_ave_id') == $tipoAve->id ? 'selected' : ''); ?>><?php echo e($tipoAve->nome); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_entrada_inicio">Data Entrada (Início)</label>
                                                <input type="date" name="data_entrada_inicio" id="data_entrada_inicio" class="form-control" value="<?php echo e(request('data_entrada_inicio')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_entrada_fim">Data Entrada (Fim)</label>
                                                <input type="date" name="data_entrada_fim" id="data_entrada_fim" class="form-control" value="<?php echo e(request('data_entrada_fim')); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-primary">Filtrar</button>
                                            <a href="<?php echo e(route('incubacoes.index')); ?>" class="btn btn-secondary">Limpar Filtros</a>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo de Ave</th>
                                                <th>Lote de Ovos</th>
                                                <th>Postura de Ovo</th>
                                                <th>Data Entrada</th>
                                                <th>Data Prevista Eclosão</th>
                                                <th>Qtd. Ovos</th>
                                                <th>Qtd. Eclodidos</th>
                                                <th>Chocadeira</th>
                                                <th>Status</th>
                                                <th style="width: 150px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $incubacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incubacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($incubacao->id); ?></td>
                                                    <td><?php echo e($incubacao->tipoAve->nome ?? 'N/A'); ?></td>
                                                    <td><?php echo e($incubacao->lote->identificacao_lote ?? 'N/A'); ?></td>
                                                    
                                                    <td><?php echo e(optional($incubacao->posturaOvo)->data_inicio_postura?->format('d/m/Y') ?? 'N/A'); ?></td>
                                                    <td><?php echo e($incubacao->data_entrada_incubadora->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($incubacao->data_prevista_eclosao->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($incubacao->quantidade_ovos); ?></td>
                                                    <td><?php echo e($incubacao->quantidade_eclodidos ?? '0'); ?></td>
                                                    <td><?php echo e($incubacao->chocadeira ?? 'N/A'); ?></td>
                                                    <td>
                                                        <?php if($incubacao->ativo): ?>
                                                            <span class="badge badge-success">Ativo</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Inativo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e(route('incubacoes.show', $incubacao->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('incubacoes.edit', $incubacao->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('incubacoes.ficha', $incubacao->id)); ?>" class="btn btn-secondary btn-sm" title="Ficha de Incubação" target="_blank">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('incubacoes.destroy', $incubacao->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja inativar esta incubação?');">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Inativar">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="11" class="text-center">Nenhuma incubação encontrada.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                <?php echo e($incubacoes->links('pagination::bootstrap-4')); ?>

                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/incubacoes/index.blade.php ENDPATH**/ ?>