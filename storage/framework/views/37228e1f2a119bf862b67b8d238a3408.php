<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-heart me-2 text-danger"></i>Database Pelanggan</h2>
            <p class="text-white-50">Kelola data loyalitas dan hubungan pelanggan (CRM).</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-danger text-white px-4 rounded-pill shadow-lg">
                <i class="fas fa-plus me-2"></i>Tambah Pelanggan Baru
            </a>
        </div>
    </div>

    <div class="card bg-glass border-0 shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Nama Pelanggan</th>
                            <th>Kontak</th>
                            <th>Outlet Terdaftar</th>
                            <th class="text-center">Loyalty Points</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo e($customer->name); ?></div>
                                        <div class="small text-white-50">ID: CUST-<?php echo e(str_pad($customer->id, 4, '0', STR_PAD_LEFT)); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small text-white"><i class="fas fa-phone me-2 text-white-50"></i><?php echo e($customer->phone ?? '-'); ?></div>
                                <div class="small text-white-50"><i class="fas fa-envelope me-2 text-white-50"></i><?php echo e($customer->email ?? '-'); ?></div>
                            </td>
                            <td><?php echo e($customer->outlet->name ?? 'Pusat'); ?></td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center bg-danger-soft text-danger px-3 py-1 rounded-pill fw-bold">
                                    <i class="fas fa-star me-2"></i><?php echo e(number_format($customer->loyalty_points, 0)); ?>

                                </div>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-glass text-white"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-glass text-white"><i class="fas fa-history"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($customers->links()); ?>

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
    .bg-danger-soft { background: rgba(220, 53, 69, 0.2); }
    .btn-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\crm\customers\index.blade.php ENDPATH**/ ?>