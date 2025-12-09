

<?php $__env->startSection('title', 'Detalles del Cliente'); ?>

<?php $__env->startSection('content'); ?>
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
                        <h5><?php echo e($cliente->nombre); ?></h5>
                        <span class="badge bg-secondary">ID: <?php echo e($cliente->id); ?></span>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-id-card text-primary"></i> Información General</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td><?php echo e($cliente->nombre); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>RFC:</strong></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($cliente->rfc); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Registrado:</strong></td>
                                        <td><?php echo e($cliente->created_at->format('d/m/Y H:i')); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Actualizado:</strong></td>
                                        <td><?php echo e($cliente->updated_at->format('d/m/Y H:i')); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-file-certificate text-success"></i> Archivos</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Certificado:</strong></td>
                                        <td>
                                            <?php if($cliente->certificado_path): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Cargado
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo e(basename($cliente->certificado_path)); ?>

                                                </small>
                                            <?php else: ?>
                                                <span class="badge bg-danger">No cargado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Llave privada:</strong></td>
                                        <td>
                                            <?php if($cliente->llave_path): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Cargada
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo e(basename($cliente->llave_path)); ?>

                                                </small>
                                            <?php else: ?>
                                                <span class="badge bg-danger">No cargada</span>
                                            <?php endif; ?>
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
                    <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <a href="<?php echo e(route('clientes.edit', $cliente)); ?>" class="btn btn-primary me-md-2">
                        <i class="fas fa-edit"></i> Editar Cliente
                    </a>
                    <form action="<?php echo e(route('clientes.destroy', $cliente)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ProyectoSAT\resources\views/clientes/show.blade.php ENDPATH**/ ?>