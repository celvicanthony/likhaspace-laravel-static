@extends('layouts.app')

@section('title', 'My Orders | LikhaSpace')

@push('styles')
<style>
    .orders-page { max-width: 1000px; margin: 0 auto; }
    .order-card { display: flex; min-width: 0; align-items: center; gap: 16px; padding: 18px; border: 1px solid var(--studio-border); border-radius: 18px; background: var(--studio-card); box-shadow: var(--shadow); }
    .order-icon { display: grid; width: 52px; height: 52px; flex: 0 0 52px; place-items: center; border-radius: 15px; background: var(--surface-strong); color: var(--studio-link); font-size: 1.3rem; }
    .order-info { min-width: 0; flex: 1; }
    .order-code { margin: 0; color: var(--ink); font-size: .95rem; font-weight: 700; overflow-wrap: anywhere; }
    .order-subtitle { margin: 4px 0 0; color: var(--muted); font-size: .78rem; }
    .order-side { display: flex; flex: 0 0 auto; align-items: flex-end; flex-direction: column; gap: 9px; }
    .order-status { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border: 1px solid var(--studio-border); border-radius: 999px; background: var(--surface-strong); color: var(--studio-link); font-size: .72rem; font-weight: 700; }
    .order-total { color: var(--ink); font-size: .9rem; font-weight: 700; }
    .order-view { color: var(--studio-link); font-size: .8rem; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .order-view:hover { color: var(--studio-primary); }
    @media (max-width: 575.98px) { .order-card { align-items: flex-start; gap: 11px; padding: 14px; } .order-icon { width: 42px; height: 42px; flex-basis: 42px; } .order-side { align-items: flex-end; } .order-total { font-size: .82rem; } .order-status { max-width: 120px; white-space: normal; text-align: center; } }
</style>
@endpush

@section('content')
<div class="orders-page">
    <header class="mb-4">
        <div class="home-kicker">Your purchases</div>
        <h1 class="h2 fw-bold mb-1">My orders</h1>
        <p class="text-muted mb-0">View your order history and follow each artwork through its fulfillment steps.</p>
    </header>

    <div class="alert bg-studio-card rounded-4 small mb-4" role="note">
        <i class="bi bi-info-circle me-2 text-primary"></i>These are prototype transactions. Checkout does not charge a payment method.
    </div>

    <section class="d-grid gap-3" aria-label="Order history">
        @forelse ($transactions as $transaction)
            @php($firstItem = $transaction['items'][0])
            <article class="order-card">
                <span class="order-icon" aria-hidden="true"><i class="bi bi-bag-check"></i></span>
                <div class="order-info">
                    <h2 class="order-code">Order {{ $transaction['order_code'] }}</h2>
                    <p class="order-subtitle">{{ count($transaction['items']) }} artwork{{ count($transaction['items']) === 1 ? '' : 's' }} · {{ $firstItem['title'] }} · {{ \Illuminate\Support\Carbon::parse($transaction['placed_at'])->format('M j, Y') }}</p>
                </div>
                <div class="order-side">
                    <span class="order-status"><i class="bi bi-record-circle"></i>{{ $transaction['status'] }}</span>
                    <span class="order-total">₱{{ number_format($transaction['total'], 2) }}</span>
                    <a class="order-view" href="{{ route('transactions.show', $transaction['order_code']) }}">Track order <i class="bi bi-arrow-right"></i></a>
                </div>
            </article>
        @empty
            <div class="bg-studio-card rounded-4 p-4 p-md-5 text-center">
                <i class="bi bi-receipt-cutoff display-5 text-primary"></i>
                <h2 class="h5 fw-bold mt-3">No orders yet</h2>
                <p class="text-muted">When you place a demo order, its status and transaction details will appear here.</p>
                <a href="{{ route('explore') }}" class="btn btn-likha rounded-4">Explore artwork</a>
            </div>
        @endforelse
    </section>
</div>
@endsection
