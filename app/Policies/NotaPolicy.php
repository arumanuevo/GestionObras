<?php

namespace App\Policies;

use App\Models\Nota;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotaPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Nota $nota)
    {
        // El creador, un admin o el destinatario pueden ver la nota
        return $user->id === $nota->user_id ||
               $user->hasRole('admin') ||
               $user->id === $nota->destinatario_id;
    }

    public function update(User $user, Nota $nota)
    {
        // Solo el creador o un admin pueden editar la nota
        return $user->id === $nota->user_id ||
               $user->hasRole('admin');
    }

    public function delete(User $user, Nota $nota)
    {
        // Solo el creador o un admin pueden eliminar la nota
        return $user->id === $nota->user_id ||
               $user->hasRole('admin');
    }
}
