@extends('layouts.app')
@section('title', 'Analisis Dead Stock')

@section('content')
<div class="page-header">
    <h1>🧟 Analisis Dead Stock</h1>
    <p class="page-subtitle">Daftar produk yang tidak mengalami penjualan dalam {{ $days }} hari terakhir. Evaluasi untuk diskon cuci gudang.</p>
</div>

<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Barang Tidak Bergerak</h3>
        <form method="GET" style="display:flex; gap:1rem;">
            <select name="days" class="form-control" onchange="this.form.submit()">
                <option value="30" @selected($days == 30)>30 Hari</option>
                <option value="60" @selected($days == 60)>60 Hari</option>
                <option value="90" @selected($days == 90)>90 Hari</option>
                <option value="180" @selected($days == 180)>180 Hari</option>
            </select>
        </form>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>SKU / Produk</th>
                    <th>Kategori</th>
                    <th>Sisa Stok Fisik</th>
                    <th>Estimasi Nilai Stok (Tertahan)</th>
                    <th>Terakhir Ada Pergerakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><strong>{{ $item['product_name'] }}</strong></td>
                    <td><span class="badge badge-secondary">{{ $item['category'] }}</span></td>
                    <td><span class="badge badge-warning">{{ $item['quantity'] }}</span></td>
                    <td style="color:var(--danger)">Rp {{ number_format($item['stock_value'], 0, ',', '.') }}</td>
                    <td>{{ $item['last_updated'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Luar biasa! Tidak ada dead stock terdeteksi dalam periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
