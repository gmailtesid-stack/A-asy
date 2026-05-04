@extends('layouts.app')

@section('title', 'Mulai Stock Opname - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Mulai Stock Opname</h2>
        <p class="text-muted">Pilih gudang yang akan dilakukan penghitungan stok fisik.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('stock_opnames.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Gudang</label>
                            <select name="warehouse_id" class="form-select rounded-pill p-3 border-light shadow-sm" required>
                                <option value="">Pilih Gudang...</option>
                                @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('stock_opnames.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Inisialisasi Opname</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
