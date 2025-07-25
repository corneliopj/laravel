<?php
    $pageTitle = 'Transações Recorrentes';
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
                        <h1 class="m-0">Transações Recorrentes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Transações Recorrentes</li>
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
                                <h3 class="card-title">Lista de Transações Recorrentes</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('financeiro.transacoes_recorrentes.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Transação Recorrente
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo e(route('financeiro.transacoes_recorrentes.index')); ?>" method="GET" class="form-inline mb-3">
                                    <div class="form-group mr-3">
                                        <label for="tipo" class="mr-2">Tipo:</label>
                                        <select name="tipo" id="tipo" class="form-control form-control-sm">
                                            <option value="">Todos</option>
                                            <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e($request->tipo == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="frequencia" class="mr-2">Frequência:</label>
                                        <select name="frequencia" id="frequencia" class="form-control form-control-sm">
                                            <option value="">Todas</option>
                                            <?php $__currentLoopData = $frequencias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e($request->frequencia == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-sm mr-2">Filtrar</button>
                                    <a href="<?php echo e(route('financeiro.transacoes_recorrentes.index')); ?>" class="btn btn-secondary btn-sm">Limpar Filtros</a>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Descrição</th>
                                                <th>Valor</th>
                                                <th>Categoria</th>
                                                <th>Tipo</th>
                                                <th>Frequência</th>
                                                <th>Início</th>
                                                <th>Fim</th>
                                                <th>Próximo Vencimento</th>
                                                <th>Última Geração</th>
                                                <th style="width: 150px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $transacoesRecorrentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($transacao->id); ?></td>
                                                    <td><?php echo e($transacao->description); ?></td>
                                                    <td>R$ <?php echo e(number_format($transacao->value, 2, ',', '.')); ?></td>
                                                    <td><?php echo e($transacao->categoria->nome ?? 'N/A'); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo e($transacao->type == 'receita' ? 'success' : 'danger'); ?>">
                                                            <?php echo e(ucfirst($transacao->type)); ?>

                                                        </span>
                                                    </td>
                                                    <td><?php echo e($frequencias[$transacao->frequency] ?? $transacao->frequency); ?></td>
                                                    <td><?php echo e($transacao->start_date->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($transacao->end_date ? $transacao->end_date->format('d/m/Y') : 'N/A'); ?></td>
                                                    <td>
                                                        <?php if($transacao->next_due_date): ?>
                                                            <?php echo e($transacao->next_due_date->format('d/m/Y')); ?>

                                                            <?php if($transacao->next_due_date->isPast() && !$transacao->end_date?->isPast()): ?>
                                                                <span class="badge badge-danger">Atrasado</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($transacao->last_generated_date ? $transacao->last_generated_date->format('d/m/Y') : 'Nunca'); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('financeiro.transacoes_recorrentes.show', $transacao->id)); ?>" class="btn btn-info btn-sm" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('financeiro.transacoes_recorrentes.edit', $transacao->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('financeiro.transacoes_recorrentes.destroy', $transacao->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta transação recorrente?');">
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
                                                    <td colspan="11" class="text-center">Nenhuma transação recorrente encontrada.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/transacoes_recorrentes/index.blade.php ENDPATH**/ ?>