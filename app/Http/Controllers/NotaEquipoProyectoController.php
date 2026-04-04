<?php

namespace App\Http\Controllers;

use App\Models\NotaEquipoProyecto;
use App\Models\NotaEquipoArchivo;
use App\Models\Obra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; 
use App\Mail\NotaEquipoProyectoCreada;

class NotaEquipoProyectoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('approved');
    }

    /**
     * Muestra el formulario para crear una nueva nota al equipo de proyecto
     */
    public function create(Obra $obra)
    {
        $this->authorize('createNotaEquipo', $obra);

        // Obtener los miembros del equipo de proyecto
        $equipoProyecto = $obra->usuarios->filter(function($usuario) {
            if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
            $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rol && in_array($rol->nombre, ['Jefe de Proyecto', 'Especialista']);
        });

        return view('notas-equipo-proyecto.create', compact('obra', 'equipoProyecto'));
    }

    /**
     * Almacena una nueva nota al equipo de proyecto
     */
    public function store(Request $request, Obra $obra)
    {
        $request->validate([
            'numero' => 'required|integer',
            'tema' => 'required|string|max:255',
            'contenido' => 'required|string',
            'fecha' => 'required|date',
            'destinatarios' => 'required|array|min:1',
            'destinatarios.*' => 'exists:users,id',
            'tipo_entrega' => 'required|string',
            'otro_tipo_entrega' => 'nullable|required_if:tipo_entrega,Otro|string|max:255',
            'plazo_entrega' => 'required|integer|min:1',
            'prioridad' => 'required|string|in:Normal,Alta,Urgente',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();

        try {
            // Crear la nota
            $nota = new NotaEquipoProyecto();
            $nota->obra_id = $obra->id;
            $nota->numero = $request->numero;
            $nota->tema = $request->tema;
            $nota->contenido = $request->contenido;
            $nota->fecha = $request->fecha;
            $nota->creador_id = Auth::id();
            $nota->tipo_entrega = $request->tipo_entrega === 'Otro' ? $request->otro_tipo_entrega : $request->tipo_entrega;
            $nota->plazo_entrega = $request->plazo_entrega;
            $nota->prioridad = $request->prioridad;
            $nota->estado = 'Emitida';
            $nota->save();

            // Asignar destinatarios y enviar emails
            foreach ($request->destinatarios as $destinatarioId) {
                $destinatario = User::find($destinatarioId);

                // Asignar destinatario
                $nota->destinatarios()->attach($destinatarioId);

                // Enviar email si el destinatario tiene email
                if ($destinatario && !empty($destinatario->email)) {
                    try {
                        Mail::to($destinatario->email)
                            ->send(new NotaEquipoProyectoCreada($nota, Auth::user(), $destinatario));

                        \Log::info('Email de nota al equipo de proyecto enviado a:', [
                            'destinatario_id' => $destinatario->id,
                            'destinatario_email' => $destinatario->email,
                            'nota_id' => $nota->id
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error al enviar email de nota al equipo de proyecto:', [
                            'error' => $e->getMessage(),
                            'destinatario_id' => $destinatario->id,
                            'nota_id' => $nota->id
                        ]);
                    }
                } else {
                    \Log::warning('No se envió email a destinatario sin email:', [
                        'destinatario_id' => $destinatarioId,
                        'nota_id' => $nota->id
                    ]);
                }
            }

            // Procesar archivos adjuntos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $nombreOriginal = $archivo->getClientOriginalName();
                    $nombreArchivo = 'nota_equipo_' . $nota->id . '_' . time() . '_' . str_replace(' ', '_', $nombreOriginal);
                    $ruta = $archivo->storeAs('notas_equipo', $nombreArchivo, 'public');

                    $archivoAdjunto = new NotaEquipoArchivo();
                    $archivoAdjunto->nota_equipo_id = $nota->id;
                    $archivoAdjunto->nombre_original = $nombreOriginal;
                    $archivoAdjunto->nombre_archivo = $nombreArchivo;
                    $archivoAdjunto->ruta = $ruta;
                    $archivoAdjunto->tipo = $archivo->getClientMimeType();
                    $archivoAdjunto->tamano = $archivo->getSize();
                    $archivoAdjunto->save();
                }
            }

            DB::commit();

            return redirect()->route('obras.show', $obra->id)
                ->with('success', 'Nota para el equipo de proyecto enviada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la nota: ' . $e->getMessage());
        }
    }

   /**
 * Muestra una lista de todas las notas al equipo de proyecto
 */
