<?php
    $pageTitle = 'Listagem de Acasalamentos';
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
                        <h1 class="m-0">Listagem de Acasalamentos</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Acasalamentos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        
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

                        <div class="card-body">
                            
                            <div class="mb-3">
                                <a href="<?php echo e(route('acasalamentos.create')); ?>" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Novo Acasalamento
                                </a>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Macho (Matrícula)</th>
                                        <th>Fêmea (Matrícula)</th>
                                        <th>Data Início</th>
                                        <th>Data Fim</th>
                                        <th>Observações</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $acasalamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acasalamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($acasalamento->id); ?></td>
                                            <td><?php echo e($acasalamento->macho->matricula ?? 'N/A'); ?></td>
                                            <td><?php echo e($acasalamento->femea->matricula ?? 'N/A'); ?></td>
                                            <td><?php echo e($acasalamento->data_inicio->format('d/m/Y')); ?></td>
                                            <td><?php echo e($acasalamento->data_fim ? $acasalamento->data_fim->format('d/m/Y') : 'Em andamento'); ?></td>
                                            <td><?php echo e($acasalamento->observacoes ?? 'N/A'); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('acasalamentos.edit', $acasalamento->id)); ?>" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <?php if(!$acasalamento->data_fim): ?> 
                                                    <a href="<?php echo e(route('acasalamentos.edit', $acasalamento->id)); ?>" class="btn btn-sm btn-warning" title="Encerrar Acasalamento" onclick="return confirm('Tem certeza que deseja encerrar este acasalamento? Isso registrará a data de fim.');">
                                                        <i class="fas fa-times-circle"></i> Encerrar
                                                    </a>
                                                <?php else: ?>
                                                    
                                                    <form action="<?php echo e(route('acasalamentos.destroy', $acasalamento->id)); ?>" method="POST" style="display:inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir Permanentemente" onclick="return confirm('ATENÇÃO: Tem certeza que deseja EXCLUIR PERMANENTEMENTE este acasalamento? Esta ação é irreversível.');">
                                                            <i class="fas fa-trash"></i> Excluir
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="7">Nenhum acasalamento registado.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/acasalamentos/listar.blade.php ENDPATH**/ ?>