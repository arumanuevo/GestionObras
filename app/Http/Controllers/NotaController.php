<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use App\Models\User;
use App\Mail\NotaCreada;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NotaAsignadaNotification;

class NotaController extends Controller
{
    // Método reutilizable para extraer texto del PDF
    private function extraerTextoDelPDF($pdfPath)
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


    // Método para generar resumen con Gemini (reutilizable)
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

    public function index()
    {
        // Obtener solo las notas donde el usuario es destinatario o es admin
        if (auth()->user()->hasRole('admin')) {
            $notas = Nota::with(['creador', 'destinatario'])->get();
        } else {
            $notas = Nota::with(['creador', 'destinatario'])
                    ->where('destinatario_id', auth()->id())
                    ->orWhere('user_id', auth()->id()) // También ver las notas que el usuario creó
                    ->get();
        }

        $tipos = Nota::select('Tipo')->distinct()->pluck('Tipo');
        $temas = Nota::select('Tema')->distinct()->pluck('Tema');

        return view('notas.index', compact('notas', 'tipos', 'temas'));
    }


    public function create()
    {
        $usuarios = User::where('approved', true)
                       ->where('id', '!=', auth()->id())
                       ->get(['id', 'name', 'first_name', 'last_name', 'organization']);
    
        return view('notas.create', compact('usuarios'));
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Tipo' => 'required|string|max:50',
            'Nro' => 'required|integer',
            'Tema' => 'required|string|max:100',
            'texto' => 'nullable|string',
            'fecha' => 'nullable|date_format:Y-m-d',
            'Rta_a_NP' => 'nullable|integer',
            'Respondida_por' => 'nullable|string|max:255',
            'Observaciones' => 'nullable|string',
            'Estado' => 'nullable|string|max:50',
            'link' => 'nullable|string|max:255',
            'pdf' => 'nullable|mimes:pdf|max:20480',
            'destinatario_id' => 'nullable|exists:users,id',
            'pdf_path_temp' => 'nullable|string',
            'texto_pdf_temp' => 'nullable|string',
            'resumen_ai_temp' => 'nullable|string'
        ]);
    
        try {
            $data = $request->except('pdf', 'pdf_path_temp', 'texto_pdf_temp', 'resumen_ai_temp');
            $data['user_id'] = auth()->id(); // Guardamos el ID del creador
    
            // Crear la nota primero
            $nota = Nota::create($data);
    
            // Si hay un PDF temporal, procesarlo
            if ($request->has('pdf_path_temp') && $request->pdf_path_temp) {
                $tempPath = $request->pdf_path_temp;
                $finalFilename = 'nota_' . $nota->id . '.pdf';
    
                // Verificar que el archivo temporal exista
                $tempFilePath = public_path('storage/' . $tempPath);
                if (file_exists($tempFilePath)) {
                    // Asegurarse de que la carpeta de destino exista
                    $destDir = public_path('storage/pdfs');
                    if (!file_exists($destDir)) {
                        mkdir($destDir, 0777, true);
                    }
    
                    // Mover el archivo de la ubicación temporal a la final
                    $finalPath = $destDir . '/' . $finalFilename;
                    rename($tempFilePath, $finalPath);
    
                    // Actualizar la nota con la ruta final del PDF y el texto extraído
                    $nota->update([
                        'pdf_path' => 'pdfs/' . $finalFilename,
                        'texto_pdf' => $request->texto_pdf_temp,
                        'resumen_ai' => $request->resumen_ai_temp ?? null
                    ]);
                }
            } elseif ($request->hasFile('pdf')) {
                // Si se sube un PDF directamente en el formulario
                $file = $request->file('pdf');
                $filename = 'nota_' . $nota->id . '.pdf';
    
                // Asegurarse de que la carpeta exista
                $destDir = public_path('storage/pdfs');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0777, true);
                }
    
                // Mover el archivo a la carpeta pública
                $file->move($destDir, $filename);
    
                // Extraer el texto del PDF
                $textoPDF = $this->extraerTextoDelPDF($destDir . '/' . $filename);
    
                $nota->update([
                    'pdf_path' => 'pdfs/' . $filename,
                    'texto_pdf' => $textoPDF
                ]);
            }
    
            // Enviar notificación si hay un destinatario
            if ($request->has('destinatario_id') && $request->destinatario_id) {
                $destinatario = User::find($request->destinatario_id);
    
                if ($destinatario) {
                    try {
                        // Verificar que el destinatario tenga email
                        if (empty($destinatario->email)) {
                            Log::warning("El destinatario no tiene email configurado. ID: {$destinatario->id}");
                        } else {
                            // Usar notifyNow para enviar la notificación inmediatamente
                            $destinatario->notifyNow(new NotaAsignadaNotification($nota, auth()->user()));
                            Log::info("Notificación de nota asignada enviada a {$destinatario->email} sobre la nota #{$nota->id}");
                        }
                    } catch (\Exception $e) {
                        Log::error("Error al enviar notificación de nota a {$destinatario->email}: " . $e->getMessage());
                    }
                }
            }
    
            // Devolver respuesta JSON para el AJAX
            return response()->json([
                'success' => true,
                'message' => 'Nota creada con éxito.',
                'redirect' => route('notas.index')
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error al crear nota: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la nota: ' . $e->getMessage()
            ], 500);
        }
    }
    


    
    

    public function edit(Nota $nota)
    {
        $usuarios = User::where('approved', true)
                       ->where('id', '!=', auth()->id())
                       ->get(['id', 'name', 'first_name', 'last_name', 'organization']);

        return view('notas.edit', compact('nota', 'usuarios'));
    }

    public function update(Request $request, Nota $nota)
    {
        $validated = $request->validate([
            'Tipo' => 'required|string|max:50',
            'Nro' => 'required|integer',
            'Tema' => 'required|string|max:100',
            'texto' => 'nullable|string',
            'fecha' => 'nullable|date',
            'Rta_a_NP' => 'nullable|integer',
            'Respondida_por' => 'nullable|string|max:255',
            'Observaciones' => 'nullable|string',
            'Estado' => 'nullable|string|max:50',
            'link' => 'nullable|string|max:255',
            'pdf' => 'nullable|mimes:pdf|max:20480',
            'destinatario_id' => 'nullable|exists:users,id'
        ]);

        $data = $request->except('pdf');

        try {
            if ($request->hasFile('pdf')) {
                // Eliminar el PDF anterior si existe
                if ($nota->pdf_path) {
                    Storage::disk('public')->delete($nota->pdf_path);
                }

                // Guardar el nuevo PDF
                $file = $request->file('pdf');
                $filename = 'nota_' . $nota->id . '.pdf';
                $path = $file->storeAs('pdfs', $filename, 'public');

                // Extraer el texto del PDF
                $textoPDF = $this->extraerTextoDelPDF($file->getRealPath());

                $data['pdf_path'] = $path;
                $data['texto_pdf'] = $textoPDF;
            }

            $nota->update($data);

            return redirect()->route('notas.index')->with('success', 'Nota actualizada con éxito.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar nota: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la nota: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo debe ser un CSV válido (máx. 2MB).',
            ], 422);
        }

        if (!$request->hasFile('csv_file')) {
            return response()->json([
                'success' => false,
                'message' => 'No se subió ningún archivo.',
            ], 400);
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $data = [];

            if (($handle = fopen($path, "r")) !== FALSE) {
                // Saltar la primera línea (encabezados)
                fgetcsv($handle, 0, ';');

                while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            }

            $errors = [];
            $importedCount = 0;

            foreach ($data as $rowIndex => $row) {
                $row = array_pad($row, 11, null);

                $notaData = [
                    'Tipo' => $row[1] ?? 'OS',
                    'Nro' => is_numeric($row[2]) ? $row[2] : 0,
                    'Tema' => $row[3] ?? 'Sin tema',
                    'texto' => $row[4] ?? null,
                    'fecha' => !empty($row[5]) ? \Carbon\Carbon::createFromFormat('d/m/Y', trim($row[5]))->format('Y-m-d') : null,
                    'Rta_a_NP' => is_numeric($row[6]) ? $row[6] : null,
                    'Respondida_por' => $row[7] ?? null,
                    'Observaciones' => $row[8] ?? null,
                    'Estado' => $row[9] ?? 'PENDIENTE',
                    'link' => $row[10] ?? null,
                ];

                try {
                    Nota::create($notaData);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error al importar fila " . ($rowIndex + 2) . ": " . implode(';', $row) . ". Error: " . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se importaron ' . $importedCount . ' registros con errores.',
                    'errors' => $errors,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Se importaron ' . $importedCount . ' registros correctamente.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al importar CSV: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function export(Request $request)
{
    // Validar permisos
    if (!auth()->user()->hasAnyRole(['admin', 'editor', 'consulta'])) {
        abort(403, 'No tienes permiso para exportar datos');
    }

    // Obtener notas con los mismos filtros que se usan en la vista
    $query = Nota::query();

    // Aplicar filtros si existen en la request
    if ($request->has('tipo') && $request->tipo) {
        $query->where('Tipo', $request->tipo);
    }

    if ($request->has('tema') && $request->tema) {
        $query->where('Tema', $request->tema);
    }

    $notas = $query->get();

    // Crear un archivo temporal
    $filename = 'notas_export_' . date('Y-m-d_His') . '.csv';
    $tempPath = storage_path('app/temp/' . $filename);

    // Asegurarse de que el directorio temp exista
    if (!file_exists(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0755, true);
    }

    // Abrir el archivo para escritura
    $file = fopen($tempPath, 'w');

    // Escribir encabezados
    fputcsv($file, [
        'ID', 'Tipo', 'Número', 'Tema', 'Texto', 'Fecha',
        'Respuesta a NP', 'Respondida por', 'Observaciones',
        'Estado', 'Enlace', 'Tiene PDF'
    ], ';');

    // Escribir datos
    foreach ($notas as $nota) {
        fputcsv($file, [
            $nota->id,
            $nota->Tipo,
            $nota->Nro,
            $nota->Tema,
            $nota->texto,
            $nota->fecha ? $nota->fecha->format('d/m/Y') : '',
            $nota->Rta_a_NP,
            $nota->Respondida_por,
            $nota->Observaciones,
            $nota->Estado,
            $nota->link,
            $nota->pdf_path ? 'Sí' : 'No'
        ], ';');
    }

    fclose($file);

    // Descargar el archivo
    return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
}

    


public function subirPDFTemporal(Request $request)
{
    $request->validate([
        'pdf' => 'required|mimes:pdf|max:20480',
    ]);

    try {
        $file = $request->file('pdf');
        $filename = 'temp_' . Str::random(40) . '.pdf';

        // Asegurarse de que la carpeta exista
        $tempDir = public_path('storage/pdfs/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Mover el archivo a la carpeta pública temporal
        $filePath = $file->move($tempDir, $filename);

        // Obtener la ruta completa del archivo temporal
        $fullPath = $filePath->getRealPath();

        // Extraer el texto del PDF
        $textoPDF = $this->extraerTextoDelPDF($fullPath);

        return response()->json([
            'success' => true,
            'texto_pdf' => $textoPDF,
            'path' => 'pdfs/temp/' . $filename
        ]);

    } catch (\Exception $e) {
        Log::error('Error al subir PDF temporal: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar el PDF: ' . $e->getMessage()
        ], 500);
    }
}


    public function generarResumenAITemporal(Request $request, $id = null)
{
    $textoPDF = $request->input('texto_pdf');
    $cantidadPalabras = $request->input('cantidad_palabras', 100);

    if (empty($textoPDF)) {
        return response()->json([
            'success' => false,
            'message' => 'No hay texto del PDF para generar el resumen.'
        ]);
    }

    $result = $this->generarResumenConGemini($textoPDF, $cantidadPalabras);

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
}

public function destroy(Nota $nota)
{
    try {
        // Eliminar el PDF asociado si existe
        if ($nota->pdf_path) {
            $pdfPath = public_path('storage/' . $nota->pdf_path);
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }

        $nota->delete();
        return redirect()->route('notas.index')->with('success', 'Nota eliminada con éxito.');

    } catch (\Exception $e) {
        Log::error('Error al eliminar nota: ' . $e->getMessage());
        return back()->with('error', 'Error al eliminar la nota: ' . $e->getMessage());
    }
}
   /* public function generarResumenAITemporal(Request $request)
    {
        $textoPDF = $request->input('texto_pdf');
        $cantidadPalabras = $request->input('cantidad_palabras', 100);

        if (empty($textoPDF)) {
            return response()->json([
                'success' => false,
                'message' => 'No hay texto del PDF para generar el resumen.'
            ]);
        }

        $result = $this->generarResumenConGemini($textoPDF, $cantidadPalabras);

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
    }*/

    public function subirPDF(Request $request, $id)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:20480',
        ]);

        try {
            $nota = Nota::findOrFail($id);

            // Eliminar el PDF anterior si existe
            if ($nota->pdf_path && Storage::disk('public')->exists($nota->pdf_path)) {
                Storage::disk('public')->delete($nota->pdf_path);
            }

            // Guardar el nuevo PDF
            $file = $request->file('pdf');
            $filename = 'nota_' . $nota->id . '.pdf';
            $path = $file->storeAs('pdfs', $filename, 'public');

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

    public function listarModelosGemini()
    {
        $apiKey = env('GOOGLE_AI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->get($url);

            $result = $response->json();

            if ($response->successful() && isset($result['models'])) {
                Log::info('Modelos disponibles en Gemini:', $result['models']);
                return $result['models'];
            } else {
                Log::error('Error al listar modelos de Gemini: ' . $response->body());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Excepción al listar modelos de Gemini: ' . $e->getMessage());
            return [];
        }
    }

    public function show(Nota $nota)
    {
        // Verificar permisos
        // Permitir acceso si:
        // 1. Es administrador
        // 2. Es el creador de la nota
        // 3. Es el destinatario de la nota
        if (!auth()->user()->hasAnyRole(['admin', 'editor', 'consulta']) &&
            auth()->user()->id !== $nota->user_id &&
            auth()->user()->id !== $nota->destinatario_id) {
            abort(403, 'No tienes permiso para ver esta nota');
        }

        return view('notas.show', compact('nota'));
    }


}
