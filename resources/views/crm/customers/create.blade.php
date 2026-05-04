@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-plus-circle me-2 text-danger"></i>Tambah Pelanggan Baru</h2>
            <p class="text-white-50">Daftarkan pelanggan baru ke program loyalitas.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-glass border-0 shadow-lg">
                <div class="card-body p-5">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label text-white-50 small fw-bold">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control bg-glass-input text-white border-white-10" placeholder="Nama Pelanggan" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control bg-glass-input text-white border-white-10" placeholder="email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">No. Telepon / WhatsApp</label>
                                <input type="text" name="phone" class="form-control bg-glass-input text-white border-white-10" placeholder="08123456789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Outlet Terdaftar</label>
                                <select name="outlet_id" class="form-select bg-glass-input text-white border-white-10">
                                    @foreach(\App\Models\Outlet::all() as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-bold">Loyalty Points (Awal)</label>
                                <input type="number" name="loyalty_points" class="form-control bg-glass-input text-white border-white-10" value="0">
                            </div>
                        </div>

                        <div class="mt-5 d-flex gap-2">
                            <button type="submit" class="btn btn-danger text-white fw-bold px-5 rounded-pill shadow">Simpan Pelanggan</button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-light px-4 rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .bg-glass-input {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .bg-glass-input:focus {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: #dc3545 !important;
        color: white;
    }
    .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
</style>
@endsection
