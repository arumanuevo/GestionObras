@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Crear Nueva Orden de Servicio para la Obra: {{ $obra->nombre }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obras.ordenes-servicio.store', $obra->id) }}" method="POST" enctype="multipart/form-data" id="ordenServicioForm">
                        @csrf
                        <input type="hidden" id="texto_pdf" name="texto_pdf">
                        <input type="hidden" id="resumen_ai" name="resumen_ai">
                        <input type="hidden" id="generate_ai_summary" name="generate_ai_summary" value="0">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Nro">Número de Orden de Servicio</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="Nro" value="OS-{{ str_pad($proximoNumero, 4, '0', STR_PAD_LEFT) }}" readonly>
                                        <input type="hidden" name="Nro" value="{{ $proximoNumero }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha">Fecha de Emisión</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="Tema">Tema</label>
                            <input type="text" class="form-control" id="Tema" name="Tema" value="{{ old('Tema') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="texto">Descripción</label>
                            <textarea class="form-control" id="texto" name="texto" rows="6" required>{{ old('texto') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_vencimiento">Fecha de Vencimiento (opcional)</label>
                                    <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}">
                                    <small id="fecha_vencimiento_help" class="form-text text-muted"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Observaciones">Observaciones (opcional)</label>
                                    <textarea class="form-control" id="Observaciones" name="Observaciones" rows="3">{{ old('Observaciones') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Información detallada sobre los destinatarios -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Destinatarios de esta Orden de Servicio</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Esta orden de servicio se enviará automáticamente a los siguientes responsables de la obra:
                                </div>

                                <div class="row">
                                    <!-- Jefe de Obra -->
                                    <div class="col-md-6">
                                        <div class="card border-primary mb-3">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">Jefe de Obra</h6>
                                            </div>
                                            <div class="card-body">
                                                @if($jefeObra)
                                                    <div class="d-flex align-items-center">
                                                        @if($jefeObra->profile_photo_path)
                                                            <img src="{{ asset('storage/' . $jefeObra->profile_photo_path) }}"
                                                                 class="img-circle elevation-2 mr-3"
                                                                 alt="{{ $jefeObra->name }}"
                                                                 style="width: 50px; height: 50px;">
                                                        @else
                                                            <div class="mr-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                                                 style="width: 50px; height: 50px; font-size: 20px;">
                                                                {{ strtoupper(substr($jefeObra->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $jefeObra->name }}</h6>
                                                            <small class="text-muted">{{ $jefeObra->email }}</small>
                                                            <br>
                                                            <small class="text-primary">Rol: Jefe de Obra</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        No hay Jefe de Obra asignado a esta obra
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Asistente Contratista -->
                                    <div class="col-md-6">
                                        <div class="card border-success mb-3">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">Asistente Contratista</h6>
                                            </div>
                                            <div class="card-body">
                                                @if($asistenteContratista)
                                                    <div class="d-flex align-items-center">
                                                        @if($asistenteContratista->profile_photo_path)
                                                            <img src="{{ asset('storage/' . $asistenteContratista->profile_photo_path) }}"
                                                                 class="img-circle elevation-2 mr-3"
                                                                 alt="{{ $asistenteContratista->name }}"
                                                                 style="width: 50px; height: 50px;">
                                                        @else
                                                            <div class="mr-3 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                                                 style="width: 50px; height: 50px; font-size: 20px;">
                                                                {{ strtoupper(substr($asistenteContratista->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $asistenteContratista->name }}</h6>
                                                            <small class="text-muted">{{ $asistenteContratista->email }}</small>
                                                            <br>
                                                            <small class="text-success">Rol: Asistente Contratista</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        No hay Asistente Contratista asignado a esta obra
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(!$jefeObra || !$asistenteContratista)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Advertencia:</strong> No todos los destinatarios necesarios están asignados a esta obra.
                                        La orden de servicio solo se enviará a los responsables disponibles.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Sección de PDF con análisis de IA -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Documento PDF y Análisis de IA <span class="text-danger">*</span></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="pdf">PDF Asociado (opcional)</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="pdf" name="pdf" accept=".pdf">
                                            <label class="custom-file-label" for="pdf">Seleccionar archivo PDF</label>
                                        </div>
                                    </div>
                                    <button type="button" id="btnRemovePDF" class="btn btn-sm btn-outline-danger mt-2" style="display: none;">
                                        <i class="fas fa-trash-alt"></i> Eliminar PDF
                                    </button>
                                    <small class="form-text text-muted">
                                        Suba un documento PDF para adjuntar a la orden de servicio.
                                        <strong class="text-danger">Si sube un PDF, debe generar un resumen con IA antes de guardar.</strong>
                                    </small>
                                    <div id="pdfInfo" class="mt-2" style="display: none;">
                                        <p class="mb-1"><strong>Archivo seleccionado:</strong> <span id="pdfFilename"></span></p>
                                    </div>
                                </div>

                                <!-- Controles para el análisis de IA -->
                                <div id="aiControls" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="aiSummaryLength">Longitud del resumen (palabras)</label>
                                                <select class="form-control" id="aiSummaryLength">
                                                    <option value="50">50 palabras</option>
                                                    <option value="100" selected>100 palabras</option>
                                                    <option value="200">200 palabras</option>
                                                    <option value="300">300 palabras</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group d-flex align-items-end">
                                                <button type="button" class="btn btn-primary btn-block" id="generateAISummary">
                                                    <i class="fas fa-robot mr-1"></i> Generar Resumen con IA
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Indicador de procesamiento -->
                                    <div id="aiProcessing" style="display: none;">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="spinner-border spinner-border-sm text-primary mr-2" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span>Generando resumen con IA...</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                        </div>
                                    </div>

                                    <!-- Resultado del análisis de IA -->
                                    <div id="aiResult" style="display: none;">
                                        <div class="form-group mt-3">
                                            <label for="aiGeneratedSummary">Resumen generado por IA:</label>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div id="aiGeneratedSummary" style="min-height: 150px; border: 1px solid #eee; padding: 10px; border-radius: 5px; background-color: #f8f9fa; white-space: pre-wrap;"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="useAISummary" checked>
                                                <label class="custom-control-label" for="useAISummary">
                                                    Usar este resumen como análisis del documento
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mensajes de error -->
                                    <div id="aiError" class="alert alert-danger" style="display: none;"></div>

                                    <!-- Alerta para resumen obligatorio -->
                                    <div id="aiRequiredAlert" class="alert alert-warning mt-3" style="display: none;">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Debe generar un resumen con IA antes de guardar la orden de servicio.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                <i class="fas fa-save"></i> Enviar Orden de Servicio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de envío -->
<div class="modal fade" id="sendingModal" tabindex="-1" role="dialog" aria-labelledby="sendingModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sendingModalLabel">Enviando Orden de Servicio</h5>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <p>Por favor espere mientras se procesa y envía la orden de servicio...</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Variables globales
    let pdfFile = null;
    let pdfText = '';
    let aiSummary = '';
    let hasPDF = false;
    let hasAISummary = false;

    // Validación de fechas
    const fechaInput = $('#fecha');
    const fechaVencimientoInput = $('#fecha_vencimiento');
    const fechaVencimientoHelp = $('#fecha_vencimiento_help');

    // Establecer fecha mínima para vencimiento
    fechaVencimientoInput.attr('min', fechaInput.val());

    // Cuando cambie la fecha de emisión
    fechaInput.on('change', function() {
        fechaVencimientoInput.attr('min', $(this).val());
        validateFechas();
    });

    // Cuando cambie la fecha de vencimiento
    fechaVencimientoInput.on('change', function() {
        validateFechas();
    });

    function validateFechas() {
        const fechaEmision = new Date(fechaInput.val());
        const fechaVencimiento = fechaVencimientoInput.val() ? new Date(fechaVencimientoInput.val()) : null;

        if (fechaVencimientoInput.val() && fechaVencimiento <= fechaEmision) {
            fechaVencimientoHelp.removeClass('text-muted').addClass('text-danger');
            fechaVencimientoHelp.text('La fecha de vencimiento debe ser posterior a la fecha de emisión');
            return false;
        } else if (fechaVencimientoInput.val()) {
            fechaVencimientoHelp.removeClass('text-danger').addClass('text-muted');
            fechaVencimientoHelp.text('Fecha de vencimiento válida');
            return true;
        } else {
            fechaVencimientoHelp.removeClass('text-danger').addClass('text-muted');
            fechaVencimientoHelp.text('Fecha de vencimiento (opcional)');
            return true;
        }
    }

    // Manejar selección de PDF
    $('#pdf').on('change', function(e) {
        if (e.target.files.length > 0) {
            pdfFile = e.target.files[0];
            const fileName = pdfFile.name;
            $('.custom-file-label').html(fileName);
            $('#pdfFilename').text(fileName);
            $('#pdfInfo').show();
            $('#aiControls').show();
            $('#aiResult').hide();
            $('#aiError').hide();
            $('#btnRemovePDF').show();
            hasPDF = true;
            hasAISummary = false;
        } else {
            pdfFile = null;
            $('.custom-file-label').html('Seleccionar archivo PDF');
            $('#pdfInfo').hide();
            $('#aiControls').hide();
            $('#aiResult').hide();
            $('#aiError').hide();
            $('#btnRemovePDF').hide();
            hasPDF = false;
            hasAISummary = false;
            $('#texto_pdf').val('');
            $('#resumen_ai').val('');
            $('#generate_ai_summary').val('0');
        }
    });

    // Botón para eliminar PDF
    $('#btnRemovePDF').on('click', function() {
        pdfFile = null;
        $('#pdf').val('');
        $('.custom-file-label').html('Seleccionar archivo PDF');
        $('#pdfFilename').text('');
        $('#pdfInfo').hide();
        $('#aiControls').hide();
        $('#aiResult').hide();
        $('#aiError').hide();
        $('#btnRemovePDF').hide();
        hasPDF = false;
        hasAISummary = false;
        $('#texto_pdf').val('');
        $('#resumen_ai').val('');
        $('#generate_ai_summary').val('0');
    });

    // Botón para generar resumen con IA
    $('#generateAISummary').on('click', function() {
        if (!pdfFile) {
            toastr.warning('Por favor seleccione un archivo PDF primero');
            return;
        }

        // Mostrar indicador de procesamiento
        $('#aiProcessing').show();
        $('#aiError').hide();
        $('#aiResult').hide();

        // Crear un FormData para enviar el archivo
        let formData = new FormData();
        formData.append('pdf', pdfFile);

        // Enviar el archivo para extraer texto
        $.ajax({
            url: "/obras/{{ $obra->id }}/ordenes-servicio/extraer-texto",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.texto_pdf) {
                    pdfText = response.texto_pdf;
                    $('#texto_pdf').val(pdfText);
                    // Generar resumen con IA
                    generateAISummary(pdfText);
                } else {
                    $('#aiProcessing').hide();
                    $('#aiError').text('Error al analizar el documento: ' + (response.message || 'Error desconocido')).show();
                }
            },
            error: function(xhr) {
                $('#aiProcessing').hide();
                $('#aiError').text('Error al analizar el documento: ' + (xhr.responseJSON?.message || xhr.statusText)).show();
            }
        });
    });

    // Función para generar resumen con IA
    function generateAISummary(textoPDF) {
        const palabras = $('#aiSummaryLength').val();

        $.ajax({
            url: "/obras/{{ $obra->id }}/ordenes-servicio/0/generar-resumen",
            method: 'POST',
            data: {
                texto_pdf: textoPDF,
                cantidad_palabras: palabras,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#aiProcessing').hide();

                if (response.success && response.resumen) {
                    aiSummary = response.resumen;
                    $('#aiGeneratedSummary').text(aiSummary);
                    $('#resumen_ai').val(aiSummary);
                    $('#aiResult').show();
                    hasAISummary = true;
                    $('#generate_ai_summary').val('1');
                    $('#aiRequiredAlert').hide();
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
        if ($(this).is(':checked') && aiSummary) {
            $('#texto').val(aiSummary);
        }
    });

    // Cambiar longitud del resumen
    $('#aiSummaryLength').on('change', function() {
        if (pdfText) {
            // Volver a generar el resumen con la nueva longitud
            generateAISummary(pdfText);
        }
    });

    // Validación del formulario antes de enviar
    $('#ordenServicioForm').on('submit', function(e) {
        if (!validateFechas()) {
            e.preventDefault();
            toastr.error('La fecha de vencimiento debe ser posterior a la fecha de emisión');
            return false;
        }

        if ($('#Tema').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese un tema para la orden de servicio');
            return false;
        }

        if ($('#texto').val() === '') {
            e.preventDefault();
            toastr.error('Por favor ingrese la descripción de la orden de servicio');
            return false;
        }

        // Validar que si hay PDF, debe haber resumen de IA
        if (hasPDF && !hasAISummary) {
            e.preventDefault();
            $('#aiRequiredAlert').show();
            toastr.error('Debe generar un resumen con IA antes de guardar la orden de servicio');
            return false;
        }

        // Mostrar modal de envío
        $('#sendingModal').modal('show');

        // Deshabilitar el botón de submit para evitar múltiples envíos
        $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');

        // Enviar el formulario
        return true;
    });

    // Mostrar notificación de éxito si existe
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
});
</script>
@endsection