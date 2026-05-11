<?php $__env->startSection('title', 'Manajemen Brand - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Brand</h2>
            <p class="text-muted">Kelola merek produk Anda.</p>
        </div>
        <?php if(auth()->user()->hasPermission('manage-master-data')): ?>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Brand
        </button>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Brand</th>
                            <th>Deskripsi</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4 fw-bold">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="brand-logo bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px; border-radius: 8px;">
                                        <?php if($brand->photo): ?>
                                        <img src="<?php echo e($brand->photo); ?>" alt="Brand Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                        <i class="bi bi-tag text-muted"></i>
                                        <?php endif; ?>
                                    </div>
                                    <span><?php echo e($brand->name); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($brand->description ?? '-'); ?></td>
                            <td class="text-end pe-4">
                                <?php if(auth()->user()->hasPermission('manage-master-data')): ?>
                                <button class="btn btn-sm btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#editBrandModal<?php echo e($brand->id); ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?php echo e(route('brands.destroy', $brand)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-light text-danger rounded-pill" onclick="return confirm('Hapus brand ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="text-muted small">View Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        
                        <div class="modal fade" id="editBrandModal<?php echo e($brand->id); ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold">Edit Brand</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="<?php echo e(route('brands.update', $brand)); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Foto Brand</label>
                                                <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Nama Brand</label>
                                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo e($brand->name); ?>" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold small">Deskripsi</label>
                                                <textarea name="description" class="form-control rounded-3" rows="3"><?php echo e($brand->description); ?></textarea>
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
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">Belum ada data brand.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Brand Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('brands.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Foto Brand (Opsional)</label>
                        <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Brand</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Nike, Samsung" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Deskripsi singkat..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\master\brands\index.blade.php ENDPATH**/ ?>