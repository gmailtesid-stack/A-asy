@extends('layouts.app')

@section('title', 'Input Fisik Opname - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Input Data Fisik: {{ $stockOpname->opname_number }}</h2>
            <p class="text-muted">Gudang: {{ $stockOpname->warehouse->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('stock_opnames.approve', $stockOpname) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-check2-all me-2"></i> Setujui & Sesuaikan Stok
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('stock_opnames.update', $stockOpname) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small uppercase">
                            <tr>
                                <th class="ps-4 py-3">Produk</th>
                                <th class="py-3 text-center">{{ !$stockOpname->is_blind ? 'Stok Sistem' : 'Status' }}</th>
                                <th class="py-3 text-center" style="width: 200px;">Stok Fisik</th>
                                @if(!$stockOpname->is_blind)
                                <th class="py-3 text-center">Selisih</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockOpname->items as $index => $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $item->product->name }}</div>
                                    <code class="small text-muted">{{ $item->product->sku }}</code>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                </td>
                                <td class="text-center">
                                    @if(!$stockOpname->is_blind)
                                        <span class="fw-bold text-muted">{{ $item->recorded_quantity }}</span>
                                    @else
                                        @if($item->verification_status === 'pending')
                                            <span class="badge bg-secondary rounded-pill">Belum Dihitung</span>
                                        @elseif($item->verification_status === 'recount')
                                            <span class="badge bg-warning text-dark rounded-pill">Hitung Ulang!</span>
                                        @else
                                            <span class="badge bg-success rounded-pill">Selesai</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input type="number" name="items[{{ $index }}][physical_quantity]" 
                                           class="form-control text-center rounded-pill border-light shadow-sm" 
                                           placeholder="Scan/Input Jumlah..."
                                           value="">
                                </td>
                                @if(!$stockOpname->is_blind)
                                <td class="text-center fw-bold {{ $item->adjustment_quantity < 0 ? 'text-danger' : ($item->adjustment_quantity > 0 ? 'text-success' : '') }}">
                                    {{ $item->adjustment_quantity > 0 ? '+' : '' }}{{ $item->adjustment_quantity }}
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Simpan Perubahan Fisik</button>
            </div>
        </div>
    </form>
</div>
@endsection
