<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-ASY POS')</title>
    <meta name="description" content="@yield('meta_description', 'Sistem POS Multi-Outlet E-ASY — Kasir, Inventori, Laporan')">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary:    #2563eb;
            --primary-dk: #1d4ed8;
            --sidebar-w:  260px;
            --sidebar-bg: #0f172a;
            --topbar-h:   64px;
            --accent:     #38bdf8;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #1e293b; margin: 0; }

        /* ── Sidebar ─────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand h4 {
            color: #fff;
            font-weight: 800;
            font-size: 1.4rem;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .sidebar-brand span { color: var(--accent); }
        .sidebar-brand small { color: #94a3b8; font-size: 0.72rem; }

        .nav-section-label {
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: .75rem 1.25rem .25rem;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all .2s;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(37,99,235,.18);
            color: #fff;
        }
        .sidebar-nav a.active { color: var(--accent); }
        .sidebar-nav a i { font-size: 1.05rem; width: 20px; text-align: center; }

        .sidebar-user {
            margin-top: auto;
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-user .avatar {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .875rem;
        }
        .role-badge {
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
        }
        .role-super_admin { background: #fef3c7; color: #92400e; }
        .role-manager     { background: #dcfce7; color: #166534; }
        .role-cashier     { background: #dbeafe; color: #1e40af; }

        /* ── Main Content ─────────────────────────────────── */
        #main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #topbar {
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0; z-index: 900;
        }
        #topbar .breadcrumb { margin: 0; font-size: .875rem; }
        .page-content { padding: 1.75rem; flex: 1; }

        /* ── Cards ───────────────────────────────────────── */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); }
        .card-header { background: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; }

        /* ── Alerts ─────────────────────────────────────── */
        .toast-container { z-index: 9999; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <h4>E<span>-ASY</span> POS</h4>
        <small>{{ auth()->user()->outlet?->name ?? 'Pusat' }}</small>
    </div>

    <div class="sidebar-nav mt-2">

        <p class="nav-section-label">Menu Utama</p>

        @if(auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isSuperAdmin())
        <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-bag-check-fill"></i> Kasir / POS
        </a>
        @endif

        @if(!auth()->user()->isCashier())
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill"></i> Laporan
        </a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i> Produk
        </a>
        <a href="{{ route('inventories.index') }}" class="{{ request()->routeIs('inventories.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-data-fill"></i> Inventori
        </a>
        @endif

        @if(auth()->user()->isSuperAdmin())
        <p class="nav-section-label">Administrasi</p>
        <a href="{{ route('outlets.index') }}" class="{{ request()->routeIs('outlets.*') ? 'active' : '' }}">
            <i class="bi bi-shop-window"></i> Outlet
        </a>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Pengguna
        </a>
        @endif

    </div>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="text-white fw-600" style="font-size:.8rem;">{{ auth()->user()->name }}</div>
                <span class="role-badge role-{{ auth()->user()->role }}">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                </span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button class="btn btn-sm w-100" style="background:rgba(255,255,255,.08);color:#94a3b8;">
                <i class="bi bi-box-arrow-right"></i> Logout
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
            {{-- Notifikasi Low Stock --}}
            @if(auth()->user()->unreadNotifications->count() > 0)
            <div class="dropdown">
                <button class="btn btn-sm position-relative" id="notifBtn" data-bs-toggle="dropdown">
                    <i class="bi bi-bell-fill text-warning fs-5"></i>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="width:320px;">
                    @foreach(auth()->user()->unreadNotifications->take(5) as $notif)
                    <li>
                        <div class="dropdown-item small">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            {{ $notif->data['message'] }}
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
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
