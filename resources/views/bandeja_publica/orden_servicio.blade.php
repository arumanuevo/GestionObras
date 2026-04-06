@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Orden de Servicio OS-{{ str_pad($ordenServicio->Nro, 4, '0', STR_PAD_LEFT) }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('obras.ordenes-servicio.bandeja', $obra->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la Bandeja de OS
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($ordenServicio->nota_pedido_id)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Nota de Pedido Relacionada</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Contenido de la nota de pedido relacionada -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Número de NP:</label>
                                                <p>
                                                    <a href="{{ route('obras.notas-pedido.show', [$obra->id, $ordenServicio->notaPedido->id]) }}">
                                                        NP-{{ str_pad($ordenServicio->notaPedido->Nro, 4, '0', STR_PAD_LEFT) }}
                                                    </a>
                                                </p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Fecha:</label>
                                                <p>{{ \Carbon\Carbon::parse($ordenServicio->notaPedido->fecha)->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Tema:</label>
                                                <p>{{ $ordenServicio->notaPedido->Tema }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Estado:</label>
                                                <p>
                                                    <span class="badge
                                                        @if($ordenServicio->notaPedido->Estado == 'Respondida con OS') badge-success
                                                        @elseif($ordenServicio->notaPedido->Estado == 'Pendiente de Firma') badge-warning
                                                        @elseif($ordenServicio->notaPedido->Estado == 'Rechazada') badge-danger
                                                        @else badge-secondary @endif">
                                                        {{ $ordenServicio->notaPedido->Estado }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Información detallada de la orden de servicio -->
                    <div class="row mb-4">
                        <!-- Datos principales -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Datos Principales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Obra:</label>
                                        <p>{{ $obra->nombre }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Número de Orden:</label>
                                        <p>OS-{{ str_pad($ordenServicio->Nro, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Tipo:</label>
                                        <p>{{ $ordenServicio->Tipo }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha de Emisión:</label>
                                        <p>{{ \Carbon\Carbon::parse($ordenServicio->fecha)->format('d/m/Y H:i') }}</p>
                                    </div>

                                    @if($ordenServicio->fecha_vencimiento)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha de Vencimiento:</label>
                                        <p>
                                            {{ \Carbon\Carbon::parse($ordenServicio->fecha_vencimiento)->format('d/m/Y H:i') }}
                                            @php
                                                $fechaVencimiento = \Carbon\Carbon::parse($ordenServicio->fecha_vencimiento);
                                                $diasRestantes = $fechaVencimiento->diffInDays(\Carbon\Carbon::now());
                                                $vencido = $fechaVencimiento->isPast();
                                            @endphp
                                            @if($vencido)
                                                <span class="badge badge-danger ml-2">
                                                    Vencido hace {{ abs(floor($diasRestantes)) }} día{{ abs(floor($diasRestantes)) != 1 ? 's' : '' }}
                                                </span>
                                            @else
                                                <span class="badge badge-{{ $diasRestantes <= 3 ? 'warning' : 'info' }} ml-2">
                                                    {{ floor($diasRestantes) }} día{{ floor($diasRestantes) != 1 ? 's' : '' }} restante{{ floor($diasRestantes) != 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="font-weight-bold">Estado:</label>
                                        <p>
                                            <span class="badge
                                                @if($ordenServicio->Estado == 'Emitida') badge-info
                                                @elseif($ordenServicio->Estado == 'Cumplida') badge-success
                                                @elseif($ordenServicio->Estado == 'Incumplida') badge-danger
                                                @else badge-secondary @endif">
                                                {{ $ordenServicio->Estado }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Firmada:</label>
                                        <p>
                                            <span class="badge {{ $ordenServicio->firmada ? 'badge-success' : 'badge-warning' }}">
                                                {{ $ordenServicio->firmada ? 'Sí' : 'No' }}
                                            </span>
                                        </p>
                                    </div>

                                    @if($ordenServicio->cumplida_por)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Cumplida por:</label>
                                        <p>{{ $ordenServicio->cumplida_por }}</p>
                                    </div>
                                    @endif

                                    @if($ordenServicio->fecha_cumplimiento)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha de Cumplimiento:</label>
                                        <p>{{ \Carbon\Carbon::parse($ordenServicio->fecha_cumplimiento)->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Información de usuarios -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Usuarios Relacionados</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Creador:</label>
                                        <div class="d-flex align-items-center">
                                            @if($ordenServicio->creador && $ordenServicio->creador->profile_photo_path)
                                            <img src="{{ asset('storage/' . $ordenServicio->creador->profile_photo_path) }}"
                                                 class="img-circle elevation-2 mr-2"
                                                 alt="{{ $ordenServicio->creador->name }}"
                                                 style="width: 40px; height: 40px;">
                                            @else
                                            <div class="mr-2 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                                 style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($ordenServicio->creador->name ?? 'U', 0, 1)) }}
                                            </div>
                                            @endif
                                            <div>
                                                <p class="mb-0">{{ $ordenServicio->creador->name ?? 'Desconocido' }}</p>
                                                <small class="text-muted">{{ $ordenServicio->creador->email ?? '' }}</small>
                                                @php
                                                    $rolCreador = null;
                                                    if ($ordenServicio->creador && $obra->usuarios->contains($ordenServicio->creador->id)) {
                                                        $pivot = $obra->usuarios->find($ordenServicio->creador->id)->pivot;
                                                        if ($pivot->rol_id) {
                                                            $rolCreador = \App\Models\RoleObra::find($pivot->rol_id);
                                                        }
                                                    }
                                                @endphp
                                                @if($rolCreador)
                                                    <br><small class="text-muted">Rol: {{ $rolCreador->nombre }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Destinatario:</label>
                                        <div class="d-flex align-items-center">
                                            @if($ordenServicio->destinatario && $ordenServicio->destinatario->profile_photo_path)
                                            <img src="{{ asset('storage/' . $ordenServicio->destinatario->profile_photo_path) }}"
                                                 class="img-circle elevation-2 mr-2"
                                                 alt="{{ $ordenServicio->destinatario->name }}"
                                                 style="width: 40px; height: 40px;">
                                            @else
                                            <div class="mr-2 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                                 style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($ordenServicio->destinatario->name ?? 'U', 0, 1)) }}
                                            </div>
                                            @endif
                                            <div>
                                                <p class="mb-0">{{ $ordenServicio->destinatario->name ?? 'Desconocido' }}</p>
                                                <small class="text-muted">{{ $ordenServicio->destinatario->email ?? '' }}</small>
                                                @php
                                                    $rolDestinatario = null;
                                                    if ($ordenServicio->destinatario && $obra->usuarios->contains($ordenServicio->destinatario->id)) {
                                                        $pivot = $obra->usuarios->find($ordenServicio->destinatario->id)->pivot;
                                                        if ($pivot->rol_id) {
                                                            $rolDestinatario = \App\Models\RoleObra::find($pivot->rol_id);
                                                        }
                                                    }
                                                @endphp
                                                @if($rolDestinatario)
                                                    <br><small class="text-muted">Rol: {{ $rolDestinatario->nombre }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de la orden de servicio -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Contenido de la Orden de Servicio</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tema:</label>
                                        <p>{{ $ordenServicio->Tema }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Instrucciones:</label>
                                        <div class="border p-3 rounded bg-light">
                                            {!! nl2br(e($ordenServicio->texto)) !!}
                                        </div>
                                    </div>

                                    <!-- Sección de resumen de IA -->
                                    @if($ordenServicio->resumen_ai || $ordenServicio->texto_pdf)
                                    <div class="form-group mt-4">
                                        <label class="font-weight-bold">Análisis de Documento:</label>

                                        @if($ordenServicio->resumen_ai)
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Resumen Generado por IA</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="border p-3 rounded bg-light">
                                                    {!! nl2br(e($ordenServicio->resumen_ai)) !!}
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        
                                    </div>
                                    @endif

                                    @if($ordenServicio->Observaciones)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Observaciones:</label>
                                        <div class="border p-3 rounded bg-light">
                                            {!! nl2br(e($ordenServicio->Observaciones)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    @if($ordenServicio->pdf_path)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Documento Adjunto:</label>
                                        <div>
                                            <a href="{{ asset('storage/' . $ordenServicio->pdf_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf mr-1"></i> Ver PDF Adjunto
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Información Adicional</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">ID de la Orden:</label>
                                                <p>{{ $ordenServicio->id }}</p>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">ID de la Obra:</label>
                                                <p>{{ $ordenServicio->obra_id }}</p>
                                            </div>

                                            @if($ordenServicio->nota_pedido_id)
                                            <div class="form-group">
                                                <label class="font-weight-bold">ID de la Nota de Pedido:</label>
                                                <p>{{ $ordenServicio->nota_pedido_id }}</p>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Creado:</label>
                                                <p>{{ \Carbon\Carbon::parse($ordenServicio->created_at)->format('d/m/Y H:i') }}</p>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Última actualización:</label>
                                                <p>{{ \Carbon\Carbon::parse($ordenServicio->updated_at)->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($ordenServicio->Estado == 'Emitida' && auth()->user()->id == $ordenServicio->destinatario_id)
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Acciones Disponibles</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <form action="{{ route('obras.ordenes-servicio.cumplir', [$obra->id, $ordenServicio->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check mr-1"></i> Marcar como Cumplida
                                            </button>
                                        </form>

                                        @can('update', $ordenServicio)
                                        <a href="{{ route('obras.ordenes-servicio.edit', [$obra->id, $ordenServicio->id]) }}" class="btn btn-primary">
                                            <i class="fas fa-edit mr-1"></i> Editar Orden
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection