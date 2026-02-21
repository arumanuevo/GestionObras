@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark" style="font-size: 1.1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Cuenta Pendiente de Aprobación
                </div>

                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-hourglass-half fa-4x text-warning mb-3"></i>
                        <h4 class="text-dark mb-3">Tu cuenta está en proceso de aprobación</h4>
                        <p class="text-muted">
                            Tu registro ha sido completado con éxito y tu dirección de email ha sido verificada.
                        </p>
                        <p class="text-muted">
                            Sin embargo, tu cuenta aún no ha sido aprobada por un administrador.
                        </p>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Recibirás una notificación por email cuando tu cuenta sea aprobada.
                    </div>

                    <div class="mb-4">
                        <a href="/" class="btn btn-primary">
                            <i class="fas fa-home mr-1"></i> Volver a la página principal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
