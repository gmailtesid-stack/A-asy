@extends('layouts.app')

@section('title', 'Manajemen Inbound - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inbound</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Inbound (Barang Masuk)</h2>
            <p class="text-muted">Kelola Purchase Order (PO) dan penerimaan barang (GRN).</p>
        </div>
        <a href="{{ route('inbound.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Buat PO Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No. PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Gudang Tujuan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pos as $po)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $po->po_number }}</td>
                            <td>{{ $po->created_at->format('d/m/Y') }}</td>
                            <td>{{ $po->supplier->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $po->warehouse->name }}</span></td>
                            <td>Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'draft' => 'bg-secondary',
                                        'ordered' => 'bg-warning',
                                        'partially_received' => 'bg-info',
                                        'received' => 'bg-success',
                                        'cancelled' => 'bg-danger'
                                    ][$po->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }} rounded-pill">{{ ucfirst(str_replace('_', ' ', $po->status)) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light rounded-pill px-3 me-2">Detail</button>
                                    @if($po->status == 'pending' && auth()->user()->hasPermission('confirm-po'))
                                    <form action="{{ route('inbound.confirm', $po) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-warning rounded-pill px-3 me-2">Konfirmasi PO</button>
                                    </form>
                                    @endif
                                    @if($po->status == 'confirmed' && auth()->user()->hasPermission('create-grn'))
                                    <a href="{{ route('inbound.receive', $po) }}" class="btn btn-sm btn-primary rounded-pill px-3">Terima Barang</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/blue/abstract-art-4.svg" alt="" style="width: 150px;" class="mb-3">
                                <p class="text-muted">Belum ada Purchase Order.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pos->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $pos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
