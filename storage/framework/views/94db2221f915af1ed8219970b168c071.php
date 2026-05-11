<?php $__env->startSection('title', 'Update Stok'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('inventories.index')); ?>">Inventori</a></li>
    <li class="breadcrumb-item active">Update Stok</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold">Update Stok: <?php echo e($inventory->product->name); ?></h5>
                <small class="text-muted">Outlet: <?php echo e($inventory->outlet->name); ?></small>
            </div>
            <div class="card-body p-4">
                <div class="alert bg-primary-subtle text-primary border-0 d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <div class="small fw-600">Stok Saat Ini</div>
                        <div class="h4 mb-0 fw-bold"><?php echo e($inventory->quantity); ?> <?php echo e($inventory->product->unit); ?></div>
                    </div>
                </div>

                <form action="<?php echo e(route('inventories.update', $inventory)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-600">Jenis Perubahan</label>
                        <select name="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="in">Stok Masuk (Restock)</option>
                            <option value="out">Stok Keluar (Rusak/Expired)</option>
                            <option value="adjustment">Penyesuaian (Opname)</option>
                        </select>
                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Jumlah Perubahan</label>
                        <div class="input-group">
                            <input type="number" name="quantity_change" class="form-control <?php $__errorArgs = ['quantity_change'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Contoh: 50 atau -10">
                            <span class="input-group-text bg-light"><?php echo e($inventory->product->unit); ?></span>
                        </div>
                        <small class="text-muted">Gunakan angka positif untuk menambah, negatif untuk mengurangi.</small>
                        <?php $__errorArgs = ['quantity_change'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Keterangan / Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Alasan perubahan stok..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Simpan Perubahan</button>
                        <a href="<?php echo e(route('inventories.index')); ?>" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\inventories\edit.blade.php ENDPATH**/ ?>