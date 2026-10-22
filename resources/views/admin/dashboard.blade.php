<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>Admin Dashboard | LikhaSpace</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('likhaspace-theme');
            document.documentElement.dataset.theme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root, [data-theme="light"] {
            color-scheme: light;
            --page: #fff8ef;
            --surface: #fff;
            --surface-soft: #f4efff;
            --surface-strong: #e7ddff;
            --ink: #211d4d;
            --muted: #77738f;
            --line: #ece6f8;
            --purple: #7254e8;
            --purple-soft: #f1eaff;
            --shadow: 0 5px 18px rgba(32, 23, 69, .07);
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
            --purple-soft: #332b49;
            --shadow: 0 5px 18px rgba(0, 0, 0, .22);
        }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; background: var(--page); color: var(--ink); font-family: Inter, sans-serif; }
        h1, h2, h3, h4, h5, h6 { color: var(--ink); font-family: Poppins, sans-serif; }
        .admin-sidebar { position: fixed; inset: 0 auto 0 0; z-index: 1020; display: flex; width: 230px; flex-direction: column; padding: 22px 14px; border-right: 1px solid var(--line); background: var(--surface-soft); }
        .brand-wrap { display: inline-flex; align-items: center; align-self: flex-start; margin: 0 0 24px 4px; }
        .brand-wrap img { width: 138px; height: auto; }
        .brand-wrap .brand-logo-dark { display: none; }
        [data-theme="dark"] .brand-wrap .brand-logo-light { display: none; }
        [data-theme="dark"] .brand-wrap .brand-logo-dark { display: block; }
        .nav-caption { margin: 12px 12px 7px; color: var(--muted); font-size: .68rem; font-weight: 700; letter-spacing: .1em; }
        .admin-nav { display: flex; flex-direction: column; gap: 3px; }
        .admin-nav a { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; color: var(--ink); font-size: .88rem; font-weight: 600; text-decoration: none; }
        .admin-nav a:hover, .admin-nav a.active { background: var(--surface-strong); color: var(--purple); }
        .admin-nav i { width: 20px; text-align: center; }
        .main-area { min-height: 100vh; margin-left: 230px; }
        .topbar { position: sticky; top: 0; z-index: 1010; display: flex; min-height: 72px; align-items: center; justify-content: space-between; gap: 18px; padding: 12px clamp(16px, 3vw, 36px); border-bottom: 1px solid var(--line); background: var(--surface); }
        .topbar-search { width: min(360px, 38vw); }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .theme-toggle { width: 38px; height: 38px; border: 1px solid var(--line); border-radius: 50%; background: var(--surface); color: var(--ink); }
        .dashboard-content { max-width: 1480px; margin: auto; padding: 30px clamp(16px, 3vw, 36px); }
        .page-heading h1 { margin-bottom: 3px; font-size: 1.7rem; font-weight: 700; letter-spacing: -.04em; }
        .page-heading p { color: var(--muted); }
        .welcome-panel, .stat-card, .transactions-panel { border: 1px solid var(--line); border-radius: 16px; background: var(--surface); box-shadow: var(--shadow); }
        .welcome-panel { display: flex; align-items: center; justify-content: space-between; gap: 24px; margin: 22px 0 16px; padding: 22px; }
        .welcome-copy small { color: var(--purple); font-size: .7rem; font-weight: 700; letter-spacing: .08em; }
        .welcome-copy h2 { margin: 8px 0 4px; font-size: 1.35rem; font-weight: 700; }
        .welcome-copy p { margin: 0; color: var(--muted); font-size: .84rem; }
        .welcome-kpis { display: grid; grid-template-columns: repeat(3, minmax(90px, 1fr)); gap: 18px; padding: 12px 16px; border-radius: 12px; background: var(--surface-soft); }
        .welcome-kpis span { display: block; color: var(--muted); font-size: .65rem; }
        .welcome-kpis strong { display: block; margin: 4px 0; font-size: 1.05rem; }
        .welcome-kpis small { color: var(--purple); font-size: .64rem; }
        .stat-card { display: flex; min-height: 94px; align-items: center; gap: 13px; padding: 16px; }
        .stat-icon { display: grid; width: 40px; height: 40px; flex: 0 0 40px; place-items: center; border-radius: 12px; background: var(--purple-soft); color: var(--purple); }
        .stat-card span { display: block; color: var(--muted); font-size: .72rem; }
        .stat-card strong { display: block; margin: 1px 0; font-size: 1.16rem; }
        .stat-card small { color: var(--purple); font-size: .66rem; }
        .transactions-panel { overflow: hidden; margin-top: 18px; }
        .panel-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 20px; }
        .panel-heading h2 { margin: 0; font-size: 1rem; font-weight: 700; }
        .panel-heading p { margin: 3px 0 0; color: var(--muted); font-size: .75rem; }
        .table { --bs-table-bg: var(--surface); --bs-table-color: var(--ink); --bs-table-hover-bg: var(--surface-soft); --bs-table-hover-color: var(--ink); margin: 0; }
        .table thead th { border-bottom-color: var(--line); background: var(--surface-soft); color: var(--muted); font-size: .65rem; font-weight: 700; letter-spacing: .04em; white-space: nowrap; }
        .table td { padding-top: 13px; padding-bottom: 13px; border-color: var(--line); font-size: .75rem; vertical-align: middle; white-space: nowrap; }
        .status-pill { padding: 5px 9px; border-radius: 999px; background: #e7f7ed; color: #27834b; font-size: .65rem; font-weight: 700; }
        .status-pill.pending { background: #fff1d8; color: #9a6500; }
        .status-pill.processing { background: #eee8ff; color: #674bd0; }
        .table-action { color: var(--purple); font-size: .7rem; text-decoration: none; }
        .page-footer { display: flex; justify-content: space-between; gap: 12px; margin-top: 24px; padding: 14px 20px; border-radius: 12px; background: var(--surface-strong); color: var(--muted); font-size: .7rem; }
        .btn-purple { border: 0; background: var(--purple); color: #fff; }
        .btn-purple:hover { color: #fff; filter: brightness(.94); }
        .btn-outline-purple { border: 1px solid var(--purple); color: var(--purple); }
        .btn-outline-purple:hover { background: var(--purple); color: #fff; }
        .form-control { border-color: var(--line); background: var(--surface); color: var(--ink); }
        .form-control::placeholder { color: var(--muted); }
        .form-control:focus { border-color: var(--purple); background: var(--surface); color: var(--ink); box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--purple) 20%, transparent); }
        @media (max-width: 991.98px) {
            .admin-sidebar { position: relative; inset: auto; width: 100%; padding: 10px 14px; border-right: 0; border-bottom: 1px solid var(--line); }
            .brand-wrap { display: none; }
            .nav-caption { display: none; }
            .admin-nav { flex-direction: row; overflow-x: auto; }
            .admin-nav a { flex: 0 0 auto; padding: 9px 11px; }
            .main-area { margin-left: 0; }
            .topbar { min-height: 64px; }
            .welcome-panel { align-items: stretch; flex-direction: column; }
            .welcome-kpis { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 575.98px) {
            .topbar { flex-wrap: wrap; gap: 9px; padding: 10px 14px; }
            .topbar-search { order: 2; width: 100%; }
            .top-actions { margin-left: auto; }
            .top-actions .store-link { display: none; }
            .dashboard-content { padding: 22px 14px; }
            .page-heading h1 { font-size: 1.4rem; }
            .welcome-panel { padding: 17px; }
            .welcome-kpis { gap: 8px; padding: 10px; }
            .welcome-kpis strong { font-size: .92rem; }
            .welcome-kpis span, .welcome-kpis small { font-size: .58rem; }
            .panel-heading { padding: 15px; }
            .page-footer { flex-direction: column; padding: 13px 15px; }
        }
    </style>
</head>
<body>
<aside class="admin-sidebar" aria-label="Admin navigation">
    <a href="{{ route('explore') }}" class="brand-wrap" aria-label="LikhaSpace storefront">
        <img src="{{ asset('likhaSpace_Logo.png') }}" alt="LikhaSpace" class="brand-logo-light">
        <img src="{{ asset('likhaSpace_Logo_Dark.png') }}" alt="" aria-hidden="true" class="brand-logo-dark">
    </a>
    <div class="nav-caption">MANAGEMENT</div>
    <nav class="admin-nav">
        <a class="active" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid"></i><span>Dashboard</span></a>
        <a href="{{ route('admin.inventory') }}"><i class="bi bi-images"></i><span>Artwork Inventory</span></a>
        <a href="{{ route('admin.dashboard') }}#transactions"><i class="bi bi-receipt"></i><span>Orders &amp; Sales</span></a>
        <a href="{{ route('admin.dashboard') }}#artists"><i class="bi bi-people"></i><span>Artists</span></a>
        <a href="{{ route('admin.dashboard') }}#settings"><i class="bi bi-gear"></i><span>Settings</span></a>
    </nav>
    <div class="mt-auto d-none d-lg-block rounded-3 p-3 small" style="background: var(--surface); color: var(--ink);">
        <div class="fw-semibold"><i class="bi bi-circle-fill text-success me-2" style="font-size: .5rem"></i>All systems operational</div>
        <div class="text-secondary mt-1">Last checked 2 mins ago</div>
    </div>
</aside>

<div class="main-area">
    <header class="topbar">
        <div class="input-group topbar-search">
            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
            <input class="form-control border-start-0" type="search" placeholder="Search orders, artworks, artists..." aria-label="Search dashboard">
        </div>
        <div class="top-actions">
            <a href="{{ route('explore') }}" class="btn btn-outline-purple btn-sm rounded-pill px-3 store-link"><i class="bi bi-shop me-1"></i>Switch to LikhaSpace</a>
            <button type="button" class="btn btn-purple btn-sm rounded-pill px-3"><i class="bi bi-plus-lg me-1"></i>Quick action</button>
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Switch color theme" title="Switch color theme"><i class="bi bi-moon-stars"></i></button>
            <div class="d-none d-sm-block text-end">
                <div class="small fw-bold">Mia Santos</div>
                <div class="text-secondary" style="font-size: .65rem">Studio Admin</div>
            </div>
            <span class="d-inline-grid rounded-circle fw-bold align-items-center justify-content-center" style="width: 34px; height: 34px; background: var(--surface-strong); color: var(--purple)">MS</span>
        </div>
    </header>

    <main class="dashboard-content">
        <div class="page-heading">
            <h1>Dashboard</h1>
            <p class="mb-0">Monitor your LikhaSpace marketplace, sales, and creative community.</p>
        </div>

        <section class="welcome-panel">
            <div class="welcome-copy">
                <small>WEDNESDAY, FEBRUARY 18</small>
                <h2>Welcome back, Admin Mia <span aria-hidden="true">👋</span></h2>
                <p>Here's a clear look at what's happening with LikhaSpace today.</p>
            </div>
            <div class="welcome-kpis" aria-label="Marketplace highlights">
                <div><span>TOTAL REVENUE</span><strong>₱148,500</strong><small>+12.5%</small></div>
                <div><span>ACTIVE LISTINGS</span><strong>124</strong><small>+8 this week</small></div>
                <div><span>PENDING APPROVALS</span><strong>5</strong><small>Needs review</small></div>
            </div>
        </section>

        <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3" aria-label="Marketplace metrics">
            <div class="col"><div class="stat-card h-100"><span class="stat-icon"><i class="bi bi-graph-up-arrow"></i></span><div><span>This month's sales</span><strong>₱86,420</strong><small>+12.5% from January</small></div></div></div>
            <div class="col"><div class="stat-card h-100"><span class="stat-icon"><i class="bi bi-receipt"></i></span><div><span>Total orders</span><strong>342</strong><small>18 received today</small></div></div></div>
            <div class="col"><div class="stat-card h-100"><span class="stat-icon"><i class="bi bi-person-check"></i></span><div><span>Active artists</span><strong>46</strong><small>3 new this month</small></div></div></div>
            <div class="col"><div class="stat-card h-100"><span class="stat-icon"><i class="bi bi-basket"></i></span><div><span>Avg. order value</span><strong>₱2,340</strong><small>+4.2% this month</small></div></div></div>
        </section>

        <section id="transactions" class="transactions-panel">
            <div class="panel-heading">
                <div><h2>Recent Transactions</h2><p>Latest customer activity across LikhaSpace.</p></div>
                <a href="{{ route('admin.inventory') }}" class="table-action">Manage inventory <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th class="ps-3 ps-md-4">ORDER ID</th><th>CUSTOMER</th><th>DATE</th><th>PAYMENT</th><th>AMOUNT</th><th>STATUS</th><th>ACTION</th></tr></thead>
                    <tbody>
                        <tr><td class="ps-3 ps-md-4 text-primary">#ORD-2026-0891</td><td>Isabelle Reyes</td><td>Feb 18, 2026</td><td>GCash</td><td class="fw-bold">₱3,500</td><td><span class="status-pill">Completed</span></td><td><a class="table-action" href="#transactions">Details</a></td></tr>
                        <tr><td class="ps-3 ps-md-4 text-primary">#ORD-2026-0890</td><td>Marco Dela Cruz</td><td>Feb 18, 2026</td><td>Credit Card</td><td class="fw-bold">₱2,850</td><td><span class="status-pill processing">Processing</span></td><td><a class="table-action" href="#transactions">Details</a></td></tr>
                        <tr><td class="ps-3 ps-md-4 text-primary">#ORD-2026-0889</td><td>Andrea Castillo</td><td>Feb 17, 2026</td><td>Maya</td><td class="fw-bold">₱1,250</td><td><span class="status-pill pending">Pending</span></td><td><a class="table-action" href="#transactions">Details</a></td></tr>
                        <tr><td class="ps-3 ps-md-4 text-primary">#ORD-2026-0888</td><td>Nico Pascual</td><td>Feb 16, 2026</td><td>GCash</td><td class="fw-bold">₱4,200</td><td><span class="status-pill processing">Processing</span></td><td><a class="table-action" href="#transactions">Details</a></td></tr>
                    </tbody>
                </table>
            </div>
        </section>
        <footer class="page-footer"><span>© {{ date('Y') }} LikhaSpace</span><span><i class="bi bi-circle-fill text-success me-1" style="font-size: .45rem"></i>System healthy <span class="ms-3">v1.0.4</span></span></footer>
    </main>
</div>
<script>
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const updateIcon = () => {
            button.innerHTML = document.documentElement.dataset.theme === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
        };
        updateIcon();
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = nextTheme;
            localStorage.setItem('likhaspace-theme', nextTheme);
            updateIcon();
        });
    });
</script>
</body>
</html>
