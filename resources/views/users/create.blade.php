@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title m-0" style="font-size: 1.1rem;">Crear Nuevo Usuario</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" style="font-size: 0.85rem;">Nombre</label>
                                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required style="font-size: 0.85rem;">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" style="font-size: 0.85rem;">Email</label>
                                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required style="font-size: 0.85rem;">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" style="font-size: 0.85rem;">Contraseña</label>
                                    <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" required style="font-size: 0.85rem;">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" style="font-size: 0.85rem;">Confirmar Contraseña</label>
                                    <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" required style="font-size: 0.85rem;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="roles" style="font-size: 0.85rem;">Roles</label>
                            <div class="d-flex flex-wrap">
                                @foreach($roles as $role)
                                <div class="form-check mr-3">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role{{ $role->id }}"
                                           @if(old('roles') && in_array($role->id, old('roles'))) checked @endif>
                                    <label class="form-check-label" for="role{{ $role->id }}" style="font-size: 0.85rem;">
                                        <span class="badge badge-pill
                                            @if($role->name == 'admin') badge-danger
                                            @elseif($role->name == 'editor') badge-primary
                                            @else badge-info @endif
                                            px-2 py-1">
                                            {{ $role->name }}
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <span class="invalid-feedback d-block" role="alert" style="font-size: 0.8rem;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="alert alert-info mt-3" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> El nuevo usuario será creado con estado "Pendiente de aprobación" y no podrá acceder al sistema hasta que un administrador lo apruebe.
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary mr-2" style="font-size: 0.85rem;">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary" style="font-size: 0.85rem;">
                                <i class="fas fa-save mr-1"></i> Guardar
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

