@extends('layouts.app')

@section('title', 'Picking Barang - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="{{ route('outbound.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Outbound
        </a>
        <h2 class="fw-bold mt-2">Proses Picking Barang</h2>
        <p class="text-muted">Mengambil barang dari gudang untuk SO: <span class="fw-bold text-success">{{ $so->so_number }}</span></p>
    </div>

    <form action="{{ route('outbound.picking.store', $so) }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Produk & Lokasi</th>
                                        <th class="text-center">Jumlah Diminta</th>
                                        <th class="text-center">Jumlah Ditemukan</th>
                                        <th>Status Picking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($so->items as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                            <div class="fw-bold">{{ $item->product->name }}</div>
                                            <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                            <div class="mt-1">
                                                @php
                                                    $inventory = \App\Models\Inventory::where(['warehouse_id' => $so->warehouse_id, 'product_id' => $item->product_id])->first();
                                                @endphp
                                                <span class="badge bg-light text-primary border">Lokasi: {{ $inventory?->location?->name ?? 'Belum Diatur' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold fs-5">{{ $item->quantity }}</td>
                                        <td style="width: 150px;">
                                            <input type="number" name="items[{{ $index }}][quantity_found]" 
                                                   class="form-control rounded-3 text-center" 
                                                   value="{{ $item->quantity }}" min="0" max="{{ $item->quantity }}">
                                        </td>
                                        <td>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="items[{{ $index }}][status]" value="found" id="found_{{ $index }}" checked>
                                                <label class="btn btn-outline-success btn-sm" for="found_{{ $index }}">Found</label>

                                                <input type="radio" class="btn-check" name="items[{{ $index }}][status]" value="partial" id="partial_{{ $index }}">
                                                <label class="btn btn-outline-warning btn-sm" for="partial_{{ $index }}">Partial</label>

                                                <input type="radio" class="btn-check" name="items[{{ $index }}][status]" value="not_found" id="notfound_{{ $index }}">
                                                <label class="btn btn-outline-danger btn-sm" for="notfound_{{ $index }}">Not Found</label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-4 border-top d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i> Pastikan barang fisik sesuai dengan jumlah yang diinput.
                            </div>
                            <button type="submit" class="btn btn-success rounded-pill px-5">
                                Selesaikan Picking <i class="bi bi-check2-all ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
