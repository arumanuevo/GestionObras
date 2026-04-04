@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title m-0">Detalles de la Nota #{{ $nota->id }}</h3>
                        <div class="card-tools d-flex">
                            @if(auth()->user()->id === $nota->user_id || auth()->user()->hasRole('admin'))
                            <a href="{{ route('notas.edit', $nota->id) }}" class="btn btn-sm btn-outline-primary mr-2">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            @endif
                            <a href="{{ route('notas.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al listado
                            </a>
                        </div>
                    </div>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Tipo</label>
                                <p>{{ $nota->Tipo }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Número</label>
                                <p>{{ $nota->Nro }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Tema</label>
                                <p>{{ $nota->Tema }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Estado</label>
                                <span class="badge
                                    @if($nota->Estado == 'CERRADO') badge-success
                                    @elseif($nota->Estado == 'ABIERTO') badge-warning
                                    @else badge-secondary @endif">
                                    {{ $nota->Estado }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Fecha</label>
                                <p>{{ $nota->fecha ? $nota->fecha->format('d/m/Y') : 'No especificada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Respondida por</label>
                                <p>{{ $nota->Respondida_por ?? 'No especificado' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($nota->destinatario)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="small">Destinatario</label>
                                <p>
                                    {{ $nota->destinatario->name }} -
                                    {{ $nota->destinatario->organization ?? 'Sin organización' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="small">Texto</label>
                        <div class="border p-3 rounded bg-light">
                            {{ $nota->texto ?? 'No hay texto' }}
                        </div>
                    </div>

                    @if($nota->Observaciones)
                    <div class="form-group">
                        <label class="small">Observaciones</label>
                        <div class="border p-3 rounded bg-light">
                            {{ $nota->Observaciones }}
                        </div>
                    </div>
                    @endif

                    @if($nota->link)
                    <div class="form-group">
                        <label class="small">Enlace externo</label>
                        <p>
                            <a href="{{ $nota->link }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-link"></i> Ver enlace
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($nota->pdf_path)
                    <div class="form-group">
                        <label class="small">Documento PDF</label>
                        <p>
                            <a href="{{ asset('storage/' . $nota->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> Ver PDF
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($nota->resumen_ai)
                    <div class="form-group">
                        <label class="small">Resumen AI</label>
                        <div class="border p-3 rounded bg-light">
                            {{ $nota->resumen_ai }}
                        </div>
                    </div>
                    @endif
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
@endsection
