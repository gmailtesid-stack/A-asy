@extends('layouts.app')

@section('title', 'Manajemen Supplier - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('master.index') }}" class="text-decoration-none">Master Data</a></li>
    <li class="breadcrumb-item active">Supplier</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="h4 mb-0 fw-800 text-uppercase" style="letter-spacing: 0.05em;">Pemasok (Supplier)</h2>
            </div>
            <p class="text-muted small mb-0">Kelola data vendor dan pemasok untuk pengadaan barang inbound.</p>
        </div>
        @if(auth()->user()->hasPermission('manage-master-data'))
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Supplier
        </button>
        @endif
    </div>

    {{-- Stats Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                    <i class="bi bi-truck fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Supplier</div>
                    <div class="fw-800 fs-3 lh-1">{{ $suppliers->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                    <i class="bi bi-box-seam fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total PO Dibuat</div>
                    <div class="fw-800 fs-3 lh-1">{{ \App\Models\PurchaseOrder::count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                    <i class="bi bi-check2-circle fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">PO Terselesaikan</div>
                    <div class="fw-800 fs-3 lh-1">{{ \App\Models\PurchaseOrder::where('status', 'received')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 supplier-table">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3">Supplier</th>
                            <th class="py-3">PIC / Kontak</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Telepon</th>
                            <th class="py-3 text-center">Total PO</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    @if($supplier->logo_url)
                                    <img src="{{ $supplier->logo_url }}" alt="{{ $supplier->name }}" class="rounded-3 object-fit-cover shadow-sm" style="width: 42px; height: 42px;">
                                    @else
                                    <div class="supplier-initial rounded-3 d-flex align-items-center justify-content-center fw-800 text-white" style="width: 42px; height: 42px; background: linear-gradient(135deg, #6366f1, #a855f7); font-size: 1.1rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-800" style="font-size: 0.9rem;">{{ $supplier->name }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 200px;">
                                            {{ $supplier->address ?? 'Alamat belum diisi' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold" style="font-size: 0.875rem;">{{ $supplier->contact_person ?? '-' }}</td>
                            <td style="font-size: 0.875rem;">
                                @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}" class="text-primary text-decoration-none">
                                    <i class="bi bi-envelope me-1"></i>{{ $supplier->email }}
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="font-size: 0.875rem;">
                                @if($supplier->phone)
                                <a href="tel:{{ $supplier->phone }}" class="text-decoration-none text-dark">
                                    <i class="bi bi-telephone me-1 text-success"></i>{{ $supplier->phone }}
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem; background: rgba(99,102,241,.12); color: #6366f1;">
                                    {{ $supplier->purchase_orders_count }} PO
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(auth()->user()->hasPermission('manage-master-data'))
                                    <button class="action-btn btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}"
                                            title="Edit" style="width: 34px; height: 34px;">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center"
                                                onclick="return confirm('Hapus supplier {{ addslashes($supplier->name) }}? Pastikan tidak ada PO aktif.')"
                                                title="Hapus" style="width: 34px; height: 34px;">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="badge bg-light text-muted border">View Only</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- EDIT MODAL --}}
                        <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <form action="{{ route('suppliers.update', $supplier) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                                        <div>
                                            <h5 class="fw-800 mb-0">Edit Supplier</h5>
                                            <p class="text-muted small mb-0">{{ $supplier->name }}</p>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body px-4 py-3">

                                        {{-- Logo Upload --}}
                                        <div class="logo-upload-zone mb-4 rounded-4 d-flex flex-column align-items-center justify-content-center text-center" onclick="document.getElementById('logoInputEdit{{ $supplier->id }}').click()">
                                            @if($supplier->logo_url)
                                            <img src="{{ $supplier->logo_url }}" id="logoPreviewEdit{{ $supplier->id }}" class="object-fit-contain rounded-3 mb-2" style="max-height: 80px; max-width: 200px;">
                                            @else
                                            <img src="" id="logoPreviewEdit{{ $supplier->id }}" class="object-fit-contain rounded-3 mb-2 d-none" style="max-height: 80px; max-width: 200px;">
                                            @endif
                                            <i class="bi bi-image fs-2 text-muted" id="logoIconEdit{{ $supplier->id }}" {{ $supplier->logo ? 'd-none' : '' }}></i>
                                            <div class="text-muted small mt-1"><strong>Klik untuk ganti logo</strong></div>
                                            <div class="text-muted" style="font-size: 0.7rem;">JPG, PNG, WEBP — maks. 2MB</div>
                                            <input type="file" id="logoInputEdit{{ $supplier->id }}" name="logo" accept="image/*" class="d-none"
                                                   onchange="previewLogoEdit('{{ $supplier->id }}', this)">
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Nama Supplier <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control rounded-3" value="{{ $supplier->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Nama PIC / Kontak</label>
                                                <input type="text" name="contact_person" class="form-control rounded-3" value="{{ $supplier->contact_person }}" placeholder="Person In Charge">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Email</label>
                                                <input type="email" name="email" class="form-control rounded-3" value="{{ $supplier->email }}" placeholder="email@supplier.com">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">No. Telepon</label>
                                                <input type="text" name="phone" class="form-control rounded-3" value="{{ $supplier->phone }}" placeholder="08xx-xxxx-xxxx">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Alamat Lengkap</label>
                                                <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Alamat perusahaan supplier...">{{ $supplier->address }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Catatan Internal</label>
                                                <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Catatan tambahan (term of payment, SLA pengiriman, dll)...">{{ $supplier->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 px-4 py-3">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                            <i class="bi bi-check2 me-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-truck fs-1 text-muted d-block mb-3 opacity-50"></i>
                                <div class="fw-bold mb-1">Belum Ada Supplier</div>
                                <p class="text-muted small mb-3">Tambahkan supplier pertama untuk mulai membuat Purchase Order.</p>
                                @if(auth()->user()->hasPermission('manage-master-data'))
                                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                    <i class="bi bi-plus-lg me-2"></i>Tambah Supplier
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($suppliers->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 px-4">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ADD SUPPLIER MODAL --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h5 class="fw-800 mb-0">Tambah Supplier Baru</h5>
                    <p class="text-muted small mb-0">Lengkapi data vendor / pemasok Anda</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">

                {{-- Logo Upload --}}
                <div class="logo-upload-zone mb-4 rounded-4 d-flex flex-column align-items-center justify-content-center text-center" onclick="document.getElementById('logoInputAdd').click()">
                    <img src="" id="logoPreviewAdd" class="object-fit-contain rounded-3 mb-2 d-none" style="max-height: 80px; max-width: 200px;">
                    <i class="bi bi-cloud-upload fs-2 text-muted" id="logoIconAdd"></i>
                    <div class="text-muted small mt-1"><strong>Upload Logo Supplier</strong></div>
                    <div class="text-muted" style="font-size: 0.7rem;">JPG, PNG, WEBP, SVG — maks. 2MB</div>
                    <input type="file" id="logoInputAdd" name="logo" accept="image/*" class="d-none"
                           onchange="previewLogoAdd(this)">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: PT. Maju Jaya Supplier" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama PIC / Kontak</label>
                        <input type="text" name="contact_person" class="form-control rounded-3" placeholder="Person In Charge">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="email@supplier.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">No. Telepon</label>
                        <input type="text" name="phone" class="form-control rounded-3" placeholder="08xx-xxxx-xxxx">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Alamat Lengkap</label>
                        <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Alamat lengkap perusahaan supplier..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Catatan Internal</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Catatan tambahan (term of payment, SLA pengiriman, dll)..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 py-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Simpan Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .fw-800 { font-weight: 800; }
    .supplier-table tbody tr:hover { background-color: rgba(99,102,241,.03); }
    [data-theme='dark'] .supplier-table tbody tr:hover { background-color: rgba(99,102,241,.07); }

    .logo-upload-zone {
        border: 2px dashed var(--border-color, #e2e8f0);
        background: var(--input-bg, #f8fafc);
        padding: 1.5rem;
        cursor: pointer;
        transition: all .3s ease;
        min-height: 110px;
    }
    .logo-upload-zone:hover {
        border-color: #6366f1;
        background: rgba(99,102,241,.04);
    }
    [data-theme='dark'] .logo-upload-zone {
        background: rgba(255,255,255,.03);
    }
    .action-btn { transition: transform .2s, box-shadow .2s; }
    .action-btn:hover { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
</style>
@endpush

@push('scripts')
<script>
function previewLogoAdd(input) {
    const preview = document.getElementById('logoPreviewAdd');
    const icon = document.getElementById('logoIconAdd');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            icon.classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewLogoEdit(supplierId, input) {
    const preview = document.getElementById('logoPreviewEdit' + supplierId);
    const icon = document.getElementById('logoIconEdit' + supplierId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (icon) icon.classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
