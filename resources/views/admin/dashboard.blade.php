@extends('layouts.app')

@section('title', 'Platform Overview | LikhaSpace')

@push('styles')
<style>
    .platform-welcome, .platform-metric, .platform-transactions { border: 1px solid var(--studio-border); border-radius: 20px; background: var(--studio-card); box-shadow: var(--shadow); }
    .platform-welcome { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 24px; }
    .platform-welcome p, .metric-label, .platform-caption { color: var(--studio-muted); }
    .platform-kpis { display: grid; grid-template-columns: repeat(3, minmax(90px, 1fr)); gap: 12px; padding: 12px; border-radius: 16px; background: var(--surface-soft); }
    .platform-kpis span { display: block; color: var(--studio-muted); font-size: .68rem; }
    .platform-kpis strong { display: block; margin-top: 5px; }
    .platform-metric { min-height: 100px; padding: 18px; }
    .metric-label { font-size: .78rem; }
    .platform-metric strong { display: block; margin-top: 5px; font-size: 1.35rem; }
    .platform-panel-title { padding: 18px 20px; }
    .platform-transactions { overflow: hidden; }
    .platform-table { --bs-table-bg: transparent; --bs-table-color: var(--studio-text); --bs-table-hover-bg: var(--surface-soft); --bs-table-hover-color: var(--studio-text); margin: 0; }
    .platform-table th { background: var(--surface-soft); color: var(--studio-muted); font-size: .7rem; white-space: nowrap; }
    .platform-table td, .platform-table th { border-color: var(--studio-border); vertical-align: middle; }
    @media (max-width: 767.98px) { .platform-welcome { align-items: stretch; flex-direction: column; } }
    @media (max-width: 575.98px) { .platform-welcome { padding: 18px; } .platform-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 7px; padding: 9px; } .platform-kpis strong { font-size: .82rem; } .platform-kpis span { font-size: .58rem; } .platform-panel-title { padding: 15px; } }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1480px">
    <header class="mb-4">
        <div class="home-kicker">Platform administration</div>
        <h1 class="h2 fw-bold mb-1">Marketplace overview</h1>
        <p class="text-muted mb-0">Manage the platform, independent artist accounts, catalog approvals, and marketplace performance.</p>
    </header>

    <section class="platform-welcome mb-3">
        <div><div class="home-kicker">Welcome, {{ $adminName }}</div><h2 class="h5 fw-bold mt-2 mb-1">The whole marketplace at a glance</h2><p class="mb-0">Review platform health and keep the catalog moving.</p></div>
        <div class="platform-kpis" aria-label="Platform highlights">
            <div><span>Catalog artworks</span><strong>{{ $artworkCount }}</strong></div>
            <div><span>Artist accounts</span><strong>{{ $artistCount }}</strong></div>
            <div><span>Awaiting approval</span><strong>{{ $pendingCount }}</strong></div>
        </div>
    </section>

    <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4" aria-label="Platform metrics">
        <div class="col"><article class="platform-metric h-100"><span class="metric-label">Platform revenue</span><strong>₱{{ number_format($platformRevenue, 2) }}</strong><small class="text-muted">Mock artist totals</small></article></div>
        <div class="col"><article class="platform-metric h-100"><span class="metric-label">Registered accounts</span><strong>{{ count($users) }}</strong><small class="text-muted">Clients, artists, and admin</small></article></div>
        <div class="col"><article class="platform-metric h-100"><span class="metric-label">Artwork submissions</span><strong>{{ $artworkCount }}</strong><small class="text-muted">Catalog and creator uploads</small></article></div>
        <div class="col"><article class="platform-metric h-100"><span class="metric-label">Demo orders in session</span><strong>{{ count($transactions) }}</strong><small class="text-muted">Checkout preview only</small></article></div>
    </section>

    <section class="platform-transactions mb-4">
        <div class="platform-panel-title d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h2 class="h6 fw-bold mb-1">Artwork review queue</h2><p class="platform-caption small mb-0">Approve new creator submissions before publication.</p></div>
            <a href="{{ route('admin.approvals') }}" class="btn btn-likha btn-sm rounded-pill">Review submissions <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </section>

    <section class="platform-transactions">
        <div class="platform-panel-title d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h2 class="h6 fw-bold mb-1">Marketplace accounts</h2><p class="platform-caption small mb-0">A single platform administrator oversees independent artist and client accounts.</p></div>
            <a href="{{ route('admin.users') }}" class="btn btn-outline-likha btn-sm rounded-pill">Manage users <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover platform-table">
                <thead><tr><th class="ps-3 ps-md-4">Account</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach (array_slice($users, 0, 5) as $user)
                        <tr><td class="ps-3 ps-md-4 fw-semibold">{{ $user['name'] }}</td><td>{{ $user['email'] }}</td><td>{{ ucfirst($user['role']) }}</td><td>{{ $user['status'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
