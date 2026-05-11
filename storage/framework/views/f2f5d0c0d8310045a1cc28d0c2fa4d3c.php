<?php $__env->startSection('title', 'Analisis Dead Stock'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>🧟 Analisis Dead Stock</h1>
    <p class="page-subtitle">Daftar produk yang tidak mengalami penjualan dalam <?php echo e($days); ?> hari terakhir. Evaluasi untuk diskon cuci gudang.</p>
</div>

<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Barang Tidak Bergerak</h3>
        <form method="GET" style="display:flex; gap:1rem;">
            <select name="days" class="form-control" onchange="this.form.submit()">
                <option value="30" <?php if($days == 30): echo 'selected'; endif; ?>>30 Hari</option>
                <option value="60" <?php if($days == 60): echo 'selected'; endif; ?>>60 Hari</option>
                <option value="90" <?php if($days == 90): echo 'selected'; endif; ?>>90 Hari</option>
                <option value="180" <?php if($days == 180): echo 'selected'; endif; ?>>180 Hari</option>
            </select>
        </form>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>SKU / Produk</th>
                    <th>Kategori</th>
                    <th>Sisa Stok Fisik</th>
                    <th>Estimasi Nilai Stok (Tertahan)</th>
                    <th>Terakhir Ada Pergerakan</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($item['product_name']); ?></strong></td>
                    <td><span class="badge badge-secondary"><?php echo e($item['category']); ?></span></td>
                    <td><span class="badge badge-warning"><?php echo e($item['quantity']); ?></span></td>
                    <td style="color:var(--danger)">Rp <?php echo e(number_format($item['stock_value'], 0, ',', '.')); ?></td>
                    <td><?php echo e($item['last_updated']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center">Luar biasa! Tidak ada dead stock terdeteksi dalam periode ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\reports\dead_stock.blade.php ENDPATH**/ ?>