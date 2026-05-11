<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-white mb-1">💰 Dashboard Keuangan</h2>
            <p class="text-white-50">Ringkasan performa finansial real-time.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-glass border-0 shadow-lg">
                <div class="card-body p-4 text-center">
                    <div class="icon-shape bg-primary-soft text-primary rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <h6 class="text-white-50 mb-1">Total Saldo Kas & Bank</h6>
                    <h3 class="fw-bold text-white">Rp <?php echo e(number_format($cashBalance, 0, ',', '.')); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-glass border-0 shadow-lg border-start border-success border-4">
                <div class="card-body p-4 text-center">
                    <div class="icon-shape bg-success-soft text-success rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h6 class="text-white-50 mb-1">Pendapatan Penjualan (Bulan Ini)</h6>
                    <h3 class="fw-bold text-success">Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-glass border-0 shadow-lg border-start border-danger border-4">
                <div class="card-body p-4 text-center">
                    <div class="icon-shape bg-danger-soft text-danger rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    </div>
                    <h6 class="text-white-50 mb-1">Total Beban / HPP</h6>
                    <h3 class="fw-bold text-danger">Rp <?php echo e(number_format($totalExpense, 0, ',', '.')); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Journal Entries -->
        <div class="col-lg-8">
            <div class="card bg-glass border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-bottom border-white-10 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold"><i class="fas fa-history me-2"></i>Jurnal Terakhir</h5>
                    <a href="<?php echo e(route('finance.journals')); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Referensi</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Total Debit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentJournals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $journal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($journal->entry_date); ?></td>
                                    <td><span class="badge bg-primary-soft text-primary"><?php echo e($journal->reference); ?></span></td>
                                    <td><?php echo e($journal->description); ?></td>
                                    <td class="text-end fw-bold">Rp <?php echo e(number_format($journal->lines->sum('debit'), 0, ',', '.')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-white-50">Belum ada data jurnal.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="col-lg-4">
            <div class="card bg-glass border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-bottom border-white-10 p-4">
                    <h5 class="mb-0 text-white fw-bold"><i class="fas fa-th-large me-2"></i>Aksi Cepat</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <a href="<?php echo e(route('finance.reports.profit-loss')); ?>" class="btn btn-primary-glass p-3 text-start d-flex align-items-center">
                            <div class="icon-circle bg-primary text-white me-3">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white">Laporan Laba Rugi</div>
                                <div class="small text-white-50">Analisis keuntungan periode ini</div>
                            </div>
                        </a>
                        <a href="<?php echo e(route('finance.accounts')); ?>" class="btn btn-info-glass p-3 text-start d-flex align-items-center">
                            <div class="icon-circle bg-info text-white me-3">
                                <i class="fas fa-list-ol"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white">Bagan Akun (COA)</div>
                                <div class="small text-white-50">Kelola kategori akuntansi</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .bg-primary-soft { background: rgba(13, 110, 253, 0.15) !important; }
    .bg-success-soft { background: rgba(25, 135, 84, 0.15) !important; }
    .bg-danger-soft { background: rgba(220, 53, 69, 0.15) !important; }
    
    .table-dark-custom {
        background: transparent;
        color: white;
    }
    .table-dark-custom thead th {
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }
    .table-dark-custom td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem 0.75rem;
    }
    .btn-primary-glass {
        background: rgba(13, 110, 253, 0.1);
        border: 1px solid rgba(13, 110, 253, 0.2);
        transition: all 0.3s;
    }
    .btn-primary-glass:hover {
        background: rgba(13, 110, 253, 0.2);
        border-color: rgba(13, 110, 253, 0.5);
    }
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\finance\index.blade.php ENDPATH**/ ?>