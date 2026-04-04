<?php

// app/Http/Controllers/OrdenServicioController.php
namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\Obra;
use App\Models\User;
use App\Models\LibroObra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use App\Models\Nota; 
use App\Models\RoleObra;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrdenServicioCreada;

class OrdenServicioController extends Controller
{
    // app/Http/Controllers/OrdenServicioController.php
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Headers para evitar el caché en todas las respuestas
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            return $next($request);
        });
    }

    public function index(Obra $obra)
    {
       
        $this->authorize('view', $obra);

        $ordenes = $obra->ordenesServicio()->with(['creador', 'destinatario', 'firmadoPor'])->get();

        return view('ordenes-servicio.index', compact('obra', 'ordenes'));
    }

   /* public function create(Obra $obra)
    {
        // Obtener el próximo número de orden de servicio
        $proximoNumero = OrdenServicio::where('obra_id', $obra->id)->max('Nro');
        $proximoNumero = $proximoNumero ? $proximoNumero + 1 : 1;
    
        // Obtener usuarios disponibles para asignar como destinatarios
        $usuarios = User::where('approved', true)->get(['id', 'name', 'first_name', 'last_name', 'organization']);
    
        // Filtrar usuarios que están asignados a la obra con roles adecuados
        $usuariosFiltrados = $usuarios->filter(function($usuario) use ($obra) {
            return $obra->usuarios->contains($usuario->id);
        });
    
        return view('ordenes-servicio.create', compact('obra', 'proximoNumero', 'usuariosFiltrados'));
    }*/
    public function create(Obra $obra)
    {
        // Obtener el próximo número de orden de servicio
        $proximoNumero = OrdenServicio::where('obra_id', $obra->id)->max('Nro');
        $proximoNumero = $proximoNumero ? $proximoNumero + 1 : 1;

        // Obtener el jefe de obra y asistente contratista de la obra
        $jefeObra = $obra->usuarios->first(function($usuario) {
            $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rolObra && $rolObra->nombre == 'Jefe de Obra';
        });

        $asistenteContratista = $obra->usuarios->first(function($usuario) {
            $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rolObra && $rolObra->nombre == 'Asistente Contratista';
        });

        return view('ordenes-servicio.create', compact('obra', 'proximoNumero', 'jefeObra', 'asistenteContratista'));
    }

    // app/Http/Controllers/OrdenServicioController.php
    // app/Http/Controllers/OrdenServicioController.php
   /* public function store(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'Nro' => 'required|integer',
            'fecha' => 'required|date_format:Y-m-d',
            'Tema' => 'required|string|max:255',
            'texto' => 'required|string',
            'Observaciones' => 'nullable|string',
            'fecha_vencimiento' => [
                'nullable',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $fechaEmision = \Carbon\Carbon::parse($request->fecha);
                        $fechaVencimiento = \Carbon\Carbon::parse($value);

                        if ($fechaVencimiento->lessThan($fechaEmision)) {
                            $fail('La fecha de vencimiento debe ser posterior a la fecha de emisión.');
                        }
                    }
                },
            ],
            'destinatario_id' => 'required|integer|exists:users,id',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Subir PDF si existe
        $pdfPath = null;
        if ($request->hasFile('pdf') && !$request->file('pdf')->getError()) {
            $pdfPath = $request->file('pdf')->store('ordenes_servicio_pdfs', 'public');
        }

        // Procesar la fecha de vencimiento
        $fechaVencimiento = null;
        if (isset($validated['fecha_vencimiento']) && $validated['fecha_vencimiento']) {
            $fechaVencimiento = \Carbon\Carbon::parse($validated['fecha_vencimiento'])->format('Y-m-d H:i:s');
        }

        // Crear la orden de servicio
        $ordenServicio = OrdenServicio::create([
            'obra_id' => $obra->id,
            'Nro' => $validated['Nro'],
            'numero' => $validated['Nro'],
            'Tipo' => 'OS',
            'fecha' => \Carbon\Carbon::parse($validated['fecha'])->format('Y-m-d H:i:s'),
            'fecha_vencimiento' => $fechaVencimiento,
            'Tema' => $validated['Tema'],
            'texto' => $validated['texto'],
            'Observaciones' => $validated['Observaciones'],
            'pdf_path' => $pdfPath,
            'creador_id' => auth()->id(),
            'destinatario_id' => $validated['destinatario_id'],
            'Estado' => 'Emitida',
            'firmada' => false,
        ]);

        return redirect()->route('obras.ordenes-servicio.show', [$obra->id, $ordenServicio->id])
            ->with('success', 'Orden de servicio creada con éxito.');
    } */

    // app/Http/Controllers/OrdenServicioController.php
