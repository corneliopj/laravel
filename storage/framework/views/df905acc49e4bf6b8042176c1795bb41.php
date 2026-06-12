<?php
    $pageTitle = 'Detalhes da Venda #' . $venda->id;
?>



<?php $__env->startSection('content'); ?>
<section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><?php echo e($pageTitle); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.vendas.index')); ?>">Vendas</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="nota-fiscal">
                    <div class="cabecalho">
                        <img src="<?php echo e(asset('img/logo.png')); ?>" alt="Logo" class="logo" style="width: 30%; height: auto;">
                        <h2>Criatório Coroné & Agente Resolve - MEI</h2>
                        <p>CNPJ 19.173.619/0001-26</p>
                        <p>Rua Belo Horizonte, 2634 - Centro - Santa Luzia d' Oeste - RO, CEP 76.950-000</p>
                        <h3>NOTA DE VENDA - MEI</h3>
                    </div>

                    <div class="detalhes-venda">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Número:</strong> <?php echo e($venda->id); ?></p>
                                <p><strong>Data:</strong> <?php echo e($venda->data_venda->format('d/m/Y H:i')); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Comprador:</strong> <?php echo e($venda->comprador); ?></p>
                                <p><strong>Status:</strong> 
                                    <?php if($venda->status == 'concluida'): ?>
                                        <span class="badge badge-success">Concluída</span>
                                    <?php elseif($venda->status == 'pendente'): ?>
                                        <span class="badge badge-warning">Pendente</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Cancelada</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <table class="table-itens">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Descrição</th>
                                <th>Qtd</th>
                                <th>Valor Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $venda->vendaItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($item->descricao_item); ?></td>
                                    <td><?php echo e($item->quantidade); ?></td>
                                    <td>R$ <?php echo e(number_format($item->preco_unitario, 2, ',', '.')); ?></td>
                                    <td>R$ <?php echo e(number_format($item->valor_total_item, 2, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <div class="totais">
                        <p><strong>Subtotal:</strong> R$ <?php echo e(number_format($venda->valor_total, 2, ',', '.')); ?></p>
                        <p><strong>Desconto:</strong> R$ <?php echo e(number_format($venda->desconto, 2, ',', '.')); ?></p>
                        <h4><strong>Total:</strong> R$ <?php echo e(number_format($venda->valor_final, 2, ',', '.')); ?></h4>
                    </div>

                    <div class="observacoes mt-4">
                        <p><strong>Observações:</strong></p>
                        <p><?php echo e($venda->observacoes ?? 'Nenhuma observação registrada.'); ?></p>
                    </div>

                    <div class="mt-4">
                        <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="btn btn-secondary">
                            Voltar
                        </a>
                        <a href="<?php echo e(route('financeiro.vendas.edit', $venda->id)); ?>" class="btn btn-primary">
                            Editar
                        </a>
                        <button onclick="window.print()" class="btn btn-success">
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .nota-fiscal {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .cabecalho {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }
    .logo {
        max-width: 150px;
        margin-bottom: 10px;
    }
    .detalhes-venda {
        margin-bottom: 30px;
    }
    .table-itens {
        width: 100%;
        margin-bottom: 20px;
    }
    .table-itens th {
        background: #f8f9fa;
        text-align: left;
        padding: 8px;
    }
    .table-itens td {
        padding: 8px;
        border-bottom: 1px solid #eee;
    }
    .totais {
        text-align: right;
        margin-top: 20px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/financeiro/vendas/show.blade.php ENDPATH**/ ?>