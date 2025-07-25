<?php
    use Carbon\Carbon; // Importa a classe Carbon
    use Illuminate\Support\Facades\Auth; // Importa a classe Auth para usar Auth::id() na view
    $pageTitle = 'Dashboard Financeiro';
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
                        <h1>Dashboard Financeiro</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard Financeiro</li>
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
                                <form action="<?php echo e(route('financeiro.dashboard')); ?>" method="GET" class="form-inline">
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

                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>R$ <?php echo e(number_format($saldoTotal, 2, ',', '.')); ?></h3>
                                <p>Saldo Total</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>R$ <?php echo e(number_format($receitasMes, 2, ',', '.')); ?></h3>
                                <p>Receitas do Mês (<?php echo e(Carbon::create(null, $mes, 1)->locale('pt_BR')->monthName); ?>)</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>R$ <?php echo e(number_format($despesasMes, 2, ',', '.')); ?></h3>
                                <p>Despesas do Mês (<?php echo e(Carbon::create(null, $mes, 1)->locale('pt_BR')->monthName); ?>)</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>R$ <?php echo e(number_format($saldoMes, 2, ',', '.')); ?></h3>
                                <p>Saldo do Mês (<?php echo e(Carbon::create(null, $mes, 1)->locale('pt_BR')->monthName); ?>)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->

                    
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>R$ <?php echo e(number_format($comissaoAcumuladaMes, 2, ',', '.')); ?></h3>
                                <p>Minhas Comissões (<?php echo e(Carbon::create(null, $mes, 1)->locale('pt_BR')->monthName); ?>)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <a href="<?php echo e(route('financeiro.vendas.index', ['user_id' => Auth::id(), 'comissao_paga' => 1, 'data_inicio' => Carbon::createFromDate($ano, $mes, 1)->format('Y-m-d'), 'data_fim' => Carbon::createFromDate($ano, $mes)->endOfMonth()->format('Y-m-d')])); ?>" class="small-box-footer">Ver Vendas <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->

                </div>
                <!-- /.row -->

                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-7 connectedSortable">
                        <!-- BAR CHART: Receitas vs. Despesas por Mês -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="far fa-chart-bar"></i>
                                    Receitas vs. Despesas por Mês (<?php echo e($ano); ?>)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- LINE CHART: Evolução do Saldo -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Evolução do Saldo Acumulado (<?php echo e($ano); ?>)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                    </section>
                    <!-- /.Left col -->

                    <!-- Right col -->
                    <section class="col-lg-5 connectedSortable">
                        <!-- PIE CHART: Distribuição de Despesas por Categoria -->
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie"></i>
                                    Despesas por Categoria (<?php echo e(Carbon::create(null, $mes, 1)->locale('pt_BR')->monthName); ?>/<?php echo e($ano); ?>)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="pieChart" height="250"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                    </section>
                    <!-- /.Right col -->
                </div>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<!-- ./wrapper -->


<?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Page specific script for ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {
        //-------------
        //- BAR CHART -
        //-------------
        var barChartCanvas = $('#barChart').get(0).getContext('2d');
        var barChartData = <?php echo json_encode($dadosGraficoBarras); ?>;
        var barChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
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
                        label += 'R$ ' + tooltipItem.yLabel.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                        return label;
                    }
                }
            }
        };
        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        });

        //-------------
        //- PIE CHART -
        //-------------
        var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
        var pieChartData = <?php echo json_encode($dadosGraficoPizza); ?>;
        var pieChartOptions = {
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
                        return data.labels[tooltipItem.index] + ': R$ ' + currentValue.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + ' (' + percentage + '%)';
                    }
                }
            }
        };
        new Chart(pieChartCanvas, {
            type: 'pie',
            data: pieChartData,
            options: pieChartOptions
        });

        //-------------
        //- LINE CHART -
        //-------------
        var lineChartCanvas = $('#lineChart').get(0).getContext('2d');
        var lineChartData = <?php echo json_encode($dadosGraficoLinha); ?>;
        var lineChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
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
                        label += 'R$ ' + tooltipItem.yLabel.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                        return label;
                    }
                }
            }
        };
        new Chart(lineChartCanvas, {
            type: 'line',
            data: lineChartData,
            options: lineChartOptions
        });
    });
</script>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/financeiro/dashboard.blade.php ENDPATH**/ ?>