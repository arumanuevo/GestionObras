@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Editar Orden de Servicio #{{ $orden->numero }} - Obra: {{ $obra->nombre }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('obras.ordenes-servicio.show', [$obra->id, $orden->id]) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obras.ordenes-servicio.update', [$obra->id, $orden->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero">Número de Orden</label>
                                    <input type="number" class="form-control" id="numero" name="numero" value="{{ old('numero', $orden->numero) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" name="tema" value="{{ old('tema', $orden->tema) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion', $orden->descripcion) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_emision">Fecha de Emisión</label>
                                    <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" value="{{ old('fecha_emision', $orden->fecha_emision ? \Carbon\Carbon::parse($orden->fecha_emision)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                                    <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $orden->fecha_vencimiento ? \Carbon\Carbon::parse($orden->fecha_vencimiento)->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="destinatario_id">Destinatario</label>
                            <select class="form-control" id="destinatario_id" name="destinatario_id" required>
                                <option value="">Seleccionar Destinatario</option>
                                @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ old('destinatario_id', $orden->destinatario_id) == $usuario->id ? 'selected' : '' }}>
                                    {{ $usuario->name }} ({{ $usuario->organization ?? 'Sin organización' }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pdf">PDF (opcional)</label>
                            <input type="file" class="form-control-file" id="pdf" name="pdf" accept=".pdf">
                            @if($orden->pdf_path)
                            <div class="mt-2">
                                <small class="text-muted">Archivo actual: <a href="{{ asset('storage/' . $orden->pdf_path) }}" target="_blank">{{ basename($orden->pdf_path) }}</a></small>
                            </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
