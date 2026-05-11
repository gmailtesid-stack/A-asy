<?php $__env->startSection('title', 'Manajemen Pengeluaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>💸 Manajemen Pengeluaran (OPEX)</h1>
    <p class="page-subtitle">Catat dan kelola biaya operasional seluruh cabang untuk menghitung Laba Bersih yang akurat.</p>
</div>


<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); margin-bottom:2rem;">
    <?php $__currentLoopData = ['rent'=>'🏠 Sewa','utilities'=>'⚡ Utilitas','salary'=>'👤 Gaji','packaging'=>'📦 Packaging','marketing'=>'📣 Marketing','other'=>'🔧 Lainnya']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="stat-card">
        <div class="stat-label"><?php echo e($label); ?></div>
        <div class="stat-value">Rp <?php echo e(number_format($summary[$cat] ?? 0, 0, ',', '.')); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div style="display:grid; grid-template-columns: 1fr 360px; gap:1.5rem; align-items:start;">

    
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Daftar Pengeluaran</h3>
            <div style="display:flex;gap:.5rem;">
                <select name="status" onchange="this.form && this.form.submit()" class="form-control" style="width:auto">
                    <option value="">Semua Status</option>
                    <option value="pending" <?php if(request('status')=='pending'): echo 'selected'; endif; ?>>Pending</option>
                    <option value="approved" <?php if(request('status')=='approved'): echo 'selected'; endif; ?>>Disetujui</option>
                    <option value="rejected" <?php if(request('status')=='rejected'): echo 'selected'; endif; ?>>Ditolak</option>
                </select>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr><th>Tanggal</th><th>Deskripsi</th><th>Kategori</th><th>Jumlah</th><th>Diajukan</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($exp->expense_date->format('d M Y')); ?></td>
                        <td><?php echo e($exp->description); ?></td>
                        <td><span class="badge badge-info"><?php echo e($exp->category); ?></span></td>
                        <td><strong>Rp <?php echo e(number_format($exp->amount, 0, ',', '.')); ?></strong></td>
                        <td><?php echo e($exp->user->name ?? '-'); ?></td>
                        <td>
                            <?php if($exp->status === 'approved'): ?> <span class="badge badge-success">✅ Disetujui</span>
                            <?php elseif($exp->status === 'rejected'): ?> <span class="badge badge-danger">❌ Ditolak</span>
                            <?php else: ?> <span class="badge badge-warning">⏳ Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($exp->status === 'pending' && auth()->user()->hasRole('admin|supervisor')): ?>
                            <form method="POST" action="<?php echo e(route('expenses.approve', $exp)); ?>" style="display:inline">
                                <?php echo csrf_field(); ?> <button class="btn btn-sm btn-success">✅</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('expenses.reject', $exp)); ?>" style="display:inline" onsubmit="return prompt('Alasan penolakan:') !== null">
                                <?php echo csrf_field(); ?> <button class="btn btn-sm btn-danger">❌</button>
                            </form>
                            <?php endif; ?>
                            <?php if($exp->receipt_url): ?>
                            <a href="<?php echo e($exp->receipt_url); ?>" target="_blank" class="btn btn-sm btn-secondary">🧾</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center">Belum ada data pengeluaran.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:1rem"><?php echo e($expenses->links()); ?></div>
    </div>

    
    <div class="card">
        <div class="card-header"><h3>➕ Catat Pengeluaran</h3></div>
        <div style="padding:1.5rem">
            <form method="POST" action="<?php echo e(route('expenses.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category" class="form-control" required>
                        <?php $__currentLoopData = ['rent','utilities','salary','packaging','marketing','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>"><?php echo e(ucfirst($cat)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <input type="text" name="description" class="form-control" placeholder="cth: Listrik Cabang A April 2026" required>
                </div>
                <div class="form-group">
                    <label>Jumlah (Rp)</label>
                    <input type="number" name="amount" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="expense_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
                </div>
                <div class="form-group">
                    <label>Bukti Pengeluaran (Foto)</label>
                    <input type="file" name="receipt" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ajukan Pengeluaran</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\finance\expenses\index.blade.php ENDPATH**/ ?>