@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title m-0" style="font-size: 1.1rem;">Editar Perfil</h3>
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 0.85rem;">
                            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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
                                                @if(!auth()->user()->profile_photo_path)
                                                    <span class="avatar-upload-indicator-large" style="width: 35px; height: 35px; font-size: 1.1rem;">
                                                        <i class="fas fa-camera"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="profile_photo" class="btn btn-sm btn-primary w-100" style="font-size: 0.85rem;">
                                                <i class="fas fa-camera mr-1"></i>
                                                {{ !auth()->user()->profile_photo_path ? 'Subir Foto' : 'Cambiar Foto' }}
                                            </label>
                                            <input type="file" class="form-control-file d-none" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/jpg,image/gif">
                                            @error('profile_photo')
                                                <span class="invalid-feedback d-block" role="alert" style="font-size: 0.8rem;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="form-text text-muted" style="font-size: 0.75rem;">
                                                @if(!auth()->user()->profile_photo_path)
                                                    <span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i> Completa tu perfil</span>
                                                @else
                                                    Formatos: JPG, PNG, GIF. Máx. 2MB
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->

                            <!-- Columna para los datos del perfil -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <ul class="nav nav-pills" style="font-size: 0.85rem;">
                                            <li class="nav-item"><a class="nav-link active" href="#personal" data-toggle="tab" style="font-size: 0.85rem;">Información Personal</a></li>
                                            <li class="nav-item"><a class="nav-link" href="#contact" data-toggle="tab" style="font-size: 0.85rem;">Contacto</a></li>
                                            <li class="nav-item"><a class="nav-link" href="#work" data-toggle="tab" style="font-size: 0.85rem;">Laboral</a></li>
                                        </ul>
                                    </div><!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- Tab Información Personal -->
                                            <div class="active tab-pane" id="personal">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="first_name" style="font-size: 0.85rem; font-weight: bold; display: block;">Nombre</label>
                                                            <input type="text" class="form-control form-control-sm @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" style="font-size: 0.85rem;">
                                                            @error('first_name')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="last_name" style="font-size: 0.85rem; font-weight: bold; display: block;">Apellido</label>
                                                            <input type="text" class="form-control form-control-sm @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" style="font-size: 0.85rem;">
                                                            @error('last_name')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.tab-pane -->

                                            <!-- Tab Contacto -->
                                            <div class="tab-pane" id="contact">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone" style="font-size: 0.85rem; font-weight: bold; display: block;">Teléfono</label>
                                                            <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" style="font-size: 0.85rem;">
                                                            @error('phone')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="email" style="font-size: 0.85rem; font-weight: bold; display: block;">Email</label>
                                                            <input type="email" class="form-control form-control-sm" id="email" value="{{ $user->email }}" disabled style="font-size: 0.85rem;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="address" style="font-size: 0.85rem; font-weight: bold; display: block;">Dirección</label>
                                                            <input type="text" class="form-control form-control-sm @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}" style="font-size: 0.85rem;">
                                                            @error('address')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="city" style="font-size: 0.85rem; font-weight: bold; display: block;">Ciudad</label>
                                                            <input type="text" class="form-control form-control-sm @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $user->city) }}" style="font-size: 0.85rem;">
                                                            @error('city')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="state" style="font-size: 0.85rem; font-weight: bold; display: block;">Estado/Provincia</label>
                                                            <input type="text" class="form-control form-control-sm @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state', $user->state) }}" style="font-size: 0.85rem;">
                                                            @error('state')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="postal_code" style="font-size: 0.85rem; font-weight: bold; display: block;">Código Postal</label>
                                                            <input type="text" class="form-control form-control-sm @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" style="font-size: 0.85rem;">
                                                            @error('postal_code')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="country" style="font-size: 0.85rem; font-weight: bold; display: block;">País</label>
                                                            <input type="text" class="form-control form-control-sm @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $user->country) }}" style="font-size: 0.85rem;">
                                                            @error('country')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.tab-pane -->

                                            <!-- Tab Laboral -->
                                            <div class="tab-pane" id="work">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="organization" style="font-size: 0.85rem; font-weight: bold; display: block;">Organización</label>
                                                            <input type="text" class="form-control form-control-sm @error('organization') is-invalid @enderror" id="organization" name="organization" value="{{ old('organization', $user->organization) }}" style="font-size: 0.85rem;">
                                                            @error('organization')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="position" style="font-size: 0.85rem; font-weight: bold; display: block;">Cargo</label>
                                                            <input type="text" class="form-control form-control-sm @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $user->position) }}" style="font-size: 0.85rem;">
                                                            @error('position')
                                                                <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.tab-pane -->
                                        </div>
                                        <!-- /.tab-content -->
                                    </div><!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('profile.show') }}" class="btn btn-sm btn-secondary mr-2" style="font-size: 0.85rem;">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary" style="font-size: 0.85rem;">
                                <i class="fas fa-save mr-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
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

