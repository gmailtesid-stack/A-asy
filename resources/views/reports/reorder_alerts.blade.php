@extends('layouts.app')
@section('title', 'Reorder Alerts')

@section('content')
<div class="page-header">
    <h1>🚨 Reorder Alerts</h1>
    <p class="page-subtitle">Sistem mendeteksi produk yang stoknya telah menyentuh batas Reorder Point. Segera buat Purchase Order.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Produk Kritis (Butuh Restock)</h3>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Lokasi Cabang</th>
                    <th>Stok Saat Ini</th>
                    <th>Reorder Point (Titik Aman)</th>
                    <th>Defisit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr style="background: rgba(var(--danger-rgb), 0.05);">
                    <td><strong>{{ $alert['product_name'] }}</strong></td>
                    <td>{{ $alert['outlet'] }}</td>
                    <td><strong style="color:var(--danger)">{{ $alert['current_stock'] }}</strong></td>
                    <td>{{ $alert['reorder_point'] }}</td>
                    <td>-{{ $alert['deficit'] }} unit</td>
                    <td>
                        <a href="{{ route('inbound.create') }}?product_id={{ $alert['product_id'] }}" class="btn btn-sm btn-primary">📝 Buat Draf PO</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Semua stok berada di atas Reorder Point. Aman!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
