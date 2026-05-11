<?php $__env->startSection('title', 'Manajemen Inbound - E-ASY WMS'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Inbound</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1 fw-800 text-dark">Inbound (Penerimaan Barang)</h2>
            <p class="text-muted small mb-0">Kelola siklus pengadaan barang mulai dari Purchase Order hingga verifikasi stok masuk.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light border shadow-sm px-3"><i class="bi bi-printer me-2"></i> Cetak Laporan</button>
            <a href="<?php echo e(route('inbound.create')); ?>" class="btn btn-primary shadow-sm px-4 fw-bold">
                <i class="bi bi-plus-lg me-2"></i> Buat PO Baru
            </a>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3">No. PO / Tanggal</th>
                            <th class="py-3">Supplier</th>
                            <th class="py-3">Gudang Tujuan</th>
                            <th class="py-3 text-end">Total Nilai</th>
                            <th class="py-3 text-center">Status Inbound</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php $__empty_1 = true; $__currentLoopData = $pos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white border-bottom">
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-primary" style="font-size: 0.95rem;"><?php echo e($po->po_number); ?></div>
                                <div class="text-muted small"><?php echo e($po->created_at->format('d/m/Y H:i')); ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0"><?php echo e($po->supplier->name); ?></div>
                                <span class="text-muted small">Vendor ID: #VND-<?php echo e(str_pad($po->supplier->id, 3, '0', STR_PAD_LEFT)); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border border-secondary-subtle px-3 py-2 rounded-pill">
                                    <i class="bi bi-building me-1"></i> <?php echo e($po->warehouse->name); ?>

                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">Rp <?php echo e(number_format($po->total_amount, 0, ',', '.')); ?></td>
                            <td class="text-center">
                                <?php
                                    $statusConfig = [
                                        'pending' => ['color' => 'warning', 'icon' => 'bi-clock'],
                                        'confirmed' => ['color' => 'primary', 'icon' => 'bi-check-all'],
                                        'received' => ['color' => 'success', 'icon' => 'bi-check-circle'],
                                        'cancelled' => ['color' => 'danger', 'icon' => 'bi-x-circle']
                                    ][$po->status] ?? ['color' => 'secondary', 'icon' => 'bi-circle'];
                                ?>
                                <span class="badge bg-<?php echo e($statusConfig['color']); ?>-subtle text-<?php echo e($statusConfig['color']); ?> border border-<?php echo e($statusConfig['color']); ?>-subtle rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi <?php echo e($statusConfig['icon']); ?> me-1"></i> <?php echo e(strtoupper($po->status)); ?>

                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light border px-3 rounded-pill">Detail</button>
                                    
                                    <?php if($po->status == 'pending' && auth()->user()->hasPermission('approve-po')): ?>
                                    <form action="<?php echo e(route('inbound.approve', $po)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-info rounded-pill px-3 shadow-sm text-white fw-bold border-0">Approve PO</button>
                                    </form>
                                    <?php endif; ?>

                                    <?php if($po->status == 'confirmed' && auth()->user()->hasPermission('create-grn')): ?>
                                    <a href="<?php echo e(route('inbound.receive', $po)); ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold border-0">Receive Items</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-box-arrow-in-down fs-1 d-block mb-3 opacity-25 text-primary"></i>
                                    <h5 class="text-muted fw-bold">Belum ada Purchase Order</h5>
                                    <p class="text-muted small">Mulai siklus pengadaan barang dengan membuat PO pertama Anda.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($pos->hasPages()): ?>
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">Menampilkan <?php echo e($pos->firstItem()); ?> - <?php echo e($pos->lastItem()); ?> dari <?php echo e($pos->total()); ?> PO</span>
            <?php echo e($pos->links('pagination::bootstrap-5')); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .table>tbody>tr>td { vertical-align: middle; padding-top: 15px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
    .transition-all:hover { background-color: #f8fafc !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\inbound\index.blade.php ENDPATH**/ ?>