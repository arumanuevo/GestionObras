<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestión de Obras') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <!-- AdminLTE CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <!-- Bootstrap CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    @yield('styles')
    <style>
        /* Estilos para las imágenes de perfil */
        .user-image {
            object-fit: cover;
            object-position: center;
            background-color: #f8f9fa;
            border-radius: 50%;
        }

        /* Estilo específico para la imagen en el navbar */
        .navbar .user-image {
            width: 28px;
            height: 28px;
            border: 1px solid #dee2e6;
            object-fit: cover;
        }

        /* Contenedor para la imagen en el navbar */
        .user-image-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            vertical-align: middle;
            margin-right: 8px;
        }

        /* Estilo para el dropdown menu */
        .dropdown-menu {
            min-width: 280px;
            padding: 0;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Estilo específico para el user-header con selector más específico */
        body .wrapper .main-header .navbar-nav .user-menu .dropdown-menu li.user-header {
            height: auto !important;
            min-height: 150px !important;
            padding: 1rem !important;
            text-align: center !important;
            background: linear-gradient(135deg, #007bff, #0056b3) !important;
            color: white !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .user-body {
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
        }

        .user-footer {
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
        }

        /* Estilos para los botones en el menú */
        .user-body .btn {
            font-size: 0.8rem;
            padding: 0.3rem 0.5rem;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .user-footer .btn {
            font-size: 0.8rem;
            padding: 0.3rem 0.5rem;
            flex: 1;
            margin: 0 0.25rem;
        }

        /* Estilo para el contenedor de la imagen en el header */
        .user-header-image {
            width: 60px;
            height: 60px;
            margin: 0 auto 0.5rem;
            position: relative;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid white;
            background-color: white;
        }

        .user-header-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        /* Estilos para el indicador de avatar por defecto */
        .default-avatar {
            position: relative;
            border: 1px dashed #6c757d;
        }

        .default-avatar-large {
            position: relative;
        }

        .avatar-upload-indicator {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 12px;
            height: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            border: 1px solid white;
        }

        .avatar-upload-indicator-large {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            z-index: 1;
        }

        /* Estilo para el contorno del avatar por defecto */
        .user-header .default-avatar-large {
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(0, 123, 255, 0.3);
        }

        /* Estilo para el contorno del avatar pequeño por defecto */
        .default-avatar {
            box-shadow: 0 0 0 1px rgba(108, 117, 125, 0.5);
        }

        /* Ajuste para el texto del header */
        .user-header p {
            margin-bottom: 0.2rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .user-header small {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        /* Ajuste para el dropdown toggle en el navbar */
        .user-menu .dropdown-toggle {
            display: flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
        }

        /* Ajuste para el badge de notificación */
        .user-menu .badge {
            margin-left: 0.3rem;
        }

        /* Estilo para el rol en el footer del menú */
        .user-role-footer {
            padding: 0.5rem 1rem;
            background-color: rgba(0, 0, 0, 0.03);
            text-align: center;
            font-size: 0.7rem;
            color: #6c757d;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .user-role-badge {
            display: inline-block;
            padding: 0.2rem 0.4rem;
            border-radius: 0.2rem;
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        /* Colores para los diferentes roles */
        .user-role-badge.admin {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .user-role-badge.editor {
            background-color: #fff8e1;
            color: #f57c00;
        }

        .user-role-badge.consulta {
            background-color: #e0f2f1;
            color: #00796b;
        }

        /* Estilos para el brand logo */
        .brand-link {
            transition: all 0.3s ease;
            overflow: hidden;
            white-space: nowrap;
        }

        .brand-icon {
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        /* Cuando el sidebar está retraído */
        .sidebar-mini.sidebar-collapse .brand-link {
            justify-content: center;
        }

        .sidebar-mini.sidebar-collapse .brand-text {
            display: none;
        }

        .sidebar-mini.sidebar-collapse .brand-icon {
            font-size: 1.5rem;
            margin-right: 0;
        }

        /* Ajuste para el brand text */
        .brand-text {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="{{ auth()->check() ? 'hold-transition sidebar-mini' : '' }}">
    @if(auth()->check())
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- User Dropdown Menu -->
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <div class="user-image-container">
                            <img src="{{ auth()->user()->profile_photo_url }}"
                                 class="user-image {{ !auth()->user()->profile_photo_path ? 'default-avatar' : '' }}"
                                 alt="User Image">
                        </div>
                        <span class="d-none d-md-inline" style="font-size: 0.85rem;">{{ auth()->user()->name }}</span>
                        @if(!auth()->user()->profile_completed)
                            <span class="badge badge-warning ml-1" style="font-size: 0.6rem; padding: 0.15rem 0.3rem;">!</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <!-- User image header -->
                        <li class="user-header">
                            <div class="user-header-image">
                                <img src="{{ auth()->user()->profile_photo_url }}"
                                     class="{{ !auth()->user()->profile_photo_path ? 'default-avatar-large' : '' }}"
                                     alt="User Image">
                                @if(!auth()->user()->profile_photo_path)
                                    <span class="avatar-upload-indicator-large">
                                        <i class="fas fa-camera"></i>
                                    </span>
                                @endif
                            </div>
                            <p>{{ auth()->user()->name }}</p>
                            <small>Miembro desde {{ auth()->user()->created_at->format('M Y') }}</small>
                        </li>

                        <!-- Menu Body -->
                        <li class="user-body">
                            <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-primary btn-block">
                                <i class="fas fa-user-circle mr-1"></i> Ver Perfil
                            </a>
                            @if(!auth()->user()->profile_completed)
                                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-warning btn-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Completar Perfil
                                </a>
                            @endif
                        </li>

                        <!-- Menu Footer -->
                        <li class="user-footer">
                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-cog mr-1"></i> Configuración
                            </a>
                            <a href="{{ route('logout') }}"
                               class="btn btn-sm btn-outline-danger"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>

                        <!-- User Role Footer -->
                        @if(auth()->user()->roles->isNotEmpty())
                        <li class="user-role-footer">
                            <span>Rol logeado:</span>
                            <span class="user-role-badge {{ auth()->user()->roles->first()->name }}">
                                {{ auth()->user()->roles->first()->name }}
                            </span>
                        </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ url('/home') }}" class="brand-link d-flex align-items-center justify-content-center" style="height: 56px;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-hard-hat brand-icon"></i>
                    <span class="brand-text font-weight-light ml-2">{{ config('app.name', 'Gestión de Obras') }}</span>
                </div>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.show') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Mi Perfil
                                    @if(!auth()->user()->profile_completed)
                                        <span class="badge badge-warning right">!</span>
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('notas.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Notas</p>
                            </a>
                        </li>
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>Gestión de Usuarios</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                @yield('content')
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2024-2026 <a href="#">Gestión de Obras</a>.</strong>
            Todos los derechos reservados.
        </footer>
    </div>
    <!-- ./wrapper -->
    @else
        @yield('content')
    @endif

    <!-- jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 4 JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS (CDN) -->
    @if(auth()->check())
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    @endif
    <!-- DataTables JS (CDN) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('scripts')
</body>
</html>


