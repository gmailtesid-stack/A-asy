<?php $__env->startSection('title', 'Dashboard — E-ASY POS'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Overview</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4 animate__animated animate__fadeIn">
    <div class="col-12">
        <div class="card border-0 overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
            <div class="card-body p-4 p-md-5 text-white position-relative">
                <div class="position-relative z-index-1">
                    <h2 class="fw-800 mb-2">Selamat Datang Kembali, <?php echo e(explode(' ', auth()->user()->name)[0]); ?>! 👋</h2>
                    <p class="opacity-75 mb-4">Pantau performa bisnis Anda hari ini secara real-time dari satu dashboard.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if(auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isSuperAdmin()): ?>
                            <a href="<?php echo e(route('pos.index')); ?>" class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-cart-plus-fill me-2"></i> Buka Kasir
                            </a>
                        <?php endif; ?>
                        <?php if(!auth()->user()->isCashier()): ?>
                            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">
                                <i class="bi bi-graph-up-arrow me-2"></i> Analisis Laporan
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="position-absolute" style="width:300px; height:300px; background:rgba(255,255,255,0.1); border-radius:50%; top:-100px; right:-100px;"></div>
                <div class="position-absolute" style="width:150px; height:150px; background:rgba(255,255,255,0.05); border-radius:50%; bottom:-50px; right:100px;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 h-100 shadow-sm hover-up">
            <div class="card-body p-4 text-center">
                <div class="mb-3 d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary">
                    <i class="bi bi-shop fs-3"></i>
                </div>
                <h5 class="fw-bold">Manajemen Outlet</h5>
                <p class="text-muted small">Kelola data cabang dan pengaturan outlet dengan mudah.</p>
                <a href="<?php echo e(route('outlets.index')); ?>" class="btn btn-sm btn-primary px-3 rounded-pill">Kelola</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 h-100 shadow-sm hover-up">
            <div class="card-body p-4 text-center">
                <div class="mb-3 d-inline-flex p-3 rounded-circle bg-success-subtle text-success">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
                <h5 class="fw-bold">Stok Produk</h5>
                <p class="text-muted small">Pantau ketersediaan barang dan update stok secara instan.</p>
                <a href="<?php echo e(route('products.index')); ?>" class="btn btn-sm btn-success px-3 rounded-pill">Cek Stok</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 h-100 shadow-sm hover-up">
            <div class="card-body p-4 text-center">
                <div class="mb-3 d-inline-flex p-3 rounded-circle bg-info-subtle text-info">
                    <i class="bi bi-people fs-3"></i>
                </div>
                <h5 class="fw-bold">Tim & Kasir</h5>
                <p class="text-muted small">Atur hak akses staf dan performa masing-masing kasir.</p>
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-sm btn-info px-3 rounded-pill text-white">Atur Tim</a>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .hover-up:hover { transform: translateY(-8px); transition: all 0.3s ease; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\dashboard.blade.php ENDPATH**/ ?>