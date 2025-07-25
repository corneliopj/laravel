<?php
    $pageTitle = 'Ficha da Ave';
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
                        <h1 class="m-0">Ficha da Ave</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('aves.index')); ?>">Aves</a></li>
                            <li class="breadcrumb-item active">Ficha</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8"> 
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Informações da Ave: <?php echo e($ave->matricula); ?></h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group text-center">
                                    
                                    <?php if($ave->foto_path): ?>
                                        <img src="<?php echo e(asset($ave->foto_path)); ?>"
                                             alt="Foto da Ave"
                                             class="img-fluid rounded"
                                             style="max-width: 200px; height: auto; border: 2px solid #ddd;">
                                    <?php else: ?>
                                        <?php
                                            $birdTypeName = strtolower($ave->tipoAve->nome ?? '');
                                            $silhouetteFileName = '';
                                            $silhouetteMap = [
                                                'galinaceos' => 'galinha.png',
                                                'perus'      => 'peru.png',
                                                'marrecos'   => 'mareco.png',
                                                'angolas'    => 'angola.png',
                                                'codornas'   => 'codorna.png',
                                                'gansos'     => 'ganso.png',
                                            ];
                                            $silhouetteFileName = $silhouetteMap[$birdTypeName] ?? null;
                                            $silhouetteSrc = $silhouetteFileName ? asset('img/' . $silhouetteFileName) : null;
                                            $placeholderText = ucfirst($birdTypeName) ?: 'Ave';
                                            $genericPlaceholderSrc = 'https://placehold.co/200x200/e0e0e0/000000?text=' . urlencode($placeholderText);
                                        ?>
                                        <img src="<?php echo e($silhouetteSrc); ?>"
                                             alt="Silhueta da Ave"
                                             class="img-fluid rounded"
                                             style="max-width: 200px; height: auto; border: 2px solid #ddd;"
                                             onerror="this.onerror=null; this.src='<?php echo e($genericPlaceholderSrc); ?>';">
                                    <?php endif; ?>
                                    <hr>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>ID:</strong> <?php echo e($ave->id); ?></p>
                                        <p><strong>Matrícula:</strong> <?php echo e($ave->matricula); ?></p>
                                        <p><strong>Tipo de Ave:</strong> <?php echo e($ave->tipoAve->nome ?? 'N/A'); ?></p>
                                        <p><strong>Variação:</strong> <?php echo e($ave->variacao->nome ?? 'N/A'); ?></p>
                                        <p><strong>Sexo:</strong> <?php echo e($ave->sexo); ?></p>
                                        <p><strong>Data de Eclosão:</strong> <?php echo e($ave->data_eclosao ? $ave->data_eclosao->format('d/m/Y') : 'N/A'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Vendável:</strong> <?php echo e($ave->vendavel ? 'Sim' : 'Não'); ?></p>
                                        <p><strong>Lote:</strong> <?php echo e($ave->lote->identificacao_lote ?? 'N/A'); ?></p>
                                        <p><strong>Incubação ID:</strong> <?php echo e($ave->incubacao->id ?? 'N/A'); ?></p>
                                        <p><strong>Status:</strong>
                                            <?php if($ave->morte): ?>
                                                <span class="badge badge-danger">Morto</span>
                                            <?php elseif(!$ave->ativo): ?>
                                                <span class="badge badge-warning">Inativo</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Ativo</span>
                                            <?php endif; ?>
                                        </p>
                                        <?php if($ave->data_inativado): ?>
                                            <p><strong>Data Inativação:</strong> <?php echo e($ave->data_inativado->format('d/m/Y H:i:s')); ?></p>
                                        <?php endif; ?>
                                        <p><strong>Código de Validação:</strong> <?php echo e($ave->codigo_validacao_certidao ?? 'Não gerado'); ?></p>
                                    </div>
                                </div>
                                <hr>

                                
                                <?php if($ave->morte): ?>
                                    <h4>Detalhes da Morte</h4>
                                    <p><strong>Data da Morte:</strong> <?php echo e($ave->morte->data_morte->format('d/m/Y')); ?></p>
                                    <p><strong>Causa:</strong> <?php echo e($ave->morte->causa ?? 'Não informada'); ?></p>
                                    <p><strong>Observações:</strong> <?php echo e($ave->morte->observacoes ?? 'N/A'); ?></p>
                                    <hr>
                                <?php endif; ?>

                                
                                <?php if($ave->incubacao): ?>
                                    <h4>Detalhes da Incubação</h4>
                                    <p><strong>Data Entrada Incubadora:</strong> <?php echo e($ave->incubacao->data_entrada_incubadora->format('d/m/Y') ?? 'N/A'); ?></p>
                                    <p><strong>Data Prevista Eclosão:</strong> <?php echo e($ave->incubacao->data_prevista_eclosao->format('d/m/Y') ?? 'N/A'); ?></p>
                                    <p><strong>Quantidade Ovos:</strong> <?php echo e($ave->incubacao->quantidade_ovos ?? 'N/A'); ?></p>
                                    <p><strong>Quantidade Eclodidos:</strong> <?php echo e($ave->incubacao->quantidade_eclodidos ?? 'N/A'); ?></p>
                                    <p><strong>Incubação Ativa:</strong> <?php echo e($ave->incubacao->ativo ? 'Sim' : 'Não'); ?></p>
                                    <hr>
                                <?php endif; ?>

                                
                                <?php if($ave->incubacao && $ave->incubacao->posturaOvo && $ave->incubacao->posturaOvo->acasalamento): ?>
                                    <?php
                                        $acasalamento = $ave->incubacao->posturaOvo->acasalamento;
                                        $macho = $acasalamento->macho;
                                        $femea = $acasalamento->femea;
                                    ?>
                                    <h4>Filiação (Pais)</h4>
                                    <p>
                                        <strong>Pai:</strong> <?php echo e($macho->matricula ?? 'N/A'); ?>

                                        <?php if($macho): ?> (<a href="<?php echo e(route('aves.show', $macho->id)); ?>">Ver Pai</a>) <?php endif; ?>
                                    </p>
                                    <p>
                                        <strong>Mãe:</strong> <?php echo e($femea->matricula ?? 'N/A'); ?>

                                        <?php if($femea): ?> (<a href="<?php echo e(route('aves.show', $femea->id)); ?>">Ver Mãe</a>) <?php endif; ?>
                                    </p>
                                    <p><strong>Acasalamento ID:</strong> <?php echo e($acasalamento->id); ?></p>
                                    <hr>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="<?php echo e(route('aves.index')); ?>" class="btn btn-secondary">Voltar para a Lista</a>
                                <a href="<?php echo e(route('aves.edit', $ave->id)); ?>" class="btn btn-warning">Editar Ave</a>
                                
                                <?php if(!$ave->morte && $ave->ativo): ?>
                                    <a href="<?php echo e(route('aves.registerDeath', $ave->id)); ?>" class="btn btn-dark">Registrar Morte</a>
                                <?php endif; ?>

                                
                                <form action="<?php echo e(route('aves.expedirCertidao', $ave->id)); ?>" method="POST" style="display:inline-block;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-primary">Emitir Certidão</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/aves/ficha.blade.php ENDPATH**/ ?>