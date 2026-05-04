@extends('layouts.app')
@section('title', 'Manajemen Pengeluaran')

@section('content')
<div class="page-header">
    <h1>💸 Manajemen Pengeluaran (OPEX)</h1>
    <p class="page-subtitle">Catat dan kelola biaya operasional seluruh cabang untuk menghitung Laba Bersih yang akurat.</p>
</div>

{{-- Summary Cards --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); margin-bottom:2rem;">
    @foreach(['rent'=>'🏠 Sewa','utilities'=>'⚡ Utilitas','salary'=>'👤 Gaji','packaging'=>'📦 Packaging','marketing'=>'📣 Marketing','other'=>'🔧 Lainnya'] as $cat => $label)
    <div class="stat-card">
        <div class="stat-label">{{ $label }}</div>
        <div class="stat-value">Rp {{ number_format($summary[$cat] ?? 0, 0, ',', '.') }}</div>
    </div>
    @endforeach
</div>

<div style="display:grid; grid-template-columns: 1fr 360px; gap:1.5rem; align-items:start;">

    {{-- Table --}}
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Daftar Pengeluaran</h3>
            <div style="display:flex;gap:.5rem;">
                <select name="status" onchange="this.form && this.form.submit()" class="form-control" style="width:auto">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status')=='pending')>Pending</option>
                    <option value="approved" @selected(request('status')=='approved')>Disetujui</option>
                    <option value="rejected" @selected(request('status')=='rejected')>Ditolak</option>
                </select>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr><th>Tanggal</th><th>Deskripsi</th><th>Kategori</th><th>Jumlah</th><th>Diajukan</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td>{{ $exp->expense_date->format('d M Y') }}</td>
                        <td>{{ $exp->description }}</td>
                        <td><span class="badge badge-info">{{ $exp->category }}</span></td>
                        <td><strong>Rp {{ number_format($exp->amount, 0, ',', '.') }}</strong></td>
                        <td>{{ $exp->user->name ?? '-' }}</td>
                        <td>
                            @if($exp->status === 'approved') <span class="badge badge-success">✅ Disetujui</span>
                            @elseif($exp->status === 'rejected') <span class="badge badge-danger">❌ Ditolak</span>
                            @else <span class="badge badge-warning">⏳ Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($exp->status === 'pending' && auth()->user()->hasRole('admin|supervisor'))
                            <form method="POST" action="{{ route('expenses.approve', $exp) }}" style="display:inline">
                                @csrf <button class="btn btn-sm btn-success">✅</button>
                            </form>
                            <form method="POST" action="{{ route('expenses.reject', $exp) }}" style="display:inline" onsubmit="return prompt('Alasan penolakan:') !== null">
                                @csrf <button class="btn btn-sm btn-danger">❌</button>
                            </form>
                            @endif
                            @if($exp->receipt_url)
                            <a href="{{ $exp->receipt_url }}" target="_blank" class="btn btn-sm btn-secondary">🧾</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">Belum ada data pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem">{{ $expenses->links() }}</div>
    </div>

    {{-- Form Tambah --}}
    <div class="card">
        <div class="card-header"><h3>➕ Catat Pengeluaran</h3></div>
        <div style="padding:1.5rem">
            <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category" class="form-control" required>
                        @foreach(['rent','utilities','salary','packaging','marketing','other'] as $cat)
                        <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <input type="text" name="description" class="form-control" placeholder="cth: Listrik Cabang A April 2026" required>
                </div>
                <div class="form-group">
                    <label>Jumlah (Rp)</label>
                    <input type="number" name="amount" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label>Bukti Pengeluaran (Foto)</label>
                    <input type="file" name="receipt" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ajukan Pengeluaran</button>
            </form>
        </div>
    </div>
</div>
@endsection
