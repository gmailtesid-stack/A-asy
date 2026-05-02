@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('breadcrumb')
    <li class="breadcrumb-item active">Produk</li>
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate__animated animate__fadeIn">
    <div>
        <h2 class="h3 mb-1 fw-800 text-dark">Manajemen Produk</h2>
        <p class="text-muted small mb-0">Kelola katalog barang dan ketersediaan produk di seluruh outlet.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-bold">
        <i class="bi bi-plus-lg me-2"></i> Tambah Produk Baru
    </a>
</div>

<div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-subtle">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase fs-xs fw-800 text-muted">Info Produk</th>
                        <th class="py-3 text-uppercase fs-xs fw-800 text-muted">Kategori</th>
                        <th class="py-3 text-uppercase fs-xs fw-800 text-muted">SKU / Kode</th>
                        <th class="py-3 text-uppercase fs-xs fw-800 text-muted">Harga Jual</th>
                        <th class="py-3 text-uppercase fs-xs fw-800 text-muted">Status</th>
                        <th class="text-end pe-4 py-3 text-uppercase fs-xs fw-800 text-muted">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="transition-all">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->name }}" 
                                         class="rounded-3 shadow-xs border" 
                                         style="width: 52px; height: 52px; object-fit: cover;"
                                         onerror="this.src='https://placehold.co/100x100?text=IMG'">
                                </div>
                                <div>
                                    <div class="fw-bold text-dark mb-0" style="font-size: .95rem;">{{ $product->name }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill" style="font-size: .65rem;">{{ $product->unit }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark fw-500">{{ $product->category->name }}</span>
                        </td>
                        <td>
                            <code class="bg-primary-subtle text-primary px-2 py-1 rounded fw-bold" style="font-size: .8rem;">{{ $product->sku }}</code>
                        </td>
                        <td>
                            <div class="fw-800 text-dark">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Non-aktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle p-2" title="Edit">
                                    <i class="bi bi-pencil-fill fs-6"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2" title="Hapus">
                                        <i class="bi bi-trash3-fill fs-6"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-25 text-primary"></i>
                                <h5 class="text-muted fw-bold">Belum ada produk</h5>
                                <p class="text-muted small">Silakan tambahkan produk pertama Anda untuk mulai berjualan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white py-3 border-top border-light">
        {{ $products->links() }}
    </div>
    @endif
</div>

<style>
    .fs-xs { font-size: .7rem; }
    .fw-800 { font-weight: 800; }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,.04); }
    .transition-all:hover { background: rgba(248, 250, 252, 0.8); }
</style>
@endsection
