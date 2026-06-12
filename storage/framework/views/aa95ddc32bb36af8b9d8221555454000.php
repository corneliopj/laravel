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
            <p>Dashboard</p>
        </a>
    </li>

    <li class="nav-header">PRODUÇÃO</li>

    <!-- Menu Aves -->
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
            <li class="nav-item <?php echo e(Request::routeIs('plantel.*') || Request::routeIs('movimentacoes-plantel.*') ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(Request::routeIs('plantel.*') || Request::routeIs('movimentacoes-plantel.*') ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-boxes"></i>
                    <p>
                        Plantéis
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('plantel.index')); ?>" class="nav-link <?php echo e(Request::routeIs('plantel.index') ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Listar Plantéis</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('plantel.create')); ?>" class="nav-link <?php echo e(Request::routeIs('plantel.create') ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Novo Plantel</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('movimentacoes-plantel.index')); ?>" class="nav-link <?php echo e(Request::routeIs('movimentacoes-plantel.*') ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Movimentações</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('incubacoes.index')); ?>" class="nav-link <?php echo e(Request::routeIs('incubacoes.*') ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-temperature-high"></i>
                    <p>Incubação</p>
                </a>
            </li>
            <li class="nav-item <?php echo e(Request::routeIs('mortes.*') ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(Request::routeIs('mortes.*') ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-skull-crossbones"></i>
                    <p>
                        Mortes
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('mortes.index')); ?>" class="nav-link <?php echo e(Request::routeIs('mortes.index') ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Listar Mortes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('mortes.create')); ?>" class="nav-link <?php echo e(Request::routeIs('mortes.create') ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Registrar Morte</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- Menu Suínos -->
    <li class="nav-item <?php echo e(Request::routeIs('suinos.*') ? 'menu-open' : ''); ?>">
        <a href="#" class="nav-link <?php echo e(Request::routeIs('suinos.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-piggy-bank"></i>
            <p>
                Suínos
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="<?php echo e(route('suinos.index')); ?>" class="nav-link <?php echo e(Request::routeIs('suinos.index') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Listar Suínos</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-header">FINANCEIRO</li>

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
                <a href="<?php echo e(route('financeiro.vendas.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.vendas.index') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Listar Vendas</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('financeiro.vendas.create')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.vendas.create') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Registrar Venda</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('financeiro.categorias.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.categorias.*') ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-tags"></i>
                    <p>Categorias</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('financeiro.reservas.index')); ?>" class="nav-link <?php echo e(Request::routeIs('financeiro.reservas.*') ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-calendar-alt"></i>
                    <p>Reservas</p>
                </a>
            </li>
        </ul>
    </li>
</ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<?php /**PATH /home/keykira/Downloads/laravel/laravel-1/resources/views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>