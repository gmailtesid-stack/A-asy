@extends('layouts.app')

@section('title', 'Dashboard Real-time - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h2 class="fw-800 mb-1">Logistik <span class="text-gradient">Overview</span></h2>
            <p class="text-muted">Pantau pergerakan stok, pengadaan, dan pengiriman secara real-time.</p>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-white border-0 shadow-sm rounded-pill px-4" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-2"></i> Refresh Data
            </button>
            <a href="{{ route('reports.wms') }}" class="btn btn-premium px-4">
                <i class="bi bi-graph-up me-2"></i> Laporan Detail
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="bg-white bg-opacity-20 rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-white text-opacity-80 mb-2 text-uppercase small fw-800" style="letter-spacing: 0.1em;">Total Nilai Stok</h6>
                    <h3 class="fw-800 mb-0">Rp {{ number_format($stats['total_stock_value'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-800" style="letter-spacing: 0.1em;">Stok Menipis (Low)</h6>
                    <h3 class="fw-800 mb-0 text-danger">{{ $stats['low_stock_count'] }} <small class="text-muted fw-normal" style="font-size: 0.9rem;">Items</small></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-cart-plus-fill fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-800" style="letter-spacing: 0.1em;">PO Pending</h6>
                    <h3 class="fw-800 mb-0 text-primary">{{ $stats['pending_po'] }} <small class="text-muted fw-normal" style="font-size: 0.9rem;">Orders</small></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-search fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-800" style="letter-spacing: 0.1em;">Picking Failures</h6>
                    <h3 class="fw-800 mb-0 text-warning">{{ $stats['picking_failures'] }} <small class="text-muted fw-normal" style="font-size: 0.9rem;">Issues</small></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Inventory Chart --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Tren Stok & Pergerakan</h5>
                    <select class="form-select form-select-sm w-auto rounded-pill">
                        <option>7 Hari Terakhir</option>
                        <option>30 Hari Terakhir</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="stockChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Aktivitas Gudang</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivity as $activity)
                        <div class="list-group-item border-0 px-4 py-3 bg-transparent">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle mt-1" style="width: 10px; height: 10px; background: {{ $activity->type == 'in' ? '#10b981' : ($activity->type == 'out' ? '#6366f1' : '#f59e0b') }}; box-shadow: 0 0 10px {{ $activity->type == 'in' ? '#10b981' : ($activity->type == 'out' ? '#6366f1' : '#f59e0b') }};"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">{{ strtoupper($activity->type) }}</span>
                                        <span class="text-muted" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
                                    </div>
                                    <p class="mb-1 small">
                                        <span class="fw-800 text-dark">{{ $activity->product_name }}</span> 
                                        {{ $activity->type == 'in' ? 'bertambah' : ($activity->type == 'out' ? 'berkurang' : 'diatur') }} 
                                         <span class="fw-800 {{ $activity->type == 'in' ? 'text-success' : ($activity->type == 'out' ? 'text-primary' : 'text-warning') }}">{{ abs($activity->quantity_change) }}</span> unit.
                                    </p>
                                    <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.65rem;">
                                        <i class="bi bi-person-circle"></i> {{ $activity->user_name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted">Belum ada aktivitas.</div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center py-4">
                    <a href="{{ route('inventories.logs') }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold">
                        Lihat Semua Log <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock Alerts Section --}}
    <div class="row mt-4 animate__animated animate__fadeInUp">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-octagon me-2"></i> Low Stock Alerts</h5>
                    <span class="badge bg-danger rounded-pill">{{ $stats['low_stock_count'] }} Items</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-800 text-muted" style="letter-spacing: 0.1em;">Produk</th>
                                    <th class="py-3 text-uppercase small fw-800 text-muted" style="letter-spacing: 0.1em;">Gudang</th>
                                    <th class="py-3 text-center text-uppercase small fw-800 text-muted" style="letter-spacing: 0.1em;">Sisa Stok</th>
                                    <th class="py-3 text-center text-uppercase small fw-800 text-muted" style="letter-spacing: 0.1em;">Min. Stok</th>
                                    <th class="py-3 text-end pe-4 text-uppercase small fw-800 text-muted" style="letter-spacing: 0.1em;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $lowStockItems = \App\Models\Inventory::with(['product', 'warehouse'])
                                        ->whereColumn('quantity', '<', 'min_quantity')
                                        ->latest()
                                        ->take(5)
                                        ->get();
                                @endphp
                                @forelse($lowStockItems as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-800 text-dark">{{ $item->product->name }}</div>
                                        <code class="text-muted small" style="font-size: 0.7rem;">{{ $item->product->sku }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border-0 px-3 py-2 rounded-3 small fw-700">{{ $item->warehouse->name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-800">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-center text-muted fw-bold">{{ $item->min_quantity }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('inbound.create', ['product_id' => $item->product_id]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                            Restock PO
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted italic">Semua stok dalam kondisi aman.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('stockChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Stok Masuk',
                data: [12, 19, 3, 5, 2, 3, 10],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Stok Keluar',
                data: [8, 11, 5, 8, 3, 7, 12],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, font: { family: 'Plus Jakarta Sans' } } }
            },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
@endsection
