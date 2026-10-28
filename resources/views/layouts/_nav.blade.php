<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="nav-link">
                <div class="profile-image">
                    <button type="button" class="btn btn-social-icon btn-linkedin btn-rounded"><i class="fas fa-user"></i></button>
                </div>
                <div class="profile-name">
                    <p class="name">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="designation">
                        <i class="fas fa-store-alt text-warning mr-1"></i> {{ $activeBranch->name ?? 'Casa Matriz' }}
                    </p>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route('home')}}">
                <i class="fa fa-home menu-icon"></i>
                <span class="menu-title">Dashboard General</span>
            </a>
        </li>

        @if(($activeCompany->business_type ?? '') === 'RESTAURANTE')
        <li class="nav-item">
            <a class="nav-link text-danger font-weight-bold" href="{{route('restaurant.index')}}">
                <i class="fas fa-utensils menu-icon text-danger"></i>
                <span class="menu-title">Mapa de Mesas y Salón</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-warning font-weight-bold" href="{{route('restaurant.kitchen')}}">
                <i class="fas fa-fire-alt menu-icon text-warning"></i>
                <span class="menu-title">Pantalla de Cocina (KDS)</span>
            </a>
        </li>
        @endif

        @if($activeCompany->enable_cash_register ?? true)
        <li class="nav-item">
            <a class="nav-link" href="{{route('cash_registers.index')}}">
                <i class="fas fa-vault menu-icon text-success"></i>
                <span class="menu-title">Caja y Gastos Chicos</span>
            </a>
        </li>
        @endif

        @can('sales.index')
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ventas-menu" aria-expanded="false" aria-controls="ventas-menu">
                <i class="fas fa-shopping-cart menu-icon"></i>
                <span class="menu-title">Ventas y Cotizaciones</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ventas-menu">
                <ul class="nav flex-column sub-menu">
                    @can('sales.create')
                    @if($activeCompany->enable_pos ?? true)
                    <li class="nav-item">
                        <a class="nav-link text-warning font-weight-bold" href="{{route('sales.pos')}}">
                            <i class="fas fa-cash-register menu-icon text-warning"></i>
                            <span class="menu-title">Caja Rápida (POS)</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('sales.create')}}">
                            <i class="fas fa-file-invoice-dollar menu-icon"></i>
                            <span class="menu-title">Venta Tradicional</span>
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('sales.index')}}">
                            <i class="fas fa-list menu-icon"></i>
                            <span class="menu-title">Historial de Ventas</span>
                        </a>
                    </li>
                    @if($activeCompany->enable_credits ?? true)
                    <li class="nav-item">
                        <a class="nav-link text-info" href="{{route('sales.credits')}}">
                            <i class="fas fa-hand-holding-usd menu-icon text-info"></i>
                            <span class="menu-title">Cuentas por Cobrar</span>
                        </a>
                    </li>
                    @endif
                    @if($activeCompany->enable_quotations ?? true)
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('quotations.index')}}">
                            <i class="fas fa-file-contract menu-icon text-primary"></i>
                            <span class="menu-title">Cotizaciones / Proformas</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endcan

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#inventario" aria-expanded="false" aria-controls="inventario">
                <i class="fas fa-archive menu-icon"></i>
                <span class="menu-title">Inventario y Catálogo</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="inventario">
                <ul class="nav flex-column sub-menu">
                    @can('products.index')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('products.index')}}">
                            <i class="fas fa-boxes menu-icon"></i>
                            <span class="menu-title">{{ $activeCompany->rubro_config['short_label'] ?? 'Catálogo' }}: Ítems</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning font-weight-bold" href="{{route('products.index', ['type' => 'COMBO'])}}">
                            <i class="fas fa-gifts menu-icon text-warning"></i>
                            <span class="menu-title">Combos y Promos</span>
                        </a>
                    </li>
                    @endcan
                    @if($activeCompany->enable_kardex ?? true)
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="{{route('kardex.index')}}">
                            <i class="fas fa-clipboard-list menu-icon text-warning"></i>
                            <span class="menu-title">Kardex y Ajustes</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link text-info font-weight-bold" href="{{route('transfers.index')}}">
                            <i class="fas fa-truck-loading menu-icon text-info"></i>
                            <span class="menu-title">Traspasos Sucursales</span>
                        </a>
                    </li>
                    @can('categories.index')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('categories.index')}}">
                            <i class="fas fa-tags menu-icon"></i>
                            <span class="menu-title">Categorías</span>
                        </a>
                    </li>
                    @endcan
                    @can('brands.index')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('brands.index')}}">
                            <i class="fas fa-tasks menu-icon"></i>
                            <span class="menu-title">{{ $activeCompany->rubro_config['brand_label'] ?? 'Marcas' }}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>

        @can('purchases.index')
        <li class="nav-item">
            <a class="nav-link" href="{{route('purchases.index')}}">
                <i class="fas fa-cart-plus menu-icon"></i>
                <span class="menu-title">Compras / Entradas</span>
            </a>
        </li>
        @endcan

        @can('clients.index')
        <li class="nav-item">
            <a class="nav-link" href="{{route('clients.index')}}">
                <i class="fas fa-users menu-icon"></i>
                <span class="menu-title">Clientes</span>
            </a>
        </li>
        @endcan

        @can('providers.index')
        <li class="nav-item">
            <a class="nav-link" href="{{route('providers.index')}}">
                <i class="fas fa-shipping-fast menu-icon"></i>
                <span class="menu-title">Proveedores</span>
            </a>
        </li>
        @endcan

        @can('analytics.index')
        <li class="nav-item">
            <a class="nav-link" href="{{route('analytics.index')}}">
                <i class="fas fa-chart-line menu-icon"></i>
                <span class="menu-title">Analítica / Demanda</span>
            </a>
        </li>
        @endcan

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#reportes" aria-expanded="false" aria-controls="reportes">
                <i class="fas fa-file-chart-line fa-file menu-icon"></i>
                <span class="menu-title">Reportes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="reportes">
                <ul class="nav flex-column sub-menu">
                    @can('reports.date')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('reports.date')}}">
                            <i class="fas fa-calendar menu-icon"></i>
                            <span class="menu-title">Reporte de Ventas</span>
                        </a>
                    </li>
                    @endcan
                    @can('reportscm.datecm')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('reportscm.datecm')}}">
                            <i class="fas fa-th-list menu-icon"></i>
                            <span class="menu-title">Reporte de Compras</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#config-saas" aria-expanded="false" aria-controls="config-saas">
                <i class="fas fa-cogs menu-icon text-info"></i>
                <span class="menu-title">Administración SaaS</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="config-saas">
                <ul class="nav flex-column sub-menu">
                    @if(Auth::user()->is_superadmin || Auth::id() === 1)
                    <li class="nav-item">
                        <a class="nav-link text-warning font-weight-bold" href="{{route('saas.tenants.index')}}">
                            <i class="fas fa-cloud menu-icon text-warning"></i>
                            <span class="menu-title">Empresas Clientes (SaaS)</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('company.index')}}">
                            <i class="fas fa-building menu-icon"></i>
                            <span class="menu-title">Mi Negocio y Sucursales</span>
                        </a>
                    </li>
                    @can('payment_methods.index')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('payment_methods.index')}}">
                            <i class="fas fa-money-check-alt menu-icon"></i>
                            <span class="menu-title">Formas de Pago</span>
                        </a>
                    </li>
                    @endcan
                    @can('users.index')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('users.index')}}">
                            <i class="fas fa-user-tag menu-icon"></i>
                            <span class="menu-title">Usuarios</span>
                        </a>
                    </li>
                    @endcan
                    @can('roles.index')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('roles.index')}}">
                            <i class="fas fa-user-cog menu-icon"></i>
                            <span class="menu-title">Roles y Permisos</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
    </ul>
</nav>
