<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\Nota;
use App\Models\OrdenServicio;
use App\Models\LibroObra as LibroObraModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LibroObraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el Libro de Obra
     */
    public function show(Obra $obra)
    {
        // Verificar si el usuario tiene acceso a esta obra
        $this->authorize('viewLibroObra', $obra);

        // Obtener notas de pedido y órdenes de servicio para esta obra
        $notas = Nota::where('obra_id', $obra->id)
            ->with(['creador', 'destinatario'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ordenesServicio = OrdenServicio::where('obra_id', $obra->id)
            ->with(['creador', 'destinatario'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Combinar y ordenar por fecha
        $documentos = $notas->concat($ordenesServicio)
            ->sortByDesc('created_at');

        return view('libro-obra.show', compact('obra', 'documentos'));
    }

    /**
     * Ver un documento específico del Libro de Obra
     */
    public function showDocumento(Obra $obra, $documentoType, $documentoId)
    {
        // Verificar si el usuario tiene acceso a esta obra
        $this->authorize('viewLibroObra', $obra);

        try {
            if ($documentoType === 'nota') {
                $documento = Nota::where('obra_id', $obra->id)
                    ->where('id', $documentoId)
                    ->with(['creador', 'destinatario'])
                    ->firstOrFail();
            } elseif ($documentoType === 'orden-servicio') {
                $documento = OrdenServicio::where('obra_id', $obra->id)
                    ->where('id', $documentoId)
                    ->with(['creador', 'destinatario'])
                    ->firstOrFail();
            } else {
                abort(404, 'Tipo de documento no válido');
            }

            return view('libro-obra.documento', compact('obra', 'documento'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Documento no encontrado en LibroObraController@showDocumento: ' . $e->getMessage());
            abort(404, 'El documento solicitado no existe o no pertenece a esta obra');
        } catch (\Exception $e) {
            Log::error('Error al mostrar documento en LibroObraController@showDocumento: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el documento: ' . $e->getMessage());
        }
    }

    /**
     * Obtener el registro completo del Libro de Obra para exportación
     */
    public function export(Obra $obra)
    {
        $this->authorize('viewLibroObra', $obra);

        try {
            // Obtener todos los documentos del libro de obra
            $libroObra = LibroObraModel::where('obra_id', $obra->id)
                ->with(['documento.creador', 'documento.destinatario'])
                ->orderBy('orden')
                ->get();

            // Formatear los datos para exportación
            $data = $libroObra->map(function ($item) {
                $documento = $item->documento;

                return [
                    'tipo' => $documento instanceof Nota ? 'Nota de Pedido' : 'Orden de Servicio',
                    'numero' => $documento instanceof Nota ?
                        'NP-' . str_pad($documento->Nro, 4, '0', STR_PAD_LEFT) :
                        'OS-' . str_pad($documento->Nro, 4, '0', STR_PAD_LEFT),
                    'tema' => $documento->Tema,
                    'fecha' => $documento->fecha->format('d/m/Y H:i'),
                    'creador' => $documento->creador->name ?? 'Desconocido',
                    'destinatario' => $documento->destinatario->name ?? 'Desconocido',
                    'estado' => $documento->Estado,
                    'fecha_registro' => $item->fecha_registro->format('d/m/Y H:i')
                ];
            });

            // Aquí podrías implementar la exportación a PDF, Excel, etc.
            // Por ahora retornamos los datos en formato JSON
            return response()->json([
                'success' => true,
                'data' => $data,
                'obra' => $obra->nombre
            ]);

        } catch (\Exception $e) {
            Log::error('Error al exportar Libro de Obra: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar el Libro de Obra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del Libro de Obra
     */
    public function statistics(Obra $obra)
    {
        $this->authorize('viewLibroObra', $obra);

        try {
            // Contar notas de pedido
            $notasCount = Nota::where('obra_id', $obra->id)->count();

            // Contar órdenes de servicio
            $ordenesCount = OrdenServicio::where('obra_id', $obra->id)->count();

            // Obtener notas por estado
            $notasPorEstado = Nota::where('obra_id', $obra->id)
                ->select('Estado', \DB::raw('count(*) as total'))
                ->groupBy('Estado')
                ->pluck('total', 'Estado');

            // Obtener órdenes por estado
            $ordenesPorEstado = OrdenServicio::where('obra_id', $obra->id)
                ->select('Estado', \DB::raw('count(*) as total'))
                ->groupBy('Estado')
                ->pluck('total', 'Estado');

            return response()->json([
                'success' => true,
                'data' => [
                    'notas_count' => $notasCount,
                    'ordenes_count' => $ordenesCount,
                    'notas_por_estado' => $notasPorEstado,
                    'ordenes_por_estado' => $ordenesPorEstado
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas del Libro de Obra: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar en el Libro de Obra
     */
    public function search(Obra $obra, Request $request)
    {
        $this->authorize('viewLibroObra', $obra);

        $request->validate([
            'search' => 'required|string|min:3'
        ]);

        try {
            $searchTerm = $request->search;

            // Buscar en notas de pedido
            $notas = Nota::where('obra_id', $obra->id)
                ->where(function($query) use ($searchTerm) {
                    $query->where('Tema', 'like', "%{$searchTerm}%")
                          ->orWhere('texto', 'like', "%{$searchTerm}%")
                          ->orWhere('Observaciones', 'like', "%{$searchTerm}%")
                          ->orWhere('resumen_ai', 'like', "%{$searchTerm}%");
                })
                ->with(['creador', 'destinatario'])
                ->get();

            // Buscar en órdenes de servicio
            $ordenesServicio = OrdenServicio::where('obra_id', $obra->id)
                ->where(function($query) use ($searchTerm) {
                    $query->where('Tema', 'like', "%{$searchTerm}%")
                          ->orWhere('texto', 'like', "%{$searchTerm}%")
                          ->orWhere('Observaciones', 'like', "%{$searchTerm}%")
                          ->orWhere('resumen_ai', 'like', "%{$searchTerm}%");
                })
                ->with(['creador', 'destinatario'])
                ->get();

            // Combinar resultados
            $resultados = $notas->concat($ordenesServicio)->sortByDesc('created_at');

            return view('libro-obra.search-results', compact('obra', 'resultados', 'searchTerm'));

        } catch (\Exception $e) {
            Log::error('Error al buscar en Libro de Obra: ' . $e->getMessage());
            return back()->with('error', 'Error al realizar la búsqueda: ' . $e->getMessage());
        }
    }
}