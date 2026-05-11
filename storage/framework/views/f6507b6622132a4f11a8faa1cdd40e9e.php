<?php $__env->startSection('title', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Pengguna</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 fw-bold text-dark">Daftar Pengguna</h2>
    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Outlet</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar shadow-sm overflow-hidden" style="width:36px; height:36px; background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700;">
                                    <?php if($user->photo): ?>
                                    <img src="<?php echo e($user->photo); ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                    <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="fw-bold text-dark"><?php echo e($user->name); ?></div>
                            </div>
                        </td>
                        <td><?php echo e($user->email); ?></td>
                        <td>
                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="role-badge role-<?php echo e($role->slug); ?>">
                                    <?php echo e($role->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td><?php echo e($user->outlet->name ?? 'Semua Outlet (Pusat)'); ?></td>
                        <td class="text-center">
                            <?php if($user->is_active): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Non-aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <?php if($user->id !== auth()->id()): ?>
                                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Belum ada data pengguna.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($users->hasPages()): ?>
    <div class="card-footer bg-white border-top-0">
        <?php echo e($users->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\users\index.blade.php ENDPATH**/ ?>