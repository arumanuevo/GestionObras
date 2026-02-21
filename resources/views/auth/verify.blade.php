@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="font-size: 1.1rem;">Verificar tu dirección de email</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert" style="font-size: 0.85rem;">
                            Se ha enviado un nuevo enlace de verificación a tu dirección de email.
                        </div>
                    @endif

                    <p style="font-size: 0.85rem;">
                        Antes de continuar, por favor verifica tu dirección de email haciendo clic en el enlace que te hemos enviado.
                    </p>
                    <p style="font-size: 0.85rem;">
                        Si no recibiste el email,
                    </p>

                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline" style="font-size: 0.85rem;">
                            haz clic aquí para solicitar otro
                        </button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
