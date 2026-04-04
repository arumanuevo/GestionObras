<!-- resources/views/libro-obra/search-results.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">Resultados de búsqueda en Libro de Obra: {{ $obra->nombre }}</h3>
                    <div>
                        <a href="{{ route('obras.libro-obra.show', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Libro de Obra
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Resultados para: "<strong>{{ $searchTerm }}</strong>"
                    </div>

                    @if($resultados->isEmpty())
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No se encontraron resultados para tu búsqueda.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Número</th>
                                        <th>Tema</th>
                                        <th>Fecha</th>
                                        <th>Remitente</th>
                                        <th>Destinatario</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resultados as $documento)
                                    <tr>
                                        <td>
                                            @if($documento instanceof \App\Models\Nota)
                                                <span class="badge bg-info">Nota de Pedido</span>
                                            @else
                                                <span class="badge bg-success">Orden de Servicio</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($documento instanceof \App\Models\Nota)
                                                NP-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                                            @else
                                                OS-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($documento->Tema, 30) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($documento->fecha)->format('d/m/Y') }}</td>
                                        <td>{{ $documento->creador->name ?? 'Desconocido' }}</td>
                                        <td>{{ $documento->destinatario->name ?? 'Desconocido' }}</td>
                                        <td>
                                            <span class="badge
                                                @if($documento->Estado == 'Pendiente de Firma') badge-warning
                                                @elseif($documento->Estado == 'Firmada') badge-success
                                                @elseif($documento->Estado == 'Rechazada') badge-danger
                                                @elseif($documento->Estado == 'Respondida con OS') badge-info
                                                @else badge-secondary @endif">
                                                {{ $documento->Estado }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="@if($documento instanceof \App\Models\Nota)
                                                        {{ route('obras.libro-obra.documento', [$obra->id, 'nota', $documento->id]) }}
                                                    @else
                                                        {{ route('obras.libro-obra.documento', [$obra->id, 'orden-servicio', $documento->id]) }}
                                                    @endif"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
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