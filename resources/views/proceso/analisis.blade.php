@extends('layouts.app')

@section('title', 'Análisis del Formulario SAT')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-search"></i> Análisis del Formulario SAT
                <small class="float-end">{{ $timestamp ?? now()->format('Y-m-d H:i:s') }}</small>
            </h4>
        </div>
        <div class="card-body">
            @if(isset($analisis['success']) && $analisis['success'])
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Análisis completado exitosamente</h5>
                    <p>URL: <strong>{{ $analisis['url'] }}</strong></p>
                    <p>Status: <span class="badge bg-success">{{ $analisis['status'] }}</span></p>
                    <p>Tamaño: <span class="badge bg-info">{{ number_format($analisis['content_length']) }} bytes</span></p>
                </div>
                
                <!-- Resumen -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h1 class="display-6">{{ $analisis['forms_count'] }}</h1>
                                <p class="text-muted">Formularios</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h1 class="display-6">{{ $analisis['has_file_input'] ? '✅' : '❌' }}</h1>
                                <p class="text-muted">Inputs de archivo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h1 class="display-6">{{ $analisis['has_password_input'] ? '✅' : '❌' }}</h1>
                                <p class="text-muted">Inputs de password</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ¿Es página de login? -->
                @if(strpos($analisis['snippet'], 'login.mat.sat.gob.mx') !== false)
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Página de Login Detectada</h5>
                    <p>El SAT está redirigiendo a un sistema de autenticación OAuth2. Necesitamos manejar el login primero.</p>
                    <p><strong>URL de login:</strong> <code>login.mat.sat.gob.mx</code></p>
                </div>
                @endif
                
                <!-- Campos detectados -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-bullseye"></i> Campos Buscados</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($analisis['campos_detectados'] as $nombre => $campo)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 {{ $campo['encontrado'] ? 'border-success' : 'border-danger' }}">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            @if($campo['encontrado'])
                                                <i class="fas fa-check text-success"></i>
                                            @else
                                                <i class="fas fa-times text-danger"></i>
                                            @endif
                                            {{ ucfirst(str_replace('_', ' ', $nombre)) }}
                                        </h6>
                                        @if($campo['encontrado'])
                                            @if(isset($campo['name']))
                                                <p class="mb-1 small"><strong>Name:</strong> <code>{{ $campo['name'] }}</code></p>
                                            @endif
                                            @if(isset($campo['type']))
                                                <p class="mb-1 small"><strong>Type:</strong> <code>{{ $campo['type'] }}</code></p>
                                            @endif
                                            @if(isset($campo['html']))
                                                <p class="mb-0 small"><strong>HTML:</strong> <small>{{ $campo['html'] }}</small></p>
                                            @endif
                                        @else
                                            <p class="text-muted small mb-0">No encontrado</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Vista previa del HTML -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-code"></i> Vista Previa del HTML (primeros 2000 caracteres)</h5>
                    </div>
                    <div class="card-body">
                        <pre style="max-height: 400px; overflow: auto; font-size: 12px;">{{ htmlspecialchars($analisis['snippet']) }}</pre>
                    </div>
                </div>
                
            @elseif(isset($analisis['error']))
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Error en el análisis</h5>
                    <p><strong>Error:</strong> {{ $analisis['error'] }}</p>
                    @if(isset($analisis['timestamp']))
                        <p><strong>Hora:</strong> {{ $analisis['timestamp'] }}</p>
                    @endif
                </div>
            @else
                <div class="alert alert-warning">
                    <h5><i class="fas fa-question-circle"></i> No se pudo analizar</h5>
                    <p>La respuesta no tiene el formato esperado.</p>
                </div>
            @endif
            
            <div class="mt-4">
                <a href="{{ route('proceso.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Volver al Proceso
                </a>
                <a href="{{ route('sat-test.index') }}" class="btn btn-secondary">
                    <i class="fas fa-vial"></i> Más Pruebas
                </a>
                <button onclick="location.reload()" class="btn btn-success">
                    <i class="fas fa-redo"></i> Actualizar Análisis
                </button>
            </div>
        </div>
    </div>
</div>
@endsection