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

    /* Estilo para las filas según estado */
    .entrega-emitida {
        border-left: 3px solid #ffc107;
    }

    .entrega-recibida {
        border-left: 3px solid #28a745;
    }

    /* Estilo para el indicador de estado */
    .estado-indicador {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        box-shadow: 0 0 0 2px white;
    }

    .estado-emitido {
        background-color: #ffc107;
    }

    .estado-recibido {
        background-color: #28a745;
    }

    /* Estilo para el contenedor de la celda con posición relativa */
    .td-con-indicador {
        position: relative;
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

    /* Estilo para el contenedor de prioridad */
    .prioridad-container {
        display: flex;
        align-items: center;
    }

    /* Estilo para el icono de prioridad */
    .prioridad-icon {
        margin-right: 5px;
        font-size: 1rem;
    }

    /* Estilo para la tarjeta de entrega */
    .entrega-card {
        position: relative;
        transition: all 0.2s ease;
    }

    .entrega-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Estilo para el badge de nuevo */
    .badge-nuevo {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #dc3545;
        color: white;
        font-size: 0.65rem;
        padding: 0.2em 0.4em;
        border-radius: 10px;
        transform: rotate(15deg);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Estilo para el contenedor de la tabla */
    .table-container {
        overflow-x: auto;
    }

    /* Estilo para el estado de la entrega */
    .estado-emitida {
        color: #6c757d;
    }

    .estado-recibida {
        color: #28a745;
    }

    /* Estilo para el asunto con elipses */
    .asunto-text {
        display: inline-block;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
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

    /* Estilo para el alert mejorado */
    .alert-entregas {
        border-left: 4px solid #17a2b8;
    }

    .entregas-count {
        font-size: 1.2rem;
        font-weight: bold;
    }

    /* Estilo para el creador de la entrega */
    .creador-badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
        border-radius: 0.25rem;
    }

    /* Estilo para las filas según el creador */
    .entrega-propia {
        background-color: rgba(173, 216, 230, 0.1);
    }

    .entrega-ajena {
        background-color: rgba(240, 248, 255, 0.3);
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Entregas al Contratista - Obra: {{ $obra->nombre }}</h3>
                        <div class="card-tools d-flex">
                            <!-- Botón para volver a la administración de la obra -->
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la obra
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $user = Auth::user();
                        $totalEntregasEmitidas = $entregas->where('estado', 'Emitida')->count();
                        $totalEntregasRecibidas = $entregas->where('estado', 'Recibida')->count();
                        $entregasPropias = $entregas->where('creador_id', $user->id);
                        $entregasAjenas = $entregas->where('creador_id', '!=', $user->id);
                    @endphp

                    <div class="alert alert-info alert-entregas mb-4 d-flex align-items-center">
                        <i class="fas fa-info-circle me-3"></i>
                        <div>
                            <strong>Aquí puedes ver TODAS las entregas al contratista</strong> relacionadas con esta obra.
                            <div class="mt-2">
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-paper-plane me-1"></i>
                                    <span class="entregas-count">{{ $totalEntregasEmitidas }}</span>
                                </span>
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    <span class="entregas-count">{{ $totalEntregasRecibidas }}</span>
                                </span>
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-user me-1"></i>
                                    <span class="entregas-count">{{ $entregasPropias->count() }}</span>
                                </span>
                                <span class="badge bg-secondary me-2">
                                    <i class="fas fa-users me-1"></i>
                                    <span class="entregas-count">{{ $entregasAjenas->count() }}</span>
                                </span>
                                Tienes <strong>{{ $entregas->count() }}</strong> entrega{{ $entregas->count() != 1 ? 's' : '' }} en total.
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="entregasContratistaTable" class="table table-bordered table-hover nowrap" style="width:100%">
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
                            @forelse($entregas as $entrega)
                            @php
                                $esPropia = $entrega->creador_id == $user->id;
                                $creador = $entrega->creador;

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
                            <tr class="entrega-card entrega-{{ strtolower($entrega->estado) }} {{ $esPropia ? 'entrega-propia' : 'entrega-ajena' }}">
                                <td>EC-{{ str_pad($entrega->numero, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="td-con-indicador">
                                    <span class="asunto-text" title="{{ $entrega->asunto }}">{{ Str::limit($entrega->asunto, 30) }}</span>
                                    <span class="estado-indicador estado-{{ strtolower($entrega->estado) }}" title="{{ $entrega->estado }}"></span>
                                </td>
                                <td class="tipo-entrega">
                                    <i class="fas fa-box-open mr-1"></i> {{ Str::limit($entrega->tipo_entrega, 20) }}
                                </td>
                                <td data-order="{{ $entrega->fecha->timestamp }}">
                                    {{ \Carbon\Carbon::parse($entrega->fecha)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="prioridad-container">
                                        @if($entrega->prioridad == 'Urgente')
                                            <i class="fas fa-exclamation-circle prioridad-icon text-danger"></i>
                                        @elseif($entrega->prioridad == 'Alta')
                                            <i class="fas fa-exclamation-triangle prioridad-icon text-warning"></i>
                                        @else
                                            <i class="fas fa-info-circle prioridad-icon text-info"></i>
                                        @endif
                                        <span class="badge badge-prioridad
                                            @if($entrega->prioridad == 'Urgente') badge-danger
                                            @elseif($entrega->prioridad == 'Alta') badge-warning
                                            @else badge-secondary @endif">
                                            {{ $entrega->prioridad }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="estado-{{ strtolower($entrega->estado) }}">
                                        <i class="fas
                                            @if($entrega->estado == 'Emitida') fa-paper-plane
                                            @elseif($entrega->estado == 'Recibida') fa-check-circle
                                            @else fa-question-circle @endif
                                            mr-1"></i>
                                        {{ $entrega->estado }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($creador && $creador->profile_photo_path)
                                            <img src="{{ asset('storage/' . $creador->profile_photo_path) }}" class="img-circle elevation-1 mr-2" alt="{{ $creador->name }}" style="width: 25px; height: 25px;">
                                        @elseif($creador)
                                            <div class="mr-2 d-flex align-items-center justify-content-center bg-{{ $esPropia ? 'primary' : 'secondary' }} text-white rounded-circle" style="width: 25px; height: 25px; font-size: 0.7rem;">
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
                                                    <span class="badge creador-badge bg-success">Tú</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @forelse($entrega->destinatarios as $destinatario)
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
                                    @if($entrega->archivos->count() > 0)
                                        <span class="badge badge-info">
                                            <i class="fas fa-paperclip mr-1"></i> {{ $entrega->archivos->count() }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Sin archivos</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('obras.entregas-contratista.show', ['obra' => $obra->id, 'entrega' => $entrega->id]) }}" class="btn btn-sm btn-outline-primary mr-1 btn-ver" title="Ver entrega">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($entrega->estado == 'Emitida' && $esPropia)
                                        <!-- Botón de edición comentado como en el original -->
                                        @endif

                                        @if($entrega->destinatarios->contains('id', $user->id) && $entrega->estado == 'Emitida')
                                        <form action="{{ route('obras.entregas-contratista.recibir', [$obra->id, $entrega->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como recibida">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No hay entregas al contratista registradas para esta obra.</td>
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

    @if($entregas->isNotEmpty())
        var table = $('#entregasContratistaTable').DataTable({
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
                        doc.content[1].table.widths = ['10%', '15%', '15%', '10%', '10%', '10%', '15%', '15%', '10%', '10%'];
                        doc.styles.tableHeader.alignment = 'center';
                        doc.defaultStyle.alignment = 'center';
                        doc.pageMargins = [20, 20, 20, 20];
                        doc.content[0].text = 'Entregas al Contratista - Obra: {{ $obra->nombre }}';
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
                        $(win.document.body).find('h1').text('Entregas al Contratista - Obra: {{ $obra->nombre }}');
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