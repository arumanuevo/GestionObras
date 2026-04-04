@extends('layouts.app')

@section('styles')
@parent
<!-- Estilos para DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">

<style>
    /* Estilos personalizados para DataTables */
    .dataTables_wrapper {
        padding: 10px;
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

    .badge-firmada {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }

    /* Estilo para los botones de acción */
    .btn-ver {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    /* Estilo para las celdas de vencimiento */
    .vencimiento-vencido {
        color: #dc3545 !important;
        font-weight: 500;
    }

    .vencimiento-proximo {
        color: #ffc107 !important;
        font-weight: 500;
    }

    .vencimiento-normal {
        color: #28a745 !important;
    }

    /* Estilo para las filas no leídas */
    .nueva-orden {
        background-color: rgba(255, 235, 186, 0.3) !important;
    }

    /* Estilo para el perfil del remitente */
    .remitente-perfil {
        width: 25px;
        height: 25px;
        font-size: 12px;
    }

    /* Estilo para los botones de acción */
    .acciones-container {
        display: flex;
        justify-content: center;
        gap: 0.3rem;
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

    /* Estilo para el contenedor de la tabla */
    .table-responsive {
        overflow: visible;
    }

    /* Estilo para el badge de nuevo */
    .badge-nuevo {
        font-size: 0.7rem;
        padding: 0.2em 0.4em;
        margin-left: 0.3rem;
    }

    /* Estilo para el PDF */
    .pdf-icon {
        font-size: 0.9rem;
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
                        <div class="d-flex align-items-center">
                            <h3 class="card-title mb-0" style="font-size: 1.2rem;">Bandeja de Órdenes de Servicio</h3>
                            @php
                                $ordenesNoLeidas = $ordenesRecibidas->where('leida', false)->count();
                            @endphp
                            @if($ordenesNoLeidas > 0)
                                <span class="badge bg-danger ml-2" style="font-size: 0.8rem;">
                                    {{ $ordenesNoLeidas }} nuevas
                                </span>
                            @endif
                        </div>
                        <div>
                            <a href="#" onclick="window.history.back(); return false;" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p style="font-size: 0.9rem;">Obra: <strong>{{ $obra->nombre }}</strong></p>

                    @if($ordenesRecibidas->isNotEmpty())
                        <div class="table-responsive">
                            <table id="ordenesServicioTable" class="table table-bordered table-hover nowrap" style="width:100%; font-size: 0.85rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th data-priority="1">Número</th>
                                        <th data-priority="2">Tema</th>
                                        <th data-priority="3">Fecha Emisión</th>
                                        <th data-priority="4">Fecha Vencimiento</th>
                                        <th data-priority="5">Estado</th>
                                        <th data-priority="6">Firmada</th>
                                        <th data-priority="7">Remitente</th>
                                        <th data-priority="8">Nota Relacionada</th>
                                        <th data-priority="9">PDF</th>
                                        <th data-priority="10" class="no-sort">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ordenesRecibidas as $orden)
                                    <tr class="{{ !$orden->leida ? 'nueva-orden' : '' }}">
                                        <td style="text-align: center; vertical-align: middle;">
                                            OS-{{ str_pad($orden->Nro ?? $orden->numero, 4, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            {{ Str::limit($orden->Tema ?? $orden->tema, 30) }}
                                            @if(!$orden->leida)
                                                <span class="badge badge-danger badge-nuevo">Nuevo</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;"
                                            data-order="{{ isset($orden->fecha) ? \Carbon\Carbon::parse($orden->fecha)->timestamp : 0 }}">
                                            {{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y H:i') }}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;"
                                            data-order="{{ isset($orden->fecha_vencimiento) ? \Carbon\Carbon::parse($orden->fecha_vencimiento)->timestamp : 0 }}"
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
                                            @if($orden->fecha_vencimiento)
                                                {{ \Carbon\Carbon::parse($orden->fecha_vencimiento)->format('d/m/Y') }}
                                            @else
                                                Sin fecha
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <span class="badge badge-estado
                                                @if($orden->Estado == 'Cumplida') badge-success
                                                @elseif($orden->Estado == 'Firmada' || $orden->Estado == 'Firmado') badge-success
                                                @elseif($orden->Estado == 'Pendiente de Firma') badge-warning
                                                @elseif($orden->Estado == 'Incumplida') badge-danger
                                                @else badge-secondary @endif">
                                                {{ $orden->Estado ?? 'Sin estado' }}
                                            </span>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <span class="badge badge-firmada {{ $orden->firmada ? 'badge-success' : 'badge-warning' }}">
                                                {{ $orden->firmada ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <div class="d-flex align-items-center justify-content-center">
                                                @if($orden->creador && $orden->creador->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $orden->creador->profile_photo_path) }}"
                                                         class="img-circle elevation-2 mr-2 remitente-perfil"
                                                         alt="{{ $orden->creador->name }}">
                                                @else
                                                    <div class="mr-2 d-flex align-items-center justify-content-center bg-success text-white rounded-circle remitente-perfil">
                                                        {{ strtoupper(substr($orden->creador->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span style="font-size: 0.85rem;">{{ $orden->creador->name ?? 'Desconocido' }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if($orden->nota_pedido_id)
                                                <a href="{{ route('obras.notas-pedido.show', [$obra->id, $orden->nota_pedido_id]) }}" class="btn btn-xs btn-outline-info" style="font-size: 0.75rem;">
                                                    NP-{{ str_pad($orden->notaPedido->Nro ?? '', 4, '0', STR_PAD_LEFT) }}
                                                </a>
                                            @else
                                                <span style="font-size: 0.85rem;">Sin nota relacionada</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if($orden->pdf_path)
                                                <a href="{{ asset('storage/' . $orden->pdf_path) }}" target="_blank" class="btn btn-xs btn-outline-primary" style="font-size: 0.75rem;">
                                                    <i class="fas fa-file-pdf pdf-icon"></i>
                                                </a>
                                            @else
                                                <span class="text-muted" style="font-size: 0.85rem;">Sin PDF</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <div class="acciones-container">
                                                <a href="{{ route('obras.ordenes-servicio.show', [$obra->id, $orden->id]) }}" class="btn btn-sm btn-outline-primary btn-ver" title="Ver orden">
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
                    @else
                        <div class="alert alert-info" style="font-size: 0.9rem;">
                            No hay órdenes de servicio en tu bandeja de entrada.
                        </div>
                    @endif
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
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

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
            { responsivePriority: 9, targets: 8 },
            { orderable: false, targets: 9 }, // Deshabilitar ordenamiento en la columna de acciones
            { className: 'text-center', targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] } // Centrar contenido en columnas específicas
        ],
        order: [[2, 'desc']], // Ordenar por fecha de emisión (columna 2) de forma descendente por defecto
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            buttons: {
                copyTitle: 'Copiar al portapapeles',
                copySuccess: {
                    _: 'Copiadas %d filas al portapapeles',
                    1: 'Copiada 1 fila al portapapeles'
                }
            }
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Excluir la columna de acciones
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
                    doc.content[1].table.widths = ['8%', '15%', '10%', '10%', '10%', '8%', '12%', '12%', '8%', '7%'];
                    doc.styles.tableHeader.alignment = 'center';
                    doc.defaultStyle.alignment = 'center';
                    doc.pageMargins = [20, 20, 20, 20];
                    doc.content[0].text = 'Bandeja de Órdenes de Servicio - Obra: {{ $obra->nombre }}';
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
                    $(win.document.body).find('h1').text('Bandeja de Órdenes de Servicio - Obra: {{ $obra->nombre }}');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columnas',
                className: 'btn btn-sm btn-dt-custom',
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        ],
        drawCallback: function() {
            // Ajustar el ancho de los badges en dispositivos móviles
            $('.badge-estado, .badge-firmada, .badge-nuevo').each(function() {
                if ($(window).width() < 768) {
                    $(this).css('font-size', '0.7rem');
                } else {
                    $(this).css('font-size', '');
                }
            });

            // Ajustar el estilo de los botones de paginación
            $('.paginate_button').removeClass('btn-dt-custom');
        },
        initComplete: function() {
            // Ajustar el margen del contenedor de botones
            $('.dt-buttons-container').css('margin-bottom', '1rem');
        }
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

    // Ajustar el estilo de los botones de acción
    $('.acciones-container .btn').css('font-size', '0.75rem');

    // Ajustar el estilo de los badges de vencimiento
    $('.vencimiento-vencido').each(function() {
        $(this).append('<br><small class="text-danger">(Vencido)</small>');
    });

    $('.vencimiento-proximo').each(function() {
        $(this).append('<br><small class="text-warning">(Próximo a vencer)</small>');
    });

    $('.vencimiento-normal').each(function() {
        $(this).append('<br><small class="text-info">(Vigente)</small>');
    });
});
</script>
@endsection