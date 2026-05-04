@extends('layouts.app')

@section('title', 'Manajemen Gudang & Lokasi - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item active">Gudang & Lokasi</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <h2 class="h4 mb-1 fw-800 text-uppercase" style="letter-spacing: 0.05em;">Gudang & Lokasi</h2>
            <p class="text-muted small mb-0">Kelola gudang fisik dan lokasi penyimpanan (Rak/Bin) di setiap outlet.</p>
        </div>
        @if(auth()->user()->hasPermission('manage-master-data'))
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Gudang
        </button>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Warehouse Cards --}}
    <div class="row g-4">
        @forelse($warehouses as $warehouse)
        <div class="col-md-4">
            <div class="card warehouse-card border-0 shadow-sm overflow-hidden">
                {{-- Foto / Header --}}
                <div class="warehouse-photo position-relative" style="height: 140px; overflow: hidden;">
                    <img src="{{ $warehouse->photo_url }}" alt="{{ $warehouse->name }}"
                         class="w-100 h-100 object-fit-cover"
                         onerror="this.src='https://placehold.co/600x200/e0e7ff/6366f1?text=Gudang'">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, transparent 40%, rgba(15,23,42,.7));"></div>
                    <span class="position-absolute bottom-0 start-0 m-3 badge rounded-pill text-white fw-bold"
                          style="background: rgba(255,255,255,.15); backdrop-filter: blur(6px); font-size: 0.7rem;">
                        <i class="bi bi-shop me-1"></i>{{ $warehouse->outlet->name ?? 'N/A' }}
                    </span>
                    @if(isset($warehouse->is_active) && !$warehouse->is_active)
                    <span class="position-absolute top-0 end-0 m-2 badge bg-danger rounded-pill" style="font-size: 0.65rem;">NONAKTIF</span>
                    @endif
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-800 mb-1">{{ $warehouse->name }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-geo-alt me-1"></i>{{ $warehouse->address ?? 'Alamat belum diatur' }}
                    </p>

                    {{-- Stats --}}
                    <div class="themed-summary rounded-3 p-3 mb-4">
                        <div class="row text-center g-0">
                            <div class="col-6 border-end" style="border-color: var(--border-color) !important;">
                                <div class="fw-800 fs-4 text-primary">{{ $warehouse->locations->count() }}</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: .05em;">Lokasi</div>
                            </div>
                            <div class="col-6">
                                <div class="fw-800 fs-4 text-primary">{{ $warehouse->inventories->count() }}</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: .05em;">SKU Stok</div>
                            </div>
                        </div>
                        @if($warehouse->latitude && $warehouse->longitude)
                        <div class="mt-2 pt-2 border-top d-flex align-items-center gap-1 text-muted" style="font-size: 0.7rem; border-color: var(--border-color) !important;">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <span>{{ $warehouse->latitude }}, {{ $warehouse->longitude }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('warehouses.show', $warehouse) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill fw-semibold">
                            <i class="bi bi-geo-fill me-1"></i> Detail Lokasi
                        </a>
                        @if(auth()->user()->hasPermission('manage-master-data'))
                        <button class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 34px; height: 34px;"
                                data-bs-toggle="modal" data-bs-target="#editWarehouseModal{{ $warehouse->id }}"
                                title="Edit Gudang">
                            <i class="bi bi-pencil text-primary" style="font-size: 0.8rem;"></i>
                        </button>
                        <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 34px; height: 34px;"
                                    onclick="return confirm('Hapus gudang {{ addslashes($warehouse->name) }}? Pastikan tidak ada stok barang.')"
                                    title="Hapus Gudang">
                                <i class="bi bi-trash text-danger" style="font-size: 0.8rem;"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- EDIT WAREHOUSE MODAL --}}
        <div class="modal fade" id="editWarehouseModal{{ $warehouse->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form action="{{ route('warehouses.update', $warehouse) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
                    @csrf @method('PUT')
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <div>
                            <h5 class="fw-800 mb-0">Edit Gudang</h5>
                            <p class="text-muted small mb-0">{{ $warehouse->name }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-3">

                        {{-- Photo Upload --}}
                        <div class="photo-upload-zone mb-4 rounded-4 position-relative overflow-hidden" onclick="document.getElementById('photoInputEdit{{ $warehouse->id }}').click()">
                            <img src="{{ $warehouse->photo_url }}"
                                 id="photoPreviewEdit{{ $warehouse->id }}"
                                 class="w-100 object-fit-cover" style="height: 160px;"
                                 onerror="this.src='https://placehold.co/600x200/e0e7ff/6366f1?text=Gudang'">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center photo-overlay">
                                <i class="bi bi-camera-fill fs-2 text-white"></i>
                                <div class="text-white small fw-bold">Klik untuk ganti foto</div>
                            </div>
                            <input type="file" id="photoInputEdit{{ $warehouse->id }}" name="photo" accept="image/*" class="d-none"
                                   onchange="previewWarehousePhoto('{{ $warehouse->id }}', this)">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Cabang / Outlet <span class="text-danger">*</span></label>
                                <select name="outlet_id" class="form-select rounded-3" required>
                                    @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ $warehouse->outlet_id == $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Gudang <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-3" value="{{ $warehouse->name }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Alamat Gudang</label>
                                <textarea name="address" class="form-control rounded-3" rows="2">{{ $warehouse->address }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i>Latitude GPS</label>
                                <input type="text" name="latitude" class="form-control rounded-3" value="{{ $warehouse->latitude }}" placeholder="-6.200000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i>Longitude GPS</label>
                                <input type="text" name="longitude" class="form-control rounded-3" value="{{ $warehouse->longitude }}" placeholder="106.816666">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit{{ $warehouse->id }}"
                                           {{ ($warehouse->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold small" for="isActiveEdit{{ $warehouse->id }}">
                                        Gudang Aktif (menerima stok & operasional)
                                    </label>
                                </div>
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
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5">
                <div class="card-body text-center">
                    <div class="mb-4" style="font-size: 4rem;">🏭</div>
                    <h5 class="fw-bold">Belum Ada Gudang</h5>
                    <p class="text-muted">Tambahkan gudang pertama untuk mulai mengelola stok WMS.</p>
                    @if(auth()->user()->hasPermission('manage-master-data'))
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Gudang
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- ADD WAREHOUSE MODAL --}}
<div class="modal fade" id="addWarehouseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('warehouses.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h5 class="fw-800 mb-0">Tambah Gudang Baru</h5>
                    <p class="text-muted small mb-0">Lengkapi informasi gudang fisik Anda</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">

                {{-- Photo Upload --}}
                <div class="photo-upload-zone-empty mb-4 rounded-4 d-flex flex-column align-items-center justify-content-center text-center" onclick="document.getElementById('photoInputAdd').click()">
                    <img src="" id="photoPreviewAdd" class="w-100 object-fit-cover rounded-4 d-none" style="height: 160px;">
                    <div id="photoPlaceholderAdd" class="py-4">
                        <i class="bi bi-cloud-upload fs-2 text-muted d-block mb-2"></i>
                        <div class="fw-bold text-muted small">Upload Foto Gudang</div>
                        <div class="text-muted" style="font-size: 0.7rem;">JPG, PNG, WEBP — maks. 2MB</div>
                    </div>
                    <input type="file" id="photoInputAdd" name="photo" accept="image/*" class="d-none"
                           onchange="previewWarehousePhotoAdd(this)">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Cabang / Outlet <span class="text-danger">*</span></label>
                        <select name="outlet_id" class="form-select rounded-3" required>
                            <option value="">Pilih Outlet...</option>
                            @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Gudang <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Gudang Utama A" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Alamat Gudang</label>
                        <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Alamat lengkap gudang..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i>Latitude GPS</label>
                        <input type="text" name="latitude" class="form-control rounded-3" placeholder="-6.200000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-primary"><i class="bi bi-geo-alt me-1"></i>Longitude GPS</label>
                        <input type="text" name="longitude" class="form-control rounded-3" placeholder="106.816666">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 py-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Simpan Gudang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .fw-800 { font-weight: 800; }

    /* Warehouse Card */
    .warehouse-card {
        border-radius: 20px !important;
        transition: transform .3s cubic-bezier(.34,1.56,.64,1), box-shadow .3s;
    }
    .warehouse-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px -10px rgba(99,102,241,.15) !important;
    }

    /* Themed summary box */
    .themed-summary {
        background: rgba(99, 102, 241, 0.05);
        border: 1px solid var(--border-color, #e2e8f0);
    }
    [data-theme='dark'] .themed-summary {
        background: rgba(99, 102, 241, 0.08);
    }

    /* Photo upload zones */
    .photo-upload-zone {
        cursor: pointer;
        border: 2px dashed var(--border-color, #e2e8f0);
    }
    .photo-upload-zone:hover .photo-overlay { opacity: 1 !important; }
    .photo-overlay {
        opacity: 0;
        background: rgba(99,102,241,.55);
        transition: opacity .3s;
    }

    .photo-upload-zone-empty {
        border: 2px dashed var(--border-color, #e2e8f0);
        background: var(--input-bg, #f8fafc);
        cursor: pointer;
        min-height: 120px;
        transition: all .3s;
    }
    .photo-upload-zone-empty:hover {
        border-color: #6366f1;
        background: rgba(99,102,241,.04);
    }
    [data-theme='dark'] .photo-upload-zone-empty {
        background: rgba(255,255,255,.03);
    }
</style>
@endpush

@push('scripts')
<script>
function previewWarehousePhoto(warehouseId, input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreviewEdit' + warehouseId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewWarehousePhotoAdd(input) {
    const preview = document.getElementById('photoPreviewAdd');
    const placeholder = document.getElementById('photoPlaceholderAdd');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            placeholder.classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
