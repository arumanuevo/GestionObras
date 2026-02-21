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

class NotaController extends Controller
{
    // Método reutilizable para extraer texto del PDF
    private function extraerTextoDelPDF($pdfPath)
    {
        try {
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
            Log::error('Error al extraer texto del PDF: ' . $e->getMessage());
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
        if (!auth()->user()->hasAnyRole(['admin', 'editor', 'consulta'])) {
            abort(403, 'No tienes permiso para acceder a esta página');
        }
        $notas = Nota::all();
        $tipos = Nota::select('Tipo')->distinct()->pluck('Tipo');
        $temas = Nota::select('Tema')->distinct()->pluck('Tema');
        return view('notas.index', compact('notas', 'tipos', 'temas'));
    }

    public function create()
    {
        return view('notas.create');
    }

    public function store(Request $request)
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
        ]);

        try {
            $data = $request->except('pdf_path_temp', 'texto_pdf_temp', 'resumen_ai_temp');

            // Crear la nota primero
            $nota = Nota::create($data);

            // Si hay un PDF temporal, procesarlo
            if ($request->has('pdf_path_temp')) {
                $tempPath = $request->pdf_path_temp;
                $finalFilename = 'nota_' . $nota->id . '.pdf';

                // Mover el archivo de la ubicación temporal a la final
                Storage::disk('public')->move($tempPath, 'pdfs/' . $finalFilename);

                // Actualizar la nota con la ruta final del PDF y el texto extraído
                $nota->update([
                    'pdf_path' => 'pdfs/' . $finalFilename,
                    'texto_pdf' => $request->texto_pdf_temp,
                    'resumen_ai' => $request->resumen_ai_temp ?? null
                ]);
            } elseif ($request->hasFile('pdf')) {
                // Si se sube un PDF directamente en el formulario (sin usar el temporal)
                $file = $request->file('pdf');
                $filename = 'nota_' . $nota->id . '.pdf';
                $path = $file->storeAs('pdfs', $filename, 'public');

                // Extraer el texto del PDF
                $textoPDF = $this->extraerTextoDelPDF($file->getRealPath());

                $nota->update([
                    'pdf_path' => $path,
                    'texto_pdf' => $textoPDF
                ]);
            }

            return redirect()->route('notas.index')->with('success', 'Nota creada con éxito.');

        } catch (\Exception $e) {
            Log::error('Error al crear nota: ' . $e->getMessage());
            return back()->with('error', 'Error al crear la nota: ' . $e->getMessage());
        }
    }

    public function edit(Nota $nota)
    {
        return view('notas.edit', compact('nota'));
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
        ]);

        $data = $request->except('pdf');

        try {
            if ($request->hasFile('pdf')) {
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

    public function destroy(Nota $nota)
    {
        try {
            // Eliminar el PDF asociado si existe
            if ($nota->pdf_path && Storage::disk('public')->exists($nota->pdf_path)) {
                Storage::disk('public')->delete($nota->pdf_path);
            }

            $nota->delete();
            return redirect()->route('notas.index')->with('success', 'Nota eliminada con éxito.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar nota: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la nota: ' . $e->getMessage());
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
            $path = $file->storeAs('pdfs/temp', $filename, 'public');

            // Extraer el texto del PDF
            $textoPDF = $this->extraerTextoDelPDF($file->getRealPath());

            return response()->json([
                'success' => true,
                'texto_pdf' => $textoPDF,
                'path' => $path
            ]);

        } catch (\Exception $e) {
            Log::error('Error al subir PDF temporal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el PDF: ' . $e->getMessage()
            ]);
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
}
