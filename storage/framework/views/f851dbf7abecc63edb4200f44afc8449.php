<?php
    $pageTitle = 'Vendas';
?>


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="wrapper">
    
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Vendas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.dashboard')); ?>">Financeiro</a></li>
                            <li class="breadcrumb-item active">Vendas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
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
                                <h3 class="card-title">Lista de Vendas</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('financeiro.vendas.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Venda (PDV)
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo e(route('financeiro.vendas.index')); ?>" method="GET" class="form-inline mb-3">
                                    <div class="form-group mr-3">
                                        <label for="status" class="mr-2">Status:</label>
                                        <select name="status" id="status" class="form-control form-control-sm">
                                            <option value="">Todos</option>
                                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e($request->status == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="data_inicio" class="mr-2">Data Início:</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control form-control-sm" value="<?php echo e($request->data_inicio); ?>">
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="data_fim" class="mr-2">Data Fim:</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control form-control-sm" value="<?php echo e($request->data_fim); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-info btn-sm mr-2">Filtrar</button>
                                    <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="btn btn-secondary btn-sm">Limpar Filtros</a>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Data Venda</th>
                                                <th>Valor Final</th>
                                                <th>Vendedor</th>
                                                <th>Comissão</th> 
                                                <th>Status</th>
                                                <th style="width: 150px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $vendas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($venda->id); ?></td>
                                                    <td><?php echo e($venda->data_venda->format('d/m/Y H:i')); ?></td>
                                                    <td>R$ <?php echo e(number_format($venda->valor_final, 2, ',', '.')); ?></td>
                                                    <td><?php echo e($venda->user->name ?? 'N/A'); ?></td>
                                                    <td>
                                                        <?php if($venda->comissao_paga && $venda->comissao_percentual > 0): ?>
                                                            R$ <?php echo e(number_format($venda->valor_final * ($venda->comissao_percentual / 100), 2, ',', '.')); ?>

                                                        <?php else: ?>
                                                            R$ 0,00
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $badgeClass = '';
                                                            switch ($venda->status) {
                                                                case 'concluida': $badgeClass = 'badge-success'; break;
                                                                case 'pendente': $badgeClass = 'badge-warning'; break;
                                                                case 'cancelada': $badgeClass = 'badge-danger'; break;
                                                                default: $badgeClass = 'badge-secondary'; break;
                                                            }
                                                        ?>
                                                        <span class="badge <?php echo e($badgeClass); ?>"><?php echo e(ucfirst($venda->status)); ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e(route('financeiro.vendas.show', $venda->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('financeiro.vendas.edit', $venda->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('financeiro.vendas.destroy', $venda->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta venda? Esta ação reativará as aves vendidas.');">
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
                                                    <td colspan="7" class="text-center">Nenhuma venda encontrada.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <?php echo e($vendas->links('pagination::bootstrap-4')); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/vendas/index.blade.php ENDPATH**/ ?>