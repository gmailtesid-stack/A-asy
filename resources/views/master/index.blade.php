@extends('layouts.app')

@section('title', 'Pusat Data SKU - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item active">Master Data</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Pusat Data SKU</h2>
            <p class="text-muted">Kelola informasi produk, merek, kategori, dan pemasok Anda.</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Produk SKU --}}
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $stats['products'] }} Items</span>
                    </div>
                    <h5 class="fw-bold mb-1">Produk (SKU)</h5>
                    <p class="text-muted small mb-4">Daftar item unik dengan kode SKU dan harga.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary w-100 rounded-pill">
                        Kelola Produk <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Brands --}}
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-success bg-opacity-10 text-success">
                            <i class="bi bi-tags fs-4"></i>
                        </div>
                        <span class="badge bg-success rounded-pill">{{ $stats['brands'] }} Brand</span>
                    </div>
                    <h5 class="fw-bold mb-1">Merek (Brand)</h5>
                    <p class="text-muted small mb-4">Manajemen merek atau produsen produk.</p>
                    <a href="{{ route('brands.index') }}" class="btn btn-outline-success w-100 rounded-pill">
                        Kelola Merek <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-grid fs-4"></i>
                        </div>
                        <span class="badge bg-warning rounded-pill">{{ $stats['categories'] }} Kategori</span>
                    </div>
                    <h5 class="fw-bold mb-1">Kategori</h5>
                    <p class="text-muted small mb-4">Pengelompokan produk berdasarkan jenis.</p>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-warning w-100 rounded-pill">
                        Kelola Kategori <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Suppliers --}}
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="brand-logo bg-info bg-opacity-10 text-info">
                            <i class="bi bi-truck fs-4"></i>
                        </div>
                        <span class="badge bg-info rounded-pill">{{ $stats['suppliers'] }} Supplier</span>
                    </div>
                    <h5 class="fw-bold mb-1">Pemasok (Supplier)</h5>
                    <p class="text-muted small mb-4">Data vendor dan supplier untuk pengadaan barang.</p>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-info w-100 rounded-pill">
                        Kelola Supplier <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity / Recent SKU --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Baru Saja Ditambahkan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th>SKU</th>
                                    <th>Brand</th>
                                    <th>Kategori</th>
                                    <th>Harga Jual</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentProducts = \App\Models\Product::with(['brand', 'category'])->latest()->take(5)->get();
                                @endphp
                                @forelse($recentProducts as $product)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $product->image_url }}" alt="" class="rounded-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <span class="fw-semibold">{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td><code>{{ $product->sku }}</code></td>
                                    <td>{{ $product->brand?->name ?? '-' }}</td>
                                    <td>{{ $product->category?->name ?? '-' }}</td>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="text-end pe-4">
                                        @if(auth()->user()->hasPermission('manage-master-data'))
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-light rounded-pill">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        @else
                                        <button class="btn btn-sm btn-light rounded-pill">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data produk.</td>
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
@endsection
