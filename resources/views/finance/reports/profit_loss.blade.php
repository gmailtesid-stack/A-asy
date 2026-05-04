@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>Laporan Laba Rugi</h2>
            <p class="text-white-50">Analisis kinerja keuangan periode {{ $startDate }} s/d {{ $endDate }}</p>
        </div>
        <div class="col-md-6 text-end">
            <form action="{{ route('finance.reports.profit-loss') }}" method="GET" class="d-flex justify-content-end gap-2">
                <input type="date" name="start_date" class="form-control form-control-sm bg-glass text-white border-0 w-auto" value="{{ $startDate }}">
                <input type="date" name="end_date" class="form-control form-control-sm bg-glass text-white border-0 w-auto" value="{{ $endDate }}">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Filter</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card bg-glass border-0 shadow-lg overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr class="bg-success-soft">
                                    <th colspan="2" class="py-3 px-4 text-white fw-bold">PENDAPATAN</th>
                                    <th class="text-end py-3 px-4 text-white">JUMLAH (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenues as $revenue)
                                <tr>
                                    <td class="ps-5" style="width: 50px;">{{ $revenue->code }}</td>
                                    <td>{{ $revenue->name }}</td>
                                    <td class="text-end px-4">Rp {{ number_format($revenue->balance ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr class="bg-white-5 fw-bold">
                                    <td colspan="2" class="ps-4">TOTAL PENDAPATAN</td>
                                    <td class="text-end px-4 text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                                </tr>

                                <tr class="bg-danger-soft">
                                    <th colspan="2" class="py-3 px-4 text-white fw-bold">BEBAN & HPP</th>
                                    <th class="text-end py-3 px-4 text-white">JUMLAH (IDR)</th>
                                </tr>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td class="ps-5" style="width: 50px;">{{ $expense->code }}</td>
                                    <td>{{ $expense->name }}</td>
                                    <td class="text-end px-4">Rp {{ number_format($expense->balance ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr class="bg-white-5 fw-bold">
                                    <td colspan="2" class="ps-4">TOTAL BEBAN</td>
                                    <td class="text-end px-4 text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                                </tr>

                                <tr class="{{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-25 fw-bold">
                                    <td colspan="2" class="py-3 ps-4 fs-5 text-white">LABA (RUGI) BERSIH</td>
                                    <td class="text-end px-4 fs-5 text-white">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .table-dark-custom { color: white; background: transparent; }
    .table-dark-custom td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 0.85rem 1rem;
    }
    .bg-success-soft { background: rgba(25, 135, 84, 0.3) !important; }
    .bg-danger-soft { background: rgba(220, 53, 69, 0.3) !important; }
    .bg-white-5 { background: rgba(255, 255, 255, 0.05) !important; }
    .form-control:focus {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
</style>
@endsection
