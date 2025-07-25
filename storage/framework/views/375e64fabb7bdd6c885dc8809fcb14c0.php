<!-- resources/views/layouts/partials/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo e(route('dashboard')); ?>" class="brand-link">
        <img src="<?php echo e(asset('img/logo.png')); ?>" alt="Criatório Coroné" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Criatório Coroné</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                
                <?php
                    $userImagePath = 'img/' . (Auth::id() ?? 'default') . '.png'; // Assume 'default.png' se não houver ID
                    $defaultImagePath = 'dist/img/user2-160x160.jpg'; // Imagem padrão do AdminLTE
                ?>
                <img src="<?php echo e(asset($userImagePath)); ?>"
                     class="img-circle elevation-2"
                     alt="User Image"
                     onerror="this.onerror=null;this.src='<?php echo e(asset($defaultImagePath)); ?>';"> 
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo e(Auth::user()->name ?? 'Utilizador'); ?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Pesquisar" aria-label="Search" name="query" id="sidebar-search-input">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(Request::routeIs('dashboard') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i> 
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <!-- Menu Aves e Submenus -->
                <li class="nav-item <?php echo e(Request::routeIs(['aves.*', 'tipos_aves.*', 'variacoes.*', 'lotes.*', 'acasalamentos.*', 'posturas_ovos.*', 'incubacoes.*']) ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(Request::routeIs(['aves.*', 'tipos_aves.*', 'variacoes.*', 'lotes.*', 'acasalamentos.*', 'posturas_ovos.*', 'incubacoes.*']) ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-feather-alt"></i> 
                        <p>
                            Aves
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('aves.index')); ?>" class="nav-link <?php echo e(Request::routeIs(['aves.index', 'aves.create', 'aves.show', 'aves.edit']) ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-feather"></i> 
                                <p>Listar Aves</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('tipos_aves.index')); ?>" class="nav-link <?php echo e(Request::routeIs('tipos_aves.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-th-list"></i> 
                                <p>Tipos de Aves</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('variacoes.index')); ?>" class="nav-link <?php echo e(Request::routeIs('variacoes.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-palette"></i> 
                                <p>Variações</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('lotes.index')); ?>" class="nav-link <?php echo e(Request::routeIs('lotes.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-boxes"></i> 
                                <p>Lotes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('acasalamentos.index')); ?>" class="nav-link <?php echo e(Request::routeIs('acasalamentos.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-heart"></i> 
                                <p>Acasalamentos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('posturas_ovos.index')); ?>" class="nav-link <?php echo e(Request::routeIs('posturas_ovos.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-egg"></i> 
                                <p>Postura de Ovos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('incubacoes.index')); ?>" class="nav-link <?php echo e(Request::routeIs('incubacoes.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-temperature-high"></i> 
                                <p>Incubação</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Separador Visual -->
                <li class="nav-header">FINANCEIRO</li>

                <!-- Menu Financeiro e Submenus -->
                <li class="nav-item <?php echo e(Request::routeIs('financeiro.*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(Request::routeIs('financeiro.*') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-dollar-sign"></i> 
                        <p>
                            Financeiro
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.dashboard')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.dashboard') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-chart-line"></i> 
                                <p>Dashboard Financeiro</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.receitas.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.receitas.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-hand-holding-usd"></i> 
                                <p>Receitas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.despesas.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.despesas.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-money-bill-wave"></i> 
                                <p>Despesas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.reservas.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.reservas.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-calendar-check"></i> 
                                <p>Reservas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.vendas.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-cash-register"></i> 
                                <p>Vendas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.categorias.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.categorias.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-folder"></i> 
                                <p>Categorias Financeiras</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.transacoes_recorrentes.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.transacoes_recorrentes.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-redo"></i> 
                                <p>Transações Recorrentes</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.contracheque.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.contracheque.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i> 
                                <p>Contracheque</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="<?php echo e(route('financeiro.relatorios.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.relatorios.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-chart-pie"></i> 
                                <p>Relatórios Financeiros</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sair -->
                <li class="nav-item">
                    <a href="<?php echo e(route('logout')); ?>" class="nav-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i> 
                        <p>
                            Sair
                        </p>
                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<?php /**PATH /home/cpetersenjr.com/httpdocs/laravel/resources/views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>