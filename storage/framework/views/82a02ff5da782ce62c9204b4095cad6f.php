<?php $__env->startSection('title', 'Tambah SKU Katalog - E-ASY OMS'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('products.index')); ?>">Katalog</a></li>
    <li class="breadcrumb-item active">Tambah SKU</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <form action="<?php echo e(route('products.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1 fw-800 text-dark">Tambah Produk Baru</h2>
                <p class="text-muted small mb-0">Definisikan SKU baru untuk distribusi multikanal.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('products.index')); ?>" class="btn btn-light border px-4 rounded-3">Batal</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold rounded-3">Simpan SKU</button>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="font-size: 1rem;">Informasi Dasar</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Nama Produk</label>
                                <input type="text" name="name" class="form-control form-control-lg bg-light border-0 rounded-3 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>" required placeholder="Masukkan nama produk lengkap...">
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Internal SKU (ISKU)</label>
                                <input type="text" name="sku" class="form-control bg-light border-0 rounded-3 <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('sku')); ?>" required placeholder="Contoh: PROD-001">
                                <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Channel SKU (CSKU)</label>
                                <input type="text" name="csku" class="form-control bg-light border-0 rounded-3 <?php $__errorArgs = ['csku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('csku')); ?>" placeholder="Opsional (Default = ISKU)">
                                <?php $__errorArgs = ['csku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Deskripsi Produk</label>
                                <textarea name="description" class="form-control bg-light border-0 rounded-3" rows="4" placeholder="Jelaskan detail produk, spesifikasi, atau keunggulan..."><?php echo e(old('description')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="font-size: 1rem;">Harga & Satuan</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Harga Beli</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">Rp</span>
                                    <input type="number" name="cost_price" class="form-control bg-light border-0 <?php $__errorArgs = ['cost_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('cost_price')); ?>" required>
                                </div>
                                <?php $__errorArgs = ['cost_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary-subtle text-primary border-0">Rp</span>
                                    <input type="number" name="price" class="form-control bg-light border-0 <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('price')); ?>" required>
                                </div>
                                <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Satuan (Unit)</label>
                                <input type="text" name="unit" class="form-control bg-light border-0 <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('unit', 'pcs')); ?>" required placeholder="pcs, box, kg">
                                <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="font-size: 1rem;">Media Produk</h5>
                        <div class="border-2 border-dashed border-secondary border-opacity-25 rounded-4 p-5 text-center bg-light">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary opacity-50"></i>
                            <p class="mt-3 mb-0 small text-muted">Klik untuk unggah atau seret gambar ke sini</p>
                            <input type="file" name="image" class="form-control mt-3 <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/*">
                            <small class="text-muted d-block mt-2">Maksimal 2MB (JPG, PNG, WebP).</small>
                            <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                
                <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0" style="font-size: 1rem;">Status Katalog</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Lifecycle Status</label>
                            <select name="status" class="form-select border-0 bg-light rounded-3 fw-bold">
                                <option value="live" class="text-success">● LIVE</option>
                                <option value="draft" selected class="text-secondary">● DRAFT</option>
                                <option value="under_review" class="text-warning">● UNDER REVIEW</option>
                                <option value="failed" class="text-danger">● FAILED</option>
                            </select>
                            <small class="text-muted mt-2 d-block">Hanya status <strong>Live</strong> yang dapat dijual di POS.</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tipe Produk</label>
                            <select name="type" class="form-select border-0 bg-light rounded-3">
                                <option value="simple" selected>Simple Product</option>
                                <option value="variant">Variant Product</option>
                                <option value="kit">Product Kit / Bundle</option>
                            </select>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="font-size: 1rem;">Organisasi</h5>
                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold text-uppercase">Kategori Utama</label>
                            <select name="category_id" class="form-select border-0 bg-light rounded-3 <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="" selected disabled>Pilih Kategori</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-4 p-3 small">
                    <i class="bi bi-info-circle-fill me-2"></i> 
                    Menambahkan SKU baru akan secara otomatis mendaftarkannya pada lokasi stok gudang utama sebagai status "Pending Stock".
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .border-dashed { border-style: dashed !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\products\create.blade.php ENDPATH**/ ?>