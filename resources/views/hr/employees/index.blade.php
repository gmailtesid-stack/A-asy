@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-users-cog me-2 text-warning"></i>Manajemen Karyawan</h2>
            <p class="text-white-50">Kelola sumber daya manusia perusahaan.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('employees.create') }}" class="btn btn-warning text-dark fw-bold px-4 rounded-pill shadow-lg">
                <i class="fas fa-user-plus me-2"></i>Tambah Karyawan
            </a>
        </div>
    </div>

    <div class="row g-4">
        @foreach($employees as $employee)
        <div class="col-xl-4 col-md-6">
            <div class="card bg-glass border-0 shadow-lg position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-lg bg-gradient-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow" style="width: 60px; height: 60px;">
                            <span class="fs-3 fw-bold">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">{{ $employee->name }}</h5>
                            <span class="text-warning small fw-bold">{{ strtoupper($employee->employee_id) }}</span>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-success-soft text-success rounded-pill px-3">{{ strtoupper($employee->status) }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="d-flex justify-content-between text-white-50 small mb-2">
                            <span>Posisi:</span>
                            <span class="text-white fw-bold">{{ $employee->position }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-white-50 small mb-2">
                            <span>Gaji Pokok:</span>
                            <span class="text-white fw-bold">Rp {{ number_format($employee->salary, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-white-50 small mb-3">
                            <span>Tanggal Bergabung:</span>
                            <span class="text-white fw-bold">{{ $employee->joined_at }}</span>
                        </div>
                    </div>

                    <hr class="border-white-10 my-3">
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-glass flex-grow-1 text-white">Profil Detail</a>
                        <button class="btn btn-sm btn-glass text-white"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
                <div class="position-absolute bottom-0 end-0 opacity-10 p-3">
                    <i class="fas fa-user-tie fa-5x"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $employees->links() }}
    </div>
</div>

<style>
    .bg-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
    }
    .bg-success-soft { background: rgba(25, 135, 84, 0.2); }
    .btn-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s;
    }
    .btn-glass:hover { background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.3); }
    .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
</style>
@endsection
