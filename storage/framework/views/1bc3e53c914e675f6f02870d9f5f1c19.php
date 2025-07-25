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
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo e($totalIncubacoesAtivas); ?></h3>
                                <p>Incubações Ativas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-thermometer-half"></i>
                            </div>
                            <a href="<?php echo e(route('incubacoes.index')); ?>" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo e($mortesUltimos30Dias); ?></h3>
                                <p>Mortes Últimos 30 Dias</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                            <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo e(number_format($taxaEclosao, 2)); ?><sup style="font-size: 20px">%</sup></h3>
                                <p>Taxa de Eclosão Global</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <a href="<?php echo e(route('incubacoes.index')); ?>" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <!-- Main row for Charts and Tables -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-7 connectedSortable">
                        <!-- PIE CHART: Aves por Tipo -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie"></i>
                                    Aves Ativas por Tipo
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="avesPorTipoPieChart" height="250"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- LINE CHART: Tendência de Mortes -->
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Tendência de Mortes (Últimos 12 Meses)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="mortesLineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- NOVO: LINE CHART: Taxa de Eclosão de Ovos Viáveis, Mensal -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i>
                                    Taxa de Eclosão de Ovos Viáveis (Mensal)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="eclosionLineChart" height="150"></canvas> 
                                </div>
                                <p class="text-muted text-center mt-3">
                                    Total de Ovos: <?php echo e($dadosTaxaEclosaoMensal['metrics']['total_ovos']); ?> |
                                    Ovos Viáveis: <?php echo e($dadosTaxaEclosaoMensal['metrics']['ovos_viaveis']); ?> |
                                    Ovos Inférteis: <?php echo e($dadosTaxaEclosaoMensal['metrics']['total_inferteis']); ?>

                                </p>
                            </div>
                        </div>
                        <!-- /.card -->

                    </section>
                    <!-- /.Left col -->

                    <!-- Right col -->
                    <section class="col-lg-5 connectedSortable">
                        <!-- BAR CHART: Histórico de Eclosões por Tipo de Ave -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar"></i>
                                    Histórico de Eclosões (Últimos 12 Meses)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="eclosoesBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas> 
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- LINE CHART: Ovos Postos Diariamente -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i> 
                                    Ovos Postos Diariamente (Últimos 30 Dias)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="ovosPostosLineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas> 
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- NOVO: LINE CHART: Ovos Não Eclodidos por Causa (Viáveis), Mensal -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-times-circle"></i>
                                    Ovos Não Eclodidos por Causa (Viáveis), Mensal
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-responsive">
                                    <canvas id="nonEclosionLineChart" height="250"></canvas> 
                                </div>
                                <p class="text-muted text-center mt-3">
                                    Total Infectados: <?php echo e($dadosOvosNaoEclodidosMensal['metrics']['total_infectados']); ?> |
                                    Total Mortos: <?php echo e($dadosOvosNaoEclodidosMensal['metrics']['total_mortos']); ?>

                                </p>
                            </div>
                        </div>
                        <!-- /.card -->

                    </section>
                    <!-- /.Right col -->
                </div>
                <!-- /.row (main row) -->

                <!-- Row for Chocadeira Bar Chart (full width) -->
                <div class="row">
                    <section class="col-lg-12 connectedSortable">
                        <!-- NOVO: BAR CHART: Desempenho por Chocadeira -->
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

                <!-- Row for Alerts and Incubation Table -->
                <div class="row">
                    <section class="col-lg-12 connectedSortable">
                        <!-- Alertas e Notificações -->
                        <?php if(!empty($alertas)): ?>
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-bell"></i>
                                        Alertas e Notificações
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php $__currentLoopData = $alertas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="alert alert-<?php echo e($alerta['type']); ?> alert-dismissible fade show" role="alert">
                                            <?php echo e($alerta['message']); ?>

                                            <?php if(isset($alerta['link'])): ?>
                                                <a href="<?php echo e($alerta['link']); ?>" class="alert-link">Ver detalhes</a>
                                            <?php endif; ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- /.card -->

                        <!-- Tabela de Incubações Ativas -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-egg"></i>
                                    Quadro de Incubações Ativas
                                </h3>
                                <div class="card-tools">
                                    <form action="<?php echo e(route('dashboard')); ?>" method="GET" class="form-inline">
                                        <label for="filter_tipo_ave" class="mr-2">Tipo de Ave:</label>
                                        <select name="tipo_ave_id" id="filter_tipo_ave" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                            <option value="">Todos</option>
                                            <?php $__currentLoopData = $tiposAves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($tipo->id); ?>" <?php echo e($selectedTipoAve == $tipo->id ? 'selected' : ''); ?>><?php echo e($tipo->nome); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                        <label for="filter_lote" class="mr-2">Lote:</label>
                                        <select name="lote_id" id="filter_lote" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                            <option value="">Todos</option>
                                            <?php $__currentLoopData = $lotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($lote->id); ?>" <?php echo e($selectedLote == $lote->id ? 'selected' : ''); ?>><?php echo e($lote->identificacao_lote); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-sm btn-secondary">Limpar Filtros</a>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Lote</th>
                                                <th>Tipo Ave</th>
                                                <th>Entrada</th>
                                                <th>Previsão Eclosão</th>
                                                <th>Ovos</th>
                                                <th>Progresso</th>
                                                <th>Status</th>
                                                <th style="width: 100px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $incubacoesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incubacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($incubacao['id']); ?></td>
                                                    <td><?php echo e($incubacao['lote_nome']); ?></td>
                                                    <td><?php echo e($incubacao['tipo_ave_nome']); ?></td>
                                                    <td><?php echo e($incubacao['data_entrada_incubadora']); ?></td>
                                                    <td><?php echo e($incubacao['data_prevista_eclosao']); ?></td>
                                                    <td><?php echo e($incubacao['quantidade_ovos']); ?></td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar bg-primary" style="width: <?php echo e($incubacao['progress_percentage']); ?>%"></div>
                                                        </div>
                                                        <span class="badge bg-primary"><?php echo e($incubacao['progress_percentage']); ?>%</span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $badgeClass = '';
                                                            switch ($incubacao['status']) {
                                                                case 'Em andamento': $badgeClass = 'badge-info'; break;
                                                                case 'Finalizando': $badgeClass = 'badge-warning'; break;
                                                                case 'Concluído': $badgeClass = 'badge-success'; break;
                                                                case 'Atrasado': $badgeClass = 'badge-danger'; break;
                                                                case 'Prevista': $badgeClass = 'badge-secondary'; break;
                                                                default: $badgeClass = 'badge-secondary'; break;
                                                            }
                                                        ?>
                                                        <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($incubacao['status']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e($incubacao['link_detalhes']); ?>" class="btn btn-sm btn-info" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">Nenhuma incubação ativa encontrada.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
   <?php echo $__env->make('layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>


<script>
    $(function () {
        //-------------
        //- PIE CHART: Aves por Tipo -
        //-------------
        var avesPorTipoPieChartCanvas = $('#avesPorTipoPieChart').get(0).getContext('2d');
        var avesPorTipoPieChartData = {
            labels: <?php echo json_encode($labelsAvesPorTipo); ?>,
            datasets: [
                {
                    data: <?php echo json_encode($dataAvesPorTipo); ?>,
                    backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'], // Exemplo de cores
                }
            ]
        };
        var avesPorTipoPieChartOptions = {
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
                        return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + '%)';
                    }
                }
            }
        };
        new Chart(avesPorTipoPieChartCanvas, {
            type: 'pie',
            data: avesPorTipoPieChartData,
            options: avesPorTipoPieChartOptions
        });

        //-------------
        //- LINE CHART: Tendência de Mortes -
        //-------------
        var mortesLineChartCanvas = $('#mortesLineChart').get(0).getContext('2d');
        var mortesLineChartData = {
            labels: <?php echo json_encode($labelsMortesMes); ?>,
            datasets: [
                {
                    label: 'Número de Mortes',
                    fill: false,
                    borderColor: '#dc3545', // Vermelho
                    data: <?php echo json_encode($dataMortesMes); ?>

                }
            ]
        };
        var mortesLineChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0 // Apenas números inteiros para contagem
                    }
                }]
            }
        };
        new Chart(mortesLineChartCanvas, {
            type: 'line',
            data: mortesLineChartData,
            options: mortesLineChartOptions
        });

        //-----------------------------------------
        //- BAR CHART: Histórico de Eclosões por Tipo de Ave -
        //-----------------------------------------
        var eclosoesBarChartCanvas = $('#eclosoesBarChart').get(0).getContext('2d');
        var eclosoesBarChartData = {
            labels: <?php echo json_encode($labelsEclosoesMesFormatted); ?>,
            datasets: <?php echo json_encode($dataEclosoesPorTipo); ?>

        };
        var eclosoesBarChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            scales: {
                xAxes: [{
                    stacked: true, // Para barras empilhadas se houver múltiplos tipos de ave no mesmo mês
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            }
        };
        new Chart(eclosoesBarChartCanvas, {
            type: 'bar',
            data: eclosoesBarChartData,
            options: eclosoesBarChartOptions
        });

        //-----------------------------------------
        //- LINE CHART: Ovos Postos Diariamente -
        //-----------------------------------------
        var ovosPostosLineChartCanvas = $('#ovosPostosLineChart').get(0).getContext('2d');
        var ovosPostosLineChartData = {
            labels: <?php echo json_encode($labelsOvosPostos); ?>,
            datasets: [
                {
                    label: 'Ovos Postos',
                    fill: false,
                    borderColor: 'rgba(255, 193, 7, 1)', // Amarelo
                    data: <?php echo json_encode($dataOvosPostos); ?>

                }
            ]
        };
        var ovosPostosLineChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            }
        };
        new Chart(ovosPostosLineChartCanvas, {
            type: 'line',
            data: ovosPostosLineChartData,
            options: ovosPostosLineChartOptions
        });


        //-----------------------------------------------------
        //- NOVO: LINE CHART: Taxa de Eclosão de Ovos Viáveis, Mensal -
        //-----------------------------------------------------
        var eclosionLineChartCanvas = $('#eclosionLineChart').get(0).getContext('2d');
        var eclosionLineChartData = <?php echo json_encode($dadosTaxaEclosaoMensal); ?>;
        var eclosionLineChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return value + '%'; // Adiciona '%' ao rótulo do eixo Y
                        }
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += tooltipItem.yLabel + '%';
                        return label;
                    }
                }
            }
        };
        new Chart(eclosionLineChartCanvas, {
            type: 'line', // ALTERADO PARA LINE
            data: eclosionLineChartData,
            options: eclosionLineChartOptions
        });

        //-----------------------------------------------------
        //- NOVO: LINE CHART: Ovos Não Eclodidos por Causa (Viáveis), Mensal -
        //-----------------------------------------------------
        var nonEclosionLineChartCanvas = $('#nonEclosionLineChart').get(0).getContext('2d');
        var nonEclosionLineChartData = <?php echo json_encode($dadosOvosNaoEclodidosMensal); ?>;
        var nonEclosionLineChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
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
        new Chart(nonEclosionLineChartCanvas, {
            type: 'line',
            data: nonEclosionLineChartData,
            options: nonEclosionLineChartOptions
        });

        //----------------------------------
        //- NOVO: CHOCADEIRA BAR CHART -
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
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/dashboard.blade.php ENDPATH**/ ?>