

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2 class="text-primary"><?php echo e($estadisticas['total']); ?></h2>
            <p class="text-muted mb-0">Total Clientes</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2 class="text-success"><?php echo e($estadisticas['positivos']); ?></h2>
            <p class="text-muted mb-0">Positivos</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2 class="text-danger"><?php echo e($estadisticas['negativos']); ?></h2>
            <p class="text-muted mb-0">Negativos</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2 class="text-secondary"><?php echo e($estadisticas['pendientes']); ?></h2>
            <p class="text-muted mb-0">Pendientes</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📋 Lista de Clientes</h5>
        <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-primary btn-sm">
            ➕ Agregar Cliente
        </a>
    </div>
    <div class="card-body">
        <?php if($clientes->isEmpty()): ?>
        <div class="text-center py-5">
            <p class="text-muted">No hay clientes registrados</p>
            <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-primary">
                Agregar primer cliente
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Estado</th>
                        <th>Última Consulta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <strong><?php echo e($cliente->nombre); ?></strong>
                            <?php if($cliente->email): ?>
                            <br><small class="text-muted"><?php echo e($cliente->email); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo e($cliente->rfc); ?></code></td>
                        <td>
                            <span class="badge-estado badge-<?php echo e($cliente->estado); ?>">
                                <?php echo e($cliente->estado_icono); ?> <?php echo e($cliente->estado); ?>

                            </span>
                        </td>
                        <td>
                            <?php echo e($cliente->ultima_consulta ? $cliente->ultima_consulta->format('d/m/Y H:i') : 'Nunca'); ?>

                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('clientes.show', $cliente)); ?>" class="btn btn-outline-info">
                                    👁️ Ver
                                </a>
                                <button onclick="consultarCliente(<?php echo e($cliente->id); ?>)" 
                                        class="btn btn-outline-primary">
                                    🔍 Consultar
                                </button>
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

<form id="consultarForm" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('POST'); ?>
</form>

<script>
function consultarCliente(id) {
    if (confirm('¿Consultar este cliente ahora?')) {
        const form = document.getElementById('consultarForm');
        form.action = `/clientes/${id}/consultar`;
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ProyectoSAT\resources\views/dashboard.blade.php ENDPATH**/ ?>