<?php
    $pageTitle = 'Detalhes da Transação Recorrente';
?>



<?php $__env->startSection('content'); ?>
<div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detalhes da Transação Recorrente</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.transacoes_recorrentes.index')); ?>">Transações Recorrentes</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title"><?php echo e($transacaoRecorrente->description); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>ID:</strong> <?php echo e($transacaoRecorrente->id); ?></p>
                                <p><strong>Descrição:</strong> <?php echo e($transacaoRecorrente->description); ?></p>
                                <p><strong>Valor:</strong> R$ <?php echo e(number_format($transacaoRecorrente->value, 2, ',', '.')); ?></p>
                                <p><strong>Categoria:</strong> <?php echo e($transacaoRecorrente->categoria->nome ?? 'N/A'); ?> (<?php echo e(ucfirst($transacaoRecorrente->categoria->tipo ?? '')); ?>)</p>
                                <p><strong>Tipo:</strong>
                                    <span class="badge badge-<?php echo e($transacaoRecorrente->type == 'receita' ? 'success' : 'danger'); ?>">
                                        <?php echo e(ucfirst($transacaoRecorrente->type)); ?>

                                    </span>
                                </p>
                                <p><strong>Frequência:</strong>
                                    <?php
                                        $frequencias = [
                                            'daily' => 'Diária',
                                            'weekly' => 'Semanal',
                                            'monthly' => 'Mensal',
                                            'quarterly' => 'Trimestral',
                                            'yearly' => 'Anual'
                                        ];
                                    ?>
                                    <?php echo e($frequencias[$transacaoRecorrente->frequency] ?? $transacaoRecorrente->frequency); ?>

                                </p>
                                <p><strong>Data de Início:</strong> <?php echo e($transacaoRecorrente->start_date->format('d/m/Y')); ?></p>
                                <p><strong>Data de Fim:</strong> <?php echo e($transacaoRecorrente->end_date ? $transacaoRecorrente->end_date->format('d/m/Y') : 'N/A'); ?></p>
                                <p><strong>Próximo Vencimento:</strong> <?php echo e($transacaoRecorrente->next_due_date ? $transacaoRecorrente->next_due_date->format('d/m/Y') : 'N/A'); ?></p>
                                <p><strong>Última Geração:</strong> <?php echo e($transacaoRecorrente->last_generated_date ? $transacaoRecorrente->last_generated_date->format('d/m/Y') : 'Nunca'); ?></p>
                                <p><strong>Criado em:</strong> <?php echo e($transacaoRecorrente->created_at->format('d/m/Y H:i:s')); ?></p>
                                <p><strong>Última Atualização:</strong> <?php echo e($transacaoRecorrente->updated_at->format('d/m/Y H:i:s')); ?></p>
                            </div>
                            <div class="card-footer">
                                <a href="<?php echo e(route('financeiro.transacoes_recorrentes.edit', $transacaoRecorrente->id)); ?>" class="btn btn-warning">Editar</a>
                                <a href="<?php echo e(route('financeiro.transacoes_recorrentes.index')); ?>" class="btn btn-secondary">Voltar para a Lista</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/financeiro/transacoes_recorrentes/show.blade.php ENDPATH**/ ?>