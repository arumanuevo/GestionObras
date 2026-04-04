<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Notifications\UserApprovedNotification;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Verificamos si el usuario tiene el rol de admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para acceder a esta página');
        }

        $users = User::with('roles')->paginate(10);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
{
    if (!auth()->user()->hasRole('admin')) {
        abort(403, 'No tienes permiso para acceder a esta página');
    }

    // Solo cargamos los roles de sistema (Admin y User)
    $roles = Role::whereIn('name', ['admin', 'user'])->get();

    return view('users.create', compact('roles'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);
    
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'approved' => false,
            ]);
    
            // Asignar el rol seleccionado
            $role = Role::find($validated['role']);
            $user->syncRoles($role->name);
    
            return redirect()->route('users.index')
                ->with('success', 'Usuario creado con éxito. El usuario necesita ser aprobado por un administrador.');
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al crear usuario: ' . $e->getMessage());
        }
    }
// app/Http/Controllers/UserController.php
public function edit(User $user)
{
    if (!auth()->user()->hasRole('admin')) {
        abort(403, 'No tienes permiso para acceder a esta página');
    }

    // Cargar todos los roles del sistema
    $roles = Role::all();

    return view('users.edit', compact('user', 'roles'));
}



    /*public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
            'approved' => 'sometimes|boolean'
        ]);

        try {
            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'approved' => $validated['approved'] ?? false,
            ];

            if ($validated['password']) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            // Actualizar el rol del usuario
            $role = Role::find($validated['role']);
            $user->syncRoles($role->name);

            return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
        }
    }*/

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
   // app/Http/Controllers/UserController.php
public function update(Request $request, User $user)
{
    Log::info('UserController@update - Iniciando actualización para usuario ID: ' . $user->id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        'role' => 'required|exists:roles,id',
        'approved' => 'sometimes|boolean',
    ]);

    try {
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'approved' => $request->has('approved'),
        ];

        $wasApproved = $user->approved;
        $isBeingApproved = $request->has('approved') && $validated['approved'];

        if ($isBeingApproved && !$wasApproved) {
            $data['approved_by'] = auth()->id();
            $data['approved_at'] = now();
            Log::info('UserController@update - Usuario siendo aprobado, ID: ' . $user->id);
        }

        $user->update($data);

        // Verificar si el usuario actual es admin y está editando su propio perfil
        $isSelfAdmin = auth()->user()->id === $user->id && $user->hasRole('admin');
        $newRole = Role::find($validated['role']);

        // Si el usuario es admin y está intentando cambiar su propio rol a user, mantener el rol admin
        if ($isSelfAdmin && $newRole->name === 'user') {
            Log::info('UserController@update - Intento de cambiar rol de admin a user bloqueado para usuario ID: ' . $user->id);
            return redirect()->route('users.index')->with('warning', 'No puedes cambiar tu propio rol de Admin a User para evitar dejar el sistema sin administradores.');
        }

        // Asignar el nuevo rol
        $user->syncRoles($newRole->name);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');
    } catch (\Exception $e) {
        Log::error('UserController@update - Error al actualizar usuario ID: ' . $user->id . ' - ' . $e->getMessage());
        return back()->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
    }
}


    public function destroy(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para realizar esta acción');
        }

        try {
            // No permitir eliminar al usuario actual
            if ($user->id === auth()->id()) {
                return back()->with('error', 'No puedes eliminarte a ti mismo.');
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'Usuario eliminado con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }

   /* public function approve(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para realizar esta acción');
        }

        try {
            $user->update([
                'approved' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            return back()->with('success', 'Usuario aprobado con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al aprobar usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al aprobar usuario: ' . $e->getMessage());
        }
    }*/

    public function approve(Request $request, User $user)
    {
        Log::info('UserController@approve - Iniciando aprobación para usuario ID: ' . $user->id);
    
        try {
            // Verificar que el usuario no esté ya aprobado
            if ($user->approved) {
                Log::info('UserController@approve - Usuario ya aprobado, ID: ' . $user->id);
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya está aprobado.'
                ], 200);
            }
    
            // Verificar que el usuario tenga el email verificado
            /*if (!$user->hasVerifiedEmail()) {
                Log::warning('UserController@approve - Email no verificado, ID: ' . $user->id);
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario debe verificar su email antes de ser aprobado.'
                ], 400);
            }*/
    
            // Aprobar el usuario
            $user->update([
                'approved' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
    
            Log::info('UserController@approve - Usuario aprobado, ID: ' . $user->id . ', Email: ' . $user->email);
    
            // Enviar notificación de aprobación
            try {
                Log::info('UserController@approve - Enviando notificación a: ' . $user->email);
                $user->notifyNow(new UserApprovedNotification());
                Log::info('UserController@approve - Notificación enviada a: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('UserController@approve - Error al enviar notificación: ' . $e->getMessage());
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Usuario aprobado con éxito. Se ha enviado una notificación al usuario.'
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('UserController@approve - Error al aprobar usuario ID: ' . $user->id . ' - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

}
