<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SatPuppeteerService
{
    /**
     * Ejecutar Puppeteer y capturar toda la salida
     */
    public function ejecutarPuppeteerDebug()
    {
        try {
            Log::info('=== EJECUTANDO PUPPETEER DEBUG ===');
            
            $scriptPath = base_path('scripts/sat-simple.mjs');
            
            if (!file_exists($scriptPath)) {
                throw new \Exception("Script no encontrado: {$scriptPath}");
            }
            
            // Verificar Node.js
            $nodeCheck = new Process(['node', '--version']);
            $nodeCheck->run();
            
            if (!$nodeCheck->isSuccessful()) {
                throw new \Exception('Node.js no está disponible: ' . $nodeCheck->getErrorOutput());
            }
            
            Log::info('Node.js version: ' . trim($nodeCheck->getOutput()));
            
            // Ejecutar Puppeteer con más detalles
            $process = new Process(['node', $scriptPath]);
            $process->setTimeout(300); // 5 minutos
            $process->setWorkingDirectory(base_path());
            
            // Capturar toda la salida
            $process->start();
            
            $output = '';
            $errorOutput = '';
            
            // Leer salida en tiempo real
            $process->wait(function ($type, $buffer) use (&$output, &$errorOutput) {
                if ($type === Process::OUT) {
                    $output .= $buffer;
                } else {
                    $errorOutput .= $buffer;
                }
            });
            
            Log::info("Output length: " . strlen($output));
            Log::info("Error output length: " . strlen($errorOutput));
            
            // Guardar logs completos
            $logPath = storage_path('logs/puppeteer_debug_' . date('Ymd_His') . '.txt');
            file_put_contents($logPath, "=== OUTPUT ===\n{$output}\n\n=== ERROR ===\n{$errorOutput}");
            
            Log::info("Logs guardados en: {$logPath}");
            
            // Intentar extraer JSON del output
            $jsonResult = $this->extraerJsonDelOutput($output);
            
            if ($jsonResult !== null) {
                Log::info('JSON extraído exitosamente');
                return $jsonResult;
            }
            
            // Si no hay JSON, verificar si hay archivos generados
            $htmlPath = base_path('sat_test.html');
            $screenshotPath = base_path('sat_test.png');
            
            $resultado = [
                'success' => false,
                'error' => 'No se pudo extraer JSON del output',
                'output_preview' => substr($output, 0, 1000),
                'error_preview' => substr($errorOutput, 0, 1000),
                'log_path' => $logPath,
                'html_exists' => file_exists($htmlPath),
                'screenshot_exists' => file_exists($screenshotPath),
                'timestamp' => now()->toDateTimeString()
            ];
            
            // Si hay archivos generados, agregar información
            if (file_exists($htmlPath)) {
                $html = file_get_contents($htmlPath);
                $resultado['html_length'] = strlen($html);
                $resultado['html_preview'] = substr($html, 0, 2000);
                
                // Buscar campos en HTML
                $resultado['campos_encontrados'] = $this->buscarCamposEnHTML($html);
            }
            
            return $resultado;
            
        } catch (\Exception $e) {
            Log::error('Error en ejecutarPuppeteerDebug: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }
    
    /**
     * Extraer JSON del output de Puppeteer
     */
    private function extraerJsonDelOutput($output)
    {
        // Buscar JSON en el output
        $jsonStart = strpos($output, '{\n');
        if ($jsonStart === false) {
            $jsonStart = strpos($output, '{');
        }
        
        $jsonEnd = strrpos($output, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
            $jsonStr = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);
            
            // Limpiar posibles caracteres extraños
            $jsonStr = trim($jsonStr);
            
            // Intentar decodificar
            $result = json_decode($jsonStr, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $result;
            }
            
            Log::warning('JSON decode error: ' . json_last_error_msg());
            Log::warning('JSON string: ' . substr($jsonStr, 0, 500));
        }
        
        return null;
    }
    
    /**
     * Buscar campos en HTML
     */
    private function buscarCamposEnHTML($html)
    {
        $campos = [
            'fileCertificate' => ['encontrado' => false, 'html' => ''],
            'filePrivately' => ['encontrado' => false, 'html' => ''],
            'privatelyPassword' => ['encontrado' => false, 'html' => ''],
            'rfc' => ['encontrado' => false, 'html' => ''],
            'submit' => ['encontrado' => false, 'html' => '']
        ];
        
        foreach ($campos as $nombre => &$info) {
            $pattern = '/<input[^>]*name=["\']' . preg_quote($nombre, '/') . '["\'][^>]*>/i';
            if (preg_match($pattern, $html, $matches)) {
                $info['encontrado'] = true;
                $info['html'] = htmlspecialchars($matches[0]);
                
                // Extraer tipo
                if (preg_match('/type=["\']([^"\']+)["\']/i', $matches[0], $typeMatch)) {
                    $info['type'] = $typeMatch[1];
                }
            }
        }
        
        return $campos;
    }
    
    /**
     * Método principal para pruebas
     */
    public function probarPuppeteer()
    {
        return $this->ejecutarPuppeteerDebug();
    }
    
    /**
     * Ver archivos generados por Puppeteer
     */
    public function verArchivosGenerados()
    {
        $htmlPath = base_path('sat_test.html');
        $screenshotPath = base_path('sat_test.png');
        
        $resultado = [
            'html' => [
                'existe' => file_exists($htmlPath),
                'ruta' => $htmlPath,
                'tamaño' => file_exists($htmlPath) ? filesize($htmlPath) : 0
            ],
            'screenshot' => [
                'existe' => file_exists($screenshotPath),
                'ruta' => $screenshotPath,
                'tamaño' => file_exists($screenshotPath) ? filesize($screenshotPath) : 0
            ],
            'timestamp' => now()->toDateTimeString()
        ];
        
        if (file_exists($htmlPath)) {
            $html = file_get_contents($htmlPath);
            $resultado['html_preview'] = substr($html, 0, 3000);
            $resultado['campos_en_html'] = $this->buscarCamposEnHTML($html);
        }
        
        return $resultado;
    }
}