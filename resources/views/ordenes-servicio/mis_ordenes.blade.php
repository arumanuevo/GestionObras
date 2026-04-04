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

    /* Estilos adicionales omitidos por brevedad... */

    /* Estilo para los destinatarios en la misma celda */
    .destinatarios-container {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        max-width: 180px;
    }

    .destinatario-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        color: white;
        cursor: pointer;
        position: relative;
        border: 1px solid rgba(0,0,0,0.1);
    }

    .destinatario-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: #fff;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        white-space: nowrap;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s;
        z-index: 100;
        margin-bottom: 5px;
        min-width: 120px;
        text-align: center;
    }

    .destinatario-icon:hover .destinatario-tooltip {
        visibility: visible;
        opacity: 1;
    }

    /* Estilo para el badge de cantidad de destinatarios */
    .destinatarios-count {
        background-color: #6c757d;
        color: white;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 0.7rem;
        margin-left: 5px;
    }

    /* Estilo para las filas de órdenes creadas por otro inspector */
    .otro-inspector {
        background-color: rgba(255, 235, 186, 0.2) !important;
        border-left: 4px solid #ffc107 !important;
    }

    /* Estilo para el nombre del creador */
    .creador-nombre {
        font-weight: 500;
        position: relative;
        display: inline-block;
    }

    .creador-nombre.otro::after {
        content: "";
        position: absolute;
        top: -5px;
        right: -12px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ffc107;
    }

    .creador-nombre.otro:hover::after {
        content: "Creada por otro inspector";
        top: -25px;
        right: -10px;
        width: auto;
        height: auto;
        border-radius: 4px;
        background-color: #ffc107;
        color: #856404;
        padding: 2px 8px;
        font-size: 0.7rem;
        font-weight: bold;
        white-space: nowrap;
    }

    /* Estilo para los tooltips */
    .tooltip-otro {
        position: relative;
        display: inline-block;
    }

    .tooltip-otro .tooltiptext {
        visibility: hidden;
        width: auto;
        background-color: #ffc107;
        color: #856404;
        text-align: center;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 0.7rem;
        font-weight: bold;
        position: absolute;
        z-index: 1;
        top: -5px;
        left: 105%;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip-otro:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
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
                        <h3 class="card-title mb-0">Mis Órdenes de Servicio - Obra: {{ $obra->nombre }}</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.ordenes-servicio.create', $obra->id) }}" class="btn btn-sm btn-success mr-2">
                                <i class="fas fa-plus mr-1"></i> Nueva Orden de Servicio
                            </a>
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Aquí puedes ver las órdenes de servicio creadas por los inspectores de esta obra.
                        @if($esInspector ?? false)
                            Las órdenes creadas por el otro inspector se muestran con un fondo amarillo claro y un indicador visual.
                            Pasa el cursor sobre los iconos de destinatarios para ver los detalles.
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table id="ordenesServicioTable" class="table table-bordered table-hover nowrap" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th data-priority="1">ID</th>
                                    <th data-priority="2">Número</th>
                                    <th data-priority="3">Tema</th>
                                    <th data-priority="4">Creador</th>
                                    <th data-priority="5">Fecha de Emisión</th>
                                    <th data-priority="6">Fecha de Vencimiento</th>
                                    <th data-priority="7">Estado</th>
                                    <th data-priority="8">Firmada</th>
                                    <th data-priority="9">Destinatario(s)</th>
                                    <th data-priority="10" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($ordenesAgrupadas as $grupo)
                            @php
                                $orden = $grupo['orden'];
                                $destinatarios = $grupo['destinatarios'];
                                $esOtroInspector = $orden->creador_id != auth()->id();
                            @endphp
                            <tr @if($esOtroInspector) class="otro-inspector" @endif>
                                <td>{{ $orden->id }}</td>
                                <td>OS-{{ str_pad($orden->Nro ?? $orden->numero, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ Str::limit($orden->Tema ?? $orden->tema, 30) }}</td>
                                <td>
                                    <div class="tooltip-otro">
                                        <span class="creador-nombre @if($esOtroInspector) creador-otro @endif">
                                            {{ $orden->creador->name ?? 'Desconocido' }}
                                            @if($orden->creador_id == auth()->id())
                                                <small class="text-muted">(Tú)</small>
                                            @endif
                                        </span>
                                        @if($esOtroInspector)
                                            <span class="tooltiptext">Creada por otro inspector</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-order="{{ isset($orden->fecha) ? \Carbon\Carbon::parse($orden->fecha)->timestamp : (isset($orden->fecha_emision) ? \Carbon\Carbon::parse($orden->fecha_emision)->timestamp : 0) }}">
                                    @if(isset($orden->fecha) && $orden->fecha)
                                        {{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}
                                    @elseif(isset($orden->fecha_emision) && $orden->fecha_emision)
                                        {{ \Carbon\Carbon::parse($orden->fecha_emision)->format('d/m/Y') }}
                                    @else
                                        Sin fecha
                                    @endif
                                </td>
                                <td data-order="{{ isset($orden->fecha_vencimiento) ? \Carbon\Carbon::parse($orden->fecha_vencimiento)->timestamp : 0 }}"
                                    class="@if(isset($orden->fecha_vencimiento))
                                        @php
                                            $fechaVencimiento = \Carbon\Carbon::parse($orden->fecha_vencimiento);
                                            $diasRestantes = $fechaVencimiento->diffInDays(\Carbon\Carbon::now());
                                            $vencido = $fechaVencimiento->isPast();
                                        @endphp
                                        @if($vencido)
                                            vencimiento-vencido
                                        @elseif($diasRestantes <= 3)
                                            vencimiento-proximo
                                        @else
                                            vencimiento-normal
                                        @endif
                                    @endif">
                                    @if(isset($orden->fecha_vencimiento) && $orden->fecha_vencimiento)
                                        {{ \Carbon\Carbon::parse($orden->fecha_vencimiento)->format('d/m/Y') }}
                                        @if($vencido)
                                            <br><small class="text-danger">(Vencido hace {{ abs(floor($diasRestantes)) }} día{{ abs(floor($diasRestantes)) != 1 ? 's' : '' }})</small>
                                        @elseif($diasRestantes <= 3)
                                            <br><small class="text-warning">(Vence en {{ floor($diasRestantes) }} día{{ floor($diasRestantes) != 1 ? 's' : '' }})</small>
                                        @else
                                            <br><small class="text-info">({{ floor($diasRestantes) }} día{{ floor($diasRestantes) != 1 ? 's' : '' }} restantes)</small>
                                        @endif
                                    @else
                                        Sin fecha
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-estado
                                        @if(($orden->Estado ?? $orden->estado) == 'Cumplida') badge-success
                                        @elseif(($orden->Estado ?? $orden->estado) == 'Firmada' || ($orden->Estado ?? $orden->estado) == 'Firmado') badge-success
                                        @elseif(($orden->Estado ?? $orden->estado) == 'Pendiente de Firma') badge-warning
                                        @elseif(($orden->Estado ?? $orden->estado) == 'Incumplida') badge-danger
                                        @else badge-secondary @endif">
                                        {{ $orden->Estado ?? $orden->estado ?? 'Sin estado' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-firmada {{ ($orden->firmada ?? false) ? 'badge-success' : 'badge-warning' }}">
                                        {{ ($orden->firmada ?? false) ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="destinatarios-container">
                                        @foreach($destinatarios as $index => $destinatario)
                                            @php
                                                $initial = strtoupper(substr($destinatario->name ?? 'U', 0, 1));
                                                $color = $index % 2 == 0 ? '#28a745' : '#007bff'; // Verde para Jefe de Obra, Azul para Asistente Contratista
                                            @endphp
                                            <div class="destinatario-icon" style="background-color: {{ $color }}">
                                                {{ $initial }}
                                                <div class="destinatario-tooltip">
                                                    {{ $destinatario->name ?? 'Desconocido' }}<br>
                                                    <small>{{ $destinatario->email ?? 'Sin email' }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if(count($destinatarios) > 1)
                                            <span class="destinatarios-count">{{ count($destinatarios) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('obras.ordenes-servicio.show', [$obra->id, $orden->id]) }}" class="btn btn-sm btn-outline-primary mr-1 btn-ver" title="Ver orden">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($orden->creador_id == auth()->id())
                                        <a href="{{ route('obras.ordenes-servicio.edit', [$obra->id, $orden->id]) }}" class="btn btn-sm btn-outline-warning mr-1" title="Editar orden">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No hay órdenes de servicio para esta obra.</td>
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
    var table = $('#ordenesServicioTable').DataTable({
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
            { orderable: false, targets: 9 },
            { className: 'text-center', targets: [0, 1, 6, 7, 9] }
        ],
        order: [[4, 'desc']],
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
                    doc.content[1].table.widths = ['5%', '8%', '15%', '10%', '10%', '10%', '10%', '8%', '15%', '8%'];
                    doc.styles.tableHeader.alignment = 'center';
                    doc.defaultStyle.alignment = 'center';
                    doc.pageMargins = [20, 20, 20, 20];
                    doc.content[0].text = 'Mis Órdenes de Servicio - Obra: {{ $obra->nombre }}';
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
                    $(win.document.body).find('h1').text('Mis Órdenes de Servicio - Obra: {{ $obra->nombre }}');
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

    // Ajustar el estilo del input de búsqueda
    $('.dataTables_filter input').addClass('form-control form-control-sm');
    $('.dataTables_filter input').attr('placeholder', 'Buscar...');

    // Ajustar el estilo del select de longitud
    $('.dataTables_length select').addClass('form-select form-select-sm');
});
</script>
@endsection