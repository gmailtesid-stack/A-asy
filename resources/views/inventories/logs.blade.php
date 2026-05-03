@extends('layouts.app')

@section('title', 'Log Pergerakan Stok - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Movement Log (Pergerakan Stok)</h2>
            <p class="text-muted">Riwayat lengkap mutasi barang, penyesuaian, dan transaksi logistik.</p>
        </div>
        <a href="{{ route('inventories.index') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-box-seam me-2"></i> Lihat Stok Saat Ini
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal & Waktu</th>
                            <th>Produk</th>
                            <th>Gudang</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Perubahan</th>
                            <th class="text-center">Stok Akhir</th>
                            <th>Referensi / Catatan</th>
                            <th class="text-end pe-4">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold small">{{ $log->inventory->product->name }}</div>
                                <code class="small text-muted">{{ $log->inventory->product->sku }}</code>
                            </td>
                            <td><span class="badge bg-light text-dark border small">{{ $log->inventory->warehouse->name }}</span></td>
                            <td class="text-center">
                                @php
                                    $typeClass = [
                                        'in' => 'bg-success',
                                        'out' => 'bg-primary',
                                        'adjustment' => 'bg-warning',
                                    ][$log->type] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $typeClass }} rounded-pill small">{{ strtoupper($log->type) }}</span>
                            </td>
                            <td class="text-center fw-bold {{ $log->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                            </td>
                            <td class="text-center fw-bold">{{ $log->quantity_after }}</td>
                            <td>
                                <div class="small">{{ $log->reference ?? '-' }}</div>
                                <div class="text-muted small italic">{{ $log->notes }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="fw-bold small">{{ $log->user->name }}</div>
                                <div class="text-muted small">ID: {{ $log->user_id }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat pergerakan stok.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white py-3 border-top border-light">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