public function store(Request $request, Obra $obra)
{
    // Validar que el número de orden no esté duplicado
    $request->validate([
        'Nro' => [
            'required',
            'integer',
            Rule::unique('ordenes_servicio')->where(function ($query) use ($obra) {
                return $query->where('obra_id', $obra->id);
            })
        ],
        'fecha' => 'required|date_format:Y-m-d',
        'Tema' => 'required|string|max:255',
        'texto' => 'required|string',
        'Observaciones' => 'nullable|string',
        'fecha_vencimiento' => [
            'nullable',
            'date_format:Y-m-d',
            function ($attribute, $value, $fail) use ($request) {
                if ($value) {
                    $fechaEmision = \Carbon\Carbon::parse($request->fecha);
                    $fechaVencimiento = \Carbon\Carbon::parse($value);

                    if ($fechaVencimiento->lessThan($fechaEmision)) {
                        $fail('La fecha de vencimiento debe ser posterior a la fecha de emisión.');
                    }
                }
            },
        ],
        'pdf' => 'nullable|file|mimes:pdf|max:10240',
        'texto_pdf' => 'nullable|string',
        'resumen_ai' => 'nullable|string',
        'generate_ai_summary' => 'nullable|integer',
    ]);

    // Verificar si ya existe una orden con este número para esta obra
    $ordenExistente = OrdenServicio::where('obra_id', $obra->id)
        ->where('Nro', $request->Nro)
        ->first();

    if ($ordenExistente) {
        return back()->with('error', 'Ya existe una orden de servicio con el número OS-' . str_pad($request->Nro, 4, '0', STR_PAD_LEFT) . ' para esta obra.');
    }

    // Obtener el jefe de obra y asistente contratista de la obra
    $jefeObra = $obra->usuarios->first(function($usuario) {
        $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
        return $rolObra && $rolObra->nombre == 'Jefe de Obra';
    });

    $asistenteContratista = $obra->usuarios->first(function($usuario) {
        $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
        return $rolObra && $rolObra->nombre == 'Asistente Contratista';
    });

    // Verificar que al menos haya un destinatario
    if (!$jefeObra && !$asistenteContratista) {
        return back()->with('error', 'No hay responsables asignados a esta obra para recibir la orden de servicio.');
    }

    // Inicializar variables para el PDF
    $pdfPath = null;
    $textoPDF = $request->texto_pdf ?? null;
    $resumenAI = $request->resumen_ai ?? null;

    // Procesar el PDF si existe
    if ($request->hasFile('pdf') && !$request->file('pdf')->getError()) {
        try {
            $pdfFile = $request->file('pdf');
            $pdfPath = $pdfFile->store('ordenes_servicio_pdfs', 'public');

            // Extraer texto del PDF si no se recibió texto_pdf
            if (empty($textoPDF)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($pdfFile->getRealPath());
                $textoPDF = '';
                foreach ($pdf->getPages() as $page) {
                    $textoPDF .= $page->getText() . "\n\n";
                }

                // Limpiar el texto extraído
                $textoPDF = mb_convert_encoding($textoPDF, 'UTF-8', mb_detect_encoding($textoPDF));
                $textoPDF = preg_replace('/[^\P{L}\P{N}\s\-\,\.\;\:\!\?\(\)\/\"]/u', ' ', $textoPDF);
                $textoPDF = trim(preg_replace('/\s+/', ' ', $textoPDF));
            }

            // Generar resumen con IA si se solicitó y no se recibió resumen_ai
            if (empty($resumenAI) && !empty($textoPDF) && ($request->generate_ai_summary ?? 0) == 1) {
                $aiResult = $this->generarResumenConGemini($textoPDF, 100);
                if ($aiResult['success']) {
                    $resumenAI = $aiResult['resumen'];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error al procesar PDF: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar el PDF: ' . $e->getMessage());
        }
    }

    // Procesar la fecha de vencimiento
    $fechaVencimiento = null;
    if (isset($request->fecha_vencimiento) && $request->fecha_vencimiento) {
        $fechaVencimiento = \Carbon\Carbon::parse($request->fecha_vencimiento)->format('Y-m-d H:i:s');
    }

    // Función para enviar email a un destinatario
    function enviarEmailOrdenServicio($ordenServicio, $destinatario) {
        try {
            Mail::to($destinatario->email)
                ->send(new \App\Mail\OrdenServicioCreada($ordenServicio, auth()->user(), $destinatario));

            \Log::info('Email de orden de servicio enviado a:', [
                'destinatario_id' => $destinatario->id,
                'destinatario_email' => $destinatario->email,
                'orden_servicio_id' => $ordenServicio->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de orden de servicio:', [
                'error' => $e->getMessage(),
                'destinatario_id' => $destinatario->id,
                'orden_servicio_id' => $ordenServicio->id
            ]);
        }
    }

    // Crear la orden de servicio para el jefe de obra si existe
    if ($jefeObra) {
        $ordenServicioData = [
            'obra_id' => $obra->id,
            'Nro' => $request->Nro,
            'numero' => $request->Nro,
            'Tipo' => 'OS',
            'fecha' => \Carbon\Carbon::parse($request->fecha)->format('Y-m-d H:i:s'),
            'fecha_vencimiento' => $fechaVencimiento,
            'Tema' => $request->Tema,
            'texto' => $request->texto,
            'Observaciones' => $request->Observaciones,
            'pdf_path' => $pdfPath,
            'texto_pdf' => $textoPDF,
            'resumen_ai' => $resumenAI,
            'creador_id' => auth()->id(),
            'destinatario_id' => $jefeObra->id,
            'Estado' => 'Emitida',
            'firmada' => false,
        ];

        $ordenServicio = OrdenServicio::create($ordenServicioData);

        // Enviar email al jefe de obra
        enviarEmailOrdenServicio($ordenServicio, $jefeObra);
    }

    // Si hay asistente contratista, crear una copia de la orden para él
    if ($asistenteContratista) {
        $ordenServicioData = [
            'obra_id' => $obra->id,
            'Nro' => $request->Nro,
            'numero' => $request->Nro,
            'Tipo' => 'OS',
            'fecha' => \Carbon\Carbon::parse($request->fecha)->format('Y-m-d H:i:s'),
            'fecha_vencimiento' => $fechaVencimiento,
            'Tema' => $request->Tema,
            'texto' => $request->texto,
            'Observaciones' => $request->Observaciones,
            'pdf_path' => $pdfPath,
            'texto_pdf' => $textoPDF,
            'resumen_ai' => $resumenAI,
            'creador_id' => auth()->id(),
            'destinatario_id' => $asistenteContratista->id,
            'Estado' => 'Emitida',
            'firmada' => false,
        ];

        $ordenServicio = OrdenServicio::create($ordenServicioData);

        // Enviar email al asistente contratista
        enviarEmailOrdenServicio($ordenServicio, $asistenteContratista);
    }

    return redirect()->route('obras.show', $obra->id)
        ->with('success', 'Orden de servicio creada con éxito.')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
}
    // app/Http/Controllers/OrdenServicioController.php
   /* public function show(Obra $obra, OrdenServicio $orden_servicio)
    {
        //$this->authorize('view', [$obra, $orden_servicio]);

        if ($orden_servicio->obra_id != $obra->id) {
            abort(404, 'La orden de servicio no pertenece a esta obra');
        }

        return view('ordenes-servicio.show', [
            'obra' => $obra,
            'ordenServicio' => $orden_servicio
        ]);
    }*/

    public function show(Obra $obra, OrdenServicio $orden_servicio)
{
    //$this->authorize('view', [$obra, $orden_servicio]);

    // Verificar que la orden de servicio pertenezca a la obra
    if ($orden_servicio->obra_id != $obra->id) {
        abort(404, 'La orden de servicio no pertenece a esta obra');
    }

    // Marcar como leída si el usuario actual es el destinatario
    if (auth()->user()->id == $orden_servicio->destinatario_id && !$orden_servicio->leida) {
        $orden_servicio->update(['leida' => true]);
    }

    return view('ordenes-servicio.show', [
        'obra' => $obra,
        'ordenServicio' => $orden_servicio // Pasamos la variable con el nombre correcto
    ]);
}

    public function edit(Obra $obra, OrdenServicio $orden)
    {
        //$this->authorize('update', $orden);

        $usuarios = User::where('approved', true)->get(['id', 'name', 'first_name', 'last_name', 'organization']);

        return view('ordenes-servicio.edit', compact('obra', 'usuarios', 'orden'));
    }


    public function update(Request $request, Obra $obra, OrdenServicio $orden)
    {
        $this->authorize('update', $orden);

        $validated = $request->validate([
            'numero' => 'required|integer',
            'tema' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after:fecha_emision',
            'destinatario_id' => 'required|exists:users,id',
            'pdf' => 'nullable|mimes:pdf|max:20480'
        ]);

        try {
            $orden->update($validated);

            if ($request->hasFile('pdf')) {
                $this->procesarPDF($request->file('pdf'), $orden);
            }

            return redirect()->route('obras.ordenes-servicio.show', [$obra->id, $orden->id])
                             ->with('success', 'Orden de Servicio actualizada con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar orden de servicio: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la orden de servicio: ' . $e->getMessage());
        }
    }

   // app/Http/Controllers/OrdenServicioController.php
    public function firmar(Request $request, Obra $obra, OrdenServicio $orden)
    {
        $this->authorize('firmar', $orden);

        $request->validate([
            'firma' => 'required|string'
        ]);

        try {
            $orden->update([
                'firmado_por' => auth()->id(),
                'firma_fecha' => now(),
                'estado' => 'Firmado'
            ]);

            // Agregar al Libro de Obra
            LibroObra::create([
                'obra_id' => $obra->id,
                'documento_type' => OrdenServicio::class,
                'documento_id' => $orden->id,
                'orden' => LibroObra::where('obra_id', $obra->id)->max('orden') + 1,
                'fecha_registro' => now()
            ]);

            return redirect()->route('obras.ordenes-servicio.show', [$obra->id, $orden->id])
                            ->with('success', 'Orden de Servicio firmada y agregada al Libro de Obra.');
        } catch (\Exception $e) {
            Log::error('Error al firmar orden de servicio: ' . $e->getMessage());
            return back()->with('error', 'Error al firmar la orden de servicio: ' . $e->getMessage());
        }
    }


    protected function procesarPDF($file, OrdenServicio $orden)
    {
        $filename = 'orden_servicio_' . $orden->id . '.pdf';
        $path = $file->storeAs('pdfs/ordenes-servicio', $filename, 'public');

        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());
        $textoPDF = '';
        foreach ($pdf->getPages() as $page) {
            $textoPDF .= $page->getText() . "\n\n";
        }

        $orden->update([
            'pdf_path' => $path,
            'texto_pdf' => $textoPDF
        ]);
    }

   // app/Http/Controllers/OrdenServicioController.php
// app/Http/Controllers/OrdenServicioController.php
    public function createFromNotaPedido(Obra $obra, Nota $nota)
    {
        //$this->authorize('create', [OrdenServicio::class, $obra]);

        // Verificar que el usuario sea inspector
        $user = auth()->user();
        $esInspector = false;
        if ($obra->usuarios->contains($user->id)) {
            $pivot = $obra->usuarios->find($user->id)->pivot;
            if ($pivot->rol_id) {
                $rolObra = RoleObra::find($pivot->rol_id);
                $esInspector = $rolObra && in_array($rolObra->nombre, ['Inspector Principal', 'Asistente Inspección']);
            }
        }

        if (!$esInspector) {
            abort(403, 'No tienes permiso para crear órdenes de servicio en esta obra');
        }

        // Obtener el próximo número de orden de servicio
        $proximoNumero = OrdenServicio::where('obra_id', $obra->id)->max('Nro');
        $proximoNumero = $proximoNumero ? $proximoNumero + 1 : 1;

        return view('ordenes-servicio.create_from_np', compact('obra', 'nota', 'proximoNumero'));
    }
// app/Http/Controllers/OrdenServicioController.php
// app/Http/Controllers/OrdenServicioController.php
// app/Http/Controllers/OrdenServicioController.php
public function storeFromNotaPedido(Request $request, Obra $obra, Nota $nota)
{
    \Log::info('Datos recibidos para crear orden de servicio:', $request->all());

    $validated = $request->validate([
        'Nro' => 'required|integer',
        'fecha' => 'required|date_format:Y-m-d',
        'Tema' => 'required|string|max:255',
        'texto' => 'required|string',
        'Observaciones' => 'nullable|string',
        'pdf' => 'nullable|file|mimes:pdf|max:10240',
        'texto_pdf' => 'nullable|string',
        'resumen_ai' => 'nullable|string',
        'usar_resumen_ai' => 'nullable|integer',
    ]);

    try {
        // Subir PDF si existe
        $pdfPath = null;
        if ($request->hasFile('pdf') && !$request->file('pdf')->getError()) {
            $pdfPath = $request->file('pdf')->store('ordenes_servicio_pdfs', 'public');
        }

        // Obtener los destinatarios del binomio de contratistas (Jefe de Obra y Asistente Contratista)
        $jefeObra = $obra->usuarios->first(function($usuario) {
            $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rolObra && $rolObra->nombre == 'Jefe de Obra';
        });

        $asistenteContratista = $obra->usuarios->first(function($usuario) {
            $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            return $rolObra && $rolObra->nombre == 'Asistente Contratista';
        });

        // Verificar que al menos haya un destinatario en el binomio de contratistas
        if (!$jefeObra && !$asistenteContratista) {
            \Log::error('No se encontraron destinatarios en el binomio de contratistas:', [
                'obra_id' => $obra->id,
                'nota_id' => $nota->id
            ]);
            return back()->with('error', 'No se encontraron destinatarios válidos para esta orden de servicio.');
        }

        // Preparar los destinatarios
        $destinatarios = [];
        if ($jefeObra) {
            $destinatarios[] = $jefeObra;
        }
        if ($asistenteContratista) {
            $destinatarios[] = $asistenteContratista;
        }

        // Datos base de la orden de servicio
        $ordenServicioData = [
            'obra_id' => $obra->id,
            'nota_pedido_id' => $nota->id,
            'Nro' => $validated['Nro'],
            'numero' => $validated['Nro'],
            'Tipo' => 'OS',
            'fecha' => $validated['fecha'],
            'Tema' => $validated['Tema'],
            'texto' => $validated['texto'],
            'resumen_ai' => $validated['resumen_ai'],
            'texto_pdf' => $validated['texto_pdf'],
            'Observaciones' => $validated['Observaciones'],
            'pdf_path' => $pdfPath,
            'creador_id' => auth()->id(),
            'Estado' => 'Emitida',
            'firmada' => false,
            'usar_resumen_ai' => $request->has('usar_resumen_ai') && $request->usar_resumen_ai == 1,
        ];

        // Crear órdenes de servicio para cada destinatario del binomio de contratistas
        $ordenesCreadas = [];
        foreach ($destinatarios as $destinatario) {
            $ordenServicio = OrdenServicio::create($ordenServicioData + ['destinatario_id' => $destinatario->id]);

            // Enviar email al destinatario si tiene email
            if ($destinatario && !empty($destinatario->email)) {
                try {
                    Mail::to($destinatario->email)
                        ->send(new OrdenServicioCreada($ordenServicio, auth()->user(), $destinatario));

                    \Log::info('Email de orden de servicio enviado correctamente:', [
                        'destinatario_id' => $destinatario->id,
                        'destinatario_email' => $destinatario->email,
                        'orden_servicio_id' => $ordenServicio->id,
                        'email_sent' => true
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error al enviar email de orden de servicio:', [
                        'error' => $e->getMessage(),
                        'destinatario_id' => $destinatario->id,
                        'destinatario_email' => $destinatario->email,
                        'orden_servicio_id' => $ordenServicio->id,
                        'exception_trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                \Log::warning('No se envió email: destinatario sin dirección de correo:', [
                    'destinatario_id' => $destinatario->id,
                    'destinatario_name' => $destinatario->name,
                    'orden_servicio_id' => $ordenServicio->id
                ]);
            }

            $ordenesCreadas[] = $ordenServicio;
        }

        // Marcar la nota de pedido como respondida
        $nota->update([
            'Estado' => 'Respondida con OS',
            'Respondida_por' => auth()->user()->name,
            'Rta_a_NP' => now(),
        ]);

        // Log para depuración
        \Log::info('Orden(es) de servicio creada(s) con éxito:', [
            'ordenes_creadas' => count($ordenesCreadas),
            'destinatarios' => array_map(function($d) { return $d->id; }, $destinatarios),
            'jefe_obra_id' => $jefeObra ? $jefeObra->id : null,
            'asistente_contratista_id' => $asistenteContratista ? $asistenteContratista->id : null,
            'obra_id' => $obra->id,
            'user_id' => auth()->id(),
            'Nro' => $validated['Nro'],
            'nota_pedido_id' => $nota->id
        ]);

        // Redirigir a la primera orden de servicio creada
        return redirect()->route('obras.ordenes-servicio.show', [$obra->id, $ordenesCreadas[0]->id])
                        ->with('success', 'Orden de Servicio N°' . $validated['Nro'] . ' creada con éxito como respuesta a la nota de pedido.');
    } catch (\Exception $e) {
        \Log::error('Error al crear orden de servicio: ' . $e->getMessage(), [
            'exception' => $e,
            'obra_id' => $obra->id,
            'nota_id' => $nota->id,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
        return back()->with('error', 'Error al crear la orden de servicio: ' . $e->getMessage());
    }
}

// app/Http/Controllers/OrdenServicioController.php
public function cumplir(Obra $obra, OrdenServicio $orden_servicio)
{
    // $this->authorize('cumplir', [$obra, $orden_servicio]);

    if (auth()->user()->id != $orden_servicio->destinatario_id) {
        abort(403, 'Solo el destinatario puede marcar esta orden como cumplida');
    }

    $orden_servicio->update([
        'Estado' => 'Cumplida',
        'cumplida_por' => auth()->user()->name,
        'fecha_cumplimiento' => now(),
    ]);

    // Cambié la redirección para que vaya al índice de órdenes de servicio de la obra
    return redirect()->route('obras.show', $obra->id)
        ->with('success', 'Orden de servicio marcada como cumplida.');
}

/*public function misOrdenes(Obra $obra)
{
    $this->authorize('view', $obra);

    // Obtener solo las órdenes de servicio creadas por el usuario actual en esta obra
    $ordenes = OrdenServicio::where('obra_id', $obra->id)
        ->where('creador_id', auth()->id())
        ->with(['creador', 'destinatario', 'firmadoPor'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('ordenes-servicio.mis_ordenes', compact('obra', 'ordenes'));
}*/
// app/Http/Controllers/OrdenServicioController.php
public function misOrdenes(Obra $obra)
{
    $this->authorize('view', $obra);

    $user = auth()->user();

    // Obtener los IDs de los inspectores de esta obra
    $inspectorIds = [];
    foreach ($obra->usuarios as $usuario) {
        if ($usuario->pivot && $usuario->pivot->rol_id) {
            $rol = \App\Models\RoleObra::find($usuario->pivot->rol_id);
            if ($rol && in_array($rol->nombre, ['Inspector Principal', 'Asistente Inspección'])) {
                $inspectorIds[] = $usuario->id;
            }
        }
    }

    // Obtener todas las órdenes de servicio creadas por inspectores de esta obra
    $ordenes = OrdenServicio::where('obra_id', $obra->id)
        ->whereIn('creador_id', $inspectorIds)
        ->with(['creador', 'destinatario', 'firmadoPor'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Agrupar órdenes por tema, fecha, creador y estado (para mostrar en una sola línea)
    $ordenesAgrupadas = [];
    foreach ($ordenes as $orden) {
        $key = $orden->Tema . '|' . $orden->fecha . '|' . $orden->creador_id . '|' . $orden->Estado;

        if (!isset($ordenesAgrupadas[$key])) {
            $ordenesAgrupadas[$key] = [
                'orden' => $orden,
                'destinatarios' => []
            ];
        }

        // Asegurarse de que el destinatario no sea nulo
        if ($orden->destinatario) {
            $ordenesAgrupadas[$key]['destinatarios'][] = $orden->destinatario;
        }
    }

    // Verificar si el usuario actual es inspector
    $esInspector = in_array($user->id, $inspectorIds);

    return view('ordenes-servicio.mis_ordenes', compact('obra', 'ordenesAgrupadas', 'esInspector'));
}
// app/Http/Controllers/OrdenServicioController.php
// app/Http/Controllers/OrdenServicioController.php
public function bandejaOrdenes(Obra $obra)
{
    //$this->authorize('view', $obra);

    // Obtener las órdenes de servicio dirigidas al usuario actual en esta obra
    $ordenesRecibidas = OrdenServicio::where('obra_id', $obra->id)
        ->where('destinatario_id', auth()->id())
        ->with(['creador', 'destinatario'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('ordenes-servicio.bandeja', compact('obra', 'ordenesRecibidas'));
}

public function extraerTexto(Request $request)
{
    $request->validate([
        'pdf' => 'required|mimes:pdf|max:10240',
    ]);

    try {
        $file = $request->file('pdf');
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());

        $textoPDF = '';
        foreach ($pdf->getPages() as $page) {
            $textoPDF .= $page->getText() . "\n\n";
        }

        // Limpiar el texto
        $textoPDF = mb_convert_encoding($textoPDF, 'UTF-8', mb_detect_encoding($textoPDF));
        $textoPDF = preg_replace('/[^\P{L}\P{N}\s\-\,\.\;\:\!\?\(\)\/\"]/u', ' ', $textoPDF);
        $textoPDF = trim(preg_replace('/\s+/', ' ', $textoPDF));

        return response()->json([
            'success' => true,
            'texto_pdf' => $textoPDF
        ]);

    } catch (\Exception $e) {
        Log::error('Error al extraer texto del PDF: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al extraer texto del PDF: ' . $e->getMessage()
        ]);
    }
}

public function generarResumen(Request $request, $orden)
{
    $request->validate([
        'texto_pdf' => 'required|string',
        'cantidad_palabras' => 'required|integer|min:10|max:500'
    ]);

    try {
        $result = $this->generarResumenConGemini($request->texto_pdf, $request->cantidad_palabras);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'resumen' => $result['resumen']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Error al generar resumen AI: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al generar el resumen: ' . $e->getMessage()
        ]);
    }
}

// Método para generar resumen con Gemini (copiado del controlador de notas de pedido)
private function generarResumenConGemini($textoPDF, $cantidadPalabras = 100)
{
    $apiKey = env('GOOGLE_AI_API_KEY');
    $modelName = 'gemini-2.5-flash';
    $url = "https://generativelanguage.googleapis.com/v1/models/{$modelName}:generateContent?key={$apiKey}";

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => "Actúa como un experto en análisis de documentos pero no digas quien eres. Resume el siguiente texto en un solo párrafo claro, conciso y coherente, destacando los puntos clave del documento. Usa un máximo de {$cantidadPalabras} palabras. No copies texto directamente, sino que genera un resumen inteligente que refleje el contenido principal. Si el documento es una orden de servicio, no menciones el número de orden, el tema, la fecha, y ese tipo de datos. Si el documento es técnico, explica de manera sencilla los puntos clave."
                        ],
                        [
                            'text' => $textoPDF
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 4096,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH']
            ]
        ]);

        $result = $response->json();

        if ($response->successful() && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true,
                'resumen' => $result['candidates'][0]['content']['parts'][0]['text']
            ];
        }

        return [
            'success' => false,
            'message' => 'No se recibió una respuesta válida de Gemini.'
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Error al generar el resumen: ' . $e->getMessage()
        ];
    }
}

}
