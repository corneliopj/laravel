<?php
    $pageTitle = 'Listar Reservas';
?>

<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="wrapper">
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listar Reservas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reservas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
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
                        <h3 class="card-title">Filtros de Reserva</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('financeiro.reservas.index')); ?>" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Todos</option>
                                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e($request->status == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="data_inicio">Data Início</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo e($request->data_inicio); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="data_fim">Data Fim</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?php echo e($request->data_fim); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                                    <a href="<?php echo e(route('financeiro.reservas.index')); ?>" class="btn btn-secondary">Limpar Filtros</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Reservas Registradas</h3>
                        <div class="card-tools">
                            <a href="<?php echo e(route('financeiro.reservas.create')); ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Nova Reserva
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Cliente</th>
                                        <th>Data Reserva</th>
                                        <th>Valor Total</th>
                                        <th>Pagamento Parcial</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $reservas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reserva): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($reserva->numero_reserva); ?></td>
                                            <td><?php echo e($reserva->nome_cliente ?? 'N/A'); ?></td>
                                            <td><?php echo e($reserva->data_reserva->format('d/m/Y')); ?></td>
                                            <td>R$ <?php echo e(number_format($reserva->valor_total, 2, ',', '.')); ?></td>
                                            <td>R$ <?php echo e(number_format($reserva->pagamento_parcial, 2, ',', '.')); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo e($reserva->status == 'pendente' ? 'warning' :
                                                    ($reserva->status == 'confirmada' ? 'info' :
                                                    ($reserva->status == 'cancelada' ? 'danger' :
                                                    ($reserva->status == 'convertida_venda' ? 'success' : 'secondary')))); ?>">
                                                    <?php echo e($statusOptions[$reserva->status] ?? $reserva->status); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('financeiro.reservas.show', $reserva->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('financeiro.reservas.edit', $reserva->id)); ?>" class="btn btn-primary btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if($reserva->status != 'convertida_venda' && $reserva->status != 'cancelada'): ?>
                                                    <form action="<?php echo e(route('financeiro.reservas.convertToVenda', $reserva->id)); ?>" method="POST" style="display:inline-block;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-success btn-sm" title="Converter para Venda" onclick="return confirm('Tem certeza que deseja converter esta reserva em uma venda? Esta ação é irreversível e irá inativar as aves associadas.')">
                                                            <i class="fas fa-cash-register"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form action="<?php echo e(route('financeiro.reservas.destroy', $reserva->id)); ?>" method="POST" style="display:inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta reserva? As aves associadas serão liberadas.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma reserva encontrada.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <?php echo e($reservas->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/reservas/index.blade.php ENDPATH**/ ?>