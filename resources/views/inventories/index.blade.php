@extends('layouts.app')

@section('title', 'Manajemen Inventori')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inventori</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 fw-bold text-dark">Stok Inventori</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Produk</th>
                        <th>Outlet</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok Saat Ini</th>
                        <th class="text-center">Status Stok</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inventory)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $inventory->product->name }}</div>
                            <small class="text-muted">SKU: {{ $inventory->product->sku }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $inventory->outlet->name }}</span>
                        </td>
                        <td>{{ $inventory->product->category->name }}</td>
                        <td class="text-center fw-bold fs-5">
                            {{ $inventory->quantity }} <small class="text-muted fw-normal" style="font-size:.7rem;">{{ $inventory->product->unit }}</small>
                        </td>
                        <td class="text-center">
                            @if($inventory->isLowStock())
                                <span class="badge bg-danger rounded-pill px-3">⚠️ Menipis</span>
                            @else
                                <span class="badge bg-success rounded-pill px-3">Normal</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-sm btn-primary shadow-sm">
                                <i class="bi bi-arrow-repeat me-1"></i> Update Stok
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Belum ada data inventori.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($inventories->hasPages())
    <div class="card-footer bg-white border-top-0">
        {{ $inventories->links() }}
    </div>
    @endif
</div>
@endsection
