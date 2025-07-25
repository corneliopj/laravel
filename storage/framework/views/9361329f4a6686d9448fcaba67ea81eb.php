<?php
    $pageTitle = 'Listagem de Variações';
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
                        <h1 class="m-0">Variações</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Variações</li>
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
                                <h3 class="card-title">Todas as Variações</h3>
                                <div class="card-tools">
                                    <a href="<?php echo e(route('variacoes.create')); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nova Variação
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

                                <?php if($variacoes->isEmpty()): ?>
                                    <div class="alert alert-info">
                                        Nenhuma variação cadastrada.
                                    </div>
                                <?php else: ?>
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Tipo de Ave</th>
                                                <th>Ativo</th>
                                                <th style="width: 150px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $variacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($variacao->id); ?></td>
                                                    <td><?php echo e($variacao->nome); ?></td>
                                                    <td><?php echo e($variacao->tipoAve->nome ?? 'N/A'); ?></td> 
                                                    <td>
                                                        <?php if($variacao->ativo): ?>
                                                            <span class="badge badge-success">Sim</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Não</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e(route('variacoes.edit', $variacao->id)); ?>" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if($variacao->ativo): ?>
                                                        <form action="<?php echo e(route('variacoes.destroy', $variacao->id)); ?>" method="POST" style="display: inline-block;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Inativar" onclick="return confirm('Tem certeza que deseja inativar esta variação? Isso não será possível se houver aves associadas.');">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
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
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/variacoes/listar.blade.php ENDPATH**/ ?>