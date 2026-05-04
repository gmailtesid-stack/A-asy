@extends('layouts.app')

@section('title', 'Stock Opname - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Stock Opname (Rekonsiliasi)</h2>
            <p class="text-muted">Kelola penyesuaian stok fisik vs sistem dengan alur persetujuan.</p>
        </div>
        <a href="{{ route('stock_opnames.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Mulai Opname Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small uppercase">
                        <tr>
                            <th class="ps-4 py-3">No. Opname / Tanggal</th>
                            <th class="py-3">Gudang</th>
                            <th class="py-3">Inisiator</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opnames as $op)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-primary">{{ $op->opname_number }}</div>
                                <div class="text-muted small">{{ $op->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>{{ $op->warehouse->name }}</td>
                            <td>{{ $op->user->name }}</td>
                            <td class="text-center">
                                @php
                                    $colors = ['pending' => 'warning', 'approved' => 'success', 'cancelled' => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $colors[$op->status] ?? 'secondary' }} rounded-pill px-3">
                                    {{ strtoupper($op->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($op->status == 'pending')
                                <a href="{{ route('stock_opnames.edit', $op) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Input Fisik</a>
                                @else
                                <button class="btn btn-sm btn-light border rounded-pill px-3">Detail</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
