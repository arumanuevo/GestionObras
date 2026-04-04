@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title m-0">Editar Nota de Pedido NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}</h3>
                        <div class="d-flex">
                            <a href="{{ route('obras.notas-pedido.index', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obras.notas-pedido.update', [$obra->id, $nota->id]) }}" method="POST" enctype="multipart/form-data" id="formNota">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Tipo" class="small">Tipo</label>
                                    <input type="text" class="form-control form-control-sm" id="Tipo" name="Tipo" value="{{ $nota->Tipo }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Nro" class="small">Número</label>
                                    <!-- Cambiar name="Nro" a solo id="Nro" para que no se envíe en el formulario -->
                                    <input type="text" class="form-control form-control-sm" id="Nro" value="NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}" readonly>
                                    <!-- Campo oculto con solo el número -->
                                    <input type="hidden" name="Nro" value="{{ $nota->Nro }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Tema" class="small">Tema</label>
                                    <input type="text" class="form-control form-control-sm" id="Tema" name="Tema" value="{{ $nota->Tema }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Estado" class="small">Estado</label>
                                    <select class="form-control form-control-sm" id="Estado" name="Estado" required>
                                        <option value="Pendiente de Firma" {{ $nota->Estado == 'Pendiente de Firma' ? 'selected' : '' }}>Pendiente de Firma</option>
                                        <option value="Firmado" {{ $nota->Estado == 'Firmado' ? 'selected' : '' }}>Firmado</option>
                                        <option value="Rechazado" {{ $nota->Estado == 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="destinatario_id" class="small">Destinatario</label>
                                    <select class="form-control form-control-sm" id="destinatario_id" name="destinatario_id" style="width: 100%;" required>
                                        <option value="">Seleccionar destinatario</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ $nota->destinatario_id == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }} - {{ $usuario->organization ?? 'Sin organización' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="texto" class="small">Descripción</label>
                            <textarea class="form-control form-control-sm" id="texto" name="texto" rows="4" style="resize: none;">{{ $nota->texto }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha" class="small">Fecha</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha" name="fecha"
                                           value="{{ $nota->fecha ? \Carbon\Carbon::parse($nota->fecha)->format('Y-m-d') : \Carbon\Carbon::parse($nota->created_at)->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Rta_a_NP" class="small">Respuesta a NP</label>
                                    <input type="number" class="form-control form-control-sm" id="Rta_a_NP" name="Rta_a_NP" value="{{ $nota->Rta_a_NP }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Respondida_por" class="small">Respondida por</label>
                                    <input type="text" class="form-control form-control-sm" id="Respondida_por" name="Respondida_por" value="{{ $nota->Respondida_por }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link" class="small">Link</label>
                                    <input type="text" class="form-control form-control-sm" id="link" name="link" value="{{ $nota->link }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="Observaciones" class="small">Observaciones</label>
                            <textarea class="form-control form-control-sm" id="Observaciones" name="Observaciones" rows="3" style="resize: none;">{{ $nota->Observaciones }}</textarea>
                        </div>

                        <!-- Sección de PDF con análisis de IA -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Documento PDF y Análisis de IA</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pdf" class="small">PDF Asociado</label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control-file" id="pdf" name="pdf" accept=".pdf">
                                        <label class="custom-file-label" for="pdf">
                                            @if($nota->pdf_path)
                                                {{ basename($nota->pdf_path) }}
                                            @else
                                                Seleccionar archivo PDF
                                            @endif
                                        </label>
                                    </div>
                                    @if($nota->pdf_path)
                                        <div class="mt-2">
                                            <p class="mb-1">Archivo actual:
                                                <a href="{{ Storage::url($nota->pdf_path) }}" target="_blank">{{ basename($nota->pdf_path) }}</a>
                                                <button type="button" class="btn btn-sm btn-link text-danger" id="eliminarPDF">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Controles para el análisis de IA -->
                                <div id="aiControls" @if(!$nota->pdf_path) style="display: none;" @endif>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="aiSummaryLength" class="small">Longitud del resumen (palabras)</label>
                                                <select class="form-control form-control-sm" id="aiSummaryLength">
                                                    <option value="50">50 palabras</option>
                                                    <option value="100" selected>100 palabras</option>
                                                    <option value="200">200 palabras</option>
                                                    <option value="300">300 palabras</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-outline-purple btn-block" id="generateAISummary">
                                                    <i class="fas fa-robot mr-1"></i> Generar Resumen con IA
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Indicador de procesamiento -->
                                    <div id="aiProcessing" style="display: none;">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="spinner-border spinner-border-sm text-purple mr-2" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span>Generando resumen con IA...</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                        </div>
                                    </div>

                                    <!-- Resultado del análisis de IA -->
                                    <div id="aiResult" @if(!$nota->resumen_ai) style="display: none;" @endif>
                                        <div class="form-group mt-3">
                                            <label for="resumen_ai" class="small">Resumen generado por IA:</label>
                                            <div class="card">
                                                <div class="card-body">
                                                    <textarea class="form-control form-control-sm" id="resumen_ai" name="resumen_ai" rows="5" style="resize: none;">{{ $nota->resumen_ai }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="useAISummary">
                                                <label class="custom-control-label" for="useAISummary">
                                                    Usar este resumen en la descripción de la nota
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mensajes de error -->
                                    <div id="aiError" class="alert alert-danger" style="display: none;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-sm btn-primary mr-2" id="btnGuardar">
                                <i class="fas fa-save mr-1"></i> Actualizar
                            </button>
                            <a href="{{ route('obras.notas-pedido.index', $obra->id) }}" class="btn btn-sm btn-outline-secondary" id="btnVolver">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Espera -->
<div class="modal fade" id="modalEspera" tabindex="-1" role="dialog" aria-labelledby="modalEsperaLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-purple" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <h5 class="mt-3" id="modalEsperaTitulo">Procesando...</h5>
                <p id="modalEsperaMensaje">Por favor espere...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmarSalidaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">¡Atención!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tiene cambios sin guardar. ¿Está seguro que desea salir sin guardar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarSalida">Salir sin guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar Select2 para el selector de destinatario
    $('#destinatario_id').select2({
        theme: 'bootstrap4',
        placeholder: "Seleccionar destinatario",
        allowClear: true,
        width: '100%'
    });

    // Bandera para detectar cambios
    let cambiosRealizados = false;
    let formSubmitted = false;
    let currentPdfFile = null;
    let hasPdf = {{ $nota->pdf_path ? 'true' : 'false' }};
    let textoPdfExistente = {!! json_encode($nota->texto_pdf) !!};

    // Función para actualizar el nombre del archivo seleccionado
    function updateFileName(input) {
        var fileName = input.files[0] ? input.files[0].name : 'Seleccionar archivo PDF';
        $(input).next('.custom-file-label').html(fileName);
    }

    // Detectar cambios en los campos
    $('input, select, textarea').on('change', function() {
        cambiosRealizados = true;
    });

    // Manejar la selección de PDF
    $('#pdf').on('change', function() {
        updateFileName(this);
        currentPdfFile = this.files[0];
        cambiosRealizados = true;

        // Mostrar controles de IA solo si hay un PDF seleccionado
        if (currentPdfFile || hasPdf) {
            $('#aiControls').show();
        }
    });

    // Manejar la eliminación del PDF
    $('#eliminarPDF').on('click', function() {
        if (confirm('¿Está seguro que desea eliminar el PDF actual?')) {
            $('#pdf').val('');
            updateFileName($('#pdf')[0]);
            currentPdfFile = null;
            hasPdf = false;
            textoPdfExistente = null;
            $('#aiControls').hide();
            $('#aiResult').hide();
            $('#aiError').hide();
            cambiosRealizados = true;
        }
    });

    // Función para generar el resumen AI
    $('#generateAISummary').on('click', function() {
        // Ocultar mensajes previos
        $('#aiProcessing').hide();
        $('#aiError').hide();

        // Validar que haya un PDF disponible
        if (!currentPdfFile && !hasPdf) {
            $('#aiError').text('No hay un archivo PDF seleccionado o asociado. Por favor suba un PDF primero.').show();
            return;
        }

        // Mostrar indicador de procesamiento
        $('#aiProcessing').show();

        var cantidadPalabras = $('#aiSummaryLength').val();

        // Si hay un nuevo PDF, primero extraer el texto
        if (currentPdfFile) {
            var formData = new FormData();
            formData.append('pdf', currentPdfFile);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('obras.notas-pedido.extraer-texto', ['obra' => $obra->id]) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Una vez extraído el texto, generar el resumen
                        generarResumenAI(response.texto_pdf, cantidadPalabras);
                    } else {
                        $('#aiProcessing').hide();
                        $('#aiError').text('Error al procesar el PDF: ' + (response.message || 'Error desconocido')).show();
                    }
                },
                error: function(xhr) {
                    $('#aiProcessing').hide();
                    $('#aiError').text('Error al procesar el PDF: ' + (xhr.responseJSON?.message || xhr.statusText)).show();
                }
            });
        } else {
            // Para el PDF existente, usar el texto que ya está almacenado
            if (!textoPdfExistente || textoPdfExistente.trim() === "") {
                $('#aiProcessing').hide();
                $('#aiError').text('No hay texto disponible en el PDF para analizar.').show();
                return;
            }

            // Generar el resumen directamente con el texto existente
            generarResumenAI(textoPdfExistente, cantidadPalabras);
        }
    });

    // Función para generar el resumen con IA
    function generarResumenAI(textoPDF, cantidadPalabras) {
        $.ajax({
            url: "{{ route('obras.notas-pedido.generar-resumen', ['obra' => $obra->id, 'nota' => $nota->id]) }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                texto_pdf: textoPDF,
                cantidad_palabras: cantidadPalabras
            },
            success: function(response) {
                $('#aiProcessing').hide();

                if (response.success) {
                    $('#resumen_ai').val(response.resumen);
                    $('#aiResult').show();
                    cambiosRealizados = true;
                } else {
                    $('#aiError').text('Error al generar resumen: ' + (response.message || 'Error desconocido')).show();
                }
            },
            error: function(xhr) {
                $('#aiProcessing').hide();
                $('#aiError').text('Error al generar resumen: ' + (xhr.responseJSON?.message || xhr.statusText)).show();
            }
        });
    }

    // Usar el resumen de IA en la descripción
    $('#useAISummary').on('change', function() {
        if ($(this).is(':checked')) {
            $('#texto').val($('#resumen_ai').val());
            cambiosRealizados = true;
        }
    });

    // Manejar el botón Guardar
    $('#btnGuardar').on('click', function(e) {
        // Validar que se haya seleccionado un destinatario
        if (!$('#destinatario_id').val()) {
            e.preventDefault();
            toastr.error('Por favor seleccione un destinatario');
            return;
        }

        // Validar que el tema no esté vacío
        if (!$('#Tema').val()) {
            e.preventDefault();
            toastr.error('Por favor ingrese un tema para la nota');
            return;
        }

        // Mostrar modal de espera
        $('#modalEsperaTitulo').text('Guardando cambios');
        $('#modalEsperaMensaje').text('Por favor espere mientras se guardan los cambios...');
        $('#modalEspera').modal('show');

        // Enviar el formulario
        formSubmitted = true;
        $('#formNota').submit();
    });

    // Manejar el botón Cancelar/Volver
    $('#btnVolver').on('click', function(e) {
        if (cambiosRealizados && !formSubmitted) {
            e.preventDefault();
            $('#confirmarSalidaModal').modal('show');
        }
    });

    // Manejar la confirmación de salida
    $('#confirmarSalida').on('click', function() {
        window.location.href = '{{ route("obras.notas-pedido.index", $obra->id) }}';
    });

    // Detectar intento de cerrar la pestaña o navegar atrás
    window.addEventListener('beforeunload', function(e) {
        if (cambiosRealizados && !formSubmitted) {
            e.preventDefault();
            e.returnValue = 'Tiene cambios sin guardar. ¿Está seguro que desea salir?';
        }
    });

    // Mostrar el resumen existente si lo hay
    @if($nota->resumen_ai)
        $('#aiResult').show();
    @endif
});
</script>
<style>
    .btn-outline-purple {
        border-color: #9c27b0;
        color: #9c27b0;
    }
    .btn-outline-purple:hover {
        background-color: #9c27b0;
        color: white;
    }
    .text-purple {
        color: #9c27b0;
    }
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.8125rem + 2px) !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.85rem !important;
    }
</style>
@endsection