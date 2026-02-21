@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title m-0" style="font-size: 1.1rem;">Mi Perfil</h3>
                    <div class="card-tools">
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit mr-1"></i> Editar Perfil
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 0.85rem;">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <!-- Profile Image -->
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile text-center">
                                    <div class="mx-auto mb-3" style="width: 100px; height: 100px; position: relative;">
                                        <div style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 2px solid #dee2e6; background-color: #f8f9fa; position: relative;">
                                            <img class="img-fluid {{ !auth()->user()->profile_photo_path ? 'default-avatar-large' : '' }}"
                                                 src="{{ auth()->user()->profile_photo_url }}"
                                                 alt="User profile picture"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>

                                    <h3 class="profile-username text-center" style="font-size: 1.1rem;">{{ auth()->user()->full_name }}</h3>

                                    <p class="text-muted text-center" style="font-size: 0.9rem;">{{ auth()->user()->position ?? 'Sin cargo' }}</p>

                                    @if(!auth()->user()->profile_photo_path)
                                        <div class="alert alert-info mt-3 py-2" style="font-size: 0.8rem;">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Completa tu perfil subiendo una foto personal.
                                        </div>
                                    @endif
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->

                            <!-- Información de contacto -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title" style="font-size: 0.9rem;">Información de Contacto</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Email</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->email }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Teléfono</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->phone ?? 'No especificado' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Organización</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->organization ?? 'No especificada' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->

                        <!-- Información Adicional -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title" style="font-size: 0.9rem;">Información Adicional</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Nombre</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->first_name ?? 'No especificado' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Apellido</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->last_name ?? 'No especificado' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Dirección</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->address ?? 'No especificada' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Ciudad</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->city ?? 'No especificada' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Estado/Provincia</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->state ?? 'No especificado' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Código Postal</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->postal_code ?? 'No especificado' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2 border-bottom">
                                                <span style="font-weight: bold; font-size: 0.85rem;">País</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->country ?? 'No especificado' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2">
                                                <span style="font-weight: bold; font-size: 0.85rem;">Cargo</span>
                                                <span style="font-size: 0.85rem;">{{ auth()->user()->position ?? 'No especificado' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
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
