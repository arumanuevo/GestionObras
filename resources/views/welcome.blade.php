@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Hero section con imagen de fondo -->
    <section class="hero-section position-relative" style="min-height: 60vh; background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1605540436563-5bca919ae766?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
        background-size: cover;
        background-position: center;
        padding-top: 70px;">

        <!-- Barra de navegación superior -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" style="height: 60px; z-index: 1000;">
            <div class="container h-100">
                <a class="navbar-brand d-flex align-items-center h-100" href="#" style="font-weight: 700; color: #2c3e50; font-size: 1.3rem;">
                    <i class="fas fa-hard-hat mr-2" style="font-size: 1.5rem;"></i>
                    Gestión de Obras
                </a>

                <div class="ml-auto d-flex align-items-center h-100">
                    <a href="{{ route('login') }}" class="btn btn-outline-dark mx-2 px-3 py-1" style="border-radius: 25px; font-weight: 500; height: 40px;">
                        <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-dark px-3 py-1" style="border-radius: 25px; font-weight: 500; height: 40px;">
                        <i class="fas fa-user-plus mr-1"></i> Registrarse
                    </a>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Contenido del hero -->
        <div class="container text-center text-white py-5" style="margin-top: 60px;">
            <h1 class="display-3 font-weight-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-size: 3.5rem;">
                Gestión de Órdenes de Servicio
            </h1>
            <p class="lead mb-5" style="font-size: 1.3rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); max-width: 800px; margin: 0 auto;">
                Plataforma integral para el seguimiento y gestión de órdenes de servicio en obras civiles
            </p>
        </div>
    </section>

    <!-- Sección de características -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 bg-white shadow-sm" style="border-radius: 15px; overflow: hidden;">
                        <div class="card-body p-4 text-center d-flex flex-column align-items-center">
                            <div class="icon-container bg-white text-dark mb-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #e9ecef;">
                                <i class="fas fa-city fa-2x"></i>
                            </div>
                            <h4 class="card-title mb-3" style="color: #2c3e50; font-weight: 600;">Gestión de Obras</h4>
                            <p class="card-text text-muted text-center px-3">
                                Seguimiento en tiempo real de todas las órdenes de servicio en tus proyectos de ingeniería civil.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 bg-white shadow-sm" style="border-radius: 15px; overflow: hidden;">
                        <div class="card-body p-4 text-center d-flex flex-column align-items-center">
                            <div class="icon-container bg-white text-dark mb-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #e9ecef;">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                            <h4 class="card-title mb-3" style="color: #2c3e50; font-weight: 600;">Documentación</h4>
                            <p class="card-text text-muted text-center px-3">
                                Centraliza toda la documentación técnica, planos y certificaciones en un solo lugar.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 bg-white shadow-sm" style="border-radius: 15px; overflow: hidden;">
                        <div class="card-body p-4 text-center d-flex flex-column align-items-center">
                            <div class="icon-container bg-white text-dark mb-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #e9ecef;">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <h4 class="card-title mb-3" style="color: #2c3e50; font-weight: 600;">Reportes</h4>
                            <p class="card-text text-muted text-center px-3">
                                Genera reportes detallados del progreso de las obras y órdenes de servicio.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de beneficios -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-4" style="color: #2c3e50; font-weight: 700;">Beneficios de nuestro sistema</h2>
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-primary mr-3" style="color: #28a745;"></i>
                            <h5 class="mb-0" style="font-weight: 600;">Seguimiento en tiempo real</h5>
                        </div>
                        <p class="text-muted">Monitorea el estado de todas tus órdenes de servicio al instante.</p>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-primary mr-3" style="color: #28a745;"></i>
                            <h5 class="mb-0" style="font-weight: 600;">Centralización de documentación</h5>
                        </div>
                        <p class="text-muted">Todos tus documentos técnicos en un solo lugar accesible.</p>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-primary mr-3" style="color: #28a745;"></i>
                            <h5 class="mb-0" style="font-weight: 600;">Notificaciones automáticas</h5>
                        </div>
                        <p class="text-muted">Recibe alertas sobre actualizaciones y plazos importantes.</p>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-primary mr-3" style="color: #28a745;"></i>
                            <h5 class="mb-0" style="font-weight: 600;">Acceso desde cualquier dispositivo</h5>
                        </div>
                        <p class="text-muted">Gestiona tus órdenes de servicio desde tu computadora, tablet o smartphone.</p>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-primary mr-3" style="color: #28a745;"></i>
                            <h5 class="mb-0" style="font-weight: 600;">Generación de reportes personalizados</h5>
                        </div>
                        <p class="text-muted">Crea reportes detallados según tus necesidades específicas.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1605540436563-5bca919ae766?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                         alt="Obras Civiles" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de CTA -->
    <section class="py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="card border-0 bg-white shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-5 text-center">
                    <h2 class="mb-4" style="color: #2c3e50; font-weight: 700;">¿Listo para optimizar la gestión de tus órdenes de servicio?</h2>
                    <p class="lead mb-5 text-muted">Únete a nuestra plataforma y comienza a gestionar tus proyectos de ingeniería civil de manera más eficiente.</p>
                    <div class="d-flex justify-content-center flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-dark btn-lg mr-3 mb-2 px-4 py-2" style="border-radius: 25px; font-weight: 600;">
                            <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-dark btn-lg mb-2 px-4 py-2" style="border-radius: 25px; font-weight: 600;">
                            <i class="fas fa-user-plus mr-1"></i> Registrarse
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
    body {
        font-family: 'Source Sans Pro', sans-serif;
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    .hero-section {
        margin-top: 0;
    }

    .navbar.fixed-top {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
    }

    .card {
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    .shadow-lg {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .btn-dark {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }

    .btn-dark:hover {
        background-color: #1e2a38;
        border-color: #1a2532;
    }

    .text-primary {
        color: #007bff !important;
    }

    .bg-primary {
        background-color: #2c3e50 !important;
    }

    .icon-container {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 767.98px) {
        .hero-section {
            min-height: 40vh;
        }

        .display-3 {
            font-size: 2.5rem !important;
        }

        .lead {
            font-size: 1.1rem !important;
        }

        .navbar {
            height: auto !important;
            padding: 1rem 0 !important;
        }

        .btn-outline-dark, .btn-dark {
            margin-bottom: 10px;
        }
    }
</style>
@endsection




