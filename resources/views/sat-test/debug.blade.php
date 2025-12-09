@extends('layouts.app')

@section('title', 'Archivos de Depuración SAT')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">
                <i class="fas fa-bug"></i> Archivos de Depuración SAT
            </h4>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Nota:</strong> Estos archivos se almacenan en: <code>{{ $storagePath }}</code>
            </div>
            
            <!-- Archivos de debug -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Archivos de Debug ({{ count($debugFiles) }})</h5>
                </div>
                <div class="card-body">
                    @if(count($debugFiles) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tamaño</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($debugFiles as $file)
                                    <tr>
                                        <td>{{ basename($file) }}</td>
                                        <td>{{ number_format(\Storage::size($file) / 1024, 2) }} KB</td>
                                        <td>
                                            <a href="{{ route('sat-test.debug-file', basename($file)) }}" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('sat-test.debug-file', basename($file)) }}?download=1" 
                                               class="btn btn-sm btn-secondary">
                                                <i class="fas fa-download"></i> Descargar
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay archivos de debug.</p>
                    @endif
                </div>
            </div>
            
            <!-- Archivos de análisis -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Archivos de Análisis ({{ count($analysisFiles) }})</h5>
                </div>
                <div class="card-body">
                    @if(count($analysisFiles) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tamaño</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analysisFiles as $file)
                                    <tr>
                                        <td>{{ basename($file) }}</td>
                                        <td>{{ number_format(\Storage::size($file) / 1024, 2) }} KB</td>
                                        <td>
                                            <a href="/storage/app/{{ $file }}" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay archivos de análisis.</p>
                    @endif
                </div>
            </div>
            
            <!-- Archivos de error -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Archivos de Error ({{ count($errorFiles) }})</h5>
                </div>
                <div class="card-body">
                    @if(count($errorFiles) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tamaño</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($errorFiles as $file)
                                    <tr>
                                        <td>{{ basename($file) }}</td>
                                        <td>{{ number_format(\Storage::size($file) / 1024, 2) }} KB</td>
                                        <td>
                                            <a href="/storage/app/{{ $file }}" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay archivos de error.</p>
                    @endif
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('sat-test.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Volver a Pruebas SAT
                </a>
                <button onclick="location.reload()" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Actualizar Lista
                </button>
            </div>
        </div>
    </div>
</div>
@endsection