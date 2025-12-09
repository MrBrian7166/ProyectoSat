@extends('layouts.app')

@section('title', 'Flujo Completo SAT')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-sitemap"></i> Flujo Completo del SAT
            </h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Este proceso simula el flujo completo: <strong>URL1 → Cookies → URL2 → Formulario</strong>
            </div>
            
            <!-- Pasos del flujo -->
            <div class="row mb-4">
                <div class="col-md-3 text-center">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px;">
                                1
                            </div>
                            <h6>URL Inicial</h6>
                            <p class="small text-muted">Establece cookies de sesión</p>
                            <a href="{{ route('sat-test.paso-1') }}" class="btn btn-sm btn-outline-primary">
                                Probar
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 text-center">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px;">
                                2
                            </div>
                            <h6>Cookies</h6>
                            <p class="small text-muted">Manejo de sesión</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 text-center">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px;">
                                3
                            </div>
                            <h6>Formulario Real</h6>
                            <p class="small text-muted">URL2 con campos</p>
                            <a href="{{ route('sat-test.ver-paso-2') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                Ver HTML
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 text-center">
                    <div class="card border-success">
                        <div class="card-body">
                            <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px;">
                                4
                            </div>
                            <h6>Envío</h6>
                            <p class="small text-muted">Archivos + datos</p>
                            <a href="{{ route('sat-test.flujo-completo') }}" class="btn btn-sm btn-success">
                                Probar Todo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resultados -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-play-circle"></i> Ejecutar Prueba Completa</h5>
                </div>
                <div class="card-body">
                    <p>Ejecuta el flujo completo para ver si funciona:</p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="{{ route('sat-test.flujo-completo') }}" class="btn btn-primary me-2" target="_blank">
                            <i class="fas fa-play"></i> Ejecutar Flujo Completo
                        </a>
                        
                        <a href="{{ route('sat-test.ver-paso-1') }}" class="btn btn-info me-2" target="_blank">
                            <i class="fas fa-eye"></i> Ver Paso 1 (URL1)
                        </a>
                        
                        <a href="{{ route('sat-test.ver-paso-2') }}" class="btn btn-warning" target="_blank">
                            <i class="fas fa-eye"></i> Ver Paso 2 (URL2)
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <h6>Clientes disponibles para prueba:</h6>
                        <div class="list-group">
                            @foreach(\App\Models\Cliente::all() as $cliente)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $cliente->nombre }}</strong>
                                        <small class="text-muted ms-2">{{ $cliente->rfc }}</small>
                                    </div>
                                    <div>
                                        @if($cliente->certificado_path && $cliente->llave_path)
                                        <span class="badge bg-success me-2">Archivos OK</span>
                                        <a href="{{ route('sat-test.ejecutar-real', $cliente->id) }}" 
                                           class="btn btn-sm btn-outline-success">
                                            Probar Cliente
                                        </a>
                                        @else
                                        <span class="badge bg-danger">Faltan archivos</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-number {
    font-weight: bold;
    font-size: 1.2rem;
}
</style>
@endsection