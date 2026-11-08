@extends('layouts.app')

@section('title', 'Orders and payments | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 1180px">
    <header class="mb-4">
        <div class="home-kicker">Artist workspace</div>
        <h1 class="h2 fw-bold mb-1">Orders &amp; payments</h1>
        <p class="text-muted mb-0">Review payment proof, confirm funds received, then securely deliver each digital artwork.</p>
    </header>

    @if (session('status'))
        <div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="alert bg-studio-card rounded-4 small">
        <i class="bi bi-shield-lock me-2 text-primary"></i>
        Only mark a payment received after checking your GCash/InstaPay account. A screenshot is not proof that funds arrived. Delivery files are private and available only to the buyer after payment approval.
    </div>

    <section class="d-grid gap-3" aria-label="Artist orders">
        @forelse ($orders as $order)
            <article class="bg-studio-card rounded-4 p-3 p-md-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Order {{ $order->order_code }}</h2>
                        <div class="text-muted small">Buyer: {{ $order->buyer_name }} · {{ $order->buyer_email }} · {{ $order->created_at->format('M j, Y g:i A') }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge rounded-pill text-bg-secondary">{{ $order->status }}</span>
                        <div class="fw-bold mt-2">₱{{ number_format($order->total_amount, 2) }}</div>
                    </div>
                </div>

                @foreach ($order->items as $item)
                    <div class="border-top py-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <strong>{{ $item->artwork_title }}</strong>
                            <span class="text-muted small">Qty {{ $item->quantity }} · ₱{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                        @if ($order->payment_status === 'paid' && $order->status === 'Processing')
                            <form method="POST" action="{{ route('artist.orders.items.delivery', [$order->order_code, $item->id]) }}" enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <label class="form-label small" for="delivery-{{ $item->id }}">Upload digital artwork for buyer</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <input id="delivery-{{ $item->id }}" class="form-control" style="max-width: 440px" type="file" name="delivery_file" accept=".zip,.pdf,.jpg,.jpeg,.png,.webp" required>
                                    <button class="btn btn-likha btn-sm rounded-pill px-3" type="submit">{{ $item->delivery_path ? 'Replace delivery' : 'Upload delivery' }}</button>
                                </div>
                                <div class="form-text text-muted">ZIP, PDF, JPG, or PNG, up to 50 MB. Files stay private until the order is ready.</div>
                            </form>
                        @elseif ($item->delivery_path)
                            <span class="text-success small"><i class="bi bi-check-circle me-1"></i>Delivery uploaded</span>
                        @endif
                    </div>
                @endforeach

                <div class="border-top pt-3">
                    <div class="small mb-2"><strong>Payment:</strong> {{ ucfirst($order->payment_status) }} · {{ $order->payment_method }}</div>
                    @if ($order->payment_status === 'paid')
                        <div class="text-muted small mb-2">Platform share (10%): ₱{{ number_format($order->platform_commission, 2) }} · Your earnings (90%): ₱{{ number_format($order->artist_earnings, 2) }}. These are tracked allocations, not automatic payouts.</div>
                    @endif
                    @if ($order->payment_status === 'submitted')
                        <a class="btn btn-outline-likha btn-sm rounded-pill mb-3" href="{{ route('artist.orders.payment-proof', $order->order_code) }}" target="_blank" rel="noopener">View buyer's payment proof</a>
                        @if ($order->payment_reference)
                            <div class="text-muted small mb-3">Reference: {{ $order->payment_reference }}</div>
                        @endif
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('artist.orders.payment-review', $order->order_code) }}">
                                @csrf
                                <input type="hidden" name="decision" value="approve">
                                <button class="btn btn-success btn-sm rounded-pill px-3" type="submit">Confirm payment received</button>
                            </form>
                            <form method="POST" action="{{ route('artist.orders.payment-review', $order->order_code) }}" class="d-flex flex-wrap gap-2">
                                @csrf
                                <input type="hidden" name="decision" value="reject">
                                <input class="form-control form-control-sm" name="payment_review_note" placeholder="Reason to request new proof" maxlength="500" required>
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3" type="submit">Reject proof</button>
                            </form>
                        </div>
                    @elseif ($order->payment_status === 'rejected' && $order->payment_review_note)
                        <div class="text-warning small">Buyer was asked to correct proof: {{ $order->payment_review_note }}</div>
                    @elseif ($order->payment_status === 'paid')
                        <div class="text-success small">Payment confirmed {{ $order->payment_reviewed_at?->format('M j, Y g:i A') }}. Upload every item above to release the order.</div>
                    @else
                        <div class="text-muted small">Waiting for the buyer to submit payment proof.</div>
                    @endif
                </div>
            </article>
        @empty
            <div class="bg-studio-card rounded-4 p-4 text-center">
                <h2 class="h5 fw-bold">No orders yet</h2>
                <p class="text-muted mb-0">Orders for your artworks will appear here.</p>
            </div>
        @endforelse
    </section>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
