<?php $__env->startSection('title', 'Manajemen Gudang & Lokasi - E-ASY WMS'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Gudang & Lokasi</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Gudang & Lokasi</h2>
            <p class="text-muted">Kelola gudang fisik dan lokasi penyimpanan (Rak/Bin) di setiap outlet.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Gudang
        </button>
    </div>

    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-primary bg-opacity-10 text-primary overflow-hidden" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <?php if($warehouse->photo): ?>
                            <img src="<?php echo e($warehouse->photo); ?>" alt="Warehouse Photo" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                            <i class="bi bi-building fs-4"></i>
                            <?php endif; ?>
                        </div>
                        <span class="badge bg-light text-primary border rounded-pill"><?php echo e($warehouse->outlet->name); ?></span>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo e($warehouse->name); ?></h5>
                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i> <?php echo e($warehouse->address ?? 'Alamat tidak diatur'); ?></p>
                    
                    <div class="themed-summary rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-2">
                            <span>Total Lokasi (Rak/Bin)</span>
                            <span class="fw-bold text-main"><?php echo e($warehouse->locations->count()); ?></span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Total Stok SKU</span>
                            <span class="fw-bold text-main"><?php echo e($warehouse->inventories->count()); ?></span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('warehouses.show', $warehouse)); ?>" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill">
                            <i class="bi bi-geo-fill me-1"></i> Detail Lokasi
                        </a>
                        <button class="btn btn-sm btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#editWarehouseModal<?php echo e($warehouse->id); ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="<?php echo e(route('warehouses.destroy', $warehouse)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-light text-danger rounded-pill" onclick="return confirm('Hapus gudang ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="modal fade" id="editWarehouseModal<?php echo e($warehouse->id); ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="fw-bold">Edit Gudang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?php echo e(route('warehouses.update', $warehouse)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Foto Gudang</label>
                                <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Cabang / Outlet</label>
                                <select name="outlet_id" class="form-select rounded-3" required>
                                    <option value="">Pilih Outlet...</option>
                                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($outlet->id); ?>" <?php echo e($warehouse->outlet_id == $outlet->id ? 'selected' : ''); ?>><?php echo e($outlet->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nama Gudang</label>
                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo e($warehouse->name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Alamat Gudang</label>
                                <textarea name="address" class="form-control rounded-3" rows="2"><?php echo e($warehouse->address); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i> Latitude</label>
                                    <input type="text" name="latitude" class="form-control rounded-3" value="<?php echo e($warehouse->latitude); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i> Longitude</label>
                                    <input type="text" name="longitude" class="form-control rounded-3" value="<?php echo e($warehouse->longitude); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 p-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5">
                <div class="card-body text-center">
                    <img src="https://illustrations.popsy.co/blue/delivery-warehouse.svg" alt="" style="width: 200px;" class="mb-4">
                    <h5 class="fw-bold">Belum Ada Gudang</h5>
                    <p class="text-muted">Silakan tambahkan gudang pertama Anda untuk mulai mengelola stok.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="addWarehouseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Gudang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('warehouses.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Foto Gudang (Opsional)</label>
                        <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Cabang / Outlet</label>
                        <select name="outlet_id" class="form-select rounded-3" required>
                            <option value="">Pilih Outlet...</option>
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($outlet->id); ?>"><?php echo e($outlet->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Gudang</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Gudang Utama A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alamat Gudang (Opsional)</label>
                        <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Alamat lengkap gudang..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i> Latitude</label>
                            <input type="text" name="latitude" class="form-control rounded-3" placeholder="-6.200000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i> Longitude</label>
                            <input type="text" name="longitude" class="form-control rounded-3" placeholder="106.816666">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .themed-summary { background: rgba(99, 102, 241, 0.05); border: 1px solid var(--border-color); }
    .text-main { color: var(--text-main); }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\warehouses\index.blade.php ENDPATH**/ ?>