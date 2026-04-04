@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Editar Obra: {{ $obra->nombre }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obras.update', $obra->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Campos básicos de la obra -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre de la Obra <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $obra->nombre) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="En progreso" {{ old('estado', $obra->estado) == 'En progreso' ? 'selected' : '' }}>En progreso</option>
                                        <option value="Finalizada" {{ old('estado', $obra->estado) == 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                                        <option value="Suspendida" {{ old('estado', $obra->estado) == 'Suspendida' ? 'selected' : '' }}>Suspendida</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $obra->descripcion) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ubicacion">Ubicación</label>
                                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $obra->ubicacion) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha de Inicio <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $obra->fecha_inicio ? \Carbon\Carbon::parse($obra->fecha_inicio)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha de Fin (opcional)</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', $obra->fecha_fin ? \Carbon\Carbon::parse($obra->fecha_fin)->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Sección para asignar roles por áreas -->
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Asignar Roles a la Obra</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning mb-4">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Importante:</strong> Cada usuario solo puede ser asignado a UN SOLO ROL en esta obra.
                                </div>

                                <!-- Área de Equipo de Proyecto -->
                                <div class="role-area mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="area-indicator bg-primary" style="width: 4px; height: 20px; margin-right: 10px;"></div>
                                        <h6 class="mb-0">Equipo de Proyecto</h6>
                                    </div>
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="jefe_proyecto_id">Jefe de Proyecto</label>
                                                        <select class="form-control role-select" name="jefe_proyecto_id" data-role="Jefe de Proyecto">
                                                            <option value="">Seleccionar Jefe de Proyecto</option>
                                                            @foreach($usuarios as $usuario)
                                                            <option value="{{ $usuario->id }}"
                                                                    data-email="{{ $usuario->email }}"
                                                                    data-organization="{{ $usuario->organization ?? 'Sin organización' }}"
                                                                    {{ $usuariosAsignados['jefe_proyecto'] && $usuariosAsignados['jefe_proyecto']->id == $usuario->id ? 'selected' : '' }}>
                                                                {{ $usuario->name }} - {{ $usuario->email }} ({{ $usuario->organization ?? 'Sin organización' }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="especialista_id">Especialistas</label>
                                                        <select class="form-control role-select" name="especialista_id" data-role="Especialista">
                                                            <option value="">Seleccionar Especialista</option>
                                                            @foreach($usuarios as $usuario)
                                                            <option value="{{ $usuario->id }}"
                                                                    data-email="{{ $usuario->email }}"
                                                                    data-organization="{{ $usuario->organization ?? 'Sin organización' }}"
                                                                    {{ $usuariosAsignados['especialista'] && $usuariosAsignados['especialista']->id == $usuario->id ? 'selected' : '' }}>
                                                                {{ $usuario->name }} - {{ $usuario->email }} ({{ $usuario->organization ?? 'Sin organización' }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Área de Contratista -->
                                <div class="role-area mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="area-indicator bg-success" style="width: 4px; height: 20px; margin-right: 10px;"></div>
                                        <h6 class="mb-0">Contratista</h6>
                                    </div>
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="jefe_obra_id">Jefe de Obra <span class="text-danger">*</span></label>
                                                        <select class="form-control role-select" id="jefe_obra_id" name="jefe_obra_id" data-role="Jefe de Obra" required>
                                                            <option value="">Seleccionar Jefe de Obra</option>
                                                            @foreach($usuarios as $usuario)
                                                            <option value="{{ $usuario->id }}"
                                                                    data-email="{{ $usuario->email }}"
                                                                    data-organization="{{ $usuario->organization ?? 'Sin organización' }}"
                                                                    {{ ($usuariosAsignados['jefe_obra'] && $usuariosAsignados['jefe_obra']->id == $usuario->id) || ($obra->contratista_id == $usuario->id) ? 'selected' : '' }}>
                                                                {{ $usuario->name }} - {{ $usuario->email }} ({{ $usuario->organization ?? 'Sin organización' }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="asistente_contratista_id">Asistente Contratista</label>
                                                        <select class="form-control role-select" name="asistente_contratista_id" data-role="Asistente Contratista">
                                                            <option value="">Seleccionar Asistente Contratista</option>
                                                            @foreach($usuarios as $usuario)
                                                            <option value="{{ $usuario->id }}"
                                                                    data-email="{{ $usuario->email }}"
                                                                    data-organization="{{ $usuario->organization ?? 'Sin organización' }}"
                                                                    {{ $usuariosAsignados['asistente_contratista'] && $usuariosAsignados['asistente_contratista']->id == $usuario->id ? 'selected' : '' }}>
                                                                {{ $usuario->name }} - {{ $usuario->email }} ({{ $usuario->organization ?? 'Sin organización' }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Área de Inspección -->
                                <div class="role-area">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="area-indicator bg-danger" style="width: 4px; height: 20px; margin-right: 10px;"></div>
                                        <h6 class="mb-0">Inspección</h6>
                                    </div>
                                    <div class="card border-danger">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="inspector_id">Inspector Principal <span class="text-danger">*</span></label>
                                                        <select class="form-control role-select" id="inspector_id" name="inspector_id" data-role="Inspector Principal" required>
                                                            <option value="">Seleccionar Inspector Principal</option>
                                                            @foreach($usuarios as $usuario)
                                                            <option value="{{ $usuario->id }}"
                                                                    data-email="{{ $usuario->email }}"
                                                                    data-organization="{{ $usuario->organization ?? 'Sin organización' }}"
                                                                    {{ ($usuariosAsignados['inspector_principal'] && $usuariosAsignados['inspector_principal']->id == $usuario->id) || ($obra->inspector_id == $usuario->id) ? 'selected' : '' }}>
                                                                {{ $usuario->name }} - {{ $usuario->email }} ({{ $usuario->organization ?? 'Sin organización' }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="asistente_inspeccion_id">Asistente Inspección</label>
                                                        <select class="form-control role-select" name="asistente_inspeccion_id" data-role="Asistente Inspección">
                                                            <option value="">Seleccionar Asistente Inspección</option>
                                                            @foreach($usuarios as $usuario)
                                                            <option value="{{ $usuario->id }}"
                                                                    data-email="{{ $usuario->email }}"
                                                                    data-organization="{{ $usuario->organization ?? 'Sin organización' }}"
                                                                    {{ $usuariosAsignados['asistente_inspeccion'] && $usuariosAsignados['asistente_inspeccion']->id == $usuario->id ? 'selected' : '' }}>
                                                                {{ $usuario->name }} - {{ $usuario->email }} ({{ $usuario->organization ?? 'Sin organización' }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Nota:</strong>
                                    <ul class="mb-0 pl-3">
                                        <li>Los roles marcados con <span class="text-danger">*</span> son obligatorios.</li>
                                        <li>Para gestionar visualizadores, usa el botón "Gestionar Usuarios".</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('obras.usuarios', $obra->id) }}" class="btn btn-info mr-2">
                                <i class="fas fa-users-cog"></i> Gestionar Usuarios
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
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
<script>
$(document).ready(function() {
    // Obtener los usuarios ya asignados en la obra
    var assignedUsers = {};

    // Cargar los usuarios ya asignados desde los selects
    $('.role-select').each(function() {
        var select = $(this);
        var userId = select.val();
        var roleName = select.data('role');

        if (userId) {
            assignedUsers[userId] = roleName;
        }
    });

    // Función para inicializar Select2 en los selects de roles
    function initSelect2() {
        $('.role-select').each(function() {
            var select = $(this);

            // Si ya está inicializado, destruirlo primero
            if (select.hasClass('select2-hidden-accessible')) {
                select.select2('destroy');
            }

            // Inicializar Select2 con búsqueda
            select.select2({
                placeholder: select.find('option:first').text(),
                allowClear: true,
                width: '100%',
                templateResult: formatUserOption,
                templateSelection: formatUserSelection
            });
        });
    }

    // Formato para las opciones del select
    function formatUserOption(user) {
        if (user.loading) {
            return user.text;
        }

        var $option = $(
            '<div class="user-option">' +
                '<div class="user-name">' + user.text.split(' - ')[0] + '</div>' +
                '<div class="user-email">' + $(user.element).data('email') + '</div>' +
                '<div class="user-organization">' + $(user.element).data('organization') + '</div>' +
            '</div>'
        );

        // Si el usuario ya está asignado a otro rol, marcarlo como deshabilitado
        if (assignedUsers[user.id] && assignedUsers[user.id] !== $(user.element).closest('select').data('role')) {
            $option.addClass('disabled-option');
            $option.css('color', '#aaa');
            $option.prepend('<i class="fas fa-ban mr-2"></i>');
            $option.append('<div class="small text-muted">Asignado como: ' + assignedUsers[user.id] + '</div>');
        }

        return $option;
    }

    // Formato para la selección
    function formatUserSelection(user) {
        if (!user.id) {
            return user.text;
        }

        var $selection = $(
            '<div class="user-selection">' +
                '<span class="user-name">' + user.text.split(' - ')[0] + '</span>' +
            '</div>'
        );

        return $selection;
    }

    // Función para actualizar todos los selects
    function updateAllSelects() {
        $('.role-select').each(function() {
            var select = $(this);
            var currentRole = select.data('role');
            var currentValue = select.val();

            // Actualizar las opciones
            select.find('option').each(function() {
                var option = $(this);
                var userId = option.val();

                if (!userId) return; // Saltar la opción vacía

                // Si el usuario está asignado a otro rol, deshabilitarlo
                if (assignedUsers[userId] && assignedUsers[userId] !== currentRole) {
                    option.prop('disabled', true);
                } else {
                    option.prop('disabled', false);
                }
            });

            // Refrescar Select2
            select.trigger('change.select2');
        });
    }

    // Evento para cuando se selecciona un usuario en cualquier select
    $('.role-select').on('change', function() {
        var select = $(this);
        var userId = select.val();
        var roleName = select.data('role');
        var previousUserId = select.data('previous-value');

        // Limpiar el registro del usuario anterior si lo había
        if (previousUserId) {
            delete assignedUsers[previousUserId];
        }

        // Guardar el valor anterior para la próxima vez
        select.data('previous-value', userId);

        // Si se seleccionó un usuario, registrarlo
        if (userId) {
            // Verificar si el usuario ya está asignado a otro rol
            if (assignedUsers[userId] && assignedUsers[userId] !== roleName) {
                // Mostrar alerta de que el usuario ya está asignado
                toastr.warning('El usuario ya está asignado como ' + assignedUsers[userId] + '.');

                // Revertir la selección
                select.val(previousUserId).trigger('change');

                // Refrescar todos los selects
                updateAllSelects();
                return;
            }

            // Asignar el usuario al rol actual
            assignedUsers[userId] = roleName;
        } else {
            // Si se deseleccionó, eliminar el registro
            if (previousUserId) {
                delete assignedUsers[previousUserId];
            }
        }

        // Actualizar todos los selects
        updateAllSelects();
    });

    // Validación de fechas
    $('#fecha_inicio').on('change', function() {
        var fechaInicio = $(this).val();
        if (fechaInicio) {
            $('#fecha_fin').attr('min', fechaInicio);

            // Si la fecha de fin ya tenía un valor y es anterior a la nueva fecha de inicio, limpiarla
            var fechaFin = $('#fecha_fin').val();
            if (fechaFin && fechaFin < fechaInicio) {
                $('#fecha_fin').val('');
            }
        }
    });

    // Validación al enviar el formulario
    $('form').on('submit', function(e) {
        var fechaInicio = $('#fecha_inicio').val();
        var fechaFin = $('#fecha_fin').val();

        if (fechaFin && fechaInicio && fechaFin < fechaInicio) {
            e.preventDefault();
            toastr.error('La fecha de fin no puede ser anterior a la fecha de inicio.');
            $('#fecha_fin').val('');
            return false;
        }

        // Validar que los roles obligatorios estén asignados
        var requiredRoles = [
            {name: 'jefe_obra_id', label: 'Jefe de Obra'},
            {name: 'inspector_id', label: 'Inspector Principal'}
        ];

        var missingRoles = [];

        requiredRoles.forEach(function(role) {
            if (!$('select[name="' + role.name + '"]').val()) {
                missingRoles.push(role.label);
            }
        });

        if (missingRoles.length > 0) {
            e.preventDefault();
            toastr.error('Debes asignar los siguientes roles obligatorios: ' + missingRoles.join(', '));
            return false;
        }
    });

    // Inicializar al cargar la página
    initSelect2();
    updateAllSelects();

    // Configurar toastr para mostrar notificaciones
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Configurar Select2 para que no muestre opciones deshabilitadas
    $.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
        $('.role-select').each(function() {
            $(this).select2({
                matcher: function(params, data) {
                    // Si la opción está deshabilitada, no mostrarla en los resultados
                    if ($(data.element).is(':disabled')) {
                        return null;
                    }
                    return oldMatcher(params, data);
                }
            });
        });
    });
});
</script>

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Estilos adicionales -->
<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 6px 12px;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f8f9fa;
    }

    .select2-container--default .select2-results__option {
        padding: 6px 12px;
    }

    .select2-container--default .select2-results__option.disabled-option {
        display: none;
    }

    .user-option {
        padding: 6px;
    }

    .user-name {
        font-weight: bold;
        font-size: 0.9rem;
    }

    .user-email {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .user-organization {
        font-size: 0.75rem;
        color: #6c757d;
        font-style: italic;
    }

    .user-selection {
        display: flex;
        align-items: center;
    }

    .user-selection .user-name {
        font-weight: normal;
    }

    /* Estilo para los selects */
    .form-group select {
        height: 38px;
        padding: 6px 12px;
    }

    /* Estilos para las áreas de roles */
    .role-area {
        border-left: 3px solid #dee2e6;
        padding-left: 15px;
        margin-bottom: 20px;
    }

    .area-indicator {
        border-radius: 2px;
    }

    .card.border-primary {
        border-left: 3px solid #007bff !important;
    }

    .card.border-success {
        border-left: 3px solid #28a745 !important;
    }

    .card.border-danger {
        border-left: 3px solid #dc3545 !important;
    }

    .card {
        border-radius: 0.25rem;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    /* Estilo para opciones deshabilitadas */
    .select2-results__option[aria-disabled="true"] {
        display: none !important;
    }
</style>
@endsection