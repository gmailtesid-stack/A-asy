@extends('layouts.app')

@section('title', 'Tambah Outlet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('outlets.index') }}">Outlet</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold">Tambah Outlet Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('outlets.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-600">Nama Outlet</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Cabang Jakarta Pusat">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Kode Outlet (Singkatan)</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required placeholder="Contoh: JKT01">
                        <small class="text-muted">Digunakan sebagai prefix nomor invoice.</small>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Contoh: 021-1234567">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Alamat Lengkap</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required placeholder="Jalan Raya No. 123..."></textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Simpan Outlet</button>
                        <a href="{{ route('outlets.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
