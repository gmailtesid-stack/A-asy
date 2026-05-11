<?php $__env->startSection('title', 'Pusat Data SKU - E-ASY WMS'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Master Data</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Pusat Data SKU</h2>
            <p class="text-muted">Kelola informasi produk, merek, kategori, dan pemasok Anda.</p>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?php echo e($stats['products']); ?> Items</span>
                    </div>
                    <h5 class="fw-bold mb-1">Produk (SKU)</h5>
                    <p class="text-muted small mb-4">Daftar item unik dengan kode SKU dan harga.</p>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary w-100 rounded-pill">
                        Kelola Produk <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-success bg-opacity-10 text-success">
                            <i class="bi bi-tags fs-4"></i>
                        </div>
                        <span class="badge bg-success rounded-pill"><?php echo e($stats['brands']); ?> Brand</span>
                    </div>
                    <h5 class="fw-bold mb-1">Merek (Brand)</h5>
                    <p class="text-muted small mb-4">Manajemen merek atau produsen produk.</p>
                    <a href="<?php echo e(route('brands.index')); ?>" class="btn btn-outline-success w-100 rounded-pill">
                        Kelola Merek <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-grid fs-4"></i>
                        </div>
                        <span class="badge bg-warning rounded-pill"><?php echo e($stats['categories']); ?> Kategori</span>
                    </div>
                    <h5 class="fw-bold mb-1">Kategori</h5>
                    <p class="text-muted small mb-4">Pengelompokan produk berdasarkan jenis.</p>
                    <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-outline-warning w-100 rounded-pill">
                        Kelola Kategori <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-info bg-opacity-10 text-info">
                            <i class="bi bi-truck fs-4"></i>
                        </div>
                        <span class="badge bg-info rounded-pill"><?php echo e($stats['suppliers']); ?> Supplier</span>
                    </div>
                    <h5 class="fw-bold mb-1">Pemasok (Supplier)</h5>
                    <p class="text-muted small mb-4">Data vendor dan supplier untuk pengadaan barang.</p>
                    <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-outline-info w-100 rounded-pill">
                        Kelola Supplier <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Baru Saja Ditambahkan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th>SKU</th>
                                    <th>Brand</th>
                                    <th>Kategori</th>
                                    <th>Harga Jual</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $recentProducts = \App\Models\Product::with(['brand', 'category'])->latest()->take(5)->get();
                                ?>
                                <?php $__empty_1 = true; $__currentLoopData = $recentProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?php echo e($product->image_url); ?>" alt="" class="rounded-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <span class="fw-semibold"><?php echo e($product->name); ?></span>
                                        </div>
                                    </td>
                                    <td><code><?php echo e($product->sku); ?></code></td>
                                    <td><?php echo e($product->brand?->name ?? '-'); ?></td>
                                    <td><?php echo e($product->category?->name ?? '-'); ?></td>
                                    <td>Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></td>
                                    <td class="text-end pe-4">
                                        <?php if(auth()->user()->hasPermission('manage-master-data')): ?>
                                        <a href="<?php echo e(route('products.edit', $product)); ?>" class="btn btn-sm btn-light rounded-pill">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        <?php else: ?>
                                        <button class="btn btn-sm btn-light rounded-pill">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data produk.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\master\index.blade.php ENDPATH**/ ?>