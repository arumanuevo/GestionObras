<?php

// app/Http/Controllers/ObraController.php
namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\User;
use App\Models\RoleObra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ObraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // app/Http/Controllers/ObraController.php
public function index()
{
    // Verificar si el usuario es admin del sistema
    $isAdmin = auth()->user()->hasRole('admin');

    if ($isAdmin) {
        // Si es admin, mostrar todas las obras
        $obras = Obra::with(['contratista', 'inspector', 'usuarios'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);
    } else {
        // Si no es admin, mostrar solo las obras en las que participa
        $obras = auth()->user()->obras()
                    ->with(['contratista', 'inspector', 'usuarios'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
    }

    return view('obras.index', compact('obras'));
}

    public function create()
{
    // Obtener todos los usuarios del sistema
    $usuarios = User::with('roles')->get();

    return view('obras.create', compact('usuarios'));
}

   // app/Http/Controllers/ObraController.php
/*public function store(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'estado' => 'required|string|in:En progreso,Finalizada,Suspendida',
        'descripcion' => 'nullable|string',
        'ubicacion' => 'nullable|string',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'nullable|date|after:fecha_inicio',
        'contratista_id' => 'required|exists:users,id',
        'inspector_id' => 'required|exists:users,id',
    ]);

    try {
        $obra = Obra::create($validated);

        return redirect()->route('obras.show', $obra->id)
                         ->with('success', 'Obra creada con éxito. Ahora puedes asignar más usuarios y roles.');
    } catch (\Exception $e) {
        Log::error('Error al crear obra: ' . $e->getMessage());
        return back()->with('error', 'Error al crear la obra: ' . $e->getMessage());
    }
}*/

// app/Http/Controllers/ObraController.php
// app/Http/Controllers/ObraController.php
// app/Http/Controllers/ObraController.php
public function store(Request $request)
{
    // Validar los datos del formulario
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'estado' => 'required|string|in:En progreso,Finalizada,Suspendida',
        'descripcion' => 'nullable|string',
        'ubicacion' => 'nullable|string',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'jefe_obra_id' => 'required|exists:users,id',
        'inspector_id' => 'required|exists:users,id',
        'jefe_proyecto_id' => 'nullable|exists:users,id',
        'asistente_contratista_id' => 'nullable|exists:users,id',
        'asistente_inspeccion_id' => 'nullable|exists:users,id',
        'especialista_id' => 'nullable|exists:users,id',
    ]);

    // Log para depuración
    \Log::info('Datos recibidos en store:', $request->all());

    try {
        // Crear la obra con los datos básicos
        $obra = Obra::create([
            'nombre' => $validated['nombre'],
            'estado' => $validated['estado'],
            'descripcion' => $validated['descripcion'],
            'ubicacion' => $validated['ubicacion'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'contratista_id' => $validated['jefe_obra_id'], // Usamos jefe_obra_id como contratista_id
            'inspector_id' => $validated['inspector_id'],
        ]);

        // Asignar roles a los usuarios
        $rolesAsignados = [];

        // Jefe de Obra (Contratista)
        if ($validated['jefe_obra_id']) {
            $rolesAsignados[] = [
                'user_id' => $validated['jefe_obra_id'],
                'rol_id' => RoleObra::where('nombre', 'Jefe de Obra')->first()->id
            ];
        }

        // Inspector Principal
        if ($validated['inspector_id']) {
            $rolesAsignados[] = [
                'user_id' => $validated['inspector_id'],
                'rol_id' => RoleObra::where('nombre', 'Inspector Principal')->first()->id
            ];
        }

        // Jefe de Proyecto
        if ($validated['jefe_proyecto_id']) {
            $rolesAsignados[] = [
                'user_id' => $validated['jefe_proyecto_id'],
                'rol_id' => RoleObra::where('nombre', 'Jefe de Proyecto')->first()->id
            ];
        }

        // Asistente Contratista
        if ($validated['asistente_contratista_id']) {
            $rolesAsignados[] = [
                'user_id' => $validated['asistente_contratista_id'],
                'rol_id' => RoleObra::where('nombre', 'Asistente Contratista')->first()->id
            ];
        }

        // Asistente Inspección
        if ($validated['asistente_inspeccion_id']) {
            $rolesAsignados[] = [
                'user_id' => $validated['asistente_inspeccion_id'],
                'rol_id' => RoleObra::where('nombre', 'Asistente Inspección')->first()->id
            ];
        }

        // Especialista
        if ($validated['especialista_id']) {
            $rolesAsignados[] = [
                'user_id' => $validated['especialista_id'],
                'rol_id' => RoleObra::where('nombre', 'Especialista')->first()->id
            ];
        }

        // Asignar los usuarios a la obra con sus roles
        foreach ($rolesAsignados as $asignacion) {
            $obra->usuarios()->attach($asignacion['user_id'], ['rol_id' => $asignacion['rol_id']]);
        }

        \Log::info('Obra creada con éxito. ID: ' . $obra->id);

        return redirect()->route('obras.show', $obra->id)
                         ->with('success', 'Obra creada con éxito. Los usuarios han sido asignados a sus roles.');

    } catch (\Exception $e) {
        \Log::error('Error al crear obra: ' . $e->getMessage());
        return back()->with('error', 'Error al crear la obra: ' . $e->getMessage());
    }
}

   // app/Http/Controllers/ObraController.php
public function show(Obra $obra)
{
    $obra->load(['usuarios' => function($query) {
        $query->withPivot('rol_id');
    }, 'contratista', 'inspector']);

    // Cargar los roles para cada usuario
    foreach ($obra->usuarios as $usuario) {
        if ($usuario->pivot->rol_id) {
            $usuario->pivot->rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
        }
    }

    // Obtener el inspector principal y asistente de inspección de la obra
    $inspectorPrincipal = $obra->usuarios->first(function($usuario) {
        return $usuario->pivot->rol && $usuario->pivot->rol->nombre == 'Inspector Principal';
    });

    $asistenteInspeccion = $obra->usuarios->first(function($usuario) {
        return $usuario->pivot->rol && $usuario->pivot->rol->nombre == 'Asistente Inspección';
    });

    return view('obras.show', compact('obra', 'inspectorPrincipal', 'asistenteInspeccion'));
}
// app/Http/Controllers/ObraController.php
public function usuarios(Obra $obra)
{
    $usuariosObra = $obra->usuarios()->withPivot('rol_id')->get();
    $usuariosDisponibles = User::where('approved', true)->whereDoesntHave('obras', function($query) use ($obra) {
        $query->where('obra_id', $obra->id);
    })->get();

    $rolesObra = \App\Models\RoleObra::all(); // Asegúrate de que esta línea esté presente

    return view('obras.usuarios', compact('obra', 'usuariosObra', 'usuariosDisponibles', 'rolesObra'));
}


/*public function edit(Obra $obra)
{
    $jefesObra = User::role('Jefe de Obra')->get(['id', 'name', 'organization']);
    $asistentesContratista = User::role('Asistente Contratista')->get(['id', 'name', 'organization']);
    $asistentesInspeccion = User::role('Asistente Inspección')->get(['id', 'name', 'organization']);
    $contratistas = User::role('Contratista')->get(['id', 'name', 'organization']);
    $inspectores = User::role('Inspector Principal')->get(['id', 'name', 'organization']);

    return view('obras.edit', compact('obra', 'jefesObra', 'asistentesContratista', 'asistentesInspeccion', 'contratistas', 'inspectores'));
}*/
public function diagnosticarEstructura()
{
    // Verificar la estructura de la tabla obra_user
    $obraUserStructure = DB::select('DESCRIBE obra_user');
    \Log::info('Estructura de la tabla obra_user:', $obraUserStructure);

    // Verificar algunos registros de ejemplo
    $ejemploRegistros = \App\Models\ObraUsuarioRol::limit(10)->get();
    \Log::info('Ejemplo de registros en obra_user:', $ejemploRegistros->toArray());

    // Verificar los roles disponibles
    $rolesDisponibles = \App\Models\RoleObra::all();
    \Log::info('Roles disponibles:', $rolesDisponibles->toArray());

    return "Diagnóstico completado. Revisa los logs para más detalles.";
}
// app/Http/Controllers/ObraController.php
public function edit(Obra $obra)
{
    // Obtener todos los usuarios del sistema
    $usuarios = User::with('roles')->get();
    $roles = RoleObra::all();

    // Obtener los IDs de los roles
    $roleIds = [
        'jefe_proyecto' => $roles->where('nombre', 'Jefe de Proyecto')->first()?->id,
        'especialista' => $roles->where('nombre', 'Especialista')->first()?->id,
        'jefe_obra' => $roles->where('nombre', 'Jefe de Obra')->first()?->id,
        'asistente_contratista' => $roles->where('nombre', 'Asistente Contratista')->first()?->id,
        'inspector_principal' => $roles->where('nombre', 'Inspector Principal')->first()?->id,
        'asistente_inspeccion' => $roles->where('nombre', 'Asistente Inspección')->first()?->id,
    ];

    // Diagnóstico detallado de la relación obra-usuarios
    $diagnostico = [
        'obra_id' => $obra->id,
        'usuarios_relacionados' => $obra->usuarios->map(function($user) {
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'rol_id' => $user->pivot->rol_id,
                'rol_nombre' => $user->pivot->rol ? $user->pivot->rol->nombre : 'Sin rol'
            ];
        }),
        'role_ids' => $roleIds,
        'contratista_id' => $obra->contratista_id,
        'inspector_id' => $obra->inspector_id
    ];

    // Obtener los usuarios asignados a cada rol
    $usuariosAsignados = [
        'jefe_proyecto' => $obra->usuarios->firstWhere('pivot.rol_id', $roleIds['jefe_proyecto']),
        'especialista' => $obra->usuarios->firstWhere('pivot.rol_id', $roleIds['especialista']),
        'jefe_obra' => $obra->contratista ?? $obra->usuarios->firstWhere('pivot.rol_id', $roleIds['jefe_obra']),
        'asistente_contratista' => $obra->usuarios->firstWhere('pivot.rol_id', $roleIds['asistente_contratista']),
        'inspector_principal' => $obra->inspector ?? $obra->usuarios->firstWhere('pivot.rol_id', $roleIds['inspector_principal']),
        'asistente_inspeccion' => $obra->usuarios->firstWhere('pivot.rol_id', $roleIds['asistente_inspeccion']),
    ];

    // Agregar el diagnóstico a los datos que pasamos a la vista
    return view('obras.edit', compact('obra', 'usuarios', 'roles', 'usuariosAsignados', 'diagnostico'));
}

// app/Http/Controllers/ObraController.php
// app/Http/Controllers/ObraController.php
// app/Http/Controllers/ObraController.php
public function update(Request $request, Obra $obra)
{
    // Log inicial de los datos recibidos
    \Log::info('Iniciando actualización de obra', [
        'obra_id' => $obra->id,
        'datos_recibidos' => $request->all()
    ]);

    try {
        // Validación de datos - Eliminamos contratista_id ya que no está en el formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|string|in:En progreso,Finalizada,Suspendida',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'inspector_id' => 'required|exists:users,id',  // Solo inspector_id es obligatorio
            'jefe_proyecto_id' => 'nullable|exists:users,id',
            'especialista_id' => 'nullable|exists:users,id',
            'jefe_obra_id' => 'nullable|exists:users,id',  // Este es el que actúa como contratista
            'asistente_contratista_id' => 'nullable|exists:users,id',
            'asistente_inspeccion_id' => 'nullable|exists:users,id',
        ]);

        \Log::info('Datos validados correctamente', ['datos' => $validated]);

        DB::beginTransaction();

        // Actualizar los datos básicos de la obra
        $obra->update([
            'nombre' => $validated['nombre'],
            'estado' => $validated['estado'],
            'descripcion' => $validated['descripcion'],
            'ubicacion' => $validated['ubicacion'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'contratista_id' => $validated['jefe_obra_id'],  // Usamos jefe_obra_id como contratista_id
            'inspector_id' => $validated['inspector_id'],
        ]);

        \Log::info('Datos básicos de la obra actualizados');

        // Obtener todos los roles de obra
        $roleObra = RoleObra::all()->keyBy('nombre');

        \Log::info('Roles de obra obtenidos', ['roles' => $roleObra->pluck('id', 'nombre')]);

        // Definir los roles y sus asignaciones
        $rolesAsignados = [
            ['user_id' => $validated['jefe_proyecto_id'], 'rol_id' => $roleObra['Jefe de Proyecto']->id, 'role_name' => 'Jefe de Proyecto'],
            ['user_id' => $validated['especialista_id'], 'rol_id' => $roleObra['Especialista']->id, 'role_name' => 'Especialista'],
            ['user_id' => $validated['jefe_obra_id'], 'rol_id' => $roleObra['Jefe de Obra']->id, 'role_name' => 'Jefe de Obra'],
            ['user_id' => $validated['asistente_contratista_id'], 'rol_id' => $roleObra['Asistente Contratista']->id, 'role_name' => 'Asistente Contratista'],
            ['user_id' => $validated['asistente_inspeccion_id'], 'rol_id' => $roleObra['Asistente Inspección']->id, 'role_name' => 'Asistente Inspección'],
        ];

        \Log::info('Asignaciones de roles preparadas', ['asignaciones' => $rolesAsignados]);

        // Primero, eliminar todas las asignaciones de usuarios actuales
        $obra->usuarios()->detach();
        \Log::info('Asignaciones de usuarios anteriores eliminadas');

        // Luego, asignar los nuevos usuarios con sus roles
        foreach ($rolesAsignados as $asignacion) {
            if ($asignacion['user_id']) {
                \Log::info('Asignando usuario a rol', [
                    'user_id' => $asignacion['user_id'],
                    'rol_id' => $asignacion['rol_id'],
                    'role_name' => $asignacion['role_name']
                ]);

                $obra->usuarios()->attach($asignacion['user_id'], ['rol_id' => $asignacion['rol_id']]);
            }
        }

        // Asegurarse de que el inspector principal esté asignado
        \Log::info('Asignando inspector principal');
        $obra->usuarios()->attach($validated['inspector_id'], ['rol_id' => $roleObra['Inspector Principal']->id]);

        DB::commit();
        \Log::info('Transacción completada con éxito');

        return redirect()->route('obras.show', $obra->id)
                         ->with('success', 'Obra actualizada con éxito.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Error de validación al actualizar obra', [
            'errores' => $e->errors(),
            'datos_recibidos' => $request->all()
        ]);
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error al actualizar obra', [
            'mensaje' => $e->getMessage(),
            'traza' => $e->getTraceAsString(),
            'datos_recibidos' => $request->all()
        ]);
        return back()->with('error', 'Error al actualizar la obra: ' . $e->getMessage());
    }
}

   /* public function gestionarUsuarios(Obra $obra)
{
    $roles = RoleObra::all();
    $usuariosObra = $obra->usuarios()->withPivot('rol_id')->get();

    // Cargar los roles para cada usuario
    foreach ($usuariosObra as $usuario) {
        if ($usuario->pivot->rol_id) {
            $usuario->pivot->rol = RoleObra::find($usuario->pivot->rol_id);
        }
    }

    $usuariosDisponibles = User::whereDoesntHave('obras', function($query) use ($obra) {
        $query->where('obra_id', $obra->id);
    })->get();

    return view('obras.usuarios', compact('obra', 'roles', 'usuariosObra', 'usuariosDisponibles'));
}*/

