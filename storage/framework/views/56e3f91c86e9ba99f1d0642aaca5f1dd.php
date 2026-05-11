<?php $__env->startSection('title', 'Manajemen Outbound - E-ASY WMS'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Outbound</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Outbound (Barang Keluar)</h2>
            <p class="text-muted">Kelola Sales Order (SO), Picking, dan Pengiriman barang.</p>
        </div>
        <a href="<?php echo e(route('outbound.create')); ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Buat SO Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No. SO / Tanggal</th>
                            <th>Gudang Asal</th>
                            <th class="text-center" style="width: 300px;">Fulfillment Progress</th>
                            <th class="text-end">Total Nilai</th>
                            <th>Tracking</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $sos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $so): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="align-middle">
                            <td class="ps-4">
                                <div class="fw-bold text-success" style="font-size: 0.95rem;"><?php echo e($so->so_number); ?></div>
                                <div class="text-muted small"><?php echo e($so->created_at->format('d/m/Y H:i')); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border border-secondary-subtle px-3 py-2 rounded-pill"><i class="bi bi-building me-1"></i> <?php echo e($so->warehouse->name); ?></span>
                            </td>
                            <td>
                                
                                <?php
                                    $steps = ['pending', 'confirmed', 'picking', 'packing', 'shipping', 'delivered'];
                                    $currentIndex = array_search($so->status, $steps);
                                    if ($so->status === 'cancelled') $currentIndex = -1;
                                ?>
                                
                                <?php if($so->status === 'cancelled'): ?>
                                    <div class="text-danger fw-bold text-center small"><i class="bi bi-x-circle-fill me-1"></i> DIBATALKAN</div>
                                <?php else: ?>
                                    <div class="position-relative m-3">
                                        <div class="progress" style="height: 4px; background-color: var(--bs-secondary-bg-subtle);">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo e(($currentIndex / (count($steps)-1)) * 100); ?>%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between position-absolute w-100" style="top: -6px; left: 0;">
                                            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $isCompleted = $index <= $currentIndex;
                                                    $isCurrent = $index === $currentIndex;
                                                    $icon = match($step) {
                                                        'pending' => 'bi-file-earmark-text',
                                                        'confirmed' => 'bi-check2-circle',
                                                        'picking' => 'bi-box-seam',
                                                        'packing' => 'bi-box',
                                                        'shipping' => 'bi-truck',
                                                        'delivered' => 'bi-house-check',
                                                        default => 'bi-circle'
                                                    };
                                                ?>
                                                <div class="text-center position-relative" title="<?php echo e(ucfirst($step)); ?>" style="width: 16px;">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center <?php echo e($isCompleted ? 'bg-primary text-white shadow-sm' : 'bg-light text-muted border border-white'); ?>" 
                                                         style="width: 16px; height: 16px; font-size: 0.5rem; <?php echo e($isCurrent ? 'transform: scale(1.3); transition: transform 0.2s; z-index: 2;' : ''); ?>">
                                                        <i class="bi <?php echo e($icon); ?>"></i>
                                                    </div>
                                                    <?php if($isCurrent): ?>
                                                        <div class="position-absolute text-primary fw-bold" style="font-size: 0.55rem; top: 20px; left: 50%; transform: translateX(-50%); white-space: nowrap; text-transform: uppercase;"><?php echo e($step); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="mt-4"></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold">Rp <?php echo e(number_format($so->total_amount, 0, ',', '.')); ?></td>
                            <td>
                                <?php if($so->status == 'shipping' || $so->status == 'delivered'): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1"><i class="bi bi-truck me-1"></i> <?php echo e($so->shipping?->tracking_number ?? 'Resi Pending'); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light rounded-pill px-3 me-2">Detail</button>
                                    <?php if($so->status == 'pending' && auth()->user()->hasPermission('confirm-so')): ?>
                                    <form action="<?php echo e(route('outbound.confirm', $so)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-warning rounded-pill px-3 me-2">Konfirmasi SO</button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if(($so->status == 'confirmed' || $so->status == 'picking') && auth()->user()->hasPermission('process-picking')): ?>
                                    <a href="<?php echo e(route('outbound.picking', $so)); ?>" class="btn btn-sm btn-warning rounded-pill px-3">Mulai Picking</a>
                                    <?php endif; ?>
                                    <?php if($so->status == 'packing' && auth()->user()->hasPermission('process-shipping')): ?>
                                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#shipModal<?php echo e($so->id); ?>">Kirim Barang</button>
                                    <?php endif; ?>
                                    <?php if($so->status == 'shipping' && auth()->user()->hasPermission('process-shipping')): ?>
                                    <form action="<?php echo e(route('outbound.deliver', $so)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-success rounded-pill px-3">Tandai Delivered</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        
                        <?php if($so->status == 'packing'): ?>
                        <div class="modal fade" id="shipModal<?php echo e($so->id); ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold">Input Pengiriman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="<?php echo e(route('outbound.ship', $so)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Ekspedisi / Kurir</label>
                                                <input type="text" name="carrier" class="form-control rounded-3" placeholder="Contoh: JNE, J&T, SiCepat" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold small">Nomor Resi (Tracking Number)</label>
                                                <input type="text" name="tracking_number" class="form-control rounded-3" placeholder="Masukkan nomor resi..." required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 p-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Konfirmasi Pengiriman</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/blue/product-launch.svg" alt="" style="width: 150px;" class="mb-3">
                                <p class="text-muted">Belum ada Sales Order.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($sos->hasPages()): ?>
        <div class="card-footer bg-white border-0 py-3">
            <?php echo e($sos->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\outbound\index.blade.php ENDPATH**/ ?>