@extends('layouts.app')

@section('title', 'Detalles del Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">
                    <i class="fas fa-eye"></i> Detalles del Cliente
                </h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <div class="avatar-circle bg-primary text-white mb-3">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h5>{{ $cliente->nombre }}</h5>
                        <span class="badge bg-secondary">ID: {{ $cliente->id }}</span>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-id-card text-primary"></i> Información General</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $cliente->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>RFC:</strong></td>
                                        <td>
                                            <span class="badge bg-info">{{ $cliente->rfc }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Registrado:</strong></td>
                                        <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Actualizado:</strong></td>
                                        <td>{{ $cliente->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-file-certificate text-success"></i> Archivos</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Certificado:</strong></td>
                                        <td>
                                            @if($cliente->certificado_path)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Cargado
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ basename($cliente->certificado_path) }}
                                                </small>
                                            @else
                                                <span class="badge bg-danger">No cargado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Llave privada:</strong></td>
                                        <td>
                                            @if($cliente->llave_path)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Cargada
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ basename($cliente->llave_path) }}
                                                </small>
                                            @else
                                                <span class="badge bg-danger">No cargada</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Nota:</strong> La contraseña de FIEL no se muestra por seguridad.
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-primary me-md-2">
                        <i class="fas fa-edit"></i> Editar Cliente
                    </a>
                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('¿Estás seguro de eliminar este cliente?')">
                            <i class="fas fa-trash"></i> Eliminar Cliente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>
@endsection