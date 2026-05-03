@extends('layouts.app')

@section('title', 'Penerimaan Barang (GRN) - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="{{ route('inbound.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Inbound
        </a>
        <h2 class="fw-bold mt-2">Penerimaan Barang (GRN)</h2>
        <p class="text-muted">Menerima barang untuk PO: <span class="fw-bold text-primary">{{ $po->po_number }}</span></p>
    </div>

    <form action="{{ route('inbound.grn.store', $po) }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Informasi PO</h5>
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted small">Supplier</span>
                            <span class="fw-bold small">{{ $po->supplier->name }}</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted small">Gudang Tujuan</span>
                            <span class="fw-bold small">{{ $po->warehouse->name }}</span>
                        </div>
                        <div class="mb-0 d-flex justify-content-between">
                            <span class="text-muted small">Tanggal PO</span>
                            <span class="fw-bold small">{{ $po->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Item yang Diterima</h5>
                        
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Dipesan</th>
                                        <th class="text-center">Diterima</th>
                                        <th>Lokasi Putaway</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($po->items as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                            <div class="fw-bold">{{ $item->product->name }}</div>
                                            <small class="text-muted">{{ $item->product->sku }}</small>
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                        <td style="width: 120px;">
                                            <input type="number" name="items[{{ $index }}][quantity_received]" 
                                                   class="form-control form-control-sm rounded-3 text-center" 
                                                   value="{{ $item->quantity }}" min="0" max="{{ $item->quantity }}">
                                        </td>
                                        <td>
                                            <select name="items[{{ $index }}][location_id]" class="form-select form-select-sm rounded-3">
                                                <option value="">Pilih Lokasi...</option>
                                                @foreach($po->warehouse->locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-4 border-top text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-5">
                                Konfirmasi Penerimaan (GRN) <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
