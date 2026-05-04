<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-ASY POS')</title>
    <meta name="description" content="@yield('meta_description', 'Sistem POS Multi-Outlet E-ASY — Kasir, Inventori, Laporan')">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=1.0.2">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Light Theme (Default) */
            --primary:    #6366f1;
            --primary-dk: #4f46e5;
            --secondary:  #64748b;
            --sidebar-w:  280px;
            --sidebar-bg: #0f172a;
            --topbar-h:   72px;
            --accent:     #818cf8;
            --glass:      rgba(255, 255, 255, 0.75);
            --bg-main:    #f8fafc;
            --card-bg:    #ffffff;
            --text-main:  #1e293b;
            --text-muted: #64748b;
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 8px 15px -6px rgba(0, 0, 0, 0.04);
            --border-color: rgba(226, 232, 240, 0.5);
        }

        [data-theme='dark'] {
            --bg-main:    #0a0a0a;
            --card-bg:    #0f172a;
            --text-main:  #f8fafc;
            --text-muted: #94a3b8;
            --glass:      rgba(15, 23, 42, 0.8);
            --border-color: rgba(255, 255, 255, 0.05);
            --card-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
            --input-bg:   #1e293b;
        }

        /* ── Dark Mode Overrides ────────────────────────── */
        [data-theme='dark'] .bg-white,
        [data-theme='dark'] .bg-light,
        [data-theme='dark'] .card-header.bg-white,
        [data-theme='dark'] .card-footer.bg-white {
            background-color: var(--card-bg) !important;
            color: var(--text-main) !important;
        }

        [data-theme='dark'] .form-control.bg-light,
        [data-theme='dark'] .form-select.bg-light,
        [data-theme='dark'] .input-group-text.bg-light {
            background-color: var(--input-bg) !important;
        }

        [data-theme='dark'] .text-dark,
        [data-theme='dark'] .text-black {
            color: var(--text-main) !important;
        }

        [data-theme='dark'] .form-control,
        [data-theme='dark'] .form-select,
        [data-theme='dark'] .input-group-text {
            background-color: var(--input-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        [data-theme='dark'] .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.5;
        }

        [data-theme='dark'] .table {
            color: var(--text-main) !important;
        }

        [data-theme='dark'] .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        [data-theme='dark'] .border,
        [data-theme='dark'] .border-bottom,
        [data-theme='dark'] .border-top,
        [data-theme='dark'] .border-end,
        [data-theme='dark'] .border-start {
            border-color: var(--border-color) !important;
        }

        [data-theme='dark'] .btn-light {
            background-color: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        [data-theme='dark'] .btn-white {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-main);
        }
        
        [data-theme='dark'] .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-muted) !important;
        }

        [data-theme='dark'] .breadcrumb-item a {
            color: var(--primary) !important;
        }

        [data-theme='dark'] .breadcrumb-item.active {
            color: var(--text-main) !important;
        }

        [data-theme='dark'] .pagination .page-link {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        [data-theme='dark'] .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        [data-theme='dark'] .pagination .page-item.disabled .page-link {
            background-color: var(--bg-main);
            border-color: var(--border-color);
            color: var(--text-muted);
        }

        /* ── Modals Dark Mode ────────────────────────── */
        [data-theme='dark'] .modal-content {
            background-color: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }
        [data-theme='dark'] .modal-header, 
        [data-theme='dark'] .modal-footer {
            border-color: var(--border-color);
            background-color: var(--card-bg);
        }
        [data-theme='dark'] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        * { box-sizing: border-box; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-main); 
            color: var(--text-main); 
            margin: 0;
            transition: background .3s, color .3s;
        }

        /* ── Sidebar ─────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: #0f172a;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: all .4s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255,255,255,0.05);
            overflow-y: auto;
        }
        /* Custom Scrollbar for Sidebar */
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-track { background: transparent; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        #sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        .sidebar-brand {
            padding: 2.5rem 1.5rem;
            text-align: center;
        }
        .sidebar-brand img { filter: drop-shadow(0 0 10px rgba(99, 102, 241, 0.3)); }

        .nav-section-label {
            color: #475569;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            padding: 1.5rem 2rem 0.75rem;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1.75rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: .9rem;
            font-weight: 600;
            border-radius: 12px;
            margin: 4px 16px;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover {
            background: rgba(255,255,255,.03);
            color: #f8fafc;
            transform: translateX(5px);
        }
        .sidebar-nav a.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 100%);
            color: #fff;
            border-left: 3px solid var(--primary);
        }
        .sidebar-nav a i { font-size: 1.25rem; transition: transform .3s; color: #475569; }
        .sidebar-nav a.active i { color: var(--primary); }
        .sidebar-nav a:hover i { transform: scale(1.1); color: var(--primary); }

        .sidebar-user {
            margin: 1.5rem 16px;
            padding: 1.25rem;
            background: rgba(255,255,255,.02);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.05);
        }
        .sidebar-user .avatar {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 800; font-size: 1.1rem;
            box-shadow: 0 8px 15px rgba(99, 102, 241, 0.3);
        }

        /* ── Main Content ─────────────────────────────────── */
        #main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all .4s ease;
        }
        #topbar {
            height: var(--topbar-h);
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            padding: 0 2.5rem;
            position: sticky;
            top: 0; z-index: 900;
        }
        .page-content { padding: 2.5rem; flex: 1; }

        /* ── Premium Cards ───────────────────────────────── */
        .card { 
            border: none;
            border-radius: 24px; 
            box-shadow: var(--card-shadow);
            background: var(--card-bg);
            transition: transform .4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow .4s ease, background .3s;
        }
        .card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08); 
        }

        /* ── Glassmorphism ─────────────────────────────── */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.4);
        }

        /* ── Badges & Status ───────────────────────────── */
        .role-badge {
            font-size: 0.65rem;
            padding: 5px 12px;
            border-radius: 100px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .role-super_admin { background: #fef3c7; color: #92400e; }
        .role-admin       { background: #fef3c7; color: #92400e; }
        .role-manager     { background: #dcfce7; color: #166534; }
        .role-cashier     { background: #dbeafe; color: #1e40af; }

        .btn-premium {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dk) 100%);
            color: #fff;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            transition: all .3s ease;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(99, 102, 241, 0.5);
            color: #fff;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        /* ── Responsive Sidebar ───────────────────────────── */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
                box-shadow: 10px 0 30px rgba(0,0,0,0.5);
            }
            #sidebar.show {
                transform: translateX(0);
            }
            #main {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 999;
                backdrop-filter: blur(3px);
            }
            .sidebar-overlay.show { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR OVERLAY (MOBILE) ───────────────────────── --}}
