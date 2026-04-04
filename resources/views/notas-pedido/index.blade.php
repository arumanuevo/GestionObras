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

    /* Estilos adicionales */
    /* Estilo para los badges */
    .badge-estado {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    /* Estilo para los botones de acción */
    .btn-ver {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

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

    /* Estilo para los avatares en la tabla */
    .avatar-table {
        width: 25px;
        height: 25px;
        font-size: 12px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }

    /* Estilo para las filas con notas respondidas */
    .nota-respondida {
        background-color: rgba(40, 167, 69, 0.1);
    }

    /* Estilo para las filas con notas pendientes */
    .nota-pendiente {
        background-color: rgba(255, 193, 7, 0.1);
    }

    /* Estilo para el contenedor de la tabla */
    .table-container {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        background-color: white;
    }

    /* Estilo para los encabezados de la tabla */
    th {
        text-align: center !important;
        vertical-align: middle !important;
    }

    /* Estilo para el contenido de las celdas */
    td {
        text-align: left !important;
        vertical-align: middle !important;
    }

    /* Estilo para las celdas de acciones */
    td:last-child {
        text-align: center !important;
    }

    /* Estilo para las celdas de número */
    td:nth-child(1) {
        text-align: center !important;
    }

    /* Estilo para las celdas de fecha */
    td:nth-child(3) {
        text-align: center !important;
    }

    /* Estilo para las celdas de estado */
    td:nth-child(4) {
        text-align: center !important;
    }

    /* Estilo para los badges de emisor */
    .badge-emisor {
        font-size: 0.7rem;
        padding: 0.2em 0.4em;
        margin-left: 0.3rem;
    }

    /* Estilo para los botones de acción */
    .btn-action {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
    }

    /* Estilo para el grupo de botones */
    .btn-group-action {
        display: flex;
        justify-content: center;
        gap: 4px;
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
                        <h3 class="card-title mb-0" style="font-size: 1.2rem;">Notas de Pedido - Obra: {{ $obra->nombre }}</h3>
                        <div class="card-tools d-flex">
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Obra
                            </a>
                            @can('create', [\App\Models\Nota::class, $obra])
                            <a href="{{ route('obras.notas-pedido.create', $obra->id) }}" class="btn btn-sm btn-primary ml-2">
                                <i class="fas fa-plus mr-1"></i> Nueva Nota de Pedido
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($notasAgrupadas))
                        <div class="alert alert-info" style="font-size: 0.9rem;">
                            No hay notas de pedido registradas para esta obra.
                        </div>
                    @else
                        <div class="alert alert-info mb-3" style="font-size: 0.9rem;">
                            <i class="fas fa-info-circle mr-2"></i>
                            Se muestran todas las notas de pedido de esta obra. Las notas que has creado tú aparecen con un badge azul.
                            Pasa el cursor sobre los iconos de destinatarios para ver los detalles.
                        </div>

                        <div class="table-container">
                            <table id="notasPedidoTable" class="table table-bordered table-hover nowrap" style="width:100%; font-size: 0.85rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th data-priority="1" style="width: 8%;">N°</th>
                                        <th data-priority="2">Tema</th>
                                        <th data-priority="3" style="width: 12%;">Fecha</th>
                                        <th data-priority="4" style="width: 12%;">Estado</th>
                                        <th data-priority="5" style="width: 15%;">Creador</th>
                                        <th data-priority="6" style="width: 15%;">Destinatario(s)</th>
                                        <th data-priority="7" style="width: 12%;" class="no-sort">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notasAgrupadas as $grupo)
                                    @php
                                        $nota = $grupo['nota'];
                                        $destinatarios = $grupo['destinatarios'];
                                        $user = auth()->user();
                                    @endphp
                                    <tr class="
                                        @if($nota->Estado == 'Respondida con OS') nota-respondida
                                        @elseif($nota->Estado == 'Pendiente de Firma') nota-pendiente
                                        @endif">
                                        <td style="vertical-align: middle;">
                                            NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {{ Str::limit($nota->Tema, 30) }}
                                        </td>
                                        <td style="vertical-align: middle;" data-order="{{ $nota->fecha ? \Carbon\Carbon::parse($nota->fecha)->timestamp : \Carbon\Carbon::parse($nota->created_at)->timestamp }}">
                                            @if($nota->fecha)
                                                {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($nota->created_at)->format('d/m/Y') }}
                                            @endif
                                        </td>
                                        <td style="vertical-align: middle;">
                                            @if($nota->Estado == 'Respondida con OS' && $nota->ordenServicio)
                                                <a href="{{ route('obras.ordenes-servicio.show', [$obra->id, $nota->ordenServicio->id]) }}"
                                                class="badge badge-success"
                                                style="font-size: 0.8rem; text-decoration: none;"
                                                data-toggle="tooltip" data-placement="top" title="Ver Orden de Servicio relacionada">
                                                    {{ $nota->Estado }}
                                                </a>
                                            @else
                                                <span class="badge
                                                    @if($nota->Estado == 'Firmado') badge-success
                                                    @elseif($nota->Estado == 'Pendiente de Firma') badge-warning
                                                    @else badge-secondary @endif"
                                                    style="font-size: 0.8rem;"
                                                    data-toggle="tooltip" data-placement="top" title="{{ $nota->Estado }}">
                                                    {{ $nota->Estado }}
                                                </span>
                                            @endif
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div class="d-flex align-items-center">
                                                @if($nota->creador && $nota->creador->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $nota->creador->profile_photo_path) }}"
                                                         class="img-circle elevation-2 mr-2"
                                                         alt="{{ $nota->creador->name }}"
                                                         style="width: 25px; height: 25px;">
                                                @else
                                                    <div class="avatar-table
                                                        @if(auth()->id() == $nota->user_id) bg-primary
                                                        @else bg-info @endif text-white">
                                                        {{ strtoupper(substr($nota->creador->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span style="font-size: 0.85rem;">{{ $nota->creador->name ?? 'Desconocido' }}</span>
                                                @if(auth()->id() == $nota->user_id)
                                                    <span class="badge-emisor badge badge-primary" data-toggle="tooltip" title="Nota creada por ti">
                                                        <i class="fas fa-user-check"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div class="destinatarios-container">
                                                @foreach($destinatarios as $index => $destinatario)
                                                    @php
                                                        $initial = strtoupper(substr($destinatario->name ?? 'U', 0, 1));
                                                        $color = $index % 2 == 0 ? '#28a745' : '#007bff'; // Verde para Inspector, Azul para Asistente
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
                                        <td style="vertical-align: middle;">
                                            <div class="btn-group-action">
                                                <a href="{{ route('obras.notas-pedido.show', [$obra->id, $nota->id]) }}"
                                                   class="btn btn-sm btn-outline-primary btn-action"
                                                   title="Ver nota">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($nota->Estado == 'Pendiente de Firma' && ($user->hasRole('admin') || ($user->id == $nota->destinatario_id)))
                                                <form id="firmar-form-{{ $nota->id }}" action="{{ route('obras.notas-pedido.firmar', [$obra->id, $nota->id]) }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success btn-action"
                                                        title="Firmar nota"
                                                        onclick="event.preventDefault(); document.getElementById('firmar-form-{{ $nota->id }}').submit();">
                                                    <i class="fas fa-signature"></i>
                                                </button>
                                                @endif

                                                @if($user->id == $nota->user_id || $user->hasRole('admin'))
                                                <a href="{{ route('obras.notas-pedido.edit', [$obra->id, $nota->id]) }}"
                                                   class="btn btn-sm btn-outline-warning btn-action"
                                                   title="Editar nota">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Configuración de DataTable
    $.fn.dataTable.ext.errMode = 'throw';
    var table = $('#notasPedidoTable').DataTable({
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        columnDefs: [
            { className: 'control', orderable: false, targets: 0 },
            { responsivePriority: 1, targets: 1 }, // N°
            { responsivePriority: 2, targets: 2 }, // Tema
            { responsivePriority: 3, targets: 3 }, // Fecha
            { responsivePriority: 4, targets: 4 }, // Estado
            { responsivePriority: 5, targets: 5 }, // Creador
            { responsivePriority: 6, targets: 6 }, // Destinatarios
            { orderable: false, targets: 7 },      // Acciones
            { className: 'text-center', targets: [0, 3, 4, 7] } // Centrar columnas específicas
        ],
        order: [[3, 'desc']], // Ordenar por fecha de forma descendente por defecto
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
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-dt-custom',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-dt-custom',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
                customize: function(doc) {
                    doc.content[1].table.widths = ['10%', '20%', '12%', '12%', '18%', '18%', '10%'];
                    doc.styles.tableHeader.alignment = 'center';
                    doc.defaultStyle.alignment = 'left';
                    doc.pageMargins = [20, 20, 20, 20];
                    doc.content[0].text = 'Notas de Pedido - Obra: {{ $obra->nombre }}';
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
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
                customize: function(win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    $(win.document.body).find('h1').text('Notas de Pedido - Obra: {{ $obra->nombre }}');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    $(win.document.body).find('th').css('text-align', 'center');
                    $(win.document.body).find('td').css('text-align', 'left');
                    $(win.document.body).find('td:nth-child(1), td:nth-child(3), td:nth-child(4), td:last-child').css('text-align', 'center');
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columnas',
                className: 'btn btn-sm btn-dt-custom',
                columns: [0, 1, 2, 3, 4, 5, 6]
            }
        ],
        drawCallback: function() {
            // Reactivar tooltips después de redibujar
            $('[data-toggle="tooltip"]').tooltip();
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

    // Ajustar el estilo del select de columnas
    $('.dt-button-collection').addClass('shadow-sm');
});
</script>
@endsection