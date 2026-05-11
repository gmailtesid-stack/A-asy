<?php $__env->startSection('title', 'Stock Opname - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Stock Opname (Rekonsiliasi)</h2>
            <p class="text-muted">Kelola penyesuaian stok fisik vs sistem dengan alur persetujuan.</p>
        </div>
        <a href="<?php echo e(route('stock_opnames.create')); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Mulai Opname Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small uppercase">
                        <tr>
                            <th class="ps-4 py-3">No. Opname / Tanggal</th>
                            <th class="py-3">Gudang</th>
                            <th class="py-3">Inisiator</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $opnames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-primary"><?php echo e($op->opname_number); ?></div>
                                <div class="text-muted small"><?php echo e($op->created_at->format('d/m/Y H:i')); ?></div>
                            </td>
                            <td><?php echo e($op->warehouse->name); ?></td>
                            <td><?php echo e($op->user->name); ?></td>
                            <td class="text-center">
                                <?php
                                    $colors = ['pending' => 'warning', 'approved' => 'success', 'cancelled' => 'danger'];
                                ?>
                                <span class="badge bg-<?php echo e($colors[$op->status] ?? 'secondary'); ?> rounded-pill px-3">
                                    <?php echo e(strtoupper($op->status)); ?>

                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <?php if($op->status == 'pending'): ?>
                                <a href="<?php echo e(route('stock_opnames.edit', $op)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Input Fisik</a>
                                <?php else: ?>
                                <button class="btn btn-sm btn-light border rounded-pill px-3">Detail</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\stock_opnames\index.blade.php ENDPATH**/ ?>