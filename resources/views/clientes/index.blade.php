@extends('layouts.app')

@section('title', 'Clientes SAT')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="fas fa-users"></i> Clientes Registrados
    </h2>
    <div>
        <!-- Botón para ir al proceso SAT -->
        <a href="{{ route('proceso.index') }}" class="btn btn-success me-2">
            <i class="fas fa-file-contract"></i> Obtencion de Opinion
        </a>
        
        <!-- Botón para nuevo cliente -->
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Cliente
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($clientes->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                <h4>No hay clientes registrados</h4>
                <p class="text-muted">Comienza agregando tu primer cliente.</p>
                <a href="{{ route('clientes.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-user-plus"></i> Registrar Primer Cliente
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Certificado</th>
                            <th>Llave</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id }}</td>
                            <td>{{ $cliente->nombre }}</td>
                            <td>
                                <span class="badge bg-info">{{ $cliente->rfc }}</span>
                            </td>
                            <td>
                                @if($cliente->certificado_path)
                                    <span class="badge bg-success">
                                        <i class="fas fa-file-certificate"></i> Cargado
                                    </span>
                                @else
                                    <span class="badge bg-danger">No cargado</span>
                                @endif
                            </td>
                            <td>
                                @if($cliente->llave_path)
                                    <span class="badge bg-success">
                                        <i class="fas fa-key"></i> Cargada
                                    </span>
                                @else
                                    <span class="badge bg-danger">No cargada</span>
                                @endif
                            </td>
                            <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('clientes.show', $cliente) }}" 
                                    class="btn btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" 
                                    class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('clientes.destroy', $cliente) }}" 
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('¿Estás seguro de eliminar este cliente?')" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection