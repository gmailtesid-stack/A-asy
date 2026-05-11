<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-user-plus me-2 text-warning"></i>Tambah Karyawan Baru</h2>
            <p class="text-white-50">Isi detail informasi karyawan untuk ditambahkan ke sistem.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-glass border-0 shadow-lg">
                <div class="card-body p-5">
                    <form action="<?php echo e(route('employees.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">ID Karyawan</label>
                                <input type="text" name="employee_id" class="form-control bg-glass-input text-white border-white-10" placeholder="Contoh: EMP001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control bg-glass-input text-white border-white-10" placeholder="Nama Karyawan" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Posisi / Jabatan</label>
                                <select name="position" class="form-select bg-glass-input text-white border-white-10">
                                    <option value="Staff">Staff</option>
                                    <option value="Supervisor">Supervisor</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Cashier">Cashier</option>
                                    <option value="Warehouse">Warehouse</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Gaji Pokok (IDR)</label>
                                <input type="number" name="salary" class="form-control bg-glass-input text-white border-white-10" placeholder="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Tanggal Bergabung</label>
                                <input type="date" name="joined_at" class="form-control bg-glass-input text-white border-white-10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Status</label>
                                <select name="status" class="form-select bg-glass-input text-white border-white-10">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-Aktif</option>
                                    <option value="on_leave">Cuti</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-5 d-flex gap-2">
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-5 rounded-pill">Simpan Data</button>
                            <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-light px-4 rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
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
    .bg-glass-input {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .bg-glass-input:focus {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: #ffc107 !important;
        color: white;
    }
    .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\hr\employees\create.blade.php ENDPATH**/ ?>