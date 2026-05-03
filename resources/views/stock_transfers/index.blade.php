@extends('layouts.app')

@section('title', 'Stock Transfer - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item active">Stock Transfer</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1 fw-800 text-dark">Perpindahan Stok (Transfer)</h2>
            <p class="text-muted small mb-0">Kelola distribusi barang antar gudang dan outlet secara sistematis.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock_transfers.create') }}" class="btn btn-primary shadow-sm px-4 fw-bold">
                <i class="bi bi-plus-lg me-2"></i> Buat Transfer Baru
            </a>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3">No. Transfer / Tanggal</th>
                            <th class="py-3">Dari Gudang</th>
                            <th class="py-3">Ke Gudang</th>
                            <th class="py-3">Operator</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($transfers as $transfer)
                        <tr class="bg-white border-bottom">
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-primary" style="font-size: 0.95rem;">{{ $transfer->transfer_number }}</div>
                                <div class="text-muted small">{{ $transfer->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0">{{ $transfer->fromWarehouse->name }}</div>
                                <span class="text-muted small">ID: #WH-{{ $transfer->from_warehouse_id }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0">{{ $transfer->toWarehouse->name }}</div>
                                <span class="text-muted small">ID: #WH-{{ $transfer->to_warehouse_id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.6rem;">
                                        {{ strtoupper(substr($transfer->user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-dark">{{ $transfer->user->name }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusConfig = [
                                        'draft' => ['color' => 'secondary', 'icon' => 'bi-pencil'],
                                        'pending' => ['color' => 'warning', 'icon' => 'bi-clock'],
                                        'transit' => ['color' => 'info', 'icon' => 'bi-truck'],
                                        'received' => ['color' => 'success', 'icon' => 'bi-check-circle'],
                                        'cancelled' => ['color' => 'danger', 'icon' => 'bi-x-circle']
                                    ][$transfer->status] ?? ['color' => 'secondary', 'icon' => 'bi-circle'];
                                @endphp
                                <span class="badge bg-{{ $statusConfig['color'] }}-subtle text-{{ $statusConfig['color'] }} border border-{{ $statusConfig['color'] }}-subtle rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi {{ $statusConfig['icon'] }} me-1"></i> {{ strtoupper($transfer->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('stock_transfers.show', $transfer) }}" class="btn btn-sm btn-light border px-3 rounded-pill">Detail</a>
                                    
                                    @if($transfer->status == 'pending')
                                    <form action="{{ route('stock_transfers.ship', $transfer) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold border-0">Kirim (Ship)</button>
                                    </form>
                                    @endif

                                    @if($transfer->status == 'transit')
                                    <form action="{{ route('stock_transfers.receive', $transfer) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold border-0">Terima (Receive)</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-arrow-left-right fs-1 d-block mb-3 opacity-25 text-primary"></i>
                                    <h5 class="text-muted fw-bold">Belum ada histori transfer</h5>
                                    <p class="text-muted small">Seluruh perpindahan stok antar lokasi akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transfers->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">Menampilkan {{ $transfers->firstItem() }} - {{ $transfers->lastItem() }} dari {{ $transfers->total() }} transfer</span>
            {{ $transfers->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .table>tbody>tr>td { vertical-align: middle; padding-top: 15px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
</style>
@endsection
