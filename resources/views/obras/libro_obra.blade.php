@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Libro de Obra: {{ $obra->nombre }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Orden</th>
                                    <th>Tipo</th>
                                    <th>Número</th>
                                    <th>Tema</th>
                                    <th>Fecha de Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($libroObra as $registro)
                            <tr>
                                <td>{{ $registro->orden }}</td>
                                <td>
                                    @if($registro->documento_type == \App\Models\Nota::class)
                                        Nota de Pedido
                                    @elseif($registro->documento_type == \App\Models\OrdenServicio::class)
                                        Orden de Servicio
                                    @else
                                        {{ $registro->documento_type }}
                                    @endif
                                </td>
                                <td>
                                    @if($registro->documento)
                                        {{ $registro->documento->Nro ?? $registro->documento->numero ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($registro->documento)
                                        {{ $registro->documento->Tema ?? $registro->documento->tema ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $registro->fecha_registro ? \Carbon\Carbon::parse($registro->fecha_registro)->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                                <td>
                                    @if($registro->documento)
                                    <a href="
                                        @if($registro->documento_type == \App\Models\Nota::class)
                                            {{ route('obras.notas.show', [$obra->id, $registro->documento_id]) }}
                                        @elseif($registro->documento_type == \App\Models\OrdenServicio::class)
                                            {{ route('obras.ordenes-servicio.show', [$obra->id, $registro->documento_id]) }}
                                        @endif
                                    " class="btn btn-sm btn-outline-primary" title="Ver documento">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay registros en el Libro de Obra.</td>
                            </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
