@extends('layouts.app')

@section('styles')
@parent
<!-- Estilos para DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<style>
    /* Estilos personalizados para DataTables */
    .dataTables_wrapper {
        padding: 15px;
    }

    .dataTables_filter input {
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        margin-left: 0.5rem;
    }

    .dataTables_length select {
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
    }

    /* Estilo para los botones de DataTables */
    .dt-buttons {
        margin-bottom: 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    /* Botones transparentes con borde */
    .btn-dt-custom {
        background-color: rgba(255, 255, 255, 0.9) !important;
        border: 1px solid #dee2e6 !important;
        color: #495057 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        transition: all 0.2s ease !important;
        border-radius: 0.25rem !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 0.85rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .btn-dt-custom:hover {
        background-color: #f8f9fa !important;
        border-color: #adb5bd !important;
        color: #212529 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    .btn-dt-custom i {
        margin-right: 0.3rem;
    }

    /* Estilo para los badges */
    .badge-estado {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    .badge-prioridad {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }

    /* Estilo para los botones de acción */
    .btn-ver {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    /* Estilo para la paginación */
    .dataTables_paginate .paginate_button {
        padding: 0.3em 0.6em;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        margin-left: 0.2rem;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #0d6efd;
        color: white !important;
        border-color: #0d6efd;
    }

    .dataTables_paginate .paginate_button:hover:not(.current) {
        background-color: #f8f9fa;
        color: #495057;
    }

    /* Estilo para el select de longitud */
    .dataTables_length {
        margin-bottom: 15px;
    }

    /* Estilo para los destinatarios */
    .destinatario-badge {
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 5px;
        font-size: 0.8rem;
        padding: 0.25em 0.5em;
        background-color: #e9ecef;
        color: #495057;
        border-radius: 0.25rem;
    }

    /* Estilo para el tipo de entrega */
    .tipo-entrega {
        font-weight: 500;
        color: #2c3e50;
    }

    /* Estilo para el card header */
    .card-header {
        padding: 1rem 1.25rem;
    }

    /* Estilo para los iconos de archivos adjuntos */
    .archivo-icon {
        font-size: 1.2rem;
        margin-right: 5px;
    }

    /* Estilo para el estado de la nota */
    .estado-emitida {
        color: #6c757d;
    }

    .estado-firmada {
        color: #28a745;
    }

    .estado-rechazada {
        color: #dc3545;
    }

    /* Estilo para las filas según el creador */
    .nota-propia {
        background-color: rgba(173, 216, 230, 0.1);
    }

    .nota-ajena {
        background-color: rgba(240, 248, 255, 0.3);
    }

    /* Estilo para el creador de la nota */
    .creador-container {
        display: flex;
        align-items: center;
    }

    .creador-avatar {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        margin-right: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.7rem;
        font-weight: bold;
    }

    /* Estilo para los roles */
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

    /* Estilo para el badge "Tú" */
    .badge-tu {
        font-size: 0.7rem;
        padding: 0.2em 0.4em;
        background-color: #28a745;
        color: white;
        border-radius: 0.25rem;
        margin-left: 5px;
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
                        <h3 class="card-title mb-0">Notas al Equipo de Proyecto - Obra: {{ $obra->nombre }}</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la obra
                            </a>
                            @can('create', [\App\Models\NotaEquipoProyecto::class, $obra])
                            <a href="{{ route('obras.notas-equipo-proyecto.create', $obra->id) }}" class="btn btn-sm btn-primary ml-2">
                                <i class="fas fa-plus mr-1"></i> Nueva Nota
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Aquí puedes ver <strong>TODAS las notas al equipo de proyecto</strong> relacionadas con esta obra.
                        <div class="mt-2">
                            <span class="badge bg-warning me-2">
                                <i class="fas fa-paper-plane me-1"></i>
                                <span>{{ $notasEmitidas }}</span>
                            </span>
                            <span class="badge bg-success me-2">
                                <i class="fas fa-check-circle me-1"></i>
                                <span>{{ $notasFirmadas }}</span>
                            </span>
                            <span class="badge bg-danger me-2">
                                <i class="fas fa-times-circle me-1"></i>
                                <span>{{ $notasRechazadas }}</span>
                            </span>
                            <span class="badge bg-primary me-2">
                                <i class="fas fa-user me-1"></i>
                                <span>{{ $notasPropias }}</span>
                            </span>
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-users me-1"></i>
                                <span>{{ $notasAjenas }}</span>
                            </span>
                            Tienes <strong>{{ $notas->count() }}</strong> nota{{ $notas->count() != 1 ? 's' : '' }} en total.
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="notasEquipoProyectoTable" class="table table-bordered table-hover nowrap" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th data-priority="1">Número</th>
                                    <th data-priority="2">Asunto</th>
                                    <th data-priority="3">Tipo de Entrega</th>
                                    <th data-priority="4">Fecha</th>
                                    <th data-priority="5">Prioridad</th>
                                    <th data-priority="6">Estado</th>
                                    <th data-priority="7">Creador</th>
                                    <th data-priority="8">Destinatarios</th>
                                    <th data-priority="9">Archivos</th>
                                    <th data-priority="10" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($notas as $nota)
                            @php
                                $esPropia = $nota->creador_id == $user->id;
                                $creador = $nota->creador;

                                // Obtener el rol del creador en la obra
                                $rolCreador = null;
                                if ($creador && isset($usuariosObra[$creador->id])) {
                                    $obraUsuario = $usuariosObra[$creador->id];
                                    if ($obraUsuario->pivot && $obraUsuario->pivot->rol_id) {
                                        $rolCreador = \App\Models\RoleObra::find($obraUsuario->pivot->rol_id);
                                    }
                                }

                                // Determinar la clase CSS según el rol
                                $rolClass = 'rol-badge rol-sin-definir';
                                if ($rolCreador) {
                                    switch ($rolCreador->nombre) {
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
                            <tr class="nota-{{ $esPropia ? 'propia' : 'ajena' }}">
                                <td>NE-{{ str_pad($nota->numero, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ Str::limit($nota->tema, 30) }}</td>
                                <td class="tipo-entrega">
                                    <i class="fas fa-box-open mr-1"></i> {{ $nota->tipo_entrega }}
                                </td>
                                <td data-order="{{ $nota->fecha->timestamp }}">
                                    {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge badge-prioridad
                                        @if($nota->prioridad == 'Urgente') badge-danger
                                        @elseif($nota->prioridad == 'Alta') badge-warning
                                        @else badge-secondary @endif">
                                        {{ $nota->prioridad }}
                                    </span>
                                </td>
                                <td>
                                    <span class="estado-{{ strtolower($nota->estado) }}">
                                        <i class="fas
                                            @if($nota->estado == 'Emitida') fa-paper-plane
                                            @elseif($nota->estado == 'Firmada') fa-check-circle
                                            @elseif($nota->estado == 'Rechazada') fa-times-circle
                                            @else fa-question-circle @endif
                                            mr-1"></i>
                                        {{ $nota->estado }}
                                    </span>
                                </td>
                                <td>
                                    <div class="creador-container">
                                        @if($creador && $creador->profile_photo_path)
                                            <img src="{{ asset('storage/' . $creador->profile_photo_path) }}" class="img-circle elevation-1 mr-2" alt="{{ $creador->name }}" style="width: 25px; height: 25px;">
                                        @elseif($creador)
                                            <div class="creador-avatar bg-{{ $esPropia ? 'primary' : 'secondary' }}">
                                                {{ strtoupper(substr($creador->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold">{{ $creador->name ?? 'Desconocido' }}</div>
                                            <div>
                                                @if($rolCreador)
                                                    <span class="{{ $rolClass }}">
                                                        {{ $rolCreador->nombre }}
                                                    </span>
                                                @else
                                                    <span class="rol-badge rol-sin-definir">
                                                        Sin rol definido
                                                    </span>
                                                @endif
                                                @if($esPropia)
                                                    <span class="badge-tu">Tú</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @forelse($nota->destinatarios as $destinatario)
                                        @php
                                            // Obtener el rol del destinatario en la obra
                                            $rolDestinatario = null;
                                            if (isset($usuariosObra[$destinatario->id])) {
                                                $obraUsuario = $usuariosObra[$destinatario->id];
                                                if ($obraUsuario->pivot && $obraUsuario->pivot->rol_id) {
                                                    $rolDestinatario = \App\Models\RoleObra::find($obraUsuario->pivot->rol_id);
                                                }
                                            }

                                            // Determinar la clase CSS según el rol del destinatario
                                            $rolDestClass = 'rol-badge rol-sin-definir';
                                            if ($rolDestinatario) {
                                                switch ($rolDestinatario->nombre) {
                                                    case 'Inspector Principal':
                                                        $rolDestClass = 'rol-badge rol-inspector';
                                                        break;
                                                    case 'Asistente Inspección':
                                                        $rolDestClass = 'rol-badge rol-asistente';
                                                        break;
                                                    case 'Jefe de Proyecto':
                                                        $rolDestClass = 'rol-badge rol-jefe-proyecto';
                                                        break;
                                                    case 'Especialista':
                                                        $rolDestClass = 'rol-badge rol-especialista';
                                                        break;
                                                    case 'Jefe de Obra':
                                                    case 'Asistente Contratista':
                                                        $rolDestClass = 'rol-badge rol-contratista';
                                                        break;
                                                    default:
                                                        $rolDestClass = 'rol-badge rol-sin-definir';
                                                }
                                            }
                                        @endphp
                                        <span class="destinatario-badge" title="{{ $destinatario->name }}">
                                            {{ strtoupper(substr($destinatario->name, 0, 1)) }}.
                                            @if($rolDestinatario)
                                                <span class="{{ $rolDestClass }}" style="font-size: 0.7rem; margin-left: 3px;">
                                                    {{ strtoupper(substr($rolDestinatario->nombre, 0, 1)) }}
                                                </span>
                                            @endif
                                        </span>
                                    @empty
                                        <span class="badge badge-secondary">Sin destinatarios</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if($nota->archivos->count() > 0)
                                        <span class="badge badge-info">
                                            <i class="fas fa-paperclip mr-1"></i> {{ $nota->archivos->count() }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Sin archivos</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('obras.notas-equipo-proyecto.show', [$obra->id, $nota->id]) }}" class="btn btn-sm btn-outline-primary mr-1 btn-ver" title="Ver nota">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($nota->estado == 'Emitida' && $esPropia)
                                        <a href="{{ route('obras.notas-equipo-proyecto.edit', [$obra->id, $nota->id]) }}" class="btn btn-sm btn-outline-warning mr-1" title="Editar nota">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No hay notas al equipo de proyecto registradas para esta obra.</td>
                            </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<!-- Scripts para DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

<script>
$(document).ready(function() {
    // Configuración de DataTable
    $.fn.dataTable.ext.errMode = 'throw';

    @if($notas->isNotEmpty())
        var table = $('#notasEquipoProyectoTable').DataTable({
            responsive: {
                details: {
                    type: 'column',
                    target: 'tr'
                }
            },
            columnDefs: [
                { className: 'control', orderable: false, targets: 0 },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 4, targets: 3 },
                { responsivePriority: 5, targets: 4 },
                { responsivePriority: 6, targets: 5 },
                { responsivePriority: 7, targets: 6 },
                { responsivePriority: 8, targets: 7 },
                { responsivePriority: 9, targets: 8 },
                { orderable: false, targets: 9 },
                { className: 'text-center', targets: [0, 3, 4, 5, 8, 9] }
            ],
            order: [[3, 'desc']], // Ordenar por fecha de forma descendente por defecto
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            dom: '<"d-flex flex-wrap justify-content-between align-items-center"'
                +'<"dt-buttons-container"B>'
                +'<"dataTables_filter d-flex align-items-center"f>'
                +'>'
                +'rt'
                +'<"d-flex flex-wrap justify-content-between align-items-center"'
                +'<"dataTables_info"i>'
                +'<"dataTables_paginate"p>'
                +'>',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copiar',
                    className: 'btn btn-sm btn-dt-custom',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-dt-custom',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-dt-custom',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    customize: function(doc) {
                        doc.content[1].table.widths = ['10%', '15%', '15%', '10%', '10%', '10%', '15%', '15%', '10%', '15%'];
                        doc.styles.tableHeader.alignment = 'center';
                        doc.defaultStyle.alignment = 'center';
                        doc.pageMargins = [20, 20, 20, 20];
                        doc.content[0].text = 'Notas al Equipo de Proyecto - Obra: {{ $obra->nombre }}';
                        doc.content[0].fontSize = 16;
                        doc.content[0].alignment = 'center';
                        doc.content[0].margin = [0, 0, 0, 20];
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    className: 'btn btn-sm btn-dt-custom',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    customize: function(win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                        $(win.document.body).find('h1').text('Notas al Equipo de Proyecto - Obra: {{ $obra->nombre }}');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i> Columnas',
                    className: 'btn btn-sm btn-dt-custom',
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            ]
        });

        // Ajustar el diseño cuando se cambie el tamaño de la ventana
        $(window).on('resize', function() {
            table.responsive.recalc();
        });
    @endif

    // Ajustar el estilo del input de búsqueda
    $('.dataTables_filter input').addClass('form-control form-control-sm');
    $('.dataTables_filter input').attr('placeholder', 'Buscar...');

    // Ajustar el estilo del select de longitud
    $('.dataTables_length select').addClass('form-select form-select-sm');

    // Mostrar notificación de éxito si existe
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
});
</script>
@endsection