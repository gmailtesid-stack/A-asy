@extends('layouts.app')

@section('title', 'Dashboard Laporan — E-ASY POS')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard Laporan</li>
@endsection

@push('styles')
<style>
    .kpi-card {
        border-radius: 14px;
        padding: 1.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
        right: -30px; top: -30px;
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,.07);
        right: 30px; bottom: -20px;
    }
    .kpi-card .kpi-label { font-size: .8rem; font-weight: 600; opacity: .85; text-transform: uppercase; letter-spacing: .05em; }
    .kpi-card .kpi-value { font-size: 1.75rem; font-weight: 800; margin: .25rem 0; letter-spacing: -1px; }
    .kpi-card .kpi-sub   { font-size: .8rem; opacity: .75; }
    .kpi-card .kpi-icon  { font-size: 2rem; position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); opacity: .25; }

    .kpi-blue   { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
    .kpi-green  { background: linear-gradient(135deg, #16a34a, #15803d); }
    .kpi-violet { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
    .kpi-orange { background: linear-gradient(135deg, #ea580c, #dc2626); }

    .chart-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 1px 8px rgba(0,0,0,.07);
    }
    .chart-card h6 { font-weight: 700; color: #1e293b; margin-bottom: 1rem; }

    .top-product-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .6rem 0;
        border-bottom: 1px solid #f8fafc;
    }
    .top-product-row:last-child { border: none; }
    .rank-badge {
        width: 28px; height: 28px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: .8rem;
    }
    .rank-1 { background: #fef3c7; color: #92400e; }
    .rank-2 { background: #e2e8f0; color: #475569; }
    .rank-3 { background: #fee2e2; color: #dc2626; }
    .rank-n { background: #f1f5f9; color: #64748b; }
</style>
@endpush

@section('content')

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-blue">
            <i class="bi bi-currency-dollar kpi-icon"></i>
            <div class="kpi-label">Pendapatan Hari Ini</div>
            <div class="kpi-value">Rp {{ number_format($summaryStats['today_revenue'], 0, ',', '.') }}</div>
            <div class="kpi-sub">{{ $summaryStats['today_trx'] }} transaksi</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-green">
            <i class="bi bi-graph-up-arrow kpi-icon"></i>
            <div class="kpi-label">Pendapatan Bulan Ini</div>
            <div class="kpi-value">Rp {{ number_format($summaryStats['month_revenue'], 0, ',', '.') }}</div>
            <div class="kpi-sub">{{ $summaryStats['month_trx'] }} transaksi</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-violet">
            <i class="bi bi-shop kpi-icon"></i>
            <div class="kpi-label">Total Outlet Aktif</div>
            <div class="kpi-value">{{ count($outletCompare) }}</div>
            <div class="kpi-sub">outlet beroperasi</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-orange">
            <i class="bi bi-fire kpi-icon"></i>
            <div class="kpi-label">Produk Terlaris</div>
            <div class="kpi-value">{{ $topProducts[0]->product_name ?? '—' }}</div>
            <div class="kpi-sub">{{ $topProducts[0]->total_sold ?? 0 }} unit terjual</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Grafik Pendapatan Harian --}}
    <div class="col-xl-8">
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">📈 Pendapatan Harian (30 Hari Terakhir)</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.export.sales') }}" class="btn btn-sm btn-success rounded-pill px-3">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export CSV
                    </a>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="refreshCharts()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
            <canvas id="dailyRevenueChart"></canvas>
        </div>
    </div>

    {{-- Perbandingan Outlet --}}
    <div class="col-xl-4">
        <div class="chart-card h-100">
            <h6>🏪 Perbandingan Outlet (Bulan Ini)</h6>
            @if(count($outletCompare) > 0)
            <canvas id="outletCompareChart"></canvas>
            @else
            <div class="text-center text-muted py-4">
                <i class="bi bi-bar-chart fs-1 d-block mb-2"></i>
                Belum ada data
            </div>
            @endif
        </div>
    </div>

    {{-- Top Produk --}}
    <div class="col-xl-7">
        <div class="chart-card">
            <h6>🔥 Top 10 Produk Paling Laris</h6>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>

    {{-- Top Produk List --}}
    <div class="col-xl-5">
        <div class="chart-card h-100">
            <h6>🏆 Ranking Produk</h6>
            @foreach($topProducts as $i => $p)
            <div class="top-product-row">
                <div class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-n')) }}">
                    {{ $i + 1 }}
                </div>
                <div class="flex-1" style="flex:1;">
                    <div style="font-size:.85rem;font-weight:600;">{{ $p->product_name }}</div>
                    <div style="font-size:.75rem;color:#64748b;">{{ $p->category }}</div>
                </div>
                <div class="text-end">
                    <div style="font-size:.85rem;font-weight:700;color:#2563eb;">{{ $p->total_sold }} unit</div>
                    <div style="font-size:.72rem;color:#64748b;">Rp {{ number_format($p->total_revenue, 0, ',', '.') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const dailyData   = @json($dailyRevenue);
const outletData  = @json($outletCompare);
const topProducts = @json($topProducts);

const COLORS = ['#2563eb','#16a34a','#dc2626','#d97706','#7c3aed','#0891b2','#db2777','#059669'];

// ── 1. Pendapatan Harian ────────────────────────────────────────────
const chartDaily = new Chart(document.getElementById('dailyRevenueChart'), {
    type: 'line',
    data: {
        labels: dailyData.labels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: dailyData.revenues,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,.08)',
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => 'Rp ' + Math.round(ctx.raw).toLocaleString('id-ID') } }
        },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000) + 'K' }, grid: { color: '#f0f4f8' } },
            x: { grid: { display: false } }
        }
    }
});

// ── 2. Perbandingan Outlet ──────────────────────────────────────────
@if(count($outletCompare) > 0)
new Chart(document.getElementById('outletCompareChart'), {
    type: 'doughnut',
    data: {
        labels: outletData.map(o => o.outlet_name),
        datasets: [{
            data: outletData.map(o => o.revenue),
            backgroundColor: COLORS,
            borderWidth: 3,
            borderColor: '#fff',
            hoverBorderWidth: 4,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 } } },
            tooltip: { callbacks: { label: ctx => ctx.label + ': Rp ' + Math.round(ctx.raw).toLocaleString('id-ID') } }
        }
    }
});
@endif

// ── 3. Top Produk (Horizontal Bar) ─────────────────────────────────
new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: topProducts.map(p => p.product_name),
        datasets: [{
            label: 'Total Terjual (unit)',
            data: topProducts.map(p => p.total_sold),
            backgroundColor: COLORS,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f0f4f8' } },
            y: { grid: { display: false }, ticks: { font: { size: 12 } } }
        }
    }
});

// ── Auto-refresh setiap 60 detik ────────────────────────────────────
async function refreshCharts() {
    try {
        const res  = await fetch('{{ route("reports.live") }}');
        const data = await res.json();
        chartDaily.data.labels   = data.daily_revenue.labels;
        chartDaily.data.datasets[0].data = data.daily_revenue.revenues;
        chartDaily.update('active');
    } catch(e) { console.error('Refresh error:', e); }
}
setInterval(refreshCharts, 60000);
</script>
@endpush
