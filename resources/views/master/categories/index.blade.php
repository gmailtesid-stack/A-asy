@extends('layouts.app')

@section('title', 'Kategori Produk - E-ASY WMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="h4 mb-0 fw-800 text-dark text-uppercase" style="letter-spacing: 0.05em;">Kategori SKU</h2>
            </div>
            <p class="text-muted small mb-0">Kelola pengelompokan produk Anda untuk optimasi pencarian dan pelaporan.</p>
        </div>
        @if(auth()->user()->hasPermission('manage-master-data'))
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Kategori
        </button>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3">Nama Kategori</th>
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3 text-center">Produk Terkait</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($categories as $cat)
                        <tr class="bg-white border-bottom transition-all">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <i class="bi bi-grid-fill"></i>
                                    </div>
                                    <span class="fw-800 text-dark">{{ $cat->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $cat->description ?? 'Tidak ada deskripsi' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">
                                    {{ $cat->products_count }} ITEMS
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(auth()->user()->hasPermission('manage-master-data'))
                                    <button class="btn btn-sm btn-light border rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light border rounded-circle" style="width: 32px; height: 32px;" onclick="return confirm('Hapus kategori ini?')">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('categories.update', $cat) }}" method="POST" class="modal-content border-0 shadow">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold">Edit Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Kategori</label>
                                            <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea name="description" class="form-control" rows="3">{{ $cat->description }}</textarea>
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
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada data kategori.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('categories.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Elektronik, Pakaian" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Penjelasan singkat kategori..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Kategori</button>
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
