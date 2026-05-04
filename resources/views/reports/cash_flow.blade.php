@extends('layouts.app')
@section('title', 'Proyeksi Arus Kas')

@section('content')
<div class="page-header">
    <h1>📈 Proyeksi Arus Kas (Cash Flow)</h1>
    <p class="page-subtitle">Prediksi kesehatan finansial berdasarkan uang yang akan masuk (Piutang OMS) dan uang yang harus keluar (Hutang PO).</p>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <!-- Saldo Kas Saat Ini -->
    <div class="stat-card" style="border-top: 4px solid var(--primary)">
        <div class="stat-label">Saldo Kas/Bank Saat Ini</div>
        <div class="stat-value">Rp {{ number_format($data['current_cash'], 0, ',', '.') }}</div>
    </div>
    
    <!-- Piutang (Akan Masuk) -->
    <div class="stat-card" style="border-top: 4px solid var(--success)">
        <div class="stat-label">Piutang Berjalan (Dari Penjualan Online)</div>
        <div class="stat-value" style="color:var(--success)">+ Rp {{ number_format($data['accounts_receivable'], 0, ',', '.') }}</div>
    </div>
    
    <!-- Hutang (Akan Keluar) -->
    <div class="stat-card" style="border-top: 4px solid var(--danger)">
        <div class="stat-label">Hutang Berjalan (Ke Supplier PO)</div>
        <div class="stat-value" style="color:var(--danger)">- Rp {{ number_format($data['accounts_payable'], 0, ',', '.') }}</div>
    </div>

    <!-- Proyeksi Kas -->
    <div class="stat-card" style="background: {{ $data['health_status'] == 'healthy' ? 'var(--success)' : 'var(--danger)' }}; color:white;">
        <div class="stat-label" style="color:rgba(255,255,255,0.8)">Proyeksi Saldo Kas Mendatang</div>
        <div class="stat-value" style="color:white">Rp {{ number_format($data['projected_cash'], 0, ',', '.') }}</div>
    </div>
</div>

<div class="card" style="margin-top:2rem;">
    <div class="card-header">
        <h3>Status Kesehatan Keuangan</h3>
    </div>
    <div style="padding:2rem;">
        @if($data['health_status'] == 'healthy')
            <div style="display:flex; align-items:center; gap:1rem; color:var(--success)">
                <span style="font-size:3rem">🟢</span>
                <div>
                    <h3 style="margin:0">Sehat (Surplus)</h3>
                    <p style="margin:.5rem 0 0 0; color:var(--text-muted)">Kas Anda diproyeksikan cukup untuk melunasi semua tagihan supplier yang berjalan.</p>
                </div>
            </div>
        @else
            <div style="display:flex; align-items:center; gap:1rem; color:var(--danger)">
                <span style="font-size:3rem">🔴</span>
                <div>
                    <h3 style="margin:0">Kritis (Defisit)</h3>
                    <p style="margin:.5rem 0 0 0; color:var(--text-muted)">Peringatan! Jika semua tagihan supplier jatuh tempo saat ini, Kas perusahaan tidak akan cukup untuk menutupnya. Tunda PO baru atau lakukan penagihan lebih cepat.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
