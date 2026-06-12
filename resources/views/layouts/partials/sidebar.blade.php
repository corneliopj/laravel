<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Dashboard -->
    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    <li class="nav-header">PRODUÇÃO</li>

    <!-- Menu Aves -->
    <li class="nav-item {{ Request::routeIs(['aves.*', 'tipos_aves.*', 'variacoes.*', 'lotes.*', 'acasalamentos.*', 'posturas_ovos.*', 'incubacoes.*']) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::routeIs(['aves.*', 'tipos_aves.*', 'variacoes.*', 'lotes.*', 'acasalamentos.*', 'posturas_ovos.*', 'incubacoes.*']) ? 'active' : '' }}">
            <i class="nav-icon fas fa-feather-alt"></i>
            <p>
                Aves
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('aves.index') }}" class="nav-link {{ Request::routeIs(['aves.index', 'aves.create', 'aves.show', 'aves.edit']) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-feather"></i>
                    <p>Listar Aves</p>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('plantel.*') || Request::routeIs('movimentacoes-plantel.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::routeIs('plantel.*') || Request::routeIs('movimentacoes-plantel.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-boxes"></i>
                    <p>
                        Plantéis
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('plantel.index') }}" class="nav-link {{ Request::routeIs('plantel.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Listar Plantéis</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('plantel.create') }}" class="nav-link {{ Request::routeIs('plantel.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Novo Plantel</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('movimentacoes-plantel.index') }}" class="nav-link {{ Request::routeIs('movimentacoes-plantel.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Movimentações</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('incubacoes.index') }}" class="nav-link {{ Request::routeIs('incubacoes.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-temperature-high"></i>
                    <p>Incubação</p>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('mortes.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::routeIs('mortes.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-skull-crossbones"></i>
                    <p>
                        Mortes
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('mortes.index') }}" class="nav-link {{ Request::// routeIs('mortes.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Listar Mortes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mortes.create') }}" class="nav-link {{ Request::routeIs('mortes.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Registrar Morte</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- LINK IRMÃO: LISTAR SUÍNOS -->
    <li class="nav-item">
        <a href="{{ route('suinos.index') }}" class="nav-link {{ Request::routeIs('suinos.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-paw"></i>
            <p>Listar Suínos</p>
        </a>
    </li>

    <li class="nav-header">FINANCEIRO</li>

    <li class="nav-item {{ Request::routeIs('financeiro.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::routeIs('financeiro.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dollar-sign"></i>
            <p>
                Financeiro
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('financeiro.dashboard') }}" class="nav-link {{ Request::routeIs('financeiro.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>Dashboard Financeiro</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('financeiro.receitas.index') }}" class="nav-link {{ Request::routeIs('financeiro.receitas.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-hand-holding-usd"></i>
                    <p>Receitas</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('financeiro.despesas.index') }}" class="nav-link {{ Request::routeIs('financeiro.despesas.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-money-bill-wave"></i>
                    <p>Despesas</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('financeiro.vendas.index') }}" class="nav-link {{ Request::routeIs('financeiro.vendas.index') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Listar Vendas</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('financeiro.vendas.create') }}" class="nav-link {{ Request::routeIs('financeiro.vendas.create') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Registrar Venda</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('financeiro.categorias.index') }}" class="nav-link {{ Request::routeIs('financeiro.categorias.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tags"></i>
                    <p>Categorias</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('financeiro.reservas.index') }}" class="nav-link {{ Request::routeIs('financeiro.reservas.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-calendar-alt"></i>
                    <p>Reservas</p>
                </a>
            </li>
        </ul>
    </li>
</ul>
