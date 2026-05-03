@extends('layouts.app')

@section('title', 'GPS Real-time Asset Map - E-ASY POS')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 700px; border-radius: 24px; box-shadow: var(--card-shadow); border: 4px solid var(--card-bg); }
    .map-popup-card { min-width: 200px; font-family: 'Plus Jakarta Sans', sans-serif; }
    .map-popup-card h6 { font-weight: 800; color: var(--primary); margin-bottom: 5px; }
    .leaflet-popup-content-wrapper { border-radius: 16px; padding: 10px; background: var(--card-bg); color: var(--text-main); }
    .leaflet-popup-tip { background: var(--card-bg); }
</style>
@endpush

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-800 mb-1">GPS <span class="text-gradient">Real-time</span> Asset Tracking</h2>
            <p class="text-muted">Pelacakan lokasi outlet dan gudang di seluruh wilayah operasional.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
                <i class="bi bi-geo-alt-fill me-1"></i> {{ count($outlets) }} Outlets
            </div>
            <div class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">
                <i class="bi bi-building-fill me-1"></i> {{ count($warehouses) }} Warehouses
            </div>
        </div>
    </div>

    <div id="map"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const outlets    = @json($outlets);
    const warehouses = @json($warehouses);

    // Initial Center (Indonesia or default)
    const initialLat = outlets.length > 0 ? outlets[0].latitude : -6.200000;
    const initialLng = outlets.length > 0 ? outlets[0].longitude : 106.816666;

    const map = L.map('map').setView([initialLat, initialLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Custom Icons
    const outletIcon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color:#6366f1; width:30px; height:30px; border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white;'><i class='bi bi-shop'></i></div>",
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    const warehouseIcon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color:#10b981; width:30px; height:30px; border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white;'><i class='bi bi-building'></i></div>",
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

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

    // Fit bounds if markers exist
    if (outlets.length > 0 || warehouses.length > 0) {
        const group = new L.featureGroup([...outlets.map(o => L.marker([o.latitude, o.longitude])), ...warehouses.map(w => L.marker([w.latitude, w.longitude]))]);
        // map.fitBounds(group.getBounds());
    }
</script>
@endpush
