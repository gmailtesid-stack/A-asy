<?php $__env->startSection('title', 'Detail Stock Transfer - E-ASY WMS'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('stock_transfers.index')); ?>">Stock Transfer</a></li>
    <li class="breadcrumb-item active"><?php echo e($stockTransfer->transfer_number); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-800 text-dark">Detail Perpindahan Stok</h2>
            <p class="text-muted small mb-0">No. Dokumen: <span class="fw-bold text-primary"><?php echo e($stockTransfer->transfer_number); ?></span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('stock_transfers.index')); ?>" class="btn btn-light border px-4 rounded-3">Kembali</a>
            <button class="btn btn-light border px-4 rounded-3"><i class="bi bi-printer me-2"></i> Cetak Surat Jalan</button>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4" style="font-size: 1rem;">Status & Informasi</h5>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Status Saat Ini</label>
                        <?php
                            $statusConfig = [
                                'draft' => ['color' => 'secondary', 'class' => 'bg-secondary'],
                                'pending' => ['color' => 'warning', 'class' => 'bg-warning'],
                                'transit' => ['color' => 'info', 'class' => 'bg-info'],
                                'received' => ['color' => 'success', 'class' => 'bg-success'],
                                'cancelled' => ['color' => 'danger', 'class' => 'bg-danger']
                            ][$stockTransfer->status] ?? ['color' => 'secondary', 'class' => 'bg-secondary'];
                        ?>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-4 <?php echo e($statusConfig['class']); ?> bg-opacity-10 border border-<?php echo e($statusConfig['color']); ?> border-opacity-25">
                            <div class="stat-icon-wrapper <?php echo e($statusConfig['class']); ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <div>
                                <div class="fw-800 text-<?php echo e($statusConfig['color']); ?>"><?php echo e(strtoupper($stockTransfer->status)); ?></div>
                                <div class="text-muted small">Diperbarui: <?php echo e($stockTransfer->updated_at->format('d/m/Y H:i')); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 pt-3 border-top">
                        <label class="form-label text-muted small fw-bold text-uppercase">Gudang Asal</label>
                        <div class="fw-bold text-dark"><?php echo e($stockTransfer->fromWarehouse->name); ?></div>
                        <div class="text-muted small"><?php echo e($stockTransfer->fromWarehouse->address ?? 'Alamat tidak tersedia'); ?></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Gudang Tujuan</label>
                        <div class="fw-bold text-dark"><?php echo e($stockTransfer->toWarehouse->name); ?></div>
                        <div class="text-muted small"><?php echo e($stockTransfer->toWarehouse->address ?? 'Alamat tidak tersedia'); ?></div>
                    </div>

                    <div class="mb-0 pt-3 border-top">
                        <label class="form-label text-muted small fw-bold text-uppercase">Catatan Operator</label>
                        <p class="text-dark small"><?php echo e($stockTransfer->note ?? 'Tidak ada catatan.'); ?></p>
                    </div>
                </div>
            </div>

            
            <?php if($stockTransfer->status == 'pending'): ?>
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold mb-3">Siap untuk Pengiriman?</h6>
                    <p class="small opacity-75 mb-4">Pastikan seluruh barang fisik sudah dikemas dan siap diangkut.</p>
                    <form action="<?php echo e(route('stock_transfers.ship', $stockTransfer)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-white w-100 fw-bold rounded-pill shadow">Konfirmasi Pengiriman</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if($stockTransfer->status == 'transit'): ?>
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white mb-4">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold mb-3">Barang Sudah Sampai?</h6>
                    <p class="small opacity-75 mb-4">Harap verifikasi jumlah barang yang diterima sesuai dengan manifes.</p>
                    <form action="<?php echo e(route('stock_transfers.receive', $stockTransfer)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-white w-100 fw-bold rounded-pill shadow">Konfirmasi Penerimaan</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0" style="font-size: 1rem;">Manifest Barang (Item List)</h5>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small fw-bold text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Produk</th>
                                    <th class="text-center py-3">QTY Diminta</th>
                                    <th class="text-center py-3">QTY Diterima</th>
                                    <th class="text-center py-3">Status Item</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php $__currentLoopData = $stockTransfer->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-box"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo e($item->product->name); ?></div>
                                                <div class="text-muted small fw-bold"><?php echo e($item->product->sku); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-800 fs-5"><?php echo e($item->quantity_requested); ?> <span class="fs-6 fw-normal text-muted"><?php echo e($item->product->unit); ?></span></td>
                                    <td class="text-center fw-800 fs-5 text-success"><?php echo e($item->quantity_received ?: '-'); ?></td>
                                    <td class="text-center">
                                        <?php if($stockTransfer->status == 'received'): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-bold">Diterima</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border rounded-pill px-3 py-1">Menunggu</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border-0 rounded-4 p-4 mt-4 shadow-sm">
                <div class="d-flex gap-3">
                    <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
                    <div>
                        <h6 class="fw-bold text-primary">Informasi Penting</h6>
                        <p class="small mb-0 opacity-75">Seluruh pergerakan stok dicatat dalam Audit Log Sistem. Kesalahan dalam penginputan jumlah barang dapat menyebabkan selisih inventori yang berdampak pada laporan keuangan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .stat-icon-wrapper { font-size: 1.2rem; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\stock_transfers\show.blade.php ENDPATH**/ ?>