<?php $__env->startSection('title', 'Buat Purchase Order Baru - E-ASY WMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="<?php echo e(route('inbound.index')); ?>" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Inbound
        </a>
        <h2 class="fw-bold mt-2">Buat Purchase Order (PO)</h2>
    </div>

    <form action="<?php echo e(route('inbound.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Informasi Utama</h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Pilih Supplier</label>
                            <select name="supplier_id" class="form-select rounded-3" required>
                                <option value="">Pilih Supplier...</option>
                                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">Gudang Tujuan</label>
                            <select name="warehouse_id" class="form-select rounded-3" required>
                                <option value="">Pilih Gudang...</option>
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?> (<?php echo e($warehouse->outlet->name); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Daftar Item Barang</h5>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control form-control-sm rounded-pill px-3 barcode-input" placeholder="Scan Barcode / SKU..." id="barcodeScanner" style="width: 200px;">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addItem()">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Baris
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle" id="itemsTable">
                                <thead class="bg-light rounded-3">
                                    <tr>
                                        <th style="width: 50%">Produk SKU</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Total</th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][product_id]" class="form-select rounded-3" required>
                                                <option value="">Pilih Produk...</option>
                                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?> (<?php echo e($product->sku); ?>)</option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control rounded-3" value="1" min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][price]" class="form-control rounded-3" value="0" min="0" required>
                                        </td>
                                        <td class="fw-bold">Rp 0</td>
                                        <td>
                                            <button type="button" class="btn btn-link text-danger p-0" onclick="removeItem(this)">
                                                <i class="bi bi-trash fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-4 border-top text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-5">
                                Simpan Purchase Order <i class="bi bi-check2-circle ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let itemIndex = 1;
    function addItem() {
        const tableBody = document.querySelector('#itemsTable tbody');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-select rounded-3" required>
                    <option value="">Pilih Produk...</option>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?> (<?php echo e($product->sku); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control rounded-3" value="1" min="1" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][price]" class="form-control rounded-3" value="0" min="0" required>
            </td>
            <td class="fw-bold">Rp 0</td>
            <td>
                <button type="button" class="btn btn-link text-danger p-0" onclick="removeItem(this)">
                    <i class="bi bi-trash fs-5"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(newRow);
        itemIndex++;
    }

    function removeItem(button) {
        const row = button.closest('tr');
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
        }
    }

    // Barcode Logic
    const barcodeScanner = document.getElementById('barcodeScanner');
    const products = <?php echo json_encode($products, 15, 512) ?>;

    barcodeScanner.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const sku = this.value;
            const product = products.find(p => p.sku === sku);
            
            if (product) {
                addItemWithProduct(product);
                this.value = '';
            } else {
                alert('Produk dengan SKU tersebut tidak ditemukan.');
            }
        }
    });

    function addItemWithProduct(product) {
        addItem();
        const rows = document.querySelectorAll('.item-row');
        const lastRow = rows[rows.length - 1];
        lastRow.querySelector('select').value = product.id;
        lastRow.querySelector('input[type="number"]').focus();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\inbound\create.blade.php ENDPATH**/ ?>