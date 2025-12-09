<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProcesoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ruta principal - redirige a clientes
Route::get('/', function () {
    return redirect()->route('clientes.index');
});


// Rutas para clientes (CRUD completo)
Route::resource('clientes', ClienteController::class);

// Rutas para el proceso SAT
Route::prefix('proceso')->controller(ProcesoController::class)->group(function () {
    Route::get('/', 'index')->name('proceso.index');
    Route::post('/procesar', 'procesar')->name('proceso.procesar');
    Route::get('/analisis', 'verAnalisis')->name('proceso.analisis');
    Route::get('/prueba-conexion', 'pruebaConexion')->name('proceso.prueba-conexion');
    Route::get('/ver-resultados', 'verResultados')->name('proceso.resultados');
});

// Rutas de prueba para el SAT (para desarrollo/debug)
Route::prefix('sat-test')->group(function () {
    // Página principal de pruebas SAT
    Route::get('/', function () {
        return view('sat-test.index');
    })->name('sat-test.index');
    
    // Probar conexión básica
    Route::get('/probar-conexion', function () {
        $satService = new \App\Services\SatService();
        $resultado = $satService->probarConexionBasica();
        
        return response()->json($resultado);
    })->name('sat-test.conexion');
    
    // Analizar formulario SAT
    Route::get('/analizar-formulario', function () {
        $satService = new \App\Services\SatService();
        $resultado = $satService->analizarFormularioSAT();
        
        return response()->json($resultado);
    })->name('sat-test.analizar-json');
    
    // Ver análisis en HTML
    Route::get('/analizar-html', function () {
        $satService = new \App\Services\SatService();
        $analisis = $satService->analizarFormularioSAT();
        
        return view('proceso.analisis', [
            'analisis' => $analisis,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    })->name('sat-test.analizar-html');
    
    // Probar con cliente específico
    Route::get('/probar-cliente/{id}', function ($id) {
        $cliente = \App\Models\Cliente::findOrFail($id);
        $satService = new \App\Services\SatService();
        
        return response()->json([
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'rfc' => $cliente->rfc,
                'tiene_certificado' => !empty($cliente->certificado_path),
                'tiene_llave' => !empty($cliente->llave_path)
            ],
            'probar_archivos' => $satService->probarSubidaArchivos($cliente),
            'probar_conexion' => $satService->probarConexionBasica()
        ]);
    })->name('sat-test.probar-cliente');
    
    // Probar con cURL
    Route::get('/probar-curl/{id}', function ($id) {
        $cliente = \App\Models\Cliente::findOrFail($id);
        $satService = new \App\Services\SatService();
        
        try {
            $resultado = $satService->probarConCURL($cliente);
            
            return response()->json([
                'success' => true,
                'resultado' => $resultado,
                'cliente' => $cliente->rfc
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('sat-test.probar-curl');
    
    // Ejecutar proceso completo
    Route::get('/ejecutar/{id}', function ($id) {
        $cliente = \App\Models\Cliente::findOrFail($id);
        $satService = new \App\Services\SatService();
        
        try {
            $resultado = $satService->obtenerOpinionCumplimiento($cliente);
            
            return response()->json([
                'success' => true,
                'resultado' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    })->name('sat-test.ejecutar');
    
    // Ver archivos de debug
    Route::get('/debug', function () {
        $debugFiles = \Illuminate\Support\Facades\Storage::files('sat_debug');
        $analysisFiles = \Illuminate\Support\Facades\Storage::files('sat_analysis');
        $errorFiles = \Illuminate\Support\Facades\Storage::files('sat_errors');
        
        return view('sat-test.debug', [
            'debugFiles' => $debugFiles,
            'analysisFiles' => $analysisFiles,
            'errorFiles' => $errorFiles,
            'storagePath' => storage_path('app')
        ]);
    })->name('sat-test.debug');
    
    // Ver contenido de archivo de debug
    Route::get('/debug/file/{filename}', function ($filename) {
        $path = 'sat_debug/' . $filename;
        
        if (!\Illuminate\Support\Facades\Storage::exists($path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        $content = \Illuminate\Support\Facades\Storage::get($path);
        $isHtml = strpos($content, '<html') !== false || strpos($content, '<!DOCTYPE') !== false;
        
        return response($content)
            ->header('Content-Type', $isHtml ? 'text/html' : 'text/plain')
            ->header('Content-Disposition', 'inline');
    })->name('sat-test.debug-file');
});

// Ruta para ver directamente la página del SAT (solo para referencia)
Route::get('/ver-sat', function () {
    return redirect('https://ptsc32d.clouda.sat.gob.mx/?/reporteOpinion32DContribuyente');
})->name('ver-sat');

// Probar autenticación SAT
Route::get('/sat-test/autenticacion', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->manejarAutenticacionSAT();
    
    return response()->json($resultado);
})->name('sat-test.autenticacion');

// Ver página de login HTML
Route::get('/sat-test/ver-login', function () {
    $satService = new \App\Services\SatService();
    $loginPage = $satService->obtenerPaginaLogin();
    
    return response($loginPage['html'])
        ->header('Content-Type', 'text/html');
})->name('sat-test.ver-login');

// Buscar formulario directo
Route::get('/sat-test/buscar-formulario', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->buscarFormularioDirecto();
    
    return response()->json($resultado);
})->name('sat-test.buscar-formulario');

// Probar formulario real del SAT
Route::get('/sat-test/formulario-real', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->probarFormularioReal();
    
    // Si es éxito, mostrar análisis detallado
    if ($resultado['success']) {
        return view('sat-test.formulario-real', [
            'resultado' => $resultado,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }
    
    return response()->json($resultado);
})->name('sat-test.formulario-real');

// Ejecutar proceso completo con formulario real
Route::get('/sat-test/ejecutar-real/{id}', function ($id) {
    $cliente = \App\Models\Cliente::findOrFail($id);
    $satService = new \App\Services\SatService();
    
    try {
        $resultado = $satService->obtenerOpinionCumplimiento($cliente);
        
        return response()->json([
            'success' => true,
            'resultado' => $resultado
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('sat-test.ejecutar-real');

// Probar URL específica
Route::get('/sat-test/probar-url', function () {
    $satService = new \App\Services\SatService();
    
    // Probar con la URL completa
    $resultado = $satService->probarUrlEspecifica();
    
    return response()->json($resultado);
})->name('sat-test.probar-url');

// Probar con URL personalizada (para debug)
Route::get('/sat-test/probar-url-personalizada', function (Request $request) {
    $url = $request->get('url', 'https://login.mat.sat.gob.mx/nidp//app/login?target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge%3DkeBew7q19naJxaTIXK27KNtB886H1Z0u8eFGqIhuzyQ%26code_challenge_method%3DS256%26response_mode%3Dform_post%26nonce%3D639007739736089596.MGFhODJjMjItYzAyNS00OWQyLWJiZGQtNThlYjY5YWQyOTczZDNjODM3ZDYtZjRhOS00OTlkLThmNDktNzA2MmUxZTNiYmI2%26state%3DCfDJ8OSJ4mcSBN1Cm3RmDyDIwh5laxT7GJezGjNNWp5rLUOmge3BVJ2lSo0iR6gplpJf8-U-owHI9VY6xrUgsTm_yTmf8XCsLZXuSIcVW4BS6o0Rrf6CtPvioLi94nxd0rLKXaLyQsdCr8OzqWjH-h62nT7XGVII_b0kdXbu75vXsp_BH2ft9SY4_iA-1uhPsNN28OdD5rS7i7yg4qdsSFtx-oxADQXNDCuz2ZkvErTexKptOTppvg4pfQWzOe6YjKacZntsMoVc6TGgW1b5pIViKzjlpUNd9Sebw2LTAXRhmlmcVj5RTpyUq467ogC03gIkmV9pvo-N-kVXWxb0_L-1olyrlEUF36NL60PgRmgsMp4f3MxkZjwy7scjnLVWTR0JLsGYk6x1HjlnzCRoMB4-722vtYC5PsNbPeeMGuHiG9wUAUuQwRWQ1VeHrstx-mgfDA%26x-client-SKU%3DID_NETSTANDARD2_0%26x-client-ver%3D6.10.0.0');
    
    $satService = new \App\Services\SatService();
    $resultado = $satService->probarUrlEspecifica($url);
    
    return response()->json($resultado);
})->name('sat-test.probar-url-personalizada');


// Probar flujo completo del SAT
Route::get('/sat-test/flujo-completo', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->obtenerFormularioSATCompleto();
    
    return response()->json($resultado);
})->name('sat-test.flujo-completo');

// Probar solo paso 1 (URL1)
Route::get('/sat-test/paso-1', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->obtenerPaginaInicial();
    
    return response()->json($resultado);
})->name('sat-test.paso-1');

// Ver HTML del paso 1
Route::get('/sat-test/ver-paso-1', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->obtenerPaginaInicial();
    
    if ($resultado['success']) {
        return response($resultado['html'] ?? 'No HTML')
            ->header('Content-Type', 'text/html');
    }
    
    return response()->json($resultado);
});

// Ver HTML del paso 2
Route::get('/sat-test/ver-paso-2', function () {
    $satService = new \App\Services\SatService();
    
    // Primero obtener cookies del paso 1
    $paso1 = $satService->obtenerPaginaInicial();
    
    if (!$paso1['success']) {
        return response()->json(['error' => 'Paso 1 falló'], 500);
    }
    
    // Luego obtener paso 2 con cookies
    $paso2 = $satService->obtenerFormularioReal($paso1['cookies']);
    
    if ($paso2['success']) {
        return response($paso2['html'])
            ->header('Content-Type', 'text/html');
    }
    
    return response()->json($paso2);
});

// Página principal de pruebas SAT
Route::get('/sat-test', function () {
    return view('sat-test.flujo-completo');
})->name('sat-test.index');

// Probar con Puppeteer
Route::get('/sat-test/puppeteer', function () {
    $puppeteerService = new \App\Services\SatPuppeteerService();
    $resultado = $puppeteerService->probarFormularioPuppeteer();
    
    return response()->json($resultado);
})->name('sat-test.puppeteer');

// Ver HTML obtenido por Puppeteer
Route::get('/sat-test/ver-puppeteer-html', function () {
    if (!file_exists('sat_formulario_puppeteer.html')) {
        return response('Archivo no encontrado. Ejecuta /sat-test/puppeteer primero.', 404);
    }
    
    $html = file_get_contents('sat_formulario_puppeteer.html');
    
    return response($html)
        ->header('Content-Type', 'text/html');
})->name('sat-test.ver-puppeteer-html');

// Ver screenshot de Puppeteer
Route::get('/sat-test/ver-puppeteer-screenshot', function () {
    if (!file_exists('sat_screenshot.png')) {
        return response('Screenshot no encontrado', 404);
    }
    
    return response(file_get_contents('sat_screenshot.png'))
        ->header('Content-Type', 'image/png');
});

// Analizar JavaScript del SAT
Route::get('/sat-test/analizar-js', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->analizarJavaScriptSAT();
    
    return response()->json($resultado);
})->name('sat-test.analizar-js');

// Probar con Puppeteer (corregido)
Route::get('/sat-test/puppeteer', function () {
    $puppeteerService = new \App\Services\SatPuppeteerService();
    $resultado = $puppeteerService->probarFormularioPuppeteer();
    
    return response()->json($resultado);
})->name('sat-test.puppeteer');

// Ver HTML obtenido por Puppeteer
Route::get('/sat-test/ver-puppeteer-html', function () {
    $path = base_path('sat_formulario_puppeteer.html');
    
    if (!file_exists($path)) {
        return response('Archivo no encontrado. Ejecuta /sat-test/puppeteer primero.', 404);
    }
    
    $html = file_get_contents($path);
    
    return response($html)
        ->header('Content-Type', 'text/html');
})->name('sat-test.ver-puppeteer-html');

// Ver screenshot de Puppeteer
Route::get('/sat-test/ver-puppeteer-screenshot', function () {
    $path = base_path('sat_screenshot.png');
    
    if (!file_exists($path)) {
        return response('Screenshot no encontrado', 404);
    }
    
    return response(file_get_contents($path))
        ->header('Content-Type', 'image/png');
})->name('sat-test.ver-puppeteer-screenshot');

// Ejecutar Puppeteer con cliente específico
Route::get('/sat-test/puppeteer-cliente/{id}', function ($id) {
    $cliente = \App\Models\Cliente::findOrFail($id);
    $puppeteerService = new \App\Services\SatPuppeteerService();
    
    $resultado = $puppeteerService->ejecutarProcesoCompleto($cliente);
    
    return response()->json($resultado);
})->name('sat-test.puppeteer-cliente');

Route::get('/sat-test/simular-fingerprinting', function () {
    $satService = new \App\Services\SatService();
    $resultado = $satService->simularFingerprinting();
    
    return response()->json($resultado);
});

// Ruta para depurar Puppeteer
Route::get('/sat-test/puppeteer-debug', function () {
    $puppeteerService = new \App\Services\SatPuppeteerService();
    $resultado = $puppeteerService->probarPuppeteer();
    
    return response()->json($resultado);
})->name('sat-test.puppeteer-debug');

// Ver archivos generados por Puppeteer
Route::get('/sat-test/puppeteer-files', function () {
    $puppeteerService = new \App\Services\SatPuppeteerService();
    $resultado = $puppeteerService->verArchivosGenerados();
    
    return response()->json($resultado);
})->name('sat-test.puppeteer-files');

// Ver HTML generado
Route::get('/sat-test/ver-html-test', function () {
    $path = base_path('sat_test.html');
    
    if (!file_exists($path)) {
        return response('Archivo no encontrado. Ejecuta /sat-test/puppeteer-debug primero.', 404);
    }
    
    $html = file_get_contents($path);
    
    return response($html)
        ->header('Content-Type', 'text/html');
})->name('sat-test.ver-html-test');

// Ver screenshot
Route::get('/sat-test/ver-screenshot-test', function () {
    $path = base_path('sat_test.png');
    
    if (!file_exists($path)) {
        return response('Screenshot no encontrado', 404);
    }
    
    return response(file_get_contents($path))
        ->header('Content-Type', 'image/png');
});

// Probar envío de formulario real
Route::get('/sat-test/enviar-formulario/{id}', function ($id) {
    $cliente = \App\Models\Cliente::findOrFail($id);
    $satService = new \App\Services\SatService();
    
    $resultado = $satService->enviarFormularioSATReal($cliente);
    
    return response()->json($resultado);
})->name('sat-test.enviar-formulario');

// Probar extracción de token
Route::get('/sat-test/extraer-token', function () {
    $satService = new \App\Services\SatService();
    
    $pagina = $satService->obtenerPaginaFormulario();
    
    if ($pagina['success']) {
        $tokenuuid = $satService->extraerTokenUuid($pagina['html']);
        
        return response()->json([
            'success' => true,
            'tokenuuid' => $tokenuuid,
            'tokenuuid_length' => strlen($tokenuuid ?? ''),
            'html_length' => strlen($pagina['html'])
        ]);
    }
    
    return response()->json($pagina);
});

// Verificar archivos de cliente
Route::get('/sat-test/verificar-archivos/{id}', function ($id) {
    $cliente = \App\Models\Cliente::findOrFail($id);
    $satService = new \App\Services\SatService();
    
    $verificacion = $satService->verificarArchivosCliente($cliente);
    
    return response()->json([
        'cliente' => $cliente->rfc,
        'tiene_certificado' => !empty($cliente->certificado_path),
        'tiene_llave' => !empty($cliente->llave_path),
        'archivos_existen' => $verificacion,
        'certificado_path' => $cliente->certificado_path,
        'llave_path' => $cliente->llave_path
    ]);
});