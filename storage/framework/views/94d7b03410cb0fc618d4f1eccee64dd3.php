<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAT Monitor - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #6f42c1, #5a32a3);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .badge-estado {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-positivo { background: #d1e7dd; color: #0f5132; }
        .badge-negativo { background: #f8d7da; color: #842029; }
        .badge-pendiente { background: #e2e3e5; color: #41464b; }
        .badge-error { background: #fff3cd; color: #664d03; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo e(route('dashboard')); ?>">
                📋 SAT Monitor
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?php echo e(route('clientes.create')); ?>">➕ Nuevo Cliente</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            ✅ <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            ❌ <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(session('info')): ?>
        <div class="alert alert-info alert-dismissible fade show">
            ℹ️ <?php echo e(session('info')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\ProyectoSAT\resources\views/layout.blade.php ENDPATH**/ ?>