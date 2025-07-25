<?php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    $pageTitle = 'Contracheque';
?>


<?php echo $__env->make('layouts.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="wrapper">
    
    <?php echo $__env->make('layouts.partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper px-4 py-2" style="min-height:797px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contracheque</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Contracheque</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Filtros de Mês e Ano -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Filtrar por Período</h3>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo e(route('financeiro.contracheque.index')); ?>" method="GET" class="form-inline">
                                    <div class="form-group mr-3">
                                        <label for="mes" class="mr-2">Mês:</label>
                                        <select name="mes" id="mes" class="form-control form-control-sm">
                                            <?php for($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?php echo e($i); ?>" <?php echo e($mes == $i ? 'selected' : ''); ?>><?php echo e(Carbon::create(null, $i, 1)->locale('pt_BR')->monthName); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label for="ano" class="mr-2">Ano:</label>
                                        <select name="ano" id="ano" class="form-control form-control-sm">
                                            <?php for($i = Carbon::now()->year - 5; $i <= Carbon::now()->year + 1; $i++): ?>
                                                <option value="<?php echo e($i); ?>" <?php echo e($ano == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Aplicar Filtro</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Contracheque do Mês -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    Contracheque do Mês (<?php echo e(Carbon::create(null, $mes, 1)->locale('pt_BR')->monthName); ?>/<?php echo e($ano); ?>)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <tr>
                                                <td>+ Salário</td>
                                                <td class="text-right">R$ <?php echo e(number_format($contrachequeSumario['salario'], 2, ',', '.')); ?></td>
                                            </tr>
                                            <tr>
                                                <td>+ Comissões</td>
                                                <td class="text-right">R$ <?php echo e(number_format($contrachequeSumario['comissoes'], 2, ',', '.')); ?></td>
                                            </tr>
                                            <?php if($contrachequeSumario['outros_positivos'] > 0): ?>
                                            <tr>
                                                <td>+ Outros Proventos</td>
                                                <td class="text-right">R$ <?php echo e(number_format($contrachequeSumario['outros_positivos'], 2, ',', '.')); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td>- Adiantamento</td>
                                                <td class="text-right">R$ <?php echo e(number_format($contrachequeSumario['adiantamento'], 2, ',', '.')); ?></td>
                                            </tr>
                                            <tr>
                                                <td>- Cartão de crédito</td>
                                                <td class="text-right">R$ <?php echo e(number_format($contrachequeSumario['cartao_credito'], 2, ',', '.')); ?></td>
                                            </tr>
                                            <?php if($contrachequeSumario['outros_negativos'] > 0): ?>
                                            <tr>
                                                <td>- Outros Descontos</td>
                                                <td class="text-right">R$ <?php echo e(number_format($contrachequeSumario['outros_negativos'], 2, ',', '.')); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr class="table-secondary">
                                                <td><strong>Valor Bruto:</strong></td>
                                                <td class="text-right"><strong>R$ <?php echo e(number_format($contrachequeSumario['valor_bruto'], 2, ',', '.')); ?></strong></td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td><strong>Descontos:</strong></td>
                                                <td class="text-right"><strong>R$ <?php echo e(number_format($contrachequeSumario['descontos'], 2, ',', '.')); ?></strong></td>
                                            </tr>
                                            <tr class="table-primary">
                                                <td><strong>Saldo Líquido:</strong></td>
                                                <td class="text-right"><strong>R$ <?php echo e(number_format($contrachequeSumario['saldo_liquido'], 2, ',', '.')); ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <h4>Lançamentos Detalhados:</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Descrição</th>
                                                <th>Tipo</th>
                                                <th class="text-right">Valor</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $contrachequeSumario['lancamentos_detalhados']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lancamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($lancamento['data']); ?></td>
                                                    <td><?php echo e($lancamento['descricao']); ?></td>
                                                    <td>
                                                        <?php if($lancamento['tipo_lancamento'] == 'positivo'): ?>
                                                            <span class="badge badge-success">Positivo</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Negativo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">R$ <?php echo e(number_format($lancamento['valor'], 2, ',', '.')); ?></td>
                                                    <td>
                                                        <form action="<?php echo e(route('financeiro.contracheque.destroy', $lancamento['id'])); ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este lançamento?');">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Nenhum lançamento de contracheque encontrado para este mês.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <hr>

                                <h4>Adicionar Novo Lançamento:</h4>
                                <form action="<?php echo e(route('financeiro.contracheque.store')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="user_id" value="<?php echo e(Auth::id()); ?>">
                                    <div class="form-group">
                                        <label for="descricao_contracheque">Descrição</label>
                                        <input type="text" name="descricao" id="descricao_contracheque" class="form-control form-control-sm" required value="<?php echo e(old('descricao')); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="valor_contracheque">Valor</label>
                                        <input type="number" step="0.01" name="valor" id="valor_contracheque" class="form-control form-control-sm" required min="0.01" value="<?php echo e(old('valor')); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_lancamento_contracheque">Tipo de Lançamento</label>
                                        <select name="tipo_lancamento" id="tipo_lancamento_contracheque" class="form-control form-control-sm" required>
                                            <option value="positivo" <?php echo e(old('tipo_lancamento') == 'positivo' ? 'selected' : ''); ?>>Positivo</option>
                                            <option value="negativo" <?php echo e(old('tipo_lancamento') == 'negativo' ? 'selected' : ''); ?>>Negativo</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="data_contracheque">Data</label>
                                        <input type="date" name="data" id="data_contracheque" class="form-control form-control-sm" value="<?php echo e(old('data', Carbon::now()->format('Y-m-d'))); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Adicionar Lançamento</button>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<!-- ./wrapper -->


<?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



<script>
    $(document).ready(function() {
        // Não há scripts de gráficos aqui, apenas se houver alguma inicialização específica de formulário
        // ou outros elementos que só existem nesta página.
    });
</script>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/contracheque/index.blade.php ENDPATH**/ ?>