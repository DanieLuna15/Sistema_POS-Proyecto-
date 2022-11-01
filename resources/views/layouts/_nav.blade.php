<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="nav-link">
                <div class="profile-image">
                    <img src="{{asset('melody/images/faces/face16.jpg')}}" alt="image" />
                </div>
                <div class="profile-name">
                    <p class="name">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="designation">
                        Super Admin
                    </p>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('home')}}">
                <i class="fa fa-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#page-layouts" aria-expanded="false"
                aria-controls="page-layouts">
                <i class="fas fa-archive menu-icon"></i>
                    <span class="menu-title">Inventario</span>
                <i class="menu-arrow"></i>
            </a>

            <div class="collapse" id="page-layouts">
                <ul class="nav flex-column sub-menu">
                    @can('products.index')
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('products.index')}}">
                            <i class="fas fa-boxes menu-icon "></i>
                            <span class="menu-title">Productos</span>
                        </a>
                    </li>
                    @endcan
                    @can('categories.index')
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('categories.index')}}">
                            <i class="fas fa-tags menu-icon"></i>
                            <span class="menu-title">Categorías</span>
                        </a>
                    </li>
                    @endcan
                    @can('brands.index')
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('brands.index')}}">
                            <i class="fas fa-tasks menu-icon"></i>
                            <span class="menu-title">Marcas</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
        <li class="nav-item ">
            <a class="nav-link" href="{{route('providers.index')}}">
                <i class="fas fa-shipping-fast menu-icon"></i>
                <span class="menu-title">Proveedores</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route('clients.index')}}">
                <i class="fas fa-users menu-icon"></i>
                <span class="menu-title">Clientes</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route('purchases.index')}}">
                <i class="fas fa-cart-plus menu-icon"></i>
                <span class="menu-title">Compras</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route('sales.index')}}">
                <i class="fas fa-shopping-cart menu-icon"></i>
                <span class="menu-title">Ventas</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route('analytics.index')}}">
                <i class="fas fa-chart-line menu-icon"></i>
                <span class="menu-title">Analítica</span>
            </a>
        </li>

        {{--<li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#page-layouts1" aria-expanded="false"
                aria-controls="page-layouts1">
                <i class="fas fa-archive menu-icon"></i>
                    <span class="menu-title">Analítica</span>
                <i class="menu-arrow"></i>
            </a>

            <div class="collapse" id="page-layouts1">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('home')}}">
                            <i class="fas fa-boxes menu-icon"></i>
                            <span class="menu-title">Pronóstico de demanda</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fa fa-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>--}}
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#reportesc" aria-expanded="false"
                aria-controls="reportesc">
                <i class="fas fa-file menu-icon"></i>
                    <span class="menu-title">Reportes de Compras</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="reportesc">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('reportscm.daycm')}}">
                            <i class="fas fa-th-list menu-icon"></i>
                            <span class="menu-title">Reportes por día</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('reportscm.datecm')}}">
                            <i class="fas fa-calendar menu-icon"></i>
                            <span class="menu-title">Reportes por Fecha</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#reportesv" aria-expanded="false"
                aria-controls="reportesv">
                <i class="fas fa-file menu-icon"></i>
                    <span class="menu-title">Reportes de Ventas</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="reportesv">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('reports.day')}}">
                            <i class="fas fa-th-list menu-icon"></i>
                            <span class="menu-title">Reportes por día</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="{{route('reports.date')}}">
                            <i class="fas fa-calendar menu-icon"></i>
                            <span class="menu-title">Reportes por Fecha</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

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
                <span class="menu-title">Roles</span>
            </a>
        </li>
        @endcan

        {{--
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#page-layouts3" aria-expanded="false"
                aria-controls="page-layouts3">
                <i class="fas fa-cogs menu-icon"></i>
                <span class="menu-title">Configuración</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="page-layouts3">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="">Empresa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Impresora</a>
                    </li>
                </ul>
            </div>
        </li>
        {{--<li class="nav-item">
            <a class="nav-link" href="https://www.youtube.com/channel/UCMWSlUcDJS00-5pmicciZ_w">
                <i class="fab fa-youtube menu-icon"></i>
                <span class="menu-title">YouTube</span>
            </a>
        </li>--}}
    </ul>
</nav>
