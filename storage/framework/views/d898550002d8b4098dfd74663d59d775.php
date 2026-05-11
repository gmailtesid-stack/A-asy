<?php $__env->startSection('title', 'Laporan Logistik (WMS) - E-ASY POS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Laporan Logistik & Inventori</h2>
            <p class="text-muted">Analisis status order, pergerakan stok tercepat, dan kendala picking.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-cash-stack me-2"></i> Laporan Penjualan
            </a>
            <button class="btn btn-white border rounded-pill px-4 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Cetak Laporan
            </button>
        </div>
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h4 class="fw-bold mb-0">Stok Dalam Perjalanan (In-Transit)</h4>
                        <p class="mb-0 opacity-75">Jumlah unit barang yang telah dikirim antar cabang namun belum diterima di tujuan.</p>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bold mb-0"><?php echo e(number_format($transitStockCount)); ?></h1>
                        <small>Unit Terdeteksi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Status Purchase Orders (Inbound)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Jumlah Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $poStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border"><?php echo e(strtoupper($status->status)); ?></span></td>
                                    <td class="text-end fw-bold"><?php echo e($status->count); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Status Sales Orders (Outbound)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Jumlah Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $soStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border"><?php echo e(strtoupper($status->status)); ?></span></td>
                                    <td class="text-end fw-bold"><?php echo e($status->count); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Top 10 SKU (Pergerakan Teraktif)</h5>
                </div>
                <div class="card-body">
                    <canvas id="movementChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Kendala Picking (Not Found / Partial)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">SO #</th>
                                    <th>Produk</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-3">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pickingFailures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-3 fw-bold small"><?php echo e($fail->picking->salesOrder->order_number); ?></td>
                                    <td>
                                        <div class="fw-bold small"><?php echo e($fail->product->name); ?></div>
                                        <code class="small"><?php echo e($fail->product->sku); ?></code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill small"><?php echo e(strtoupper($fail->status)); ?></span>
                                    </td>
                                    <td class="text-end pe-3 small text-muted"><?php echo e($fail->created_at->format('d/m H:i')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada kendala picking ditemukan.</td>
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

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const movementCtx = document.getElementById('movementChart').getContext('2d');
    const movementData = <?php echo json_encode($topMovement, 15, 512) ?>;
    
    new Chart(movementCtx, {
        type: 'bar',
        data: {
            labels: movementData.map(m => m.name),
            datasets: [{
                label: 'Total Mutasi Stok',
                data: movementData.map(m => m.total_movement),
                backgroundColor: '#6366f1',
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\reports\wms.blade.php ENDPATH**/ ?>