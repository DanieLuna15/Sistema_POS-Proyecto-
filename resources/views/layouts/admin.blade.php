<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | {{ $activeCompany->name ?? 'Sistema SaaS POS' }}</title>
    {!! Html::style('melody/vendors/iconfonts/font-awesome/css/all.min.css') !!}
    {!! Html::style('melody/vendors/css/vendor.bundle.base.css') !!}
    {!! Html::style('melody/vendors/css/vendor.bundle.addons.css') !!}
    {!! Html::style('melody/css/style.css') !!}
    <style>
        .branch-selector-pill {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff !important;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .branch-selector-pill:hover {
            background: rgba(255, 255, 255, 0.24);
            text-decoration: none;
        }
        .cash-status-pill {
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none !important;
        }
        .cash-open {
            background: #dcfce7;
            color: #166534 !important;
            border: 1px solid #86efac;
        }
        .cash-closed {
            background: #fef3c7;
            color: #92400e !important;
            border: 1px solid #fde68a;
        }
    </style>
    @yield('styles')
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
</head>

<body class="sidebar-fixed sidebar-dark">
    <div class="container-scroller">
        <!-- NAVBAR SUPERIOR -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row default-layout-navbar">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center px-2">
                <a class="navbar-brand brand-logo d-flex align-items-center justify-content-center" href="{{route('home')}}" style="color: #fff; font-weight: 800; font-size: 15px; text-decoration: none;">
                    @if(!empty($activeCompany->logo) && file_exists(public_path('image/'.$activeCompany->logo)))
                        <img src="{{asset('image/'.$activeCompany->logo)}}" alt="logo" style="max-height: 36px; width: auto; margin-right: 8px; border-radius: 4px;" />
                    @else
                        <i class="fas fa-layer-group text-warning mr-2" style="font-size: 20px;"></i>
                    @endif
                    <span class="text-truncate" style="max-width: 155px;">{{ $activeCompany->name ?? 'SAA-POS' }}</span>
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{route('home')}}">
                    <i class="fas fa-layer-group text-warning" style="font-size: 22px;"></i>
                </a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-stretch">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="fas fa-bars"></span>
                </button>

                <!-- SELECTOR MULTI-SUCURSAL Y ESTADO DE CAJA EN VIVO -->
                <ul class="navbar-nav mr-auto d-none d-md-flex align-items-center ml-2">
                    <li class="nav-item dropdown mr-2">
                        <a class="branch-selector-pill dropdown-toggle" href="#" id="branchDropdown" data-toggle="dropdown" aria-expanded="false" title="Sucursal Activa">
                            <i class="fas fa-store-alt text-warning"></i>
                            <span>{{ $activeBranch->name ?? 'Casa Matriz' }}</span>
                            <span class="badge badge-light text-dark ml-1" style="font-size: 10px;">{{ $activeCompany->business_type ?? 'GENERAL' }}</span>
                        </a>
                        <div class="dropdown-menu navbar-dropdown shadow" aria-labelledby="branchDropdown" style="min-width: 260px;">
                            <h6 class="dropdown-header text-dark font-weight-bold">
                                <i class="fas fa-building mr-1 text-primary"></i> Cambiar Sucursal Activa
                            </h6>
                            @if(isset($allBranches) && $allBranches->count() > 0)
                                @foreach($allBranches as $b)
                                    <form action="{{ route('branches.switch') }}" method="POST" class="m-0">
                                        @csrf
                                        <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center {{ (isset($activeBranch) && $activeBranch->id == $b->id) ? 'bg-light font-weight-bold text-primary' : '' }}">
                                            <span>
                                                <i class="fas fa-map-marker-alt mr-2 {{ (isset($activeBranch) && $activeBranch->id == $b->id) ? 'text-primary' : 'text-muted' }}"></i>
                                                {{ $b->name }}
                                            </span>
                                            @if(isset($activeBranch) && $activeBranch->id == $b->id)
                                                <span class="badge badge-primary badge-pill">Activa</span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            @endif
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-primary font-weight-bold" href="{{ route('company.index') }}">
                                <i class="fas fa-cogs mr-2"></i> Configurar Mi Negocio / Sucursales
                            </a>
                            @if(Auth::user()->is_superadmin || Auth::id() === 1)
                            <a class="dropdown-item text-warning font-weight-bold bg-dark" href="{{ route('saas.tenants.index') }}">
                                <i class="fas fa-cloud mr-2 text-warning"></i> Panel SuperAdmin (Empresas SaaS)
                            </a>
                            @endif
                        </div>
                    </li>

                    @if($activeCompany->enable_cash_register ?? true)
                    <li class="nav-item">
                        @if(isset($openCashRegister) && $openCashRegister)
                            <a href="{{ route('cash_registers.index') }}" class="cash-status-pill cash-open" title="Ver Turno de Caja Abierto">
                                <i class="fas fa-lock-open"></i>
                                Caja Abierta: {{ $activeCompany->currency_symbol ?? 'Bs.' }} {{ number_format($openCashRegister->expected_amount, 2) }}
                            </a>
                        @else
                            <a href="{{ route('cash_registers.index') }}" class="cash-status-pill cash-closed" title="Haga clic para Abrir Caja">
                                <i class="fas fa-lock"></i>
                                Caja Cerrada (Abrir Turno)
                            </a>
                        @endif
                    </li>
                    @endif
                </ul>

                <!-- ACCESOS RÁPIDOS DERECHA -->
                <ul class="navbar-nav navbar-nav-right">
                    @yield('create')

                    @if(($activeCompany->business_type ?? '') === 'RESTAURANTE')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('restaurant.index')}}" title="Salón de Mesas y Comandas">
                            <button type="button" class="btn btn-danger btn-rounded btn-icon font-weight-bold">
                                <i class="fas fa-utensils"></i>
                            </button>
                        </a>
                    </li>
                    @endif

                    @can('sales.create')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('sales.pos')}}" title="Caja Rápida POS Táctil">
                            <button type="button" class="btn btn-warning btn-rounded btn-icon font-weight-bold text-dark">
                                <i class="fas fa-cash-register"></i>
                            </button>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('sales.create')}}" title="Nueva Venta">
                            <button type="button" class="btn btn-info btn-rounded btn-icon"><i class="fas fa-shopping-cart"></i></button>
                        </a>
                    </li>
                    @endcan

                    @if($activeCompany->enable_quotations ?? true)
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('quotations.create')}}" title="Nueva Cotización / Proforma">
                            <button type="button" class="btn btn-primary btn-rounded btn-icon"><i class="fas fa-file-contract"></i></button>
                        </a>
                    </li>
                    @endif

                    @can('purchases.create')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('purchases.create')}}" title="Nueva Compra / Abastecimiento">
                            <button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="fas fa-cart-plus"></i></button>
                        </a>
                    </li>
                    @endcan

                    <li class="nav-item nav-profile dropdown" align="center">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            <button type="button" class="btn btn-social-icon btn-linkedin btn-rounded">
                                <i class="fas fa-user"></i>
                            </button>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                            <div class="dropdown-header text-center">
                                <strong>{{ Auth::user()->name }}</strong><br>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('company.index') }}">
                                <i class="fas fa-cogs text-primary"></i>
                                Empresa y Sucursales
                            </a>
                            <a class="dropdown-item" href="{{ route('cash_registers.index') }}">
                                <i class="fas fa-cash-register text-success"></i>
                                Control de Caja
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-power-off text-danger"></i>
                                Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                    @yield('options')
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="fas fa-bars"></span>
                </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            @yield('preference')
            @include('layouts._nav')
            <div class="main-panel">
                @yield('content')
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            <strong>{{ $activeCompany->name ?? 'Sistema SaaS Multi-Uso' }}</strong> &bull; Sucursal: {{ $activeBranch->name ?? 'Casa Matriz' }} &bull; Moneda: {{ $activeCompany->currency_symbol ?? 'Bs.' }}
                        </span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center text-muted">
                            Rubro: <span class="badge badge-outline-primary">{{ $activeCompany->business_type ?? 'GENERAL' }}</span>
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    {!! Html::script('melody/vendors/js/vendor.bundle.base.js') !!}
    {!! Html::script('melody/vendors/js/vendor.bundle.addons.js') !!}
    {!! Html::script('melody/js/off-canvas.js') !!}
    {!! Html::script('melody/js/hoverable-collapse.js') !!}
    {!! Html::script('melody/js/misc.js') !!}
    {!! Html::script('melody/js/settings.js') !!}
    {!! Html::script('melody/js/todolist.js') !!}
    {!! Html::script('js/sweetalert2.all.min.js') !!}

    @include('sweetalert::alert')
    @if($errors->any())
    <script>
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                type: 'error',
                title: 'Verifique los datos',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        }
    </script>
    @endif
    @yield('scripts')
</body>
</html>
