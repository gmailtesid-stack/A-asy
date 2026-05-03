@extends('layouts.app')

@section('title', 'Master Katalog - E-ASY OMS')

@section('breadcrumb')
    <li class="breadcrumb-item active">Master Katalog</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    
    {{-- Header Layout --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="h4 mb-0 fw-800 text-dark text-uppercase" style="letter-spacing: 0.05em;">Katalog Produk</h2>
                <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                <span class="text-primary fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.05em;">{{ strtoupper(str_replace('_', ' ', $status)) }}</span>
            </div>
            <p class="text-muted small mb-0">Kelola inventaris, varian, dan harga produk multikanal secara terpusat.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light px-3 py-2 shadow-sm border border-secondary-subtle">
                <i class="bi bi-download me-2"></i> Ekspor
            </button>
            <a href="{{ route('products.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-2"></i> Tambah Produk
            </a>
        </div>
    </div>

    {{-- Tabs (Anchanto Style) --}}
    <div class="d-flex flex-wrap gap-4 border-bottom pb-2 mb-4" style="font-size: 0.9rem; font-weight: 600;">
        <a href="{{ route('products.index', ['status' => 'all']) }}" class="text-decoration-none pb-2 {{ $status === 'all' ? 'text-primary border-bottom border-primary border-2' : 'text-muted' }}">
            Semua Produk <span class="badge {{ $status === 'all' ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }} rounded-pill ms-1">{{ $metrics['all'] }}</span>
        </a>
        <a href="{{ route('products.index', ['status' => 'live']) }}" class="text-decoration-none pb-2 {{ $status === 'live' ? 'text-primary border-bottom border-primary border-2' : 'text-muted' }}">
            Live <span class="badge {{ $status === 'live' ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }} rounded-pill ms-1">{{ $metrics['live'] }}</span>
        </a>
        <a href="{{ route('products.index', ['status' => 'under_review']) }}" class="text-decoration-none pb-2 {{ $status === 'under_review' ? 'text-primary border-bottom border-primary border-2' : 'text-muted' }}">
            Dalam Ulasan <span class="badge {{ $status === 'under_review' ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }} rounded-pill ms-1">{{ $metrics['under_review'] }}</span>
        </a>
        <a href="{{ route('products.index', ['status' => 'draft']) }}" class="text-decoration-none pb-2 {{ $status === 'draft' ? 'text-primary border-bottom border-primary border-2' : 'text-muted' }}">
            Draft <span class="badge {{ $status === 'draft' ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }} rounded-pill ms-1">{{ $metrics['draft'] }}</span>
        </a>
        <a href="{{ route('products.index', ['status' => 'failed']) }}" class="text-decoration-none pb-2 {{ $status === 'failed' ? 'text-primary border-bottom border-primary border-2' : 'text-muted' }}">
            Gagal <span class="badge {{ $status === 'failed' ? 'bg-danger-subtle text-danger' : 'bg-light text-muted' }} rounded-pill ms-1">{{ $metrics['failed'] }}</span>
        </a>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        {{-- Table Toolbar --}}
        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari SKU atau Nama Produk...">
                </div>
                <button class="btn btn-sm btn-light border"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary">Kit Product</button>
                <button class="btn btn-sm btn-outline-secondary">Variant Product</button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                            <th class="py-3">Image</th>
                            <th class="py-3">ISKU</th>
                            <th class="py-3">CSKU</th>
                            <th class="py-3">Product Name</th>
                            <th class="py-3">Last Updated</th>
                            <th class="py-3 text-end">Stock</th>
                            <th class="py-3 text-end">Price</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="text-end pe-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($products as $product)
                        <tr class="transition-all bg-white border-bottom">
                            <td class="ps-4"><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <img src="{{ $product->image_url }}" alt="" class="rounded border" style="width: 40px; height: 40px; object-fit: cover; background: #f8fafc;" onerror="this.src='https://placehold.co/100x100?text=IMG'">
                            </td>
                            <td><span class="text-primary fw-bold">{{ $product->sku }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-info fw-bold">{{ $product->csku ?? $product->sku }}</span>
                                    @if($product->type === 'variant')
                                    <span class="badge bg-secondary-subtle text-secondary">+1</span>
                                    @endif
                                </div>
                            </td>
                            <td class="fw-500 text-dark">{{ $product->name }}</td>
                            <td class="text-muted">{{ $product->updated_at->format('M d, g:i a') }}</td>
                            <td class="text-end fw-bold">{{ $product->inventories()->sum('quantity') ?? 0 }}</td>
                            <td class="text-end fw-bold text-dark">Rp {{ number_format($product->price, 0, ',', '.') }} <i class="bi bi-pencil ms-1 text-primary" style="font-size: 0.7rem; cursor: pointer;"></i></td>
                            <td class="text-center">
                                @if($product->status === 'live')
                                    <span class="d-inline-flex align-items-center gap-2 fw-bold text-success" style="font-size: 0.8rem;">
                                        <span class="rounded-circle bg-success" style="width: 8px; height: 8px;"></span> Live
                                    </span>
                                @elseif($product->status === 'draft')
                                    <span class="d-inline-flex align-items-center gap-2 fw-bold text-secondary" style="font-size: 0.8rem;">
                                        <span class="rounded-circle bg-secondary" style="width: 8px; height: 8px;"></span> Draft
                                    </span>
                                @elseif($product->status === 'under_review')
                                    <span class="d-inline-flex align-items-center gap-2 fw-bold text-warning" style="font-size: 0.8rem;">
                                        <span class="rounded-circle bg-warning" style="width: 8px; height: 8px;"></span> Review
                                    </span>
                                @else
                                    <span class="d-inline-flex align-items-center gap-2 fw-bold text-danger" style="font-size: 0.8rem;">
                                        <span class="rounded-circle bg-danger" style="width: 8px; height: 8px;"></span> Failed
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('products.edit', $product) }}" class="text-primary text-decoration-none fw-bold me-3">Edit <i class="bi bi-chevron-right ms-1" style="font-size: 0.6rem;"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-25 text-primary"></i>
                                    <h5 class="text-muted fw-bold">Belum ada data produk</h5>
                                    <p class="text-muted small">Silakan tambahkan produk baru pada status ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }}</span>
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<style>
    .fw-500 { font-weight: 500; }
    .fw-800 { font-weight: 800; }
    .transition-all:hover { background-color: #f8fafc !important; }
    .table>tbody>tr>td { vertical-align: middle; padding-top: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
</style>
@endsection
