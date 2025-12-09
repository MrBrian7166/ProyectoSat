@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-edit"></i> Editar Cliente: {{ $cliente->nombre }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('clientes.update', $cliente) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="rfc" class="form-label">RFC *</label>
                            <input type="text" class="form-control @error('rfc') is-invalid @enderror" 
                                   id="rfc" name="rfc" value="{{ old('rfc', $cliente->rfc) }}" 
                                   pattern="^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$" 
                                   title="Formato: 13 caracteres alfanuméricos (ejemplo: XAXX010101000)" 
                                   maxlength="13" 
                                   oninput="this.value = this.value.toUpperCase()" 
                                   required>
                            <small class="form-text text-muted">13 caracteres (ejemplo: XAXX010101000)</small>
                            @error('rfc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contrasena_fiel" class="form-label">Contraseña de FIEL</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('contrasena_fiel') is-invalid @enderror" 
                                   id="contrasena_fiel" name="contrasena_fiel" 
                                   placeholder="Dejar en blanco para no cambiar">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePassword('contrasena_fiel')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Solo llene este campo si desea cambiar la contraseña</small>
                        @error('contrasena_fiel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="certificado" class="form-label">Certificado (.cer)</label>
                            <input type="file" class="form-control @error('certificado') is-invalid @enderror" 
                                   id="certificado" name="certificado" accept=".cer,.pem">
                            <small class="form-text text-muted">
                                Dejar en blanco para mantener el actual
                                @if($cliente->certificado_path)
                                    <br>
                                    <span class="text-success">
                                        <i class="fas fa-file"></i> Actual: {{ basename($cliente->certificado_path) }}
                                    </span>
                                @endif
                            </small>
                            @error('certificado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="llave" class="form-label">Llave privada (.key)</label>
                            <input type="file" class="form-control @error('llave') is-invalid @enderror" 
                                   id="llave" name="llave" accept=".key,.pem,.txt">
                            <small class="form-text text-muted">
                                Dejar en blanco para mantener la actual
                                @if($cliente->llave_path)
                                    <br>
                                    <span class="text-success">
                                        <i class="fas fa-key"></i> Actual: {{ basename($cliente->llave_path) }}
                                    </span>
                                @endif
                            </small>
                            @error('llave')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Los campos de archivo son opcionales. Solo suba nuevos archivos si desea reemplazar los existentes.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-history"></i> Información del Registro</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Fecha de creación:</strong> {{ $cliente->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Última actualización:</strong> {{ $cliente->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        button.classList.remove('fa-eye');
        button.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        button.classList.remove('fa-eye-slash');
        button.classList.add('fa-eye');
    }
}
</script>
@endpush