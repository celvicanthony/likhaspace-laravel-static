
@extends('layouts.app')

@section('title', 'Transactions | LikhaSpace')

@push('styles')
<style>
    .admin-orders { max-width: 1400px; margin: 0 auto; }
    .admin-orders-panel {
        overflow: hidden;
        border: 1px solid var(--studio-border);
        border-radius: 20px;
        background: var(--studio-card);
        box-shadow: var(--shadow);
    }
    .admin-orders-table {
        --bs-table-bg: transparent;
        --bs-table-color: var(--studio-text);
        --bs-table-hover-bg: var(--surface-soft);
        --bs-table-hover-color: var(--studio-text);
        margin: 0;
    }
    .admin-orders-table th {
        background: var(--surface-soft);
        color: var(--studio-muted);
        font-size: .72rem;
        white-space: nowrap;
    }
    .admin-orders-table td,
    .admin-orders-table th {
        border-color: var(--studio-border);
        vertical-align: middle;
    }
    .order-status-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        background: var(--surface-strong);
        color: var(--studio-link);
        font-size: .72rem;
        font-weight: 700;
    }
    .order-code-text {
        color: var(--studio-link);
        font-weight: 700;
        overflow-wrap: anywhere;
    }
    .order-item-list {
        min-width: 180px;
        font-size: .8rem;
    }
    .order-item-list div + div { margin-top: 4px; }
</style>
@endpush

@section('content')
<div class="admin-orders">
    <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="home-kicker">Platform administration</div>
            <h1 class="h2 fw-bold mb-1">Transactions</h1>
            <p class="text-muted mb-0">
                View customer orders and their purchased artworks.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-likha rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </header>

    <section class="admin-orders-panel">
        <div class="p-3 p-md-4 border-bottom" style="border-color: var(--studio-border) !important">
            <h2 class="h6 fw-bold mb-1">All customer orders</h2>
            <p class="text-muted small mb-0">
                {{ $orders->total() }} recorded order{{ $orders->total() === 1 ? '' : 's' }} in the database.
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover admin-orders-table">
                <thead>
                    <tr>
                        <th class="ps-3 ps-md-4">Order number</th>
                        <th>Customer</th>
                        <th>Purchased artworks</th>
                        <th>Payment</th>
                        <th>Order status</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="ps-3 ps-md-4">
                                <span class="order-code-text">
                                    {{ $order->order_code }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $order->buyer_name }}</div>
                                <div class="text-muted small">{{ $order->buyer_email }}</div>
                            </td>

                            <td>
                                <div class="order-item-list">
                                    @foreach ($order->items as $item)
                                        <div>
                                            {{ $item->artwork_title }}
                                            <span class="text-muted">× {{ $item->quantity }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <td>
                                <div>{{ $order->payment_method }}</div>
                                <small class="text-muted">
                                    {{ ucfirst($order->payment_status) }}
                                </small>
                            </td>

                            <td>
                                <span class="order-status-badge">
                                    {{ $order->status }}
                                </span>
                            </td>

                            <td class="text-nowrap fw-bold">
                                ₱{{ number_format($order->total_amount, 2) }}
                            </td>

                            <td class="text-nowrap">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-receipt display-5 text-primary"></i>
                                <h3 class="h6 fw-bold mt-3">No transactions yet</h3>
                                <p class="text-muted small mb-0">
                                    Customer orders will appear here after checkout.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $orders->links() }}
        </div>
    </section>
</div>
@endsection
