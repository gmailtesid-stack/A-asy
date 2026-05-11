<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-calendar-check me-2 text-info"></i>Riwayat Absensi</h2>
            <p class="text-white-50">Monitoring kehadiran karyawan secara harian.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-info-glass text-white px-4 rounded-pill shadow-lg">
                <i class="fas fa-clock me-2"></i>Catat Kehadiran
            </button>
        </div>
    </div>

    <div class="card bg-glass border-0 shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Karyawan</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($attendance->date); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <?php echo e(strtoupper(substr($attendance->employee->name, 0, 1))); ?>

                                    </div>
                                    <?php echo e($attendance->employee->name); ?>

                                </div>
                            </td>
                            <td class="text-success fw-bold"><?php echo e($attendance->clock_in ?? '--:--'); ?></td>
                            <td class="text-danger fw-bold"><?php echo e($attendance->clock_out ?? '--:--'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($attendance->status == 'present' ? 'success' : 'warning'); ?>-soft">
                                    <?php echo e(strtoupper($attendance->status)); ?>

                                </span>
                            </td>
                            <td class="text-white-50 small"><?php echo e($attendance->notes ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($attendances->links()); ?>

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
    .btn-info-glass { background: rgba(13, 202, 240, 0.2); border: 1px solid rgba(13, 202, 240, 0.3); }
    .bg-success-soft { background: rgba(25, 135, 84, 0.2); color: #198754; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\hr\attendances\index.blade.php ENDPATH**/ ?>