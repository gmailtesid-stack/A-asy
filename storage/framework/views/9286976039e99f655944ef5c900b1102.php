<?php $__env->startSection('title', 'WMS Operator Console - Picking'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <a href="<?php echo e(route('outbound.index')); ?>" class="btn btn-sm btn-light border rounded-circle" style="width: 32px; height: 32px;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="h4 mb-0 fw-800 text-dark">Picking Console</h2>
            </div>
            <p class="text-muted small mb-0 ms-5">Order ID: <span class="fw-bold text-success"><?php echo e($so->so_number); ?></span> | Gudang: <span class="fw-bold"><?php echo e($so->warehouse->name); ?></span></p>
        </div>
        <div class="text-md-end">
            <span class="badge bg-warning text-dark border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-clock-history me-1"></i> STATUS: PICKING IN PROGRESS
            </span>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-1">Daftar Barang (Pick List)</h5>
                    <p class="mb-0 opacity-75 small">Total <?php echo e(count($so->items)); ?> SKU unik yang harus diambil.</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="d-inline-block text-center me-4">
                        <h4 class="fw-800 mb-0"><?php echo e(count($so->items)); ?></h4>
                        <span class="small opacity-75">Items</span>
                    </div>
                    <div class="d-inline-block text-center">
                        <h4 class="fw-800 mb-0"><?php echo e($so->items->sum('quantity')); ?></h4>
                        <span class="small opacity-75">Total Qty</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('outbound.picking.store', $so)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <?php $__currentLoopData = $so->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $inventory = \App\Models\Inventory::where(['warehouse_id' => $so->warehouse_id, 'product_id' => $item->product_id])->first();
                    $location = $inventory?->location?->name ?? 'Default Bin';
                ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden picking-card transition-all">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                
                                <div class="col-md-2 bg-light d-flex flex-column align-items-center justify-content-center p-3 border-end">
                                    <span class="text-muted small fw-bold text-uppercase mb-1">Location</span>
                                    <div class="h3 fw-800 text-primary mb-0"><?php echo e($location); ?></div>
                                </div>
                                
                                
                                <div class="col-md-4 p-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?php echo e($item->product->image_url); ?>" alt="" class="rounded-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark"><?php echo e($item->product->name); ?></h6>
                                            <code class="text-muted small"><?php echo e($item->product->sku); ?></code>
                                            <div class="mt-2">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Requested: <?php echo e($item->quantity); ?> <?php echo e($item->product->unit); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-md-3 p-4 bg-light bg-opacity-25 border-start border-end d-flex flex-column align-items-center justify-content-center">
                                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">Quantity Found</label>
                                    <div class="input-group input-group-lg" style="max-width: 150px;">
                                        <button type="button" class="btn btn-white border px-3" onclick="this.nextElementSibling.stepDown()">-</button>
                                        <input type="number" name="items[<?php echo e($index); ?>][quantity_found]" 
                                               class="form-control border text-center fw-800" 
                                               value="<?php echo e($item->quantity); ?>" min="0" max="<?php echo e($item->quantity); ?>">
                                        <button type="button" class="btn btn-white border px-3" onclick="this.previousElementSibling.stepUp()">+</button>
                                    </div>
                                    <input type="hidden" name="items[<?php echo e($index); ?>][product_id]" value="<?php echo e($item->product_id); ?>">
                                </div>

                                
                                <div class="col-md-3 p-4 d-flex align-items-center justify-content-center">
                                    <div class="btn-group w-100 shadow-sm rounded-pill overflow-hidden" role="group">
                                        <input type="radio" class="btn-check" name="items[<?php echo e($index); ?>][status]" value="found" id="found_<?php echo e($index); ?>" checked>
                                        <label class="btn btn-outline-success border-0 py-3 fw-bold" for="found_<?php echo e($index); ?>"><i class="bi bi-check-circle-fill d-block mb-1"></i> Found</label>
                                        
                                        <input type="radio" class="btn-check" name="items[<?php echo e($index); ?>][status]" value="partial" id="partial_<?php echo e($index); ?>">
                                        <label class="btn btn-outline-warning border-0 py-3 fw-bold" for="partial_<?php echo e($index); ?>"><i class="bi bi-exclamation-circle-fill d-block mb-1"></i> Partial</label>

                                        <input type="radio" class="btn-check" name="items[<?php echo e($index); ?>][status]" value="not_found" id="notfound_<?php echo e($index); ?>">
                                        <label class="btn btn-outline-danger border-0 py-3 fw-bold" for="notfound_<?php echo e($index); ?>"><i class="bi bi-x-circle-fill d-block mb-1"></i> Missing</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="mt-5 mb-5 p-4 bg-white shadow-lg rounded-4 d-flex flex-column flex-md-row justify-content-between align-items-center sticky-bottom border border-primary border-opacity-10">
            <div class="mb-3 mb-md-0">
                <div class="fw-bold text-dark"><i class="bi bi-shield-check text-success me-2"></i> Konfirmasi Akurasi</div>
                <p class="text-muted small mb-0">Dengan menyelesaikan picking, stok akan secara otomatis dipotong dari lokasi gudang.</p>
            </div>
            <div class="d-flex gap-3 w-100 w-md-auto">
                <a href="<?php echo e(route('outbound.index')); ?>" class="btn btn-light px-4 py-3 fw-bold border rounded-pill flex-grow-1 flex-md-grow-0">Batal</a>
                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow rounded-pill flex-grow-1 flex-md-grow-0 animate__animated animate__pulse animate__infinite">
                    SELESAIKAN PICKING <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .picking-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .btn-check:checked + .btn-outline-success { background-color: #d1e7dd; border-color: #a3cfbb; color: #0f5132; }
    .btn-check:checked + .btn-outline-warning { background-color: #fff3cd; border-color: #ffe69c; color: #664d03; }
    .btn-check:checked + .btn-outline-danger { background-color: #f8d7da; border-color: #f1aeb5; color: #842029; }
    .sticky-bottom { position: sticky; bottom: 20px; z-index: 1000; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\outbound\picking.blade.php ENDPATH**/ ?>