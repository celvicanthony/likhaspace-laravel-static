<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title', 'LikhaSpace | Digital Artwork Marketplace')</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('likhaspace-theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.dataset.theme = savedTheme || systemTheme;
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root, [data-theme="light"] {
            color-scheme: light;
            --page: #fffaf3;
            --surface: #ffffff;
            --surface-soft: #f5f0ff;
            --surface-strong: #e6ddff;
            --ink: #211d4d;
            --muted: #77738f;
            --line: #eee8fa;
            --purple: #7254e8;
            --purple-hover: #6043d2;
            --shadow: 0 8px 24px rgba(32, 23, 69, .12);
        }
        [data-theme="dark"] {
            color-scheme: dark;
            --page: #15131e;
            --surface: #201d2c;
            --surface-soft: #2a253a;
            --surface-strong: #38304f;
            --ink: #f5f1ff;
            --muted: #b5aecb;
            --line: #393449;
            --purple: #a18aff;
            --purple-hover: #b5a5ff;
            --shadow: 0 8px 24px rgba(0, 0, 0, .25);
        }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; background: var(--page); color: var(--ink); font-family: Inter, sans-serif; }
        h1, h2, h3, h4, h5, h6, .brand-font { font-family: Poppins, sans-serif; color: var(--ink); }
        a { color: var(--purple); }
        .site-header { position: sticky; top: 0; z-index: 1030; min-height: 76px; background: var(--page); border-bottom: 1px solid var(--line); }
        .header-inner { min-height: 76px; }
        .brand-logo-wrap { display: inline-flex; align-items: center; }
        .brand-logo { display: block; width: 138px; height: auto; }
        .brand-logo-dark { display: none; }
        [data-theme="dark"] .brand-logo-light { display: none; }
        [data-theme="dark"] .brand-logo-dark { display: block; }
        .app-sidebar { --bs-offcanvas-width: 230px; background: var(--surface-soft); border-right: 1px solid var(--line); }
        .sidebar-inner { display: flex; height: 100%; flex-direction: column; padding: 22px 14px; }
        .sidebar-label { padding: 12px 12px 7px; color: var(--muted); font-size: .68rem; font-weight: 700; letter-spacing: .1em; }
        .side-link { display: flex; align-items: center; gap: 13px; min-height: 42px; margin: 2px 0; padding: 9px 12px; border-radius: 11px; color: var(--ink); font-size: .9rem; font-weight: 600; text-decoration: none; }
        .side-link:hover, .side-link.active { background: var(--surface-strong); color: var(--purple); }
        .side-link i { width: 20px; font-size: 1.08rem; text-align: center; }
        .sidebar-spacer { flex: 1; }
        .app-main { min-height: calc(100vh - 76px); margin-left: 230px; display: flex; flex-direction: column; }
        .app-content { flex: 1; width: 100%; padding: 30px clamp(18px, 3vw, 40px); }
        .btn-likha { border: 0; background: var(--purple); color: #fff; }
        .btn-likha:hover { background: var(--purple-hover); color: #fff; }
        .btn-outline-likha { border: 1px solid var(--purple); color: var(--purple); }
        .btn-outline-likha:hover { background: var(--purple); color: #fff; }
        .theme-toggle { width: 38px; height: 38px; padding: 0; border: 1px solid var(--line); border-radius: 50%; background: var(--surface); color: var(--ink); }
        .site-footer { padding: 22px clamp(18px, 3vw, 40px); background: var(--surface-strong); color: var(--ink); font-size: .78rem; }
        .site-footer a { color: inherit; text-decoration: none; }
        .site-footer a:hover { color: var(--purple); }
        .text-muted, .text-secondary { color: var(--muted) !important; }
        .form-control, .form-select, .input-group-text { border-color: var(--line); background-color: var(--surface); color: var(--ink); }
        .form-control::placeholder { color: var(--muted); opacity: .8; }
        .form-control:focus, .form-select:focus { border-color: var(--purple); background-color: var(--surface); color: var(--ink); box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--purple) 20%, transparent); }
        @media (min-width: 992px) {
            .app-sidebar { position: fixed; top: 76px; bottom: 0; left: 0; visibility: visible !important; transform: none !important; }
        }
        @media (max-width: 991.98px) {
            .app-main { min-height: calc(100vh - 76px); margin-left: 0; }
            .app-content { padding: 24px 18px; }
        }
        @media (max-width: 575.98px) {
            .brand-logo { width: 96px; }
            .header-inner { gap: 6px; padding-right: 10px !important; padding-left: 10px !important; }
            .header-inner > .d-flex:first-child { gap: 8px !important; }
            .header-actions { gap: 5px !important; }
            .theme-toggle { width: 34px; height: 34px; }
            .header-actions .admin-link { display: none; }
            .app-content { padding: 20px 14px; }
            .site-footer { text-align: center; }
        }
    </style>
    @stack('styles')
</head>
<body>
<header class="site-header">
    <div class="container-fluid px-3 px-md-4 d-flex align-items-center justify-content-between header-inner">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#siteSidebar" aria-controls="siteSidebar" aria-label="Open navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <a href="{{ route('explore') }}" class="brand-logo-wrap" aria-label="LikhaSpace home">
                <img src="{{ asset('likhaSpace_Logo.png') }}" alt="LikhaSpace" class="brand-logo brand-logo-light">
                <img src="{{ asset('likhaSpace_Logo_Dark.png') }}" alt="" aria-hidden="true" class="brand-logo brand-logo-dark">
            </a>
        </div>
        <div class="d-flex align-items-center gap-2 header-actions">
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Switch color theme" title="Switch color theme"><i class="bi bi-moon-stars"></i></button>
            <button class="btn btn-outline-likha btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="bi bi-person me-1"></i><span class="d-none d-sm-inline">Sign in</span><span class="d-sm-none">Sign in</span>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-likha btn-sm rounded-pill px-3 admin-link">Admin portal</a>
        </div>
    </div>
</header>

<aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="siteSidebar" aria-label="Main navigation">
    <div class="sidebar-inner">
        <div class="sidebar-label">DISCOVER</div>
        <a class="side-link {{ request()->routeIs('explore') ? 'active' : '' }}" href="{{ route('explore') }}"><i class="bi bi-compass"></i>Explore Art</a>
        <a class="side-link" href="{{ route('explore') }}"><i class="bi bi-house-door"></i>Home</a>
        <a class="side-link" href="{{ route('explore') }}#artworks"><i class="bi bi-grid"></i>Categories</a>
        <a class="side-link" href="{{ route('explore') }}#artworks"><i class="bi bi-heart"></i>My Favorites</a>
        <a class="side-link" href="{{ route('explore') }}#artworks"><i class="bi bi-cart3"></i>My Cart</a>
        <a class="side-link" href="{{ route('explore') }}#about"><i class="bi bi-info-circle"></i>About</a>
        <a class="side-link" href="#settings"><i class="bi bi-gear"></i>Settings</a>
        <div class="sidebar-spacer"></div>
        <div class="sidebar-label">ADMIN PORTAL</div>
        <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
        <a class="side-link {{ request()->routeIs('admin.inventory') ? 'active' : '' }}" href="{{ route('admin.inventory') }}"><i class="bi bi-box-seam"></i>Inventory</a>
    </div>
</aside>

<div class="app-main">
    <main class="app-content">@yield('content')</main>
    <footer class="site-footer d-flex flex-wrap align-items-center justify-content-between gap-3">
        <span>© {{ date('Y') }} LikhaSpace. All rights reserved.</span>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('explore') }}">Explore art</a>
            <a href="{{ route('admin.dashboard') }}">Admin portal</a>
            <span class="d-none d-sm-inline">A space for Filipino creativity.</span>
        </div>
    </footer>
</div>

@include('auth.login-modal')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const updateIcon = () => {
            button.innerHTML = document.documentElement.dataset.theme === 'dark'
                ? '<i class="bi bi-sun"></i>'
                : '<i class="bi bi-moon-stars"></i>';
        };
        updateIcon();
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = nextTheme;
            localStorage.setItem('likhaspace-theme', nextTheme);
            document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
                toggle.innerHTML = nextTheme === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>
