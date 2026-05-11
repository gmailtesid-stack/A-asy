<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #<?php echo e($transaction->invoice_number); ?></title>
    <style>
        body { font-family: monospace; width: 300px; margin: 0 auto; color: #000; padding: 20px 0; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; } .mb-3 { margin-bottom: 15px; }
        hr { border: none; border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; font-size: 14px; }
        .right { text-align: right; }
        .totals { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">

<div class="text-center mb-3">
    <h2 class="mb-1 fw-bold"><?php echo e($transaction->outlet->name ?? 'E-ASY POS'); ?></h2>
    <div><?php echo e($transaction->outlet->address ?? ''); ?></div>
    <div><?php echo e($transaction->outlet->phone ?? ''); ?></div>
</div>

<hr>
<div>
    No: <?php echo e($transaction->invoice_number); ?><br>
    Tgl: <?php echo e($transaction->created_at->format('d/m/Y H:i')); ?><br>
    Kasir: <?php echo e($transaction->cashier->name); ?>

</div>
<hr>

<table>
    <?php $__currentLoopData = $transaction->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td colspan="3"><?php echo e($detail->product_name); ?></td>
    </tr>
    <tr>
        <td><?php echo e($detail->quantity); ?> x <?php echo e(number_format($detail->unit_price, 0)); ?></td>
        <td class="right"></td>
        <td class="right"><?php echo e(number_format($detail->subtotal, 0)); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>

<hr>
<table class="totals">
    <tr><td>Subtotal</td><td class="right"><?php echo e(number_format($transaction->subtotal, 0)); ?></td></tr>
    <?php if($transaction->discount > 0): ?>
    <tr><td>Diskon</td><td class="right">-<?php echo e(number_format($transaction->discount, 0)); ?></td></tr>
    <?php endif; ?>
    <tr><td>PPN (11%)</td><td class="right"><?php echo e(number_format($transaction->tax, 0)); ?></td></tr>
    <tr><td style="font-size: 16px;">TOTAL</td><td class="right" style="font-size: 16px;"><?php echo e(number_format($transaction->total, 0)); ?></td></tr>
    <tr><td colspan="2"><hr style="margin:5px 0;"></td></tr>
    <tr><td>Tunai</td><td class="right"><?php echo e(number_format($transaction->cash_amount, 0)); ?></td></tr>
    <tr><td>Kembali</td><td class="right"><?php echo e(number_format($transaction->change_amount, 0)); ?></td></tr>
</table>

<div class="text-center" style="margin-top: 30px; font-size: 12px;">
    Terima Kasih Atas Kunjungan Anda!
</div>

</body>
</html>
<?php /**PATH C:\Users\Admin\.gemini\antigravity\scratch\easy-pos\resources\views\pos\receipt.blade.php ENDPATH**/ ?>