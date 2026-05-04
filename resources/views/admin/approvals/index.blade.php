@extends('layouts.app')
@section('title', 'Approval Gateway')

@section('content')
<div class="page-header">
    <h1>🛡️ Approval Gateway</h1>
    <p class="page-subtitle">Pusat persetujuan manajerial untuk operasi sensitif (Opname, Biaya, dll) demi menjaga integritas data.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Permohonan Menunggu Persetujuan</h3>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu Pengajuan</th>
                    <th>Tipe Dokumen</th>
                    <th>ID Ref</th>
                    <th>Diajukan Oleh</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $approval)
                <tr>
                    <td>{{ $approval->created_at->format('d M Y H:i') }}</td>
                    <td><span class="badge badge-info">{{ class_basename($approval->approvable_type) }}</span></td>
                    <td>#{{ $approval->approvable_id }}</td>
                    <td>{{ $approval->requester->name ?? '-' }}</td>
                    <td>{{ $approval->notes ?? '-' }}</td>
                    <td>
                        <form method="POST" action="{{ route('approvals.approve', $approval) }}" style="display:inline" onsubmit="return confirm('Setujui permohonan ini?')">
                            @csrf
                            <button class="btn btn-sm btn-success">✅ Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('approvals.reject', $approval) }}" style="display:inline" onsubmit="return prompt('Alasan penolakan:') !== null">
                            @csrf
                            <button class="btn btn-sm btn-danger">❌ Tolak</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada permohonan yang menunggu persetujuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem">{{ $pending->links() }}</div>
</div>
@endsection
