<?php
    $pageTitle = 'Dashboard Principal';
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
                        <h1>Dashboard Principal</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo e($totalAvesAtivas); ?></h3>
                                <p>Aves Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dove"></i>
                            </div>
                            <a href="<?php echo e(route('aves.index')); ?>" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?php echo e($totalMachos); ?></h3>
                                <p>Machos Ativos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-mars"></i>
                            </div>
                            <a href="<?php echo e(route('aves.index', ['sexo' => 'Macho'])); ?>" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-pink">
                            <div class="inner">
                                <h3><?php echo e($totalFemeas); ?></h3>
                                <p>Fêmeas Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-venus"></i>
                            </div>
                            <a href="<?php echo e(route('aves.index', ['sexo' => 'Femea'])); ?>" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo e($totalAcasalamentosAtivos); ?></h3>
                                <p>Acasalamentos Ativos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <a href="<?php echo e(route('acasalamentos.index')); ?>" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo e($totalPosturasAtivas); ?></h3>
                                <p>Posturas de Ovos Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-egg"></i>
                            </div>
                            <a href="<?php echo e(route('posturas_ovos.index')); ?>" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <!-- Main row for Incubation Charts -->
                <div class="row">
                    <!-- Left col for Eclosion and Non-Eclosion Charts -->
                    <section class="col-lg-7 connectedSortable">
                        <!-- PIE CHART: Taxa de Eclosão de Ovos Viáveis -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-egg"></i>
                                    Taxa de Eclosão de Ovos Viáveis
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="chart-responsive">
                                            <canvas id="eclosionPieChart" height="150"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="chart-legend clearfix">
                                            <li><i class="far fa-circle text-success"></i> Eclodidos (<?php echo e(number_format($dadosTaxaEclosao['metrics']['taxa_eclosao'], 2)); ?>%)</li>
                                            <li><i class="far fa-circle text-danger"></i> Não Eclodidos (<?php echo e(number_format($dadosTaxaEclosao['metrics']['taxa_nao_eclosao'], 2)); ?>%)</li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="text-muted text-center mt-3">
                                    Total de Ovos: <?php echo e($dadosTaxaEclosao['metrics']['total_ovos']); ?> |
                                    Ovos Viáveis: <?php echo e($dadosTaxaEclosao['metrics']['ovos_viaveis']); ?> |
                                    Ovos Inférteis: <?php echo e($dadosTaxaEclosao['metrics']['total_inferteis']); ?>

                                </p>
                            </div>
                        </div>
                        <!-- /.card -->
                    </section>

                    <!-- Right col for Non-Eclosion and Chocadeira Charts -->
                    <section class="col-lg-5 connectedSortable">
                        <!-- PIE CHART: Ovos Não Eclodidos por Causa (Viáveis) -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-times-circle"></i>
                                    Ovos Não Eclodidos por Causa (Viáveis)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="nonEclosionPieChart" height="250"></canvas>
                                </div>
                                <p class="text-muted text-center mt-3">
                                    Total Infectados: <?php echo e($dadosOvosNaoEclodidos['metrics']['total_infectados']); ?> |
                                    Total Mortos: <?php echo e($dadosOvosNaoEclodidos['metrics']['total_mortos']); ?>

                                </p>
                            </div>
                        </div>
                        <!-- /.card -->
                    </section>
                </div>
                <!-- /.row -->

                <div class="row">
                    <section class="col-lg-12 connectedSortable">
                        <!-- BAR CHART: Desempenho por Chocadeira -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-thermometer-half"></i>
                                    Desempenho por Chocadeira
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="chocadeiraBarChart" height="250"></canvas>
                                </div>
                                <?php if(!empty($dadosDesempenhoChocadeira['labels'])): ?>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th>Chocadeira</th>
                                                    <?php $__currentLoopData = $dadosDesempenhoChocadeira['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <th><?php echo e($label); ?></th>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Taxa Eclosão (%)</td>
                                                    <?php $__currentLoopData = $dadosDesempenhoChocadeira['taxas_eclosao']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <td><?php echo e(number_format($taxa, 2)); ?>%</td>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- /.card -->
                    </section>
                </div>
                <!-- /.row -->

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?php echo e(asset('vendor/jquery/jquery.min.js')); ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo e(asset('vendor/adminlte/dist/js/adminlte.min.js')); ?>"></script>
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(function () {
        //-------------------------
        //- ECLOSION PIE CHART -
        //-------------------------
        var eclosionPieChartCanvas = $('#eclosionPieChart').get(0).getContext('2d');
        var eclosionPieChartData = <?php echo json_encode($dadosTaxaEclosao); ?>;
        var eclosionPieChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                            return previousValue + currentValue;
                        });
                        var currentValue = dataset.data[tooltipItem.index];
                        var percentage = parseFloat((currentValue/total*100).toFixed(2));
                        return data.labels[tooltipItem.index] + ': ' + currentValue + ' ovos (' + percentage + '%)';
                    }
                }
            }
        };
        new Chart(eclosionPieChartCanvas, {
            type: 'pie',
            data: eclosionPieChartData,
            options: eclosionPieChartOptions
        });

        //----------------------------------
        //- Ovos Não Eclodidos por Causa (PIE CHART) -
        //----------------------------------
        var nonEclosionPieChartCanvas = $('#nonEclosionPieChart').get(0).getContext('2d');
        var nonEclosionPieChartData = <?php echo json_encode($dadosOvosNaoEclodidos); ?>;
        var nonEclosionPieChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                            return previousValue + currentValue;
                        });
                        var currentValue = dataset.data[tooltipItem.index];
                        var percentage = parseFloat((currentValue/total*100).toFixed(2));
                        return data.labels[tooltipItem.index] + ': ' + currentValue + ' ovos (' + percentage + '%)';
                    }
                }
            }
        };
        new Chart(nonEclosionPieChartCanvas, {
            type: 'pie',
            data: nonEclosionPieChartData,
            options: nonEclosionPieChartOptions
        });

        //----------------------------------
        //- CHOCADEIRA BAR CHART -
        //----------------------------------
        var chocadeiraBarChartCanvas = $('#chocadeiraBarChart').get(0).getContext('2d');
        var chocadeiraBarChartData = <?php echo json_encode($dadosDesempenhoChocadeira); ?>;
        var chocadeiraBarChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return value + ' ovos';
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += tooltipItem.yLabel + ' ovos';
                        return label;
                    }
                }
            }
        };
        new Chart(chocadeiraBarChartCanvas, {
            type: 'bar',
            data: chocadeiraBarChartData,
            options: chocadeiraBarChartOptions
        });

    });
</script>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/dashboard/index.blade.php ENDPATH**/ ?>