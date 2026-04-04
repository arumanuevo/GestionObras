@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title m-0">Detalles de la Nota de Pedido NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}</h3>
                        <div class="card-tools d-flex">
                            @if(auth()->user()->id === $nota->user_id || auth()->user()->hasRole('admin'))
                            <a href="{{ route('obras.notas-pedido.edit', [$obra->id, $nota->id]) }}" class="btn btn-sm btn-outline-primary mr-2">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                            @endif
                            <a href="{{ route('obras.notas-pedido.index', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Número de Nota:</label>
                                <p>NP-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Tema:</label>
                                <p>{{ $nota->Tema }}</p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha:</label>
                                <p>
                                    @if($nota->fecha)
                                        {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($nota->created_at)->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Estado:</label>
                                <p>
                                    <span class="badge
                                        @if($nota->Estado == 'Firmado') badge-success
                                        @elseif($nota->Estado == 'Pendiente de Firma') badge-warning
                                        @else badge-secondary @endif">
                                        {{ $nota->Estado }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Creador:</label>
                                <p>{{ $nota->creador->name ?? 'Desconocido' }}</p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Destinatario:</label>
                                <p>{{ $nota->destinatario->name ?? 'Desconocido' }}</p>
                            </div>
                            @if($nota->firmado_por)
                            <div class="form-group">
                                <label class="font-weight-bold">Firmado por:</label>
                                <p>{{ $nota->firmadoPor->name ?? 'Desconocido' }}</p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha de firma:</label>
                                <p>
                                    @if($nota->firma_fecha)
                                        {{ \Carbon\Carbon::parse($nota->firma_fecha)->format('d/m/Y H:i') }}
                                    @else
                                        Sin fecha de firma
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sección de documento y resumen de IA -->
                    @if($nota->pdf_path || $nota->resumen_ai || $nota->texto_pdf)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Documento y Análisis de IA</h5>
                        </div>
                        <div class="card-body">
                            @if($nota->pdf_path)
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Documento adjunto:</label>
                                <div class="d-flex align-items-center">
                                    <a href="{{ asset('storage/' . $nota->pdf_path) }}" class="btn btn-sm btn-outline-primary mr-2" target="_blank">
                                        <i class="fas fa-file-pdf mr-1"></i> Ver PDF
                                    </a>
                                    @if($nota->usar_resumen_ai)
                                    <span class="badge badge-info">
                                        <i class="fas fa-robot mr-1"></i> Analizado con IA
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($nota->texto_pdf)
                           <!-- <div class="form-group mb-4">
                                <label class="font-weight-bold">Texto extraído del PDF:</label>
                                <div class="card">
                                    <div class="card-body bg-light p-3" style="max-height: 200px; overflow-y: auto;">
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $nota->texto_pdf }}</p>
                                    </div>
                                </div>
                            </div>-->
                            @endif

                            @if($nota->resumen_ai)
                            <div class="form-group">
                                <label class="font-weight-bold">Resumen generado por IA:</label>
                                <div class="card border-left-primary">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-robot mr-1"></i> Resumen de IA
                                            </h6>
                                            @if($nota->usar_resumen_ai)
                                            <span class="badge badge-success">Usado en descripción</span>
                                            @endif
                                        </div>
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $nota->resumen_ai }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Descripción:</label>
                                <div class="card">
                                    <div class="card-body bg-light p-3">
                                        <p style="white-space: pre-wrap;">{{ $nota->texto ?? 'Sin descripción' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($nota->Observaciones)
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Observaciones:</label>
                                <div class="card">
                                    <div class="card-body bg-light p-3">
                                        <p style="white-space: pre-wrap;">{{ $nota->Observaciones }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($nota->Estado == 'Pendiente de Firma' && auth()->user()->id == $nota->destinatario_id)
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Acciones Disponibles</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <!-- Cambiar el enlace por un formulario -->
                                        <form action="{{ route('obras.notas-pedido.firmar', [$obra->id, $nota->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-signature mr-1"></i> Firmar Nota de Pedido
                                            </button>
                                        </form>

                                        <a href="{{ route('obras.ordenes-servicio.create_from_np', [$obra->id, $nota->id]) }}"
                                        class="btn btn-info">
                                            <i class="fas fa-file-alt mr-1"></i> Responder con Orden de Servicio
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