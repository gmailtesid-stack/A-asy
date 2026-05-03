@extends('layouts.app')

@section('title', 'Buat Transfer Stok - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('stock_transfers.index') }}">Stock Transfer</a></li>
    <li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    <form action="{{ route('stock_transfers.store') }}" method="POST">
        @csrf
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1 fw-800 text-dark">Buat Permintaan Transfer</h2>
                <p class="text-muted small mb-0">Pindahkan stok antar gudang atau outlet untuk penyeimbangan inventori.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('stock_transfers.index') }}" class="btn btn-light border px-4 rounded-3">Batal</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold rounded-3">Buat Transfer</button>
            </div>
        </div>

        <div class="row g-4">
            {{-- Configuration --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="font-size: 1rem;">Rute Transfer</h5>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Gudang Asal</label>
                            <select name="from_warehouse_id" class="form-select border-0 bg-light rounded-3 fw-bold @error('from_warehouse_id') is-invalid @enderror" required>
                                <option value="" selected disabled>Pilih Gudang Asal</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('from_warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4 text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-arrow-down fs-4"></i>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Gudang Tujuan</label>
                            <select name="to_warehouse_id" class="form-select border-0 bg-light rounded-3 fw-bold @error('to_warehouse_id') is-invalid @enderror" required>
                                <option value="" selected disabled>Pilih Gudang Tujuan</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('to_warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold text-uppercase">Catatan (Opsional)</label>
                            <textarea name="note" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Alasan transfer atau instruksi khusus..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning border-0 rounded-4 p-3 small shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                    Pastikan stok tersedia di gudang asal sebelum melakukan konfirmasi pengiriman.
                </div>
            </div>

            {{-- Items --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0" style="font-size: 1rem;">Daftar Item Barang</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="add-item">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle" id="transfer-items-table">
                                <thead class="text-muted small fw-bold text-uppercase">
                                    <tr>
                                        <th style="width: 60%;">Produk</th>
                                        <th style="width: 30%;">Jumlah Transfer</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][product_id]" class="form-select border-0 bg-light rounded-3 product-select" required>
                                                <option value="" selected disabled>Pilih Produk</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="items[0][quantity]" class="form-control border-0 bg-light rounded-3 fw-bold text-center" value="1" min="1" required>
                                                <span class="input-group-text border-0 bg-light rounded-3 small text-muted">UNIT</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-link text-danger p-0 remove-row"><i class="bi bi-trash fs-5"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .fw-800 { font-weight: 800; }
</style>

@push('scripts')
<script>
    let rowIndex = 1;
    document.getElementById('add-item').addEventListener('click', function() {
        const table = document.getElementById('transfer-items-table').getElementsByTagName('tbody')[0];
        const newRow = table.rows[0].cloneNode(true);
        
        // Update input names
        const selects = newRow.getElementsByTagName('select');
        const inputs = newRow.getElementsByTagName('input');
        
        for (let s of selects) {
            s.name = s.name.replace('[0]', '[' + rowIndex + ']');
            s.selectedIndex = 0;
        }
        for (let i of inputs) {
            i.name = i.name.replace('[0]', '[' + rowIndex + ']');
            i.value = 1;
        }
        
        table.appendChild(newRow);
        rowIndex++;
        
        // Re-attach remove event
        attachRemoveEvent();
    });

    function attachRemoveEvent() {
        const removeButtons = document.querySelectorAll('.remove-row');
        removeButtons.forEach(btn => {
            btn.onclick = function() {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    this.closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 item barang.');
                }
            };
        });
    }

    attachRemoveEvent();
</script>
@endpush
@endsection
