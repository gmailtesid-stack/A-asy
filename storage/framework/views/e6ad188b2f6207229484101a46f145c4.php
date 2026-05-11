<?php $__env->startSection('title', 'Lokasi Gudang - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="<?php echo e(route('warehouses.index')); ?>" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Gudang
        </a>
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div>
                <h2 class="fw-bold mb-1">Lokasi di <?php echo e($warehouse->name); ?></h2>
                <p class="text-muted">Kelola Rak, Bin, atau Section spesifik di gudang ini.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                <i class="bi bi-plus-lg me-2"></i> Tambah Lokasi
            </button>
        </div>
    </div>

    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="brand-logo bg-light text-primary mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-geo-alt-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo e($location->name); ?></h5>
                    <div class="mb-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill small"><?php echo e(strtoupper($location->type)); ?></span>
                    </div>
                    <p class="text-muted small mb-3">ID: <code>LOC-<?php echo e($location->id); ?></code></p>
                    
                    <form action="<?php echo e(route('locations.destroy', $location)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-link text-danger text-decoration-none" onclick="return confirm('Hapus lokasi ini?')">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5 text-center text-muted">
                Belum ada lokasi spesifik di gudang ini.
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('locations.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="warehouse_id" value="<?php echo e($warehouse->id); ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lokasi (Rak/Bin)</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Rak A-01, Bin 12" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Tipe Lokasi</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="rack">Rak (Rack)</option>
                            <option value="bin">Kotak (Bin)</option>
                            <option value="zone">Area (Zone)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\master\locations\index.blade.php ENDPATH**/ ?>