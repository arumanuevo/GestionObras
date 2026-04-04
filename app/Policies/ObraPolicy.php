<?php

// app/Policies/ObraPolicy.php
// app/Policies/ObraPolicy.php
namespace App\Policies;

use App\Models\Obra;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObraPolicy
{
    use HandlesAuthorization;

    /*public function viewAny(User $user)
    {
        return $user->hasAnyRole(['admin', 'Jefe de Obra', 'Inspector Principal', 'Visualizador']);
    }*/

    public function viewAny(User $user)
{
    // Los administradores pueden ver todas las obras
    if ($user->hasRole('admin')) {
        return true;
    }

    // Otros usuarios solo pueden ver las obras en las que participan
    return true; // Esto permite que el controlador maneje la lógica
}

    /*public function view(User $user, Obra $obra)
    {
        return $user->hasAnyRole(['admin', 'Jefe de Obra', 'Inspector Principal', 'Visualizador']) ||
               $obra->usuarios->contains($user->id);
    }*/

    public function view(User $user, Obra $obra)
{
    // Los administradores pueden ver cualquier obra
    if ($user->hasRole('admin')) {
        return true;
    }

    // Otros usuarios solo pueden ver obras en las que participan
    return $obra->usuarios->contains($user);
}

    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'Jefe de Obra', 'Inspector Principal']);
    }

    public function update(User $user, Obra $obra)
    {
        return $user->hasAnyRole(['admin']) ||
               ($obra->contratista_id === $user->id && $user->hasRole('Jefe de Obra')) ||
               ($obra->inspector_id === $user->id && $user->hasRole('Inspector Principal'));
    }

    public function gestionarUsuarios(User $user, Obra $obra)
    {
        return $user->hasAnyRole(['admin']) ||
               ($obra->contratista_id === $user->id && $user->hasRole('Jefe de Obra')) ||
               ($obra->inspector_id === $user->id && $user->hasRole('Inspector Principal'));
    }

    public function delete(User $user, Obra $obra)
    {
        return $user->hasRole('admin');
    }

    // app/Policies/ObraPolicy.php
// Añadir este método a la política existente
public function viewLibroObra(User $user, Obra $obra)
{
    // Verificar si el usuario tiene algún rol en esta obra
    $hasRoleInObra = $obra->usuarios()->where('user_id', $user->id)->exists();

    // Verificar si el usuario es admin
    $isAdmin = $user->hasRole('admin');

    // Verificar si el usuario tiene el rol de Visualizador en esta obra
    $visualizadorRole = \App\Models\RoleObra::where('nombre', 'Visualizador')->first();
    $isVisualizador = $obra->usuarios()
        ->where('user_id', $user->id)
        ->wherePivot('rol_id', $visualizadorRole->id)
        ->exists();

    return $isAdmin || $hasRoleInObra || $isVisualizador;
}

}


