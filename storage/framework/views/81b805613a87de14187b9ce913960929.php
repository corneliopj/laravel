<?php
    $pageTitle = 'Receitas Financeiras';
?>


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        
        <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="content-wrapper px-4 py-2" style="min-height:797px;">
            
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?php echo e($pageTitle); ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                                <li class="breadcrumb-item active">Receitas Financeiras</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="content">
                <div class="container-fluid">
                    
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

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Receitas</h3>
                            <div class="card-tools">
                                <a href="<?php echo e(route('financeiro.receitas.create')); ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nova Receita
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <form action="<?php echo e(route('financeiro.receitas.index')); ?>" method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_categoria">Filtrar por Categoria:</label>
                                            <select name="categoria_id" id="filter_categoria" class="form-control select2 rounded" style="width: 100%;">
                                                <option value="">Todas as Categorias</option>
                                                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($categoria->id); ?>" <?php echo e(request('categoria_id') == $categoria->id ? 'selected' : ''); ?>><?php echo e($categoria->nome); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_data_inicio">Data Início:</label>
                                            <input type="date" name="data_inicio" id="filter_data_inicio" class="form-control rounded" value="<?php echo e(request('data_inicio')); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_data_fim">Data Fim:</label>
                                            <input type="date" name="data_fim" id="filter_data_fim" class="form-control rounded" value="<?php echo e(request('data_fim')); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary rounded mr-2"><i class="fas fa-filter"></i> Filtrar</button>
                                        <a href="<?php echo e(route('financeiro.receitas.index')); ?>" class="btn btn-secondary rounded"><i class="fas fa-sync-alt"></i> Limpar</a>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Valor</th>
                                        <th>Data</th>
                                        <th>Observações</th> 
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $receitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($receita->id); ?></td>
                                            <td><?php echo e($receita->descricao); ?></td>
                                            <td><?php echo e($receita->categoria->nome ?? 'N/A'); ?></td>
                                            <td>R$ <?php echo e(number_format($receita->valor, 2, ',', '.')); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($receita->data)->format('d/m/Y')); ?></td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($receita->observacoes, 50, '...')); ?></td> 
                                            <td>
                                                <a href="<?php echo e(route('financeiro.receitas.edit', $receita->id)); ?>" class="btn btn-info btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-delete-<?php echo e($receita->id); ?>" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                
                                                <div class="modal fade" id="modal-delete-<?php echo e($receita->id); ?>" tabindex="-1" role="dialog" aria-labelledby="modal-delete-label-<?php echo e($receita->id); ?>" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modal-delete-label-<?php echo e($receita->id); ?>">Confirmar Exclusão</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Tem certeza que deseja excluir a receita <strong><?php echo e($receita->descricao); ?></strong> no valor de <strong>R$ <?php echo e(number_format($receita->valor, 2, ',', '.')); ?></strong>? Esta ação não pode ser desfeita.
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                <form action="<?php echo e(route('financeiro.receitas.destroy', $receita->id)); ?>" method="POST" style="display: inline;">
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field('DELETE'); ?>
                                                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma receita encontrada.</td> 
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        
        <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    

    <script>
        $(function () {
            // Inicializar Select2 para o filtro de categoria
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
</body>
</html>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/receitas/index.blade.php ENDPATH**/ ?>