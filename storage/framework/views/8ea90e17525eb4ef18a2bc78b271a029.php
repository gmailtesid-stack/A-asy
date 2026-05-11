<?php $__env->startSection('title', 'Input Fisik Opname - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Input Data Fisik: <?php echo e($stockOpname->opname_number); ?></h2>
            <p class="text-muted">Gudang: <?php echo e($stockOpname->warehouse->name); ?></p>
        </div>
        <div class="d-flex gap-2">
            <form action="<?php echo e(route('stock_opnames.approve', $stockOpname)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-check2-all me-2"></i> Setujui & Sesuaikan Stok
                </button>
            </form>
        </div>
    </div>

    <form action="<?php echo e(route('stock_opnames.update', $stockOpname)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small uppercase">
                            <tr>
                                <th class="ps-4 py-3">Produk</th>
                                <th class="py-3 text-center"><?php echo e(!$stockOpname->is_blind ? 'Stok Sistem' : 'Status'); ?></th>
                                <th class="py-3 text-center" style="width: 200px;">Stok Fisik</th>
                                <?php if(!$stockOpname->is_blind): ?>
                                <th class="py-3 text-center">Selisih</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $stockOpname->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo e($item->product->name); ?></div>
                                    <code class="small text-muted"><?php echo e($item->product->sku); ?></code>
                                    <input type="hidden" name="items[<?php echo e($index); ?>][id]" value="<?php echo e($item->id); ?>">
                                </td>
                                <td class="text-center">
                                    <?php if(!$stockOpname->is_blind): ?>
                                        <span class="fw-bold text-muted"><?php echo e($item->recorded_quantity); ?></span>
                                    <?php else: ?>
                                        <?php if($item->verification_status === 'pending'): ?>
                                            <span class="badge bg-secondary rounded-pill">Belum Dihitung</span>
                                        <?php elseif($item->verification_status === 'recount'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill">Hitung Ulang!</span>
                                        <?php else: ?>
                                            <span class="badge bg-success rounded-pill">Selesai</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <input type="number" name="items[<?php echo e($index); ?>][physical_quantity]" 
                                           class="form-control text-center rounded-pill border-light shadow-sm" 
                                           placeholder="Scan/Input Jumlah..."
                                           value="">
                                </td>
                                <?php if(!$stockOpname->is_blind): ?>
                                <td class="text-center fw-bold <?php echo e($item->adjustment_quantity < 0 ? 'text-danger' : ($item->adjustment_quantity > 0 ? 'text-success' : '')); ?>">
                                    <?php echo e($item->adjustment_quantity > 0 ? '+' : ''); ?><?php echo e($item->adjustment_quantity); ?>

                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Simpan Perubahan Fisik</button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\stock_opnames\edit.blade.php ENDPATH**/ ?>