@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Panel de Seguimiento de Notas</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-import"></i> Importar CSV
                        </button>
                        <button id="exportCSV" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-file-export"></i> Exportar CSV
                        </button>
                        <a href="{{ route('notas.create') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-plus"></i> Agregar Nota
                        </a>
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
                    </div>

                    <!-- Tabla de Notas -->
                    <div class="table-responsive p-3" id="tableContainer" style="display: none;">
                        <table id="tabla-notas" class="table table-bordered table-hover table-sm" style="font-size: 0.85rem; width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Nro</th>
                                    <th>Tema</th>
                                    <th>Texto</th>
                                    <th>Fecha</th>
                                    <th>Rta a NP</th>
                                    <th>Respondida por</th>
                                    <th>Observaciones</th>
                                    <th>Estado</th>
                                    <th>Link</th>
                                    <th>PDF</th>
                                    <th>Resumen AI</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notas as $nota)
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
                                        data-estado="{{ $nota->Estado }}">
                                        <td>{{ $nota->id }}</td>
                                        <td>{{ $nota->Tipo }}</td>
                                        <td>{{ $nota->Nro }}</td>
                                        <td>{{ Str::limit($nota->Tema, 30) }}</td>
                                        <td>{{ Str::limit($nota->texto, 50) }}</td>
                                        <td>
                                            @if ($nota->fecha)
                                                @if ($nota->fecha instanceof \Carbon\Carbon || $nota->fecha instanceof \DateTime)
                                                    {{ $nota->fecha->format('d/m/Y') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $nota->Rta_a_NP }}</td>
                                        <td>{{ Str::limit($nota->Respondida_por, 20) }}</td>
                                        <td>{{ Str::limit($nota->Observaciones, 30) }}</td>
                                        <td>
                                            @if($nota->Estado == 'CERRADO')
                                                <span class="badge badge-success p-2">CERRADO</span>
                                            @elseif($nota->Estado == 'ABIERTO')
                                                <span class="badge badge-warning p-2">ABIERTO</span>
                                            @else
                                                <span class="badge badge-secondary p-2">{{ $nota->Estado }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($nota->link)
                                                <a href="{{ $nota->link }}" target="_blank" class="btn btn-xs btn-outline-info p-1">
                                                    <i class="fas fa-link fa-sm"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($nota->pdf_path)
                                                <a href="{{ Storage::url($nota->pdf_path) }}" target="_blank" class="btn btn-xs btn-outline-danger p-1" title="Ver PDF">
                                                    <i class="fas fa-file-pdf fa-sm"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-outline-purple p-1" title="Ver Resumen AI" data-toggle="modal" data-target="#resumenAIModal{{ $nota->id }}">
                                                <i class="fas fa-robot fa-sm"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('notas.edit', $nota->id) }}" class="btn btn-xs btn-outline-warning p-1" title="Editar">
                                                <i class="fas fa-edit fa-sm"></i>
                                            </a>
                                            <form action="{{ route('notas.destroy', $nota->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger p-1" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta nota?')">
                                                    <i class="fas fa-trash fa-sm"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
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
                <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach

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
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered": "(filtrado de _MAX_ entradas totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
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
        "initComplete": function() {
            // Resaltar filas al hacer clic
            $('#tabla-notas tbody').on('click', 'tr', function() {
                $('#tabla-notas tbody tr').removeClass('table-active');
                $(this).addClass('table-active');
            });
        }
    });

    // Función para exportar a CSV
    $('#exportCSV').on('click', function() {
        // Obtener los datos filtrados de la tabla
        const rows = table.rows({ search: 'applied' }).nodes();

        // Crear array para el CSV
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "ID;Tipo;Nro;Tema;Texto;Fecha;Rta a NP;Respondida por;Observaciones;Estado\n";

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
            const estado = $row.data('estado');

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
                escapeCsv(estado)
            ].join(';');

            csvContent += line + "\n";
        });

        // Crear enlace para descarga
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "notas_export_" + new Date().toISOString().slice(0, 10) + ".csv");
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

    // Filtros
    $('#filtro-tema').on('change', function() {
        table.column(3).search(this.value).draw();
    });

    $('#filtro-tipo').on('change', function() {
        table.column(1).search(this.value).draw();
    });
});
</script>
<style>
    #tabla-notas tbody tr.table-active {
        background-color: rgba(0, 123, 255, 0.1) !important;
        font-weight: 500;
    }
    .btn-xs {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
        line-height: 1.2;
    }
    .fa-sm {
        font-size: 0.8em;
    }
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
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
    .btn-outline-purple {
        border-color: #9c27b0;
        color: #9c27b0;
    }
    .btn-outline-purple:hover {
        background-color: #9c27b0;
        color: white;
    }
</style>
@endsection


