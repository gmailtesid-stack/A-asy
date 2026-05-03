@extends('layouts.app')

@section('title', 'Manajemen Supplier - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Supplier</h2>
            <p class="text-muted">Kelola vendor dan pemasok barang Anda.</p>
        </div>
        @if(auth()->user()->hasPermission('manage-master-data'))
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Supplier
        </button>
        @endif
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Supplier</th>
                            <th>Kontak</th>
                            <th>Email & HP</th>
                            <th>Alamat</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td>
                                <div class="small">{{ $supplier->email ?? '-' }}</div>
                                <div class="small text-muted">{{ $supplier->phone ?? '-' }}</div>
                            </td>
                            <td class="small">{{ $supplier->address ?? '-' }}</td>
                            <td class="text-end pe-4">
                                @if(auth()->user()->hasPermission('manage-master-data'))
                                <button class="btn btn-sm btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger rounded-pill" onclick="return confirm('Hapus supplier ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">View Only</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold">Edit Supplier</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Nama Supplier</label>
                                                <input type="text" name="name" class="form-control rounded-3" value="{{ $supplier->name }}" required>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small">Nama Kontak</label>
                                                    <input type="text" name="contact_person" class="form-control rounded-3" value="{{ $supplier->contact_person }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small">No. HP</label>
                                                    <input type="text" name="phone" class="form-control rounded-3" value="{{ $supplier->phone }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Email</label>
                                                <input type="email" name="email" class="form-control rounded-3" value="{{ $supplier->email }}">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold small">Alamat</label>
                                                <textarea name="address" class="form-control rounded-3" rows="3">{{ $supplier->address }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 p-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data supplier.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Supplier Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Supplier</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Nama Perusahaan/Vendor" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Kontak</label>
                            <input type="text" name="contact_person" class="form-control rounded-3" placeholder="Person In Charge">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">No. HP</label>
                            <input type="text" name="phone" class="form-control rounded-3" placeholder="Nomor Telepon">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="Email aktif">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Alamat</label>
                        <textarea name="address" class="form-control rounded-3" rows="3" placeholder="Alamat lengkap supplier..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
