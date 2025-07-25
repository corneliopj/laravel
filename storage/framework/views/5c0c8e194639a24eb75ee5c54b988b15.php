<?php
    $pageTitle = 'Listagem de Incubações';
?>


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="wrapper">
    
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height: 797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Incubações</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
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

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Lista de Incubações</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('incubacoes.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Incubação
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                
                                <form action="<?php echo e(route('incubacoes.index')); ?>" method="GET" class="form-inline mb-3">
                                    <div class="form-group mr-3">
                                        <label for="status" class="mr-2">Status:</label>
                                        <select name="status" id="status" class="form-control form-control-sm">
                                            <option value="">Todos</option>
                                            <option value="ativo" <?php echo e($request->status == 'ativo' ? 'selected' : ''); ?>>Ativa</option>
                                            <option value="inativo" <?php echo e($request->status == 'inativo' ? 'selected' : ''); ?>>Inativa</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="data_entrada_inicio" class="mr-2">Data Entrada (Início):</label>
                                        <input type="date" name="data_entrada_inicio" id="data_entrada_inicio" class="form-control form-control-sm" value="<?php echo e($request->data_entrada_inicio); ?>">
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="data_entrada_fim" class="mr-2">Data Entrada (Fim):</label>
                                        <input type="date" name="data_entrada_fim" id="data_entrada_fim" class="form-control form-control-sm" value="<?php echo e($request->data_entrada_fim); ?>">
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="tipo_ave_id" class="mr-2">Tipo de Ave:</label>
                                        <select name="tipo_ave_id" id="tipo_ave_id" class="form-control form-control-sm">
                                            <option value="">Todos</option>
                                            <?php $__currentLoopData = $tiposAve; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($tipo->id); ?>" <?php echo e($request->tipo_ave_id == $tipo->id ? 'selected' : ''); ?>><?php echo e($tipo->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-sm mr-2">Filtrar</button>
                                    <a href="<?php echo e(route('incubacoes.index')); ?>" class="btn btn-secondary btn-sm">Limpar Filtros</a>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>Lote de Ovos</th>
                                                <th>Tipo de Ave</th>
                                                <th>Data Entrada</th>
                                                <th>Data Prev. Eclosão</th>
                                                <th>Chocadeira</th>
                                                <th>Ovos Totais</th>
                                                <th>Observações</th>
                                                <th>Status</th>
                                                <th style="width: 120px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $incubações; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incubacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?> 
                                                <tr>
                                                    <td><?php echo e($incubacao->id); ?></td>
                                                    <td><?php echo e($incubacao->lote->identificacao_lote ?? 'N/A'); ?></td>
                                                    <td><?php echo e($incubacao->tipoAve->nome ?? 'N/A'); ?></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($incubacao->data_entrada_incubadora)->format('d/m/Y')); ?></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($incubacao->data_prevista_eclosao)->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($incubacao->chocadeira ?? 'N/A'); ?></td>
                                                    <td><?php echo e($incubacao->quantidade_ovos); ?></td>
                                                    <td><?php echo e(Str::limit($incubacao->observacoes, 30, '...')); ?></td>
                                                    <td>
                                                        <?php if($incubacao->ativo): ?>
                                                            <span class="badge badge-success">Ativa</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Inativa</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e(route('incubacoes.show', $incubacao->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('incubacoes.edit', $incubacao->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('incubacoes.destroy', $incubacao->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja inativar esta incubação?');">
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
                                                    <td colspan="10" class="text-center">Nenhuma incubação encontrada.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <?php echo e($incubações->links('pagination::bootstrap-4')); ?> 
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
    </div>
    <!-- /.content-wrapper -->
<?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<!-- ./wrapper -->
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/incubacoes/listar.blade.php ENDPATH**/ ?>