public function index(Obra $obra)
{
    // Obtener todas las notas de la obra sin filtrar por usuario
    $notas = NotaEquipoProyecto::where('obra_id', $obra->id)
        ->with(['creador', 'destinatarios'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Obtener información del usuario actual
    $user = Auth::user();

    // Obtener información sobre los roles de los usuarios en la obra para mostrar en la vista
    $usuariosObra = $obra->usuarios->keyBy('id');

    // Contar notas por estado y tipo
    $notasEmitidas = $notas->where('estado', 'Emitida')->count();
    $notasFirmadas = $notas->where('estado', 'Firmada')->count();
    $notasRechazadas = $notas->where('estado', 'Rechazada')->count();
    $notasPropias = $notas->where('creador_id', $user->id)->count();
    $notasAjenas = $notas->where('creador_id', '!=', $user->id)->count();

    return view('notas-equipo-proyecto.index', compact('obra', 'notas', 'user', 'usuariosObra', 'notasEmitidas', 'notasFirmadas', 'notasRechazadas', 'notasPropias', 'notasAjenas'));
}

   
    /**
     * Muestra el formulario para editar una nota al equipo de proyecto
     */
    public function edit(Obra $obra, NotaEquipoProyecto $nota)
    {
       // $this->authorize('updateNotaEquipo', [$obra, $nota]);

        $equipoProyecto = $obra->usuarios->filter(function($usuario) {
            if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
            $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rol && in_array($rol->nombre, ['Jefe de Proyecto', 'Especialista']);
        });

        return view('notas-equipo-proyecto.edit', compact('obra', 'nota', 'equipoProyecto'));
    }

    /**
     * Actualiza una nota al equipo de proyecto
     */
    public function update(Request $request, Obra $obra, NotaEquipoProyecto $nota)
    {
        $this->authorize('updateNotaEquipo', [$obra, $nota]);

        $request->validate([
            'tema' => 'required|string|max:255',
            'contenido' => 'required|string',
            'fecha' => 'required|date',
            'destinatarios' => 'required|array|min:1',
            'destinatarios.*' => 'exists:users,id',
            'tipo_entrega' => 'required|string',
            'otro_tipo_entrega' => 'nullable|required_if:tipo_entrega,Otro|string|max:255',
            'plazo_entrega' => 'required|integer|min:1',
            'prioridad' => 'required|string|in:Normal,Alta,Urgente',
            'archivos.*' => 'nullable|file|max:10240', // Máximo 10MB por archivo
        ]);

        DB::beginTransaction();

        try {
            // Actualizar la nota
            $nota->tema = $request->tema;
            $nota->contenido = $request->contenido;
            $nota->fecha = $request->fecha;
            $nota->tipo_entrega = $request->tipo_entrega === 'Otro' ? $request->otro_tipo_entrega : $request->tipo_entrega;
            $nota->plazo_entrega = $request->plazo_entrega;
            $nota->prioridad = $request->prioridad;
            $nota->save();

            // Actualizar destinatarios
            $nota->destinatarios()->sync($request->destinatarios);

            // Procesar nuevos archivos adjuntos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $nombreOriginal = $archivo->getClientOriginalName();
                    $nombreArchivo = 'nota_equipo_' . $nota->id . '_' . time() . '_' . str_replace(' ', '_', $nombreOriginal);
                    $ruta = $archivo->storeAs('notas_equipo', $nombreArchivo, 'public');

                    $archivoAdjunto = new NotaEquipoArchivo();
                    $archivoAdjunto->nota_equipo_id = $nota->id;
                    $archivoAdjunto->nombre_original = $nombreOriginal;
                    $archivoAdjunto->nombre_archivo = $nombreArchivo;
                    $archivoAdjunto->ruta = $ruta;
                    $archivoAdjunto->tipo = $archivo->getClientMimeType();
                    $archivoAdjunto->tamano = $archivo->getSize();
                    $archivoAdjunto->save();
                }
            }

            DB::commit();

            return redirect()->route('obras.notas-equipo-proyecto.show', [$obra->id, $nota->id])
                ->with('success', 'Nota actualizada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la nota: ' . $e->getMessage());
        }
    }

    /**
     * Marca una nota como firmada
     */
   /* public function firmar(Obra $obra, NotaEquipoProyecto $nota)
    {
        //$this->authorize('firmarNotaEquipo', [$obra, $nota]);

        $nota->update(['estado' => 'Firmada']);

        return redirect()->route('obras.notas-equipo-proyecto.show', [$obra->id, $nota->id])
            ->with('success', 'Nota firmada con éxito.');
    }*/
 /**
 * Marca una nota como firmada con registro adicional
 */
