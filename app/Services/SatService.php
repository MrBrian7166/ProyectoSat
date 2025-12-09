<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Exception;

class SatService
{
    // URLs del flujo SAT (actualizadas con las que descubriste)
    private $urlPaso1 = 'https://login.mat.sat.gob.mx/nidp//app/login?target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge%3DkeBew7q19naJxaTIXK27KNtB886H1Z0u8eFGqIhuzyQ%26code_challenge_method%3DS256%26response_mode%3Dform_post%26nonce%3D639007739736089596.MGFhODJjMjItYzAyNS00OWQyLWJiZGQtNThlYjY5YWQyOTczZDNjODM3ZDYtZjRhOS00OTlkLThmNDktNzA2MmUxZTNiYmI2%26state%3DCfDJ8OSJ4mcSBN1Cm3RmDyDIwh5laxT7GJezGjNNWp5rLUOmge3BVJ2lSo0iR6gplpJf8-U-owHI9VY6xrUgsTm_yTmf8XCsLZXuSIcVW4BS6o0Rrf6CtPvioLi94nxd0rLKXaLyQsdCr8OzqWjH-h62nT7XGVII_b0kdXbu75vXsp_BH2ft9SY4_iA-1uhPsNN28OdD5rS7i7yg4qdsSFtx-oxADQXNDCuz2ZkvErTexKptOTppvg4pfQWzOe6YjKacZntsMoVc6TGgW1b5pIViKzjlpUNd9Sebw2LTAXRhmlmcVj5RTpyUq467ogC03gIkmV9pvo-N-kVXWxb0_L-1olyrlEUF36NL60PgRmgsMp4f3MxkZjwy7scjnLVWTR0JLsGYk6x1HjlnzCRoMB4-722vtYC5PsNbPeeMGuHiG9wUAUuQwRWQ1VeHrstx-mgfDA%26x-client-SKU%3DID_NETSTANDARD2_0%26x-client-ver%3D6.10.0.0';
    
    private $urlPaso2 = 'https://login.mat.sat.gob.mx/nidp/app/login?id=contr-dual-eFirma-totp&sid=0&option=credential&sid=0&target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge%3DkeBew7q19naJxaTIXK27KNtB886H1Z0u8eFGqIhuzyQ%26code_challenge_method%3DS256%26response_mode%3Dform_post%26nonce%3D639007739736089596.MGFhODJjMjItYzAyNS00OWQyLWJiZGQtNThlYjY5YWQyOTczZDNjODM3ZDYtZjRhOS00OTlkLThmNDktNzA2MmUxZTNiYmI2%26state%3DCfDJ8OSJ4mcSBN1Cm3RmDyDIwh5laxT7GJezGjNNWp5rLUOmge3BVJ2lSo0iR6gplpJf8-U-owHI9VY6xrUgsTm_yTmf8XCsLZXuSIcVW4BS6o0Rrf6CtPvioLi94nxd0rLKXaLyQsdCr8OzqWjH-h62nT7XGVII_b0kdXbu75vXsp_BH2ft9SY4_iA-1uhPsNN28OdD5rS7i7yg4qdsSFtx-oxADQXNDCuz2ZkvErTexKptOTppvg4pfQWzOe6YjKacZntsMoVc6TGgW1b5pIViKzjlpUNd9Sebw2LTAXRhmlmcVj5RTpyUq467ogC03gIkmV9pvo-N-kVXWxb0_L-1olyrlEUF36NL60PgRmgsMp4f3MxkZjwy7scjnLVWTR0JLsGYk6x1HjlnzCRoMB4-722vtYC5PsNbPeeMGuHiG9wUAUuQwRWQ1VeHrstx-mgfDA%26x-client-SKU%3DID_NETSTANDARD2_0%26x-client-ver%3D6.10.0.0';
    
    private $urlRedireccion = 'https://ptsc32d.clouda.sat.gob.mx/?/reporteOpinion32DContribuyente';
    
