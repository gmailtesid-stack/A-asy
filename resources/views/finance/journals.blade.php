@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-history me-2 text-primary"></i>Riwayat Jurnal</h2>
            <p class="text-white-50">Daftar entri jurnal akuntansi sistem.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary-glass text-white px-4 rounded-pill">
                <i class="fas fa-plus me-2"></i>Entri Jurnal Manual
            </button>
        </div>
    </div>

    @forelse($journals as $journal)
    <div class="card bg-glass border-0 shadow-lg mb-4">
        <div class="card-header bg-transparent border-bottom border-white-10 p-3 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-primary text-white me-2">{{ $journal->entry_date }}</span>
                <span class="text-white fw-bold">{{ $journal->reference }}</span>
                <span class="mx-2 text-white-50">|</span>
                <span class="text-white-50 small">{{ $journal->description }}</span>
            </div>
            <div class="small text-white-50">Dicatat oleh: {{ $journal->user->name ?? 'System' }}</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journal->lines as $line)
                        <tr>
                            <td>{{ $line->account->code }}</td>
                            <td>{{ $line->account->name }}</td>
                            <td class="text-end text-success">
                                @if($line->debit > 0) Rp {{ number_format($line->debit, 0, ',', '.') }} @else - @endif
                            </td>
                            <td class="text-end text-danger">
                                @if($line->credit > 0) Rp {{ number_format($line->credit, 0, ',', '.') }} @else - @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card bg-glass border-0 p-5 text-center">
        <i class="fas fa-ghost fa-3x text-white-10 mb-3"></i>
        <h5 class="text-white-50">Belum ada catatan jurnal.</h5>
    </div>
    @endforelse

    <div class="mt-4">
        {{ $journals->links() }}
    </div>
</div>

<style>
    .bg-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .table-dark-custom { color: white; background: transparent; }
    .table-dark-custom thead th {
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.8rem;
    }
    .table-dark-custom td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 0.75rem 1rem;
    }
    .btn-primary-glass {
        background: rgba(13, 110, 253, 0.2);
        border: 1px solid rgba(13, 110, 253, 0.3);
        backdrop-filter: blur(5px);
    }
</style>
@endsection
