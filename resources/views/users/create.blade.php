@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold">Tambah Pengguna Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-600">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required id="roleSelect">
                            <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Kasir</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager Outlet</option>
                            @if(auth()->user()->isSuperAdmin())
                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3" id="outletDiv">
                        <label class="form-label fw-600">Outlet Tugas</label>
                        <select name="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror">
                            <option value="" disabled selected>Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="isActive">Akun Aktif</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Simpan Pengguna</button>
                        <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const roleSelect = document.getElementById('roleSelect');
    const outletDiv  = document.getElementById('outletDiv');

    function toggleOutlet() {
        if (roleSelect.value === 'super_admin') {
            outletDiv.style.display = 'none';
        } else {
            outletDiv.style.display = 'block';
        }
    }

    roleSelect.addEventListener('change', toggleOutlet);
    toggleOutlet();
</script>
@endpush
