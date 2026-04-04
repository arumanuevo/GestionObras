@extends('layouts.app')
@section('styles')
    @parent
    <link href="{{ asset('css/misobras.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container-fluid px-3">
    <!-- Contenedor unificado del banner y filtros -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <!-- Banner principal con fondo transparente -->
        <div class="card-header bg-gradient-primary p-0" style="background: rgba(0, 123, 255, 0.85) !important;">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white mb-1 d-flex align-items-center">
                            <i class="fas fa-hard-hat me-2"></i> Mis Obras
                        </h2>
                        <p class="text-white-70 mb-0">Gestión de proyectos de ingeniería civil</p>
                    </div>
                    <div class="card-tools">
                        @can('create', App\Models\Obra::class)
                        <a href="{{ route('obras.create') }}" class="btn btn-light shadow-sm">
                            <i class="fas fa-plus-circle me-1"></i> Nueva Obra
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de búsqueda integrados -->
<!-- Filtros de búsqueda integrados -->
<div class="card-body p-4" style="border-top: 1px solid rgba(0, 0, 0, 0.05);">
    <div class="row align-items-end g-3">
        <!-- Fila de búsqueda -->
        <div class="col-12 col-md-5">
            <div class="input-group">
                <input type="text" class="form-control" id="searchObra" placeholder="Buscar obra por nombre...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Select de estados -->
        <!--<div class="col-12 col-md-3">
            <select class="form-control select2" id="filterEstado">
                <option value="">Todos los estados</option>
                <option value="en-progreso">En progreso</option>
                <option value="finalizada">Finalizada</option>
                <option value="suspendida">Suspendida</option>
                <option value="planificada">Planificada</option>
            </select>
        </div>-->

        <!-- Botones de filtro rápido -->
        <div class="col-12 col-md-4">
            <div class="btn-group d-flex w-100" role="group" aria-label="Filtros rápidos">
                <button type="button" class="btn btn-outline-primary flex-fill py-2" data-filter="all">
                    <span class="d-none d-sm-inline"><i class="fas fa-th-large me-1"></i> Todas</span>
                    <span class="d-sm-none"><i class="fas fa-th-large"></i></span>
                </button>
                <button type="button" class="btn btn-outline-primary flex-fill py-2" data-filter="progreso">
                    <span class="d-none d-sm-inline"><i class="fas fa-spinner me-1"></i> En progreso</span>
                    <span class="d-sm-none"><i class="fas fa-spinner"></i></span>
                </button>
                <button type="button" class="btn btn-outline-primary flex-fill py-2" data-filter="suspendida">
                    <span class="d-none d-sm-inline"><i class="fas fa-pause me-1"></i> Suspendida</span>
                    <span class="d-sm-none"><i class="fas fa-pause"></i></span>
                </button>
                <button type="button" class="btn btn-outline-primary flex-fill py-2" data-filter="finalizada">
                    <span class="d-none d-sm-inline"><i class="fas fa-check-circle me-1"></i> Finalizadas</span>
                    <span class="d-sm-none"><i class="fas fa-check-circle"></i></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!-----fin filtros--->
    </div>

    <!-- Contenedor de obras -->
    <div class="row g-4" id="obrasContainer">
        @forelse($obras as $obra)
        <div class="col-md-6 col-lg-4 obra-item"
             data-estado="{{ strtolower(str_replace(' ', '-', $obra->estado)) }}"
             data-nombre="{{ strtolower($obra->nombre) }}">
            <div class="card h-100 shadow-sm border-0 obra-card">
                <!-- Encabezado de la obra -->
                <div class="card-header bg-white border-bottom-0 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hard-hat me-2 text-primary"></i>
                            <h5 class="card-title mb-0">
                                <a href="#" class="text-dark text-decoration-none fw-bold">
                                    {{ Str::limit($obra->nombre, 40) }}
                                </a>
                            </h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge
                                @if($obra->estado == 'En progreso') bg-info
                                @elseif($obra->estado == 'Finalizada') bg-success
                                @elseif($obra->estado == 'Suspendida') bg-warning
                                @else bg-secondary @endif
                                rounded-pill me-2">
                                {{ $obra->estado }}
                            </span>
                            <span class="badge bg-light text-dark">ID: {{ $obra->id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Imagen de ingeniería civil -->
                <div class="obra-image-container position-relative" style="height: 120px; background: #f8f9fa;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" preserveAspectRatio="none" class="w-100 h-100" style="opacity: 0.1;">
                        <defs>
                            <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                                <path d="M 20 0 L 0 0 0 20" fill="none" stroke="#6c757d" stroke-width="0.5" opacity="0.3"/>
                            </pattern>
                        </defs>
                        <rect width="200" height="120" fill="url(#grid)"/>
                        <path d="M20,100 L20,20 L180,20 L180,100" stroke="#6c757d" stroke-width="0.5" fill="none" opacity="0.3"/>
                        <path d="M40,100 L40,30 L160,30 L160,100" stroke="#6c757d" stroke-width="0.5" fill="none" opacity="0.3"/>
                        <path d="M60,100 L60,40 L140,40 L140,100" stroke="#6c757d" stroke-width="0.5" fill="none" opacity="0.3"/>
                        <rect x="50" y="80" width="8" height="20" fill="#6c757d" opacity="0.2"/>
                        <rect x="80" y="70" width="8" height="30" fill="#6c757d" opacity="0.2"/>
                        <rect x="110" y="60" width="8" height="40" fill="#6c757d" opacity="0.2"/>
                        <rect x="140" y="70" width="8" height="30" fill="#6c757d" opacity="0.2"/>
                    </svg>
                </div>

                <!-- Cuerpo de la tarjeta -->
                <div class="card-body p-3">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            <small class="text-muted">{{ Str::limit($obra->ubicacion, 50) }}</small>
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                            <small class="text-muted">
                                @if($obra->fecha_inicio)
                                    {{ \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') }}
                                    @if($obra->fecha_fin)
                                        - {{ \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') }}
                                    @endif
                                @else
                                    Sin fechas definidas
                                @endif
                            </small>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-muted me-2"></i>
                            <small class="text-muted">
                                {{ $obra->usuarios->count() }} participantes |
                                Contratista: {{ $obra->contratista->name ?? 'No asignado' }}
                            </small>
                        </div>
                    </div>

                <!-- Progreso de la obra -->
                @php
                    $progreso = 0;
                    if ($obra->fecha_inicio && $obra->fecha_fin) {
                        $inicio = \Carbon\Carbon::parse($obra->fecha_inicio);
                        $fin = \Carbon\Carbon::parse($obra->fecha_fin);
                        $hoy = \Carbon\Carbon::now();

                        // Calcular progreso basado en tiempo
                        $progresoTiempo = 0;
                        if ($hoy->greaterThan($fin)) {
                            $progresoTiempo = 100; // Obra terminada en fecha
                        } elseif ($hoy->between($inicio, $fin)) {
                            $diasTotales = $fin->diffInDays($inicio);
                            if ($diasTotales > 0) {
                                $diasTranscurridos = $hoy->diffInDays($inicio);
                                $progresoTiempo = min(100, ($diasTranscurridos / $diasTotales) * 100);
                            }
                        } else {
                            $progresoTiempo = 0; // Obra no ha comenzado
                        }

                        // Calcular elementos pendientes
                        $notasPendientes = \App\Models\Nota::where('obra_id', $obra->id)
                            ->where('Estado', '!=', 'Respondida con OS')
                            ->where('Estado', '!=', 'Cumplida')
                            ->count();

                        $ordenesPendientes = \App\Models\OrdenServicio::where('obra_id', $obra->id)
                            ->where('Estado', '!=', 'Cumplida')
                            ->where('Estado', '!=', 'Firmada')
                            ->count();

                        $totalPendientes = $notasPendientes + $ordenesPendientes;

                        // Calcular penalización por elementos pendientes
                        $penalizacion = 0;
                        if ($totalPendientes > 0) {
                            // Máximo 50% de penalización (5% por cada elemento pendiente hasta 10 elementos)
                            $penalizacion = min(50, $totalPendientes * 5);
                        }

                        // Calcular progreso final (70% tiempo, 30% elementos pendientes)
                        $progreso = max(0, min(100, $progresoTiempo * 0.7 - $penalizacion));
                    }
                @endphp

                @if($obra->fecha_inicio && $obra->fecha_fin)
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar
                            @if($progreso < 30) bg-danger
                            @elseif($progreso < 70) bg-warning
                            @else bg-success @endif"
                            role="progressbar"
                            style="width: {{ $progreso }}%"
                            aria-valuenow="{{ $progreso }}"
                            aria-valuemin="0"
                            aria-valuemax="100"></div>
                    </div>

                    <!-- Información detallada del progreso -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <small class="text-muted mr-2">Avance:</small>
                            <small class="font-weight-bold">{{ round($progreso) }}%</small>

                            @php
                                $notasPendientes = \App\Models\Nota::where('obra_id', $obra->id)
                                    ->where('Estado', '!=', 'Respondida con OS')
                                    ->where('Estado', '!=', 'Cumplida')
                                    ->count();

                                $ordenesPendientes = \App\Models\OrdenServicio::where('obra_id', $obra->id)
                                    ->where('Estado', '!=', 'Cumplida')
                                    ->where('Estado', '!=', 'Firmada')
                                    ->count();

                                $totalPendientes = $notasPendientes + $ordenesPendientes;
                            @endphp

                            @if($totalPendientes > 0)
                                <span class="badge badge-danger ml-2">-{{ min(50, $totalPendientes * 5) }}%</span>
                            @endif
                        </div>

                        @if($obra->fecha_fin)
                            <small class="text-muted">
                                {{ $hoy->greaterThan($fin) ? 'Finalizada' : $hoy->format('d/m/Y') }}
                            </small>
                        @endif
                    </div>

                    <!-- Detalle de elementos pendientes -->
                    @if($totalPendientes > 0)
                    <div class="alert alert-light p-2 mb-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                            <small>
                                {{ $totalPendientes }} elemento{{ $totalPendientes > 1 ? 's' : '' }} pendiente{{ $totalPendientes > 1 ? 's' : '' }}
                                @if($notasPendientes > 0)
                                    ({{ $notasPendientes }} nota{{ $notasPendientes > 1 ? 's' : '' }})
                                @endif
                                @if($ordenesPendientes > 0)
                                    {{ $notasPendientes > 0 ? ', ' : '' }}{{ $ordenesPendientes }} orden{{ $ordenesPendientes > 1 ? 'es' : '' }}
                                @endif
                                afectando el avance
                            </small>
                        </div>
                    </div>
                    @endif
                @endif

                <!-------------------fin-------------------->
                </div>

                <!-- Pie de la tarjeta con acciones -->
                <div class="card-footer bg-white border-top-0 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex">
                            @can('update', $obra)
                            <a href="{{ route('obras.edit', $obra->id) }}" class="btn btn-sm btn-outline-secondary me-2" title="Editar obra">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan

                            @can('gestionarUsuarios', $obra)
                            <a href="{{ route('obras.usuarios', $obra->id) }}" class="btn btn-sm btn-outline-info me-2" title="Gestionar usuarios">
                                <i class="fas fa-users-cog"></i>
                            </a>
                            @endcan
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-tools me-1"></i> Administrar Obra
                            </a>
                            <a href="{{ route('libro-obra.show', $obra->id) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-book me-1"></i> Libro de Obra
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4 class="mb-3">No hay obras registradas</h4>
                @can('create', App\Models\Obra::class)
                <p class="mb-0">Puedes crear una nueva obra haciendo clic en el botón "<strong>Nueva Obra</strong>" arriba.</p>
                @endcan
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($obras instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $obras->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
   
</style>
@endsection

@section('scripts')
@parent
<script>
// Funcionalidad de búsqueda
$('#searchObra').on('keyup', function() {
    const searchTerm = $(this).val().toLowerCase();
    filterObras(searchTerm, $('[data-filter].active').data('filter'));
});

// Funcionalidad de filtro rápido
$('[data-filter]').on('click', function() {
    // Remover clase active de todos los botones
    $('[data-filter]').removeClass('active');

    // Añadir clase active al botón clickeado
    $(this).addClass('active');

    // Obtener el filtro seleccionado
    const filter = $(this).data('filter');

    // Obtener el término de búsqueda actual
    const searchTerm = $('#searchObra').val().toLowerCase();

    // Aplicar el filtro
    filterObras(searchTerm, filter);
});

// Función centralizada para filtrar obras
function filterObras(searchTerm, filter) {
    $('.obra-item').each(function() {
        const obraName = $(this).data('nombre').toLowerCase();
        const obraEstado = $(this).data('estado').toLowerCase();

        // Verificar si cumple con el filtro de búsqueda
        const matchesSearch = searchTerm === '' || obraName.includes(searchTerm);

        // Verificar si cumple con el filtro de estado
        let matchesEstado = true;

        switch(filter) {
            case 'progreso':
                matchesEstado = obraEstado === 'en-progreso';
                break;
            case 'suspendida':
                matchesEstado = obraEstado === 'suspendida';
                break;
            case 'finalizada':
                matchesEstado = obraEstado === 'finalizada';
                break;
            case 'all':
            default:
                matchesEstado = true;
        }

        if (matchesSearch && matchesEstado) {
            $(this).removeClass('hidden');
        } else {
            $(this).addClass('hidden');
        }
    });
}

// Inicializar el primer botón como activo
$('[data-filter="all"]').addClass('active');
</script>
@endsection