    /**
     * Obtener formulario SAT completo (flujo de 2 pasos)
     */
    public function obtenerFormularioSATCompleto()
    {
        try {
            Log::info('=== INICIANDO FLUJO COMPLETO SAT ===');
            
            // PASO 1: Obtener URL1 (establece cookies/sesión)
            Log::info('PASO 1: Obteniendo URL inicial para establecer cookies...');
            $paso1 = $this->obtenerPaginaInicial();
            
            if (!$paso1['success']) {
                throw new Exception('❌ Error en Paso 1: ' . $paso1['error']);
            }
            
            Log::info('✅ Paso 1 completado. Cookies obtenidas: ' . count($paso1['cookies']));
            
            // PASO 2: Obtener URL2 (formulario real con cookies)
            Log::info('PASO 2: Obteniendo formulario real con cookies...');
            $paso2 = $this->obtenerFormularioReal($paso1['cookies']);
            
            if (!$paso2['success']) {
                throw new Exception('❌ Error en Paso 2: ' . $paso2['error']);
            }
            
            Log::info('✅ Paso 2 completado. Formulario obtenido.');
            
            return [
                'success' => true,
                'message' => 'Flujo completo completado exitosamente',
                'paso_1' => [
                    'url' => $this->urlPaso1,
                    'status' => $paso1['status'],
                    'cookies_count' => count($paso1['cookies']),
                    'redirected' => $paso1['redirected'],
                    'timestamp' => $paso1['timestamp']
                ],
                'paso_2' => [
                    'url' => $this->urlPaso2,
                    'status' => $paso2['status'],
                    'content_length' => $paso2['content_length'],
                    'tiene_formulario' => $paso2['tiene_formulario'],
                    'tiene_campos_archivo' => $paso2['tiene_campos_archivo'],
                    'campos_encontrados' => $paso2['campos_encontrados'],
                    'timestamp' => $paso2['timestamp']
                ],
                'campos_detectados' => $paso2['campos_encontrados'],
                'cookies_finales' => $paso2['cookies'],
                'html_formulario' => $paso2['html'],
                'timestamp' => now()->toDateTimeString()
            ];
            
        } catch (Exception $e) {
            Log::error('❌ Error en flujo completo SAT: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ];
        }

        
    }
    // En la clase SatService, agrega estas constantes
    private $camposSAT = [
        'certificado_texto' => 'txtCertificate',
        'certificado_archivo' => 'fileCertificate',
        'llave_texto' => 'txtPrivateKey',
        'llave_archivo' => 'filePrivateKey',
        'password' => 'privateKeyPassword',
        'rfc' => 'rfc',
        'submit' => 'submit',
        'token' => 'token',
        'tokenuuid' => 'tokenuuid'
    ];
    

/**
 * Enviar formulario al SAT (usando los campos reales encontrados)
 */
public function enviarFormularioSATReal(Cliente $cliente)
{
    try {
        Log::info("Enviando formulario SAT para cliente: {$cliente->rfc}");
        
        // Verificar archivos
        if (!$this->verificarArchivosCliente($cliente)) {
            throw new Exception('El cliente no tiene los archivos necesarios');
        }
        
        // Primero necesitamos obtener la página para extraer tokenuuid
        Log::info('Obteniendo página del formulario para extraer tokens...');
        $paginaFormulario = $this->obtenerPaginaFormulario();
        
        if (!$paginaFormulario['success']) {
            throw new Exception('Error obteniendo formulario: ' . $paginaFormulario['error']);
        }
        
        // Extraer tokenuuid del HTML
        $tokenuuid = $this->extraerTokenUuid($paginaFormulario['html']);
        if (!$tokenuuid) {
            $tokenuuid = 'ZGQwYTU2NDktYmJjNi00MGEyLWE1ZGQtMWE5N2E4NjNkNjhl'; // Valor por defecto del HTML
        }
        
        // Obtener paths de archivos
        $certPath = Storage::disk('public')->path($cliente->certificado_path);
        $keyPath = Storage::disk('public')->path($cliente->llave_path);
        
        // URL de acción del formulario
        $actionUrl = 'https://login.mat.sat.gob.mx/nidp/app/login';
        
        // Construir datos multipart
        $multipart = [
            // Archivo certificado (.cer)
            [
                'name' => 'fileCertificate',
                'contents' => fopen($certPath, 'r'),
                'filename' => 'certificado.cer',
                'headers' => ['Content-Type' => 'application/x-x509-ca-cert']
            ],
            
            // Archivo llave (.key)
            [
                'name' => 'filePrivateKey',
                'contents' => fopen($keyPath, 'r'),
                'filename' => 'llave.key',
                'headers' => ['Content-Type' => 'application/octet-stream']
            ],
            
            // Contraseña
            [
                'name' => 'privateKeyPassword',
                'contents' => $cliente->contrasena_fiel
            ],
            
            // RFC
            [
                'name' => 'rfc',
                'contents' => $cliente->rfc
            ],
            
            // Token UUID (extraído del formulario)
            [
                'name' => 'tokenuuid',
                'contents' => $tokenuuid
            ],
            
            // Token (se generará vacío primero)
            [
                'name' => 'token',
                'contents' => ''
            ],
            
            // Otros campos hidden necesarios
            [
                'name' => 'credentialsRequired',
                'contents' => 'CERT'
            ],
            [
                'name' => 'guid',
                'contents' => $tokenuuid
            ],
            [
                'name' => 'ks',
                'contents' => 'null'
            ],
            [
                'name' => 'urlApplet',
                'contents' => 'https://login.mat.sat.gob.mx/nidp/app/login?id=contr-eFirma'
            ],
            
            // Botón submit
            [
                'name' => 'submit',
                'contents' => 'Enviar'
            ]
        ];
        
        // Usar cookies de la página obtenida
        $cookies = $paginaFormulario['cookies'] ?? [];
        
        Log::info("Enviando formulario a: {$actionUrl}");
        Log::info("Token UUID: {$tokenuuid}");
        Log::info("RFC cliente: {$cliente->rfc}");
        
        // Enviar solicitud
        $response = Http::withoutVerifying()
            ->timeout(120)
            ->withOptions([
                'cookies' => $cookies,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'es-MX,es;q=0.9,en;q=0.8',
                    'Referer' => 'https://login.mat.sat.gob.mx/',
                    'Origin' => 'https://login.mat.sat.gob.mx',
                    'Content-Type' => 'multipart/form-data'
                ]
            ])
            ->asMultipart()
            ->post($actionUrl, $multipart);
        
        // Guardar respuesta
        $debugPath = 'sat_respuestas/respuesta_' . $cliente->rfc . '_' . date('Ymd_His');
        Storage::put($debugPath . '.html', $response->body());
        
        Log::info("Respuesta recibida - Status: {$response->status()}");
        
        // Procesar respuesta
        return $this->procesarRespuestaFormulario($response->body(), $cliente, $debugPath);
        
    } catch (Exception $e) {
        Log::error("Error enviando formulario SAT: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'cliente' => $cliente->rfc,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}

/**
 * Obtener página del formulario para extraer tokens
 */
private function obtenerPaginaFormulario()
{
    try {
        $url = 'https://login.mat.sat.gob.mx/nidp//app/login?target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge_method%3DS256%26response_mode%3Dform_post';
        
        $response = Http::withoutVerifying()
            ->timeout(60)
            ->withOptions([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ])
            ->get($url);
        
        return [
            'success' => true,
            'html' => $response->body(),
            'cookies' => $response->cookies(),
            'status' => $response->status()
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Extraer tokenuuid del HTML
 */
private function extraerTokenUuid($html)
{
    if (preg_match('/tokenuuid["\']\s*value=["\']([^"\']+)["\']/i', $html, $matches)) {
        return html_entity_decode($matches[1]);
    }
    
    return null;
}

/**
 * Procesar respuesta del formulario
 */
private function procesarRespuestaFormulario($respuesta, Cliente $cliente, $debugPath)
{
    // Verificar si es PDF
    if ($this->esArchivoPDF($respuesta)) {
        Log::info('✅ Respuesta es un PDF. Parseando...');
        
        $resultado = $this->parsearPDF($respuesta);
        $pdfPath = $this->guardarPDF($respuesta, $cliente);
        
        return [
            'success' => true,
            'resultado' => $resultado,
            'pdf_path' => $pdfPath,
            'tipo' => 'PDF',
            'cliente' => $cliente->rfc,
            'mensaje' => '✅ Proceso completado exitosamente'
        ];
    }
    
    // Si no es PDF, analizar HTML
    Log::info('📄 Respuesta es HTML. Analizando...');
    
    // Buscar redirección o error
    $analisis = $this->analizarRespuestaHTMLCompleta($respuesta);
    
    return [
        'success' => $analisis['es_exito'] ?? false,
        'resultado' => $analisis['tipo'] ?? 'HTML',
        'mensaje' => $analisis['mensaje'] ?? 'Respuesta HTML recibida',
        'debug_path' => $debugPath,
        'analisis' => $analisis,
        'cliente' => $cliente->rfc
    ];
}

/**
 * Analizar respuesta HTML completa
 */
private function analizarRespuestaHTMLCompleta($html)
{
    $analisis = [
        'es_pdf' => $this->esArchivoPDF($html),
        'es_redireccion' => false,
        'es_error' => false,
        'tiene_pdf' => false,
        'mensaje' => 'HTML recibido',
        'tipo' => 'HTML'
    ];
    
    // Buscar redirección
    if (strpos($html, 'window.location') !== false || 
        strpos($html, 'http-equiv="refresh"') !== false ||
        strpos($html, 'location.href') !== false) {
        $analisis['es_redireccion'] = true;
        $analisis['mensaje'] = 'Redirección detectada';
    }
    
    // Buscar errores
    $errores = [
        'Error' => 'Error general',
        'incorrect' => 'Datos incorrectos',
        'invalid' => 'Inválido',
        'obligatorio' => 'Campo obligatorio',
        'revocado' => 'Certificado revocado',
        'caduco' => 'Certificado caduco'
    ];
    
    foreach ($errores as $texto => $mensaje) {
        if (stripos($html, $texto) !== false) {
            $analisis['es_error'] = true;
            $analisis['mensaje'] = $mensaje;
            break;
        }
    }
    
    // Buscar enlace a PDF
    if (preg_match('/href=["\']([^"\']+\.pdf)["\']/i', $html, $matches) ||
        preg_match('/src=["\']([^"\']+\.pdf)["\']/i', $html, $matches)) {
        $analisis['tiene_pdf'] = true;
        $analisis['url_pdf'] = $matches[1];
    }
    
    // Verificar si es la página de resultado
    if (strpos($html, 'Opinión de cumplimiento') !== false ||
        strpos($html, 'cumplimiento de obligaciones') !== false) {
        $analisis['es_exito'] = true;
        $analisis['mensaje'] = 'Página de resultado encontrada';
    }
    
    return $analisis;
}
    
    /**
     * PASO 1: Obtener página inicial (URL1)
     */
    public function obtenerPaginaInicial()
    {
        try {
            Log::info('Obteniendo página inicial (URL1)...');
            
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withOptions([
                    'verify' => false,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language' => 'es-MX,es;q=0.8,en-US;q=0.5,en;q=0.3',
                        'Referer' => 'https://www.sat.gob.mx/'
                    ],
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => false,
                        'referer' => true,
                        'protocols' => ['http', 'https'],
                        'track_redirects' => true
                    ]
                ])
                ->get($this->urlPaso1);
            
            // Guardar respuesta para análisis
            Storage::put('sat_flujo/url1_' . date('Ymd_His') . '.html', $response->body());
            
            // Obtener cookies
            $cookies = $response->cookies();
            
            // Verificar redirección
            $finalUrl = $response->effectiveUri() ?? $this->urlPaso1;
            $redirected = $finalUrl !== $this->urlPaso1;
            
            return [
                'success' => true,
                'url_original' => $this->urlPaso1,
                'url_final' => $finalUrl,
                'redirected' => $redirected,
                'status' => $response->status(),
                'content_length' => strlen($response->body()),
                'cookies' => $cookies,
                'cookies_count' => count($cookies),
                'is_formulario' => $this->contieneFormulario($response->body()),
                'timestamp' => now()->toDateTimeString()
            ];
            
        } catch (Exception $e) {
            Log::error('Error obteniendo URL1: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'url' => $this->urlPaso1,
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }
    
    /**
     * PASO 2: Obtener formulario real (URL2) con cookies de URL1
     */
    public function obtenerFormularioReal($cookies)
    {
        try {
            Log::info('Obteniendo formulario real (URL2) con cookies...');
            
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withOptions([
                    'verify' => false,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language' => 'es-MX,es;q=0.8,en-US;q=0.5,en;q=0.3',
                        'Referer' => $this->urlPaso1,
                        'Origin' => 'https://login.mat.sat.gob.mx'
                    ],
                    'cookies' => $cookies,
                    'allow_redirects' => false
                ])
                ->get($this->urlPaso2);
            
            $html = $response->body();
            $tamano = strlen($html);
            
            // Guardar respuesta
            Storage::put('sat_flujo/url2_' . date('Ymd_His') . '.html', $html);
            
            // Buscar campos específicos
            $camposEncontrados = $this->buscarCamposEspecificos($html);
            
            return [
                'success' => true,
                'url' => $this->urlPaso2,
                'status' => $response->status(),
                'content_length' => $tamano,
                'html' => $html,
                'campos_encontrados' => $camposEncontrados,
                'cookies' => $response->cookies(),
                'tiene_formulario' => strpos($html, '<form') !== false,
                'tiene_campos_archivo' => $camposEncontrados['fileCertificate']['encontrado'] || 
                                         $camposEncontrados['filePrivately']['encontrado'],
                'campos_archivo_encontrados' => $this->obtenerCamposArchivoEncontrados($camposEncontrados),
                'html_preview' => substr($html, 0, 5000),
                'timestamp' => now()->toDateTimeString()
            ];
            
        } catch (Exception $e) {
            Log::error('Error obteniendo URL2: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'url' => $this->urlPaso2,
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }
    
    /**
     * Buscar campos específicos en el HTML del formulario
     */
    private function buscarCamposEspecificos($html)
    {
        $campos = [
            'fileCertificate' => ['encontrado' => false, 'html' => null, 'attributes' => []],
            'txtCertificate' => ['encontrado' => false, 'html' => null, 'attributes' => []],
            'filePrivately' => ['encontrado' => false, 'html' => null, 'attributes' => []],
            'txtPrivately' => ['encontrado' => false, 'html' => null, 'attributes' => []],
            'privatelyPassword' => ['encontrado' => false, 'html' => null, 'attributes' => []],
            'rfc' => ['encontrado' => false, 'html' => null, 'attributes' => []],
            'submit' => ['encontrado' => false, 'html' => null, 'attributes' => []],
            'contrasena' => ['encontrado' => false, 'html' => null, 'attributes' => []]
        ];
        
        // Patrones para buscar cada campo
        $patrones = [
            'fileCertificate' => '/<input[^>]*name=["\']fileCertificate["\'][^>]*>/i',
            'txtCertificate' => '/<input[^>]*name=["\']txtCertificate["\'][^>]*>/i',
            'filePrivately' => '/<input[^>]*name=["\']filePrivately["\'][^>]*>/i',
            'txtPrivately' => '/<input[^>]*name=["\']txtPrivately["\'][^>]*>/i',
            'privatelyPassword' => '/<input[^>]*name=["\']privatelyPassword["\'][^>]*>/i',
            'rfc' => '/<input[^>]*name=["\']rfc["\'][^>]*>/i',
            'submit' => '/(<input[^>]*(name=["\']submit["\']|id=["\']submit["\'])[^>]*>|<button[^>]*type=["\']submit["\'][^>]*>)/i',
            'contrasena' => '/<input[^>]*name=["\']contrasena["\'][^>]*>/i'
        ];
        
        foreach ($patrones as $campo => $patron) {
            if (preg_match($patron, $html, $matches)) {
                $campos[$campo]['encontrado'] = true;
                $campos[$campo]['html'] = htmlspecialchars($matches[0]);
                
                // Extraer atributos comunes
                $atributos = ['name', 'id', 'type', 'value', 'placeholder', 'class', 'onclick', 'accept'];
                foreach ($atributos as $attr) {
                    if (preg_match('/' . $attr . '=["\']([^"\']*)["\']/i', $matches[0], $attrMatch)) {
                        $campos[$campo]['attributes'][$attr] = $attrMatch[1];
                    }
                }
            }
        }
        
        return $campos;
    }
    
    /**
     * Obtener lista de campos de archivo encontrados
     */
    private function obtenerCamposArchivoEncontrados($camposEncontrados)
    {
        $archivos = [];
        $camposArchivo = ['fileCertificate', 'filePrivately'];
        
        foreach ($camposArchivo as $campo) {
            if ($camposEncontrados[$campo]['encontrado']) {
                $archivos[] = [
                    'campo' => $campo,
                    'name' => $camposEncontrados[$campo]['attributes']['name'] ?? $campo,
                    'type' => $camposEncontrados[$campo]['attributes']['type'] ?? 'file',
                    'accept' => $camposEncontrados[$campo]['attributes']['accept'] ?? ''
                ];
            }
        }
        
        return $archivos;
    }
    
    /**
     * Verificar si contiene formulario
     */
    private function contieneFormulario($html)
    {
        return strpos($html, '<form') !== false;
    }
    
    /**
 * Obtener opinión de cumplimiento (MÉTODO PRINCIPAL MEJORADO)
 */
public function obtenerOpinionCumplimiento(Cliente $cliente)
{
    Log::info("=== PROCESO SAT PARA CLIENTE: {$cliente->rfc} ===");
    
    try {
        // Verificar archivos
        if (!$this->verificarArchivosCliente($cliente)) {
            throw new Exception('❌ El cliente no tiene los archivos necesarios (.cer y .key)');
        }
        
        Log::info('✅ Archivos verificados. Iniciando envío de formulario...');
        
        // Usar el nuevo método con los campos correctos
        $resultado = $this->enviarFormularioSATReal($cliente);
        
        Log::info("Resultado del proceso: " . ($resultado['success'] ? 'ÉXITO' : 'ERROR'));
        
        return $resultado;
        
    } catch (Exception $e) {
        Log::error("❌ Error en proceso SAT: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'cliente' => $cliente->rfc,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}

/**
 * Verificar archivos del cliente
 */
public function verificarArchivosCliente(Cliente $cliente)
{
    if (empty($cliente->certificado_path) || empty($cliente->llave_path)) {
        Log::warning("Cliente {$cliente->rfc} no tiene paths de archivos");
        return false;
    }
    
    $certPath = Storage::disk('public')->path($cliente->certificado_path);
    $keyPath = Storage::disk('public')->path($cliente->llave_path);
    
    $certExiste = file_exists($certPath);
    $keyExiste = file_exists($keyPath);
    
    Log::info("Verificación archivos - Certificado: " . ($certExiste ? 'EXISTE' : 'NO EXISTE'));
    Log::info("Verificación archivos - Llave: " . ($keyExiste ? 'EXISTE' : 'NO EXISTE'));
    
    return $certExiste && $keyExiste;
}
    
    /**
     * Enviar formulario con datos del cliente
     */
    private function enviarFormularioConDatos(Cliente $cliente, $htmlFormulario, $cookies)
    {
        // Obtener paths de archivos
        $certPath = Storage::disk('public')->path($cliente->certificado_path);
        $keyPath = Storage::disk('public')->path($cliente->llave_path);
        
        if (!file_exists($certPath) || !file_exists($keyPath)) {
            throw new Exception('Archivos del cliente no encontrados en el servidor');
        }
        
        // Extraer acción del formulario
        $actionUrl = $this->extraerActionFormulario($htmlFormulario);
        if (!$actionUrl) {
            $actionUrl = 'https://login.mat.sat.gob.mx/nidp/app/login';
        }
        
        // Extraer campos ocultos
        $camposOcultos = $this->extraerCamposOcultos($htmlFormulario);
        
        // Construir datos multipart
        $multipart = [
            // Archivo certificado (.cer)
            [
                'name' => 'fileCertificate',
                'contents' => fopen($certPath, 'r'),
                'filename' => 'certificado.cer',
                'headers' => ['Content-Type' => 'application/x-x509-ca-cert']
            ],
            
            // Archivo llave (.key)
            [
                'name' => 'filePrivately',
                'contents' => fopen($keyPath, 'r'),
                'filename' => 'llave.key',
                'headers' => ['Content-Type' => 'application/octet-stream']
            ],
            
            // Contraseña de FIEL
            [
                'name' => 'privatelyPassword',
                'contents' => $cliente->contrasena_fiel
            ],
            
            // RFC (aunque esté disabled, lo enviamos)
            [
                'name' => 'rfc',
                'contents' => $cliente->rfc
            ],
            
            // Botón submit
            [
                'name' => 'submit',
                'contents' => 'Enviar'
            ]
        ];
        
        // Agregar campos ocultos
        foreach ($camposOcultos as $nombre => $valor) {
            $multipart[] = [
                'name' => $nombre,
                'contents' => $valor
            ];
        }
        
        Log::info("Enviando formulario a: {$actionUrl}");
        Log::info("Campos a enviar: " . count($multipart));
        
        // Enviar solicitud
        $response = Http::withoutVerifying()
            ->timeout(120)
            ->withOptions([
                'cookies' => $cookies,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => $this->urlPaso2,
                    'Origin' => 'https://login.mat.sat.gob.mx',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ]
            ])
            ->asMultipart()
            ->post($actionUrl, $multipart);
        
        // Guardar respuesta para depuración
        $debugPath = 'sat_envios/cliente_' . $cliente->rfc . '_' . date('Ymd_His');
        Storage::put($debugPath . '.bin', $response->body());
        
        Log::info("Respuesta recibida - Status: {$response->status()}, Tamaño: " . strlen($response->body()));
        
        return $response->body();
    }
    
    /**
     * Extraer action URL del formulario
     */
    private function extraerActionFormulario($html)
    {
        if (preg_match('/<form[^>]*action=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            $action = html_entity_decode($matches[1]);
            
            // Si es una URL relativa, hacerla absoluta
            if (strpos($action, 'http') !== 0) {
                $baseUrl = 'https://login.mat.sat.gob.mx';
                $action = rtrim($baseUrl, '/') . '/' . ltrim($action, '/');
            }
            
            return $action;
        }
        
        return null;
    }
    
    /**
     * Extraer campos ocultos del formulario
     */
    private function extraerCamposOcultos($html)
    {
        $campos = [];
        
        if (preg_match_all('/<input[^>]*type=["\']hidden["\'][^>]*>/i', $html, $matches)) {
            foreach ($matches[0] as $input) {
                if (preg_match('/name=["\']([^"\']+)["\']/', $input, $nameMatch) &&
                    preg_match('/value=["\']([^"\']*)["\']/', $input, $valueMatch)) {
                    $campos[$nameMatch[1]] = html_entity_decode($valueMatch[1]);
                }
            }
        }
        
        return $campos;
    }
    
    /**
     * Procesar respuesta del SAT
     */
    private function procesarRespuestaSAT($respuesta, Cliente $cliente)
    {
        // Verificar si es PDF
        if ($this->esArchivoPDF($respuesta)) {
            Log::info('✅ Respuesta es un PDF. Parseando...');
            
            $resultado = $this->parsearPDF($respuesta);
            $pdfPath = $this->guardarPDF($respuesta, $cliente);
            
            return [
                'success' => true,
                'resultado' => $resultado,
                'pdf_path' => $pdfPath,
                'tipo' => 'PDF',
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'rfc' => $cliente->rfc
                ],
                'fecha' => now()->format('Y-m-d H:i:s'),
                'mensaje' => '✅ Proceso completado exitosamente. PDF generado.'
            ];
        } else {
            // Es HTML, analizar error o redirección
            Log::info('⚠️ Respuesta es HTML. Analizando...');
            return $this->analizarRespuestaHTML($respuesta, $cliente);
        }
    }
    
    /**
     * Verificar si es archivo PDF
     */
    private function esArchivoPDF($contenido)
    {
        return strpos($contenido, '%PDF-') === 0;
    }
    
    /**
     * Parsear PDF para extraer resultado
     */
    private function parsearPDF($pdfContent)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseContent($pdfContent);
            $text = $pdf->getText();
            
            // Guardar texto extraído para análisis
            $timestamp = date('Ymd_His');
            Storage::put("sat_pdf/texto_{$timestamp}.txt", $text);
            
            // Buscar "Sentido" en el texto
            if (preg_match('/Sentido[\s:]*([^\n\r]+)/i', $text, $matches)) {
                $sentido = trim($matches[1]);
                Log::info("Sentido encontrado en PDF: {$sentido}");
                
                // Normalizar resultado
                if (stripos($sentido, 'POSITIVO') !== false) {
                    return 'POSITIVO';
                } elseif (stripos($sentido, 'NEGATIVO') !== false) {
                    return 'NEGATIVO';
                } else {
                    return 'INDETERMINADO: ' . $sentido;
                }
            }
            
            // Buscar otras variantes
            $patrones = [
                '/Resultado[\s:]*([^\n\r]+)/i',
                '/Cumplimiento[\s:]*([^\n\r]+)/i',
                '/Opinión[\s:]*([^\n\r]+)/i'
            ];
            
            foreach ($patrones as $patron) {
                if (preg_match($patron, $text, $matches)) {
                    return 'ENCONTRADO: ' . trim($matches[1]);
                }
            }
            
            Log::warning('No se encontró "Sentido" en el PDF');
            return 'NO_ENCONTRADO';
            
        } catch (Exception $e) {
            Log::error('Error parseando PDF: ' . $e->getMessage());
            return 'ERROR_PARSE: ' . $e->getMessage();
        }
    }
    
    /**
     * Guardar PDF para referencia
     */
    private function guardarPDF($pdfContent, Cliente $cliente)
    {
        $filename = 'pdf_sat/' . $cliente->rfc . '_' . date('Ymd_His') . '.pdf';
        Storage::put($filename, $pdfContent);
        return $filename;
    }
    
    /**
     * Analizar respuesta HTML (error o redirección)
     */
    private function analizarRespuestaHTML($respuesta, Cliente $cliente)
    {
        $errorPath = 'sat_errors/error_' . $cliente->rfc . '_' . date('Ymd_His') . '.html';
        Storage::put($errorPath, $respuesta);
        
        // Buscar mensajes de error comunes
        $errores = [
            'Este campo es obligatorio' => 'Falta completar algún campo obligatorio',
            'certificado' => 'Error con el certificado (.cer)',
            'contraseña' => 'Contraseña de FIEL incorrecta',
            'inválido' => 'Datos inválidos',
            'Error' => 'Error general del sistema',
            'sesión' => 'Sesión expirada',
            'login' => 'Necesita autenticarse nuevamente'
        ];
        
        $mensajeError = 'Error desconocido';
        foreach ($errores as $texto => $mensaje) {
            if (stripos($respuesta, $texto) !== false) {
                $mensajeError = $mensaje;
                break;
            }
        }
        
        // Verificar si es redirección
        if (strpos($respuesta, 'window.location') !== false || 
            strpos($respuesta, 'http-equiv="refresh"') !== false ||
            strpos($respuesta, 'location.href') !== false) {
            $mensajeError = 'Redirección detectada (posible éxito)';
        }
        
        return [
            'success' => false,
            'error' => $mensajeError,
            'tipo' => 'HTML',
            'debug_path' => $errorPath,
            'respuesta_preview' => substr($respuesta, 0, 500),
            'cliente' => $cliente->rfc,
            'timestamp' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Probar conexión básica con el SAT
     */
    public function probarConexionBasica()
    {
        try {
            Log::info('Probando conexión básica con SAT...');
            
            $startTime = microtime(true);
            
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withOptions([
                    'verify' => false,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ]
                ])
                ->get($this->urlRedireccion);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $resultado = [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'response_time_ms' => $responseTime,
                'url' => $this->urlRedireccion,
                'timestamp' => now()->toDateTimeString(),
                'content_type' => $response->header('Content-Type'),
                'content_length' => strlen($response->body())
            ];
            
            Log::info("Prueba de conexión: " . json_encode($resultado));
            
            return $resultado;
            
        } catch (Exception $e) {
            Log::error('Error en prueba de conexión: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }
    
    /**
     * Probar subida de archivos del cliente
     */
    public function probarSubidaArchivos(Cliente $cliente)
    {
        try {
            if (!Storage::disk('public')->exists($cliente->certificado_path)) {
                throw new Exception("Certificado no encontrado: {$cliente->certificado_path}");
            }
            
            if (!Storage::disk('public')->exists($cliente->llave_path)) {
                throw new Exception("Llave no encontrada: {$cliente->llave_path}");
            }
            
            $certInfo = [
                'path' => $cliente->certificado_path,
                'size' => Storage::disk('public')->size($cliente->certificado_path),
                'extension' => pathinfo($cliente->certificado_path, PATHINFO_EXTENSION),
                'exists' => true
            ];
            
            $keyInfo = [
                'path' => $cliente->llave_path,
                'size' => Storage::disk('public')->size($cliente->llave_path),
                'extension' => pathinfo($cliente->llave_path, PATHINFO_EXTENSION),
                'exists' => true
            ];
            
            return [
                'success' => true,
                'archivos' => [
                    'certificado' => $certInfo,
                    'llave' => $keyInfo
                ],
                'mensaje' => 'Archivos verificados correctamente'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Método anterior para compatibilidad (analizar formulario)
     */
    public function analizarFormularioSAT()
    {
        return $this->obtenerFormularioSATCompleto();
    }

    // Agrega estos métodos al SatService existente

/**
 * Analizar el JavaScript del SAT para entender el flujo real
 */
public function analizarJavaScriptSAT()
{
    try {
        Log::info('Analizando JavaScript del SAT...');
        
        // Obtener el HTML con JavaScript
        $resultadoFlujo = $this->obtenerFormularioSATCompleto();
        
        if (!$resultadoFlujo['success']) {
            throw new Exception('No se pudo obtener el formulario: ' . $resultadoFlujo['error']);
        }
        
        $html = $resultadoFlujo['html_formulario'];
        
        // Buscar script tags
        $scripts = $this->extraerScripts($html);
        
        // Analizar scripts
        $analisisScripts = $this->analizarScripts($scripts);
        
        // Buscar URLs de acción en JavaScript
        $urlsAccion = $this->buscarUrlsAccion($html);
        
        return [
            'success' => true,
            'html_length' => strlen($html),
            'scripts_encontrados' => count($scripts),
            'analisis_scripts' => $analisisScripts,
            'urls_accion' => $urlsAccion,
            'timestamp' => now()->toDateTimeString()
        ];
        
    } catch (Exception $e) {
        Log::error('Error analizando JavaScript: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'timestamp' => now()->toDateTimeString()
        ];
    }
}

/**
 * Extraer scripts del HTML
 */
private function extraerScripts($html)
{
    $scripts = [];
    
    if (preg_match_all('/<script[^>]*>([\s\S]*?)<\/script>/i', $html, $matches)) {
        foreach ($matches[1] as $i => $scriptContent) {
            if (!empty(trim($scriptContent))) {
                $scripts[] = [
                    'content_preview' => substr($scriptContent, 0, 500),
                    'length' => strlen($scriptContent),
                    'has_fingerprint' => strpos($scriptContent, 'fingerprint') !== false ||
                                         strpos($scriptContent, 'Fingerprint') !== false,
                    'has_submit' => strpos($scriptContent, 'submit') !== false ||
                                   strpos($scriptContent, 'Submit') !== false,
                    'has_form' => strpos($scriptContent, 'form') !== false ||
                                 strpos($scriptContent, 'Form') !== false
                ];
            }
        }
    }
    
    return $scripts;
}

/**
 * Analizar scripts para entender el flujo
 */
private function analizarScripts($scripts)
{
    $analisis = [
        'tiene_fingerprinting' => false,
        'tiene_redireccion' => false,
        'tiene_ajax' => false,
        'acciones_detectadas' => []
    ];
    
    foreach ($scripts as $script) {
        if ($script['has_fingerprint']) {
            $analisis['tiene_fingerprinting'] = true;
            $analisis['acciones_detectadas'][] = 'Fingerprinting de dispositivo';
        }
        
        if (strpos($script['content_preview'], 'window.location') !== false ||
            strpos($script['content_preview'], 'location.href') !== false) {
            $analisis['tiene_redireccion'] = true;
            $analisis['acciones_detectadas'][] = 'Redirección JavaScript';
        }
        
        if (strpos($script['content_preview'], 'XMLHttpRequest') !== false ||
            strpos($script['content_preview'], 'fetch(') !== false ||
            strpos($script['content_preview'], '.ajax') !== false) {
            $analisis['tiene_ajax'] = true;
            $analisis['acciones_detectadas'][] = 'Llamadas AJAX';
        }
    }
    
    return $analisis;
}

/**
 * Buscar URLs de acción en el HTML/JavaScript
 */
private function buscarUrlsAccion($html)
{
    $urls = [];
    
    // Buscar en atributos action
    if (preg_match_all('/action=["\']([^"\']+)["\']/i', $html, $matches)) {
        foreach ($matches[1] as $url) {
            $urls[] = [
                'tipo' => 'form_action',
                'url' => html_entity_decode($url),
                'completa' => (strpos($url, 'http') === 0)
            ];
        }
    }
    
    // Buscar en JavaScript
    if (preg_match_all('/["\'](https?:\/\/[^"\']+)["\']/i', $html, $matches)) {
        foreach ($matches[1] as $url) {
            // Filtrar URLs que parezcan de acción
            if (strpos($url, 'login.mat.sat.gob.mx') !== false ||
                strpos($url, 'ptsc32d.clouda.sat.gob.mx') !== false) {
                $urls[] = [
                    'tipo' => 'javascript_url',
                    'url' => html_entity_decode($url)
                ];
            }
        }
    }
    
    return array_slice($urls, 0, 10); // Limitar a 10 URLs
}

// Agrega este método al SatService existente

/**
 * Simular fingerprinting para pasar la protección del SAT
 */
public function simularFingerprinting()
{
    try {
        Log::info('Simulando fingerprinting del SAT...');
        
        // Primero obtener la página con fingerprinting
        $response = Http::withoutVerifying()
            ->timeout(60)
            ->withOptions([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept'=> 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language'=> 'es-MX,es;q=0.9,en;q=0.8',
                    'Accept-Encoding'=> 'gzip, deflate, br',
                    'Connection'=> 'keep-alive',
                    'Upgrade-Insecure-Requests'=> '1',
                    'Sec-Fetch-Dest'=> 'document',
                    'Sec-Fetch-Mode'=> 'navigate',
                    'Sec-Fetch-Site'=> 'none',
                    'Sec-Fetch-User'=> '?1',
                    'Cache-Control'=> 'max-age=0'
                ]
            ])
            ->get($this->urlPaso1);
        
        // Analizar el HTML para extraer datos del fingerprinting
        $html = $response->body();
        
        // Extraer datos del script de fingerprinting
        $fingerprintData = $this->extraerDatosFingerprinting($html);
        
        // Construir datos que espera el SAT
        $postData = [
            'deviceAttributes' => $fingerprintData['device_attributes'] ?? $this->generarDeviceAttributes(),
            'fingerprint' => $fingerprintData['fingerprint'] ?? '',
            'deviceFetchGuidance' => $fingerprintData['device_fetch_guidance'] ?? '',
            'innerCall' => 'true',
            'rid' => $fingerprintData['rid'] ?? '2',
            'firstTimeFingerprint' => 'false'
        ];
        
        // Enviar datos de fingerprinting
        $actionUrl = $this->extraerActionUrl($html) ?? $this->urlPaso1;
        
        Log::info('Enviando datos de fingerprinting a: ' . $actionUrl);
        
        $response2 = Http::withoutVerifying()
            ->timeout(60)
            ->withOptions([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Content-Type'=> 'application/x-www-form-urlencoded',
                    'Origin'=> 'https://login.mat.sat.gob.mx',
                    'Referer'=> $this->urlPaso1,
                    'Accept'=> 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ],
                'cookies' => $response->cookies()
            ])
            ->asForm()
            ->post($actionUrl, $postData);
        
        // Guardar respuesta
        Storage::put('sat_fingerprinting/respuesta_' . date('Ymd_His') . '.html', $response2->body());
        
        return [
            'success' => true,
            'step1_status' => $response->status(),
            'step2_status' => $response2->status(),
            'final_html_length' => strlen($response2->body()),
            'has_formulario' => $this->contieneFormularioReal($response2->body()),
            'fingerprint_data' => $fingerprintData,
            'post_data' => $postData,
            'timestamp' => now()->toDateTimeString()
        ];
        
    } catch (\Exception $e) {
        Log::error('Error simulando fingerprinting: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'timestamp' => now()->toDateTimeString()
        ];
    }
}

private function extraerDatosFingerprinting($html)
{
    $data = [];
    
    // Buscar valores en inputs hidden
    if (preg_match('/deviceAttributes["\']\s*value=["\']([^"\']*)["\']/i', $html, $matches)) {
        $data['device_attributes'] = html_entity_decode($matches[1]);
    }
    
    if (preg_match('/rid["\']\s*value=["\']([^"\']*)["\']/i', $html, $matches)) {
        $data['rid'] = html_entity_decode($matches[1]);
    }
    
    return $data;
}

private function generarDeviceAttributes()
{
    // Generar un deviceAttributes simulado
    return json_encode([
        'deviceType' => 'desktop',
        'deviceLanguage' => 'es-MX',
        'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'cpuArchitecture' => 'x64',
        'timezoneOffset' => -360,
        'colorDepth' => 24,
        'screenResolution' => '1920x1080',
        'navigatorPlatform' => 'Win32'
    ]);
}

private function contieneFormularioReal($html)
{
    // Buscar indicadores del formulario real
    $indicadores = [
        'fileCertificate',
        'filePrivately', 
        'privatelyPassword',
        'Certificado (.cer)',
        'Clave privada',
        'Contraseña de clave privada'
    ];
    
    foreach ($indicadores as $indicador) {
        if (stripos($html, $indicador) !== false) {
            return true;
        }
    }
    
    return false;
}
}