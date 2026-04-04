<?php

// app/Policies/OrdenServicioPolicy.php
// app/Policies/OrdenServicioPolicy.php
namespace App\Policies;

use App\Models\OrdenServicio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrdenServicioPolicy
{
    use HandlesAuthorization;

    public function view(User $user, OrdenServicio $orden)
    {
        return $user->hasAnyRole(['admin', 'Jefe de Obra', 'Inspector Principal', 'Asistente Contratista', 'Asistente Inspección'])
               || $user->id === $orden->creador_id
               || $user->id === $orden->destinatario_id;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'Inspector Principal', 'Asistente Inspección']);
    }

    public function update(User $user, OrdenServicio $orden)
    {
        return $user->hasRole('admin') || $user->id === $orden->creador_id;
    }

    public function firmar(User $user, OrdenServicio $orden)
    {
        return $user->hasRole('Inspector Principal');
    }
}


