<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\Nota;
use App\Models\NotaEquipoProyecto;
use App\Models\EntregaContratista;
use App\Models\OrdenServicio;
use Illuminate\Http\Request;

class BandejaPublicaController extends Controller
{
    /**
     * Muestra el detalle de una nota de pedido desde bandeja pública
     */
    public function notaPedido(Obra $obra, Nota $nota)
    {
        $this->authorize('view', [$obra, $nota]);

        // Obtener el rol del usuario actual en esta obra
        $user = auth()->user();
        $rol = $obra->usuarios->find($user->id)->pivot->rol_id ?? null;

        return view('bandeja_publica.nota_pedido', compact('obra', 'nota', 'rol'));
    }

    /**
     * Muestra el detalle de una nota al equipo de proyecto desde bandeja pública
     */
    public function notaEquipo(Obra $obra, NotaEquipoProyecto $nota)
    {
        $this->authorize('view', [$obra, $nota]);

        // Obtener el rol del usuario actual en esta obra
        $user = auth()->user();
        $rol = $obra->usuarios->find($user->id)->pivot->rol_id ?? null;

        return view('bandeja_publica.nota_equipo', compact('obra', 'nota', 'rol'));
    }

    public function entregaContratista(Obra $obra, EntregaContratista $entrega)
    {
        // Verificar que el usuario tenga acceso a esta obra
        $this->authorize('view', $obra);

        // Obtener el rol del usuario actual en esta obra
        $user = auth()->user();
        $obraUsuario = $obra->usuarios->find($user->id);

        $rol = $obraUsuario ? $obraUsuario->pivot->rol_id ?? null : null;

        // Verificar si el usuario es destinatario de esta entrega
        $esDestinatario = $entrega->destinatarios->contains($user->id);

        // Obtener información adicional sobre los destinatarios
        $destinatarios = $entrega->destinatarios->map(function($destinatario) use ($obra) {
            $obraUsuario = $obra->usuarios->find($destinatario->id);
            $rol = $obraUsuario ? \App\Models\RoleObra::find($obraUsuario->pivot->rol_id) : null;

            return [
                'user' => $destinatario,
                'rol' => $rol,
                'recibida' => $destinatario->pivot->recibida
            ];
        });

        return view('bandeja_publica.entrega_contratista', compact(
            'obra',
            'entrega',
            'rol',
            'esDestinatario',
            'destinatarios'
        ));
    }

    /**
     * Muestra el detalle de una orden de servicio desde bandeja pública
     */
    public function ordenServicio(Obra $obra, OrdenServicio $ordenServicio)
{
     //$this->authorize('view', [$obra, $orden_servicio]);

    // Verificar que la orden de servicio pertenezca a la obra
    if ($ordenServicio->obra_id != $obra->id) {
        abort(404, 'La orden de servicio no pertenece a esta obra');
    }

    // Marcar como leída si el usuario actual es el destinatario
    if (auth()->user()->id == $ordenServicio->destinatario_id && !$ordenServicio->leida) {
        $ordenServicio->update(['leida' => true]);
    }

    return view('bandeja_publica.orden_servicio', [
        'obra' => $obra,
        'ordenServicio' => $ordenServicio // Pasamos la variable con el nombre correcto
    ]);
    /*return view('bandeja_publica.orden_servicio', compact(
        'obra',
        'ordenServicio',
        'rol',
        'esDestinatario',
        'destinatarios',
        'notaPedido'
    ));*/
}
}