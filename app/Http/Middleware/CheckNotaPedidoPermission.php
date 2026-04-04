<?php
// app/Http/Middleware/CheckNotaPedidoPermission.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckNotaPedidoPermission
{
    public function handle($request, Closure $next)
    {
        // Obtener la obra de la ruta
        $obra = $request->route('obra');

        // Si no hay obra en la ruta, continuar
        if (!$obra) {
            return $next($request);
        }

        $user = $request->user();

        // Si no hay usuario autenticado, continuar (el middleware 'auth' ya lo maneja)
        if (!$user) {
            return $next($request);
        }

        // Registrar información de depuración
        $pivot = null;
        $rolObra = null;
        $tieneRolAdecuado = false;

        if ($obra->usuarios->contains($user->id)) {
            $pivot = $obra->usuarios->find($user->id)->pivot;
            if ($pivot->rol_id) {
                $rolObra = \App\Models\RoleObra::find($pivot->rol_id);
                $tieneRolAdecuado = $rolObra && in_array($rolObra->nombre, ['Jefe de Obra', 'Asistente Contratista']);
            }
        }

        Log::debug('CheckNotaPedidoPermission', [
            'user_id' => $user->id,
            'obra_id' => $obra->id,
            'asignado_a_obra' => $obra->usuarios->contains($user->id),
            'rol_id' => $pivot ? $pivot->rol_id : null,
            'rol_obra' => $rolObra ? $rolObra->nombre : null,
            'tiene_rol_adecuado' => $tieneRolAdecuado
        ]);

        // Administradores pueden hacer cualquier cosa
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Verificar que el usuario esté asignado a la obra
        if (!$obra->usuarios->contains($user->id)) {
            abort(403, 'No autorizado: No estás asignado a esta obra');
        }

        // Si no tiene rol_id asignado, no puede crear notas
        if (!$pivot->rol_id) {
            abort(403, 'No autorizado: No tienes un rol asignado en esta obra');
        }

        // Verificar si tiene un rol que le permita crear notas de pedido
        if (!$tieneRolAdecuado) {
            abort(403, 'No autorizado: No tienes permisos para esta acción en esta obra');
        }

        return $next($request);
    }
}
