@extends('layouts.app')

@section('title', 'Dashboard Real-time - E-ASY WMS & POS')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ── DASHBOARD PREMIUM VARS ── */
    :root {
        --dash-card-bg: rgba(255, 255, 255, 0.75);
        --dash-glass-border: rgba(255, 255, 255, 0.5);
        --dash-blur: blur(20px);
        --dash-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
        --dash-hover-shadow: 0 25px 45px -10px rgba(99, 102, 241, 0.15);
    }
    
    [data-theme='dark'] {
        --dash-card-bg: rgba(15, 23, 42, 0.6);
        --dash-glass-border: rgba(255, 255, 255, 0.05);
        --dash-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
        --dash-hover-shadow: 0 30px 50px -10px rgba(99, 102, 241, 0.3);
    }

    /* ── GLASS CARD COMPONENT ── */
    .glass-panel {
        background: var(--dash-card-bg);
        backdrop-filter: var(--dash-blur);
        -webkit-backdrop-filter: var(--dash-blur);
        border: 1px solid var(--dash-glass-border);
        border-radius: 28px;
        box-shadow: var(--dash-shadow);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }
    .glass-panel:hover {
        transform: translateY(-5px);
        box-shadow: var(--dash-hover-shadow);
    }

    /* ── HEADER WIDGETS ── */
    .widget-clock {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        letter-spacing: -1px;
    }
    .widget-cal-icon {
        width: 60px; height: 60px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dk));
        color: white;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
    }

    /* ── QUICK ACTIONS ── */
    .quick-action-btn {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 12px; padding: 20px;
        border-radius: 24px;
        background: var(--dash-card-bg);
        border: 1px solid var(--dash-glass-border);
        text-decoration: none; color: var(--text-main);
        transition: all 0.4s ease;
        box-shadow: var(--dash-shadow);
    }
    .quick-action-btn:hover {
        transform: translateY(-8px) scale(1.05);
        background: linear-gradient(135deg, var(--primary), var(--primary-dk));
        color: white !important;
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }
    .quick-action-icon {
        width: 56px; height: 56px;
        border-radius: 18px;
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; transition: all 0.3s;
    }
    .quick-action-btn:hover .quick-action-icon { background: rgba(255,255,255,0.2); color: white; }
    .quick-action-btn span { font-weight: 700; font-size: 0.9rem; }

    /* ── STATS CARDS ── */
    .stat-value { font-size: 2.2rem; font-weight: 800; line-height: 1; margin: 10px 0; }
    .stat-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); }
    .stat-icon-wrapper {
        width: 54px; height: 54px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center; font-size: 24px;
    }

    /* ── LEAFLET MAP CUSTOM ── */
    #dashboard-map { height: 450px; width: 100%; border-radius: 28px; z-index: 1;}
    .leaflet-popup-content-wrapper { border-radius: 20px; padding: 5px; background: var(--card-bg); color: var(--text-main); box-shadow: var(--dash-shadow);}
    .leaflet-popup-tip { background: var(--card-bg); }
    .map-popup-card { font-family: 'Plus Jakarta Sans', sans-serif; padding: 10px; min-width: 200px; }
    .map-popup-card h6 { font-weight: 800; margin-bottom: 5px; }

    /* ── TABLE CUSTOM ── */
    .table-custom th { border-bottom: 1px dashed var(--dash-glass-border); text-transform: uppercase; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.1em; padding: 15px; }
    .table-custom td { border-bottom: 1px dashed var(--dash-glass-border); padding: 15px; vertical-align: middle;}
    .table-custom tr:last-child td { border-bottom: none; }
</style>
@endpush