<div class="sidebar-overlay" id="sidebar-overlay"></div>

{{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex justify-content-center">
            <img src="{{ asset('images/logo.png') }}" id="semicolon-brand-logo" alt="Logo" style="width: 160px; height: auto; object-fit: contain;">
        </div>
        <small class="d-block text-center mt-2" style="color: #94a3b8; font-size: 0.75rem;">{{ auth()->user()->outlet?->name ?? 'Pusat Distribusi' }}</small>
    </div>

    <div class="sidebar-nav mt-2">

        <p class="nav-section-label">Dashboard</p>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Control Tower
        </a>

        <p class="nav-section-label">OMS (Order Management)</p>
        @if(auth()->user()->hasPermission('view-master-data') || auth()->user()->isSuperAdmin())
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i> Master Katalog (SKU)
        </a>
        <a href="{{ route('channels.index') }}" class="{{ request()->routeIs('channels.*') ? 'active' : '' }}">
            <i class="bi bi-intersect"></i> Marketplace Channels
        </a>
        @endif

        <p class="nav-section-label">WMS (Warehouse Management)</p>
        @if(auth()->user()->hasPermission('create-po') || auth()->user()->hasPermission('create-grn') || auth()->user()->isSuperAdmin())
        <a href="{{ route('inbound.index') }}" id="nav-wms" class="{{ request()->routeIs('inbound.*') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-in-down-right"></i> Inbound (Receiving)
        </a>
        @endif
        @if(auth()->user()->hasPermission('create-so') || auth()->user()->hasPermission('process-picking') || auth()->user()->isSuperAdmin())
        <a href="{{ route('outbound.index') }}" class="{{ request()->routeIs('outbound.*') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-up-right"></i> Outbound (Pick & Pack)
        </a>
        @endif
        @if(auth()->user()->hasPermission('view-master-data') || auth()->user()->hasPermission('manage-stock-adjustment') || auth()->user()->isSuperAdmin())
        <a href="{{ route('inventories.index') }}" class="{{ request()->routeIs('inventories.index') ? 'active' : '' }}">
            <i class="bi bi-archive-fill"></i> Stok Real-time
        </a>
        <a href="{{ route('stock_transfers.index') }}" class="{{ request()->routeIs('stock_transfers.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i> Stock Transfer
        </a>
        <a href="{{ route('inventories.logs') }}" id="nav-audit" class="{{ request()->routeIs('inventories.logs') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Movement Log
        </a>
        @endif

        <p class="nav-section-label">Retail & Offline</p>
        @if(auth()->user()->hasPermission('create-so') || auth()->user()->isSuperAdmin())
        <a href="{{ route('pos.index') }}" id="nav-pos" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-pc-display-horizontal"></i> Kasir / POS Offline
        </a>
        @endif

        <p class="nav-section-label">ERP Core Modules</p>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('supervisor'))
        <a href="{{ route('finance.index') }}" class="{{ request()->routeIs('finance.*') ? 'active' : '' }}">
            <i class="bi bi-bank2"></i> Finance & Accounting
        </a>
        <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> HR & Payroll
        </a>
        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i> CRM & Loyalty
        </a>
        @endif

        @if(auth()->user()->hasPermission('view-reports') || auth()->user()->isSuperAdmin())
        <p class="nav-section-label">Analytics</p>
        <a href="{{ route('reports.wms') }}" class="{{ request()->routeIs('reports.wms') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-steps"></i> Metrik Operasional WMS
        </a>
        <a href="{{ route('reports.analytics') }}" id="nav-report" class="{{ request()->routeIs('reports.analytics') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i> Live Advanced Analytics
        </a>
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.index') ? 'active' : '' }}">
            <i class="bi bi-pie-chart-fill"></i> Analisa Penjualan (POS)
        </a>
        <a href="{{ route('assets.map') }}" class="{{ request()->routeIs('assets.map') ? 'active' : '' }}">
            <i class="bi bi-geo-fill"></i> GPS Tracking Asset
        </a>
        @endif

        @if(auth()->user()->hasPermission('manage-users') || auth()->user()->hasPermission('manage-master-data') || auth()->user()->isSuperAdmin())
        <p class="nav-section-label">System & Settings</p>
        @if(auth()->user()->hasPermission('manage-master-data'))
        <a href="{{ route('warehouses.index') }}" class="{{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
            <i class="bi bi-building-fill"></i> Manajemen Gudang
        </a>
        @endif
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('outlets.index') }}" class="{{ request()->routeIs('outlets.*') ? 'active' : '' }}">
            <i class="bi bi-shop"></i> Cabang / Outlet
        </a>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock-fill"></i> Akun & Role RBAC
        </a>
        <a href="{{ route('audit_logs.index') }}" class="{{ request()->routeIs('audit_logs.*') ? 'active' : '' }}">
            <i class="bi bi-eye-fill"></i> System Audit Trails
        </a>
        @endif
        @endif

    </div>

    {{-- User Info --}}
    <div class="sidebar-user mt-auto">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="text-white fw-bold text-truncate" style="font-size:.85rem;">{{ auth()->user()->name }}</div>
                @foreach(auth()->user()->roles as $role)
                <span class="role-badge role-{{ $role->slug }}">
                    {{ $role->name }}
                </span>
                @endforeach
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2" 
                    style="background:rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1); color: #94a3b8; transition: all .3s;">
                <i class="bi bi-power"></i> Keluar Sistem
            </button>
        </form>
    </div>

    {{-- Watermark / Powered By --}}
    <div class="text-center pb-4 pt-1">
        <span style="font-size: 0.6rem; font-weight: 800; color: #475569; letter-spacing: 0.05em; text-transform: uppercase;">
            Powered By
        </span><br>
        <span style="font-size: 0.65rem; font-weight: 800; color: #6366f1; letter-spacing: 0.05em;">
            PT. SADAJIWA TEKNOLOGI INDONESIA
        </span>
    </div>
