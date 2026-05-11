<?php $__env->startSection('title', 'Log Pergerakan Stok - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Movement Log (Pergerakan Stok)</h2>
            <p class="text-muted">Riwayat lengkap mutasi barang, penyesuaian, dan transaksi logistik.</p>
        </div>
        <a href="<?php echo e(route('inventories.index')); ?>" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-box-seam me-2"></i> Lihat Stok Saat Ini
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal & Waktu</th>
                            <th>Produk</th>
                            <th>Gudang</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Perubahan</th>
                            <th class="text-center">Stok Akhir</th>
                            <th>Referensi / Catatan</th>
                            <th class="text-end pe-4">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small"><?php echo e($log->created_at->format('d/m/Y')); ?></div>
                                <div class="text-muted small"><?php echo e($log->created_at->format('H:i:s')); ?></div>
                            </td>
                            <td>
                                <div class="fw-bold small"><?php echo e($log->inventory->product->name); ?></div>
                                <code class="small text-muted"><?php echo e($log->inventory->product->sku); ?></code>
                            </td>
                            <td><span class="badge bg-light text-dark border small"><?php echo e($log->inventory->warehouse->name); ?></span></td>
                            <td class="text-center">
                                <?php
                                    $typeClass = [
                                        'in' => 'bg-success',
                                        'out' => 'bg-primary',
                                        'adjustment' => 'bg-warning',
                                    ][$log->type] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?php echo e($typeClass); ?> rounded-pill small"><?php echo e(strtoupper($log->type)); ?></span>
                            </td>
                            <td class="text-center fw-bold <?php echo e($log->quantity_change > 0 ? 'text-success' : 'text-danger'); ?>">
                                <?php echo e($log->quantity_change > 0 ? '+' : ''); ?><?php echo e($log->quantity_change); ?>

                            </td>
                            <td class="text-center fw-bold"><?php echo e($log->quantity_after); ?></td>
                            <td>
                                <div class="small"><?php echo e($log->reference ?? '-'); ?></div>
                                <div class="text-muted small italic"><?php echo e($log->notes); ?></div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="fw-bold small"><?php echo e($log->user->name); ?></div>
                                <div class="text-muted small">ID: <?php echo e($log->user_id); ?></div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat pergerakan stok.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($logs->hasPages()): ?>
        <div class="card-footer bg-white py-3 border-top border-light">
            <?php echo e($logs->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\inventories\logs.blade.php ENDPATH**/ ?>