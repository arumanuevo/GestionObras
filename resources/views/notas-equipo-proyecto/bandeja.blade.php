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

    /* Estilo para las filas no leídas - Versión mejorada */
    .nota-no-leida {
        background-color: rgba(255, 235, 186, 0.1) !important;
        border-left: 3px solid #ffc107;
    }

    .nota-no-leida:hover {
        background-color: rgba(255, 235, 186, 0.25) !important;
        transform: scale(1.01);
    }

    /* Estilo para el indicador de nueva nota - Versión mejorada */
    .nuevo-indicador {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 12px;
        height: 12px;
        background-color: #dc3545;
        border-radius: 50%;
        box-shadow: 0 0 0 2px white;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    /* Estilo para el contenedor de la celda con posición relativa */
    .td-con-indicador {
        position: relative;
        padding-right: 30px !important;
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

    /* Estilo para la tarjeta de nota */
    .nota-card {
        position: relative;
        transition: all 0.2s ease;
    }

    /* Estilo para el badge de nuevo (versión alternativa) */
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

    /* Estilo para el asunto con elipses */
    .asunto-text {
        display: inline-block;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }

    /* Estilo para el alert mejorado */
    .alert-notas {
        border-left: 4px solid #0d6efd;
    }

    .notas-count {
        font-size: 1.2rem;
        font-weight: bold;
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
                        <h3 class="card-title mb-0">Bandeja de Entrada - Notas al Equipo de Proyecto</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la obra
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-notas mb-4 d-flex align-items-center">
                        <i class="fas fa-info-circle me-3"></i>
                        <div>
                            <strong>Aquí puedes ver todas las notas al equipo de proyecto</strong> que has recibido como miembro del equipo de proyecto.
                            @php
                                $notasNoLeidas = $notas->filter(function($nota) {
                                    $destinatario = $nota->destinatarios->first();
                                    return $destinatario && !$destinatario->pivot->leida;
                                })->count();
                            @endphp
                            @if($notasNoLeidas > 0)
                            <div class="mt-2">
                                <span class="badge bg-danger me-2">
                                    <i class="fas fa-envelope me-1"></i>
                                    <span class="notas-count">{{ $notasNoLeidas }}</span>
                                </span>
                                Tienes <strong>{{ $notasNoLeidas }}</strong> nota{{ $notasNoLeidas != 1 ? 's' : '' }} sin leer.
                            </div>
                            @else
                            <div class="mt-2 text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                No tienes notas nuevas sin leer.
                            </div>
                            @endif
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
                                    <th data-priority="7">Remitente</th>
                                    <th data-priority="8">Archivos</th>
                                    <th data-priority="9" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($notas as $nota)
                            @php
                                $destinatario = $nota->destinatarios->first();
                                $leida = $destinatario ? $destinatario->pivot->leida : false;
                            @endphp
                            <tr class="nota-card {{ !$leida ? 'nota-no-leida' : '' }}">
                                <td>NE-{{ str_pad($nota->numero, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="td-con-indicador">
                                    <span class="asunto-text" title="{{ $nota->tema }}">{{ Str::limit($nota->tema, 30) }}</span>
                                    @if(!$leida)
                                        <span class="nuevo-indicador" title="Nota nueva sin leer"></span>
                                    @endif
                                </td>
                                <td class="tipo-entrega">
                                    <i class="fas fa-box-open mr-1"></i> {{ Str::limit($nota->tipo_entrega, 20) }}
                                </td>
                                <td data-order="{{ $nota->fecha->timestamp }}">
                                    {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="prioridad-container">
                                        @if($nota->prioridad == 'Urgente')
                                            <i class="fas fa-exclamation-circle prioridad-icon text-danger"></i>
                                        @elseif($nota->prioridad == 'Alta')
                                            <i class="fas fa-exclamation-triangle prioridad-icon text-warning"></i>
                                        @else
                                            <i class="fas fa-info-circle prioridad-icon text-info"></i>
                                        @endif
                                        <span class="badge badge-prioridad
                                            @if($nota->prioridad == 'Urgente') badge-danger
                                            @elseif($nota->prioridad == 'Alta') badge-warning
                                            @else badge-secondary @endif">
                                            {{ $nota->prioridad }}
                                        </span>
                                    </div>
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
                                    <div class="d-flex align-items-center">
                                        @if($nota->creador && $nota->creador->profile_photo_path)
                                            <img src="{{ asset('storage/' . $nota->creador->profile_photo_path) }}" class="img-circle elevation-1 mr-2" alt="{{ $nota->creador->name }}" style="width: 25px; height: 25px;">
                                        @elseif($nota->creador)
                                            <div class="mr-2 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                                {{ strtoupper(substr($nota->creador->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $nota->creador->name ?? 'Desconocido' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($nota->archivos->count() > 0)
                                        <span class="badge badge-info">
                                            <i class="fas fa-paperclip mr-1"></i> {{ $nota->archivos->count() }}
                                        </span>
                                    @else
                                        <span class="badge badge-info">Sin archivos</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <!--<a href="{{ route('obras.notas-equipo-proyecto.show', [$obra->id, $nota->id]) }}" class="btn btn-sm btn-outline-primary mr-1 btn-ver" title="Ver nota">
                                            <i class="fas fa-eye"></i>
                                        </a>-->
                                        <a href="{{ route('bandeja-publica.notas-equipo-proyecto.show', [
            'obra' => $obra->id,
            'nota' => $nota->id
        ]) }}" class="btn btn-sm btn-outline-primary mr-1 btn-ver" title="Ver nota equipo">
            <i class="fas fa-eye"></i>
        </a>
                                        @if($nota->estado == 'Emitida' && !$leida)
                                        <form action="{{ route('obras.notas-equipo-proyecto.firmar', [$obra->id, $nota->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Firmar nota">
                                                <i class="fas fa-signature"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No hay notas al equipo de proyecto en tu bandeja de entrada.</td>
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
<script src="https://datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
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
            { orderable: false, targets: 8 },
            { className: 'text-center', targets: [0, 3, 4, 5, 7, 8] }
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-dt-custom',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-dt-custom',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                customize: function(doc) {
                    doc.content[1].table.widths = ['10%', '15%', '15%', '10%', '10%', '10%', '15%', '10%', '15%'];
                    doc.styles.tableHeader.alignment = 'center';
                    doc.defaultStyle.alignment = 'center';
                    doc.pageMargins = [20, 20, 20, 20];
                    doc.content[0].text = 'Bandeja de Entrada - Notas al Equipo de Proyecto';
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                customize: function(win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    $(win.document.body).find('h1').text('Bandeja de Entrada - Notas al Equipo de Proyecto');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columnas',
                className: 'btn btn-sm btn-dt-custom',
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            }
        ]
    });

    // Ajustar el diseño cuando se cambie el tamaño de la ventana
    $(window).on('resize', function() {
        table.responsive.recalc();
    });

    // Ajustar el estilo del input de búsqueda
    $('.dataTables_filter input').addClass('form-control form-control-sm');
    $('.dataTables_filter input').attr('placeholder', 'Buscar...');

    // Ajustar el estilo del select de longitud
    $('.dataTables_length select').addClass('form-select form-select-sm');

    // Tooltip para los indicadores de notas nuevas
    $('[title="Nota nueva sin leer"]').tooltip({
        title: "Nota nueva sin leer",
        placement: "left",
        trigger: "hover",
        delay: {"show": 500, "hide": 100}
    });

    // Mostrar notificación de éxito si existe
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
});
</script>
@endsection