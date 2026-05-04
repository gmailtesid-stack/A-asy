@extends('layouts.app')

@section('title', 'Manajemen Supplier - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}" class="text-decoration-none">Master Data</a></li>
    <li class="breadcrumb-item active">Supplier</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="h4 mb-0 fw-800 text-dark text-uppercase" style="letter-spacing: 0.05em;">Supplier / Pemasok</h2>
            </div>
            <p class="text-muted small mb-0">Kelola data pemasok atau vendor untuk keperluan pengadaan barang dan Inbound WMS.</p>
        </div>
        @if(auth()->user()->hasPermission('manage-master-data'))
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Supplier
        </button>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3">Nama Supplier</th>
                            <th class="py-3">Kontak / Person</th>
                            <th class="py-3">Email & Telepon</th>
                            <th class="py-3">Alamat</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($suppliers as $supplier)
                        <tr class="bg-white border-bottom transition-all">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <span class="fw-800 text-dark">{{ $supplier->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $supplier->contact_person ?? '-' }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if($supplier->email)
                                    <span class="text-muted small"><i class="bi bi-envelope me-1"></i> {{ $supplier->email }}</span>
                                    @endif
                                    @if($supplier->phone)
                                    <span class="text-muted small"><i class="bi bi-telephone me-1"></i> {{ $supplier->phone }}</span>
                                    @endif
                                    @if(!$supplier->email && !$supplier->phone)
                                    <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-muted text-truncate" style="max-width: 200px;" title="{{ $supplier->address }}">
                                {{ $supplier->address ?? '-' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(auth()->user()->hasPermission('manage-master-data'))
                                    <button class="btn btn-sm btn-light border rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light border rounded-circle" style="width: 32px; height: 32px;" onclick="return confirm('Hapus supplier ini?')">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="modal-content border-0 shadow">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold">Edit Supplier</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Supplier</label>
                                            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Contact Person</label>
                                            <input type="text" name="contact_person" class="form-control" value="{{ $supplier->contact_person }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Telepon</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Alamat Lengkap</label>
                                            <textarea name="address" class="form-control" rows="3">{{ $supplier->address }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                    </div>
                                </form>
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
            @if(method_exists($suppliers, 'links'))
            <div class="px-4 py-3 border-top">
                {{ $suppliers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('suppliers.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Supplier Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Supplier</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: PT. Sumber Makmur" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" placeholder="Contoh: Bpk. Budi">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="budi@sumbermakmur.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="081234567890">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Alamat lengkap perusahaan..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .fw-800 { font-weight: 800; }
    .transition-all:hover { background-color: #f8fafc !important; }
</style>
@endpush
