@extends('layouts.app')

@section('title', 'Edit SKU Katalog - E-ASY OMS')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Katalog</a></li>
    <li class="breadcrumb-item active">Edit SKU</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1 fw-800 text-dark">Edit Produk: {{ $product->name }}</h2>
                <p class="text-muted small mb-0">Perbarui spesifikasi SKU dan status operasional multikanal.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-light border px-4 rounded-3">Batal</a>
                <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold rounded-3">Update SKU</button>
            </div>
        </div>

        <div class="row g-4">
            {{-- Main Content --}}
            <div class="col-lg-8">
                {{-- Basic Information --}}
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="font-size: 1rem;">Informasi Dasar</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Nama Produk</label>
                                <input type="text" name="name" class="form-control form-control-lg bg-light border-0 rounded-3 @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required placeholder="Masukkan nama produk lengkap...">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Internal SKU (ISKU)</label>
                                <input type="text" name="sku" class="form-control bg-light border-0 rounded-3 @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                                @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Channel SKU (CSKU)</label>
                                <input type="text" name="csku" class="form-control bg-light border-0 rounded-3 @error('csku') is-invalid @enderror" value="{{ old('csku', $product->csku) }}" placeholder="Opsional (Default = ISKU)">
                                @error('csku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Deskripsi Produk</label>
                                <textarea name="description" class="form-control bg-light border-0 rounded-3" rows="4" placeholder="Jelaskan detail produk...">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pricing & Inventory --}}
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="font-size: 1rem;">Harga & Satuan</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Harga Beli</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">Rp</span>
                                    <input type="number" name="cost_price" class="form-control bg-light border-0 @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', $product->cost_price) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary-subtle text-primary border-0">Rp</span>
                                    <input type="number" name="price" class="form-control bg-light border-0 @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Satuan (Unit)</label>
                                <input type="text" name="unit" class="form-control bg-light border-0 @error('unit') is-invalid @enderror" value="{{ old('unit', $product->unit) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Media --}}
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="font-size: 1rem;">Media Produk</h5>
                        <div class="row align-items-center">
                            @if($product->image_url)
                            <div class="col-auto">
                                <img src="{{ $product->image_url }}" alt="" class="rounded-4 border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            @endif
                            <div class="col">
                                <div class="border-2 border-dashed border-secondary border-opacity-25 rounded-4 p-4 text-center bg-light">
                                    <p class="mb-0 small text-muted">Ganti gambar produk...</p>
                                    <input type="file" name="image" class="form-control mt-2 bg-white border-0 @error('image') is-invalid @enderror" accept="image/*">
                                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Content --}}
            <div class="col-lg-4">
                {{-- Status & Visibility --}}
                <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0" style="font-size: 1rem;">Status Katalog</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Lifecycle Status</label>
                            @php
                                $statusColors = [
                                    'live' => 'text-success',
                                    'draft' => 'text-secondary',
                                    'under_review' => 'text-warning',
                                    'failed' => 'text-danger'
                                ];
                            @endphp
                            <select name="status" class="form-select border-0 bg-light rounded-3 fw-bold {{ $statusColors[$product->status] ?? 'text-muted' }}">
                                <option value="live" {{ old('status', $product->status) === 'live' ? 'selected' : '' }} class="text-success">● LIVE</option>
                                <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }} class="text-secondary">● DRAFT</option>
                                <option value="under_review" {{ old('status', $product->status) === 'under_review' ? 'selected' : '' }} class="text-warning">● UNDER REVIEW</option>
                                <option value="failed" {{ old('status', $product->status) === 'failed' ? 'selected' : '' }} class="text-danger">● FAILED</option>
                            </select>
                            <small class="text-muted mt-2 d-block">Hanya status <strong>Live</strong> yang dapat diakses oleh Kasir POS.</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tipe Produk</label>
                            <select name="type" class="form-select border-0 bg-light rounded-3">
                                <option value="simple" {{ old('type', $product->type) === 'simple' ? 'selected' : '' }}>Simple Product</option>
                                <option value="variant" {{ old('type', $product->type) === 'variant' ? 'selected' : '' }}>Variant Product</option>
                                <option value="kit" {{ old('type', $product->type) === 'kit' ? 'selected' : '' }}>Product Kit / Bundle</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Organization --}}
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="font-size: 1rem;">Organisasi</h5>
                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold text-uppercase">Kategori Utama</label>
                            <select name="category_id" class="form-select border-0 bg-light rounded-3 @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4 rounded-4 bg-primary bg-opacity-10">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-primary mb-2">Total Stok Terdistribusi</h6>
                        <h3 class="fw-800 mb-0">{{ $product->inventories()->sum('quantity') }} <span class="fs-6 fw-normal text-muted">{{ $product->unit }}</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .border-dashed { border-style: dashed !important; }
</style>
@endsection
