<?php
    $pageTitle = 'Detalhes da Reserva';
?>



<?php $__env->startSection('content'); ?>
<section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Detalhes da Reserva #<?php echo e($reserva->numero_reserva); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.reservas.index')); ?>">Reservas</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informações da Reserva</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Número da Reserva:</strong> <?php echo e($reserva->numero_reserva); ?></p>
                                <p><strong>Data da Reserva:</strong> <?php echo e($reserva->data_reserva->format('d/m/Y')); ?></p>
                                <p><strong>Data Prevista de Entrega:</strong> <?php echo e($reserva->data_prevista_entrega ? $reserva->data_prevista_entrega->format('d/m/Y') : 'N/A'); ?></p>
                                <p><strong>Data Vencimento Proposta:</strong> <?php echo e($reserva->data_vencimento_proposta ? $reserva->data_vencimento_proposta->format('d/m/Y') : 'N/A'); ?></p>
                                <p><strong>Valor Total:</strong> R$ <?php echo e(number_format($reserva->valor_total, 2, ',', '.')); ?></p>
                                <p><strong>Pagamento Parcial:</strong> R$ <?php echo e(number_format($reserva->pagamento_parcial, 2, ',', '.')); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Nome do Cliente:</strong> <?php echo e($reserva->nome_cliente ?? 'N/A'); ?></p>
                                <p><strong>Contato do Cliente:</strong> <?php echo e($reserva->contato_cliente ?? 'N/A'); ?></p>
                                <p><strong>Status:</strong>
                                    <span class="badge badge-<?php echo e($reserva->status == 'pendente' ? 'warning' :
                                        ($reserva->status == 'confirmada' ? 'info' :
                                        ($reserva->status == 'cancelada' ? 'danger' :
                                        ($reserva->status == 'convertida_venda' ? 'success' : 'secondary')))); ?>">
                                        <?php echo e(['pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'cancelada' => 'Cancelada', 'convertida_venda' => 'Convertida em Venda'][$reserva->status] ?? $reserva->status); ?>

                                    </span>
                                </p>
                                <p><strong>Observações:</strong> <?php echo e($reserva->observacoes ?? 'N/A'); ?></p>
                                <p><strong>Criado em:</strong> <?php echo e($reserva->created_at->format('d/m/Y H:i')); ?></p>
                                <p><strong>Última Atualização:</strong> <?php echo e($reserva->updated_at->format('d/m/Y H:i')); ?></p>
                            </div>
                        </div>

                        <hr>
                        <h4>Itens da Reserva</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Descrição do Item</th>
                                        <th>Ave (Matrícula)</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unitário</th>
                                        <th>Valor Total Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $reserva->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($item->descricao_item); ?></td>
                                            <td>
                                                <?php if($item->ave): ?>
                                                    <a href="<?php echo e(route('aves.show', $item->ave->id)); ?>"><?php echo e($item->ave->matricula); ?> (<?php echo e($item->ave->tipoAve->nome ?? 'N/A'); ?>)</a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($item->quantidade); ?></td>
                                            <td>R$ <?php echo e(number_format($item->preco_unitario, 2, ',', '.')); ?></td>
                                            <td>R$ <?php echo e(number_format($item->valor_total_item, 2, ',', '.')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Nenhum item nesta reserva.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($reserva->vendas->count() > 0): ?>
                            <hr>
                            <h4>Vendas Associadas</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID da Venda</th>
                                            <th>Data da Venda</th>
                                            <th>Valor Final</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $reserva->vendas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($venda->id); ?></td>
                                                <td><?php echo e($venda->data_venda->format('d/m/Y')); ?></td>
                                                <td>R$ <?php echo e(number_format($venda->valor_final, 2, ',', '.')); ?></td>
                                                <td><?php echo e($venda->status); ?></td>
                                                <td>
                                                    <a href="<?php echo e(route('financeiro.vendas.show', $venda->id)); ?>" class="btn btn-info btn-sm" title="Ver Venda">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="card-footer">
                        <a href="<?php echo e(route('financeiro.reservas.index')); ?>" class="btn btn-secondary">Voltar</a>
                        <a href="<?php echo e(route('financeiro.reservas.edit', $reserva->id)); ?>" class="btn btn-primary">Editar</a>
                        <?php if($reserva->status != 'convertida_venda' && $reserva->status != 'cancelada'): ?>
                            <form action="<?php echo e(route('financeiro.reservas.convertToVenda', $reserva->id)); ?>" method="POST" style="display:inline-block;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-success" onclick="return confirm('Tem certeza que deseja converter esta reserva em uma venda? Esta ação é irreversível e irá inativar as aves associadas.')">
                                    <i class="fas fa-cash-register"></i> Converter para Venda
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/financeiro/reservas/show.blade.php ENDPATH**/ ?>