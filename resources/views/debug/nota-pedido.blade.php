@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Depuración de Nota de Pedido</div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Usuario ID</th>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <th>Nombre de usuario</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Roles globales</th>
                                <td>{{ $user->getRoleNames()->implode(', ') }}</td>
                            </tr>
                            <tr>
                                <th>Obra ID</th>
                                <td>{{ $obra->id }}</td>
                            </tr>
                            <tr>
                                <th>Nombre de obra</th>
                                <td>{{ $obra->nombre }}</td>
                            </tr>
                            <tr>
                                <th>Asignado a obra</th>
                                <td>{{ $asignadoAObra ? 'Sí' : 'No' }}</td>
                            </tr>
                            <tr>
                                <th>Rol en obra</th>
                                <td>{{ $rolEnObra ?? 'No asignado' }}</td>
                            </tr>
                            <tr>
                                <th>Tiene rol adecuado</th>
                                <td>{{ $tieneRolAdecuado ? 'Sí' : 'No' }}</td>
                            </tr>
                            <tr>
                                <th>Puede crear (política)</th>
                                <td>{{ $puedeCrear ? 'Sí' : 'No' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
