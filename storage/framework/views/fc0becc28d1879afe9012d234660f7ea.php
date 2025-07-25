<?php
    $pageTitle = 'Relatório Detalhado de Transações';
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
                                <li class="breadcrumb-item"><a href="<?php echo e(route('financeiro.relatorios.index')); ?>">Relatórios Financeiros</a></li>
                                <li class="breadcrumb-item active">Transações</li>
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
                            <h3 class="card-title">Filtros do Relatório</h3>
                        </div>
                        <form action="<?php echo e(route('financeiro.relatorios.transacoes')); ?>" method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="tipo_transacao">Tipo de Transação:</label>
                                        <select name="tipo_transacao" id="tipo_transacao" class="form-control select2" style="width: 100%;">
                                            <option value="">Todas</option>
                                            <option value="receita" <?php echo e(request('tipo_transacao') == 'receita' ? 'selected' : ''); ?>>Receita</option>
                                            <option value="despesa" <?php echo e(request('tipo_transacao') == 'despesa' ? 'selected' : ''); ?>>Despesa</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="categoria_id">Categoria:</label>
                                        <select name="categoria_id" id="categoria_id" class="form-control select2" style="width: 100%;">
                                            <option value="">Todas as Categorias</option>
                                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($categoria->id); ?>" <?php echo e(request('categoria_id') == $categoria->id ? 'selected' : ''); ?>><?php echo e($categoria->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="data_inicio">Data Início:</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo e(request('data_inicio')); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="data_fim">Data Fim:</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?php echo e(request('data_fim')); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Aplicar Filtros</button>
                                <a href="<?php echo e(route('financeiro.relatorios.transacoes')); ?>" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Limpar Filtros</a>
                            </div>
                        </form>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Transações Encontradas</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Valor</th>
                                        <th>Data</th>
                                        <th>Observações</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $transacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($transacao->id); ?></td>
                                            <td>
                                                <?php if($transacao->type == 'receita'): ?>
                                                    <span class="badge badge-success">Receita</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Despesa</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($transacao->descricao); ?></td>
                                            <td><?php echo e($transacao->categoria->nome ?? 'N/A'); ?></td>
                                            <td class="<?php echo e($transacao->type == 'receita' ? 'text-success' : 'text-danger'); ?>">
                                                R$ <?php echo e(number_format($transacao->valor, 2, ',', '.')); ?>

                                            </td>
                                            <td><?php echo e(\Carbon\Carbon::parse($transacao->data)->format('d/m/Y')); ?></td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($transacao->observacoes, 50, '...')); ?></td> 
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhuma transação encontrada com os filtros aplicados.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <?php echo e($transacoes->links('pagination::bootstrap-4')); ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        
        <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    

    <script>
        $(function () {
            // Inicializar Select2 para os filtros
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
</body>
</html>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/relatorios/transacoes.blade.php ENDPATH**/ ?>