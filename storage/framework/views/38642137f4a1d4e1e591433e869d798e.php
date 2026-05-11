<?php $__env->startSection('title', 'Approval Gateway'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>🛡️ Approval Gateway</h1>
    <p class="page-subtitle">Pusat persetujuan manajerial untuk operasi sensitif (Opname, Biaya, dll) demi menjaga integritas data.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Permohonan Menunggu Persetujuan</h3>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu Pengajuan</th>
                    <th>Tipe Dokumen</th>
                    <th>ID Ref</th>
                    <th>Diajukan Oleh</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($approval->created_at->format('d M Y H:i')); ?></td>
                    <td><span class="badge badge-info"><?php echo e(class_basename($approval->approvable_type)); ?></span></td>
                    <td>#<?php echo e($approval->approvable_id); ?></td>
                    <td><?php echo e($approval->requester->name ?? '-'); ?></td>
                    <td><?php echo e($approval->notes ?? '-'); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('approvals.approve', $approval)); ?>" style="display:inline" onsubmit="return confirm('Setujui permohonan ini?')">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-success">✅ Setujui</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('approvals.reject', $approval)); ?>" style="display:inline" onsubmit="return prompt('Alasan penolakan:') !== null">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-danger">❌ Tolak</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada permohonan yang menunggu persetujuan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:1rem"><?php echo e($pending->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\admin\approvals\index.blade.php ENDPATH**/ ?>