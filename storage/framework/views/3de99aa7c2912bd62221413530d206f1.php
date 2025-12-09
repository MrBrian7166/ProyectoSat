

<?php $__env->startSection('title', 'Clientes SAT'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="fas fa-users"></i> Clientes Registrados
    </h2>
    <div>
        <!-- Botón para ir al proceso SAT -->
        <a href="<?php echo e(route('proceso.index')); ?>" class="btn btn-success me-2">
            <i class="fas fa-file-contract"></i> Obtencion de Opinion
        </a>
        
        <!-- Botón para nuevo cliente -->
        <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Cliente
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if($clientes->isEmpty()): ?>
            <div class="text-center py-5">
                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                <h4>No hay clientes registrados</h4>
                <p class="text-muted">Comienza agregando tu primer cliente.</p>
                <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-primary mt-2">
                    <i class="fas fa-user-plus"></i> Registrar Primer Cliente
                </a>
            </div>
        <?php else: ?>
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
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($cliente->id); ?></td>
                            <td><?php echo e($cliente->nombre); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo e($cliente->rfc); ?></span>
                            </td>
                            <td>
                                <?php if($cliente->certificado_path): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-file-certificate"></i> Cargado
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">No cargado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($cliente->llave_path): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-key"></i> Cargada
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">No cargada</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($cliente->created_at->format('d/m/Y')); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo e(route('clientes.show', $cliente)); ?>" 
                                    class="btn btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('clientes.edit', $cliente)); ?>" 
                                    class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('clientes.destroy', $cliente)); ?>" 
                                        method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('¿Estás seguro de eliminar este cliente?')" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ProyectoSAT\resources\views/clientes/index.blade.php ENDPATH**/ ?>