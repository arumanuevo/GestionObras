@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Nota de Pedido NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}</h3>
                        <div class="card-tools">
                            @php
                                // Obtener el rol del usuario actual en esta obra
                                $userRole = null;
                                $userPivot = $obra->usuarios->find(auth()->user()->id);
                                if ($userPivot) {
                                    $userRole = \App\Models\RoleObra::find($userPivot->pivot->rol_id);
                                }

                                // Determinar si es un rol que crea notas (Jefe de Obra o Asistente del Jefe de Obra)
                                $esCreadorDeNotas = $userRole && in_array($userRole->nombre, ['Jefe de Obra', 'Asistente del Jefe de Obra']);
                            @endphp

                            @if($esCreadorDeNotas)
                                <a href="{{ route('obras.notas-pedido.index', $obra->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Volver a Mis Notas
                                </a>
                                
                            @else
                                <a href="{{ route('obras.notas.bandeja', $obra->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Volver a Bandeja de Entrada
                                </a>
                                
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(!$nota->leida && auth()->user()->id == $nota->destinatario_id)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Esta nota ha sido marcada como leída.
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Obra:</label>
                                <p>{{ $obra->nombre }}</p>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Fecha:</label>
                                <p>{{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Estado:</label>
                                <p>
                                @if($nota->Estado == 'Respondida con OS' && $nota->ordenServicio)
                                    <a href="{{ route('obras.ordenes-servicio.show', [$obra->id, $nota->ordenServicio->id]) }}"
                                    class="badge badge-success"
                                    style="font-size: 0.8rem; text-decoration: none;">
                                        {{ $nota->Estado }}
                                    </a>
                                @else
                                    <span class="badge
                                        @if($nota->Estado == 'Pendiente de Firma') badge-warning
                                        @elseif($nota->Estado == 'Firmada') badge-success
                                        @elseif($nota->Estado == 'Rechazada') badge-danger
                                        @else badge-secondary @endif"
                                        style="font-size: 0.8rem;">
                                        {{ $nota->Estado }}
                                    </span>
                                @endif
                                </p>
                            </div>

                            @if($nota->fecha_lectura && auth()->user()->id == $nota->destinatario_id)
                            <div class="form-group">
                                <label class="font-weight-bold">Leída el:</label>
                                <p>{{ \Carbon\Carbon::parse($nota->fecha_lectura)->format('d/m/Y H:i') }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Remitente:</label>
                                <div class="d-flex align-items-center">
                                    @if($nota->creador && $nota->creador->profile_photo_path)
                                    <img src="{{ asset('storage/' . $nota->creador->profile_photo_path) }}"
                                         class="img-circle elevation-2 mr-2"
                                         alt="{{ $nota->creador->name }}"
                                         style="width: 40px; height: 40px;">
                                    @else
                                    <div class="mr-2 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                         style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($nota->creador->name ?? 'U', 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="mb-0">{{ $nota->creador->name ?? 'Desconocido' }}</p>
                                        <small class="text-muted">{{ $nota->creador->email ?? '' }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Destinatario:</label>
                                <div class="d-flex align-items-center">
                                    @if($nota->destinatario && $nota->destinatario->profile_photo_path)
                                    <img src="{{ asset('storage/' . $nota->destinatario->profile_photo_path) }}"
                                         class="img-circle elevation-2 mr-2"
                                         alt="{{ $nota->destinatario->name }}"
                                         style="width: 40px; height: 40px;">
                                    @else
                                    <div class="mr-2 d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                                         style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($nota->destinatario->name ?? 'U', 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="mb-0">{{ $nota->destinatario->name ?? 'Desconocido' }}</p>
                                        <small class="text-muted">{{ $nota->destinatario->email ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Contenido de la Nota</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tema:</label>
                                        <p>{{ $nota->Tema }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Descripción:</label>
                                        <div class="border p-3 rounded bg-light">
                                            {!! nl2br(e($nota->texto)) !!}
                                        </div>
                                    </div>

                                    @if($nota->Observaciones)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Observaciones:</label>
                                        <div class="border p-3 rounded bg-light">
                                            {!! nl2br(e($nota->Observaciones)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    @if($nota->resumen_ai)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Resumen de IA:</label>
                                        <div class="border p-3 rounded bg-light">
                                            {!! nl2br(e($nota->resumen_ai)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    @if($nota->pdf_path)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Documento Adjunto:</label>
                                        <div>
                                            <a href="{{ asset('storage/' . $nota->pdf_path) }}"
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

                    @if($nota->Estado == 'Pendiente de Firma' && auth()->user()->id == $nota->destinatario_id)
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Acciones Disponibles</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('obras.notas-pedido.firmar', [$obra->id, $nota->id]) }}"
                                        class="btn btn-success">
                                            <i class="fas fa-signature mr-1"></i> Firmar Nota de Pedido
                                        </a>

                                        <a href="{{ route('obras.ordenes-servicio.create_from_np', [$obra->id, $nota->id]) }}"
                                        class="btn btn-info">
                                            <i class="fas fa-file-alt mr-1"></i> Responder Con Orden de Servicio
                                        </a>
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