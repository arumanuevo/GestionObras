@extends('layouts.app')

@section('styles')
@parent
<style>
    .entrega-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        border-bottom: 1px solid #dee2e6;
    }

    .entrega-body {
        padding: 20px;
    }

    .entrega-footer {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 0 0 8px 8px;
        border-top: 1px solid #dee2e6;
    }

    .destinatario-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 6px;
        background-color: #f8f9fa;
    }

    .destinatario-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    .archivo-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 6px;
        background-color: #f8f9fa;
    }

    .archivo-icon {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        margin-right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .badge-prioridad {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    .badge-estado {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    /* Estilo para el rol del destinatario */
    .rol-badge {
        font-size: 0.75rem;
        padding: 0.2em 0.4em;
        border-radius: 0.25rem;
        margin-left: 5px;
    }

    /* Estilos para los diferentes roles */
    .rol-inspector {
        background-color: #28a745;
        color: white;
    }

    .rol-asistente {
        background-color: #007bff;
        color: white;
    }

    .rol-jefe-proyecto {
        background-color: #6f42c1;
        color: white;
    }

    .rol-especialista {
        background-color: #fd7e14;
        color: white;
    }

    .rol-contratista {
        background-color: #20c997;
        color: white;
    }

    .rol-sin-definir {
        background-color: #6c757d;
        color: white;
    }

    /* Estilo para el contenedor de información del destinatario */
    .destinatario-info {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    /* Estilo para el estado de recepción */
    .estado-recepcion {
        margin-left: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Entrega al Contratista #EC-{{ str_pad($entrega->numero, 4, '0', STR_PAD_LEFT) }}</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.entregas-contratista.bandeja', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver al listado
                            </a>
                            @if($entrega->creador_id == auth()->id() && $entrega->estado == 'Emitida')
                            <!-- Botón de edición comentado como en el original -->
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="entrega-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-0">
                                            <i class="fas fa-truck-loading mr-2"></i>
                                            {{ $entrega->asunto }}
                                        </h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <span class="badge badge-prioridad
                                                @if($entrega->prioridad == 'Urgente') badge-danger
                                                @elseif($entrega->prioridad == 'Alta') badge-warning
                                                @else badge-secondary @endif mr-2">
                                                {{ $entrega->prioridad }}
                                            </span>

                                            <span class="badge badge-estado
                                                @if($entrega->estado == 'Recibida') badge-success
                                                @else badge-info @endif">
                                                <i class="fas
                                                    @if($entrega->estado == 'Emitida') fa-paper-plane
                                                    @elseif($entrega->estado == 'Recibida') fa-check-circle
                                                    @else fa-question-circle @endif
                                                    mr-1"></i>
                                                {{ $entrega->estado }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">NÚMERO DE ENTREGA</small>
                                        <p class="mb-0">EC-{{ str_pad($entrega->numero, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">FECHA DE EMISIÓN</small>
                                        <p class="mb-0">{{ \Carbon\Carbon::parse($entrega->fecha)->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">TIPO DE ENTREGA</small>
                                        <p class="mb-0">
                                            <i class="fas fa-box-open mr-1"></i> {{ $entrega->tipo_entrega }}
                                        </p>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <small class="text-muted">PLAZO DE RECEPCIÓN</small>
                                        <p class="mb-0">{{ $entrega->plazo_recepcion }} días</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">OBRA</small>
                                        <p class="mb-0">{{ $obra->nombre }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">REMITENTE</small>
                                        <p class="mb-0">{{ $entrega->creador->name ?? 'Desconocido' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="entrega-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card mb-4">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Descripción de la Entrega</h5>
                                            </div>
                                            <div class="card-body">
                                                <div style="min-height: 200px; border: 1px solid #eee; padding: 15px; border-radius: 5px; background-color: #f8f9fa; white-space: pre-wrap;">
                                                    {!! nl2br(e($entrega->descripcion)) !!}
                                                </div>
                                            </div>
                                        </div>

                                        @if($entrega->archivos->count() > 0)
                                        <div class="card mb-4">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Archivos Adjuntos ({{ $entrega->archivos->count() }})</h5>
                                            </div>
                                            <div class="card-body">
                                                @foreach($entrega->archivos as $archivo)
                                                <div class="archivo-item">
                                                    <div class="archivo-icon
                                                        @if(str_starts_with($archivo->tipo, 'image/')) bg-primary
                                                        @elseif(str_starts_with($archivo->tipo, 'application/pdf')) bg-danger
                                                        @elseif(str_starts_with($archivo->tipo, 'application/msword') || str_starts_with($archivo->tipo, 'application/vnd.openxmlformats-officedocument.wordprocessingml')) bg-info
                                                        @elseif(str_starts_with($archivo->tipo, 'application/vnd.ms-excel') || str_starts_with($archivo->tipo, 'application/vnd.openxmlformats-officedocument.spreadsheetml')) bg-success
                                                        @else bg-secondary @endif">
                                                        @if(str_starts_with($archivo->tipo, 'image/'))
                                                            <i class="fas fa-file-image"></i>
                                                        @elseif(str_starts_with($archivo->tipo, 'application/pdf'))
                                                            <i class="fas fa-file-pdf"></i>
                                                        @elseif(str_starts_with($archivo->tipo, 'application/msword') || str_starts_with($archivo->tipo, 'application/vnd.openxmlformats-officedocument.wordprocessingml'))
                                                            <i class="fas fa-file-word"></i>
                                                        @elseif(str_starts_with($archivo->tipo, 'application/vnd.ms-excel') || str_starts_with($archivo->tipo, 'application/vnd.openxmlformats-officedocument.spreadsheetml'))
                                                            <i class="fas fa-file-excel"></i>
                                                        @else
                                                            <i class="fas fa-file-alt"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $archivo->nombre_original }}</h6>
                                                        <small class="text-muted">
                                                            {{ round($archivo->tamano / 1024, 2) }} KB |
                                                            <a href="{{ asset('storage/' . $archivo->ruta) }}" target="_blank" class="text-primary">
                                                                <i class="fas fa-download mr-1"></i> Descargar
                                                            </a>
                                                        </small>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card mb-4">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Destinatarios ({{ $entrega->destinatarios->count() }})</h5>
                                            </div>
                                            <div class="card-body">
                                                @forelse($entrega->destinatarios as $destinatario)
                                                @php
                                                    // Obtener el rol del usuario en esta obra
                                                    $rol = null;
                                                    $pivot = $destinatario->pivot;

                                                    // Verificar si hay información del rol en el pivot
                                                    if (isset($pivot->rol_id) && $pivot->rol_id) {
                                                        $rol = \App\Models\RoleObra::find($pivot->rol_id);
                                                    } else {
                                                        // Si no está en el pivot, buscar en la relación obra-usuario
                                                        $obraUsuario = $obra->usuarios->find($destinatario->id);
                                                        if ($obraUsuario && $obraUsuario->pivot && $obraUsuario->pivot->rol_id) {
                                                            $rol = \App\Models\RoleObra::find($obraUsuario->pivot->rol_id);
                                                        }
                                                    }

                                                    // Determinar la clase CSS según el rol
                                                    $rolClass = 'rol-badge rol-sin-definir';
                                                    if ($rol) {
                                                        switch ($rol->nombre) {
                                                            case 'Inspector Principal':
                                                                $rolClass = 'rol-badge rol-inspector';
                                                                break;
                                                            case 'Asistente Inspección':
                                                                $rolClass = 'rol-badge rol-asistente';
                                                                break;
                                                            case 'Jefe de Proyecto':
                                                                $rolClass = 'rol-badge rol-jefe-proyecto';
                                                                break;
                                                            case 'Especialista':
                                                                $rolClass = 'rol-badge rol-especialista';
                                                                break;
                                                            case 'Jefe de Obra':
                                                            case 'Asistente Contratista':
                                                                $rolClass = 'rol-badge rol-contratista';
                                                                break;
                                                            default:
                                                                $rolClass = 'rol-badge rol-sin-definir';
                                                        }
                                                    }
                                                @endphp
                                                <div class="destinatario-item">
                                                    <div class="destinatario-avatar
                                                        @if($destinatario->pivot->recibida) bg-success
                                                        @else bg-secondary @endif">
                                                        {{ strtoupper(substr($destinatario->name, 0, 1)) }}
                                                    </div>
                                                    <div class="destinatario-info">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-0">{{ $destinatario->name }}</h6>
                                                                @if($rol)
                                                                    <span class="{{ $rolClass }}">
                                                                        {{ $rol->nombre }}
                                                                    </span>
                                                                @else
                                                                    <span class="rol-badge rol-sin-definir">
                                                                        Sin rol definido
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="estado-recepcion">
                                                                @if($destinatario->pivot->recibida)
                                                                    <span class="badge badge-success">
                                                                        <i class="fas fa-check-circle mr-1"></i> Recibida
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-warning">
                                                                        <i class="fas fa-clock mr-1"></i> Pendiente
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                                    No hay destinatarios asignados
                                                </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Información Adicional</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <small class="text-muted">FECHA DE CREACIÓN</small>
                                                    <p class="mb-0">
                                                        {{ \Carbon\Carbon::parse($entrega->created_at)->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>

                                                <div class="mb-3">
                                                    <small class="text-muted">ÚLTIMA ACTUALIZACIÓN</small>
                                                    <p class="mb-0">
                                                        {{ \Carbon\Carbon::parse($entrega->updated_at)->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>

                                                @if($entrega->estado == 'Recibida')
                                                <div class="mb-3">
                                                    <small class="text-muted">FECHA DE RECEPCIÓN</small>
                                                    <p class="mb-0">
                                                        {{ $entrega->fecha_recepcion ? \Carbon\Carbon::parse($entrega->fecha_recepcion)->format('d/m/Y H:i') : 'No registrada' }}
                                                    </p>
                                                </div>
                                                @endif

                                                @if($entrega->estado == 'Recibida')
                                                <div class="alert alert-success mt-3">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    Esta entrega ha sido recibida por todos los destinatarios.
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="entrega-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            Entrega al Contratista #EC-{{ str_pad($entrega->numero, 4, '0', STR_PAD_LEFT) }} |
                                            Obra: {{ $obra->nombre }} |
                                            {{ \Carbon\Carbon::parse($entrega->created_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="{{ route('obras.entregas-contratista.bandeja', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i> Volver
                                        </a>

                                        @if($entrega->creador_id == auth()->id() && $entrega->estado == 'Emitida')
                                        <!-- Botón de edición comentado como en el original -->
                                        @endif

                                        @if($entrega->destinatarios->contains(auth()->id()) && $entrega->estado == 'Emitida')
                                        <form action="{{ route('obras.entregas-contratista.recibir', [$obra->id, $entrega->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-check mr-1"></i> Marcar como recibida
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Mostrar notificación de éxito si existe
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
});
</script>
@endsection