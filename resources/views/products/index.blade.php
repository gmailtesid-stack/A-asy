@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('breadcrumb')
    <li class="breadcrumb-item active">Produk</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 fw-bold text-dark">Manajemen Produk</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Produk</th>
                        <th>Kategori</th>
                        <th>SKU</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $product->image ?? 'https://placehold.co/100?text=No+Image' }}" 
                                     alt="{{ $product->name }}" class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->unit }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name }}</td>
                        <td><code class="text-primary">{{ $product->sku }}</code></td>
                        <td class="fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Non-aktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-25"></i>
                            Belum ada data produk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white py-3 border-top-0">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
