<?php $__env->startSection('title', 'Penerimaan Barang (GRN) - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="<?php echo e(route('inbound.index')); ?>" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Inbound
        </a>
        <h2 class="fw-bold mt-2">Penerimaan Barang (GRN)</h2>
        <p class="text-muted">Menerima barang untuk PO: <span class="fw-bold text-primary"><?php echo e($po->po_number); ?></span></p>
    </div>

    <form action="<?php echo e(route('inbound.grn.store', $po)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Informasi PO</h5>
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted small">Supplier</span>
                            <span class="fw-bold small"><?php echo e($po->supplier->name); ?></span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted small">Gudang Tujuan</span>
                            <span class="fw-bold small"><?php echo e($po->warehouse->name); ?></span>
                        </div>
                        <div class="mb-0 d-flex justify-content-between">
                            <span class="text-muted small">Tanggal PO</span>
                            <span class="fw-bold small"><?php echo e($po->created_at->format('d/m/Y')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Item yang Diterima</h5>
                        
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Dipesan</th>
                                        <th class="text-center">Diterima</th>
                                        <th>Lokasi Putaway</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $po->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="items[<?php echo e($index); ?>][product_id]" value="<?php echo e($item->product_id); ?>">
                                            <div class="fw-bold"><?php echo e($item->product->name); ?></div>
                                            <small class="text-muted"><?php echo e($item->product->sku); ?></small>
                                        </td>
                                        <td class="text-center fw-bold"><?php echo e($item->quantity); ?></td>
                                        <td style="width: 120px;">
                                            <input type="number" name="items[<?php echo e($index); ?>][quantity_received]" 
                                                   class="form-control form-control-sm rounded-3 text-center" 
                                                   value="<?php echo e($item->quantity); ?>" min="0" max="<?php echo e($item->quantity); ?>">
                                        </td>
                                        <td>
                                            <select name="items[<?php echo e($index); ?>][location_id]" class="form-select form-select-sm rounded-3">
                                                <option value="">Pilih Lokasi...</option>
                                                <?php $__currentLoopData = $po->warehouse->locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($location->id); ?>"><?php echo e($location->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-4 border-top text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-5">
                                Konfirmasi Penerimaan (GRN) <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\inbound\receive.blade.php ENDPATH**/ ?>