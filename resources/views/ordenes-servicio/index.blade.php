@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Órdenes de Servicio de la Obra: {{ $obra->nombre }}</h3>
                        <div class="card-tools d-flex">
                            @can('create', \App\Models\OrdenServicio::class)
                            <a href="{{ route('obras.ordenes-servicio.create', $obra->id) }}" class="btn btn-sm btn-success mr-2">
                                <i class="fas fa-plus mr-1"></i> Nueva Orden de Servicio
                            </a>
                            @endcan
                            <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Tema</th>
                                    <th>Fecha de Emisión</th>
                                    <th>Fecha de Vencimiento</th>
                                    <th>Estado</th>
                                    <th>Firmada</th>
                                    <th>Destinatario</th>
                                    <th>Creador</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($ordenes as $orden)
                            <tr>
                                <td>{{ $orden->id }}</td>
                                <td>OS-{{ str_pad($orden->Nro ?? $orden->numero, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ Str::limit($orden->Tema ?? $orden->tema, 30) }}</td>
                                <td>
                                    @if(isset($orden->fecha) && $orden->fecha)
                                        {{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}
                                    @elseif(isset($orden->fecha_emision) && $orden->fecha_emision)
                                        {{ \Carbon\Carbon::parse($orden->fecha_emision)->format('d/m/Y') }}
                                    @else
                                        Sin fecha
                                    @endif
                                </td>
                                <td>
                                @if(isset($orden->fecha_vencimiento) && $orden->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($orden->fecha_vencimiento)->format('d/m/Y') }}
                                    @php
                                        $fechaVencimiento = \Carbon\Carbon::parse($orden->fecha_vencimiento);
                                        $diasRestantes = $fechaVencimiento->diffInDays(\Carbon\Carbon::now());
                                        $vencido = $fechaVencimiento->isPast();
                                    @endphp
                                    @if($vencido)
                                        <br><small class="text-danger">(Vencido hace {{ abs(floor($diasRestantes)) }} día{{ abs(floor($diasRestantes)) != 1 ? 's' : '' }})</small>
                                    @elseif($diasRestantes <= 3)
                                        <br><small class="text-warning">(Vence en {{ floor($diasRestantes) }} día{{ floor($diasRestantes) != 1 ? 's' : '' }})</small>
                                    @else
                                        <br><small class="text-info">({{ floor($diasRestantes) }} día{{ floor($diasRestantes) != 1 ? 's' : '' }} restantes)</small>
                                    @endif
                                @else
                                    Sin fecha
                                @endif
                            </td>
                                <td>
                                    <span class="badge
                                        @if(($orden->Estado ?? $orden->estado) == 'Cumplida') badge-success
                                        @elseif(($orden->Estado ?? $orden->estado) == 'Firmada' || ($orden->Estado ?? $orden->estado) == 'Firmado') badge-success
                                        @elseif(($orden->Estado ?? $orden->estado) == 'Pendiente de Firma') badge-warning
                                        @elseif(($orden->Estado ?? $orden->estado) == 'Incumplida') badge-danger
                                        @else badge-secondary @endif">
                                        {{ $orden->Estado ?? $orden->estado ?? 'Sin estado' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ ($orden->firmada ?? false) ? 'badge-success' : 'badge-warning' }}">
                                        {{ ($orden->firmada ?? false) ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ $orden->destinatario->name ?? 'Sin destinatario' }}</td>
                                <td>{{ $orden->creador->name ?? 'Sin creador' }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('obras.ordenes-servicio.show', [$obra->id, $orden->id]) }}" class="btn btn-sm btn-outline-primary mr-1" title="Ver orden">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $orden)
                                        <!--<a href="{{ route('obras.ordenes-servicio.edit', [$obra->id, $orden->id]) }}" class="btn btn-sm btn-outline-warning mr-1" title="Editar orden">
                                            <i class="fas fa-edit"></i>
                                        </a>-->
                                        @endcan
                                        @if(($orden->Estado ?? $orden->estado) == 'Pendiente de Firma' && auth()->user()->id == $orden->destinatario_id)
                                        <form action="{{ route('obras.ordenes-servicio.firmar', [$obra->id, $orden->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Firmar orden">
                                                <i class="fas fa-signature"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No hay órdenes de servicio registradas para esta obra.</td>
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