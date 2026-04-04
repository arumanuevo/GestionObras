@extends('layouts.app')

@section('styles')
@parent
<style>
    /* Estilos para el formulario de edición */
    .form-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .form-section-header {
        font-weight: 500;
        margin-bottom: 15px;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }

    .form-section-header i {
        margin-right: 10px;
        color: #0d6efd;
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
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.8rem;
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
        width: 35px;
        height: 35px;
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Editar Nota al Equipo de Proyecto #NE-{{ str_pad($nota->numero, 4, '0', STR_PAD_LEFT) }}</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.notas-equipo-proyecto.show', [$obra->id, $nota->id]) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la nota
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obras.notas-equipo-proyecto.update', [$obra->id, $nota->id]) }}" method="POST" enctype="multipart/form-data" id="notaEquipoProyectoForm">
                        @csrf
                        @method('PUT')

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-info-circle"></i>
                                Información Básica
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_nota_equipo">Número de Nota</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">NE-</span>
                                            </div>
                                            <input type="text" class="form-control" id="numero_nota_equipo" value="NE-{{ str_pad($nota->numero, 4, '0', STR_PAD_LEFT) }}" readonly>
                                            <input type="hidden" name="numero" value="{{ $nota->numero }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_equipo">Fecha</label>
                                        <input type="date" class="form-control" id="fecha_equipo" name="fecha" value="{{ old('fecha', $nota->fecha->format('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tema_equipo">Asunto</label>
                                <input type="text" class="form-control" id="tema_equipo" name="tema" value="{{ old('tema', $nota->tema) }}" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-box-open"></i>
                                Tipo de Entrega
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_entrega">Selecciona el tipo de entrega</label>
                                        <select class="form-control" id="tipo_entrega" name="tipo_entrega" required>
                                            <option value="" disabled>Selecciona un tipo de entrega</option>
                                            <option value="Entrega de Documentación" {{ $nota->tipo_entrega == 'Entrega de Documentación' ? 'selected' : '' }}>Entrega de Documentación</option>
                                            <option value="Entrega de Planos" {{ $nota->tipo_entrega == 'Entrega de Planos' ? 'selected' : '' }}>Entrega de Planos</option>
                                            <option value="Entrega de Informes" {{ $nota->tipo_entrega == 'Entrega de Informes' ? 'selected' : '' }}>Entrega de Informes</option>
                                            <option value="Entrega de Certificados" {{ $nota->tipo_entrega == 'Entrega de Certificados' ? 'selected' : '' }}>Entrega de Certificados</option>
                                            <option value="Entrega de Muestras" {{ $nota->tipo_entrega == 'Entrega de Muestras' ? 'selected' : '' }}>Entrega de Muestras</option>
                                            <option value="Entrega de Actas" {{ $nota->tipo_entrega == 'Entrega de Actas' ? 'selected' : '' }}>Entrega de Actas</option>
                                            <option value="Entrega de Presupuestos" {{ $nota->tipo_entrega == 'Entrega de Presupuestos' ? 'selected' : '' }}>Entrega de Presupuestos</option>
                                            <option value="Entrega de Memorias" {{ $nota->tipo_entrega == 'Entrega de Memorias' ? 'selected' : '' }}>Entrega de Memorias de Cálculo</option>
                                            <option value="Otro" {{ $nota->tipo_entrega != 'Entrega de Documentación' &&
                                                                $nota->tipo_entrega != 'Entrega de Planos' &&
                                                                $nota->tipo_entrega != 'Entrega de Informes' &&
                                                                $nota->tipo_entrega != 'Entrega de Certificados' &&
                                                                $nota->tipo_entrega != 'Entrega de Muestras' &&
                                                                $nota->tipo_entrega != 'Entrega de Actas' &&
                                                                $nota->tipo_entrega != 'Entrega de Presupuestos' &&
                                                                $nota->tipo_entrega != 'Entrega de Memorias' ? 'selected' : '' }}>Otro tipo de entrega</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="otro_tipo_entrega_group" style="{{ $nota->tipo_entrega != 'Entrega de Documentación' &&
                                                                                            $nota->tipo_entrega != 'Entrega de Planos' &&
                                                                                            $nota->tipo_entrega != 'Entrega de Informes' &&
                                                                                            $nota->tipo_entrega != 'Entrega de Certificados' &&
                                                                                            $nota->tipo_entrega != 'Entrega de Muestras' &&
                                                                                            $nota->tipo_entrega != 'Entrega de Actas' &&
                                                                                            $nota->tipo_entrega != 'Entrega de Presupuestos' &&
                                                                                            $nota->tipo_entrega != 'Entrega de Memorias' ? '' : 'display: none;' }}">
                                        <label for="otro_tipo_entrega">Especificar otro tipo de entrega</label>
                                        <input type="text" class="form-control" id="otro_tipo_entrega" name="otro_tipo_entrega" value="{{ $nota->tipo_entrega }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="plazo_entrega">Plazo de entrega (días)</label>
                                        <input type="number" class="form-control" id="plazo_entrega" name="plazo_entrega" min="1" value="{{ old('plazo_entrega', $nota->plazo_entrega) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prioridad">Prioridad</label>
                                        <select class="form-control" id="prioridad" name="prioridad" required>
                                            <option value="Normal" {{ $nota->prioridad == 'Normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="Alta" {{ $nota->prioridad == 'Alta' ? 'selected' : '' }}>Alta</option>
                                            <option value="Urgente" {{ $nota->prioridad == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-align-left"></i>
                                Contenido de la Nota
                            </div>

                            <div class="form-group">
                                <label for="contenido_equipo">Contenido</label>
                                <textarea class="form-control" id="contenido_equipo" name="contenido" rows="8" required>{{ old('contenido', $nota->contenido) }}</textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-users"></i>
                                Destinatarios
                            </div>

                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                Selecciona los destinatarios de esta nota para el equipo de proyecto:
                            </div>

                            <div class="form-group">
                                <label>Destinatarios</label>
                                <div class="row">
                                    @forelse($equipoProyecto as $miembro)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="destinatario_{{ $miembro->id }}" name="destinatarios[]" value="{{ $miembro->id }}"
                                                {{ $nota->destinatarios->contains($miembro->id) ? 'checked' : '' }}>
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

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-paperclip"></i>
                                Archivos Adjuntos
                            </div>

                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                Archivos actualmente adjuntos a esta nota:
                            </div>

                            @if($nota->archivos->count() > 0)
                            <div class="mb-3">
                                @foreach($nota->archivos as $archivo)
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
                            @else
                            <div class="alert alert-light mb-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                No hay archivos adjuntos a esta nota.
                            </div>
                            @endif

                            <div class="form-group">
                                <label for="archivos_equipo">Adjuntar nuevos archivos (opcional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="archivos_equipo" name="archivos[]" multiple>
                                    <label class="custom-file-label" for="archivos_equipo">Seleccionar archivos</label>
                                </div>
                                <small class="form-text text-muted">
                                    Puedes adjuntar múltiples archivos (PDF, imágenes, documentos).
                                </small>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <a href="{{ route('obras.notas-equipo-proyecto.show', [$obra->id, $nota->id]) }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
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
    // Script para el formulario de edición
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

    // Validación del formulario de Nota al Equipo de Proyecto
    $('#notaEquipoProyectoForm').on('submit', function(e) {
        if ($('#tema_equipo').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese un asunto para la nota');
            return false;
        }

        if ($('input[name="destinatarios[]"]:checked').length === 0) {
            e.preventDefault();
            toastr.error('Por favor seleccione al menos un destinatario');
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

        return true;
    });

    // Mostrar notificación de éxito si existe
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
});
</script>
@endsection