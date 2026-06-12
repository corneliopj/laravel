<?php
    $pageTitle = 'Listagem de Vendas';
?>



<?php $__env->startSection('content'); ?>
<section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Listagem de Vendas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Vendas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo e(route('financeiro.vendas.index')); ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Data Início</label>
                                        <input type="date" name="data_inicio" class="form-control" value="<?php echo e(request('data_inicio')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Data Fim</label>
                                        <input type="date" name="data_fim" class="form-control" value="<?php echo e(request('data_fim')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Comprador</label>
                                        <input type="text" name="comprador" class="form-control" value="<?php echo e(request('comprador')); ?>" list="compradores">
                                        <datalist id="compradores">
                                            <?php $__currentLoopData = $compradores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comprador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($comprador); ?>">
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ordenar por</label>
                                        <select name="ordenar" class="form-control">
                                            <option value="recentes" <?php echo e(request('ordenar') == 'recentes' ? 'selected' : ''); ?>>Mais Recentes</option>
                                            <option value="antigas" <?php echo e(request('ordenar') == 'antigas' ? 'selected' : ''); ?>>Mais Antigas</option>
                                            <option value="valor_maior" <?php echo e(request('ordenar') == 'valor_maior' ? 'selected' : ''); ?>>Maior Valor</option>
                                            <option value="valor_menor" <?php echo e(request('ordenar') == 'valor_menor' ? 'selected' : ''); ?>>Menor Valor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="btn btn-secondary">Limpar</a>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Comprador</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Observação</th>
                                        <th style="width: 150px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $vendas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($venda->id); ?></td>
                                            <td><?php echo e($venda->data_venda->format('d/m/Y H:i')); ?></td>
                                            <td><?php echo e($venda->comprador ?? 'N/A'); ?></td>
                                            <td>R$ <?php echo e(number_format($venda->valor_final, 2, ',', '.')); ?></td>
                                            <td>
                                                <?php if($venda->status == 'concluida'): ?>
                                                    <span class="badge badge-success">Concluída</span>
                                                <?php elseif($venda->status == 'pendente'): ?>
                                                    <span class="badge badge-warning">Pendente</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Cancelada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e(Str::limit($venda->observacoes, 30)); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('financeiro.vendas.show', $venda->id)); ?>" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('financeiro.vendas.edit', $venda->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('financeiro.vendas.destroy', $venda->id)); ?>" method="POST" style="display:inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma venda encontrada</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <?php echo e($vendas->appends(request()->query())->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
            </div>
        </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/financeiro/vendas/index.blade.php ENDPATH**/ ?>