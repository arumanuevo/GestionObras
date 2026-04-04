@extends('layouts.app')

@section('content')
<div class="container-fluid">


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Crear Nueva Nota de Pedidoooo - Obra: {{ $obra->nombre }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('obras.notas-pedido.index', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obras.notas-pedido.store', $obra->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Nro">Número de Nota de Pedido</label>
                                    <input type="number" class="form-control" id="Nro" name="Nro" value="{{ old('Nro') }}" required>
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
                            <label for="Tema">Tema</label>
                            <input type="text" class="form-control" id="Tema" name="Tema" value="{{ old('Tema') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="texto">Descripción</label>
                            <textarea class="form-control" id="texto" name="texto" rows="3">{{ old('texto') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="Observaciones">Observaciones</label>
                            <textarea class="form-control" id="Observaciones" name="Observaciones" rows="2">{{ old('Observaciones') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="destinatario_id">Destinatario</label>
                            <select class="form-control" id="destinatario_id" name="destinatario_id" required>
                                <option value="">Seleccionar destinatario</option>
                                @foreach($obra->usuarios as $usuario)
                                    @if($usuario->hasAnyRole(['Inspector Principal', 'Asistente Inspección']))
                                        <option value="{{ $usuario->id }}" {{ $usuario->id == $destinatarioDefault ? 'selected' : '' }}>
                                            {{ $usuario->name }} ({{ $usuario->getRoleNames()->first() }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pdf">PDF Asociado (opcional)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="pdf" name="pdf" accept=".pdf">
                                <label class="custom-file-label" for="pdf">Seleccionar archivo PDF</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Nota de Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Actualizar el nombre del archivo seleccionado
    $('#pdf').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
});
</script>
@endsection





