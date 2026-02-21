@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Panel de Seguimiento de Notas</h3>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filtro-tema">Filtrar por Tema:</label>
                            <select id="filtro-tema" class="form-control">
                                <option value="">Todos los temas</option>
                                @foreach($temas as $tema)
                                    <option value="{{ $tema }}">{{ $tema }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro-tipo">Filtrar por Tipo:</label>
                            <select id="filtro-tipo" class="form-control">
                                <option value="">Todos los tipos</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Tabla de Notas -->
                    <table id="tabla-notas" class="table table-bordered table-striped">
                        <thead>
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notas as $nota)
                                <tr>
                                    <td>{{ $nota->id }}</td>
                                    <td>{{ $nota->Tipo }}</td>
                                    <td>{{ $nota->Nro }}</td>
                                    <td>{{ $nota->Tema }}</td>
                                    <td>{{ Str::limit($nota->texto, 50) }}</td>
                                    <td>{{ $nota->fecha ? $nota->fecha->format('d/m/Y') : '' }}</td>
                                    <td>{{ $nota->Rta_a_NP }}</td>
                                    <td>{{ $nota->Respondida_por }}</td>
                                    <td>{{ Str::limit($nota->Observaciones, 50) }}</td>
                                    <td>{{ $nota->Estado }}</td>
                                    <td>
                                        @if($nota->link)
                                            <a href="{{ $nota->link }}" target="_blank" class="btn btn-info btn-sm">
                                                <i class="fas fa-link"></i> Link
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('notas.edit', $nota->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form action="{{ route('notas.destroy', $nota->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta nota?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('notas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Agregar Nota
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#tabla-notas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "responsive": true,
    });

    $('#filtro-tema').on('change', function() {
        table.column(3).search(this.value).draw();
    });

    $('#filtro-tipo').on('change', function() {
        table.column(1).search(this.value).draw();
    });
});
</script>
@endsection



