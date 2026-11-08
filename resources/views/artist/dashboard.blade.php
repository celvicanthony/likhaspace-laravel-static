@extends('layouts.app')

@section('title', 'Artist Studio | LikhaSpace')

@push('styles')
<style>
    .artist-panel, .artist-metric { border: 1px solid var(--studio-border); border-radius: 20px; background: var(--studio-card); box-shadow: var(--shadow); }
    .artist-panel { overflow: hidden; }
    .artist-metric { padding: 18px; }
    .artist-metric span, .artist-subtitle { color: var(--studio-muted); font-size: .8rem; }
    .artist-metric strong { display: block; margin-top: 6px; font-size: 1.3rem; }
    .artist-panel-header { padding: 18px 20px; }
    .artist-table { --bs-table-bg: transparent; --bs-table-color: var(--studio-text); --bs-table-hover-bg: var(--surface-soft); --bs-table-hover-color: var(--studio-text); margin: 0; }
    .artist-table th { background: var(--surface-soft); color: var(--studio-muted); font-size: .7rem; white-space: nowrap; }
    .artist-table td, .artist-table th { border-color: var(--studio-border); vertical-align: middle; }
    .artist-status { padding: 5px 9px; border: 1px solid var(--studio-border); border-radius: 999px; background: var(--surface-strong); color: var(--studio-link); font-size: .72rem; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1280px">
    <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><div class="home-kicker">Creator workspace</div><h1 class="h2 fw-bold mb-1">Welcome, {{ $artist['name'] }}</h1><p class="text-muted mb-0">{{ $artist['country'] }} · {{ $artist['studio'] }}</p></div>
        <a href="{{ route('artist.inventory') }}" class="btn btn-likha rounded-pill"><i class="bi bi-plus-lg me-1"></i>Submit artwork</a>
    </header>

    <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4" aria-label="Studio performance">
        <div class="col"><article class="artist-metric h-100"><span>Artwork listings</span><strong>{{ $artworks->count() + $submissions->count() }}</strong></article></div>
        <div class="col"><article class="artist-metric h-100"><span>Sales</span><strong>{{ $artist['sales'] }}</strong></article></div>
        <div class="col"><article class="artist-metric h-100"><span>Artist earnings (90%)</span><strong>₱{{ number_format($artist['revenue'], 2) }}</strong><small class="artist-subtitle">Paid orders, after platform share</small></article></div>
        <div class="col"><article class="artist-metric h-100"><span>In review</span><strong>{{ $artworks->where('status', 'pending')->count() + $submissions->where('status', 'pending')->count() }}</strong></article></div>
    </section>
    <p class="text-muted small">Platform share (10%) tracked from your paid orders: ₱{{ number_format($artist['commission'], 2) }}. Payout settlement is handled separately.</p>

    <section class="artist-panel mb-4">
        <div class="artist-panel-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h2 class="h6 fw-bold mb-1">Recent orders</h2><p class="artist-subtitle mb-0">Review payments and deliver digital files from your orders workspace.</p></div>
            <a href="{{ route('artist.orders') }}" class="small text-decoration-none">Manage all orders <i class="bi bi-arrow-right"></i></a>
        </div>
        @forelse ($artistOrders as $order)
            <div class="border-top px-3 px-md-4 py-3 d-flex flex-wrap justify-content-between gap-2">
                <div><strong>{{ $order->order_code }}</strong><div class="artist-subtitle">{{ $order->buyer_name }} · {{ $order->items->count() }} artwork(s)</div></div>
                <div class="text-end"><span class="artist-status">{{ $order->status }}</span><div class="artist-subtitle mt-1">₱{{ number_format($order->total_amount, 2) }}</div></div>
            </div>
        @empty
            <div class="border-top px-3 px-md-4 py-3 artist-subtitle">No orders yet.</div>
        @endforelse
    </section>

    <section class="artist-panel">
        <div class="artist-panel-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h2 class="h6 fw-bold mb-1">Your studio catalog</h2><p class="artist-subtitle mb-0">Only your own artwork and submissions appear in this workspace.</p></div>
            <a href="{{ route('artist.inventory') }}" class="small text-decoration-none">Manage inventory <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover artist-table">
                <thead><tr><th class="ps-3 ps-md-4">Artwork</th><th>Country</th><th>Price</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($artworks as $artwork)
                        <tr><td class="ps-3 ps-md-4 fw-semibold">{{ $artwork['title'] }}</td><td>{{ $artwork['country'] }}</td><td>₱{{ number_format($artwork['price'], 2) }}</td><td><span class="artist-status">{{ ucfirst($artwork['status']) }}</span></td></tr>
                    @endforeach
                    @foreach ($submissions as $submission)
                        <tr><td class="ps-3 ps-md-4 fw-semibold">{{ $submission['title'] }}</td><td>{{ $submission['country'] }}</td><td>₱{{ number_format($submission['price'], 2) }}</td><td><span class="artist-status">{{ ucfirst($submission['status']) }}</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
