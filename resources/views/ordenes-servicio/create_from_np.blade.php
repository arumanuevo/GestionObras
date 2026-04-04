@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Crear Orden de Servicio en respuesta a Nota de Pedido</h3>
                </div>

                <div class="card-body">
                    <!-- Información de la Nota de Pedido original -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Nota de Pedido Original</h5>
                        </div>
                        <div class="card-body">
                            <!-- Contenido de la nota de pedido -->
                            <div class="mb-3">
                                <strong>Número:</strong> NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="mb-3">
                                <strong>Tema:</strong> {{ $nota->Tema }}
                            </div>
                            <div class="mb-3">
                                <strong>Contenido:</strong>
                                <div class="border p-3 mt-2 bg-light">
                                    {{ $nota->texto }}
                                </div>
                            </div>
                            @if($nota->resumen_ai)
                            <div class="mb-3">
                                <strong>Resumen de IA:</strong>
                                <div class="border p-3 mt-2 bg-light">
                                    {{ $nota->resumen_ai }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Formulario para crear la Orden de Servicio -->
                    <form action="{{ route('obras.ordenes-servicio.store_from_np', [$obra->id, $nota->id]) }}" method="POST" enctype="multipart/form-data" id="ordenServicioForm">
                        @csrf
                        <input type="hidden" name="obra_id" value="{{ $nota->obra->id }}">
                        <input type="hidden" name="nota_pedido_id" value="{{ $nota->id }}">
                        <input type="hidden" name="Nro" value="{{ $proximoNumero }}">
                        <input type="hidden" id="texto_pdf" name="texto_pdf" value="{{ old('texto_pdf') }}">
                        <input type="hidden" id="resumen_ai" name="resumen_ai" value="{{ old('resumen_ai') }}">
                        <input type="hidden" id="usar_resumen_ai" name="usar_resumen_ai" value="0">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Nro">Número de Orden de Servicio</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="Nro" value="OS-{{ str_pad($proximoNumero, 4, '0', STR_PAD_LEFT) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="Tema">Tema <span class="text-muted">(Respuesta a: "{{ $nota->Tema }}")</span></label>
                            <input type="text" class="form-control" id="Tema" name="Tema"
                                   value="{{ old('Tema', 'Respuesta a NP-' . str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) . ': ' . $nota->Tema) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="texto">Descripción de la Orden de Servicio</label>
                            <textarea class="form-control" id="texto" name="texto" rows="6" required>{{ old('texto') }}</textarea>
                            <small class="form-text text-muted">
                                Incluya instrucciones claras, plazos si aplica, y cualquier requisito específico.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="Observaciones">Observaciones (opcional)</label>
                            <textarea class="form-control" id="Observaciones" name="Observaciones" rows="3">{{ old('Observaciones') }}</textarea>
                        </div>

                        <!-- Sección de PDF con análisis de IA -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Documento PDF y Análisis de IA</h6>
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
                                        Adjunte documentos de apoyo como planos, especificaciones técnicas o normativas aplicables.
                                    </small>
                                    <div id="pdfInfo" class="mt-2" style="display: none;">
                                        <p class="mb-1"><strong>Archivo seleccionado:</strong> <span id="pdfFilename"></span></p>
                                        <div id="pdfWarning" class="alert alert-warning mt-2" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Debe generar un resumen con IA antes de guardar la orden de servicio.
                                        </div>
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
                                                    Incluir este resumen como análisis del documento
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mensajes de error -->
                                    <div id="aiError" class="alert alert-danger" style="display: none;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('obras.notas-pedido.show', [$obra->id, $nota->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la Nota de Pedido
                            </a>
                            <button type="submit" class="btn btn-success" id="submitButton">
                                <i class="fas fa-save mr-1"></i> Enviar Orden de Servicio
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
    let originalText = $('#texto').val();

    // Actualizar el nombre del archivo seleccionado
    $('#pdf').on('change', function(e) {
        if (e.target.files.length > 0) {
            pdfFile = e.target.files[0];
            const fileName = pdfFile.name;
            $('.custom-file-label').html(fileName);
            $('#pdfFilename').text(fileName);
            $('#pdfInfo').show();
            $('#pdfWarning').show();
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
            $('#pdfWarning').hide();
            $('#aiControls').hide();
            $('#aiResult').hide();
            $('#aiError').hide();
            $('#btnRemovePDF').hide();
            hasPDF = false;
            hasAISummary = false;
            $('#texto_pdf').val('');
            $('#resumen_ai').val('');
            $('#usar_resumen_ai').val('0');
        }
    });

    // Botón para eliminar PDF
    $('#btnRemovePDF').on('click', function() {
        pdfFile = null;
        $('#pdf').val('');
        $('.custom-file-label').html('Seleccionar archivo PDF');
        $('#pdfFilename').text('');
        $('#pdfInfo').hide();
        $('#pdfWarning').hide();
        $('#aiControls').hide();
        $('#aiResult').hide();
        $('#aiError').hide();
        $('#btnRemovePDF').hide();
        hasPDF = false;
        hasAISummary = false;
        $('#texto_pdf').val('');
        $('#resumen_ai').val('');
        $('#usar_resumen_ai').val('0');
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
            url: "{{ route('obras.ordenes-servicio.extraer-texto', ['obra' => $obra->id]) }}",
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
            url: "{{ route('obras.ordenes-servicio.generar-resumen', ['obra' => $obra->id, 'orden' => 0]) }}",
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
                    $('#usar_resumen_ai').val('1');
                    $('#pdfWarning').hide();
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

    // Validación del formulario antes de enviar
    $('#ordenServicioForm').on('submit', function(e) {
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
            $('#pdfWarning').show();
            toastr.error('Debe generar un resumen con IA antes de guardar la orden de servicio');
            return false;
        }

        // Mostrar modal de envío
        $('#sendingModal').modal('show');

        // Deshabilitar el botón de submit para evitar múltiples envíos
        $('#submitButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...');

        // Enviar el formulario
        return true;
    });
});
</script>
@endsection