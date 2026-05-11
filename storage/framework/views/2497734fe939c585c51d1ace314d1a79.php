<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-list-ol me-2 text-info"></i>Bagan Akun (COA)</h2>
            <p class="text-white-50">Daftar kategori akuntansi sistem ERP.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-info-glass text-white px-4 rounded-pill">
                <i class="fas fa-plus me-2"></i>Tambah Akun
            </button>
        </div>
    </div>

    <div class="card bg-glass border-0 shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Akun</th>
                            <th>Tipe</th>
                            <th class="text-end">Debit (Total)</th>
                            <th class="text-end">Kredit (Total)</th>
                            <th class="text-end">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $balance = ($account->type == 'asset' || $account->type == 'expense') 
                                ? ($account->total_debit - $account->total_credit)
                                : ($account->total_credit - $account->total_debit);
                        ?>
                        <tr>
                            <td><span class="fw-bold text-info"><?php echo e($account->code); ?></span></td>
                            <td><?php echo e($account->name); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($account->type == 'asset' ? 'primary' : ($account->type == 'revenue' ? 'success' : 'warning')); ?>-soft">
                                    <?php echo e(strtoupper($account->type)); ?>

                                </span>
                            </td>
                            <td class="text-end">Rp <?php echo e(number_format($account->total_debit ?? 0, 0, ',', '.')); ?></td>
                            <td class="text-end">Rp <?php echo e(number_format($account->total_credit ?? 0, 0, ',', '.')); ?></td>
                            <td class="text-end fw-bold <?php echo e($balance < 0 ? 'text-danger' : 'text-white'); ?>">
                                Rp <?php echo e(number_format($balance, 0, ',', '.')); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .table-dark-custom { color: white; background: transparent; }
    .table-dark-custom thead th {
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        padding: 1.25rem 1rem;
    }
    .table-dark-custom td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem;
    }
    .btn-info-glass {
        background: rgba(13, 202, 240, 0.2);
        border: 1px solid rgba(13, 202, 240, 0.3);
    }
    .bg-primary-soft { background: rgba(13, 110, 253, 0.2); color: #0d6efd; }
    .bg-success-soft { background: rgba(25, 135, 84, 0.2); color: #198754; }
    .bg-warning-soft { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\finance\accounts.blade.php ENDPATH**/ ?>