public function firmar(Obra $obra, NotaEquipoProyecto $nota)
{
    $this->authorize('firmarNotaEquipo', [$obra, $nota]);

    // 1. Generar un hash del contenido actual
    $contenidoParaHash = $nota->tema . $nota->contenido . $nota->fecha . $nota->creador_id;
    $hashDocumento = hash('sha256', $contenidoParaHash);

    // 2. Registrar quién firmó y cuándo
    $nota->update([
        'estado' => 'Firmada',
        'firmado_por_id' => auth()->id(),
        'fecha_firma' => now(),
        'hash_firma' => $hashDocumento  // Añadir campo hash_firma a la tabla
    ]);

    // 3. Obtener el último número de orden
    $ultimoOrden = \DB::table('libro_obras')->where('obra_id', $obra->id)->max('orden');
    $nuevoOrden = $ultimoOrden ? $ultimoOrden + 1 : 1;

    // 4. Registrar en el libro de obra
    \DB::table('libro_obras')->insert([
        'obra_id' => $obra->id,
        'documento_type' => \App\Models\NotaEquipoProyecto::class,
        'documento_id' => $nota->id,
        'orden' => $nuevoOrden,
        'fecha_registro' => now(),
        'hash_documento' => $hashDocumento,  // Añadir campo hash_documento a la tabla
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // 5. Marcar como leída
    $destinatario = $nota->destinatarios()->where('user_id', auth()->id())->first();
    if ($destinatario && !$destinatario->pivot->leida) {
        $nota->destinatarios()->updateExistingPivot(auth()->id(), [
            'leida' => true,
            'fecha_lectura' => now()
        ]);
    }

    return redirect()->route('obras.notas-equipo-proyecto.show', [$obra->id, $nota->id])
        ->with('success', 'Nota firmada con éxito. Hash de verificación: ' . substr($hashDocumento, 0, 8) . '...');
}
    /**
     * Muestra la bandeja de entrada de notas al equipo de proyecto
     */
    public function bandeja(Obra $obra)
    {
        //$this->authorize('viewNotaEquipo', $obra);

        $user = Auth::user();

        // Obtener todas las notas donde el usuario es destinatario
        $notas = NotaEquipoProyecto::where('obra_id', $obra->id)
            ->whereHas('destinatarios', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['creador', 'destinatarios', 'archivos'])
            ->with(['destinatarios' => function($query) use ($user) {
                $query->where('user_id', $user->id)->select('user_id', 'leida');
            }])
            ->orderBy('leida', 'asc') // Mostrar primero las no leídas
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notas-equipo-proyecto.bandeja', compact('obra', 'notas'));
    }

    /**
     * Marca una nota como leída
     */
    public function marcarComoLeida(Obra $obra, NotaEquipoProyecto $nota)
    {
        //$this->authorize('viewNotaEquipo', [$obra, $nota]);

        // Verificar que el usuario es destinatario de la nota
        if ($nota->destinatarios->contains(Auth::id())) {
            $nota->destinatarios()->updateExistingPivot(Auth::id(), ['leida' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
    }

    /**
     * Actualiza el método show para marcar como leída al ver la nota
     */
    public function show(Obra $obra, NotaEquipoProyecto $nota)
    {
       // $this->authorize('viewNotaEquipo', [$obra, $nota]);
    
        $user = Auth::user();
    
        // Verificar si el usuario es destinatario de la nota
        $esDestinatario = $nota->destinatarios->contains($user->id);
    
        // Si es destinatario y no está leída, marcar como leída
        if ($esDestinatario) {
            $destinatario = $nota->destinatarios->find($user->id);
            if ($destinatario && !$destinatario->pivot->leida) {
                $nota->destinatarios()->updateExistingPivot($user->id, ['leida' => true]);
            }
        }
    
        return view('notas-equipo-proyecto.show', compact('obra', 'nota'));
    }
}