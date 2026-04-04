@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title m-0">Gestión de Usuarios</h3>
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

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="font-size: 0.85rem;">
                            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row mb-3 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm" placeholder="Buscar por nombre, email o rol..." id="searchInput" style="font-size: 0.85rem;">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" style="font-size: 0.85rem;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('users.create') }}" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.375rem 0.75rem;">
                                <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="usersTable" class="table table-bordered table-hover table-sm" style="width: 100%; font-size: 0.85rem;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%; text-align: center; font-size: 0.85rem;">ID</th>
                                    <th style="width: 20%; font-size: 0.85rem;">Nombre</th>
                                    <th style="width: 20%; font-size: 0.85rem;">Email</th>
                                    <th style="width: 15%; text-align: center; font-size: 0.85rem;">Rol del Sistema</th>
                                    <th style="width: 10%; text-align: center; font-size: 0.85rem;">Estado</th>
                                    <th style="width: 10%; text-align: center; font-size: 0.85rem;">Aprobado</th>
                                    <th style="width: 15%; text-align: center; font-size: 0.85rem;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr style="font-size: 0.85rem;">
                                    <td style="text-align: center;">{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" class="rounded-circle" alt="{{ $user->name }}" style="width: 25px; height: 25px; object-fit: cover;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center bg-secondary text-white rounded-circle" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold" style="font-size: 0.85rem;">{{ $user->name }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">Registrado: {{ $user->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        <div>
                                            <div>{{ $user->email }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                @if($user->email_verified_at)
                                                    <i class="fas fa-check-circle text-success mr-1" style="font-size: 0.8rem;"></i> Verificado
                                                @else
                                                    <i class="fas fa-times-circle text-warning mr-1" style="font-size: 0.8rem;"></i> No verificado
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-size: 0.85rem;">
                                        @php
                                            $systemRole = $user->roles->whereIn('name', ['admin', 'user'])->first();
                                        @endphp
                                        @if($systemRole)
                                            <span class="badge badge-pill
                                                @if($systemRole->name == 'admin') badge-danger
                                                @else badge-info @endif
                                                mb-1 d-block" style="font-size: 0.75rem; padding: 0.25em 0.5em;">
                                                {{ ucfirst($systemRole->name) }}
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-secondary" style="font-size: 0.75rem; padding: 0.25em 0.5em;">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-size: 0.85rem;">
                                        @if($user->email_verified_at)
                                            <span class="badge badge-pill badge-success d-inline-flex align-items-center justify-content-center" style="font-size: 0.75rem; padding: 0.25em 0.5em;">
                                                <i class="fas fa-check-circle mr-1" style="font-size: 0.8rem;"></i> Activo
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-warning d-inline-flex align-items-center justify-content-center" style="font-size: 0.75rem; padding: 0.25em 0.5em;">
                                                <i class="fas fa-exclamation-circle mr-1" style="font-size: 0.8rem;"></i> Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-size: 0.85rem;">
                                        @if($user->approved)
                                            <span class="badge badge-pill badge-success d-inline-flex align-items-center justify-content-center" style="font-size: 0.75rem; padding: 0.25em 0.5em;">
                                                <i class="fas fa-check-circle mr-1" style="font-size: 0.8rem;"></i> Aprobado
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-danger d-inline-flex align-items-center justify-content-center" style="font-size: 0.75rem; padding: 0.25em 0.5em;">
                                                <i class="fas fa-times-circle mr-1" style="font-size: 0.8rem;"></i> Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-size: 0.85rem;">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Editar usuario" style="width: 30px; height: 30px; padding: 0;">
                                                <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                            </a>
                                            @if(!$user->approved)
                                                <form action="{{ route('users.approve', $user) }}" method="POST" class="d-inline approve-form" data-user-id="{{ $user->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success approve-btn" title="Aprobar usuario" style="width: 30px; height: 30px; padding: 0;">
                                                        <i class="fas fa-check" style="font-size: 0.8rem;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar usuario" style="width: 30px; height: 30px; padding: 0;">
                                                        <i class="fas fa-trash" style="font-size: 0.8rem;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix" style="font-size: 0.85rem;">
                    <div class="float-left">
                        <span>Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} usuarios</span>
                    </div>
                    <div class="float-right">
                        {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar DataTable con configuración personalizada
    $.fn.dataTable.ext.errMode = 'throw';
    $('#usersTable').DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        order: [[0, 'asc']],
        dom: '<"top"r>t<"clear">', // Eliminamos todos los controles de DataTables
        bLengthChange: false,
        bInfo: false,
        bPaginate: false, // Desactivamos la paginación de DataTables
        columnDefs: [
            { targets: [0, 3, 4, 5, 6], orderable: false, className: 'text-center' }
        ]
    });

    // Funcionalidad de búsqueda personalizada
    $('#searchInput').on('keyup', function() {
        $('#usersTable').DataTable().search(this.value).draw();
    });

    // Manejar el envío del formulario de aprobación
    $('.approve-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var button = form.find('.approve-btn');
        var userId = form.data('user-id');

        // Deshabilitar el botón para evitar múltiples envíos
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i>');

        // Enviar el formulario mediante AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Actualizar la interfaz sin recargar la página
                var row = form.closest('tr');
                row.find('.badge-danger').removeClass('badge-danger').addClass('badge-success');
                row.find('.badge-danger i').removeClass('fa-times-circle').addClass('fa-check-circle');
                row.find('.badge-danger .mr-1').after(' Aprobado');

                // Cambiar el texto del badge
                row.find('.badge-danger').text(' Aprobado');
                row.find('.badge-danger').prepend('<i class="fas fa-check-circle mr-1" style="font-size: 0.8rem;"></i>');

                // Eliminar el formulario de aprobación
                form.remove();

                // Mostrar mensaje de éxito
                toastr.success('Usuario aprobado con éxito');
            },
            error: function(xhr) {
                // Mostrar mensaje de error
                var errorMessage = 'Error al aprobar el usuario';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }

                toastr.error(errorMessage);

                // Restaurar el botón
                button.prop('disabled', false);
                button.html('<i class="fas fa-check" style="font-size: 0.8rem;"></i>');
            }
        });
    });
});
</script>
<style>
    .table-sm td, .table-sm th {
        padding: 0.4rem;
        vertical-align: middle;
    }
    .badge-pill {
        padding: 0.25em 0.5em;
    }
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-group-sm > .btn {
        margin: 0 2px;
    }
    .dataTables_wrapper .dataTables_filter {
        display: none;
    }
    .dataTables_wrapper .dataTables_info {
        display: none;
    }
    .dataTables_wrapper .dataTables_length {
        display: none;
    }
    .dataTables_wrapper .dataTables_paginate {
        display: none;
    }
    .card-title {
        font-size: 1.1rem;
    }
    .input-group-sm > .form-control, .input-group-sm > .input-group-append > .btn {
        font-size: 0.85rem;
    }
    .pagination-sm .page-link {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }
    .btn-primary {
        padding: 0.375rem 0.75rem;
        font-size: 0.85rem;
    }
    .rounded-circle {
        border-radius: 50% !important;
    }
    .pagination {
        margin-bottom: 0;
    }
</style>
@endsection