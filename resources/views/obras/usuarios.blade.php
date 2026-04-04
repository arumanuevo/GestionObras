@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestionar Usuarios de la Obra: {{ $obra->nombre }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('obras.show', $obra->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Usuarios Asignados</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Rol en la Obra</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($usuariosObra as $usuario)
                                            <tr>
                                                <td>{{ $usuario->name }}</td>
                                                <td>{{ $usuario->email }}</td>
                                                <td>
                                                    @php
                                                        $obraUsuarioRol = $usuario->pivot;
                                                        $rolObra = \App\Models\RoleObra::find($obraUsuarioRol->rol_id);
                                                    @endphp
                                                    @if($rolObra)
                                                        <span class="badge badge-info">
                                                            {{ $rolObra->nombre }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            Sin rol asignado
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('obras.usuarios.remove', [$obra->id, $usuario->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover usuario">
                                                            <i class="fas fa-user-minus"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No hay usuarios asignados a esta obra.</td>
                                            </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Asignar Nuevo Usuario</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('obras.usuarios.asignar', $obra->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="user_id">Usuario</label>
                                            <select class="form-control" id="user_id" name="user_id" required>
                                                <option value="">Seleccionar usuario</option>
                                                @foreach($usuariosDisponibles as $usuario)
                                                <option value="{{ $usuario->id }}">
                                                    {{ $usuario->name }} ({{ $usuario->email }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="rol_id">Rol en la Obra</label>
                                            <select class="form-control" id="rol_id" name="rol_id" required>
                                                <option value="">Seleccionar rol en la obra</option>
                                                @foreach($rolesObra as $rol)
                                                <option value="{{ $rol->id }}">
                                                    {{ $rol->nombre }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-user-plus"></i> Asignar Usuario
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
