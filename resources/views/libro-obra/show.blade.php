@extends('layouts.app')

@section('styles')
@parent
<!-- Estilos adicionales para DataTables -->
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

    /* Estilo específico para cada tipo de botón */
    .btn-copy {
        border-color: #80bdff !important;
        color: #0d6efd !important;
    }

    .btn-copy:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
        color: #0b5ed7 !important;
    }

    .btn-excel {
        border-color: #7dd181 !important;
        color: #198754 !important;
    }

    .btn-excel:hover {
        background-color: rgba(25, 135, 84, 0.1) !important;
        color: #157347 !important;
    }

    .btn-pdf {
        border-color: #ff7875 !important;
        color: #dc3545 !important;
    }

    .btn-pdf:hover {
        background-color: rgba(220, 53, 69, 0.1) !important;
        color: #bb2d3b !important;
    }

    .btn-print {
        border-color: #80bdff !important;
        color: #0d6efd !important;
    }

    .btn-print:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
        color: #0b5ed7 !important;
    }

    .btn-colvis {
        border-color: #80bdff !important;
        color: #0dcaf0 !important;
    }

    .btn-colvis:hover {
        background-color: rgba(13, 202, 240, 0.1) !important;
        color: #0dcaf0 !important;
    }

    /* Estilos para los badges */
    .badge-documento {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    .badge-estado {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }

    /* Ajuste para los botones de acción */
    .btn-ver {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    /* Ajuste para el contenedor de botones */
    .dt-button-collection {
        background-color: white !important;
        border: 1px solid #dee2e6 !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Ajuste para el dropdown de columnas */
    .dt-button-collection .dt-button {
        display: block;
        width: 100%;
        text-align: left;
        padding: 0.5rem 1rem;
        background-color: transparent !important;
        border: none !important;
        color: #495057 !important;
    }

    .dt-button-collection .dt-button:hover {
        background-color: #f8f9fa !important;
    }

    /* Ajuste para el contenedor de la tabla */
    div.dataTables_wrapper div.dataTables_processing {
        background: rgba(255, 255, 255, 0.9);
        color: #495057;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Ajuste para el input de búsqueda */
    .dataTables_filter label {
        display: flex;
        align-items: center;
    }

    /* Ajuste para la información de la tabla */
    .dataTables_info {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Ajuste para la paginación - SIN RECUADROS */
    .dataTables_paginate .paginate_button {
        padding: 0.3em 0.6em;
        border: none !important;
        border-radius: 0.25rem;
        margin-left: 0.2rem;
        background: transparent !important;
        color: #6c757d !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #0d6efd !important;
        color: white !important;
        border-radius: 0.25rem !important;
    }

    .dataTables_paginate .paginate_button:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    /* Estilo para el select de longitud */
    .dataTables_length {
        margin-bottom: 15px;
    }

    /* Estilo para el contenedor de paginación */
    .dataTables_paginate {
        margin-top: 15px;
    }

    /* Estilo para el card-header */
    .card-header {
        padding: 1rem 1.25rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">Libro de Obra: {{ $obra->nombre }}</h3>
                    <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver a la obra
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Estás viendo el <strong>Libro de Obra</strong> de {{ $obra->nombre }}.
                        Este registro contiene todas las Notas de Pedido y Órdenes de Servicio relacionadas con la obra.
                    </div>

                    <div class="table-responsive">
                        <table id="libroObraTable" class="table table-hover table-striped nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th data-priority="1">Tipo</th>
                                    <th data-priority="2">Número</th>
                                    <th data-priority="3">Tema</th>
                                    <th data-priority="4">Fecha</th>
                                    <th>Remitente</th>
                                    <th>Destinatario</th>
                                    <th data-priority="5">Estado</th>
                                    <th data-priority="6" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentos as $documento)
                                <tr>
                                    <td>
                                        @if($documento instanceof \App\Models\Nota)
                                            <span class="badge bg-info badge-documento">Nota de Pedido</span>
                                        @else
                                            <span class="badge bg-success badge-documento">Orden de Servicio</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($documento instanceof \App\Models\Nota)
                                            NP-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                                        @else
                                            OS-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($documento->Tema, 50) }}</td>
                                    <td data-order="{{ $documento->fecha->timestamp }}">
                                        {{ \Carbon\Carbon::parse($documento->fecha)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>{{ $documento->creador->name ?? 'Desconocido' }}</td>
                                    <td>{{ $documento->destinatario->name ?? 'Desconocido' }}</td>
                                    <td>
                                        <span class="badge badge-estado
                                            @if($documento->Estado == 'Pendiente de Firma') badge-warning
                                            @elseif($documento->Estado == 'Firmada') badge-success
                                            @elseif($documento->Estado == 'Rechazada') badge-danger
                                            @elseif($documento->Estado == 'Respondida con OS') badge-info
                                            @else badge-secondary @endif">
                                            {{ $documento->Estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="@if($documento instanceof \App\Models\Nota)
                                                    {{ route('libro-obra.documento', [$obra->id, 'nota', $documento->id]) }}
                                                @else
                                                    {{ route('libro-obra.documento', [$obra->id, 'orden-servicio', $documento->id]) }}
                                                @endif"
                                           class="btn btn-sm btn-outline-primary btn-ver">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay documentos registrados en el Libro de Obra.</td>
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
    var table = $('#libroObraTable').DataTable({
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
            { responsivePriority: 5, targets: 6 },
            { responsivePriority: 6, targets: 7 },
            { orderable: false, targets: 7 },
            { className: 'text-center', targets: [0, 1, 6, 7] }
        ],
        order: [[3, 'desc']],
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
                className: 'btn btn-sm btn-dt-custom btn-copy',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-dt-custom btn-excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-dt-custom btn-pdf',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
                customize: function(doc) {
                    doc.content[1].table.widths = ['15%', '15%', '25%', '15%', '15%', '15%', '15%'];
                    doc.styles.tableHeader.alignment = 'center';
                    doc.defaultStyle.alignment = 'center';
                    doc.pageMargins = [20, 20, 20, 20];
                    doc.content[0].text = 'Libro de Obra: {{ $obra->nombre }}';
                    doc.content[0].fontSize = 16;
                    doc.content[0].alignment = 'center';
                    doc.content[0].margin = [0, 0, 0, 20];
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-sm btn-dt-custom btn-print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
                customize: function(win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    $(win.document.body).find('h1').text('Libro de Obra: {{ $obra->nombre }}');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columnas',
                className: 'btn btn-sm btn-dt-custom btn-colvis',
                columns: [0, 1, 2, 3, 4, 5, 6]
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