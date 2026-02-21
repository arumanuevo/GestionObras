@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title m-0" style="font-size: 1.1rem;">Editar Usuario</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" style="font-size: 0.85rem;">Nombre</label>
                                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required style="font-size: 0.85rem;">
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
                                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required style="font-size: 0.85rem;">
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
                                    <label for="password" style="font-size: 0.85rem;">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                                    <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" style="font-size: 0.85rem;">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert" style="font-size: 0.8rem;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" style="font-size: 0.85rem;">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" style="font-size: 0.85rem;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.85rem;">Rol</label>
                            <div class="d-flex flex-wrap">
                                @foreach($roles as $role)
                                <div class="form-check mr-3">
                                    <input class="form-check-input" type="radio" name="role" value="{{ $role->id }}" id="role{{ $role->id }}"
                                           @if(old('role', $user->roles->first()->id ?? null) == $role->id) checked @endif>
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
                            @error('role')
                                <span class="invalid-feedback d-block" role="alert" style="font-size: 0.8rem;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="approved" name="approved" value="1"
                                       @if(old('approved', $user->approved)) checked @endif>
                                <label class="custom-control-label" for="approved" style="font-size: 0.85rem;">Usuario aprobado</label>
                            </div>
                            <small class="form-text text-muted" style="font-size: 0.8rem;">
                                Si desactivas esta opción, el usuario no podrá acceder al sistema hasta que sea aprobado nuevamente.
                            </small>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary mr-2" style="font-size: 0.85rem;">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary" style="font-size: 0.85rem;">
                                <i class="fas fa-save mr-1"></i> Actualizar
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
