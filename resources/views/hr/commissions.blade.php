@extends('layouts.app')

@section('title', 'Laporan Komisi Karyawan - E-ASY ERP')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Performa & Komisi Karyawan</h2>
        <p class="text-muted">Pantau pencapaian KPI dan kalkulasi komisi berdasarkan real-time sales.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small uppercase">
                        <tr>
                            <th class="ps-4 py-3">Nama Karyawan</th>
                            <th class="py-3">Role</th>
                            <th class="py-3 text-center">Total Transaksi</th>
                            <th class="py-3 text-end">Total Omzet</th>
                            <th class="py-3 text-center">Rate Komisi</th>
                            <th class="py-3 text-end pe-4">Estimasi Komisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $emp->name }}</div>
                                <div class="text-muted small">{{ $emp->email }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">
                                    {{ strtoupper($emp->role) }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">{{ $emp->total_sales_count }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($emp->total_revenue, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $emp->commission_rate }}%</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="fw-bold text-success" style="font-size: 1.1rem;">
                                    Rp {{ number_format($emp->commission_amount, 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
