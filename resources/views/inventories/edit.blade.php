@extends('layouts.app')

@section('title', 'Update Stok')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventori</a></li>
    <li class="breadcrumb-item active">Update Stok</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold">Update Stok: {{ $inventory->product->name }}</h5>
                <small class="text-muted">Outlet: {{ $inventory->outlet->name }}</small>
            </div>
            <div class="card-body p-4">
                <div class="alert bg-primary-subtle text-primary border-0 d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <div class="small fw-600">Stok Saat Ini</div>
                        <div class="h4 mb-0 fw-bold">{{ $inventory->quantity }} {{ $inventory->product->unit }}</div>
                    </div>
                </div>

                <form action="{{ route('inventories.update', $inventory) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-600">Jenis Perubahan</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="in">Stok Masuk (Restock)</option>
                            <option value="out">Stok Keluar (Rusak/Expired)</option>
                            <option value="adjustment">Penyesuaian (Opname)</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Jumlah Perubahan</label>
                        <div class="input-group">
                            <input type="number" name="quantity_change" class="form-control @error('quantity_change') is-invalid @enderror" required placeholder="Contoh: 50 atau -10">
                            <span class="input-group-text bg-light">{{ $inventory->product->unit }}</span>
                        </div>
                        <small class="text-muted">Gunakan angka positif untuk menambah, negatif untuk mengurangi.</small>
                        @error('quantity_change') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Keterangan / Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Alasan perubahan stok..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Simpan Perubahan</button>
                        <a href="{{ route('inventories.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
