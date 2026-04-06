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

    /* Estilo para las filas no recibidas */
    .entrega-no-recibida {
        background-color: rgba(186, 255, 201, 0.1) !important;
        border-left: 3px solid #28a745;
    }

    .entrega-no-recibida:hover {
        background-color: rgba(186, 255, 201, 0.25) !important;
        transform: scale(1.01);
    }

    /* Estilo para el indicador de entrega no recibida */
    .no-recibido-indicador {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 12px;
        height: 12px;
        background-color: #28a745;
        border-radius: 50%;
        box-shadow: 0 0 0 2px white;
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

    /* Estilo para la tarjeta de entrega */
    .entrega-card {
        position: relative;
        transition: all 0.2s ease;
    }

    /* Estilo para el badge de nuevo */
    .badge-nuevo {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #28a745;
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
        margin-bottom: 20px;
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

    /* Estilo para el alert mejorado */
    .alert-entregas {
        border-left: 4px solid #17a2b8;
    }

    .entregas-count {
        font-size: 1.2rem;
        font-weight: bold;
    }

    /* Estilo para centrar el contenido de las celdas de número */
    .numero-entrega {
        text-align: center;
        font-weight: 500;
    }

    /* Estilo para la paginación */
    .dataTables_paginate {
        margin-top: 15px;
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
                        <h3 class="card-title mb-0">Bandeja de Entraddda - Entregas del Equipo de Proyecto</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la obra
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-entregas mb-4 d-flex align-items-center">
                        <i class="fas fa-info-circle me-3"></i>
                        <div>
                            <strong>Aquí puedes ver todas las entregas</strong> que has recibido como contratista de esta obra.
                            @php
                                $entregasNoRecibidas = $entregas->filter(function($entrega) {
                                    $destinatario = $entrega->destinatarios->first();
                                    return $destinatario && !$destinatario->pivot->recibida;
                                })->count();
                            @endphp
                            @if($entregasNoRecibidas > 0)
                            <div class="mt-2">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-truck-loading me-1"></i>
                                    <span class="entregas-count">{{ $entregasNoRecibidas }}</span>
                                </span>
                                Tienes <strong>{{ $entregasNoRecibidas }}</strong> entrega{{ $entregasNoRecibidas != 1 ? 's' : '' }} sin recibir.
                            </div>
                            @else
                            <div class="mt-2 text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                No tienes entregas nuevas sin leer.
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive table-container">
                        <table id="entregasContratistaTable" class="table table-bordered table-hover nowrap" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th data-priority="1" class="text-center">Número de Entrega</th>
                                    <th data-priority="2">Asunto</th>
                                    <th data-priority="3">Tipo de Entrega</th>
                                    <th data-priority="4">Fecha</th>
                                    <th data-priority="5">Prioridad</th>
                                    <th data-priority="6">Estado</th>
                                    <th data-priority="7">Remitente</th>
                                    <th data-priority="8">Archivos</th>
                                    <th data-priority="9" class="no-sort text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($entregas as $entrega)
                            @php
                                $destinatario = $entrega->destinatarios->first();
                                $recibida = $destinatario ? $destinatario->pivot->recibida : false;
                            @endphp
                            <tr class="entrega-card {{ !$recibida ? 'entrega-no-recibida' : '' }}">
                                <td class="numero-entrega" data-order="{{ $entrega->numero }}">
                                    EC-{{ str_pad($entrega->numero, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="td-con-indicador">
                                    <span class="asunto-text" title="{{ $entrega->asunto }}">{{ Str::limit($entrega->asunto, 30) }}</span>
                                    @if(!$recibida)
                                        <span class="no-recibido-indicador" title="Entrega no recibida"></span>
                                    @endif
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
                                        @if($entrega->creador && $entrega->creador->profile_photo_path)
                                            <img src="{{ asset('storage/' . $entrega->creador->profile_photo_path) }}" class="img-circle elevation-1 mr-2" alt="{{ $entrega->creador->name }}" style="width: 25px; height: 25px;">
                                        @elseif($entrega->creador)
                                            <div class="mr-2 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                                {{ strtoupper(substr($entrega->creador->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $entrega->creador->name ?? 'Desconocido' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($entrega->archivos->count() > 0)
                                        <span class="badge badge-info">
                                            <i class="fas fa-paperclip mr-1"></i> {{ $entrega->archivos->count() }}
                                        </span>
                                    @else
                                        <span class="badge badge-info">Sin archivos</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('bandeja-publica.entregas-contratista.show', [
                                            'obra' => $obra->id,
                                            'entrega' => $entrega->id
                                        ]) }}" class="btn btn-sm btn-outline-primary mr-1 btn-ver" title="Ver entrega">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($entrega->estado == 'Emitida' && !$recibida)
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
                                <td colspan="9" class="text-center">No hay entregas en tu bandeja de entrada.</td>
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
        // Inicializar DataTable con configuración simplificada
        var table = $('#entregasContratistaTable').DataTable({
            responsive: true,
            columnDefs: [
                {
                    targets: 0, // Columna de Número de Entrega
                    className: 'text-center',
                    orderData: [0, 1], // Ordenar por el número real
                    render: function(data, type, row, meta) {
                        // Para mostrar en la tabla
                        if (type === 'display') {
                            return 'EC-' + String(row.numero).padStart(4, '0');
                        }
                        // Para ordenar
                        return row.numero;
                    }
                },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 4, targets: 3 },
                { responsivePriority: 5, targets: 4 },
                { responsivePriority: 6, targets: 5 },
                { responsivePriority: 7, targets: 6 },
                { responsivePriority: 8, targets: 7 },
                { orderable: false, targets: 8 },
                { className: 'text-center', targets: [0, 3, 5, 8] }
            ],
            order: [[3, 'desc']], // Ordenar por fecha de forma descendente por defecto
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                paginate: {
                    previous: '<i class="fas fa-chevron-left">',
                    next: '<i class="fas fa-chevron-right">'
                }
            },
            dom: '<"d-flex flex-wrap justify-content-between align-items-center"'
                +'<"dt-buttons-container"B>'
                +'<"dataTables_filter d-flex align-items-center"f>'
                +'>'
                +'rt'
                +'<"d-flex flex-wrap justify-content-between align-items-center"'
                +'<"dataTables_info"i>'
                +'<"dataTables_paginate mt-3"p>'
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
                        doc.content[1].table.widths = ['12%', '18%', '15%', '10%', '10%', '10%', '15%', '10%', '10%'];
                        doc.styles.tableHeader.alignment = 'center';
                        doc.defaultStyle.alignment = 'center';
                        doc.pageMargins = [20, 20, 20, 20];
                        doc.content[0].text = 'Bandeja de Entrada - Entregas del Equipo de Proyecto';
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
                        $(win.document.body).find('h1').text('Bandeja de Entrada - Entregas del Equipo de Proyecto');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                        $(win.document.body).find('th').css('text-align', 'center');
                        $(win.document.body).find('td').css('text-align', 'left');
                        $(win.document.body).find('td:nth-child(1), td:nth-child(4), td:nth-child(6), td:last-child').css('text-align', 'center');
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i> Columnas',
                    className: 'btn btn-sm btn-dt-custom',
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            ],
            drawCallback: function() {
                // Reactivar tooltips después de redibujar
                $('[title="Entrega no recibida"]').tooltip({
                    title: "Entrega no recibida",
                    placement: "left",
                    trigger: "hover",
                    delay: {"show": 500, "hide": 100}
                });
            }
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

    // Inicializar tooltips
    $('[title="Entrega no recibida"]').tooltip({
        title: "Entrega no recibida",
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