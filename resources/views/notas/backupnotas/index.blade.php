@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Panel de Seguimiento de Notas</h3>
                    <div class="card-tools">
                        @if(auth()->user()->hasRole('admin'))
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-import"></i> Importar CSV
                        </button>
                        <button id="exportCSV" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-file-export"></i> Exportar CSV
                        </button>
                        @endif
                        @if(auth()->user()->hasAnyRole(['admin', 'editor']))
                        <a href="{{ route('notas.create') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-plus"></i> Agregar Nota
                        </a>
                        @endif
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <!-- Filtros -->
                    <div class="row p-3 pb-0">
                        <div class="col-md-3">
                            <label for="filtro-tema" class="small">Filtrar por Tema:</label>
                            <select id="filtro-tema" class="form-control form-control-sm">
                                <option value="">Todos los temas</option>
                                @foreach($temas as $tema)
                                    <option value="{{ $tema }}">{{ $tema }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro-tipo" class="small">Filtrar por Tipo:</label>
                            <select id="filtro-tipo" class="form-control form-control-sm">
                                <option value="">Todos los tipos</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro-estado" class="small">Filtrar por Estado:</label>
                            <select id="filtro-estado" class="form-control form-control-sm">
                                <option value="">Todos los estados</option>
                                <option value="ABIERTO">Abierto</option>
                                <option value="CERRADO">Cerrado</option>
                                <option value="PENDIENTE">Pendiente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="busqueda-global" class="small">Buscar:</label>
                            <input type="text" id="busqueda-global" class="form-control form-control-sm" placeholder="Buscar en todos los campos...">
                        </div>
                    </div>

                    <!-- Tabla de Notas -->
                    <div class="table-responsive p-3" id="tableContainer" style="display: none;">
                        <table id="tabla-notas" class="table table-bordered table-hover table-sm" style="font-size: 0.85rem; width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">ID</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Tipo</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Nro</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Tema</th>
                                    <th style="text-align: center; padding: 0.75rem 0.5rem !important;">Texto</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Fecha</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Rta a NP</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Respondida por</th>
                                    <th style="text-align: center; padding: 0.75rem 0.5rem !important;">Observaciones</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Estado</th>
                                    <th style="text-align: center; padding: 0.75rem 0.5rem !important;">Link</th>
                                    <th style="text-align: center; padding: 0.75rem 0.5rem !important;">PDF</th>
                                    @if(auth()->user()->hasRole('admin'))
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Destinatario</th>
                                    @endif
                                    <th style="text-align: left; padding: 0.75rem 0.5rem !important;">Creador</th>
                                    <th style="text-align: center; padding: 0.75rem 0.5rem !important;">Resumen AI</th>
                                    <th style="text-align: center; padding: 0.75rem 0.5rem !important;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notas as $nota)
                                    @php
                                        // Procesar la fecha para el atributo data
                                        $fechaData = '';
                                        if ($nota->fecha) {
                                            try {
                                                if ($nota->fecha instanceof \Carbon\Carbon || $nota->fecha instanceof \DateTime) {
                                                    $fechaData = $nota->fecha->format('d/m/Y');
                                                } else {
                                                    // Intentar parsear si es string
                                                    $fechaData = \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y');
                                                }
                                            } catch (\Exception $e) {
                                                $fechaData = $nota->fecha;
                                            }
                                        }
                                    @endphp
                                    <tr data-id="{{ $nota->id }}"
                                        data-tipo="{{ $nota->Tipo }}"
                                        data-nro="{{ $nota->Nro }}"
                                        data-tema="{{ $nota->Tema }}"
                                        data-texto="{{ $nota->texto }}"
                                        data-fecha="{{ $fechaData }}"
                                        data-rta-np="{{ $nota->Rta_a_NP }}"
                                        data-respondida-por="{{ $nota->Respondida_por }}"
                                        data-observaciones="{{ $nota->Observaciones }}"
                                        data-estado="{{ $nota->Estado }}"
                                        data-link="{{ $nota->link }}"
                                        @if($nota->destinatario)
                                        data-destinatario="{{ $nota->destinatario->name }} - {{ $nota->destinatario->organization ?? 'Sin organización' }}"
                                        @endif
                                        data-creador="{{ $nota->creador ? $nota->creador->name : 'Sin creador' }}">
                                        <td style="text-align: left;">{{ $nota->id }}</td>
                                        <td style="text-align: left;">{{ $nota->Tipo }}</td>
                                        <td style="text-align: left;">{{ $nota->Nro }}</td>
                                        <td style="text-align: left;">{{ Str::limit($nota->Tema, 30) }}</td>
                                        <td class="text-center">
                                            @if(!empty($nota->texto))
                                                <a href="#"
                                                   class="btn-action btn-outline-text"
                                                   title="Ver texto completo"
                                                   data-nota-id="{{ $nota->id }}"
                                                   data-texto-id="{{ $nota->texto }}">
                                                    <i class="fas fa-file-word"></i>
                                                </a>
                                            @else
                                                <button class="btn-action btn-outline-text" title="Sin texto asociado" disabled>
                                                    <i class="fas fa-file-word"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td style="text-align: left;">
                                            @if ($nota->fecha)
                                                @if ($nota->fecha instanceof \Carbon\Carbon || $nota->fecha instanceof \DateTime)
                                                    {{ $nota->fecha->format('d/m/Y') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td style="text-align: left;">{{ $nota->Rta_a_NP }}</td>
                                        <td style="text-align: left;">{{ Str::limit($nota->Respondida_por, 20) }}</td>
                                        <td class="text-center">
                                            @if(!empty($nota->Observaciones))
                                                <button type="button"
                                                        class="btn-action btn-outline-observation"
                                                        title="Ver observaciones"
                                                        data-toggle="modal"
                                                        data-target="#observacionesModal{{ $nota->id }}">
                                                    <i class="fas fa-comment-dots"></i>
                                                </button>
                                            @else
                                                <button class="btn-action btn-outline-observation" title="Sin observaciones" disabled>
                                                    <i class="fas fa-comment-dots"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td style="text-align: left;">
                                            @if($nota->Estado == 'CERRADO')
                                                <span class="badge badge-success p-1" style="font-size: 0.7rem;">CERRADO</span>
                                            @elseif($nota->Estado == 'ABIERTO')
                                                <span class="badge badge-warning p-1" style="font-size: 0.7rem;">ABIERTO</span>
                                            @elseif($nota->Estado == 'PENDIENTE')
                                                <span class="badge badge-secondary p-1" style="font-size: 0.7rem;">PENDIENTE</span>
                                            @else
                                                <span class="badge badge-secondary p-1" style="font-size: 0.7rem;">{{ $nota->Estado }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($nota->link)
                                                <a href="{{ $nota->link }}" target="_blank" class="btn-action btn-outline-info" title="Ver enlace">
                                                    <i class="fas fa-link"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($nota->pdf_path)
                                                <a href="{{ asset('storage/' . $nota->pdf_path) }}" target="_blank" class="btn-action btn-outline-danger" title="Ver PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @endif
                                        </td>
                                        @if(auth()->user()->hasRole('admin'))
                                        <td style="text-align: left;">
                                            @if($nota->destinatario)
                                                <span class="badge badge-info p-1" style="font-size: 0.7rem;" title="{{ $nota->destinatario->name }} - {{ $nota->destinatario->organization ?? 'Sin organización' }}">
                                                    {{ Str::limit($nota->destinatario->name, 15) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary p-1" style="font-size: 0.7rem;">Sin destinatario</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td style="text-align: left;">
                                            @if($nota->creador)
                                                <span class="badge badge-primary p-1" style="font-size: 0.7rem;" title="{{ $nota->creador->name }} - {{ $nota->creador->email }}">
                                                    {{ Str::limit($nota->creador->name, 15) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary p-1" style="font-size: 0.7rem;">Sin creador</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-action btn-outline-purple" title="Ver Resumen AI" data-toggle="modal" data-target="#resumenAIModal{{ $nota->id }}">
                                                <i class="fas fa-robot"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <!-- Botón para ver la nota (disponible para todos los roles) -->
                                                <a href="{{ route('notas.show', $nota->id) }}" class="btn-action btn-outline-primary" title="Ver nota">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Botón para responder (solo si el usuario es el destinatario) -->
                                                @if(auth()->user()->id === $nota->destinatario_id)
                                                <a href="{{ route('notas.edit', $nota->id) }}" class="btn-action btn-outline-success" title="Responder">
                                                    <i class="fas fa-reply"></i>
                                                </a>
                                                @endif

                                                <!-- Botón para editar (solo visible si es el creador o admin) -->
                                                @if(auth()->user()->id === $nota->user_id || auth()->user()->hasRole('admin'))
                                                <a href="{{ route('notas.edit', $nota->id) }}" class="btn-action btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif

                                                <!-- Formulario para eliminar (solo visible si es el creador o admin) -->
                                                @if(auth()->user()->id === $nota->user_id || auth()->user()->hasRole('admin'))
                                                <form action="{{ route('notas.destroy', $nota->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-outline-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta nota?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal para Observaciones -->
                                    <div class="modal fade" id="observacionesModal{{ $nota->id }}" tabindex="-1" role="dialog" aria-labelledby="observacionesModalLabel{{ $nota->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title" id="observacionesModalLabel{{ $nota->id }}">Observaciones - Nota #{{ $nota->id }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p class="card-text">{{ $nota->Observaciones }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('admin') ? '15' : '14' }}" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                No se encontraron notas asignadas a ti.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="loadingIndicator" class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando datos...</p>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->

<!-- Modal para Importar CSV -->
@if(auth()->user()->hasRole('admin'))
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Importar Notas desde CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="csv_file">Seleccionar Archivo CSV</label>
                        <input type="file" class="form-control-file" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                    <div class="alert alert-info">
                        <strong>Instrucciones:</strong>
                        <ul>
                            <li>El archivo debe ser un CSV con punto y coma (;) como delimitador.</li>
                            <li>La primera fila debe contener los encabezados.</li>
                            <li>Formato de fecha: DD/MM/AAAA.</li>
                        </ul>
                    </div>
                    <div id="importProgress" class="progress" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <div id="importMessages"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@foreach($notas as $nota)
<!-- Modal para Resumen AI -->
<div class="modal fade" id="resumenAIModal{{ $nota->id }}" tabindex="-1" role="dialog" aria-labelledby="resumenAIModalLabel{{ $nota->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="resumenAIModalLabel{{ $nota->id }}">Resumen AI - Nota #{{ $nota->id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($nota->resumen_ai)
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">{{ $nota->resumen_ai }}</p>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        No hay resumen AI disponible para esta nota.
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('styles')
<style>
    /* Estilos para la tabla */
    #tabla-notas {
        width: 100% !important;
    }

    /* Estilos para los encabezados de la tabla */
    #tabla-notas th {
        text-align: left !important;
        padding: 0.75rem 0.5rem !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        position: relative;
    }

    /* Eliminar los estilos de ordenamiento de Bootstrap */
    #tabla-notas th[aria-sort] {
        background-image: none !important;
    }

    /* Estilos para las celdas de la tabla */
    #tabla-notas td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    /* Excepciones para columnas que no deben estar centradas */
    #tabla-notas td:nth-child(4), /* Tema */
    #tabla-notas td:nth-child(6), /* Fecha */
    #tabla-notas td:nth-child(7), /* Rta a NP */
    #tabla-notas td:nth-child(8), /* Respondida por */
    #tabla-notas td:nth-child(12) /* Creador */
    @if(auth()->user()->hasRole('admin'))
    ,#tabla-notas td:nth-child(13) /* Destinatario (solo admin) */
    @endif
    {
        text-align: left;
    }

    /* Estilo para los botones de acción */
    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.2rem;
    }

    /* Estilo común para todos los botones de acción */
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 4px;
        border: 1px solid transparent;
        background-color: transparent;
        color: inherit;
        font-size: 0.875rem;
        line-height: 1;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .btn-action i {
        font-size: 0.85rem;
    }

    /* Estilo para cada tipo de botón */
    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
    }

    .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }

    .btn-outline-success:hover {
        background-color: #28a745;
        color: white;
    }

    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }

    .btn-outline-warning:hover {
        background-color: #ffc107;
        color: #212529;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
    }

    .btn-outline-info {
        color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-outline-info:hover {
        background-color: #17a2b8;
        color: white;
    }

    .btn-outline-text {
        color: #20c997;
        border-color: #20c997;
    }

    .btn-outline-text:hover {
        background-color: #20c997;
        color: white;
    }

    .btn-outline-observation {
        color: #6f42c1;
        border-color: #6f42c1;
    }

    .btn-outline-observation:hover {
        background-color: #6f42c1;
        color: white;
    }

    .btn-outline-purple {
        color: #9c27b0;
        border-color: #9c27b0;
    }

    .btn-outline-purple:hover {
        background-color: #9c27b0;
        color: white;
    }

    /* Ajustar el ancho de la columna de acciones */
    #tabla-notas th:last-child,
    #tabla-notas td:last-child {
        width: 160px;
        min-width: 160px;
    }

    /* Ajustar el ancho de las columnas de botones */
    #tabla-notas th:nth-child(5), /* Texto */
    #tabla-notas td:nth-child(5),
    #tabla-notas th:nth-child(9), /* Observaciones */
    #tabla-notas td:nth-child(9),
    #tabla-notas th:nth-child(10), /* Link */
    #tabla-notas td:nth-child(10),
    #tabla-notas th:nth-child(11), /* PDF */
    #tabla-notas td:nth-child(11),
    #tabla-notas th:nth-child(13), /* Resumen AI */
    #tabla-notas td:nth-child(13) {
        width: 40px;
        min-width: 40px;
        text-align: center;
    }

    /* Estilos ESPECÍFICOS para los badges de estado en esta vista */
    .badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.25em 0.5em;
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-secondary {
        background-color: #6c757d;
    }

    .badge-info {
        background-color: #17a2b8;
    }

    .badge-primary {
        background-color: #007bff;
    }

    /* Estilo para el mensaje de "no hay notas" */
    #tabla-notas tbody tr td[colspan] {
        padding: 2rem !important;
    }

    /* Estilo para las celdas de texto */
    #tabla-notas td:nth-child(4) { /* Tema */
        max-width: 200px;
        white-space: normal;
        word-wrap: break-word;
    }

    /* Estilo para las celdas de observaciones */
    #tabla-notas td:nth-child(8) { /* Observaciones */
        max-width: 150px;
        white-space: normal;
        word-wrap: break-word;
    }

    /* Estilo para el contenedor de DataTables */
    .dataTables_wrapper .dataTables_filter input {
        display: none !important; /* Ocultar el campo de búsqueda de DataTables */
    }

    /* Estilo para los controles de paginación */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3em 0.6em;
    }

    /* Estilo para los selectores de longitud */
    .dataTables_wrapper .dataTables_length select {
        padding: 0.375rem 0.75rem;
    }

    /* Estilo para los encabezados de las columnas ordenables */
    #tabla-notas th.sorting,
    #tabla-notas th.sorting_asc,
    #tabla-notas th.sorting_desc {
        padding-right: 20px !important;
        position: relative;
    }

    /* Estilo para los iconos de ordenamiento personalizados */
    #tabla-notas th.sorting:after,
    #tabla-notas th.sorting_asc:after,
    #tabla-notas th.sorting_desc:after {
        position: absolute;
        top: 50%;
        right: 8px;
        display: block;
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        opacity: 0.5;
        transform: translateY(-50%);
        content: "\f0dc"; /* Icono de ordenamiento por defecto */
        font-size: 0.7rem;
    }

    #tabla-notas th.sorting_asc:after {
        content: "\f0de"; /* Icono de ordenamiento ascendente */
    }

    #tabla-notas th.sorting_desc:after {
        content: "\f0dd"; /* Icono de ordenamiento descendente */
    }

    /* Eliminar los estilos de Bootstrap para ordenamiento */
    table.dataTable thead th,
    table.dataTable thead td {
        border-bottom: 1px solid #dee2e6 !important;
    }

    /* Asegurar que los estilos de DataTables tengan prioridad */
    table.dataTable {
        border-collapse: collapse !important;
    }

    /* Estilo para el botón de Agregar Nota */
    .card-tools .btn-outline-success {
        border-width: 1px;
    }

    /* Estilo para botones deshabilitados */
    .btn-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Estilo para el campo de búsqueda global */
    #busqueda-global {
        width: 100%;
    }

    /* Estilo para el contenedor de DataTables más compacto */
    .dataTables_wrapper {
        padding-top: 0.5rem;
    }

    /* Ocultar el label "Show entries" */
    .dataTables_wrapper .dataTables_length label {
        display: none;
    }

    /* Estilo para el selector de cantidad de registros */
    .dataTables_wrapper .dataTables_length {
        margin-left: 0;
        margin-right: 1rem;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Mostrar la tabla cuando los datos estén cargados
    $('#tableContainer').show();
    $('#loadingIndicator').hide();

    // Configuración de DataTables en español
    var table = $('#tabla-notas').DataTable({
        "language": {
            "decimal": "",
            "emptyTable": "No hay notas asignadas a ti",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ notas",
            "infoEmpty": "Mostrando 0 a 0 de 0 notas",
            "infoFiltered": "(filtrado de _MAX_ notas totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ notas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "", // Desactivar el campo de búsqueda de DataTables
            "searchPlaceholder": "Buscar...",
            "zeroRecords": "No se encontraron notas asignadas a ti",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna de manera ascendente",
                "sortDescending": ": activar para ordenar la columna de manera descendente"
            }
        },
        "responsive": true,
        "order": [[0, 'desc']],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "dom": '<"top"l>rt<"bottom"ip><"clear">', // Simplificar el DOM para evitar duplicados y ocultar el campo de búsqueda
        "columnDefs": [
            {
                "targets": [0, 1, 2, 5, 6, 7, 9, 10, 11, 13], // Columnas que deben estar centradas
                "className": "text-center"
            },
            {
                "targets": -1, // Última columna (Acciones)
                "className": "text-center",
                "width": "160px",
                "orderable": false
            },
            {
                "targets": [3, 4, 8, 12], // Columnas de texto
                "className": "text-left"
            }
        ],
        "initComplete": function() {
            // Resaltar filas al hacer clic
            $('#tabla-notas tbody').on('click', 'tr', function() {
                $('#tabla-notas tbody tr').removeClass('table-active');
                $(this).addClass('table-active');
            });

            // Ajustar el ancho de las columnas de botones
            this.api().columns([5, 9, 10, 11, 13]).every(function() {
                var column = this;
                $(column.header()).css('width', '40px');
            });
        },
        "drawCallback": function() {
            // Asegurar que los estilos se mantengan después de cada redibujado
            $('#tabla-notas th').css({
                'text-align': 'left',
                'padding': '0.75rem 0.5rem',
                'font-weight': '600'
            });
        }
    });

    // Añadir filtro por estado
    $('#filtro-estado').on('change', function() {
        if ($(this).val() === "") {
            table.column(9).search("").draw();
        } else {
            table.column(9).search($(this).val()).draw();
        }
    });

    // Filtros existentes
    $('#filtro-tema').on('change', function() {
        table.column(3).search(this.value).draw();
    });

    $('#filtro-tipo').on('change', function() {
        table.column(1).search(this.value).draw();
    });

    // Búsqueda global en todos los campos
    $('#busqueda-global').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();

        // Buscar en todos los datos de las filas
        table.rows().every(function() {
            var row = this;
            var data = row.data();

            // Verificar si el texto de búsqueda aparece en algún campo
            var found = false;

            // Buscar en todos los datos disponibles
            for (var i = 0; i < data.length; i++) {
                if (data[i] && data[i].toString().toLowerCase().includes(searchText)) {
                    found = true;
                    break;
                }
            }

            // Mostrar u ocultar la fila según si se encontró el texto
            if (searchText === '' || found) {
                row.node().style.display = '';
            } else {
                row.node().style.display = 'none';
            }
        });
    });

    // Manejar clic en botón de texto
    $(document).on('click', '.btn-outline-text', function(e) {
        e.preventDefault();
        const notaId = $(this).data('nota-id');
        const textoId = $(this).data('texto-id');

        alert('Preparado para abrir texto de nota ID: ' + notaId + ' con texto ID: ' + textoId);
    });

    // Función para exportar a CSV
    $('#exportCSV').on('click', function() {
        // Obtener los datos filtrados de la tabla
        const rows = table.rows({ search: 'applied' }).nodes();

        // Crear array para el CSV
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "ID;Tipo;Nro;Tema;Texto;Fecha;Rta a NP;Respondida por;Observaciones;Estado;Link";

        @if(auth()->user()->hasRole('admin'))
        csvContent += ";Destinatario";
        @endif

        csvContent += ";Creador;Resumen AI\n";

        // Procesar cada fila
        $(rows).each(function(index, row) {
            const $row = $(row);

            // Obtener datos de los atributos data-*
            const id = $row.data('id');
            const tipo = $row.data('tipo');
            const nro = $row.data('nro');
            const tema = $row.data('tema');
            const texto = $row.data('texto');
            const fecha = $row.data('fecha');
            const rta_np = $row.data('rta-np');
            const respondida_por = $row.data('respondida-por');
            const observaciones = $row.data('observaciones');
            const estado = $row.find('td:nth-child(10)').text().trim();

            // Obtener creador y resumen AI de la fila
            @php
                $creadorCol = auth()->user()->hasRole('admin') ? 13 : 12;
                $resumenCol = auth()->user()->hasRole('admin') ? 14 : 13;
            @endphp

            const creador = $row.find('td:nth-child({{ $creadorCol }})').text().trim();
            const resumenAI = $row.find('td:nth-child({{ $resumenCol }}) button').length > 0 ? 'Sí' : 'No';

            // Obtener destinatario si es admin
            let destinatario = '';
            @if(auth()->user()->hasRole('admin'))
            destinatario = $row.find('td:nth-child(12)').text().trim();
            @endif

            // Escapar valores para CSV
            const escapeCsv = (value) => {
                if (value === null || value === undefined) return '';
                value = value.toString();
                // Escapar comillas dobles
                value = value.replace(/"/g, '""');
                // Si contiene comas, punto y coma o saltos de línea, envolver en comillas
                if (value.includes(';') || value.includes(',') || value.includes('\n')) {
                    return '"' + value + '"';
                }
                return value;
            };

            // Crear línea CSV
            const line = [
                escapeCsv(id),
                escapeCsv(tipo),
                escapeCsv(nro),
                escapeCsv(tema),
                escapeCsv(texto),
                escapeCsv(fecha),
                escapeCsv(rta_np),
                escapeCsv(respondida_por),
                escapeCsv(observaciones),
                escapeCsv(estado),
                escapeCsv('') // Link
            ];

            @if(auth()->user()->hasRole('admin'))
            line.push(escapeCsv(destinatario));
            @endif

            line.push(escapeCsv(creador));
            line.push(escapeCsv(resumenAI));

            csvContent += line.join(';') + "\n";
        });

        // Crear enlace para descarga
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "mis_notas_export_" + new Date().toISOString().slice(0, 10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Manejo del formulario de importación
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        $('#importProgress').show();
        $('#importMessages').html('');
        var progressBar = $('.progress-bar');
        progressBar.css('width', '0%').text('0%');

        var formData = new FormData(this);

        $.ajax({
            url: '{{ route("notas.import") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        progressBar.css('width', percentComplete + '%').text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                progressBar.css('width', '100%').text('100%');
                if (response.success) {
                    $('#importMessages').html(`
                        <div class="alert alert-success">
                            <strong>¡Éxito!</strong> ${response.message}
                        </div>
                    `);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    let errorMessages = '<div class="alert alert-warning"><strong>Advertencia:</strong> ' + response.message + '</div>';
                    if (response.errors && response.errors.length > 0) {
                        errorMessages += '<div class="alert alert-danger mt-2"><strong>Errores:</strong><ul>';
                        response.errors.forEach(function(error) {
                            errorMessages += '<li>' + error + '</li>';
                        });
                        errorMessages += '</ul></div>';
                    }
                    $('#importMessages').html(errorMessages);
                }
            },
            error: function(xhr, status, error) {
                progressBar.css('width', '100%').text('100%');
                let errorMessage = '<div class="alert alert-danger"><strong>Error:</strong> ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                } else {
                    errorMessage += 'Ocurrió un error al importar el archivo.';
                }
                errorMessage += '</div>';
                $('#importMessages').html(errorMessage);
            }
        });
    });
});
</script>
@endsection

