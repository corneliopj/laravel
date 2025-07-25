<?php
    $pageTitle = 'Detalhes da Incubação';
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
                        <h1 class="m-0">Detalhes da Incubação</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('incubacoes.index')); ?>">Incubações</a></li>
                            <li class="breadcrumb-item active">Detalhes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline rounded">
                    <div class="card-header">
                        <h3 class="card-title">Informações da Incubação #<?php echo e($incubacao->id); ?></h3>
                        <div class="card-tools">
                            <a href="<?php echo e(route('incubacoes.edit', ['incubacao' => $incubacao->id])); ?>" class="btn btn-sm btn-warning rounded" title="Editar Incubação">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Lote de Ovos:</strong> <?php echo e($incubacao->lote->identificacao_lote ?? 'N/A'); ?></p>
                                <p><strong>Tipo de Ave:</strong> <?php echo e($incubacao->tipoAve->nome ?? 'N/A'); ?></p>
                                <p><strong>Postura de Ovo Associada:</strong> <?php echo e($incubacao->posturaOvo->id ?? 'N/A'); ?></p>
                                <p><strong>Data de Entrada na Incubadora:</strong> <?php echo e(\Carbon\Carbon::parse($incubacao->data_entrada_incubadora)->format('d/m/Y')); ?></p>
                                <p><strong>Data Prevista de Eclosão:</strong> <?php echo e(\Carbon\Carbon::parse($incubacao->data_prevista_eclosao)->format('d/m/Y')); ?></p>
                                <p><strong>Chocadeira:</strong> <?php echo e($incubacao->chocadeira ?? 'N/A'); ?></p>
                                <p><strong>Quantidade de Ovos:</strong> <?php echo e($incubacao->quantidade_ovos); ?></p>
                                <p><strong>Quantidade Eclodidos:</strong> <?php echo e($incubacao->quantidade_eclodidos ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ovos Inférteis:</strong> <?php echo e($incubacao->quantidade_inferteis ?? 'N/A'); ?></p>
                                <p><strong>Ovos Infectados:</strong> <?php echo e($incubacao->quantidade_infectados ?? 'N/A'); ?></p>
                                <p><strong>Ovos Mortos:</strong> <?php echo e($incubacao->quantidade_mortos ?? 'N/A'); ?></p>
                                <p><strong>Status:</strong>
                                    <?php
                                        $agora = \Carbon\Carbon::now();
                                        $dataEntrada = \Carbon\Carbon::parse($incubacao->data_entrada_incubadora);
                                        $dataPrevistaEclosao = \Carbon\Carbon::parse($incubacao->data_prevista_eclosao);
                                        $status = 'Em andamento';
                                        $diasParaEclosao = $agora->diffInDays($dataPrevistaEclosao, false);

                                        if ($dataPrevistaEclosao->isPast() && ($incubacao->quantidade_eclodidos !== null && $incubacao->quantidade_eclodidos > 0)) {
                                            $status = 'Concluído';
                                        } elseif ($dataPrevistaEclosao->isPast() && ($incubacao->quantidade_eclodidos === null || $incubacao->quantidade_eclodidos === 0)) {
                                            $status = 'Atrasado';
                                        } elseif ($agora->lt($dataEntrada)) {
                                            $status = 'Prevista';
                                        } elseif ($diasParaEclosao >= 0 && $diasParaEclosao <= 5) {
                                            $status = 'Finalizando';
                                        }

                                        $badgeClass = '';
                                        switch ($status) {
                                            case 'Em andamento': $badgeClass = 'badge-info'; break;
                                            case 'Finalizando': $badgeClass = 'badge-warning'; break;
                                            case 'Concluído': $badgeClass = 'badge-success'; break;
                                            case 'Atrasado': $badgeClass = 'badge-danger'; break;
                                            case 'Prevista': $badgeClass = 'badge-secondary'; break;
                                            default: $badgeClass = 'badge-secondary'; break;
                                        }
                                    ?>
                                    <span class="badge <?php echo e($badgeClass); ?> rounded"><?php echo e($status); ?></span>
                                </p>
                                <p><strong>Observações:</strong> <?php echo e($incubacao->observacoes ?? 'Nenhuma.'); ?></p>
                                <p><strong>Ativo:</strong> <?php echo e($incubacao->ativo ? 'Sim' : 'Não'); ?></p>
                                <p><strong>Data de Criação:</strong> <?php echo e(\Carbon\Carbon::parse($incubacao->created_at)->format('d/m/Y H:i:s')); ?></p>
                                <p><strong>Última Atualização:</strong> <?php echo e(\Carbon\Carbon::parse($incubacao->updated_at)->format('d/m/Y H:i:s')); ?></p>
                            </div>
                        </div>
                        <hr>
                        <h4>Aves Desta Incubação:</h4>
                        <?php if($incubacao->aves->isEmpty()): ?>
                            <p class="text-muted">Nenhuma ave associada a esta incubação ainda.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Matrícula</th>
                                            <th>Tipo Ave</th>
                                            <th>Variação</th>
                                            <th>Sexo</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $incubacao->aves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($ave->matricula); ?></td>
                                                <td><?php echo e($ave->tipoAve->nome ?? 'N/A'); ?></td>
                                                <td><?php echo e($ave->variacao->nome ?? 'N/A'); ?></td>
                                                <td><?php echo e($ave->sexo); ?></td>
                                                <td>
                                                    <a href="<?php echo e(route('aves.show', $ave->id)); ?>" class="btn btn-sm btn-info rounded" title="Ver Ficha da Ave">
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
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/incubacoes/ficha.blade.php ENDPATH**/ ?>