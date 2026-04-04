<?php

// app/Policies/NotaPedidoPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Nota;
use App\Models\Obra;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class NotaPedidoPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Nota $nota)
    {
        return $user->hasRole('admin') ||
               $user->id === $nota->user_id ||
               $user->id === $nota->destinatario_id;
    }

    public function viewAny(User $user, Obra $obra)
    {
        // Administradores pueden ver todo
        if ($user->hasRole('admin')) {
            return true;
        }

        // Verificar que el usuario esté asignado a la obra
        if (!$obra->usuarios->contains($user->id)) {
            return false;
        }

        // Verificar que el usuario tenga un rol que le permita ver notas de pedido
        return $user->hasRole('Jefe de Obra') ||
               $user->hasRole('Asistente Contratista') ||
               $user->hasRole('Inspector Principal') ||
               $user->hasRole('Asistente Inspección') ||
               $user->hasRole('Visualizador');
    }
    // app/Policies/NotaPedidoPolicy.php
    public function create(User $user, Obra $obra)
{
    // Permitir acceso temporalmente para depuración
    return true;
}

    public function update(User $user, Nota $nota)
    {
        return $user->hasRole('admin') ||
               $user->id === $nota->user_id;
    }

    public function firmar(User $user, Nota $nota)
    {
        return $user->hasRole('admin') ||
               $user->hasRole('Inspector Principal') ||
               $user->hasRole('Asistente Inspección');
    }
}
