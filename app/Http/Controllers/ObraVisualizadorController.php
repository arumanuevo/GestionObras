<?php

// app/Http/Controllers/ObraVisualizadorController.php
namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\User;
use App\Models\RoleObra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ObraVisualizadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar formulario para asignar visualizadores a una obra
     */
    public function create(Obra $obra)
    {
        $this->authorize('update', $obra);

        // Obtener todos los usuarios que no están asignados a esta obra
        $users = User::whereDoesntHave('obras', function($query) use ($obra) {
            $query->where('obra_id', $obra->id);
        })->get();

        // Obtener el rol de Visualizador
        $visualizadorRole = RoleObra::where('nombre', 'Visualizador')->first();

        return view('obras.visualizadores.create', compact('obra', 'users', 'visualizadorRole'));
    }

    /**
     * Asignar usuarios como visualizadores de una obra
     */
    public function store(Request $request, Obra $obra)
    {
        $this->authorize('update', $obra);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            // Obtener el rol de Visualizador
            $visualizadorRole = RoleObra::where('nombre', 'Visualizador')->first();

            if (!$visualizadorRole) {
                return back()->with('error', 'El rol de Visualizador no está configurado en el sistema.');
            }

            // Asignar los usuarios seleccionados como visualizadores
            foreach ($request->user_ids as $userId) {
                $obra->usuarios()->attach($userId, [
                    'rol_id' => $visualizadorRole->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return redirect()->route('obras.show', $obra->id)
                ->with('success', 'Visualizadores asignados con éxito a la obra.');
        } catch (\Exception $e) {
            Log::error('Error al asignar visualizadores: ' . $e->getMessage());
            return back()->with('error', 'Error al asignar visualizadores: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar lista de visualizadores de una obra
     */
    public function index(Obra $obra)
    {
        $this->authorize('view', $obra);

        // Obtener el rol de Visualizador
        $visualizadorRole = RoleObra::where('nombre', 'Visualizador')->first();

        // Obtener los visualizadores de esta obra
        $visualizadores = $obra->usuarios()
            ->wherePivot('rol_id', $visualizadorRole->id)
            ->withPivot('rol_id')
            ->get();

        return view('obras.visualizadores.index', compact('obra', 'visualizadores'));
    }

    /**
     * Eliminar un visualizador de una obra
     */
    public function destroy(Obra $obra, User $user)
    {
        $this->authorize('update', $obra);

        try {
            // Obtener el rol de Visualizador
            $visualizadorRole = RoleObra::where('nombre', 'Visualizador')->first();

            // Desvincular al usuario de esta obra con el rol de visualizador
            $obra->usuarios()->detach($user->id);

            return redirect()->route('obras.visualizadores.index', $obra->id)
                ->with('success', 'Visualizador eliminado con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar visualizador: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar visualizador: ' . $e->getMessage());
        }
    }
}