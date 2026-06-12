<?php
    $pageTitle = 'Dashboard Principal';
?>



<?php $__env->startSection('content'); ?>
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
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-gradient-success text-white shadow-sm" style="border: none;">
                            <div class="card-body d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; font-size: 20px; min-width: 45px;">
                                        <i class="fas fa-cart-plus"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Nova Venda</h5>
                                        <small style="opacity: 0.9;">Registre uma venda rapidamente para agilizar o atendimento</small>
                                    </div>
                                </div>
                                <a href="<?php echo e(route('financeiro.vendas.create')); ?>" class="btn btn-light btn-lg font-weight-bold text-success shadow-sm px-4">
                                    <i class="fas fa-plus"></i> Nova Venda
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-filter"></i>
                                    Filtros de Período
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body" style="display: none;">
                                <form method="GET" action="<?php echo e(route('dashboard')); ?>" id="filtros-form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ano">Ano:</label>
                                                <select name="ano" id="ano" class="form-control">
                                                    <?php for($y = Carbon\Carbon::now()->year; $y >= 2020; $y--): ?>
                                                        <option value="<?php echo e($y); ?>" <?php echo e($ano == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="trimestre">Trimestre:</label>
                                                <select name="trimestre" id="trimestre" class="form-control">
                                                    <option value="">Todos</option>
                                                    <option value="1" <?php echo e($trimestre == '1' ? 'selected' : ''); ?>>1º Trimestre</option>
                                                    <option value="2" <?php echo e($trimestre == '2' ? 'selected' : ''); ?>>2º Trimestre</option>
                                                    <option value="3" <?php echo e($trimestre == '3' ? 'selected' : ''); ?>>3º Trimestre</option>
                                                    <option value="4" <?php echo e($trimestre == '4' ? 'selected' : ''); ?>>4º Trimestre</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mes">Mês:</label>
                                                <select name="mes" id="mes" class="form-control">
                                                    <option value="">Todos</option>
                                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                                        <option value="<?php echo e($m); ?>" <?php echo e($mes == $m ? 'selected' : ''); ?>>
                                                            <?php echo e(Carbon\Carbon::create(null, $m, 1)->monthName); ?>

                                                        </option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-sync-alt"></i> Atualizar
                                                </button>
                                                <button type="button" class="btn btn-secondary ml-2" onclick="limparFiltros()">
                                                    <i class="fas fa-eraser"></i> Limpar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
                                <h3><?php echo e($totalGeralAves); ?></h3>
                                <p>Total Geral de Aves</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-paw"></i>
                            </div>
                            <a href="#" class="small-box-footer">Visão Geral <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->

                    
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3><?php echo e($totalAvesAtivas); ?></h3>
                                <p>Aves Individuais Ativas</p>
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

                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Indicadores de Performance (KPIs)
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeTaxaEclosao" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeTaxaFertilidade" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeMelhorChocadeira" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div id="gaugeMediaOvos" style="width: 200px; height: 160px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt"></i>
                                    Previsão de Eclosão - Próximos 30 Dias
                                </h3>
                                <div class="card-tools">
                                    <span class="badge badge-warning"><?php echo e(count($previsoesEclosao)); ?> incubações</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if(count($previsoesEclosao) > 0): ?>
                                    <div class="row">
                                        <?php $__currentLoopData = $previsoesEclosao; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $previsao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card card-outline 
                                                    <?php if($previsao['status'] == 'urgente'): ?> card-danger 
                                                    <?php elseif($previsao['status'] == 'proximo'): ?> card-warning 
                                                    <?php elseif($previsao['status'] == 'atrasado'): ?> card-dark 
                                                    <?php else: ?> card-info <?php endif; ?>">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">
                                                            <i class="fas fa-egg"></i>
                                                            <?php echo e($previsao['lote']); ?> - <?php echo e($previsao['tipo_ave']); ?>

                                                        </h5>
                                                        <div class="card-tools">
                                                            <span class="badge 
                                                                <?php if($previsao['status'] == 'urgente'): ?> badge-danger 
                                                                <?php elseif($previsao['status'] == 'proximo'): ?> badge-warning 
                                                                <?php elseif($previsao['status'] == 'atrasado'): ?> badge-dark 
                                                                <?php else: ?> badge-info <?php endif; ?>">
                                                                <?php if($previsao['status'] == 'urgente'): ?> URGENTE
                                                                <?php elseif($previsao['status'] == 'proximo'): ?> PRÓXIMO
                                                                <?php elseif($previsao['status'] == 'atrasado'): ?> ATRASADO
                                                                <?php else: ?> NORMAL <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <strong>Data Eclosão:</strong><br>
                                                                <span class="text-primary"><?php echo e($previsao['data_eclosao']); ?></span>
                                                            </div>
                                                            <div class="col-6">
                                                                <strong>Dias Restantes:</strong><br>
                                                                <span class="text-danger"><?php echo e($previsao['dias_restantes']); ?> dias</span>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-6">
                                                                <strong>Ovos:</strong><br>
                                                                <span class="text-success"><?php echo e($previsao['quantidade_ovos']); ?></span>
                                                            </div>
                                                            <div class="col-6">
                                                                <strong>Chocadeira:</strong><br>
                                                                <span class="text-info"><?php echo e($previsao['chocadeira']); ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <strong>Progresso da Incubação:</strong>
                                                            <div class="progress mt-1">
                                                                <div class="progress-bar 
                                                                    <?php if($previsao['progresso'] >= 90): ?> bg-success 
                                                                    <?php elseif($previsao['progresso'] >= 70): ?> bg-warning 
                                                                    <?php else: ?> bg-info <?php endif; ?>" 
                                                                    role="progressbar" 
                                                                    style="width: <?php echo e($previsao['progresso']); ?>%" 
                                                                    aria-valuenow="<?php echo e($previsao['progresso']); ?>" 
                                                                    aria-valuemin="0" 
                                                                    aria-valuemax="100">
                                                                    <?php echo e($previsao['progresso']); ?>%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php if($previsao['temperatura_atual'] > 0 || $previsao['umidade_atual'] > 0): ?>
                                                            <div class="row mt-2">
                                                                <div class="col-6">
                                                                    <small><strong>Temp:</strong> <?php echo e($previsao['temperatura_atual']); ?>°C</small>
                                                                </div>
                                                                <div class="col-6">
                                                                    <small><strong>Umidade:</strong> <?php echo e($previsao['umidade_atual']); ?>%</small>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="card-footer">
                                                        <a href="<?php echo e(route('incubacoes.show', $previsao['id'])); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> Ver Detalhes
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Não há eclosões previstas para os próximos 30 dias.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .kpi-gauge {
        margin: 0 auto;
        text-align: center;
    }
    .previsao-card {
        transition: transform 0.2s;
    }
    .previsao-card:hover {
        transform: translateY(-2px);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
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
                    borderColor: '#dc3545',
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
                        precision: 0
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
                    stacked: true,
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
                    borderColor: 'rgba(255, 193, 7, 1)',
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
                            return value + '%';
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
            type: 'line',
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

    // NOVO: Renderização dos KPIs Visuais (Gauges)
    function renderGauge(elementId, value, label, max = 100) {
        const gaugeElement = document.getElementById(elementId);
        if (!gaugeElement) return;

        const gauge = new JustGage({
            id: elementId,
            value: value,
            min: 0,
            max: max,
            label: label,
            pointer: true,
            gaugeWidthScale: 0.6,
            counter: true,
            relativeGaugeSize: true,
            levelColors: [
                "#ff0000",
                "#f9c802",
                "#a9d70b"
            ],
            customSectors: [
                { color: "#ff0000", lo: 0, hi: 50 },
                { color: "#f9c802", lo: 51, hi: 75 },
                { color: "#a9d70b", lo: 76, hi: 100 }
            ],
            valueFontColor: '#343a40',
            labelFontColor: '#6c757d',
            pointerOptions: {
                toplength: -15,
                bottomlength: 10,
                bottomwidth: 12,
                color: '#8e8e93',
                stroke: '#ffffff',
                stroke_width: 3,
                stroke_linecap: 'round'
            },
            shadowOpacity: 0.5,
            shadowSize: 5,
            shadowVerticalOffset: 2
        });
    }

    // Renderizar os gauges com os dados do Laravel
    document.addEventListener('DOMContentLoaded', function() {
        renderGauge('gaugeTaxaEclosao', <?php echo json_encode($kpis['taxa_eclosao_30_dias'], 15, 512) ?>, 'Taxa Eclosão (30D)');
        renderGauge('gaugeTaxaFertilidade', <?php echo json_encode($kpis['taxa_fertilidade'], 15, 512) ?>, 'Taxa Fertilidade');
        renderGauge('gaugeMelhorChocadeira', <?php echo json_encode($kpis['melhor_chocadeira_eficiencia'], 15, 512) ?>, 'Melhor Chocadeira');
        renderGauge('gaugeMediaOvos', <?php echo json_encode($kpis['media_ovos_incubacao'], 15, 512) ?>, 'Média Ovos/Incub.', 50);
    });

    // Função para limpar os filtros
    function limparFiltros() {
        document.getElementById('ano').value = '';
        document.getElementById('trimestre').value = '';
        document.getElementById('mes').value = '';
        document.getElementById('filtros-form').submit();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/raphael@2.3.0/raphael.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/justgage@1.4.0/justgage.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/dashboard.blade.php ENDPATH**/ ?>