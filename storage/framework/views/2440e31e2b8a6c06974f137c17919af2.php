<?php
    $pageTitle = 'Detalhes da Venda';
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
                        <h1 class="m-0">Detalhes da Venda</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.vendas.index')); ?>">Vendas</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-10">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Venda #<?php echo e($venda->id); ?></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>ID da Venda:</strong> <?php echo e($venda->id); ?></p> 
                                        <p><strong>Data da Venda:</strong> <?php echo e($venda->data_venda->format('d/m/Y H:i')); ?></p>
                                        <p><strong>Método de Pagamento:</strong> <?php echo e($venda->metodo_pagamento ?? 'Não Informado'); ?></p>
                                        <p><strong>Status:</strong>
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
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Valor Total (Bruto):</strong> R$ <?php echo e(number_format($venda->valor_total, 2, ',', '.')); ?></p>
                                        <p><strong>Desconto:</strong> R$ <?php echo e(number_format($venda->desconto, 2, ',', '.')); ?></p>
                                        <p><strong>Valor Final:</strong> R$ <?php echo e(number_format($venda->valor_final, 2, ',', '.')); ?></p>
                                    </div>
                                </div>
                                <p><strong>Observações:</strong> <?php echo e($venda->observacoes ?? 'N/A'); ?></p>

                                <hr>
                                <h4>Informações de Comissão</h4>
                                <?php if($venda->user): ?>
                                    <p><strong>Vendedor:</strong> <?php echo e($venda->user->name); ?></p>
                                    <p><strong>Percentual de Comissão:</strong> <?php echo e(number_format($venda->comissao_percentual, 2, ',', '.')); ?>%</p>
                                    <?php if($venda->despesaComissao): ?>
                                        <p><strong>Despesa de Comissão Gerada:</strong>
                                            <a href="<?php echo e(route('financeiro.despesas.show', $venda->despesaComissao->id)); ?>">Ver Despesa #<?php echo e($venda->despesaComissao->id); ?></a> (R$ <?php echo e(number_format($venda->despesaComissao->valor, 2, ',', '.')); ?>)
                                        </p>
                                    <?php else: ?>
                                        <p><strong>Despesa de Comissão:</strong> Não gerada ou removida.</p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p>Nenhuma comissão associada a esta venda.</p>
                                <?php endif; ?>

                                <hr>
                                <h4>Itens da Venda</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Ave (Matrícula)</th>
                                                <th>Qtd</th>
                                                <th>Preço Unit.</th>
                                                <th>Total Item</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $venda->vendaItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                                    <td colspan="5" class="text-center">Nenhum item nesta venda.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="<?php echo e(route('financeiro.vendas.edit', $venda->id)); ?>" class="btn btn-warning">Editar Venda</a>
                                <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="btn btn-secondary">Voltar para a Lista</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/vendas/show.blade.php ENDPATH**/ ?>