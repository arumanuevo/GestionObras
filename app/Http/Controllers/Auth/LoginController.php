<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ObraUsuarioRol;
use App\Models\RoleObra;
use Illuminate\Support\Facades\DB; 

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/obras';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        // Verificar si el usuario está aprobado
        if (!$user->approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('unapproved');
        }
    
        // Cargar roles de sistema
        $user->load('roles');
        Log::info('Roles de sistema cargados', ['roles' => $user->roles->pluck('name')]);
    
        // Obtener las obras del usuario usando la relación correcta
        $user->load(['obras' => function($query) {
            $query->select('obras.id', 'obras.nombre');
        }]);
    
        Log::info('Obras del usuario cargadas', [
            'count' => $user->obras->count(),
            'obras' => $user->obras->pluck('nombre')
        ]);
    
        // Obtener los roles de obra para cada obra
        $obraRoles = collect();
        foreach ($user->obras as $obra) {
            if ($obra->pivot && $obra->pivot->rol_id) {
                $rol = RoleObra::find($obra->pivot->rol_id);
    
                if ($rol) {
                    $obraRoles->push((object)[
                        'obra' => (object)[
                            'id' => $obra->id,
                            'nombre' => $obra->nombre
                        ],
                        'rol' => (object)[
                            'id' => $rol->id,
                            'nombre' => $rol->nombre
                        ]
                    ]);
                }
            }
        }
    
        Log::info('Roles de obra del usuario', [
            'count' => $obraRoles->count(),
            'data' => $obraRoles->toArray()
        ]);
    
        // Almacenar los datos del usuario en la sesión
        $request->session()->put('user_roles', [
            'system_roles' => $user->roles,
            'obra_roles' => $obraRoles
        ]);
    
        Log::info('Datos de roles almacenados en sesión', [
            'system_roles_count' => $user->roles->count(),
            'obra_roles_count' => $obraRoles->count(),
            'obra_roles_data' => $obraRoles->toArray()
        ]);
    
        return redirect()->intended($this->redirectPath());
    }
    
    public function showUnapprovedMessage()
    {
        return view('auth.unapproved');
    }

    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo;
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}