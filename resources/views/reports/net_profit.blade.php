@extends('layouts.app')
@section('title', 'Laporan Laba Bersih (Net Profit)')

@section('content')
<div class="page-header">
    <h1>💹 Laporan Laba Bersih (Real Net Profit)</h1>
    <p class="page-subtitle">Analisis profitabilitas menyeluruh: Omzet, HPP, dan Biaya Operasional (OPEX).</p>
</div>

<div style="margin-bottom:1.5rem; display:flex; justify-content:flex-end;">
    <form method="GET" style="display:flex; gap:.5rem;">
        <select name="period" class="form-control" onchange="this.form.submit()">
            <option value="week" @selected($period == 'week')>Minggu Ini</option>
            <option value="month" @selected($period == 'month')>Bulan Ini</option>
            <option value="year" @selected($period == 'year')>Tahun Ini</option>
        </select>
    </form>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card">
        <div class="stat-label">Total Omzet (POS + OMS)</div>
        <div class="stat-value" style="color:var(--primary)">Rp {{ number_format($data['pos']['revenue'] + $data['oms']['revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Harga Pokok Penjualan (HPP)</div>
        <div class="stat-value" style="color:var(--danger)">- Rp {{ number_format($data['pos']['cogs'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Biaya Operasional (OPEX)</div>
        <div class="stat-value" style="color:var(--warning)">- Rp {{ number_format($data['expenses'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="background:var(--success); color:white;">
        <div class="stat-label" style="color:rgba(255,255,255,0.8)">Laba Bersih (Net Profit)</div>
        <div class="stat-value" style="color:white">Rp {{ number_format($data['net_profit'], 0, ',', '.') }}</div>
    </div>
</div>

<div class="card" style="margin-top:2rem;">
    <div class="card-header"><h3>Kinerja per Kanal (Channel Profitability)</h3></div>
    <div style="padding:1.5rem; display:grid; grid-template-columns: 1fr 1fr; gap:2rem;">
        
        <!-- POS Physical -->
        <div style="border:1px solid var(--border); border-radius:12px; padding:1.5rem;">
            <h4 style="margin-bottom:1rem; border-bottom:1px solid var(--border); padding-bottom:.5rem;">🏬 Penjualan Fisik (POS)</h4>
            <div style="display:flex; justify-content:space-between; margin-bottom:.5rem;">
                <span>Pendapatan Kotor:</span> <strong>Rp {{ number_format($data['pos']['revenue'], 0, ',', '.') }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:.5rem; color:var(--danger)">
                <span>Total Diskon:</span> <strong>- Rp {{ number_format($data['pos']['discount'], 0, ',', '.') }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:.5rem; color:var(--warning)">
                <span>HPP (COGS FIFO):</span> <strong>- Rp {{ number_format($data['pos']['cogs'], 0, ',', '.') }}</strong>
            </div>
            <hr style="margin:1rem 0; border:none; border-top:1px dashed var(--border);">
            <div style="display:flex; justify-content:space-between; font-size:1.1rem;">
                <span>Laba Kotor (Gross Margin):</span> 
                <strong style="color:var(--success)">
                    Rp {{ number_format($data['pos']['gross_profit'], 0, ',', '.') }} ({{ $data['pos']['margin_pct'] }}%)
                </strong>
            </div>
        </div>

        <!-- OMS Online -->
        <div style="border:1px solid var(--border); border-radius:12px; padding:1.5rem;">
            <h4 style="margin-bottom:1rem; border-bottom:1px solid var(--border); padding-bottom:.5rem;">🌐 Penjualan Online (OMS)</h4>
            <div style="display:flex; justify-content:space-between; margin-bottom:.5rem;">
                <span>Total Estimasi GMV:</span> <strong>Rp {{ number_format($data['oms']['revenue'], 0, ',', '.') }}</strong>
            </div>
            <p style="color:var(--text-muted); font-size:0.9rem; margin-top:1rem;">
                *Laba kotor OMS akan terhitung secara rinci setelah integrasi API Marketplace menarik potongan admin platform.
            </p>
        </div>

    </div>
</div>
@endsection
