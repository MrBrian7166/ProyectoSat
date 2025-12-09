

<?php $__env->startSection('title', 'Pruebas SAT'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-vial"></i> Panel de Pruebas SAT
            </h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Este panel es solo para desarrollo y pruebas. Usa estas herramientas para depurar el proceso SAT.
            </div>
            
            <div class="row">
                <!-- Pruebas de conexión -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-wifi"></i> Pruebas de Conexión</h5>
                        </div>
                        <div class="card-body">
                            <p>Verifica que puedas conectar con el portal del SAT.</p>
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('sat-test.conexion')); ?>" class="btn btn-info" target="_blank">
                                    <i class="fas fa-plug"></i> Probar Conexión Básica
                                </a>
                                <a href="<?php echo e(route('ver-sat')); ?>" class="btn btn-secondary" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Ver Portal SAT
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Análisis de formulario -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-search"></i> Análisis de Formulario</h5>
                        </div>
                        <div class="card-body">
                            <p>Analiza la estructura del formulario del SAT.</p>
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('sat-test.analizar-json')); ?>" class="btn btn-warning" target="_blank">
                                    <i class="fas fa-code"></i> Analizar (JSON)
                                </a>
                                <a href="<?php echo e(route('sat-test.analizar-html')); ?>" class="btn btn-warning">
                                    <i class="fas fa-eye"></i> Analizar (HTML)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pruebas con clientes -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Pruebas con Clientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>RFC</th>
                                    <th>Certificado</th>
                                    <th>Llave</th>
                                    <th>Acciones de Prueba</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $clientes = \App\Models\Cliente::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($cliente->id); ?></td>
                                    <td><?php echo e($cliente->nombre); ?></td>
                                    <td><code><?php echo e($cliente->rfc); ?></code></td>
                                    <td>
                                        <?php if($cliente->certificado_path): ?>
                                            <span class="badge bg-success">✓</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">✗</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($cliente->llave_path): ?>
                                            <span class="badge bg-success">✓</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">✗</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo e(route('sat-test.probar-cliente', $cliente->id)); ?>" 
                                               class="btn btn-info" target="_blank" title="Probar cliente">
                                                <i class="fas fa-vial"></i>
                                            </a>
                                            <a href="<?php echo e(route('sat-test.probar-curl', $cliente->id)); ?>" 
                                               class="btn btn-warning" target="_blank" title="Probar con cURL">
                                                <i class="fas fa-terminal"></i>
                                            </a>
                                            <a href="<?php echo e(route('sat-test.ejecutar', $cliente->id)); ?>" 
                                               class="btn btn-success" target="_blank" title="Ejecutar proceso completo">
                                                <i class="fas fa-play"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Herramientas de depuración -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-bug"></i> Herramientas de Depuración</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Archivos de depuración:</h6>
                            <p>Revisa los archivos generados durante las pruebas.</p>
                            <a href="<?php echo e(route('sat-test.debug')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-folder-open"></i> Ver Archivos de Debug
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h6>Regresar a proceso normal:</h6>
                            <p>Usa la interfaz normal del sistema.</p>
                            <a href="<?php echo e(route('proceso.index')); ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Ir a Proceso SAT
                            </a>
                            <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-users"></i> Ir a Clientes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ProyectoSAT\resources\views/sat-test/index.blade.php ENDPATH**/ ?>