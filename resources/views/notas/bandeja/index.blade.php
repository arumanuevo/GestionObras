@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Bandeja de Entrada - Notas de Pedido</h3>
                        <div class="card-tools">
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la Obra
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Obra: {{ $obra->nombre }}</h4>
                                <div>
                                    <span class="badge badge-info">
                                        <i class="fas fa-hard-hat mr-1"></i> {{ $obra->estado }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($notasRecibidas->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i> No hay notas de pedido en tu bandeja de entrada.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Tema</th>
                                    <th>Remitente</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notasRecibidas as $nota)
                                <tr class="{{ !$nota->leida ? 'table-active font-weight-bold' : '' }}">
                                    <td>NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div style="max-width: 200px; white-space: normal;">
                                            {{ Str::limit($nota->Tema, 50) }}
                                            @if(!$nota->leida)
                                                <span class="badge badge-danger ml-1">Nuevo</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($nota->creador && $nota->creador->profile_photo_path)
                                            <img src="{{ asset('storage/' . $nota->creador->profile_photo_path) }}"
                                                 class="img-circle elevation-2 mr-2"
                                                 alt="{{ $nota->creador->name }}"
                                                 style="width: 30px; height: 30px;">
                                            @else
                                            <div class="mr-2 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                                 style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ strtoupper(substr($nota->creador->name ?? 'U', 0, 1)) }}
                                            </div>
                                            @endif
                                            <div>
                                                <small class="d-block">{{ $nota->creador->name ?? 'Desconocido' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($nota->Estado == 'Pendiente de Firma') badge-warning
                                            @elseif($nota->Estado == 'Firmada') badge-success
                                            @elseif($nota->Estado == 'Rechazada') badge-danger
                                            @else badge-secondary @endif">
                                            {{ $nota->Estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('obras.notas-pedido.show', [$obra->id, $nota->id]) }}"
                                               class="btn btn-outline-primary"
                                               title="Ver detalles de la nota">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($nota->Estado == 'Pendiente de Firma')
                                            <a href="{{ route('obras.notas-pedido.firmar', [$obra->id, $nota->id]) }}"
                                               class="btn btn-outline-success"
                                               title="Firmar nota">
                                                <i class="fas fa-signature"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $.fn.dataTable.ext.errMode = 'throw';
    $('.table').DataTable({
        responsive: true,
        language: {
            "decimal": "",
            "emptyTable": "No hay notas de pedido en tu bandeja de entrada",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ notas",
            "infoEmpty": "Mostrando 0 a 0 de 0 notas",
            "infoFiltered": "(filtrado de _MAX_ notas totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ notas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron notas coincidentes",
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
        order: [[1, 'desc']] // Ordenar por fecha (columna 1) de forma descendente
    });

    // Mostrar notificación de éxito si existe
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
});
</script>
@endsection