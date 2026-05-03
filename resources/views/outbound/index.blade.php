@extends('layouts.app')

@section('title', 'Manajemen Outbound - E-ASY WMS')

@section('breadcrumb')
    <li class="breadcrumb-item active">Outbound</li>
@endsection

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Outbound (Barang Keluar)</h2>
            <p class="text-muted">Kelola Sales Order (SO), Picking, dan Pengiriman barang.</p>
        </div>
        <a href="{{ route('outbound.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Buat SO Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No. SO / Tanggal</th>
                            <th>Gudang Asal</th>
                            <th class="text-center" style="width: 300px;">Fulfillment Progress</th>
                            <th class="text-end">Total Nilai</th>
                            <th>Tracking</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sos as $so)
                        <tr class="align-middle">
                            <td class="ps-4">
                                <div class="fw-bold text-success" style="font-size: 0.95rem;">{{ $so->so_number }}</div>
                                <div class="text-muted small">{{ $so->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border border-secondary-subtle px-3 py-2 rounded-pill"><i class="bi bi-building me-1"></i> {{ $so->warehouse->name }}</span>
                            </td>
                            <td>
                                {{-- Progress Tracker Visual --}}
                                @php
                                    $steps = ['pending', 'confirmed', 'picking', 'packing', 'shipping', 'delivered'];
                                    $currentIndex = array_search($so->status, $steps);
                                    if ($so->status === 'cancelled') $currentIndex = -1;
                                @endphp
                                
                                @if($so->status === 'cancelled')
                                    <div class="text-danger fw-bold text-center small"><i class="bi bi-x-circle-fill me-1"></i> DIBATALKAN</div>
                                @else
                                    <div class="position-relative m-3">
                                        <div class="progress" style="height: 4px; background-color: var(--bs-secondary-bg-subtle);">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($currentIndex / (count($steps)-1)) * 100 }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between position-absolute w-100" style="top: -6px; left: 0;">
                                            @foreach($steps as $index => $step)
                                                @php
                                                    $isCompleted = $index <= $currentIndex;
                                                    $isCurrent = $index === $currentIndex;
                                                    $icon = match($step) {
                                                        'pending' => 'bi-file-earmark-text',
                                                        'confirmed' => 'bi-check2-circle',
                                                        'picking' => 'bi-box-seam',
                                                        'packing' => 'bi-box',
                                                        'shipping' => 'bi-truck',
                                                        'delivered' => 'bi-house-check',
                                                        default => 'bi-circle'
                                                    };
                                                @endphp
                                                <div class="text-center position-relative" title="{{ ucfirst($step) }}" style="width: 16px;">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $isCompleted ? 'bg-primary text-white shadow-sm' : 'bg-light text-muted border border-white' }}" 
                                                         style="width: 16px; height: 16px; font-size: 0.5rem; {{ $isCurrent ? 'transform: scale(1.3); transition: transform 0.2s; z-index: 2;' : '' }}">
                                                        <i class="bi {{ $icon }}"></i>
                                                    </div>
                                                    @if($isCurrent)
                                                        <div class="position-absolute text-primary fw-bold" style="font-size: 0.55rem; top: 20px; left: 50%; transform: translateX(-50%); white-space: nowrap; text-transform: uppercase;">{{ $step }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="mt-4"></div>
                                @endif
                            </td>
                            <td class="text-end fw-bold">Rp {{ number_format($so->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($so->status == 'shipping' || $so->status == 'delivered')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1"><i class="bi bi-truck me-1"></i> {{ $so->shipping?->tracking_number ?? 'Resi Pending' }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light rounded-pill px-3 me-2">Detail</button>
                                    @if($so->status == 'pending' && auth()->user()->hasPermission('confirm-so'))
                                    <form action="{{ route('outbound.confirm', $so) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-warning rounded-pill px-3 me-2">Konfirmasi SO</button>
                                    </form>
                                    @endif
                                    @if(($so->status == 'confirmed' || $so->status == 'picking') && auth()->user()->hasPermission('process-picking'))
                                    <a href="{{ route('outbound.picking', $so) }}" class="btn btn-sm btn-warning rounded-pill px-3">Mulai Picking</a>
                                    @endif
                                    @if($so->status == 'packing' && auth()->user()->hasPermission('process-shipping'))
                                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#shipModal{{ $so->id }}">Kirim Barang</button>
                                    @endif
                                    @if($so->status == 'shipping' && auth()->user()->hasPermission('process-shipping'))
                                    <form action="{{ route('outbound.deliver', $so) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3">Tandai Delivered</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Ship --}}
                        @if($so->status == 'packing')
                        <div class="modal fade" id="shipModal{{ $so->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold">Input Pengiriman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('outbound.ship', $so) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small">Ekspedisi / Kurir</label>
                                                <input type="text" name="carrier" class="form-control rounded-3" placeholder="Contoh: JNE, J&T, SiCepat" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold small">Nomor Resi (Tracking Number)</label>
                                                <input type="text" name="tracking_number" class="form-control rounded-3" placeholder="Masukkan nomor resi..." required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 p-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Konfirmasi Pengiriman</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/blue/product-launch.svg" alt="" style="width: 150px;" class="mb-3">
                                <p class="text-muted">Belum ada Sales Order.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sos->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $sos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
