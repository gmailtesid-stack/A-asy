@extends('layouts.app')

@section('title', 'Lokasi Gudang - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <a href="{{ route('warehouses.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Gudang
        </a>
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div>
                <h2 class="fw-bold mb-1">Lokasi di {{ $warehouse->name }}</h2>
                <p class="text-muted">Kelola Rak, Bin, atau Section spesifik di gudang ini.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                <i class="bi bi-plus-lg me-2"></i> Tambah Lokasi
            </button>
        </div>
    </div>

    <div class="row g-4">
        @forelse($locations as $location)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="brand-logo bg-light text-primary mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-geo-alt-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $location->name }}</h5>
                    <div class="mb-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill small">{{ strtoupper($location->type) }}</span>
                    </div>
                    <p class="text-muted small mb-3">ID: <code>LOC-{{ $location->id }}</code></p>
                    
                    <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-link text-danger text-decoration-none" onclick="return confirm('Hapus lokasi ini?')">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5 text-center text-muted">
                Belum ada lokasi spesifik di gudang ini.
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('locations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lokasi (Rak/Bin)</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Rak A-01, Bin 12" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Tipe Lokasi</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="rack">Rak (Rack)</option>
                            <option value="bin">Kotak (Bin)</option>
                            <option value="zone">Area (Zone)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
