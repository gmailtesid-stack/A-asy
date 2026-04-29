@extends('layouts.app')

@section('title', 'Edit Outlet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('outlets.index') }}">Outlet</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold">Edit Outlet: {{ $outlet->name }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('outlets.update', $outlet) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-600">Nama Outlet</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $outlet->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Kode Outlet</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $outlet->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $outlet->phone) }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Alamat Lengkap</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $outlet->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Update Outlet</button>
                        <a href="{{ route('outlets.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
