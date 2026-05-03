@extends('layouts.app')

@section('title', 'Manajemen Inventori')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inventori</li>
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="h3 mb-1 fw-800 text-dark">Stok Inventori</h2>
        <p class="text-muted small mb-0">Monitor dan perbarui ketersediaan stok produk di setiap outlet.</p>
    </div>
</div>

<div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-subtle">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase fs-xs fw-800 text-muted">Produk</th>
                        <th class="py-3 text-uppercase fs-xs fw-800 text-muted">Outlet & Gudang</th>
                        <th class="py-3 text-uppercase fs-xs fw-800 text-muted">Kategori</th>
                        <th class="py-3 text-uppercase fs-xs fw-800 text-muted">Lokasi Spesifik</th>
                        <th class="text-center py-3 text-uppercase fs-xs fw-800 text-muted">Stok Saat Ini</th>
                        <th class="text-center py-3 text-uppercase fs-xs fw-800 text-muted">Status</th>
                        <th class="text-end pe-4 py-3 text-uppercase fs-xs fw-800 text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inventory)
                    <tr class="transition-all">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark mb-0" style="font-size: .95rem;">{{ $inventory->product->name }}</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <code class="bg-light text-muted px-2 py-0 rounded" style="font-size: .75rem;">{{ $inventory->product->sku }}</code>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size: .85rem;">{{ $inventory->outlet->name }}</div>
                            <div class="text-muted small"><i class="bi bi-building me-1"></i> {{ $inventory->warehouse->name }}</div>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: .85rem;">{{ $inventory->product->category->name }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border rounded-pill">{{ $inventory->location->name ?? 'Belum Ditentukan' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="fw-800 {{ $inventory->isLowStock() ? 'text-danger' : 'text-dark' }} fs-5">
                                {{ $inventory->quantity }}
                                <span class="text-muted fw-normal" style="font-size:.7rem;">{{ $inventory->product->unit }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($inventory->isLowStock())
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Menipis
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                    <i class="bi bi-check-circle-fill me-1"></i> Aman
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if(auth()->user()->hasPermission('manage-stock-adjustment'))
                            <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                                <i class="bi bi-plus-slash-minus me-1"></i> Update
                            </a>
                            @else
                            <span class="text-muted small">View Only</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-archive fs-1 d-block mb-3 opacity-25 text-primary"></i>
                                <h5 class="text-muted fw-bold">Data inventori kosong</h5>
                                <p class="text-muted small">Hubungi Super Admin jika produk belum terdistribusi ke outlet ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($inventories->hasPages())
    <div class="card-footer bg-white py-3 border-top border-light">
        {{ $inventories->links() }}
    </div>
    @endif
</div>

<style>
    .fs-xs { font-size: .7rem; }
    .fw-600 { font-weight: 600; }
    .fw-800 { font-weight: 800; }
    .transition-all:hover { background: rgba(248, 250, 252, 0.8); }
</style>
@endsection
