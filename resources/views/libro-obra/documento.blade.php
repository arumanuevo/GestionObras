<!-- resources/views/libro-obra/documento.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">
                        @if($documento instanceof \App\Models\Nota)
                            Nota de Pedido NP-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                        @else
                            Orden de Servicio OS-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                        @endif
                    </h3>
                    <div>
                        <a href="{{ route('libro-obra.show', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Libro de Obra
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Obra:</label>
                                <p>{{ $obra->nombre }}</p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Tipo:</label>
                                <p>
                                    @if($documento instanceof \App\Models\Nota)
                                        <span class="badge bg-info">Nota de Pedido</span>
                                    @else
                                        <span class="badge bg-success">Orden de Servicio</span>
                                    @endif
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Número:</label>
                                <p>
                                    @if($documento instanceof \App\Models\Nota)
                                        NP-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                                    @else
                                        OS-{{ str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha:</label>
                                <p>{{ \Carbon\Carbon::parse($documento->fecha)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Estado:</label>
                                <p>
                                    <span class="badge
                                        @if($documento->Estado == 'Pendiente de Firma') badge-warning
                                        @elseif($documento->Estado == 'Firmada') badge-success
                                        @elseif($documento->Estado == 'Rechazada') badge-danger
                                        @elseif($documento->Estado == 'Respondida con OS') badge-info
                                        @else badge-secondary @endif">
                                        {{ $documento->Estado }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Remitente:</label>
                                <p>{{ $documento->creador->name ?? 'Desconocido' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Destinatario:</label>
                                <p>{{ $documento->destinatario->name ?? 'Desconocido' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Contenido del Documento</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Tema:</label>
                                <p>{{ $documento->Tema }}</p>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Descripción:</label>
                                <div class="border p-3 rounded bg-light">
                                    {!! nl2br(e($documento->texto)) !!}
                                </div>
                            </div>

                            @if($documento->Observaciones)
                            <div class="form-group">
                                <label class="font-weight-bold">Observaciones:</label>
                                <div class="border p-3 rounded bg-light">
                                    {!! nl2br(e($documento->Observaciones)) !!}
                                </div>
                            </div>
                            @endif

                            @if($documento->resumen_ai)
                            <div class="form-group">
                                <label class="font-weight-bold">Resumen de IA:</label>
                                <div class="border p-3 rounded bg-light">
                                    {!! nl2br(e($documento->resumen_ai)) !!}
                                </div>
                            </div>
                            @endif

                            @if($documento->pdf_path)
                            <div class="form-group">
                                <label class="font-weight-bold">Documento Adjunto:</label>
                                <div>
                                    <a href="{{ asset('storage/' . $documento->pdf_path) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf me-1"></i> Ver PDF Adjunto
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection