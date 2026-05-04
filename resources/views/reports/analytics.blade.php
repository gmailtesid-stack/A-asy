@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-white mb-1"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Advanced Analytics</h2>
            <p class="text-white-50">Wawasan data mendalam untuk pengambilan keputusan strategis.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8">
            <div class="card bg-glass border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-bottom border-white-10 p-4">
                    <h5 class="mb-0 text-white fw-bold">Tren Penjualan (14 Hari Terakhir)</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="salesTrendChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Predictive Stock Alerts -->
        <div class="col-lg-4">
            <div class="card bg-glass border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-bottom border-white-10 p-4">
                    <h5 class="mb-0 text-white fw-bold">Prediksi Stok Habis</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush bg-transparent">
                        @forelse($predictions as $p)
                        <div class="list-group-item bg-transparent border-white-10 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-white fw-bold small">{{ $p['product_name'] }}</span>
                                <span class="badge bg-{{ $p['status'] == 'critical' ? 'danger' : 'warning' }}-soft">
                                    {{ $p['days_left'] }} Hari Lagi
                                </span>
                            </div>
                            <div class="progress bg-white-10" style="height: 6px;">
                                <div class="progress-bar bg-{{ $p['status'] == 'critical' ? 'danger' : 'warning' }}" 
                                     role="progressbar" 
                                     style="width: {{ max(10, 100 - ($p['days_left'] * 10)) }}%"></div>
                            </div>
                            <small class="text-white-50 mt-2 d-block">Rata-rata penjualan: {{ $p['avg_sales'] }} unit/hari</small>
                        </div>
                        @empty
                        <div class="p-5 text-center text-white-50">
                            <i class="bi bi-shield-check fs-1 mb-2 d-block"></i>
                            Stok aman untuk 7 hari ke depan.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Top Products Table -->
        <div class="col-lg-12">
            <div class="card bg-glass border-0 shadow-lg">
                <div class="card-header bg-transparent border-bottom border-white-10 p-4">
                    <h5 class="mb-0 text-white fw-bold">Top 10 Produk Terlaris</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah Terjual</th>
                                    <th class="text-end">Total Omset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $tp)
                                <tr>
                                    <td class="fw-bold">{{ $tp->product_name }}</td>
                                    <td><span class="badge bg-primary-soft">{{ $tp->category }}</span></td>
                                    <td class="text-center fw-bold text-info">{{ number_format($tp->total_sold) }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyRevenue['labels']) !!},
            datasets: [{
                label: 'Omset Penjualan',
                data: {!! json_encode($dailyRevenue['revenues']) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: 'rgba(255, 255, 255, 0.5)', callback: (v) => 'Rp ' + v.toLocaleString('id-ID') }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(255, 255, 255, 0.5)' }
                }
            }
        }
    });
</script>

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
        padding: 1rem;
    }
    .table-dark-custom td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem;
    }
    .bg-white-10 { background: rgba(255, 255, 255, 0.1) !important; }
    .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
    .bg-primary-soft { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
    .bg-danger-soft { background: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .bg-warning-soft { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
</style>
@endsection
