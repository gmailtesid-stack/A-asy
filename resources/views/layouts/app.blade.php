<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-ASY POS')</title>
    <meta name="description" content="@yield('meta_description', 'Sistem POS Multi-Outlet E-ASY — Kasir, Inventori, Laporan')">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

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
            --primary:    #6366f1;
            --primary-dk: #4f46e5;
            --secondary:  #64748b;
            --sidebar-w:  280px;
            --sidebar-bg: #0f172a;
            --topbar-h:   72px;
            --accent:     #818cf8;
            --glass:      rgba(255, 255, 255, 0.7);
            --bg-main:    #f8fafc;
        }

        * { box-sizing: border-box; }
        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; 
            background: var(--bg-main); 
            color: #1e293b; 
            margin: 0;
            overflow-x: hidden;
        }

        /* ── Sidebar ─────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: all .4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 30px rgba(0,0,0,.05);
        }
        .sidebar-brand {
            padding: 2rem 1.5rem;
            margin-bottom: 1rem;
        }
        .sidebar-brand h4 {
            color: #fff;
            font-weight: 800;
            font-size: 1.6rem;
            margin: 0;
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo {
            width: 38px; height: 38px;
            background: var(--primary);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        .sidebar-brand span { color: var(--accent); }
        .sidebar-brand small { color: #94a3b8; font-size: 0.75rem; display: block; margin-top: 4px; }

        .nav-section-label {
            color: #475569;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.5rem 1.75rem 0.75rem;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1.5rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: .925rem;
            font-weight: 600;
            border-radius: 12px;
            margin: 4px 16px;
            transition: all .3s;
        }
        .sidebar-nav a:hover {
            background: rgba(255,255,255,.05);
            color: #f8fafc;
            transform: translateX(5px);
        }
        .sidebar-nav a.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0.05) 100%);
            color: #fff;
            border-left: 4px solid var(--primary);
            padding-left: 1.25rem;
        }
        .sidebar-nav a i { font-size: 1.2rem; transition: transform .3s; }
        .sidebar-nav a:hover i { transform: scale(1.2); }

        .sidebar-user {
            margin: 2rem 16px 1.5rem;
            padding: 1.25rem;
            background: rgba(255,255,255,.03);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.05);
        }
        .sidebar-user .avatar {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,.2);
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
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            position: sticky;
            top: 0; z-index: 900;
        }
        .page-content { padding: 2.5rem; flex: 1; }

        /* ── Cards ───────────────────────────────────────── */
        .card { 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            border-radius: 20px; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08); }

        /* ── Glass Elements ─────────────────────────────── */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.5);
        }

        /* ── Custom Badges ─────────────────────────────── */
        .role-badge {
            font-size: 0.65rem;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .role-super_admin { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .role-manager     { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .role-cashier     { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <h4 class="align-items-center">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;">
            <span class="ms-2">E-ASY</span> <span style="font-weight: 300;">POS</span>
        </h4>
        <small class="ps-1">{{ auth()->user()->outlet?->name ?? 'Pusat Distribusi' }}</small>
    </div>

    <div class="sidebar-nav mt-2">

        <p class="nav-section-label">Menu Utama</p>

        @if(auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isSuperAdmin())
        <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Kasir / POS
        </a>
        @endif

        @if(!auth()->user()->isCashier())
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-pie-chart-fill"></i> Laporan
        </a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-fill"></i> Produk
        </a>
        <a href="{{ route('inventories.index') }}" class="{{ request()->routeIs('inventories.*') ? 'active' : '' }}">
            <i class="bi bi-archive-fill"></i> Inventori
        </a>
        @endif

        @if(auth()->user()->isSuperAdmin())
        <p class="nav-section-label">Administrasi</p>
        <a href="{{ route('outlets.index') }}" class="{{ request()->routeIs('outlets.*') ? 'active' : '' }}">
            <i class="bi bi-shop"></i> Outlet
        </a>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i> Pengguna
        </a>
        @endif

    </div>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="text-white fw-bold text-truncate" style="font-size:.85rem;">{{ auth()->user()->name }}</div>
                <span class="role-badge role-{{ auth()->user()->role }}">
                    {{ str_replace('_', ' ', auth()->user()->role) }}
                </span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="btn btn-sm w-100 text-muted d-flex align-items-center justify-content-center gap-2" 
                    style="background:rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);">
                <i class="bi bi-power"></i> Keluar Sistem
            </button>
        </form>
    </div>
</nav>

{{-- ── MAIN CONTENT ──────────────────────────────────────────── --}}
<main id="main">
    <div id="topbar">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                @yield('breadcrumb')
            </ol>
        </nav>
        <div class="ms-auto d-flex align-items-center gap-3">
            {{-- Notifikasi Low Stock (Disabled temporarily due to missing table) --}}
            {{-- 
            @if(auth()->user()->unreadNotifications->count() > 0)
            ...
            @endif 
            --}}
            <span class="text-muted small">{{ now()->format('d M Y, H:i') }}</span>
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
</body>
</html>
