<?php
    $pageTitle = 'Lista de Aves';
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
                        <h1 class="m-0">Lista de Aves</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Aves</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Gerenciamento de Aves</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('aves.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Ave
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                
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

                                
                                <div class="mb-3">
                                    <strong class="mr-2">Filtrar por Status:</strong>
                                    <a href="<?php echo e(route('aves.index', ['status' => 'ativas'])); ?>"
                                       class="btn btn-outline-primary btn-sm <?php echo e(request('status') == 'ativas' || (!request('status') && !request()->has('status')) ? 'active' : ''); ?>">
                                        Ativas
                                    </a>
                                    <a href="<?php echo e(route('aves.index', ['status' => 'excluidas'])); ?>"
                                       class="btn btn-outline-warning btn-sm <?php echo e(request('status') == 'excluidas' ? 'active' : ''); ?>">
                                        Excluídas
                                    </a>
                                    <a href="<?php echo e(route('aves.index', ['status' => 'mortas'])); ?>"
                                       class="btn btn-outline-danger btn-sm <?php echo e(request('status') == 'mortas' ? 'active' : ''); ?>">
                                        Mortas
                                    </a>
                                    <a href="<?php echo e(route('aves.index', ['status' => 'inativas'])); ?>"
                                       class="btn btn-outline-secondary btn-sm <?php echo e(request('status') == 'inativas' ? 'active' : ''); ?>">
                                        Todas Inativas
                                    </a>
                                    <a href="<?php echo e(route('aves.index')); ?>"
                                       class="btn btn-outline-info btn-sm <?php echo e(!request('status') && request()->has('status') ? 'active' : ''); ?>">
                                        Todas as Aves
                                    </a>
                                </div>

                                
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Imagem</th> 
                                            <th>Matrícula</th>
                                            <th>Tipo de Ave</th>
                                            <th>Variação</th>
                                            <th>Lote</th>
                                            <th>Data Eclosão</th>
                                            <th>Sexo</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $aves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($ave->id); ?></td>
                                                <td>
                                                    
                                                    <?php if($ave->foto_path): ?>
                                                        <img src="<?php echo e(asset($ave->foto_path)); ?>" alt="Foto da Ave" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
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
                                                                'gansos'     => 'ganso.png', // Certifique-se de ter 'ganso.png' em public/img/
                                                            ];
                                                            $silhouetteFileName = $silhouetteMap[$birdTypeName] ?? null;
                                                            $silhouetteSrc = $silhouetteFileName ? asset('img/' . $silhouetteFileName) : null;
                                                            $placeholderText = ucfirst($birdTypeName) ?: 'Ave';
                                                            // O placeholder genérico será exibido apenas se a silhueta específica não for encontrada.
                                                            // Usa a primeira letra do tipo de ave para o texto do placeholder.
                                                            $genericPlaceholderSrc = 'https://placehold.co/50x50/e0e0e0/000000?text=' . urlencode(substr($placeholderText, 0, 1));
                                                        ?>
                                                        <img src="<?php echo e($silhouetteSrc); ?>"
                                                             alt="Silhueta da Ave"
                                                             class="img-thumbnail"
                                                             style="width: 50px; height: 50px; object-fit: cover;"
                                                             onerror="this.onerror=null; this.src='<?php echo e($genericPlaceholderSrc); ?>';" 
                                                        >
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($ave->matricula); ?></td>
                                                <td><?php echo e($ave->tipoAve->nome ?? 'N/A'); ?></td>
                                                <td><?php echo e($ave->variacao->nome ?? 'N/A'); ?></td>
                                                <td><?php echo e($ave->lote->identificacao_lote ?? 'N/A'); ?></td>
                                                <td><?php echo e(\Carbon\Carbon::parse($ave->data_eclosao)->format('d/m/Y')); ?></td>
                                                <td><?php echo e($ave->sexo); ?></td>
                                                <td>
                                                    <?php if($ave->ativo): ?>
                                                        <span class="badge badge-success">Ativa</span>
                                                    <?php elseif($ave->mortes()->exists()): ?>
                                                        <span class="badge badge-danger">Morta</span>
                                                    <?php elseif($ave->trashed()): ?>
                                                        <span class="badge badge-warning">Inativa (Excluída)</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inativa</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo e(route('aves.show', $ave->id)); ?>" class="btn btn-info btn-sm mb-1" title="Ver Detalhes">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                    
                                                    <form action="<?php echo e(route('aves.expedirCertidao', $ave->id)); ?>" method="POST" style="display:inline-block;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-primary btn-sm mb-1" title="Expedir Certidão">
                                                            <i class="fas fa-file-alt"></i> Certidão
                                                        </button>
                                                    </form>
                                                    <?php if($ave->ativo): ?>
                                                        <a href="<?php echo e(route('aves.edit', $ave->id)); ?>" class="btn btn-warning btn-sm mb-1" title="Editar">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <a href="<?php echo e(route('aves.registerDeath', $ave->id)); ?>" class="btn btn-danger btn-sm mb-1" title="Registrar Morte">
                                                            <i class="fas fa-skull"></i> Morte
                                                        </a>
                                                        <form action="<?php echo e(route('aves.destroy', $ave->id)); ?>" method="POST" style="display:inline-block;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-secondary btn-sm mb-1" onclick="return confirm('Tem certeza que deseja INATIVAR (EXCLUIR) esta ave? Ela será marcada como inativa e removida das listagens padrão, mas poderá ser restaurada.');" title="Inativar/Excluir">
                                                                <i class="fas fa-trash"></i> Inativar
                                                            </button>
                                                        </form>
                                                    <?php elseif($ave->trashed() && !$ave->mortes()->exists()): ?> 
                                                        <form action="<?php echo e(route('aves.restore', $ave->id)); ?>" method="POST" style="display:inline-block;">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-success btn-sm mb-1" onclick="return confirm('Tem certeza que deseja RESTAURAR esta ave?');" title="Restaurar">
                                                                <i class="fas fa-undo"></i> Restaurar
                                                            </button>
                                                        </form>
                                                        <form action="<?php echo e(route('aves.forceDelete', $ave->id)); ?>" method="POST" style="display:inline-block;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Tem certeza que deseja EXCLUIR PERMANENTEMENTE esta ave? Esta ação é irreversível.');" title="Excluir Permanentemente">
                                                                <i class="fas fa-bomb"></i> Excluir Def.
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="10" class="text-center">Nenhuma ave encontrada.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                
                                <div class="d-flex justify-content-center">
                                    <?php echo e($aves->links('pagination::bootstrap-4')); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/aves/listar.blade.php ENDPATH**/ ?>