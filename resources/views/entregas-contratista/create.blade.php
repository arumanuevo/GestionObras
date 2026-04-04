@extends('layouts.app')

@section('styles')
@parent
<style>
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
                        <h3 class="card-title mb-0">Crear Entrega al Contratista</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la obra
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obras.entregas-contratista.store', $obra->id) }}" method="POST" enctype="multipart/form-data" id="entregaContratistaForm">
                        @csrf

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-info-circle"></i>
                                Información Básica
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_entrega">Número de Entrega</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">EC-</span>
                                            </div>
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

                            <div class="form-group">
                                <label for="asunto_entrega">Asunto</label>
                                <input type="text" class="form-control" id="asunto_entrega" name="asunto" value="{{ old('asunto') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="descripcion_entrega">Descripción</label>
                                <textarea class="form-control" id="descripcion_entrega" name="descripcion" rows="4" required>{{ old('descripcion') }}</textarea>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-users"></i>
                                Destinatarios: Contratistas
                            </div>

                            <div class="alert alert-info mb-3">
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
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="otro_tipo_entrega_group" style="display: none;">
                                        <label for="otro_tipo_entrega">Especificar otro tipo de entrega</label>
                                        <input type="text" class="form-control" id="otro_tipo_entrega" name="otro_tipo_entrega">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="plazo_recepcion">Plazo de recepción (días)</label>
                                        <input type="number" class="form-control" id="plazo_recepcion" name="plazo_recepcion" min="1" value="7">
                                    </div>
                                </div>
                                <div class="col-md-6">
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

                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-paperclip"></i>
                                Archivos Adjuntos
                            </div>

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

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i> Enviar Entrega
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
    // Script para el formulario de Entrega al Contratista
    $('#archivos_entrega').on('change', function() {
        var fileNames = [];
        $.each(this.files, function(i, file) {
            fileNames.push(file.name);
        });
        $('.custom-file-label[for="archivos_entrega"]').html(fileNames.join(', ') || 'Seleccionar archivos');
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
    $('#entregaContratistaForm').on('submit', function(e) {
        if ($('#asunto_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese un asunto para la entrega');
            return false;
        }

        if ($('#descripcion_entrega').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese una descripción para la entrega');
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
});
</script>
@endsection