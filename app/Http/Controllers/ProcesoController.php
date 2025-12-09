<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Services\SatService;

class ProcesoController extends Controller
{
    protected $satService;
    
    public function __construct(SatService $satService)
    {
        $this->satService = $satService;
    }
    
    public function index()
    {
        $clientes = Cliente::all();
        return view('proceso.index', compact('clientes'));
    }
    
    public function procesar(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id'
    ]);
    
    $cliente = Cliente::findOrFail($request->cliente_id);
    
    try {
        Log::info("Iniciando proceso SAT para cliente: {$cliente->rfc}");
        
        // Usar el nuevo método mejorado
        $resultado = $this->satService->obtenerOpinionCumplimiento($cliente);
        
        if ($resultado['success']) {
            // Guardar resultado en la base de datos
            \App\Models\ResultadoSat::create([
                'cliente_id' => $cliente->id,
                'periodo' => date('Y-m'),
                'resultado' => $resultado['resultado'] ?? 'POSITIVO',
                'detalles' => json_encode($resultado),
                'pdf_path' => $resultado['pdf_path'] ?? null
            ]);
            
            return redirect()->route('proceso.index')
                ->with('success', '✅ ' . ($resultado['mensaje'] ?? 'Proceso completado exitosamente'))
                ->with('resultado_detalles', $resultado);
        } else {
            return redirect()->route('proceso.index')
                ->with('error', '❌ Error: ' . ($resultado['error'] ?? 'Desconocido'))
                ->with('detalles_error', $resultado);
        }
        
    } catch (\Exception $e) {
        Log::error("Error en proceso SAT: " . $e->getMessage());
        
        return redirect()->route('proceso.index')
            ->with('error', 'Error en el proceso: ' . $e->getMessage());
    }
}
    
    /**
     * Probar conexión con SAT (sin procesar)
     */
    public function probarConexion(Request $request)
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get('https://ptsc32d.clouda.sat.gob.mx/?/reporteOpinion32DContribuyente');
                
            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'time' => now()->format('H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

        /**
         * Mostrar página de resultados
         */
        public function verResultados()
        {
            $resultados = \App\Models\ResultadoSat::with('cliente')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('proceso.resultados', compact('resultados'));
        }

        /**
         * Probar conexión
         */
        public function pruebaConexion()
        {
            $resultado = $this->satService->probarConexionBasica();
            
            return response()->json($resultado);
        }
}