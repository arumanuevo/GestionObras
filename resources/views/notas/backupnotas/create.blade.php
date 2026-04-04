@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title m-0">Crear Nueva Nota</h3>
                        <a href="{{ route('notas.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('notas.store') }}" method="POST" enctype="multipart/form-data" id="formNota">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Tipo" class="small">Tipo</label>
                                    <input type="text" class="form-control form-control-sm" id="Tipo" name="Tipo" value="OS" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Nro" class="small">Número</label>
                                    <input type="number" class="form-control form-control-sm" id="Nro" name="Nro" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Tema" class="small">Tema</label>
                                    <input type="text" class="form-control form-control-sm" id="Tema" name="Tema" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Estado" class="small">Estado</label>
                                    <select class="form-control form-control-sm" id="Estado" name="Estado" required>
                                        <option value="ABIERTO" selected>ABIERTO</option>
                                        <option value="CERRADO">CERRADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para seleccionar destinatario -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="destinatario_id" class="small">Destinatario</label>
                                    <select class="form-control form-control-sm" id="destinatario_id" name="destinatario_id" style="width: 100%;">
                                        <option value="">Seleccionar destinatario (opcional)</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">
                                                {{ $usuario->name }} - {{ $usuario->organization ?? 'Sin organización' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="texto" class="small">Texto</label>
                            <textarea class="form-control form-control-sm" id="texto" name="texto" rows="3" style="resize: none;"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha" class="small">Fecha</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha" name="fecha">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Rta_a_NP" class="small">Rta a NP</label>
                                    <input type="number" class="form-control form-control-sm" id="Rta_a_NP" name="Rta_a_NP">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Respondida_por" class="small">Respondida por</label>
                                    <input type="text" class="form-control form-control-sm" id="Respondida_por" name="Respondida_por">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link" class="small">Link</label>
                                    <input type="text" class="form-control form-control-sm" id="link" name="link">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Observaciones" class="small">Observaciones</label>
                            <textarea class="form-control form-control-sm" id="Observaciones" name="Observaciones" rows="3" style="resize: none;"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="pdf" class="small">PDF Asociado</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="pdf" name="pdf" accept=".pdf">
                                <label class="custom-file-label" for="pdf">Seleccionar archivo PDF</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="texto_pdf" class="small">Texto del PDF</label>
                            <textarea class="form-control form-control-sm" id="texto_pdf" name="texto_pdf" rows="10" style="resize: none;" readonly></textarea>
                        </div>
                        <div class="form-group">
                            <label for="resumen_ai" class="small">Resumen AI</label>
                            <div class="d-flex flex-column">
                                <div class="d-flex mb-2">
                                    <select class="form-control form-control-sm mr-2" id="cantidad_palabras" style="width: 150px;">
                                        <option value="20">20 palabras</option>
                                        <option value="50" selected>50 palabras</option>
                                        <option value="100">100 palabras</option>
                                        <option value="200">200 palabras</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-outline-purple" id="generarResumenAI">
                                        <i class="fas fa-robot mr-1"></i> Generar Resumen AI
                                    </button>
                                </div>
                                <textarea class="form-control form-control-sm" id="resumen_ai" name="resumen_ai" rows="5" style="resize: none;"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-sm btn-primary mr-2" id="btnGuardar">
                                <i class="fas fa-save mr-1"></i> Guardar
                            </button>
                            <a href="{{ route('notas.index') }}" class="btn btn-sm btn-outline-secondary" id="btnVolver">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
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

<!-- Modal de Espera -->
<div class="modal fade" id="modalEspera" tabindex="-1" role="dialog" aria-labelledby="modalEsperaLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-purple" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <h5 class="mt-3" id="modalEsperaTitulo">Procesando...</h5>
                <p id="modalEsperaMensaje">Por favor espere mientras se guarda la nota y se envía la notificación...</p>
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

<!-- Modal de Éxito -->
<div class="modal fade" id="modalExito" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3" style="font-size: 3rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h5 class="mt-2" id="modalExitoTitulo">Nota guardada con éxito</h5>
                <p id="modalExitoMensaje">La nota se ha guardado correctamente.</p>
                @if(session('email_sent'))
                    <p class="text-muted small">Se ha enviado una notificación por email al destinatario.</p>
                @endif
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" id="btnRedirigir">
                    <i class="fas fa-list mr-1"></i> Ver listado de notas
                </button>
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
        placeholder: "Seleccionar destinatario (opcional)",
        allowClear: true,
        width: '100%'
    });

    // Bandera para detectar cambios
    let cambiosRealizados = false;
    let formSubmitted = false;

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
        cambiosRealizados = true;

        if (this.files && this.files[0]) {
            var file = this.files[0];

            // Mostrar modal de espera
            $('#modalEsperaTitulo').text('Subiendo PDF');
            $('#modalEsperaMensaje').text('Procesando el archivo...');
            $('#modalEspera').modal('show');

            var formData = new FormData();
            formData.append('pdf', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("notas.subir-pdf-temporal") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modalEspera').modal('hide');
                    if (response.success) {
                        $('#texto_pdf').val(response.texto_pdf);
                        // Guardar el path temporal en un campo oculto para usarlo al guardar la nota
                        $('#formNota').append('<input type="hidden" name="pdf_path_temp" value="' + response.path + '">');
                        $('#formNota').append('<input type="hidden" name="texto_pdf_temp" value="' + response.texto_pdf + '">');
                    } else {
                        // Mostrar error en el modal
                        $('#modalEsperaTitulo').text('Error');
                        $('#modalEsperaMensaje').text(response.message);
                        $('#modalEspera').modal('show');
                        setTimeout(function() {
                            $('#modalEspera').modal('hide');
                        }, 2000);
                        // Limpiar el input si hay error
                        $('#pdf').val('');
                        updateFileName($('#pdf')[0]);
                    }
                },
                error: function(xhr) {
                    $('#modalEspera').modal('hide');
                    // Mostrar error en el modal
                    $('#modalEsperaTitulo').text('Error');
                    let errorMessage = 'Error al procesar el PDF.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMessage = xhr.responseText;
                    }
                    $('#modalEsperaMensaje').text(errorMessage);
                    $('#modalEspera').modal('show');
                    setTimeout(function() {
                        $('#modalEspera').modal('hide');
                    }, 3000);
                    // Limpiar el input si hay error
                    $('#pdf').val('');
                    updateFileName($('#pdf')[0]);
                }
            });
        }
    });

    // Función para generar el resumen AI
    $('#generarResumenAI').on('click', function() {
        var textoPDF = $('#texto_pdf').val();
        var cantidadPalabras = $('#cantidad_palabras').val();

        if (!textoPDF) {
            // Mostrar error en el modal
            $('#modalEsperaTitulo').text('Advertencia');
            $('#modalEsperaMensaje').text('Primero debes cargar un PDF.');
            $('#modalEspera').modal('show');
            setTimeout(function() {
                $('#modalEspera').modal('hide');
            }, 2000);
            return;
        }

        // Mostrar modal de espera
        $('#modalEsperaTitulo').text('Generando Resumen');
        $('#modalEsperaMensaje').text('Por favor, espere mientras se genera el resumen.');
        $('#modalEspera').modal('show');

        $.ajax({
            url: '{{ route("notas.generar-resumen-ai-temporal") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                texto_pdf: textoPDF,
                cantidad_palabras: cantidadPalabras
            },
            success: function(response) {
                $('#modalEspera').modal('hide');
                if (response.success) {
                    $('#resumen_ai').val(response.resumen);
                    // Guardar el resumen en un campo oculto
                    $('#formNota').append('<input type="hidden" name="resumen_ai_temp" value="' + response.resumen + '">');
                    cambiosRealizados = true;
                } else {
                    // Mostrar error en el modal
                    $('#modalEsperaTitulo').text('Error');
                    $('#modalEsperaMensaje').text(response.message);
                    $('#modalEspera').modal('show');
                    setTimeout(function() {
                        $('#modalEspera').modal('hide');
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                $('#modalEspera').modal('hide');
                // Mostrar error en el modal
                $('#modalEsperaTitulo').text('Error');
                $('#modalEsperaMensaje').text('Error al generar el resumen AI.');
                $('#modalEspera').modal('show');
                setTimeout(function() {
                    $('#modalEspera').modal('hide');
                }, 2000);
            }
        });
    });

    // Manejar el envío del formulario con AJAX para mostrar el modal de espera
    $('#btnGuardar').on('click', function(e) {
        e.preventDefault();
        formSubmitted = true;

        // Validar el formulario antes de enviar
        if (!$('#formNota')[0].checkValidity()) {
            $('#formNota')[0].reportValidity();
            return;
        }

        // Mostrar modal de espera
        $('#modalEsperaTitulo').text('Guardando Nota');
        $('#modalEsperaMensaje').text('Por favor espere mientras se guarda la nota y se envía la notificación...');
        $('#modalEspera').modal('show');

        // Deshabilitar el botón de guardar para evitar múltiples envíos
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');

        // Enviar el formulario mediante AJAX
        $.ajax({
            url: $('#formNota').attr('action'),
            type: 'POST',
            data: new FormData($('#formNota')[0]),
            processData: false,
            contentType: false,
            success: function(response) {
                // Ocultar modal de espera
                $('#modalEspera').modal('hide');

                // Mostrar modal de éxito
                $('#modalExito').modal('show');

                // Configurar el botón de redirección
                $('#btnRedirigir').on('click', function() {
                    window.location.href = response.redirect;
                });
            },
            error: function(xhr) {
                // Ocultar modal de espera
                $('#modalEspera').modal('hide');

                // Habilitar el botón de guardar
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar');

                // Mostrar error en el modal
                $('#modalEsperaTitulo').text('Error');
                let errorMessage = 'Error al guardar la nota.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }
                $('#modalEsperaMensaje').text(errorMessage);
                $('#modalEspera').modal('show');

                // Auto-ocultar el modal después de 3 segundos
                setTimeout(function() {
                    $('#modalEspera').modal('hide');
                }, 3000);
            }
        });
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
        window.location.href = '{{ route("notas.index") }}';
    });

    // Detectar intento de cerrar la pestaña o navegar atrás
    window.addEventListener('beforeunload', function(e) {
        if (cambiosRealizados && !formSubmitted) {
            e.preventDefault();
            e.returnValue = 'Tiene cambios sin guardar. ¿Está seguro que desea salir?';
        }
    });
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
    /* Estilo adicional para el card-header */
    .card-header {
        padding: 0.75rem 1.25rem;
    }
    /* Estilo para el modal de éxito */
    #modalExito .modal-content {
        border: 2px solid #28a745;
    }
    #modalExito .fa-check-circle {
        color: #28a745;
    }
</style>
@endsection





