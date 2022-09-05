<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="nav-link">
                <div class="profile-image">
                    <img src="{{asset('melody/images/faces/face16.jpg')}}" alt="image" />
                </div>
                <div class="profile-name">
                    <p class="name">
                        Bienvenido: Daniel
                    </p>
                    <p class="designation">
                        Super Admin
                    </p>
                </div>
            </div>
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
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            <i class="fas fa-boxes menu-icon"></i>
                            <span class="menu-title">Productos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            <i class="fas fa-tags menu-icon"></i>
                            <span class="menu-title">Categorías</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            <i class="fas fa-shipping-fast menu-icon"></i>
                            <span class="menu-title">Proveedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            <i class="fas fa-tasks menu-icon"></i>
                            <span class="menu-title">Marcas</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#sidebar-layouts" aria-expanded="false"
                aria-controls="sidebar-layouts">
                <i class="fas fa-users menu-icon"></i>
                    <span class="menu-title">Clientes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="sidebar-layouts">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            <i class="fas fa-list-ul menu-icon"></i>
                            <span class="menu-title">Ver todos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">
                            <i class="fas fa-plus-square menu-icon"></i>
                            <span class="menu-title">Añadir Nuevo</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{--
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fa fa-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>--}}
        {{--<li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#page-layouts2" aria-expanded="false"
                aria-controls="page-layouts2">
                <i class="fas fa-chart-line menu-icon"></i>
                <span class="menu-title">Reportes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="page-layouts2">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="">Reportes por día</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Reportes por fecha</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fas fa-cart-plus menu-icon"></i>
                <span class="menu-title">Compras</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fas fa-shopping-cart menu-icon"></i>
                <span class="menu-title">Ventas</span>
            </a>
        </li>--}}
        


        
        {{--<li class="nav-item">
            <a class="nav-link" href="">
                <i class="fas fa-user-tag menu-icon"></i>
                <span class="menu-title">Usuarios</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="fas fa-user-cog menu-icon"></i>
                <span class="menu-title">Roles</span>
            </a>
        </li>
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