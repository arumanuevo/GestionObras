<?php
// app/Http/Controllers/NotaPedidoController.php
namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Obra;
use App\Models\User;
use App\Models\LibroObra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use App\Notifications\NotaAsignadaNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class NotaPedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   /* public function index(Obra $obra)
    {
      
        $user = auth()->user();
    
        $notas = Nota::where('obra_id', $obra->id)
                    ->where('Tipo', 'NP')
                    ->where(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhere('destinatario_id', $user->id)
                              ->orWhere(function($query) use ($user) {
                                  // Si es admin, puede ver todas las notas
                                  if ($user->hasRole('admin')) {
                                      $query->where('id', '>', 0);
                                  }
                              });
                    })
                    ->with(['creador', 'destinatario'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    
        return view('notas-pedido.index', compact('obra', 'notas', 'user'));
    }*/
    public function index(Obra $obra)
    {
        $user = auth()->user();
    
        // Obtener todas las notas de pedido de la obra
        $notas = Nota::where('obra_id', $obra->id)
                    ->where('Tipo', 'NP')
                    ->with(['creador', 'destinatario'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    
        // Agrupar notas por tema, fecha y creador (para mostrar en una sola línea)
        $notasAgrupadas = [];
        foreach ($notas as $nota) {
            $key = $nota->Tema . '|' . $nota->fecha . '|' . ($nota->creador ? $nota->creador->id : 'null');
    
            if (!isset($notasAgrupadas[$key])) {
                $notasAgrupadas[$key] = [
                    'nota' => $nota,
                    'destinatarios' => []
                ];
            }
    
            $notasAgrupadas[$key]['destinatarios'][] = $nota->destinatario;
        }
    
        return view('notas-pedido.index', compact('obra', 'notasAgrupadas', 'user'));
    }
    // app/Http/Controllers/NotaPedidoController.php
    public function create(Obra $obra)
    {
        if (!Gate::allows('create-nota', [$obra])) {
            abort(403, 'No autorizado');
        }
        // Depuración: Verificar la política manualmente
        $user = auth()->user();
        $asignadoAObra = $obra->usuarios->contains($user->id);
        $tieneRolAdecuado = false;
    
        if ($asignadoAObra) {
            $pivot = $obra->usuarios->find($user->id)->pivot;
            if ($pivot->rol_id) {
                $rolObra = \App\Models\RoleObra::find($pivot->rol_id);
                $tieneRolAdecuado = $rolObra && in_array($rolObra->nombre, ['Jefe de Obra', 'Asistente Contratista']);
            }
        }
    
        // Verificar la política manualmente
        $policy = new \App\Policies\NotaPedidoPolicy();
        $policyResult = $policy->create($user, $obra);
    
        // Verificar el método can()
        $canResult = $user->can('create', [\App\Models\Nota::class, $obra]);
    
        // Guardar información de depuración en la sesión
        session()->flash('debug_info', [
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames()->toArray(),
            'obra_id' => $obra->id,
            'asignado_a_obra' => $asignadoAObra,
            'tiene_rol_adecuado' => $tieneRolAdecuado,
            'policy_result' => $policyResult,
            'can_result' => $canResult,
            'pivot_data' => $asignadoAObra ? $obra->usuarios->find($user->id)->pivot->toArray() : null,
        ]);
    
        // Obtener el inspector principal y asistente de inspección de la obra
        $inspectorPrincipal = $obra->usuarios()->withPivot('rol_id')
            ->get()
            ->first(function($usuario) {
                $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
                return $rolObra && $rolObra->nombre == 'Inspector Principal';
            });
    
        $asistenteInspeccion = $obra->usuarios()->withPivot('rol_id')
            ->get()
            ->first(function($usuario) {
                $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
                return $rolObra && $rolObra->nombre == 'Asistente Inspección';
            });
    
        // Generar el próximo número de nota de pedido para esta obra
        $proximoNumero = \App\Models\Nota::where('obra_id', $obra->id)
                            ->where('Tipo', 'NP')
                            ->max('Nro') + 1;
    
        // El destinatario por defecto es el inspector principal
        $destinatarioDefault = $inspectorPrincipal ? $inspectorPrincipal->id : null;
    
        return view('notas-pedido.create', compact('obra', 'destinatarioDefault', 'proximoNumero'));
    }
   // app/Http/Controllers/NotaPedidoController.php
    // app/Http/Controllers/NotaPedidoController.php
    // app/Http/Controllers/NotaController.php
   // app/Http/Controllers/NotaController.php
   public function store(Request $request, Obra $obra)
   {
       \Log::info('Datos recibidos en el controlador:', $request->all());
   
       $validated = $request->validate([
           'Tema' => 'required|string|max:100',
           'texto' => 'nullable|string',
           'fecha' => 'nullable|date_format:Y-m-d',
           'Observaciones' => 'nullable|string',
           'pdf' => 'nullable|mimes:pdf|max:20480',
           'texto_pdf' => 'nullable|string',
           'resumen_ai' => 'nullable|string',
           'generate_ai_summary' => 'nullable|integer',
           'destinatarios' => 'nullable|array', // Cambiado a nullable
       ]);
   
       \Log::info('Datos validados:', $validated);
   
       try {
           // Obtener los inspectores de la obra
           $inspectorPrincipal = $obra->usuarios->first(function($usuario) {
               $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
               return $rolObra && $rolObra->nombre == 'Inspector Principal';
           });
   
           $asistenteInspeccion = $obra->usuarios->first(function($usuario) {
               $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
               return $rolObra && $rolObra->nombre == 'Asistente Inspección';
           });
   
           // Preparar array de destinatarios (ambos inspectores si existen)
           $destinatarios = [];
           if ($inspectorPrincipal) {
               $destinatarios[] = $inspectorPrincipal->id;
           }
           if ($asistenteInspeccion) {
               $destinatarios[] = $asistenteInspeccion->id;
           }
   
           // Verificar que al menos haya un destinatario
           if (empty($destinatarios)) {
               return back()->with('error', 'No hay inspectores asignados a esta obra para recibir la nota de pedido.');
           }
   
           // Datos base de la nota
           $notaData = [
               'user_id' => auth()->id(),
               'obra_id' => $obra->id,
               'Tipo' => 'NP',
               'Estado' => 'Pendiente de Firma',
               'leida' => false,
               'fecha_lectura' => null,
               'Tema' => $validated['Tema'],
               'texto' => $validated['texto'],
               'Observaciones' => $validated['Observaciones'],
               'fecha' => $validated['fecha'] ?? now()->format('Y-m-d'),
               'resumen_ai' => $validated['resumen_ai'] ?? null,
               'texto_pdf' => $validated['texto_pdf'] ?? null,
               'usar_resumen_ai' => isset($validated['generate_ai_summary']) && $validated['generate_ai_summary'] == 1,
           ];
   
           // Generar número de nota
           $notaData['Nro'] = Nota::where('obra_id', $obra->id)
                                   ->where('Tipo', 'NP')
                                   ->max('Nro') + 1;
   
           // Generar link único
           $notaData['link'] = 'np-' . $obra->id . '-' . $notaData['Nro'] . '-' . Str::random(8);
   
           // Crear notas para cada destinatario
           $notasCreadas = [];
           foreach ($destinatarios as $destinatarioId) {
               $destinatario = User::find($destinatarioId);
               $nota = Nota::create($notaData + ['destinatario_id' => $destinatarioId]);
   
               if ($request->hasFile('pdf')) {
                   $this->procesarPDF($request->file('pdf'), $nota);
               }
   
               $notasCreadas[] = $nota;
           }
   
           // Log para depuración
           \Log::info('Nota de pedido creada con éxito:', [
               'notas_creadas' => count($notasCreadas),
               'destinatarios' => $destinatarios,
               'inspector_principal' => $inspectorPrincipal ? $inspectorPrincipal->id : 'No asignado',
               'asistente_inspeccion' => $asistenteInspeccion ? $asistenteInspeccion->id : 'No asignado',
               'resumen_ai' => $validated['resumen_ai'] ? 'Sí (' . strlen($validated['resumen_ai']) . ' caracteres)' : 'No',
               'usar_resumen_ai' => isset($validated['generate_ai_summary']) && $validated['generate_ai_summary'] == 1,
               'texto_length' => strlen($validated['texto'])
           ]);
   
           return redirect()->route('obras.show', $obra->id)
                           ->with('success', 'Nota de Pedido N°' . $notaData['Nro'] . ' creada con éxito y enviada a los inspectores.');
       } catch (\Exception $e) {
           \Log::error('Error al crear nota de pedido: ' . $e->getMessage(), [
               'exception' => $e,
               'obra_id' => $obra->id,
               'user_id' => auth()->id(),
               'request_data' => $request->all()
           ]);
           return back()->with('error', 'Error al crear la nota de pedido: ' . $e->getMessage());
       }
   }
    protected function procesarPDF($file, Nota $nota)
    {
        $filename = 'nota_pedido_' . $nota->id . '.pdf';
        $path = $file->storeAs('pdfs/notas-pedido', $filename, 'public');

        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());
        $textoPDF = '';
        foreach ($pdf->getPages() as $page) {
            $textoPDF .= $page->getText() . "\n\n";
        }

        $nota->update([
            'pdf_path' => $path,
            'texto_pdf' => $textoPDF
        ]);
    }

   /* public function show(Obra $obra, Nota $nota)
    {
        //$this->authorize('view', $nota);

        return view('notas-pedido.show', compact('obra', 'nota'));
    }*/

    public function edit(Obra $obra, Nota $nota)
    {
        // Verificar permisos
        $user = auth()->user();
        if (!$user->hasRole('admin') && $user->id != $nota->user_id) {
            abort(403, 'No autorizado');
        }

        // Obtener usuarios disponibles para ser destinatarios
        $usuarios = $obra->usuarios()
                        ->where('id', '!=', $user->id)
                        ->get(['id', 'name', 'organization']);

        return view('notas-pedido.edit', compact('obra', 'nota', 'usuarios'));
    }

    public function update(Request $request, Obra $obra, Nota $nota)
    {
        // Log inicial para verificar que entramos al método
        Log::info('Iniciando actualización de nota de pedido', [
            'nota_id' => $nota->id,
            'request_all' => $request->all()
        ]);
    
        $validated = $request->validate([
            'Tema' => 'required|string|max:100',
            'texto' => 'nullable|string',
            'fecha' => 'nullable|date_format:Y-m-d',
            'Observaciones' => 'nullable|string',
            'destinatario_id' => 'required|exists:users,id',
            'Estado' => 'required|string|max:50',
            'Rta_a_NP' => 'nullable|integer',
            'Respondida_por' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'resumen_ai' => 'nullable|string',
            'pdf' => 'nullable|mimes:pdf|max:20480',
        ]);
    
        try {
            // Preparar datos para actualizar
            $data = $request->except(['_token', '_method', 'pdf', 'Nro']);
    
            // Procesar el número de nota (solo la parte numérica)
            if (isset($request->Nro)) {
                // Extraer solo los números del formato "NP-0005"
                $nroParts = explode('-', $request->Nro);
                if (count($nroParts) > 1) {
                    $data['Nro'] = (int)end($nroParts); // Convertir a entero
                }
            }
    
            // Verificar si se seleccionó usar el resumen de IA
            if ($request->has('useAISummary') && $request->input('useAISummary') == 'on') {
                $data['texto'] = $request->input('resumen_ai');
                Log::info('Usando resumen de IA', ['resumen' => $request->input('resumen_ai')]);
            }
    
            // Log de los datos que vamos a actualizar
            Log::info('Datos preparados para actualizar', ['data' => $data]);
    
            // Actualizar la nota con los datos
            $nota->update($data);
    
            // Verificar la actualización
            $notaActualizada = Nota::find($nota->id);
            Log::info('Nota después de actualizar', ['nota' => $notaActualizada->toArray()]);
    
            // Manejar el PDF si se subió uno nuevo
            if ($request->hasFile('pdf')) {
                Log::info('Procesando nuevo PDF', [
                    'pdf_name' => $request->file('pdf')->getClientOriginalName()
                ]);
    
                // Eliminar el PDF anterior si existe
                if ($nota->pdf_path && Storage::disk('public')->exists($nota->pdf_path)) {
                    Storage::disk('public')->delete($nota->pdf_path);
                    Log::info('PDF anterior eliminado', ['path' => $nota->pdf_path]);
                }
    
                // Guardar el nuevo PDF
                $file = $request->file('pdf');
                $filename = 'nota_pedido_' . $nota->id . '.pdf';
                $path = $file->storeAs('pdfs/notas-pedido', $filename, 'public');
    
                // Extraer el texto del PDF
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getRealPath());
                $textoPDF = '';
                foreach ($pdf->getPages() as $page) {
                    $textoPDF .= $page->getText() . "\n\n";
                }
    
                // Actualizar solo los campos de PDF
                $nota->update([
                    'pdf_path' => $path,
                    'texto_pdf' => $textoPDF
                ]);
    
                Log::info('PDF procesado y guardado correctamente', [
                    'path' => $path,
                    'texto_length' => strlen($textoPDF)
                ]);
            }
    
            return redirect()->route('obras.notas-pedido.index', $obra->id)
                             ->with('success', 'Nota de Pedido actualizada con éxito.');
    
        } catch (\Exception $e) {
            Log::error('Error al actualizar nota de pedido: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Error al actualizar la nota de pedido: ' . $e->getMessage());
        }
    }

   /* public function firmar(Request $request, Obra $obra, Nota $nota)
    {
        //$this->authorize('firmar', $nota);

        $request->validate([
            'firma' => 'required|string'
        ]);

        try {
            $nota->update([
                'firmado_por' => auth()->id(),
                'firma_fecha' => now(),
                'Estado' => 'Firmado'
            ]);

            // Agregar al Libro de Obra
            LibroObra::create([
                'obra_id' => $obra->id,
                'documento_type' => Nota::class,
                'documento_id' => $nota->id,
                'orden' => LibroObra::where('obra_id', $obra->id)->max('orden') + 1,
                'fecha_registro' => now()
            ]);

            return redirect()->route('obras.notas-pedido.show', [$obra->id, $nota->id])
                             ->with('success', 'Nota de Pedido firmada y agregada al Libro de Obra.');
        } catch (\Exception $e) {
            Log::error('Error al firmar nota de pedido: ' . $e->getMessage());
            return back()->with('error', 'Error al firmar la nota de pedido: ' . $e->getMessage());
        }
    }*/

   /* private function extraerTextoDelPDF($pdfPath)
    {
        try {
            if (!file_exists($pdfPath)) {
                throw new \Exception("El archivo PDF no existe en la ruta: " . $pdfPath);
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);

            $text = '';
            foreach ($pdf->getPages() as $page) {
                $pageText = $page->getText();

                // Limpiar el texto: convertir a UTF-8 y eliminar caracteres no válidos
                $pageText = mb_convert_encoding($pageText, 'UTF-8', mb_detect_encoding($pageText));
                $pageText = preg_replace('/[^\P{L}\P{N}\s\-\,\.\;\:\!\?\(\)\/\"]/u', ' ', $pageText);
                $pageText = trim(preg_replace('/\s+/', ' ', $pageText));

                $text .= $pageText . "\n\n";
            }

            return $text;

        } catch (\Exception $e) {
            Log::error('Error al extraer texto del PDF: ' . $e->getMessage() . ' - Ruta: ' . $pdfPath);
            return '';
        }
    }*/

    // Método para generar resumen con Gemini
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

    // Método para subir PDF y generar resumen
    public function subirPDF(Request $request, Obra $obra, Nota $nota)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:20480',
        ]);

        try {
            // Eliminar el PDF anterior si existe
            if ($nota->pdf_path && Storage::disk('public')->exists($nota->pdf_path)) {
                Storage::disk('public')->delete($nota->pdf_path);
            }

            // Guardar el nuevo PDF
            $file = $request->file('pdf');
            $filename = 'nota_pedido_' . $nota->id . '.pdf';
            $path = $file->storeAs('pdfs/notas-pedido', $filename, 'public');

            // Extraer el texto del PDF
            $textoPDF = $this->extraerTextoDelPDF($file->getRealPath());

            // Actualizar la nota
            $nota->update([
                'pdf_path' => $path,
                'texto_pdf' => $textoPDF
            ]);

            return response()->json([
                'success' => true,
                'texto_pdf' => $textoPDF,
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            Log::error('Error al subir el PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el PDF: ' . $e->getMessage()
            ]);
        }
    }

    // Método para generar resumen AI
    /*public function generarResumenAI(Request $request, Obra $obra, Nota $nota)
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
    }*/

    // app/Http/Controllers/NotaPedidoController.php