@section('content')
<div class="animate__animated animate__fadeIn">

    <!-- HEADER: WIDGET JAM, KALENDER & GREETING -->
    <div class="row mb-5 align-items-center">
        <div class="col-xl-7 col-lg-6 mb-4 mb-lg-0">
            <h1 class="fw-800 mb-1" style="font-size: 2.5rem; letter-spacing:-1px;">
                Selamat Datang, <span class="text-gradient">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-muted fs-6 mb-0">Pusat kendali E-ASY WMS & POS. Semua operasi terpantau secara real-time.</p>
        </div>
        
        <div class="col-xl-5 col-lg-6">
            <div class="glass-panel p-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="widget-cal-icon">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div>
                        <div class="stat-label mb-1" id="dash-date">Memuat...</div>
                        <div class="text-dark fw-800" id="dash-month-year" style="font-size: 1.1rem;">Memuat...</div>
                    </div>
                </div>
                <div class="vr opacity-25"></div>
                <div class="text-end">
                    <div class="stat-label mb-1">WAKTU SISTEM</div>
                    <div class="widget-clock text-primary" id="dash-time" style="font-size: 2rem; line-height: 1;">00:00:00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS MENU -->
    <div class="mb-5">
        <h6 class="stat-label mb-3 ps-2"><i class="bi bi-lightning-charge-fill text-warning me-2"></i> Akses Cepat (Quick Actions)</h6>
        <div class="row g-3">
            @if(auth()->user()->hasPermission('create-so') || auth()->user()->isSuperAdmin())
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('pos.index') }}" class="quick-action-btn">
                    <div class="quick-action-icon"><i class="bi bi-grid-1x2-fill"></i></div>
                    <span>Kasir POS</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->hasPermission('create-po') || auth()->user()->hasPermission('create-grn') || auth()->user()->isSuperAdmin())
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('inbound.create') }}" class="quick-action-btn">
                    <div class="quick-action-icon" style="color: #10b981; background: rgba(16,185,129,0.1)"><i class="bi bi-box-arrow-in-down"></i></div>
                    <span>Restock (PO)</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->hasPermission('create-so') || auth()->user()->hasPermission('process-picking') || auth()->user()->isSuperAdmin())
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('outbound.create') }}" class="quick-action-btn">
                    <div class="quick-action-icon" style="color: #f59e0b; background: rgba(245,158,11,0.1)"><i class="bi bi-box-arrow-up"></i></div>
                    <span>Order Keluar</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->hasPermission('view-master-data') || auth()->user()->isSuperAdmin())
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('inventories.index') }}" class="quick-action-btn">
                    <div class="quick-action-icon" style="color: #8b5cf6; background: rgba(139,92,246,0.1)"><i class="bi bi-archive-fill"></i></div>
                    <span>Cek Stok</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->hasPermission('view-reports') || auth()->user()->isSuperAdmin())
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('reports.index') }}" class="quick-action-btn">
                    <div class="quick-action-icon" style="color: #ec4899; background: rgba(236,72,153,0.1)"><i class="bi bi-pie-chart-fill"></i></div>
                    <span>Laporan POS</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('reports.wms') }}" class="quick-action-btn">
                    <div class="quick-action-icon" style="color: #06b6d4; background: rgba(6,182,212,0.1)"><i class="bi bi-bar-chart-steps"></i></div>
                    <span>Statistik WMS</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- STATS CARDS OVERVIEW -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="glass-panel p-4 h-100" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                <div class="stat-icon-wrapper bg-white bg-opacity-20 text-white mb-4">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-label text-white text-opacity-75">Inventory Asset Value</div>
                <div class="stat-value text-white">Rp {{ number_format($stats['total_stock_value'], 0, ',', '.') }}</div>
                <div class="mt-3 pt-3 border-top border-white border-opacity-10">
                    <div class="d-flex justify-content-between text-white small opacity-75">
                        <span>OMS Live SKU</span>
                        <span class="fw-bold">{{ $stats['oms_live'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-panel p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2">Urgent</span>
                    </div>
                </div>
                <div class="stat-label">Stok Kritis / Menipis</div>
                <div class="stat-value text-danger">{{ $stats['low_stock_count'] }} <span class="fs-6 text-muted fw-normal">Items</span></div>
                <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i> Perlu restock segera.</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-panel p-4 h-100">
                <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info mb-4">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stat-label">OMS Lifecycle Status</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="text-center">
                        <div class="fw-800 text-dark" style="font-size: 1.2rem;">{{ $stats['oms_draft'] }}</div>
                        <div class="text-muted fs-xs fw-bold">DRAFT</div>
                    </div>
                    <div class="vr opacity-25"></div>
                    <div class="text-center">
                        <div class="fw-800 text-warning" style="font-size: 1.2rem;">{{ $stats['oms_review'] }}</div>
                        <div class="text-muted fs-xs fw-bold">REVIEW</div>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; border-radius: 10px; background: rgba(0,0,0,0.05);">
                    @php 
                        $total = max(1, $stats['oms_live'] + $stats['oms_draft'] + $stats['oms_review']);
                        $pLive = ($stats['oms_live'] / $total) * 100;
                        $pDraft = ($stats['oms_draft'] / $total) * 100;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $pLive }}%"></div>
                    <div class="progress-bar bg-secondary" style="width: {{ $pDraft }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-panel p-4 h-100" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: none;">
                <div class="stat-icon-wrapper bg-white bg-opacity-10 text-white mb-4">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-label text-white text-opacity-75">Order Pipeline</div>
                <div class="row g-2 mt-2">
                    <div class="col-4 text-center">
                        <div class="fw-800 text-white" style="font-size: 1.1rem;">{{ $stats['pending_so'] }}</div>
                        <div class="text-white text-opacity-50 fs-xs">NEW</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-800 text-warning" style="font-size: 1.1rem;">{{ $stats['picking_so'] }}</div>
                        <div class="text-white text-opacity-50 fs-xs">PICK</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-800 text-info" style="font-size: 1.1rem;">{{ $stats['packing_so'] }}</div>
                        <div class="text-white text-opacity-50 fs-xs">PACK</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN DASHBOARD CONTENT: MAP & ACTIVITY -->
    <div class="row g-4 mb-5">
        
        <!-- REAL-TIME GPS MAP -->
        <div class="col-xl-8">
            <div class="glass-panel h-100 d-flex flex-column p-1">
                <div class="p-4 pb-0 d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="fw-800 mb-1"><i class="bi bi-geo-alt-fill text-primary me-2"></i> Peta Aset Real-time</h4>
                        <p class="text-muted small mb-0">Lokasi geografis seluruh outlet dan gudang operasional.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2"><i class="bi bi-shop me-1"></i> {{ count($outlets) }} Outlet</span>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2"><i class="bi bi-building-fill me-1"></i> {{ count($warehouses) }} Gudang</span>
                    </div>
                </div>
                <div id="dashboard-map" class="mt-auto"></div>
            </div>
        </div>

        <!-- ACTIVITY FEED -->
        <div class="col-xl-4">
            <div class="glass-panel h-100 p-0 d-flex flex-column">
                <div class="p-4 border-bottom border-secondary border-opacity-10">
                    <h5 class="fw-800 mb-0"><i class="bi bi-activity text-primary me-2"></i> Live Activity Feed</h5>
                </div>
                <div class="p-0 overflow-auto" style="max-height: 450px;">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivity as $activity)
                            @if(!is_object($activity)) @continue @endif
                        <div class="list-group-item border-0 p-4 bg-transparent position-relative" style="border-bottom: 1px dashed var(--dash-glass-border) !important;">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle mt-1 flex-shrink-0" style="width: 12px; height: 12px; background: {{ ($activity->type ?? '') == 'in' ? '#10b981' : (($activity->type ?? '') == 'out' ? '#6366f1' : '#f59e0b') }}; box-shadow: 0 0 10px {{ ($activity->type ?? '') == 'in' ? '#10b981' : (($activity->type ?? '') == 'out' ? '#6366f1' : '#f59e0b') }};"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-{{ ($activity->type ?? '') == 'in' ? 'success' : (($activity->type ?? '') == 'out' ? 'primary' : 'warning') }}-subtle text-{{ ($activity->type ?? '') == 'in' ? 'success' : (($activity->type ?? '') == 'out' ? 'primary' : 'warning') }} rounded-pill" style="font-size: 0.65rem; font-weight: 800;">{{ strtoupper($activity->type ?? 'LOG') }}</span>
                                        <span class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($activity->created_at ?? now())->diffForHumans() }}</span>
                                    </div>
                                    <p class="mb-2 small lh-sm">
                                        <span class="fw-800 text-dark">{{ $activity->product_name ?? 'Produk' }}</span> 
                                        {{ ($activity->type ?? '') == 'in' ? 'bertambah' : (($activity->type ?? '') == 'out' ? 'berkurang' : 'diatur (adjust)') }} 
                                         <span class="fw-800 {{ ($activity->type ?? '') == 'in' ? 'text-success' : (($activity->type ?? '') == 'out' ? 'text-primary' : 'text-warning') }} fs-6">{{ abs($activity->quantity_change ?? 0) }}</span> unit.
                                    </p>
                                    <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.7rem; font-weight: 600;">
                                        <div class="avatar-small rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 18px; height: 18px; font-size: 0.5rem;">
                                            {{ strtoupper(substr($activity->user_name ?? 'U', 0, 1)) }}
                                        </div>
                                        {{ $activity->user_name ?? 'System' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-inbox fs-1 mb-3 d-block opacity-50"></i>
                            <span class="fw-bold">Belum ada aktivitas.</span>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="p-3 mt-auto text-center border-top border-secondary border-opacity-10">
                    <a href="{{ route('inventories.logs') }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">
                        Lihat Seluruh Log <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- LOW STOCK ALERTS -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="glass-panel p-0">
                <div class="p-4 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-10">
                    <div>
                        <h4 class="fw-800 mb-1 text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i> Peringatan Stok Kritis</h4>
                        <p class="text-muted small mb-0">Daftar produk yang jumlah stoknya berada di bawah batas minimum.</p>
                    </div>
                    <a href="{{ route('inbound.create') }}" class="btn btn-premium rounded-pill px-4"><i class="bi bi-plus-lg me-2"></i> Buat PO Baru</a>
                </div>
                <div class="table-responsive p-4 pt-0">
                    <table class="table table-borderless table-custom align-middle mb-0 mt-3 bg-transparent" style="--bs-table-bg: transparent;">
                        <thead class="bg-secondary bg-opacity-10 text-muted" style="border-radius: 16px;">
                            <tr>
                                <th class="rounded-start-3">Informasi Produk</th>
                                <th>Lokasi / Gudang</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-center">Min. Stok</th>
                                <th class="text-end rounded-end-3">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $lowStockItems = \App\Models\Inventory::with(['product', 'warehouse'])
                                    ->whereColumn('quantity', '<', 'min_quantity')
                                    ->latest()
                                    ->take(6)
                                    ->get();
                            @endphp
                            @forelse($lowStockItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="bi bi-box-seam fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark" style="font-size: 0.95rem;">{{ $item->product->name }}</div>
                                            <div class="text-muted small fw-bold" style="letter-spacing: 0.05em;">{{ $item->product->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2 rounded-pill fw-bold border">{{ $item->warehouse->name ?? 'Gudang Pusat' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-800 shadow-sm" style="font-size: 0.9rem;">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="text-center text-muted fw-bold">{{ $item->min_quantity }}</td>
                                <td class="text-end">
                                    <a href="{{ route('inbound.create', ['product_id' => $item->product_id]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-4 fw-bold">
                                        Restock
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="bi bi-check-lg fs-1"></i>
                                        </div>
                                        <h5 class="fw-800 text-success mb-1">Semua Stok Aman!</h5>
                                        <p class="text-muted small">Tidak ada produk yang memerlukan restock mendesak.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ── JAM DIGITAL & KALENDER WIDGET ──
    function updateDashClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
        
        // Memisahkan Tanggal, Bulan, Tahun untuk UI Premium
        const dayName = now.toLocaleDateString('id-ID', { weekday: 'long' });
        const dateNum = now.getDate();
        const monthYear = now.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        
        const elTime = document.getElementById('dash-time');
        const elDate = document.getElementById('dash-date');
        const elMonthYear = document.getElementById('dash-month-year');
        
        if (elTime) elTime.innerText = timeStr;
        if (elDate) elDate.innerText = `${dayName}, ${dateNum}`;
        if (elMonthYear) elMonthYear.innerText = monthYear;
    }
    setInterval(updateDashClock, 1000);
    updateDashClock();

    // ── LEAFLET GPS MAP ──
    const outlets    = @json($outlets);
    const warehouses = @json($warehouses);

    // Initial Center (Indonesia)
    const initialLat = outlets.length > 0 ? outlets[0].latitude : -6.200000;
    const initialLng = outlets.length > 0 ? outlets[0].longitude : 106.816666;

    const map = L.map('dashboard-map', {
        zoomControl: false // Sembunyikan default zoom untuk kesan UI lebih rapi
    }).setView([initialLat, initialLng], 12);
    
    // Tambah zoom control di pojok kanan bawah
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Menggunakan tileset CartoDB Voyager yang lebih modern dan bersih
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Custom UI Icons dengan bayangan
    const createIcon = (color, iconClass) => {
        return L.divIcon({
            className: 'custom-div-icon',
            html: `<div style='background: ${color}; width:36px; height:36px; border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white; box-shadow: 0 5px 15px rgba(0,0,0,0.3);'><i class='bi ${iconClass} fs-6'></i></div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -18]
        });
    };

    const outletIcon = createIcon('linear-gradient(135deg, #6366f1, #4f46e5)', 'bi-shop');
    const warehouseIcon = createIcon('linear-gradient(135deg, #10b981, #059669)', 'bi-building');

    // Add Outlets
    outlets.forEach(o => {
        const popup = `
            <div class="map-popup-card">
                <h6>${o.name}</h6>
                <div class="small text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>${o.address || '-'}</div>
                <div class="badge bg-primary rounded-pill small">Outlet Aktif</div>
            </div>
        `;
        L.marker([o.latitude, o.longitude], {icon: outletIcon}).addTo(map).bindPopup(popup);
    });

    // Add Warehouses
    warehouses.forEach(w => {
        const popup = `
            <div class="map-popup-card">
                <h6>${w.name}</h6>
                <div class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>${w.address || '-'}</div>
                <div class="small text-muted mb-2"><i class="bi bi-shop me-1"></i>Outlet: ${w.outlet ? w.outlet.name : '-'}</div>
                <div class="badge bg-success rounded-pill small">Gudang Penyimpanan</div>
            </div>
        `;
        L.marker([w.latitude, w.longitude], {icon: warehouseIcon}).addTo(map).bindPopup(popup);
    });

    // Sesuaikan view agar semua marker terlihat jika jumlahnya > 1
    if (outlets.length > 0 || warehouses.length > 0) {
        const markers = [
            ...outlets.map(o => L.marker([o.latitude, o.longitude])), 
            ...warehouses.map(w => L.marker([w.latitude, w.longitude]))
        ];
        const group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds(), { padding: [50, 50] });
    }
</script>
@endpush
