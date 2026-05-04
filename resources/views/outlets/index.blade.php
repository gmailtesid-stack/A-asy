@extends('layouts.app')

@section('title', 'Manajemen Outlet')

@section('breadcrumb')
    <li class="breadcrumb-item active">Outlet</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 fw-bold text-dark">Daftar Outlet / Cabang</h2>
    <a href="{{ route('outlets.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Outlet
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Outlet</th>
                        <th>Kode</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outlets as $outlet)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="outlet-logo bg-light shadow-sm d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px; border-radius: 8px;">
                                    @if($outlet->photo)
                                    <img src="{{ $outlet->photo }}" alt="Outlet Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                    <i class="bi bi-shop text-muted"></i>
                                    @endif
                                </div>
                                <div class="fw-bold text-dark">{{ $outlet->name }}</div>
                            </div>
                        </td>
                        <td><span class="badge bg-dark">{{ $outlet->code }}</span></td>
                        <td>{{ $outlet->phone ?? '-' }}</td>
                        <td class="text-truncate" style="max-width: 250px;">{{ $outlet->address }}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('outlets.edit', $outlet) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('outlets.destroy', $outlet) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus outlet ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            Belum ada data outlet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