// Método para extraer texto del PDF (ya lo tienes)
protected function extraerTextoDelPDF($pdfPath)
{
    try {
        if (!file_exists($pdfPath)) {
            throw new \Exception("El archivo PDF no existe en la ruta: " . $pdfPath);
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);

        $text = '';
        foreach ($pdf->getPages() as $page) {
            $pageText = $page->getText();

            // Limpiar el texto: convertir a UTF-8 y eliminar caracteres no válidos
            $pageText = mb_convert_encoding($pageText, 'UTF-8', mb_detect_encoding($pageText));
            $pageText = preg_replace('/[^\P{L}\P{N}\s\-\,\.\;\:\!\?\(\)\/\"]/u', ' ', $pageText);
            $pageText = trim(preg_replace('/\s+/', ' ', $pageText));

            $text .= $pageText . "\n\n";
        }

        return $text;

    } catch (\Exception $e) {
        Log::error('Error al extraer texto del PDF: ' . $e->getMessage() . ' - Ruta: ' . $pdfPath);
        return '';
    }
}

// Método para manejar la extracción de texto del PDF desde el frontend
public function extraerTextoPDF(Request $request, Obra $obra)
{
    $request->validate([
        'pdf' => 'required|mimes:pdf|max:20480',
    ]);

    try {
        $file = $request->file('pdf');
        $textoPDF = $this->extraerTextoDelPDF($file->getRealPath());

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



// Método para manejar la generación de resumen desde el frontend
public function generarResumenAI(Request $request, Obra $obra, $notaId)
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






public function bandejaEntrada(Obra $obra)
{
    $this->authorize('view', $obra);

    $user = auth()->user();
    $notasRecibidas = Nota::where('obra_id', $obra->id)
        ->where('destinatario_id', $user->id)
        ->with(['creador', 'destinatario'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('notas.bandeja.index', compact('obra', 'notasRecibidas'));
}

/*public function show(Obra $obra, Nota $nota)
{
    $this->authorize('view', [$obra, $nota]);

    // Verificar que el usuario tenga permiso para ver esta nota
    if (auth()->user()->id != $nota->destinatario_id && auth()->user()->id != $nota->creador_id && !auth()->user()->hasRole('admin')) {
        abort(403, 'No tienes permiso para ver esta nota de pedido');
    }

    return view('notas.bandeja.show', compact('obra', 'nota'));
}*/

// Método show actualizado
// Método show actualizado para notas de pedido
public function show(Obra $obra, Nota $nota)
{
    $user = auth()->user();
    $userId = $user->id;
    $isAdmin = $user->hasRole('admin');

    // Verificar si el usuario es parte del binomio de contratistas de la obra
    $isContratista = $obra->usuarios->contains(function($usuario) use ($userId) {
        $rolObra = \App\Models\RoleObra::find($usuario->pivot->rol_id);
        return $usuario->id == $userId &&
               $rolObra &&
               in_array($rolObra->nombre, ['Jefe de Obra', 'Asistente Contratista']);
    });

    // Verificar si el usuario es el destinatario específico de esta nota
    $isDestinatario = $userId == $nota->destinatario_id;

    // Log de información para depuración
    \Log::info('Verificación de permisos para ver nota de pedido', [
        'user_id' => $userId,
        'is_admin' => $isAdmin,
        'is_destinatario' => $isDestinatario,
        'destinatario_id' => $nota->destinatario_id,
        'is_contratista' => $isContratista,
        'nota_id' => $nota->id,
    ]);

    // Verificar que el usuario tenga permiso para ver esta nota
    // Solo puede verla si es admin, destinatario específico o parte del binomio de contratistas
   /* $tienePermiso = $isAdmin || $isDestinatario || $isContratista;

    if (!$tienePermiso) {
        \Log::warning('Acceso denegado a nota de pedido', [
            'user_id' => $userId,
            'nota_id' => $nota->id,
            'es_admin' => $isAdmin,
            'es_destinatario' => $isDestinatario,
            'es_contratista' => $isContratista,
        ]);

        abort(403, 'No tienes permiso para ver esta nota de pedido');
    }*/

    // Marcar como leída si es el destinatario y no está leída
    if ($isDestinatario && !$nota->leida) {
        $nota->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);
    }

    // Cargar relaciones necesarias
    $nota->load(['creador', 'destinatario', 'ordenServicio']);

    // Obtener información sobre el rol del usuario en la obra
    $rolUsuario = null;
    if ($isContratista) {
        $pivot = $obra->usuarios->find($userId)->pivot;
        $rolUsuario = \App\Models\RoleObra::find($pivot->rol_id)->nombre;
    }

    return view('notas-pedido.show', compact('obra', 'nota', 'rolUsuario'));
}

public function firmar(Obra $obra, Nota $nota)
{
    //$this->authorize('firmar', [$obra, $nota]);

    // Verificar que el usuario sea el destinatario
    if (auth()->user()->id != $nota->destinatario_id) {
        abort(403, 'Solo el destinatario puede firmar esta nota de pedido');
    }

    // Actualizar el estado de la nota
    $nota->update([
        'Estado' => 'Firmada',
        'Respondida_por' => auth()->user()->name,
        'Rta_a_NP' => now(),
    ]);

    return redirect()->route('obras.notas-pedido.index', $obra->id)
        ->with('success', 'Nota de pedido firmada con éxito.');
}

// Método para marcar una nota como leída
public function marcarComoLeida(Obra $obra, Nota $nota)
{
    $this->authorize('view', [$obra, $nota]);

    if (auth()->user()->id == $nota->destinatario_id && !$nota->leida) {
        $nota->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);
    }

    return redirect()->route('obras.notas-pedido.show', [$obra->id, $nota->id]);
}

// En NotaPedidoController.php
public function getNotasNoLeidasCount(Obra $obra)
{
    $user = auth()->user();
    return \App\Models\Nota::where('obra_id', $obra->id)
        ->where('destinatario_id', $user->id)
        ->where('leida', false)
        ->count();
}


}

