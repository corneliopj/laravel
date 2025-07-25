<?php
    $pageTitle = 'Relatório por Categoria (' . ucfirst($tipo) . ')';
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
                                <li class="breadcrumb-item active">Por Categoria</li>
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
                            <h3 class="card-title">Filtros do Relatório por Categoria</h3>
                        </div>
                        <form action="<?php echo e(route('financeiro.relatorios.por_categoria')); ?>" method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="tipo">Tipo:</label>
                                        <select name="tipo" id="tipo" class="form-control select2" style="width: 100%;" onchange="this.form.submit()">
                                            <option value="receita" <?php echo e($tipo == 'receita' ? 'selected' : ''); ?>>Receita</option>
                                            <option value="despesa" <?php echo e($tipo == 'despesa' ? 'selected' : ''); ?>>Despesa</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="data_inicio">Data Início:</label>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo e($dataInicio->format('Y-m-d')); ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="data_fim">Data Fim:</label>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?php echo e($dataFim->format('Y-m-d')); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Aplicar Filtros</button>
                                <a href="<?php echo e(route('financeiro.relatorios.por_categoria')); ?>" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Limpar Filtros</a>
                            </div>
                        </form>
                    </div>

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Totais por Categoria</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Categoria</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $dadosPorCategoria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($dado->categoria->nome ?? 'N/A'); ?></td>
                                            <td class="<?php echo e($tipo == 'receita' ? 'text-success' : 'text-danger'); ?>">
                                                R$ <?php echo e(number_format($dado->total, 2, ',', '.')); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="2" class="text-center">Nenhum dado encontrado para o período e tipo selecionados.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total Geral</th>
                                        <th class="<?php echo e($tipo == 'receita' ? 'text-success' : 'text-danger'); ?>">
                                            R$ <?php echo e(number_format($totalGeral, 2, ',', '.')); ?>

                                        </th>
                                    </tr>
                                </tfoot>
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
            // Inicializar Select2 para os filtros
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
</body>
</html>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/relatorios/por_categoria.blade.php ENDPATH**/ ?>