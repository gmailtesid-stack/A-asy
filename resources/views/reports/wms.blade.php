@extends('layouts.app')

@section('title', 'Laporan Logistik (WMS) - E-ASY POS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Laporan Logistik & Inventori</h2>
            <p class="text-muted">Analisis status order, pergerakan stok tercepat, dan kendala picking.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-cash-stack me-2"></i> Laporan Penjualan
            </a>
            <button class="btn btn-white border rounded-pill px-4 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Status PO --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Status Purchase Orders (Inbound)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Jumlah Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($poStatus as $status)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($status->status) }}</span></td>
                                    <td class="text-end fw-bold">{{ $status->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status SO --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Status Sales Orders (Outbound)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Jumlah Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($soStatus as $status)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($status->status) }}</span></td>
                                    <td class="text-end fw-bold">{{ $status->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Movement --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Top 10 SKU (Pergerakan Teraktif)</h5>
                </div>
                <div class="card-body">
                    <canvas id="movementChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Picking Failures --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Kendala Picking (Not Found / Partial)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">SO #</th>
                                    <th>Produk</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-3">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pickingFailures as $fail)
                                <tr>
                                    <td class="ps-3 fw-bold small">{{ $fail->picking->salesOrder->order_number }}</td>
                                    <td>
                                        <div class="fw-bold small">{{ $fail->product->name }}</div>
                                        <code class="small">{{ $fail->product->sku }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill small">{{ strtoupper($fail->status) }}</span>
                                    </td>
                                    <td class="text-end pe-3 small text-muted">{{ $fail->created_at->format('d/m H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada kendala picking ditemukan.</td>
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
    const movementCtx = document.getElementById('movementChart').getContext('2d');
    const movementData = @json($topMovement);
    
    new Chart(movementCtx, {
        type: 'bar',
        data: {
            labels: movementData.map(m => m.name),
            datasets: [{
                label: 'Total Mutasi Stok',
                data: movementData.map(m => m.total_movement),
                backgroundColor: '#6366f1',
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
@endsection
