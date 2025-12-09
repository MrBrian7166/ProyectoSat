

<?php $__env->startSection('title', 'Proceso SAT - Opinión de Cumplimiento'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-contract"></i> Proceso SAT - Opinión de Cumplimiento
                    </h4>
                    <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver a Clientes
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Mensajes de estado -->
                <?php if(session('info')): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> <?php echo e(session('info')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Panel de información -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="alert alert-primary">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle"></i> ¿Qué es la Opinión de Cumplimiento?
                            </h5>
                            <p class="mb-0">
                                La Opinión de Cumplimiento es un documento oficial del SAT que confirma 
                                que un contribuyente se encuentra al corriente en sus obligaciones fiscales.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-file-invoice-dollar fa-3x text-success mb-3"></i>
                                <h5>Proceso Automatizado</h5>
                                <p class="text-muted small">
                                    Sistema integrado con el SAT para generación automática
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pasos del proceso -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">
                            <i class="fas fa-list-ol"></i> Pasos del Proceso
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                            <strong>1</strong>
                                        </div>
                                        <h6>Selección de Cliente</h6>
                                        <p class="small text-muted">Selecciona un cliente registrado para el proceso</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                            <strong>2</strong>
                                        </div>
                                        <h6>Autenticación SAT</h6>
                                        <p class="small text-muted">Autenticación con FIEL (.cer y .key)</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                            <strong>3</strong>
                                        </div>
                                        <h6>Solicitud al SAT</h6>
                                        <p class="small text-muted">Generación y envío de solicitud oficial</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                            <strong>4</strong>
                                        </div>
                                        <h6>Descarga de Documento</h6>
                                        <p class="small text-muted">Obtención del PDF con la opinión</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de proceso -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-play-circle"></i> Iniciar Proceso
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('proceso.procesar')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="cliente_id" class="form-label">Seleccionar Cliente *</label>
                                    <select class="form-select" id="cliente_id" name="cliente_id" required>
                                        <option value="">-- Seleccione un cliente --</option>
                                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cliente->id); ?>">
                                                <?php echo e($cliente->nombre); ?> - <?php echo e($cliente->rfc); ?>

                                                <?php if(!$cliente->certificado_path || !$cliente->llave_path): ?>
                                                    (Faltan archivos)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <small class="form-text text-muted">
                                        El cliente debe tener certificado y llave cargados
                                    </small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="periodo" class="form-label">Periodo *</label>
                                    <select class="form-select" id="periodo" name="periodo" required>
                                        <option value="">-- Seleccione periodo --</option>
                                        <option value="2024-01">Enero 2024</option>
                                        <option value="2024-02">Febrero 2024</option>
                                        <option value="2024-03">Marzo 2024</option>
                                        <option value="2024-04">Abril 2024</option>
                                        <option value="2024-05">Mayo 2024</option>
                                        <option value="2024-06">Junio 2024</option>
                                        <option value="2024-07">Julio 2024</option>
                                        <option value="2024-08">Agosto 2024</option>
                                        <option value="2024-09">Septiembre 2024</option>
                                        <option value="2024-10">Octubre 2024</option>
                                        <option value="2024-11">Noviembre 2024</option>
                                        <option value="2024-12">Diciembre 2024</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones (Opcional)</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" 
                                          rows="3" placeholder="Notas adicionales sobre el proceso..."></textarea>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Nota:</strong> Este proceso realizará una solicitud oficial al SAT. 
                                Asegúrese de contar con la autorización del cliente.
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-redo"></i> Limpiar Formulario
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-play"></i> Iniciar Proceso SAT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Historial de procesos (futuro) -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-history"></i> Historial de Procesos (Próximamente)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5>Funcionalidad en desarrollo</h5>
                            <p class="text-muted">
                                Próximamente podrás ver el historial de todas las solicitudes realizadas al SAT
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.step-number {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
    font-weight: bold;
}

.card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ProyectoSAT\resources\views/proceso/index.blade.php ENDPATH**/ ?>