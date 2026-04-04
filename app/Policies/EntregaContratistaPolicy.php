<?php

// app/Policies/EntregaContratistaPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Obra;
use App\Models\EntregaContratista;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntregaContratistaPolicy
{
    use HandlesAuthorization;

    public function createEntregaContratista(User $user, Obra $obra)
    {
        // Verificar si el usuario tiene un rol adecuado en esta obra
        if ($user->hasRole('admin')) {
            return true;
        }

        $asignadoAObra = $obra->usuarios->contains($user->id);
        if (!$asignadoAObra) {
            return false;
        }

        $pivot = $obra->usuarios->find($user->id)->pivot;
        if (!$pivot->rol_id) {
            return false;
        }

        $rolObra = \App\Models\RoleObra::find($pivot->rol_id);
        return $rolObra && in_array($rolObra->nombre, ['Jefe de Proyecto', 'Especialista']);
    }

    public function viewEntregaContratista(User $user, Obra $obra)
    {
        // Cualquier usuario asignado a la obra puede ver las entregas
        return $obra->usuarios->contains($user->id);
    }

    public function updateEntregaContratista(User $user, Obra $obra, EntregaContratista $entrega)
    {
        // Solo el creador de la entrega puede actualizarla
        return $entrega->creador_id === $user->id;
    }

    public function recibirEntregaContratista(User $user, Obra $obra, EntregaContratista $entrega)
    {
        // Solo los destinatarios pueden recibir la entrega
        return $entrega->destinatarios->contains($user->id);
    }
}