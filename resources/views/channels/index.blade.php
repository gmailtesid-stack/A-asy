@extends('layouts.app')

@section('title', 'Marketplace Channels - E-ASY OMS')

@section('content')
<div class="animate__animated animate__fadeIn">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="h4 mb-1 fw-800 text-dark">Manajemen Kanal (OMS)</h2>
            <p class="text-muted small mb-0">Hubungkan dan sinkronisasikan stok Anda ke berbagai marketplace secara otomatis.</p>
        </div>
        <button class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Tambah Toko Baru
        </button>
    </div>

    <div class="row g-4">
        @foreach($channels as $channel)
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-all hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="platform-logo bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            @if($channel->platform == 'shopee')
                                <i class="bi bi-shop text-danger fs-2"></i>
                            @elseif($channel->platform == 'tokopedia')
                                <i class="bi bi-shop text-success fs-2"></i>
                            @elseif($channel->platform == 'tiktok')
                                <i class="bi bi-tiktok text-dark fs-2"></i>
                            @else
                                <i class="bi bi-globe text-primary fs-2"></i>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $channel->status == 'connected' ? 'success' : 'secondary' }}-subtle text-{{ $channel->status == 'connected' ? 'success' : 'secondary' }} rounded-pill px-3 py-2 border border-{{ $channel->status == 'connected' ? 'success' : 'secondary' }}-subtle fw-bold" style="font-size: 0.7rem;">
                                <i class="bi bi-record-fill me-1 animate__animated animate__flash animate__infinite"></i> {{ strtoupper($channel->status) }}
                            </span>
                        </div>
                    </div>

                    <h5 class="fw-800 text-dark mb-1">{{ $channel->name }}</h5>
                    <div class="text-muted small mb-4">Store ID: <code class="bg-light px-2 rounded">{{ $channel->store_id }}</code></div>

                    <div class="p-3 bg-light rounded-4 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Platform</span>
                            <span class="fw-bold text-dark text-capitalize small">{{ $channel->platform }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Terakhir Sinkron</span>
                            <span class="fw-bold text-dark small">{{ $channel->last_sync_at ? \Carbon\Carbon::parse($channel->last_sync_at)->diffForHumans() : 'Belum pernah' }}</span>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-8">
                            @if($channel->status == 'connected')
                                <form action="{{ route('channels.sync', $channel) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill fw-bold" {{ $channel->sync_status == 'processing' ? 'disabled' : '' }}>
                                        @if($channel->sync_status == 'processing')
                                            <span class="spinner-border spinner-border-sm me-2"></span> Memproses...
                                        @else
                                            <i class="bi bi-arrow-repeat me-2"></i> Sinkron Sekarang
                                        @endif
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('channels.toggle', $channel) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                                        <i class="bi bi-plug-fill me-2"></i> Hubungkan Sekarang
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="col-4">
                            @if($channel->status == 'connected')
                                <form action="{{ route('channels.toggle', $channel) }}" method="POST" onsubmit="return confirm('Putuskan koneksi?')">
                                    @csrf
                                    <button type="submit" class="btn btn-light border w-100 rounded-pill fw-bold text-danger">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-light border w-100 rounded-pill fw-bold" disabled><i class="bi bi-gear"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Add New Placeholder --}}
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 border-2 border-dashed bg-transparent h-100 d-flex align-items-center justify-content-center p-5 text-center text-muted" style="border: 2px dashed #cbd5e1 !important;">
                <div>
                    <div class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-plus-lg fs-3 text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Tambah Integrasi Baru</h6>
                    <p class="small mb-0 opacity-75">Hubungkan Lazada, Blibli, atau Marketplace lainnya.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .fw-800 { font-weight: 800; }
    .hover-up:hover { transform: translateY(-8px); }
</style>
@endsection
