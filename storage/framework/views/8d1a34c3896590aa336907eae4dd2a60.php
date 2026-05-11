<?php $__env->startSection('title', 'Manajemen Outlet'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Outlet</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 fw-bold text-dark">Daftar Outlet / Cabang</h2>
    <a href="<?php echo e(route('outlets.create')); ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Outlet
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Outlet</th>
                        <th>Kode</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="outlet-logo bg-light shadow-sm d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px; border-radius: 8px;">
                                    <?php if($outlet->photo): ?>
                                    <img src="<?php echo e($outlet->photo); ?>" alt="Outlet Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                    <i class="bi bi-shop text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="fw-bold text-dark"><?php echo e($outlet->name); ?></div>
                            </div>
                        </td>
                        <td><span class="badge bg-dark"><?php echo e($outlet->code); ?></span></td>
                        <td><?php echo e($outlet->phone ?? '-'); ?></td>
                        <td class="text-truncate" style="max-width: 250px;"><?php echo e($outlet->address); ?></td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="<?php echo e(route('outlets.edit', $outlet)); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?php echo e(route('outlets.destroy', $outlet)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus outlet ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            Belum ada data outlet.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\outlets\index.blade.php ENDPATH**/ ?>