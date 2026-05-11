<?php $__env->startSection('title', 'Mulai Stock Opname - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Mulai Stock Opname</h2>
        <p class="text-muted">Pilih gudang yang akan dilakukan penghitungan stok fisik.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="<?php echo e(route('stock_opnames.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Gudang</label>
                            <select name="warehouse_id" class="form-select rounded-pill p-3 border-light shadow-sm" required>
                                <option value="">Pilih Gudang...</option>
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($w->id); ?>"><?php echo e($w->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('stock_opnames.index')); ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Inisialisasi Opname</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\stock_opnames\create.blade.php ENDPATH**/ ?>