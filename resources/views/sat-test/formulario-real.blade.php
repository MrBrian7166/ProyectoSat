@extends('layouts.app')

@section('title', 'Formulario Real SAT')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">
                <i class="fas fa-check-circle"></i> Formulario Real del SAT Encontrado
                <small class="float-end">{{ $timestamp }}</small>
            </h4>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <h5><i class="fas fa-check"></i> ¡Formulario cargado exitosamente!</h5>
                <p><strong>URL:</strong> <code>{{ $resultado['url'] }}</code></p>
                <p><strong>Status:</strong> <span class="badge bg-success">{{ $resultado['status_code'] }}</span></p>
                <p><strong>Tamaño:</strong> {{ number_format($resultado['content_length']) }} bytes</p>
                <p><strong>Tiempo respuesta:</strong> {{ $resultado['response_time_ms'] }} ms</p>
            </div>
            
            <!-- Campos encontrados -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Campos Detectados en el Formulario</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($resultado['campos_encontrados'] as $nombre => $campo)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 {{ $campo['encontrado'] ? 'border-success' : 'border-warning' }}">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        @if($campo['encontrado'])
                                            <i class="fas fa-check text-success"></i>
                                        @else
                                            <i class="fas fa-times text-warning"></i>
                                        @endif
                                        {{ $nombre }}
                                    </h6>
                                    
                                    @if($campo['encontrado'])
                                        @if(!empty($campo['attributes']))
                                            <div class="small">
                                                @foreach($campo['attributes'] as $attr => $value)
                                                    @if($value)
                                                    <div><strong>{{ $attr }}:</strong> <code>{{ $value }}</code></div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($campo['html'])
                                            <div class="mt-2">
                                                <small class="text-muted">HTML:</small>
                                                <pre class="bg-light p-2 small mb-0">{{ $campo['html'] }}</pre>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-muted small mb-0">No encontrado en el formulario</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Resumen -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Resumen</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li>
                                    @if($resultado['tiene_formulario'])
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-danger"></i>
                                    @endif
                                    Tiene formulario: {{ $resultado['tiene_formulario'] ? 'Sí' : 'No' }}
                                </li>
                                <li>
                                    @if($resultado['tiene_campos_archivo'])
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-danger"></i>
                                    @endif
                                    Campos de archivo: {{ $resultado['tiene_campos_archivo'] ? 'Sí' : 'No' }}
                                </li>
                                <li>
                                    <i class="fas fa-file-alt"></i>
                                    Content-Type: {{ $resultado['content_type'] }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">Siguientes Pasos</h6>
                        </div>
                        <div class="card-body">
                            <ol class="small">
                                <li>Verificar que todos los campos necesarios estén presentes</li>
                                <li>Preparar archivos .cer y .key del cliente</li>
                                <li>Probar envío del formulario</li>
                                <li>Procesar respuesta PDF</li>
                                <li>Extraer resultado "Sentido"</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Vista previa del HTML -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> Vista Previa del HTML (primeros 5000 caracteres)</h5>
                </div>
                <div class="card-body">
                    <pre style="max-height: 500px; overflow: auto; font-size: 11px; background: #f8f9fa; padding: 15px; border-radius: 5px;">{{ htmlspecialchars($resultado['html_preview']) }}</pre>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('sat-test.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Volver a Pruebas
                </a>
                <a href="{{ route('proceso.index') }}" class="btn btn-success">
                    <i class="fas fa-play"></i> Probar con Cliente
                </a>
                <a href="  https://login.mat.sat.gob.mx/nidp//app/login?target=https%3A%2F%2Flogin.mat.sat.gob.mx%2Fnidp%2Foauth%2Fnam%2Fauthz%3Fclient_id%3D50bffab8-793c-41c4-b639-9abe2a93cb2c%26redirect_uri%3Dhttps%3A%2F%2Fptsc32d.clouda.sat.gob.mx%3A443%2Foauth2%2Fcallback%26response_type%3Dcode%26scope%3Dopenid%2Bmscontribuyente%26code_challenge%3DkeBew7q19naJxaTIXK27KNtB886H1Z0u8eFGqIhuzyQ%26code_challenge_method%3DS256%26response_mode%3Dform_post%26nonce%3D639007739736089596.MGFhODJjMjItYzAyNS00OWQyLWJiZGQtNThlYjY5YWQyOTczZDNjODM3ZDYtZjRhOS00OTlkLThmNDktNzA2MmUxZTNiYmI2%26state%3DCfDJ8OSJ4mcSBN1Cm3RmDyDIwh5laxT7GJezGjNNWp5rLUOmge3BVJ2lSo0iR6gplpJf8-U-owHI9VY6xrUgsTm_yTmf8XCsLZXuSIcVW4BS6o0Rrf6CtPvioLi94nxd0rLKXaLyQsdCr8OzqWjH-h62nT7XGVII_b0kdXbu75vXsp_BH2ft9SY4_iA-1uhPsNN28OdD5rS7i7yg4qdsSFtx-oxADQXNDCuz2ZkvErTexKptOTppvg4pfQWzOe6YjKacZntsMoVc6TGgW1b5pIViKzjlpUNd9Sebw2LTAXRhmlmcVj5RTpyUq467ogC03gIkmV9pvo-N-kVXWxb0_L-1olyrlEUF36NL60PgRmgsMp4f3MxkZjwy7scjnLVWTR0JLsGYk6x1HjlnzCRoMB4-722vtYC5PsNbPeeMGuHiG9wUAUuQwRWQ1VeHrstx-mgfDA%26x-client-SKU%3DID_NETSTANDARD2_0%26x-client-ver%3D6.10.0.0" 
                   class="btn btn-info" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Ver Formulario SAT
                </a>
            </div>
        </div>
    </div>
</div>
@endsection