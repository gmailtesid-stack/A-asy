<?php $__env->startSection('title', 'Reorder Alerts'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>🚨 Reorder Alerts</h1>
    <p class="page-subtitle">Sistem mendeteksi produk yang stoknya telah menyentuh batas Reorder Point. Segera buat Purchase Order.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Produk Kritis (Butuh Restock)</h3>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Lokasi Cabang</th>
                    <th>Stok Saat Ini</th>
                    <th>Reorder Point (Titik Aman)</th>
                    <th>Defisit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="background: rgba(var(--danger-rgb), 0.05);">
                    <td><strong><?php echo e($alert['product_name']); ?></strong></td>
                    <td><?php echo e($alert['outlet']); ?></td>
                    <td><strong style="color:var(--danger)"><?php echo e($alert['current_stock']); ?></strong></td>
                    <td><?php echo e($alert['reorder_point']); ?></td>
                    <td>-<?php echo e($alert['deficit']); ?> unit</td>
                    <td>
                        <a href="<?php echo e(route('inbound.create')); ?>?product_id=<?php echo e($alert['product_id']); ?>" class="btn btn-sm btn-primary">📝 Buat Draf PO</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">Semua stok berada di atas Reorder Point. Aman!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\reports\reorder_alerts.blade.php ENDPATH**/ ?>