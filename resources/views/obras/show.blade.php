@extends('layouts.app')
@section('styles')
    @parent
    <link href="{{ asset('css/herramientas.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Obra: {{ $obra->nombre }}</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.index', ['obra' => $obra->id]) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Selección de Obras
                            </a>

                            @can('update', $obra)
                            <a href="{{ route('obras.edit', $obra->id) }}" class="btn btn-sm btn-outline-warning ml-2">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            @endcan
                            @can('gestionarUsuarios', $obra)
                            <a href="{{ route('obras.usuarios', $obra->id) }}" class="btn btn-sm btn-outline-info ml-2">
                                <i class="fas fa-users-cog"></i> Gestionar Usuarios
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">

                <!-- Sección de herramientas para crear documentos -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Herramientas de Documentación</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $user = auth()->user();
                                    $asignadoAObra = $obra->usuarios->contains($user->id);
                                    $tieneRolAdecuado = false;
                                    $rolEnObra = null;
                                    $esInspector = false;
                                    $esJefeObra = false;
                                    $esAsistenteObra = false;
                                    $esJefeProyecto = false;
                                    $esEspecialista = false;

                                    // Obtener información del rol del usuario en esta obra
                                    if ($asignadoAObra) {
                                        $pivot = $obra->usuarios->find($user->id)->pivot;
                                        if ($pivot->rol_id) {
                                            $rolObra = \App\Models\RoleObra::find($pivot->rol_id);
                                            $tieneRolAdecuado = $rolObra && in_array($rolObra->nombre, ['Jefe de Obra', 'Asistente Contratista']);
                                            $esInspector = $rolObra && in_array($rolObra->nombre, ['Inspector Principal', 'Asistente Inspección']);
                                            $esJefeObra = $rolObra && in_array($rolObra->nombre, ['Jefe de Obra']);
                                            $esAsistenteObra = $rolObra && in_array($rolObra->nombre, ['Asistente Contratista']);
                                            $esJefeProyecto = $rolObra && in_array($rolObra->nombre, ['Jefe de Proyecto']);
                                            $esEspecialista = $rolObra && in_array($rolObra->nombre, ['Especialista']);
                                            $rolEnObra = $rolObra ? $rolObra->nombre : 'No asignado';
                                        }
                                    }

                                    // Obtener los inspectores de la obra
                                    $inspectorPrincipal = $obra->usuarios->first(function($usuario) {
                                        if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
                                        $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
                                        return $rol && $rol->nombre == 'Inspector Principal';
                                    });

                                    $asistenteInspeccion = $obra->usuarios->first(function($usuario) {
                                        if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
                                        $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
                                        return $rol && $rol->nombre == 'Asistente Inspección';
                                    });

                                    // Obtener los miembros del equipo de proyecto
                                    $equipoProyecto = $obra->usuarios->filter(function($usuario) {
                                        if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
                                        $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
                                        return $rol && in_array($rol->nombre, ['Jefe de Proyecto', 'Especialista']);
                                    });

                                    // Obtener los contratistas (Jefe de Obra y Asistente Contratista)
                                    $contratistas = $obra->usuarios->filter(function($usuario) {
                                        if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
                                        $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
                                        return $rol && in_array($rol->nombre, ['Jefe de Obra', 'Asistente Contratista']);
                                    });

                                    // Contar notas no leídas para el equipo de proyecto
                                    $notasEquipoNoLeidas = 0;
                                    if ($esJefeProyecto || $esEspecialista) {
                                        $notasEquipoNoLeidas = \App\Models\NotaEquipoProyecto::where('obra_id', $obra->id)
                                            ->whereHas('destinatarios', function($query) use ($user) {
                                                $query->where('user_id', $user->id)
                                                    ->where('leida', false);
                                            })
                                            ->count();
                                    }

                                    // Contar entregas no recibidas para contratistas
                                    $entregasNoRecibidas = 0;
                                    if ($esJefeObra || $esAsistenteObra) {
                                        $entregasNoRecibidas = \App\Models\EntregaContratista::where('obra_id', $obra->id)
                                            ->whereHas('destinatarios', function($query) use ($user) {
                                                $query->where('user_id', $user->id)
                                                    ->where('recibida', false);
                                            })
                                            ->count();
                                    }
                                @endphp

                                <!-- Contenedor principal de herramientas centrado -->
                                <div class="tools-container-wrapper">
                                    <div class="tools-container">
                                        <!-- Botón para crear Nota de Pedido -->
                                        @if($user->hasRole('admin') || $tieneRolAdecuado)
                                        <div class="tool-item">
                                            <button type="button" class="tool-btn btn-primary" data-toggle="modal" data-target="#crearNotaPedidoModal" title="Crear Nota de Pedido">
                                                <div class="tool-icon">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <div class="tool-text">Nota de Pedido</div>
                                            </button>
                                        </div>
                                        @endif

                                        <!-- Botón para crear Orden de Servicio -->
                                        @if($esInspector)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.ordenes-servicio.create', $obra->id) }}" class="tool-btn btn-success" title="Crear Orden de Servicio">
                                                <div class="tool-icon">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                                <div class="tool-text">Orden de Servicio</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Botón para crear Nota al Equipo de Proyecto -->
                                        @if($esJefeObra || $esAsistenteObra)
                                        <div class="tool-item">
                                            <button type="button" class="tool-btn btn-warning" data-toggle="modal" data-target="#crearNotaEquipoProyectoModal" title="Nota a Equipo de Proyecto">
                                                <div class="tool-icon">
                                                    <i class="fas fa-paper-plane"></i>
                                                </div>
                                                <div class="tool-text">Nota a Equipo</div>
                                            </button>
                                        </div>
                                        @endif

                                        <!-- Botón para Entrega al Contratista -->
                                        @if($esJefeProyecto || $esEspecialista)
                                        <div class="tool-item">
                                            <button type="button" class="tool-btn btn-info" data-toggle="modal" data-target="#crearEntregaContratistaModal" title="Entrega al Contratista">
                                                <div class="tool-icon">
                                                    <i class="fas fa-truck-loading"></i>
                                                </div>
                                                <div class="tool-text">Entrega a Contratista</div>
                                            </button>
                                        </div>
                                        @endif

                                        <!-- Botón para ver Mis Notas al Equipo de Proyecto -->
                                        @if($esJefeObra || $esAsistenteObra)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.notas-equipo-proyecto.index', $obra->id) }}" class="tool-btn btn-warning" title="Mis Notas a Equipo">
                                                <div class="tool-icon">
                                                    <i class="fas fa-list"></i>
                                                </div>
                                                <div class="tool-text">Notas a Equipo Enviadas</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Botón para ver Mis Entregas al Contratista -->
                                        @if($esJefeProyecto || $esEspecialista)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.entregas-contratista.index', $obra->id) }}" class="tool-btn btn-info" title="Mis Entregas a Contratista">
                                                <div class="tool-icon">
                                                    <i class="fas fa-list-alt"></i>
                                                </div>
                                                <div class="tool-text">Bandeja de Entregas</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Botón para ver Mis Órdenes de Servicio -->
                                        @if($esInspector)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.mis-ordenes-servicio', $obra->id) }}" class="tool-btn btn-info" title="Mis Órdenes de Servicio">
                                                <div class="tool-icon">
                                                    <i class="fas fa-list-ol"></i>
                                                </div>
                                                <div class="tool-text">Mis Órdenes</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Botón para ver Libro de Obra -->
                                        <div class="tool-item">
                                            <a href="{{ route('libro-obra.show', $obra->id) }}" class="tool-btn btn-info" title="Ver Libro de Obra">
                                                <div class="tool-icon">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div class="tool-text">Libro de Obra</div>
                                            </a>
                                        </div>

                                        <!-- Botón para ver Notas de Pedido creadas por el usuario -->
                                        @if($user->hasRole('admin') || $tieneRolAdecuado)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.notas-pedido.index', ['obra' => $obra->id]) }}" class="tool-btn btn-warning" title="Ver Mis Notas de Pedido">
                                                <div class="tool-icon">
                                                    <i class="fas fa-list"></i>
                                                </div>
                                                <div class="tool-text">Notas de Pedido Enviadas</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Bandeja de entrada para inspectores -->
                                        @if($esInspector)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.notas-pedido.index', ['obra' => $obra->id]) }}" class="tool-btn btn-danger position-relative" title="Bandeja de Entrada">
                                                <div class="tool-icon">
                                                    <i class="fas fa-inbox"></i>
                                                    @php
                                                        $notasNoLeidas = \App\Models\Nota::where('obra_id', $obra->id)
                                                            ->where('destinatario_id', $user->id)
                                                            ->where('leida', false)
                                                            ->count();
                                                    @endphp
                                                    @if($notasNoLeidas > 0)
                                                        <span class="tool-badge">{{ $notasNoLeidas }}</span>
                                                    @endif
                                                </div>
                                                <div class="tool-text">Bandeja de Entrada</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Bandeja de órdenes de servicio para jefes de obra -->
                                        @if($tieneRolAdecuado)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.ordenes-servicio.bandeja', ['obra' => $obra->id]) }}" class="tool-btn btn-danger position-relative" title="Bandeja de Órdenes">
                                                <div class="tool-icon">
                                                    <i class="fas fa-inbox"></i>
                                                    @php
                                                        $ordenesNoLeidas = \App\Models\OrdenServicio::where('obra_id', $obra->id)
                                                            ->where('destinatario_id', $user->id)
                                                            ->where('leida', false)
                                                            ->count();
                                                    @endphp
                                                    @if($ordenesNoLeidas > 0)
                                                        <span class="tool-badge">{{ $ordenesNoLeidas }}</span>
                                                    @endif
                                                </div>
                                                <div class="tool-text">Bandeja de Órdenes</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Bandeja de entrada para notas al equipo de proyecto -->
                                        @if($esJefeProyecto || $esEspecialista)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.notas-equipo-proyecto.bandeja', $obra->id) }}" class="tool-btn btn-danger position-relative" title="Bandeja de Notas a Equipo">
                                                <div class="tool-icon">
                                                    <i class="fas fa-inbox"></i>
                                                    @if($notasEquipoNoLeidas > 0)
                                                        <span class="tool-badge">{{ $notasEquipoNoLeidas }}</span>
                                                    @endif
                                                </div>
                                                <div class="tool-text">Bandeja Notas de Equipo</div>
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Bandeja de entregas para contratistas -->
                                        @if($esJefeObra || $esAsistenteObra)
                                        <div class="tool-item">
                                            <a href="{{ route('obras.entregas-contratista.bandeja', $obra->id) }}" class="tool-btn btn-danger position-relative" title="Bandeja de Entregas">
                                                <div class="tool-icon">
                                                    <i class="fas fa-inbox"></i>
                                                    @if($entregasNoRecibidas > 0)
                                                        <span class="tool-badge">{{ $entregasNoRecibidas }}</span>
                                                    @endif
                                                </div>
                                                <div class="tool-text">Bandeja de Entregas</div>
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Estilos CSS para los botones de herramientas -->
                   
                    <!--fin de estilos por las dudas deba insertarse aca de nuevo-->

                    <!-- Resto de la vista se mantiene igual -->
                    <!-- Sección de detalles de la obra -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Descripción:</label>
                                <p>{{ $obra->descripcion ?? 'Sin descripción' }}</p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Ubicación:</label>
                                <p>{{ $obra->ubicacion ?? 'Sin ubicación' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Fechas:</label>
                                <p>
                                    <i class="fas fa-calendar-alt"></i> Inicio:
                                    @if($obra->fecha_inicio)
                                        {{ \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') }}
                                    @else
                                        Sin fecha de inicio
                                    @endif
                                    |
                                    @if($obra->fecha_fin)
                                        <i class="fas fa-calendar-check"></i> Fin: {{ \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') }}
                                    @else
                                        <span class="badge badge-warning">Sin fecha de finalización</span>
                                    @endif
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Estado:</label>
                                <p>
                                    <span class="badge
                                        @if($obra->estado == 'En progreso') badge-info
                                        @elseif($obra->estado == 'Finalizada') badge-success
                                        @elseif($obra->estado == 'Suspendida') badge-warning
                                        @else badge-secondary @endif">
                                        {{ $obra->estado }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Contratista:</label>
                                <div class="d-flex align-items-center">
                                    @if($obra->contratista && $obra->contratista->profile_photo_path)
                                    <img src="{{ asset('storage/' . $obra->contratista->profile_photo_path) }}" class="img-circle elevation-2 mr-2" alt="{{ $obra->contratista->name }}" width="40">
                                    @elseif($obra->contratista)
                                    <div class="mr-2 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($obra->contratista->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        @if($obra->contratista)
                                        <p class="mb-0">{{ $obra->contratista->name }}</p>
                                        <small class="text-muted">{{ $obra->contratista->email }}</small>
                                        @else
                                        <p class="mb-0">No asignado</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Inspector:</label>
                                <div class="d-flex align-items-center">
                                    @if($obra->inspector && $obra->inspector->profile_photo_path)
                                    <img 
                                        src="{{ asset('storage/' . $obra->inspector->profile_photo_path) }}" 
                                        class="img-circle elevation-2 mr-2"
                                        alt="{{ $obra->inspector->name }}"
                                        style="width: 40px; height: 40px; object-fit: cover;"
                                    >
                                    @elseif($obra->inspector)
                                    <div class="mr-2 d-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($obra->inspector->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        @if($obra->inspector)
                                        <p class="mb-0">{{ $obra->inspector->name }}</p>
                                        <small class="text-muted">{{ $obra->inspector->email }}</small>
                                        @else
                                        <p class="mb-0">No asignado</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   <!-- Sección de usuarios de la obra -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <h5 class="card-title mb-0" style="font-size: 1.2rem;">Usuarios de la Obra</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" style="font-size: 0.85rem;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="text-align: center; vertical-align: middle;">Nombre</th>
                                                    <th style="text-align: center; vertical-align: middle;">Email</th>
                                                    <th style="text-align: center; vertical-align: middle;">Rol</th>
                                                    <th style="text-align: center; vertical-align: middle;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($obra->usuarios as $usuario)
                                            <tr>
                                                <td style="text-align: left; vertical-align: middle;">{{ $usuario->name }}</td>
                                                <td style="text-align: center; vertical-align: middle;">{{ $usuario->email }}</td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    @php
                                                        $obraUsuarioRol = $usuario->pivot;
                                                        $rol = \App\Models\RoleObra::find($obraUsuarioRol->rol_id);
                                                    @endphp
                                                    @if($rol)
                                                        <span class="badge badge-info" style="font-size: 0.8rem;">
                                                            {{ $rol->nombre }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary" style="font-size: 0.8rem;">
                                                            Sin rol asignado
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    @can('gestionarUsuarios', $obra)
                                                    <form action="{{ route('obras.usuarios.remove', [$obra->id, $usuario->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover usuario" style="font-size: 0.75rem;">
                                                            <i class="fas fa-user-minus"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center" style="font-size: 0.9rem;">No hay usuarios asignados a esta obra.</td>
                                            </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @can('gestionarUsuarios', $obra)
                                    <div class="mt-3 text-right">
                                        <a href="{{ route('obras.usuarios', $obra->id) }}" class="btn btn-sm btn-outline-primary" style="font-size: 0.8rem;">
                                            <i class="fas fa-user-plus"></i> Asignar más usuarios
                                        </a>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bandeja de entrada para inspectores -->
                    @if($esInspector)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <h5 class="card-title mb-0">Notas de Pedido Recibidas</h5>
                                        <div>
                                            @php
                                                $notasNoLeidasEnSeccion = \App\Models\Nota::where('obra_id', $obra->id)
                                                    ->where('destinatario_id', $user->id)
                                                    ->where('leida', false)
                                                    ->count();
                                            @endphp
                                            @if($notasNoLeidasEnSeccion > 0)
                                                <span class="badge bg-danger">
                                                    {{ $notasNoLeidasEnSeccion }} nuevas
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $notasRecibidas = \App\Models\Nota::where('obra_id', $obra->id)
                                            ->where('destinatario_id', $user->id)
                                            ->with(['creador', 'destinatario'])
                                            ->orderBy('leida', 'asc') // Mostrar primero las no leídas
                                            ->orderBy('created_at', 'desc')
                                            ->get();
                                    @endphp

                                    @if($notasRecibidas->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Número</th>
                                                    <th>Fecha</th>
                                                    <th>Tema</th>
                                                    <th>Remitente</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($notasRecibidas as $nota)
                                                <tr class="{{ !$nota->leida ? 'table-active font-weight-bold' : '' }}">
                                                    <td>NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}</td>
                                                    <td>
                                                        {{ Str::limit($nota->Tema, 30) }}
                                                        @if(!$nota->leida)
                                                            <span class="badge badge-danger ml-1">Nuevo</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $nota->creador->name ?? 'Desconocido' }}</td>
                                                    <td>
                                                        <span class="badge
                                                            @if($nota->Estado == 'Pendiente de Firma') badge-warning
                                                            @elseif($nota->Estado == 'Firmada') badge-success
                                                            @elseif($nota->Estado == 'Rechazada') badge-danger
                                                            @else badge-secondary @endif">
                                                            {{ $nota->Estado }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('obras.notas-pedido.show', [$obra->id, $nota->id]) }}" class="btn btn-sm btn-outline-primary" title="Ver nota">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($nota->Estado == 'Pendiente de Firma')
                                                        <a href="{{ route('obras.notas-pedido.firmar', [$obra->id, $nota->id]) }}" class="btn btn-sm btn-outline-success" title="Firmar nota">
                                                            <i class="fas fa-signature"></i>
                                                        </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info">
                                        No hay notas de pedido recibidas.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Bandeja de órdenes de servicio para jefes de obra -->
                    @if($tieneRolAdecuado)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <h5 class="card-title mb-0" style="font-size: 1.2rem;">Órdenes de Servicio Nuevas</h5>
                                        <div>
                                            @php
                                                $ordenesNoLeidasEnSeccion = \App\Models\OrdenServicio::where('obra_id', $obra->id)
                                                    ->where('destinatario_id', $user->id)
                                                    ->where('leida', false)
                                                    ->count();
                                            @endphp
                                            @if($ordenesNoLeidasEnSeccion > 0)
                                                <span class="badge bg-danger" style="font-size: 0.8rem;">
                                                    {{ $ordenesNoLeidasEnSeccion }} nuevas
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $ordenesRecibidas = \App\Models\OrdenServicio::where('obra_id', $obra->id)
                                            ->where('destinatario_id', $user->id)
                                            ->where('leida', false) // Solo mostrar las no leídas
                                            ->with(['creador', 'destinatario'])
                                            ->orderBy('created_at', 'desc')
                                            ->take(5) // Mostrar solo las 5 más recientes
                                            ->get();
                                    @endphp

                                    @if($ordenesRecibidas->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" style="font-size: 0.85rem;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="text-align: center; vertical-align: middle;">Número</th>
                                                    <th style="text-align: center; vertical-align: middle;">Tema</th>
                                                    <th style="text-align: center; vertical-align: middle;">Fecha Emisión</th>
                                                    <th style="text-align: center; vertical-align: middle;">Vencimiento</th>
                                                    <th style="text-align: center; vertical-align: middle;">Estado</th>
                                                    <th style="text-align: center; vertical-align: middle;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ordenesRecibidas as $orden)
                                                <tr class="table-active font-weight-bold">
                                                    <td style="text-align: center; vertical-align: middle;">OS-{{ str_pad($orden->Nro ?? $orden->numero, 4, '0', STR_PAD_LEFT) }}</td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        {{ Str::limit($orden->Tema ?? $orden->tema, 30) }}
                                                        <span class="badge badge-danger ml-1" style="font-size: 0.8rem;">Nuevo</span>
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y H:i') }}</td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        @if($orden->fecha_vencimiento)
                                                            {{ \Carbon\Carbon::parse($orden->fecha_vencimiento)->format('d/m/Y') }}
                                                            @php
                                                                $fechaVencimiento = \Carbon\Carbon::parse($orden->fecha_vencimiento);
                                                                $diasRestantes = $fechaVencimiento->diffInDays(\Carbon\Carbon::now());
                                                                $vencido = $fechaVencimiento->isPast();
                                                            @endphp
                                                            @if($vencido)
                                                                <br><small class="text-danger" style="font-size: 0.75rem;">(Vencido hace {{ abs(floor($diasRestantes)) }} días)</small>
                                                            @elseif($diasRestantes <= 3)
                                                                <br><small class="text-warning" style="font-size: 0.75rem;">(Vence en {{ floor($diasRestantes) }} días)</small>
                                                            @else
                                                                <br><small class="text-info" style="font-size: 0.75rem;">({{ floor($diasRestantes) }} días restantes)</small>
                                                            @endif
                                                        @else
                                                            Sin fecha
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <span class="badge
                                                            @if($orden->Estado == 'Cumplida') badge-success
                                                            @elseif($orden->Estado == 'Firmada' || $orden->Estado == 'Firmado') badge-success
                                                            @elseif($orden->Estado == 'Pendiente de Firma') badge-warning
                                                            @elseif($orden->Estado == 'Incumplida') badge-danger
                                                            @else badge-secondary @endif" style="font-size: 0.8rem;">
                                                            {{ $orden->Estado ?? 'Sin estado' }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <div class="d-flex justify-content-center">
                                                            <a href="{{ route('obras.ordenes-servicio.show', [$obra->id, $orden->id]) }}" class="btn btn-sm btn-outline-primary mr-1" title="Ver orden" style="font-size: 0.75rem;">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($orden->Estado == 'Emitida')
                                                            <form action="{{ route('obras.ordenes-servicio.cumplir', [$obra->id, $orden->id]) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como cumplida" style="font-size: 0.75rem;">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($ordenesNoLeidasEnSeccion > 5)
                                    <div class="mt-3 text-right">
                                        <a href="{{ route('obras.ordenes-servicio.bandeja', $obra->id) }}" class="btn btn-sm btn-danger">
                                            <i class="fas fa-inbox mr-1"></i> Ver todas las órdenes nuevas
                                        </a>
                                    </div>
                                    @endif
                                    @else
                                    <div class="alert alert-info" style="font-size: 0.9rem;">
                                        No hay órdenes de servicio nuevas.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Sección de eliminación de obra -->
                    <div class="d-flex justify-content-end mt-4">
                        @can('delete', $obra)
                        <form action="{{ route('obras.destroy', $obra->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta obra?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar Obra
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para crear Entrega al Contratista -->
<div class="modal fade" id="crearEntregaContratistaModal" tabindex="-1" role="dialog" aria-labelledby="crearEntregaContratistaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="crearEntregaContratistaModalLabel">Crear Entrega al Contratista</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('obras.entregas-contratista.store', $obra->id) }}" method="POST" enctype="multipart/form-data" id="entregaContratistaForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_entrega">Número de Entrega</label>
                                <div class="input-group">
                                   
                                    @php
                                        $proximoNumeroEntrega = \App\Models\EntregaContratista::where('obra_id', $obra->id)->max('numero') + 1;
                                    @endphp
                                    <input type="text" class="form-control" id="numero_entrega" value="EC-{{ str_pad($proximoNumeroEntrega, 4, '0', STR_PAD_LEFT) }}" readonly>
                                    <input type="hidden" name="numero" value="{{ $proximoNumeroEntrega }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_entrega">Fecha</label>
                                <input type="date" class="form-control" id="fecha_entrega" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Destinatarios: Contratistas -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Destinatarios: Contratistas</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Selecciona los destinatarios de esta entrega (Jefe de Obra y/o Asistente Contratista):
                            </div>

                            <div class="form-group">
                                <label>Destinatarios</label>
                                <div class="row">
                                    @forelse($contratistas as $contratista)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="destinatario_contratista_{{ $contratista->id }}" name="destinatarios[]" value="{{ $contratista->id }}" checked>
                                            <label class="custom-control-label" for="destinatario_contratista_{{ $contratista->id }}">
                                                @php
                                                    $rol = \App\Models\RoleObra::find($contratista->pivot->rol_id);
                                                @endphp
                                                {{ $contratista->name }} ({{ $rol->nombre ?? 'Sin rol' }})
                                            </label>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            No hay contratistas asignados a esta obra.
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            @if($contratistas->isEmpty())
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Advertencia:</strong> No hay contratistas asignados a esta obra.
                                Debes asignar al menos un Jefe de Obra o Asistente Contratista para poder enviar entregas.
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="asunto_entrega">Asunto</label>
                        <input type="text" class="form-control" id="asunto_entrega" name="asunto" value="{{ old('asunto') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion_entrega">Descripción</label>
                        <textarea class="form-control" id="descripcion_entrega" name="descripcion" rows="4" required>{{ old('descripcion') }}</textarea>
                    </div>

                    <!-- Sección de tipo de entrega -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Tipo de Entrega</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="tipo_entrega">Selecciona el tipo de entrega</label>
                                <select class="form-control" id="tipo_entrega" name="tipo_entrega" required>
                                    <option value="" selected disabled>Selecciona un tipo de entrega</option>
                                    <option value="Documentación">Documentación</option>
                                    <option value="Planos">Planos</option>
                                    <option value="Informes">Informes Técnicos</option>
                                    <option value="Certificados">Certificados</option>
                                    <option value="Muestras">Muestras de Materiales</option>
                                    <option value="Actas">Actas de Reunión</option>
                                    <option value="Presupuestos">Presupuestos</option>
                                    <option value="Memorias">Memorias de Cálculo</option>
                                    <option value="Otro">Otro tipo de entrega</option>
                                </select>
                            </div>

                            <div class="form-group" id="otro_tipo_entrega_group" style="display: none;">
                                <label for="otro_tipo_entrega">Especificar otro tipo de entrega</label>
                                <input type="text" class="form-control" id="otro_tipo_entrega" name="otro_tipo_entrega">
                            </div>

                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plazo_entrega">Plazo de recepción (días)</label>
                                    <input type="number" class="form-control" id="plazo_entrega" name="plazo_entrega" min="1" value="{{ old('plazo_entrega', 7) }}" required>
                                </div>
                            </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prioridad_entrega">Prioridad</label>
                                        <select class="form-control" id="prioridad_entrega" name="prioridad" required>
                                            <option value="Normal" selected>Normal</option>
                                            <option value="Alta">Alta</option>
                                            <option value="Urgente">Urgente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de archivos adjuntos -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Archivos Adjuntos</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="archivos_entrega">Adjuntar archivos (opcional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="archivos_entrega" name="archivos[]" multiple>
                                    <label class="custom-file-label" for="archivos_entrega">Seleccionar archivos</label>
                                </div>
                                <small class="form-text text-muted">
                                    Puedes adjuntar múltiples archivos (PDF, imágenes, documentos).
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal para crear Nota de Pedido -->
@php
use App\Models\Nota;
$proximoNumero = Nota::where('obra_id', $obra->id)->where('Tipo', 'NP')->max('Nro') + 1;
@endphp
<div class="modal fade" id="crearNotaPedidoModal" tabindex="-1" role="dialog" aria-labelledby="crearNotaPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="crearNotaPedidoModalLabel">Crear Nueva Nota de Pedido</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('obras.notas-pedido.store', $obra->id) }}" method="POST" enctype="multipart/form-data" id="notaPedidoForm">
                @csrf
                <div class="modal-body">
                    <!-- Campos básicos -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Nro">Número de Nota de Pedido</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="Nro" value="NP-{{ str_pad($proximoNumero, 4, '0', STR_PAD_LEFT) }}" readonly>
                                    <input type="hidden" name="Nro" value="{{ $proximoNumero }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de destinatarios -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Destinatarios de esta Nota de Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Esta nota de pedido se enviará automáticamente a los siguientes inspectores de la obra:
                            </div>

                            <div class="row">
                                <!-- Inspector Principal -->
                                <div class="col-md-6">
                                    <div class="card border-primary mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">Inspector Principal</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($inspectorPrincipal)
                                                <div class="d-flex align-items-center">
                                                    @if($inspectorPrincipal->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $inspectorPrincipal->profile_photo_path) }}"
                                                             class="img-circle elevation-2 mr-3"
                                                             alt="{{ $inspectorPrincipal->name }}"
                                                             style="width: 50px; height: 50px;">
                                                    @else
                                                        <div class="mr-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                                             style="width: 50px; height: 50px; font-size: 20px;">
                                                            {{ strtoupper(substr($inspectorPrincipal->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $inspectorPrincipal->name }}</h6>
                                                        <small class="text-muted">{{ $inspectorPrincipal->email }}</small>
                                                        <br>
                                                        <small class="text-primary">Rol: Inspector Principal</small>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="destinatarios[]" value="{{ $inspectorPrincipal->id }}">
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                                    No hay Inspector Principal asignado a esta obra
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Asistente de Inspección -->
                                <div class="col-md-6">
                                    <div class="card border-success mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">Asistente de Inspección</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($asistenteInspeccion)
                                                <div class="d-flex align-items-center">
                                                    @if($asistenteInspeccion->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $asistenteInspeccion->profile_photo_path) }}"
                                                             class="img-circle elevation-2 mr-3"
                                                             alt="{{ $asistenteInspeccion->name }}"
                                                             style="width: 50px; height: 50px;">
                                                    @else
                                                        <div class="mr-3 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                                             style="width: 50px; height: 50px; font-size: 20px;">
                                                            {{ strtoupper(substr($asistenteInspeccion->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $asistenteInspeccion->name }}</h6>
                                                        <small class="text-muted">{{ $asistenteInspeccion->email }}</small>
                                                        <br>
                                                        <small class="text-success">Rol: Asistente de Inspección</small>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="destinatarios[]" value="{{ $asistenteInspeccion->id }}">
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                                    No hay Asistente de Inspección asignado a esta obra
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(!$inspectorPrincipal || !$asistenteInspeccion)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Advertencia:</strong> No todos los destinatarios necesarios están asignados a esta obra.
                                    La nota de pedido solo se enviará a los inspectores disponibles.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Campos principales -->
                    <div class="form-group">
                        <label for="Tema">Tema</label>
                        <input type="text" class="form-control" id="Tema" name="Tema" value="{{ old('Tema') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="texto">Descripción</label>
                        <textarea class="form-control" id="texto" name="texto" rows="4">{{ old('texto') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="Observaciones">Observaciones</label>
                        <textarea class="form-control" id="Observaciones" name="Observaciones" rows="3">{{ old('Observaciones') }}</textarea>
                    </div>

                    <!-- Sección de PDF con análisis de IA -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Documento PDF y Análisis de IA</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="pdf">PDF Asociado (opcional)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="pdf" name="pdf" accept=".pdf">
                                        <label class="custom-file-label" for="pdf">Seleccionar archivo PDF</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Suba un documento PDF para adjuntar a la nota de pedido.
                                    <strong>Si sube un PDF, debe generar un resumen con IA antes de guardar.</strong>
                                </small>
                                <div id="pdfInfo" class="mt-2" style="display: none;">
                                    <p class="mb-1"><strong>Archivo seleccionado:</strong> <span id="pdfFilename"></span></p>
                                    <div id="pdfWarning" class="alert alert-warning mt-2" style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Debe generar un resumen con IA antes de guardar la nota.
                                    </div>
                                </div>
                            </div>

                            <!-- Controles para el análisis de IA -->
                            <div id="aiControls" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="aiSummaryLength">Longitud del resumen (palabras)</label>
                                            <select class="form-control" id="aiSummaryLength">
                                                <option value="50">50 palabras</option>
                                                <option value="100" selected>100 palabras</option>
                                                <option value="200">200 palabras</option>
                                                <option value="300">300 palabras</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group d-flex align-items-end">
                                            <button type="button" class="btn btn-primary btn-block" id="generateAISummary">
                                                <i class="fas fa-robot mr-1"></i> Generar Resumen con IA
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Indicador de procesamiento -->
                                <div id="aiProcessing" style="display: none;">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="spinner-border spinner-border-sm text-primary mr-2" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <span>Generando resumen con IA...</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                    </div>
                                </div>

                                <!-- Resultado del análisis de IA -->
                                <div id="aiResult" style="display: none;">
                                    <div class="form-group mt-3">
                                        <label for="aiGeneratedSummary">Resumen generado por IA:</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <div id="aiGeneratedSummary" style="min-height: 150px; border: 1px solid #eee; padding: 10px; border-radius: 5px; background-color: #f8f9fa; white-space: pre-wrap;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="useAISummary">
                                            <label class="custom-control-label" for="useAISummary">
                                                Agregar resumen de IA a la descripción
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mensajes de error -->
                                <div id="aiError" class="alert alert-danger" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos ocultos -->
                    <input type="hidden" id="resumen_ai" name="resumen_ai" value="{{ old('resumen_ai') }}">
                    <input type="hidden" id="texto_pdf" name="texto_pdf" value="{{ old('texto_pdf') }}">
                    <input type="hidden" id="generate_ai_summary" name="generate_ai_summary" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="guardarNotaBtn">
                        <i class="fas fa-save"></i> Guardar Nota de Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal para crear Nota al Equipo de Proyecto -->
<div class="modal fade" id="crearNotaEquipoProyectoModal" tabindex="-1" role="dialog" aria-labelledby="crearNotaEquipoProyectoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="crearNotaEquipoProyectoModalLabel">Crear Nota para Equipo de Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('obras.notas-equipo-proyecto.store', $obra->id) }}" method="POST" enctype="multipart/form-data" id="notaEquipoProyectoForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_nota_equipo">Número de Nota</label>
                                <div class="input-group">
                                    
                                    @php
                                        $proximoNumeroEquipo = \App\Models\NotaEquipoProyecto::where('obra_id', $obra->id)->max('numero') + 1;
                                    @endphp
                                    <input type="text" class="form-control" id="numero_nota_equipo" value="NE-{{ str_pad($proximoNumeroEquipo, 4, '0', STR_PAD_LEFT) }}" readonly>
                                    <input type="hidden" name="numero" value="{{ $proximoNumeroEquipo }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_equipo">Fecha</label>
                                <input type="date" class="form-control" id="fecha_equipo" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Destinatarios: Equipo de Proyecto -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Destinatarios: Equipo de Proyecto</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Selecciona los destinatarios de esta nota para el equipo de proyecto:
                            </div>

                            <div class="form-group">
                                <label>Destinatarios</label>
                                <div class="row">
                                    @forelse($equipoProyecto as $miembro)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="destinatario_{{ $miembro->id }}" name="destinatarios[]" value="{{ $miembro->id }}" checked>
                                            <label class="custom-control-label" for="destinatario_{{ $miembro->id }}">
                                                @php
                                                    $rol = \App\Models\RoleObra::find($miembro->pivot->rol_id);
                                                @endphp
                                                {{ $miembro->name }} ({{ $rol->nombre ?? 'Sin rol' }})
                                            </label>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            No hay miembros del equipo de proyecto asignados a esta obra.
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            @if($equipoProyecto->isEmpty())
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Advertencia:</strong> No hay miembros del equipo de proyecto asignados a esta obra.
                                Debes asignar al menos un Jefe de Proyecto o Especialista para poder enviar notas.
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tema_equipo">Asunto</label>
                        <input type="text" class="form-control" id="tema_equipo" name="tema" value="{{ old('tema') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="contenido_equipo">Contenido</label>
                        <textarea class="form-control" id="contenido_equipo" name="contenido" rows="6" required>{{ old('contenido') }}</textarea>
                    </div>

                    <!-- Sección de archivos adjuntos -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Archivos Adjuntos</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="archivos_equipo">Adjuntar archivos (opcional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="archivos_equipo" name="archivos[]" multiple>
                                    <label class="custom-file-label" for="archivos_equipo">Seleccionar archivos</label>
                                </div>
                                <small class="form-text text-muted">
                                    Puedes adjuntar múltiples archivos (PDF, imágenes, documentos).
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de tipo de entrega -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Tipo de Entrega</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="tipo_entrega">Selecciona el tipo de entrega</label>
                                <select class="form-control" id="tipo_entrega" name="tipo_entrega" required>
                                    <option value="" selected disabled>Selecciona un tipo de entrega</option>
                                    <option value="Entrega de Documentación">Entrega de Documentación</option>
                                    <option value="Entrega de Planos">Entrega de Planos</option>
                                    <option value="Entrega de Informes">Entrega de Informes</option>
                                    <option value="Entrega de Certificados">Entrega de Certificados</option>
                                    <option value="Entrega de Muestras">Entrega de Muestras</option>
                                    <option value="Entrega de Actas">Entrega de Actas</option>
                                    <option value="Entrega de Presupuestos">Entrega de Presupuestos</option>
                                    <option value="Entrega de Memorias">Entrega de Memorias de Cálculo</option>
                                    <option value="Otro">Otro tipo de entrega</option>
                                </select>
                            </div>

                            <div class="form-group" id="otro_tipo_entrega_group" style="display: none;">
                                <label for="otro_tipo_entrega">Especificar otro tipo de entrega</label>
                                <input type="text" class="form-control" id="otro_tipo_entrega" name="otro_tipo_entrega">
                            </div>

                            <div class="form-group">
                                <label for="plazo_entrega">Plazo de entrega (días)</label>
                                <input type="number" class="form-control" id="plazo_entrega" name="plazo_entrega" min="1" value="7">
                            </div>

                            <div class="form-group">
                                <label for="prioridad">Prioridad</label>
                                <select class="form-control" id="prioridad" name="prioridad" required>
                                    <option value="Normal" selected>Normal</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar Nota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Variables globales
    let pdfFile = null;
    let pdfText = '';
    let aiSummary = '';
    let pdfSelected = false;
    let originalText = '';
    let archivosSeleccionados = [];

    // Guardar el texto original al cargar la página
    originalText = $('#texto').val();

    $('#archivos_entrega').on('change', function(e) {
        var fileNames = [];
        $.each(this.files, function(i, file) {
            fileNames.push(file.name);
        });

        // Actualizar el label del input de archivos
        if (fileNames.length > 0) {
            $('.custom-file-label[for="archivos_entrega"]').html(fileNames.join(', ') || 'Seleccionar archivos');
        } else {
            $('.custom-file-label[for="archivos_entrega"]').html('Seleccionar archivos');
        }

        // Almacenar los archivos seleccionados
        archivosSeleccionados = Array.from(this.files);
    });

    // Mostrar/ocultar campo para otro tipo de entrega
    $('#tipo_entrega').on('change', function() {
        if ($(this).val() === 'Otro') {
            $('#otro_tipo_entrega_group').show();
            $('#otro_tipo_entrega').prop('required', true);
        } else {
            $('#otro_tipo_entrega_group').hide();
            $('#otro_tipo_entrega').prop('required', false);
        }
    });

    // Validación del formulario de entrega
    $('#entregaContratistaForm').on('submit', function(e) {
        if ($('#asunto_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese un asunto para la entrega');
            return false;
        }

        if ($('#descripcion_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese la descripción de la entrega');
            return false;
        }

        if ($('#tipo_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor seleccione un tipo de entrega');
            return false;
        }

        if ($('#tipo_entrega').val() === 'Otro' && $('#otro_tipo_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor especifique el otro tipo de entrega');
            return false;
        }

        if ($('input[name="destinatarios[]"]:checked').length === 0) {
            e.preventDefault();
            toastr.error('Por favor seleccione al menos un destinatario');
            return false;
        }

        return true;
    });
    // Actualizar el nombre del archivo seleccionado
    $('#pdf').on('change', function(e) {
        if (e.target.files.length > 0) {
            pdfFile = e.target.files[0];
            pdfSelected = true;
            const fileName = pdfFile.name;
            $('.custom-file-label').html(fileName);
            $('#pdfFilename').text(fileName);
            $('#pdfInfo').show();
            $('#pdfWarning').show();
            $('#aiControls').show();
            $('#aiResult').hide();
            $('#aiError').hide();
        } else {
            pdfFile = null;
            pdfSelected = false;
            $('.custom-file-label').html('Seleccionar archivo PDF');
            $('#pdfInfo').hide();
            $('#pdfWarning').hide();
            $('#aiControls').hide();
            $('#aiResult').hide();
            $('#aiError').hide();
        }
    });

    // Botón para generar resumen con IA
    $('#generateAISummary').on('click', function() {
        if (!pdfFile) {
            toastr.warning('Por favor seleccione un archivo PDF primero');
            return;
        }

        // Mostrar indicador de procesamiento
        $('#aiProcessing').show();
        $('#aiError').hide();
        $('#aiResult').hide();

        // Crear un FormData para enviar el archivo
        let formData = new FormData();
        formData.append('pdf', pdfFile);

        // Enviar el archivo para extraer texto
        $.ajax({
            url: "{{ route('obras.notas-pedido.extraer-texto', ['obra' => $obra->id]) }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.texto_pdf) {
                    pdfText = response.texto_pdf;
                    // Guardar el texto del PDF en el campo oculto
                    $('#texto_pdf').val(pdfText);
                    // Generar resumen con IA
                    generateAISummary(pdfText);
                } else {
                    $('#aiProcessing').hide();
                    $('#aiError').text('Error al analizar el documento: ' + (response.message || 'Error desconocido')).show();
                }
            },
            error: function(xhr) {
                $('#aiProcessing').hide();
                $('#aiError').text('Error al analizar el documento: ' + (xhr.responseJSON?.message || xhr.statusText)).show();
            }
        });
    });



    // Función para generar resumen con IA
    function generateAISummary(textoPDF) {
        const palabras = $('#aiSummaryLength').val();

        $.ajax({
            url: "{{ route('obras.notas-pedido.generar-resumen', ['obra' => $obra->id, 'nota' => 0]) }}",
            method: 'POST',
            data: {
                texto_pdf: textoPDF,
                cantidad_palabras: palabras,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#aiProcessing').hide();

                if (response.success && response.resumen) {
                    aiSummary = response.resumen;
                    $('#aiGeneratedSummary').text(aiSummary);
                    $('#aiResult').show();
                    $('#pdfWarning').hide();
                } else {
                    $('#aiError').text('Error al generar resumen: ' + (response.message || 'Error desconocido')).show();
                }
            },
            error: function(xhr) {
                $('#aiProcessing').hide();
                $('#aiError').text('Error al generar resumen: ' + (xhr.responseJSON?.message || xhr.statusText)).show();
            }
        });
    }

    // Usar el resumen de IA como complemento
    $('#useAISummary').on('change', function() {
        if ($(this).is(':checked') && aiSummary) {
            // Solo afectar al texto visible, el resumen ya está guardado en el campo oculto
            let currentText = $('#texto').val();
            if (currentText) {
                $('#texto').val(currentText + "\n\n---\n\nResumen de IA:\n" + aiSummary);
            } else {
                $('#texto').val("Resumen de IA:\n" + aiSummary);
            }
        } else {
            // Restaurar el texto original, pero mantener el resumen en el campo oculto
            $('#texto').val(originalText);
        }
    });

    // Cambiar longitud del resumen
    $('#aiSummaryLength').on('change', function() {
        if (pdfText) {
            generateAISummary(pdfText);
        }
    });

    // Validación del formulario antes de enviar
    $('#notaPedidoForm').on('submit', function(e) {
        if ($('#Tema').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese un tema para la nota');
            return false;
        }

        // Validación para PDF: si se seleccionó un PDF, debe generarse el resumen con IA
        if (pdfSelected && !aiSummary) {
            e.preventDefault();
            toastr.error('Debe generar un resumen con IA antes de guardar la nota cuando adjunta un PDF');
            return false;
        }

        // Asegurar que el resumen de IA se envíe siempre si existe
        if (aiSummary) {
            $('#resumen_ai').val(aiSummary);
        }

        return true;
    });

    $('#archivos_equipo').on('change', function() {
        var fileNames = [];
        $.each(this.files, function(i, file) {
            fileNames.push(file.name);
        });
        $('.custom-file-label[for="archivos_equipo"]').html(fileNames.join(', ') || 'Seleccionar archivos');
    });

    // Mostrar/ocultar campo para otro tipo de entrega
    $('#tipo_entrega').on('change', function() {
        if ($(this).val() === 'Otro') {
            $('#otro_tipo_entrega_group').show();
            $('#otro_tipo_entrega').prop('required', true);
        } else {
            $('#otro_tipo_entrega_group').hide();
            $('#otro_tipo_entrega').prop('required', false);
        }
    });

    // Validación del formulario
    $('#notaEquipoProyectoForm').on('submit', function(e) {
        if ($('#tema_equipo').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese un asunto para la nota');
            return false;
        }

        if ($('#contenido_equipo').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese el contenido de la nota');
            return false;
        }

        if ($('#tipo_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor seleccione un tipo de entrega');
            return false;
        }

        if ($('#tipo_entrega').val() === 'Otro' && $('#otro_tipo_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor especifique el otro tipo de entrega');
            return false;
        }

        if ($('input[name="destinatarios[]"]:checked').length === 0) {
            e.preventDefault();
            toastr.error('Por favor seleccione al menos un destinatario');
            return false;
        }

        return true;
    });
    // Mostrar notificación de éxito si existe
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
});

</script>
@endsection