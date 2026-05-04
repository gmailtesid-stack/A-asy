@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold">Edit Pengguna: {{ $user->name }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-600">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Foto Profil (Biarkan kosong jika tidak diubah)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak diubah">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required id="roleSelect">
                            <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Kasir</option>
                            <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager Outlet</option>
                            @if(auth()->user()->isSuperAdmin())
                                <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3" id="outletDiv">
                        <label class="form-label fw-600">Outlet Tugas</label>
                        <select name="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror">
                            <option value="" disabled>Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('outlet_id', $user->outlet_id) == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="isActive">Akun Aktif</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Update Pengguna</button>
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