</nav>

{{-- ── MAIN CONTENT ──────────────────────────────────────────── --}}
<main id="main">
    <div id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebar-toggle" class="btn btn-light d-lg-none border shadow-sm rounded-3 p-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="ms-auto d-flex align-items-center gap-4">
            <div id="digital-clock" class="d-none d-lg-flex flex-column align-items-end text-end">
                <div class="fw-800 text-primary" id="clock-time" style="font-size: 1.1rem; line-height: 1;">00:00:00</div>
                <div class="text-muted small" id="clock-date" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Memuat...</div>
            </div>
            <div class="vr mx-2 opacity-25"></div>
            <a href="{{ route('inventories.index') }}" class="btn btn-white border shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center position-relative" style="width: 40px; height: 40px; border-color: var(--border-color) !important;">
                <i class="bi bi-bell-fill text-primary"></i>
                @if(isset($globalLowStockCount) && $globalLowStockCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                    {{ $globalLowStockCount }}
                </span>
                @endif
            </a>
            <button id="theme-toggle" class="btn btn-white border shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-color: var(--border-color) !important;">
                <i class="bi bi-moon-stars-fill text-primary" id="theme-icon"></i>
            </button>
            <div class="vr mx-2 opacity-25"></div>
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-small rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="fw-bold small d-none d-md-block">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        // ── Theme Toggle Logic ──────────────────────────────────────────
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon   = document.getElementById('theme-icon');
        const htmlElement = document.documentElement;

        const currentTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', currentTheme);
        htmlElement.setAttribute('data-bs-theme', currentTheme); // Sync with Bootstrap 5
        updateThemeIcon(currentTheme);

        themeToggle.addEventListener('click', () => {
            const newTheme = htmlElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            htmlElement.setAttribute('data-theme', newTheme);
            htmlElement.setAttribute('data-bs-theme', newTheme); // Sync with Bootstrap 5
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
                themeIcon.classList.replace('text-primary', 'text-warning');
            } else {
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
                themeIcon.classList.replace('text-warning', 'text-primary');
            }
        }

        // ── Digital Clock ──────────────────────────────────────────────
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            
            const clockTime = document.getElementById('clock-time');
            const clockDate = document.getElementById('clock-date');
            
            if (clockTime) clockTime.innerText = timeStr;
            if (clockDate) clockDate.innerText = dateStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ── Auto-focus barcode input ────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeInput = document.querySelector('.barcode-input');
            if (barcodeInput) barcodeInput.focus();
        });

        // ── Mobile Sidebar Toggle ───────────────────────────────────────
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }
    </script>
</body>
</html>
