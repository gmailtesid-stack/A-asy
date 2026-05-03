@extends('layouts.app')

@section('title', 'Manajemen Inventori - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inventori</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1 fw-800 text-dark">Stok Real-time</h2>
            <p class="text-muted small mb-0">Pantau ketersediaan produk di seluruh gudang dan lokasi penyimpanan secara akurat.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light border shadow-sm px-3"><i class="bi bi-download me-2"></i> Ekspor Stok</button>
            <button class="btn btn-primary shadow-sm px-4 fw-bold"><i class="bi bi-arrow-repeat me-2"></i> Sync Stok</button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari Produk atau SKU...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select border-secondary-subtle">
                        <option selected>Semua Gudang</option>
                        @foreach(\App\Models\Warehouse::all() as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select border-secondary-subtle">
                        <option selected>Semua Status Stok</option>
                        <option>Aman (In Stock)</option>
                        <option>Menipis (Low Stock)</option>
                        <option>Habis (Out of Stock)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-dark w-100"><i class="bi bi-funnel me-2"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3">Produk</th>
                            <th class="py-3">Gudang & Lokasi</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3 text-center">Tersedia</th>
                            <th class="py-3 text-center">Status Stok</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($inventories as $inventory)
                        <tr class="bg-white border-bottom">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $inventory->product->name }}</div>
                                        <code class="text-primary small fw-bold">{{ $inventory->product->sku }}</code>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0">{{ $inventory->warehouse->name }}</div>
                                <span class="badge bg-secondary-subtle text-secondary px-2 rounded-pill" style="font-size: 0.65rem;">
                                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $inventory->location->name ?? 'Default' }}
                                </span>
                            </td>
                            <td><span class="text-muted">{{ $inventory->product->category->name }}</span></td>
                            <td class="text-center">
                                <div class="fw-800 {{ $inventory->isLowStock() ? 'text-danger' : 'text-dark' }} fs-5">
                                    {{ $inventory->quantity }}
                                    <span class="text-muted fw-normal" style="font-size: 0.7rem;">{{ $inventory->product->unit }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($inventory->quantity <= 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                                        <span class="rounded-circle bg-danger d-inline-block me-1" style="width: 6px; height: 6px;"></span> Habis
                                    </span>
                                @elseif($inventory->isLowStock())
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                                        <span class="rounded-circle bg-warning d-inline-block me-1" style="width: 6px; height: 6px;"></span> Menipis
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                        <span class="rounded-circle bg-success d-inline-block me-1" style="width: 6px; height: 6px;"></span> Aman
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border rounded-circle" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 0.85rem; border-radius: 12px;">
                                        <li><a class="dropdown-item py-2" href="{{ route('inventories.edit', $inventory) }}"><i class="bi bi-pencil-square me-2 text-primary"></i> Stock Adjustment</a></li>
                                        <li><a class="dropdown-item py-2" href="#"><i class="bi bi-arrow-left-right me-2 text-info"></i> Stock Transfer</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2 text-danger" href="#"><i class="bi bi-exclamation-circle me-2"></i> Mark Damaged</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-archive fs-1 d-block mb-3 opacity-25 text-primary"></i>
                                    <h5 class="text-muted fw-bold">Data inventori kosong</h5>
                                    <p class="text-muted small">Hubungi Super Admin jika produk belum terdistribusi ke gudang ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($inventories->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">Menampilkan {{ $inventories->firstItem() }} - {{ $inventories->lastItem() }} dari {{ $inventories->total() }} stok</span>
            {{ $inventories->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .table>tbody>tr>td { vertical-align: middle; padding-top: 15px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
    .transition-all:hover { background-color: #f8fafc !important; }
</style>
@endsection
