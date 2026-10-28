<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title>@yield('title', 'LikhaSpace | Digital Artwork Marketplace')</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('likhaspace-theme');
            document.documentElement.setAttribute('data-bs-theme', savedTheme === 'light' ? 'light' : 'dark');
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root, [data-bs-theme="dark"] {
            color-scheme: dark;
            --studio-primary: #6C5CE7;
            --studio-accent: #8C82F5;
            --studio-bg: #121214;
            --studio-card: #1E1E2E;
            --studio-text: #FAF7F2;
            --studio-muted: #C7C4D8;
            --studio-border: #39334B;
            --studio-primary-text: var(--studio-text);
            --studio-link: #BDB5FF;
            --sidebar-bg: #1A1625;
            --sidebar-hover: #352D49;
            --sidebar-text: #F5F1FF;
            --page: var(--studio-bg);
            --surface: var(--studio-card);
            --surface-soft: #221D2F;
            --surface-strong: #30283F;
            --ink: var(--studio-text);
            --muted: var(--studio-muted);
            --line: var(--studio-border);
            --purple: var(--studio-primary);
            --purple-hover: var(--studio-primary);
            --button-ink: var(--studio-primary-text);
            --shadow: 0 8px 24px rgba(0, 0, 0, .25);
            --bs-body-bg: var(--page);
            --bs-body-color: var(--ink);
            --bs-secondary-color: var(--muted);
            --bs-secondary-bg: var(--surface-soft);
            --bs-tertiary-bg: var(--surface-strong);
            --bs-emphasis-color: var(--studio-text);
            --bs-border-color: var(--line);
            --bs-card-bg: var(--studio-card);
            --bs-card-color: var(--studio-text);
            --bs-primary: var(--studio-primary);
            --bs-primary-rgb: 108, 92, 231;
            --bs-link-color: var(--studio-accent);
            --bs-link-hover-color: var(--studio-accent);
        }
        [data-bs-theme="light"] {
            color-scheme: light;
            --studio-primary: #6C5CE7;
            --studio-accent: #5146B8;
            --studio-bg: #FAF7F2;
            --studio-card: #F2EEF9;
            --studio-text: #29263B;
            --studio-muted: #5D5A72;
            --studio-border: #DDD6EB;
            --studio-primary-text: #FFFFFF;
            --studio-link: #5146B8;
            --sidebar-bg: #F2EEF9;
            --sidebar-hover: #E7E1F5;
            --sidebar-text: #302D49;
            --page: var(--studio-bg);
            --surface: var(--studio-card);
            --surface-soft: #F7F4FC;
            --surface-strong: #E9E3F5;
            --ink: var(--studio-text);
            --muted: var(--studio-muted);
            --line: var(--studio-border);
            --purple: var(--studio-primary);
            --purple-hover: var(--studio-primary);
            --button-ink: var(--studio-primary-text);
            --shadow: 0 8px 24px rgba(76, 62, 130, .10);
            --bs-body-bg: var(--page);
            --bs-body-color: var(--ink);
            --bs-secondary-color: var(--muted);
            --bs-secondary-bg: var(--surface-soft);
            --bs-tertiary-bg: var(--surface-strong);
            --bs-emphasis-color: var(--studio-text);
            --bs-border-color: var(--line);
            --bs-card-bg: var(--studio-card);
            --bs-card-color: var(--studio-text);
            --bs-primary: var(--studio-primary);
            --bs-primary-rgb: 108, 92, 231;
            --bs-link-color: var(--studio-primary);
            --bs-link-hover-color: var(--studio-primary);
        }
        * { box-sizing: border-box; }
        html, body { transition: background-color .2s ease, color .2s ease; }
        body { min-height: 100vh; margin: 0; overflow-x: hidden; background: var(--page); color: var(--ink); font-family: Inter, sans-serif; }
        h1, h2, h3, h4, h5, h6, .brand-font { font-family: Poppins, sans-serif; color: var(--ink); letter-spacing: -.02em; }
        a { color: var(--bs-link-color); }
        .site-header { position: sticky; top: 0; z-index: 1030; min-height: 76px; background: var(--page); border-bottom: 1px solid var(--line); }
        .header-inner { min-height: 76px; }
        .brand-logo-wrap { display: inline-flex; align-items: center; }
        .brand-logo { display: block; width: 138px; height: auto; }
        .brand-logo-dark { display: none; }
        [data-bs-theme="dark"] .brand-logo-light { display: none; }
        [data-bs-theme="dark"] .brand-logo-dark { display: block; }
        .app-sidebar { --bs-offcanvas-width: 280px; width: 240px; border-right: 1px solid var(--line); border-radius: 0 18px 18px 0; background: var(--sidebar-bg); box-shadow: var(--shadow); transition: width .22s ease, transform .22s ease; }
        .sidebar-inner { display: flex; min-height: 100%; flex-direction: column; padding: 18px 12px; }
        .sidebar-top { display: flex; min-height: 40px; align-items: center; justify-content: space-between; gap: 8px; padding: 0 7px; }
        .sidebar-account { min-width: 0; overflow: hidden; white-space: nowrap; }
        .sidebar-collapse-toggle { display: inline-grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border: 1px solid var(--line); border-radius: 10px; background: transparent; color: var(--sidebar-text); }
        .sidebar-collapse-toggle:hover { background: var(--sidebar-hover); }
        .sidebar-close { border: 0; background: transparent; color: var(--sidebar-text); font-size: 1.25rem; }
        .sidebar-label { padding: 12px 12px 7px; color: var(--sidebar-text); opacity: .7; font-size: .68rem; font-weight: 700; letter-spacing: .1em; }
        .side-link { display: flex; min-width: 0; align-items: center; gap: 13px; min-height: 42px; margin: 2px 0; padding: 9px 12px; border-radius: 11px; color: var(--sidebar-text); font-size: .9rem; font-weight: 600; text-decoration: none; white-space: nowrap; }
        .side-link:hover { background: var(--sidebar-hover); color: var(--studio-link); }
        .side-link.active { background: var(--studio-primary); color: #FFFFFF; }
        .side-link i { width: 20px; flex: 0 0 20px; font-size: 1.08rem; text-align: center; }
        .sidebar-spacer { flex: 1; }
        .app-main { display: flex; width: calc(100% - 240px); min-width: 0; min-height: calc(100vh - 76px); flex-direction: column; margin-left: 240px; }
        .app-content { width: 100%; min-width: 0; flex: 1 0 auto; padding: 30px clamp(18px, 3vw, 40px); }
        .btn-likha, .btn-violet, .btn-purple { border: 1px solid var(--studio-primary); background: var(--studio-primary); color: var(--studio-primary-text); }
        .btn-likha:hover, .btn-violet:hover, .btn-purple:hover { border-color: var(--studio-primary); background: var(--studio-primary); color: var(--studio-primary-text); filter: brightness(1.12); }
        .btn-primary { --bs-btn-color: var(--studio-primary-text); --bs-btn-bg: var(--studio-primary); --bs-btn-border-color: var(--studio-primary); --bs-btn-hover-color: var(--studio-primary-text); --bs-btn-hover-bg: var(--studio-primary); --bs-btn-hover-border-color: var(--studio-primary); --bs-btn-active-color: var(--studio-primary-text); --bs-btn-active-bg: var(--studio-primary); --bs-btn-active-border-color: var(--studio-primary); --bs-btn-disabled-color: var(--studio-primary-text); --bs-btn-disabled-bg: var(--studio-primary); --bs-btn-disabled-border-color: var(--studio-primary); --bs-btn-focus-shadow-rgb: 108, 92, 231; }
        .btn-outline-likha { border: 1px solid var(--studio-border); color: var(--studio-link); }
        .btn-outline-likha:hover { border-color: var(--studio-primary); background: var(--studio-primary); color: var(--studio-primary-text); }
        .bg-studio-card { border: 1px solid var(--studio-border); border-radius: 16px; background: var(--studio-card); color: var(--studio-text); }
        .text-cyan { color: var(--studio-link) !important; }
        .text-primary { color: var(--studio-link) !important; }
        .studio-muted { color: var(--studio-muted); }
        .border, .border-top, .border-bottom, .border-start, .border-end { border-color: var(--studio-border) !important; }
        .alert-success, .alert-danger, .alert-warning { border-color: var(--studio-border); background: var(--surface-strong); color: var(--studio-text); }
        .text-danger { color: var(--studio-link) !important; }
        .invalid-feedback, .invalid-tooltip { color: var(--studio-link); }
        .form-control.is-invalid, .form-select.is-invalid { border-color: var(--studio-accent); background-image: none; }
        .theme-toggle { width: 38px; height: 38px; padding: 0; border: 1px solid var(--line); border-radius: 50%; background: var(--surface); color: var(--ink); }
        .site-footer { display: flex; width: 100%; min-width: 0; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px; padding: 22px clamp(18px, 3vw, 40px); background: var(--surface-strong); color: var(--ink); font-size: .78rem; }
        .site-footer > * { min-width: 0; }
        .site-footer-links { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 18px; }
        .site-footer a { color: inherit; text-decoration: none; }
        .site-footer a:hover { color: var(--studio-link); }
        .text-muted, .text-secondary { color: var(--muted) !important; }
        .form-control, .form-select, .input-group-text { border-color: var(--line); background-color: var(--surface); color: var(--ink); }
        .dropdown-menu { --bs-dropdown-bg: var(--surface); --bs-dropdown-color: var(--ink); --bs-dropdown-link-color: var(--ink); --bs-dropdown-link-hover-bg: var(--surface-soft); --bs-dropdown-link-hover-color: var(--studio-link); --bs-dropdown-border-color: var(--line); }
        .form-control::placeholder { color: var(--muted); opacity: .8; }
        .form-control:focus, .form-select:focus { border-color: var(--purple); background-color: var(--surface); color: var(--ink); box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--purple) 20%, transparent); }
        @media (min-width: 992px) {
            .app-sidebar { position: fixed; top: 76px; bottom: 0; left: 0; visibility: visible !important; transform: none !important; overflow-y: auto; }
            body.sidebar-collapsed .app-sidebar { width: 76px; }
            body.sidebar-collapsed .app-main { width: calc(100% - 76px); margin-left: 76px; }
            body.sidebar-collapsed .sidebar-account,
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .side-link-text { display: none; }
            body.sidebar-collapsed .sidebar-top { justify-content: center; padding: 0; }
            body.sidebar-collapsed .sidebar-collapse-toggle i { transform: rotate(180deg); }
            body.sidebar-collapsed .side-link { position: relative; justify-content: center; padding-right: 8px; padding-left: 8px; }
            body.sidebar-collapsed .side-link i { width: 24px; flex-basis: 24px; }
            body.sidebar-collapsed .side-link:hover::after { position: absolute; z-index: 5; top: 50%; left: calc(100% + 10px); padding: 6px 10px; transform: translateY(-50%); border: 1px solid var(--line); border-radius: 8px; background: var(--surface); color: var(--ink); content: attr(aria-label); font-size: .78rem; box-shadow: var(--shadow); }
        }
        @media (max-width: 991.98px) {
            .app-sidebar { width: min(280px, 86vw); max-width: 86vw; }
            .sidebar-inner { min-height: 100%; }
            .sidebar-top { margin-bottom: 12px; }
            .app-main { width: 100%; min-height: calc(100vh - 76px); margin-left: 0; }
            .app-content { padding: 24px 18px; }
        }
        @media (max-width: 575.98px) {
            .brand-logo { width: 96px; }
            .header-inner { gap: 6px; padding-right: 10px !important; padding-left: 10px !important; }
            .header-inner > .d-flex:first-child { gap: 8px !important; }
            .header-actions { gap: 5px !important; }
            .theme-toggle { width: 34px; height: 34px; }
            .header-actions .admin-link { display: none; }
            .header-actions .admin-clearance-link { display: none; }
            .app-content { padding: 20px 14px; }
            .site-footer { justify-content: center !important; padding: 18px 14px; text-align: center; }
            .site-footer-links { justify-content: center; }
        }
        @media (max-width: 359.98px) {
            .header-actions .register-link { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
@php($role = auth()->user()?->role ?? 'guest')
@php($workspaceContext = in_array($role, ['artist', 'admin'], true) && request()->routeIs('artist.*', 'admin.*'))
<header class="site-header">
    <div class="container-fluid px-3 px-md-4 d-flex align-items-center justify-content-between header-inner">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#siteSidebar" aria-controls="siteSidebar" aria-label="Open navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <a href="{{ route('home') }}" class="brand-logo-wrap" aria-label="LikhaSpace home">
                <img src="{{ asset('likhaSpace_Logo.png') }}" alt="LikhaSpace" class="brand-logo brand-logo-light">
                <img src="{{ asset('likhaSpace_Logo_Dark.png') }}" alt="" aria-hidden="true" class="brand-logo brand-logo-dark">
            </a>
        </div>
        <div class="d-flex align-items-center gap-2 header-actions">
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Switch color theme" title="Switch color theme"><i class="bi bi-moon-stars"></i></button>
            @if ($role === 'guest')
                <a href="{{ route('login') }}" class="btn btn-outline-likha btn-sm rounded-pill px-3">Sign in</a>
                <a href="{{ route('register') }}" class="btn btn-likha btn-sm rounded-pill px-3 register-link">Join</a>
            @else
                @if (in_array($role, ['client', 'artist'], true))
                    @php($profileRoute = $role === 'artist' ? 'artist.profile' : 'profile')
                    <a href="{{ route($profileRoute) }}" class="btn btn-outline-likha btn-sm rounded-pill px-3 d-none d-sm-inline-flex">My profile</a>
                @endif
                @if ($role === 'admin')
                    <a href="{{ route('admin.vault.gate') }}" class="btn btn-outline-likha btn-sm rounded-pill px-3 admin-clearance-link">User clearance</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-outline-likha btn-sm rounded-pill px-3" type="submit">Sign out</button>
                </form>
            @endif
        </div>
    </div>
</header>

<aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="siteSidebar" aria-label="Main navigation">
    <div class="sidebar-inner">
        <div class="sidebar-top">
            <div class="sidebar-account d-flex align-items-center gap-2">
                <span class="rounded-circle d-grid place-items-center fw-bold" style="width: 38px; height: 38px; flex: 0 0 38px; background: var(--sidebar-hover); color: var(--sidebar-text)"><i class="bi bi-person"></i></span>
                <div class="small fw-semibold" style="color: var(--sidebar-text)">{{ $workspaceContext ? ($role === 'artist' ? 'Artist Studio' : 'Platform Admin') : ($role === 'guest' ? 'Welcome' : 'My Account') }}<small class="d-block fw-normal opacity-75">{{ ucfirst($role) }}</small></div>
            </div>
            <button class="sidebar-collapse-toggle d-none d-lg-inline-grid" type="button" data-sidebar-collapse aria-label="Collapse sidebar" aria-pressed="false" title="Collapse sidebar"><i class="bi bi-chevron-left"></i></button>
            <button class="sidebar-close d-lg-none" type="button" data-bs-dismiss="offcanvas" aria-label="Close navigation"><i class="bi bi-x-lg"></i></button>
        </div>
        @if ($role === 'artist')
            <div class="sidebar-label">ARTIST STUDIO</div>
            <a class="side-link {{ request()->routeIs('artist.dashboard') ? 'active' : '' }}" href="{{ route('artist.dashboard') }}" aria-label="Studio Dashboard" title="Studio Dashboard"><i class="bi bi-speedometer2"></i><span class="side-link-text">Studio Dashboard</span></a>
            <a class="side-link {{ request()->routeIs('artist.inventory') ? 'active' : '' }}" href="{{ route('artist.inventory') }}" aria-label="My Artwork" title="My Artwork"><i class="bi bi-palette"></i><span class="side-link-text">My Artwork</span></a>
            <a class="side-link {{ request()->routeIs('artist.profile') ? 'active' : '' }}" href="{{ route('artist.profile') }}" aria-label="Artist Profile" title="Artist Profile"><i class="bi bi-person-gear"></i><span class="side-link-text">Artist Profile</span></a>
            <div class="sidebar-spacer"></div>
            <a class="side-link" href="{{ route('explore') }}" aria-label="Marketplace" title="Marketplace"><i class="bi bi-arrow-left-circle"></i><span class="side-link-text">Marketplace</span></a>
        @elseif ($role === 'admin')
            <div class="sidebar-label">PLATFORM ADMIN</div>
            <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" aria-label="Overview" title="Overview"><i class="bi bi-speedometer2"></i><span class="side-link-text">Overview</span></a>
            <a class="side-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}" aria-label="Manage Users" title="Manage Users"><i class="bi bi-people"></i><span class="side-link-text">Manage Users</span></a>
            <a class="side-link {{ request()->routeIs('admin.approvals') ? 'active' : '' }}" href="{{ route('admin.approvals') }}" aria-label="Artwork Approvals" title="Artwork Approvals"><i class="bi bi-patch-check"></i><span class="side-link-text">Artwork Approvals</span></a>
            <div class="sidebar-spacer"></div>
            <a class="side-link" href="{{ route('explore') }}" aria-label="Marketplace" title="Marketplace"><i class="bi bi-shop"></i><span class="side-link-text">Marketplace</span></a>
        @elseif ($role === 'client')
            <div class="sidebar-label">DISCOVER</div>
            <a class="side-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" aria-label="Home" title="Home"><i class="bi bi-house-door"></i><span class="side-link-text">Home</span></a>
            <a class="side-link {{ request()->routeIs('explore') ? 'active' : '' }}" href="{{ route('explore') }}" aria-label="Explore Art" title="Explore Art"><i class="bi bi-compass"></i><span class="side-link-text">Explore Art</span></a>
            <a class="side-link" href="{{ route('home') }}#collections" aria-label="Categories" title="Categories"><i class="bi bi-grid"></i><span class="side-link-text">Categories</span></a>
            <a class="side-link {{ request()->routeIs('favorites') ? 'active' : '' }}" href="{{ route('favorites') }}" aria-label="My Favorites" title="My Favorites"><i class="bi bi-heart"></i><span class="side-link-text">My Favorites</span></a>
            <a class="side-link {{ request()->routeIs('cart', 'checkout') ? 'active' : '' }}" href="{{ route('cart') }}" aria-label="My Cart" title="My Cart"><i class="bi bi-cart3"></i><span class="side-link-text">My Cart</span></a>
            <a class="side-link {{ request()->routeIs('transactions', 'transactions.show') ? 'active' : '' }}" href="{{ route('transactions') }}" aria-label="My Orders" title="My Orders"><i class="bi bi-receipt"></i><span class="side-link-text">My Orders</span></a>
            <a class="side-link" href="{{ route('home') }}#about" aria-label="About" title="About"><i class="bi bi-info-circle"></i><span class="side-link-text">About</span></a>
            <a class="side-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}" aria-label="My Profile" title="My Profile"><i class="bi bi-person-gear"></i><span class="side-link-text">My Profile</span></a>
            <div class="sidebar-spacer"></div>
        @else
            <div class="sidebar-label">DISCOVER</div>
            <a class="side-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" aria-label="Home" title="Home"><i class="bi bi-house-door"></i><span class="side-link-text">Home</span></a>
            <a class="side-link {{ request()->routeIs('explore') ? 'active' : '' }}" href="{{ route('explore') }}" aria-label="Explore Art" title="Explore Art"><i class="bi bi-compass"></i><span class="side-link-text">Explore Art</span></a>
            <div class="sidebar-spacer"></div>
        @endif
    </div>
</aside>

<div class="app-main">
    <main class="app-content">@yield('content')</main>
    <footer class="site-footer d-flex flex-wrap align-items-center justify-content-between gap-3">
        <span>© {{ date('Y') }} LikhaSpace. All rights reserved.</span>
        <div class="site-footer-links">
            <a href="{{ route('explore') }}">Explore art</a>
            @if ($role === 'client')
                <a href="{{ route('cart') }}">Cart</a>
                <a href="{{ route('transactions') }}">My orders</a>
            @endif
            @if ($role === 'artist')
                <a href="{{ route('artist.dashboard') }}">Artist studio</a>
            @elseif ($role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Platform admin</a>
            @endif
            <span class="d-none d-sm-inline">A space for Filipino creativity.</span>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const applyTheme = (theme) => {
        const selectedTheme = theme === 'light' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', selectedTheme);
        localStorage.setItem('likhaspace-theme', selectedTheme);
        document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
            toggle.innerHTML = selectedTheme === 'dark'
                ? '<i class="bi bi-sun"></i>'
                : '<i class="bi bi-moon-stars"></i>';
        });
    };

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        applyTheme(document.documentElement.getAttribute('data-bs-theme'));
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        });
    });

    (() => {
        const sidebar = document.getElementById('siteSidebar');
        const toggle = document.querySelector('[data-sidebar-collapse]');
        if (!sidebar || !toggle) return;

        const setCollapsed = (collapsed) => {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            toggle.setAttribute('aria-pressed', String(collapsed));
            toggle.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
            toggle.setAttribute('title', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
            localStorage.setItem('likhaspace-sidebar-collapsed', String(collapsed));
        };

        setCollapsed(localStorage.getItem('likhaspace-sidebar-collapsed') === 'true');
        toggle.addEventListener('click', () => {
            setCollapsed(!document.body.classList.contains('sidebar-collapsed'));
        });
    })();
</script>
@stack('scripts')
</body>
</html>
