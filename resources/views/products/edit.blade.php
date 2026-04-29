@extends('layouts.app')

@section('title', 'Edit Produk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Produk: {{ $product->name }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-600">Nama Produk</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">SKU / Kode</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-600">Kategori</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Satuan (Unit)</label>
                            <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $product->unit) }}" required>
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-600">Harga Beli (Modal)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', $product->cost_price) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 text-primary">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-subtle text-primary border-primary-subtle">Rp</span>
                                <input type="number" name="price" class="form-control border-primary-subtle @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-600">Deskripsi (Opsional)</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-600">Foto Produk</label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ $product->image }}" class="rounded shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                                    <p class="small text-muted mt-1">Gambar saat ini. Upload baru untuk mengganti.</p>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isActive">Produk Aktif & Bisa Dijual</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-warning px-4 shadow-sm text-dark fw-bold">Update Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