public function gestionarUsuarios(Obra $obra)
{
    $roles = RoleObra::all();
    $usuariosObra = $obra->usuarios()->withPivot('rol_id')->get();
    $usuariosDisponibles = User::whereDoesntHave('obras', function($query) use ($obra) {
        $query->where('obra_id', $obra->id);
    })->get();

    return view('obras.usuarios', compact('obra', 'roles', 'usuariosObra', 'usuariosDisponibles'));
}


public function asignarUsuario(Request $request, Obra $obra)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'rol_id' => 'required|exists:roles_obra,id',
    ]);

    $user = User::findOrFail($request->user_id);
    $obra->usuarios()->attach($user->id, ['rol_id' => $request->rol_id]);

    return redirect()->route('obras.usuarios', $obra->id)
                     ->with('success', 'Usuario asignado con éxito.');
}
    public function removerUsuario(Obra $obra, User $user)
    {
        try {
            $obra->usuarios()->detach($user->id);
            return back()->with('success', 'Usuario removido de la obra con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al remover usuario de obra: ' . $e->getMessage());
            return back()->with('error', 'Error al remover usuario: ' . $e->getMessage());
        }
    }

    public function destroy(Obra $obra)
    {
        try {
            $obra->delete();
            return redirect()->route('obras.index')->with('success', 'Obra eliminada con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar obra: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la obra: ' . $e->getMessage());
        }
    }

    public function notas(Obra $obra)
    {
        $this->authorize('view', $obra);

        $notas = $obra->notas()->with(['creador', 'destinatario', 'firmadoPor'])->get();

        return view('obras.notas.index', compact('obra', 'notas'));
    }

    public function ordenesServicio(Obra $obra)
    {
        $this->authorize('view', $obra);

        $ordenes = $obra->ordenesServicio()->with(['creador', 'destinatario', 'firmadoPor'])->get();

        return view('obras.ordenes_servicio.index', compact('obra', 'ordenes'));
    }

    public function libroObra(Obra $obra)
    {
        $this->authorize('view', $obra);

        $libroObra = $obra->libroObra()->with('documento')->orderBy('orden')->get();

        return view('obras.libro_obra', compact('obra', 'libroObra'));
    }

}

