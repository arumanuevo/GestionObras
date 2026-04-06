<?php

// app/Http/Controllers/EntregaContratistaController.php
namespace App\Http\Controllers;

use App\Models\EntregaContratista;
use App\Models\EntregaContratistaArchivo;
use App\Models\Obra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\EntregaContratistaCreada;
use Illuminate\Support\Facades\Mail; 

class EntregaContratistaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('approved');
    }

    /**
     * Muestra el formulario para crear una nueva entrega al contratista
     */
    public function create(Obra $obra)
    {
       // $this->authorize('createEntregaContratista', $obra);

        // Obtener los contratistas de la obra
        $contratistas = $obra->usuarios->filter(function($usuario) {
            if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
            $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rol && in_array($rol->nombre, ['Jefe de Obra', 'Asistente Contratista']);
        });

        return view('entregas-contratista.create', compact('obra', 'contratistas'));
    }

    /**
     * Almacena una nueva entrega al contratista
     */
   // app/Http/Controllers/EntregaContratistaController.php
   public function store(Request $request)
{
    // Obtener el ID de la obra desde la URL
    $obraId = $request->route('obra');

    if (empty($obraId)) {
        return back()->with('error', 'No se ha especificado una obra válida');
    }

    $obra = Obra::find($obraId);

    if (empty($obra)) {
        return back()->with('error', 'La obra especificada no existe');
    }

    // Log de los datos recibidos y del objeto obra
    \Log::info('Datos recibidos en store:', $request->all());
    \Log::info('Objeto obra:', ['id' => $obra->id, 'nombre' => $obra->nombre]);

    try {
        $validated = $request->validate([
            'numero' => 'required|integer',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'destinatarios' => 'required|array|min:1',
            'destinatarios.*' => 'exists:users,id',
            'tipo_entrega' => 'required|string',
            'otro_tipo_entrega' => 'nullable|required_if:tipo_entrega,Otro|string|max:255',
            'plazo_entrega' => 'required|integer|min:1',
            'prioridad' => 'required|string|in:Normal,Alta,Urgente',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        \Log::info('Validación exitosa:', $validated);

        DB::beginTransaction();

        try {
            // Verificar que el número de entrega sea único para esta obra
            $numeroExistente = EntregaContratista::where('obra_id', $obra->id)
                ->where('numero', $request->numero)
                ->exists();

            if ($numeroExistente) {
                // Si el número ya existe, buscar el próximo número disponible
                $ultimoNumero = EntregaContratista::where('obra_id', $obra->id)
                    ->max('numero');

                $nuevoNumero = $ultimoNumero ? $ultimoNumero + 1 : 1;

                \Log::warning('Número de entrega duplicado. Asignando nuevo número:', [
                    'numero_solicitado' => $request->numero,
                    'nuevo_numero' => $nuevoNumero
                ]);

                $validated['numero'] = $nuevoNumero;
            } else {
                $nuevoNumero = $request->numero;
            }

            // Crear la entrega
            $entrega = new EntregaContratista();
            $entrega->obra_id = $obra->id;
            $entrega->numero = $nuevoNumero;
            $entrega->asunto = $request->asunto;
            $entrega->descripcion = $request->descripcion;
            $entrega->fecha = $request->fecha;
            $entrega->creador_id = Auth::id();
            $entrega->tipo_entrega = $request->tipo_entrega === 'Otro' ? $request->otro_tipo_entrega : $request->tipo_entrega;
            $entrega->plazo_recepcion = $request->plazo_entrega;
            $entrega->prioridad = $request->prioridad;
            $entrega->estado = 'Emitida';

            \Log::info('Datos de la entrega antes de guardar:', $entrega->toArray());

            $entrega->save();

            // Asignar destinatarios y enviar emails
            foreach ($request->destinatarios as $destinatarioId) {
                $destinatario = User::find($destinatarioId);

                // Asignar destinatario
                $entrega->destinatarios()->attach($destinatarioId);

                // Enviar email si el destinatario tiene email
                if ($destinatario && !empty($destinatario->email)) {
                    try {
                        Mail::to($destinatario->email)
                            ->send(new EntregaContratistaCreada($entrega, Auth::user(), $destinatario));

                        \Log::info('Email de entrega al contratista enviado a:', [
                            'destinatario_id' => $destinatario->id,
                            'destinatario_email' => $destinatario->email,
                            'entrega_id' => $entrega->id
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error al enviar email de entrega al contratista:', [
                            'error' => $e->getMessage(),
                            'destinatario_id' => $destinatario->id,
                            'entrega_id' => $entrega->id
                        ]);
                    }
                } else {
                    \Log::warning('No se envió email a destinatario sin email:', [
                        'destinatario_id' => $destinatarioId,
                        'entrega_id' => $entrega->id
                    ]);
                }
            }

            // Procesar archivos adjuntos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $nombreOriginal = $archivo->getClientOriginalName();
                    $nombreArchivo = 'entrega_contratista_' . $entrega->id . '_' . time() . '_' . str_replace(' ', '_', $nombreOriginal);
                    $ruta = $archivo->storeAs('entregas_contratista', $nombreArchivo, 'public');

                    $archivoAdjunto = new EntregaContratistaArchivo();
                    $archivoAdjunto->entrega_id = $entrega->id;
                    $archivoAdjunto->nombre_original = $nombreOriginal;
                    $archivoAdjunto->nombre_archivo = $nombreArchivo;
                    $archivoAdjunto->ruta = $ruta;
                    $archivoAdjunto->tipo = $archivo->getClientMimeType();
                    $archivoAdjunto->tamano = $archivo->getSize();
                    $archivoAdjunto->save();
                }
            }

            DB::commit();

            // Mensaje de éxito con el número de entrega asignado
            $mensaje = $nuevoNumero == $request->numero
                ? 'Entrega al contratista enviada con éxito.'
                : 'Entrega al contratista enviada con éxito. Se asignó el número EC-' . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT) . ' ya que el número solicitado ya estaba en uso.';

            return redirect()->route('obras.entregas-contratista.index', $obra->id)
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al guardar la entrega:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al crear la entrega: ' . $e->getMessage());
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Error de validación:', ['error' => $e->errors()]);
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        \Log::error('Error inesperado:', ['error' => $e->getMessage()]);
        return back()->with('error', 'Error inesperado: ' . $e->getMessage());
    }
}
    /**
     * Muestra una lista de las entregas al contratista
     */
    public function index(Obra $obra)
    {
        //$this->authorize('viewEntregaContratista', $obra);

        $user = Auth::user();
        $entregas = EntregaContratista::where('obra_id', $obra->id)
            ->where('creador_id', $user->id)
            ->with(['destinatarios', 'archivos'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener los contratistas de la obra para el modal de creación
        $contratistas = $obra->usuarios->filter(function($usuario) {
            if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
            $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rol && in_array($rol->nombre, ['Jefe de Obra', 'Asistente Contratista']);
        });

        return view('entregas-contratista.index', compact('obra', 'entregas', 'contratistas'));
    }
    /**
     * Muestra una entrega específica al contratista
     */
    public function show(Obra $obra, EntregaContratista $entrega)
    {
        //$this->authorize('viewEntregaContratista', [$obra, $entrega]);

        return view('entregas-contratista.show', compact('obra', 'entrega'));
    }

    /**
     * Muestra el formulario para editar una entrega al contratista
     */
    public function edit(Obra $obra, EntregaContratista $entrega)
    {
       // $this->authorize('updateEntregaContratista', [$obra, $entrega]);

        $contratistas = $obra->usuarios->filter(function($usuario) {
            if (!$usuario->pivot || !$usuario->pivot->rol_id) return false;
            $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rol && in_array($rol->nombre, ['Jefe de Obra', 'Asistente Contratista']);
        });

        return view('entregas-contratista.edit', compact('obra', 'entrega', 'contratistas'));
    }

    /**
     * Actualiza una entrega al contratista
     */
    public function update(Request $request, Obra $obra, EntregaContratista $entrega)
    {
       // $this->authorize('updateEntregaContratista', [$obra, $entrega]);

        $request->validate([
            'asunto' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'destinatarios' => 'required|array|min:1',
            'destinatarios.*' => 'exists:users,id',
            'tipo_entrega' => 'required|string',
            'otro_tipo_entrega' => 'nullable|required_if:tipo_entrega,Otro|string|max:255',
            'plazo_recepcion' => 'required|integer|min:1',
            'prioridad' => 'required|string|in:Normal,Alta,Urgente',
            'archivos.*' => 'nullable|file|max:10240', // Máximo 10MB por archivo
        ]);

        DB::beginTransaction();

        try {
            // Actualizar la entrega
            $entrega->asunto = $request->asunto;
            $entrega->descripcion = $request->descripcion;
            $entrega->fecha = $request->fecha;
            $entrega->tipo_entrega = $request->tipo_entrega === 'Otro' ? $request->otro_tipo_entrega : $request->tipo_entrega;
            $entrega->plazo_recepcion = $request->plazo_recepcion;
            $entrega->prioridad = $request->prioridad;
            $entrega->save();

            // Actualizar destinatarios
            $entrega->destinatarios()->sync($request->destinatarios);

            // Procesar nuevos archivos adjuntos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $nombreOriginal = $archivo->getClientOriginalName();
                    $nombreArchivo = 'entrega_contratista_' . $entrega->id . '_' . time() . '_' . str_replace(' ', '_', $nombreOriginal);
                    $ruta = $archivo->storeAs('entregas_contratista', $nombreArchivo, 'public');

                    $archivoAdjunto = new EntregaContratistaArchivo();
                    $archivoAdjunto->entrega_id = $entrega->id;
                    $archivoAdjunto->nombre_original = $nombreOriginal;
                    $archivoAdjunto->nombre_archivo = $nombreArchivo;
                    $archivoAdjunto->ruta = $ruta;
                    $archivoAdjunto->tipo = $archivo->getClientMimeType();
                    $archivoAdjunto->tamano = $archivo->getSize();
                    $archivoAdjunto->save();
                }
            }

            DB::commit();

            return redirect()->route('obras.entregas-contratista.show', [$obra->id, $entrega->id])
                ->with('success', 'Entrega actualizada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la entrega: ' . $e->getMessage());
        }
    }

    /**
     * Marca una entrega como recibida
     */
    public function recibir(Obra $obra, EntregaContratista $entrega)
    {
        //$this->authorize('recibirEntregaContratista', [$obra, $entrega]);

        $user = Auth::user();

        // Verificar que el usuario es destinatario de la entrega
        if (!$entrega->destinatarios->contains($user->id)) {
            return back()->with('error', 'No estás autorizado para recibir esta entrega.');
        }

        // Marcar como recibida para este usuario
        $entrega->destinatarios()->updateExistingPivot($user->id, [
            'recibida' => true,
            'fecha_recepcion' => now()
        ]);

        // Verificar si todos los destinatarios han recibido la entrega
        $todosRecibidos = $entrega->destinatarios->every(function($destinatario) {
            return $destinatario->pivot->recibida;
        });

        // Si todos han recibido, marcar la entrega como recibida
        if ($todosRecibidos) {
            $entrega->update([
                'recibida' => true,
                'fecha_recepcion' => now(),
                'estado' => 'Recibida'
            ]);
            return redirect()->route('obras.entregas-contratista.bandeja', [$obra->id, $entrega->id])
                ->with('success', 'Entrega recibida con éxito. Todos los destinatarios han recibido la entrega.');
        } else {
            return redirect()->route('obras.entregas-contratista.bandeja', [$obra->id, $entrega->id])
                ->with('success', 'Entrega marcada como recibida.');
        }
    }

    /**
     * Muestra la bandeja de entrada de entregas al contratista
     */
    // En el método bandeja del controlador EntregaContratistaController
    public function bandeja(Obra $obra)
    {
        // Obtener todas las entregas de la obra sin filtrar por usuario
        $entregas = EntregaContratista::where('obra_id', $obra->id)
            ->with(['creador', 'destinatarios', 'archivos'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Obtener información del usuario actual para resaltar sus entregas
        $user = Auth::user();
    
        // Contar entregas no recibidas por el usuario actual
        $entregasNoRecibidas = 0;
        foreach ($entregas as $entrega) {
            $destinatario = $entrega->destinatarios->where('id', $user->id)->first();
            if ($destinatario && !$destinatario->pivot->recibida) {
                $entregasNoRecibidas++;
            }
        }
    
        // Obtener información sobre los roles de los usuarios en la obra para mostrar en la vista
        $usuariosObra = $obra->usuarios->keyBy('id');
    
        return view('entregas-contratista.bandeja', compact('obra', 'entregas', 'entregasNoRecibidas', 'user', 'usuariosObra'));
    }
}