
@extends('layouts.app')

@section('title', 'Track Order | LikhaSpace')

@php
    $steps = ['Order placed', 'Payment review', 'Preparing artwork', 'Ready for download'];

    $currentStep = match ($order->status) {
        'Payment submitted' => 2,
        'Processing' => 3,
        'Ready for download' => 4,
        default => 1,
    };
@endphp

@push('styles')
<style>
    .transaction-page { max-width: 1000px; margin: 0 auto; }
    .transaction-heading { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .transaction-code { color: var(--studio-link); font-size: .82rem; font-weight: 700; overflow-wrap: anywhere; }
    .transaction-panel { border: 1px solid var(--studio-border); border-radius: 20px; background: var(--studio-card); box-shadow: var(--shadow); }
    .transaction-panel h2 { font-size: 1.05rem; font-weight: 700; }
    .transaction-timeline { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); margin: 24px 0 12px; }
    .transaction-step { position: relative; min-width: 0; color: var(--muted); text-align: center; }
    .transaction-step:not(:last-child)::after { position: absolute; top: 16px; left: calc(50% + 17px); width: calc(100% - 34px); height: 2px; background: var(--studio-border); content: ""; }
    .transaction-step.is-complete:not(:last-child)::after { background: var(--studio-primary); }
    .step-marker { position: relative; z-index: 1; display: grid; width: 34px; height: 34px; margin: 0 auto 9px; place-items: center; border: 1px solid var(--studio-border); border-radius: 50%; background: var(--surface); color: var(--muted); }
    .transaction-step.is-complete .step-marker { border-color: var(--studio-primary); background: var(--studio-primary); color: #fff; }
    .transaction-step.is-current { color: var(--studio-link); font-weight: 700; }
    .step-label { display: block; font-size: .75rem; }
    .transaction-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 0; border-top: 1px solid var(--studio-border); }
    .transaction-item:first-of-type { margin-top: 12px; }
    .transaction-item-title { color: var(--ink); font-size: .86rem; font-weight: 700; }
    .transaction-item-meta { margin-top: 3px; color: var(--muted); font-size: .76rem; }
    .transaction-total { color: var(--ink); font-weight: 700; white-space: nowrap; }
    .transaction-detail { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--studio-border); font-size: .84rem; }
    .transaction-detail:last-child { border-bottom: 0; }
    .transaction-detail span:first-child { color: var(--muted); }
    .transaction-detail span:last-child { color: var(--ink); font-weight: 600; text-align: right; overflow-wrap: anywhere; }
    @media (max-width: 575.98px) {
        .transaction-panel { border-radius: 16px; }
        .transaction-timeline { margin-right: -6px; margin-left: -6px; }
        .step-label { font-size: .66rem; }
        .transaction-step:not(:last-child)::after { top: 15px; left: calc(50% + 15px); width: calc(100% - 30px); }
        .step-marker { width: 32px; height: 32px; }
    }
</style>
@endpush

@section('content')
<div class="transaction-page">
    <div class="transaction-heading mb-4">
        <div>
            <div class="home-kicker">Order tracking</div>
            <h1 class="h2 fw-bold mb-1">Order status</h1>
            <div class="transaction-code">{{ $order->order_code }}</div>
        </div>

        <a href="{{ route('transactions') }}" class="btn btn-outline-likha rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>All orders
        </a>
    </div>

    @if (session('status'))
        <div class="alert bg-studio-card rounded-4" role="status">
            <i class="bi bi-check-circle me-2 text-primary"></i>
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="alert bg-studio-card rounded-4 small" role="note">
        <i class="bi bi-shield-lock me-2 text-primary"></i>
        Pay the artist directly. They will check their account and confirm receipt before uploading your private digital files.
    </div>

    <section class="transaction-panel p-3 p-md-4 mb-4" aria-labelledby="progress-heading">
        <h2 id="progress-heading">Fulfillment progress</h2>

        <div class="transaction-timeline">
            @foreach ($steps as $index => $step)
                @php($stepNumber = $index + 1)

                <div
                    class="transaction-step {{ $stepNumber <= $currentStep ? 'is-complete' : '' }} {{ $stepNumber === $currentStep ? 'is-current' : '' }}"
                    @if ($stepNumber === $currentStep) aria-current="step" @endif
                >
                    <span class="step-marker">
                        <i class="bi {{ $stepNumber < $currentStep ? 'bi-check-lg' : ($stepNumber === $currentStep ? 'bi-record-circle' : 'bi-circle') }}"></i>
                    </span>
                    <span class="step-label">{{ $step }}</span>
                </div>
            @endforeach
        </div>

        <p class="text-muted small mb-0 mt-3">
            Placed {{ $order->created_at->format('M j, Y \a\t g:i A') }}
        </p>
    </section>

    @if (in_array($order->payment_status, ['unpaid', 'rejected'], true) && $order->seller)
        <section class="transaction-panel p-3 p-md-4 mb-4" aria-labelledby="payment-heading">
            <h2 id="payment-heading">Pay {{ $order->seller->name }}</h2>
            <p class="text-muted small">Send exactly ₱{{ number_format($order->total_amount, 2) }} using {{ ucfirst($order->payment_method) }}. Payment details are private to this order and are not shown on public artist pages.</p>
            <div class="transaction-detail"><span>Recipient</span><span>{{ $order->payment_recipient_name }}</span></div>
            <div class="transaction-detail"><span>{{ strtoupper($order->payment_method) }} account</span><span>{{ $order->payment_recipient_identifier }}</span></div>
            @if ($order->payment_qr_path)
                <div class="mt-3">
                    <img src="{{ route('transactions.payment.qr', $order->order_code) }}" alt="Private payment QR code for this order" class="img-fluid rounded-3 border" style="width: 220px; max-height: 220px; object-fit: contain">
                </div>
            @endif
            @if ($order->payment_status === 'rejected')
                <div class="alert alert-warning small mt-3 mb-0">The artist requested a new proof: {{ $order->payment_review_note }}</div>
            @endif
            <form method="POST" action="{{ route('transactions.payment.submit', $order->order_code) }}" enctype="multipart/form-data" class="mt-3">
                @csrf
                <label class="form-label" for="payment-reference">Payment reference (optional)</label>
                <input id="payment-reference" name="payment_reference" class="form-control mb-3" maxlength="120" value="{{ old('payment_reference') }}">
                <label class="form-label" for="payment-proof">Payment receipt/screenshot</label>
                <input id="payment-proof" name="payment_proof" type="file" class="form-control" accept="image/png,image/jpeg,image/webp" required>
                <div class="form-text text-muted">PNG, JPG, or WebP up to 5 MB. This proof is private to you and the artist on this order.</div>
                <button class="btn btn-likha rounded-pill px-4 mt-3" type="submit">Send proof for artist review</button>
            </form>
        </section>
    @elseif ($order->payment_status === 'submitted')
        <div class="alert alert-info rounded-4" role="status">Payment proof sent. Wait for {{ $order->seller?->name ?? 'the artist' }} to verify the funds before artwork delivery.</div>
    @elseif ($order->payment_status === 'paid' && $order->status !== 'Ready for download')
        <div class="alert alert-success rounded-4" role="status">The artist confirmed payment. They are preparing your digital artwork.</div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <section class="transaction-panel p-3 p-md-4 h-100" aria-labelledby="items-heading">
                <h2 id="items-heading">Items in this order</h2>

                @foreach ($order->items as $item)
                    <div class="transaction-item">
                        <div class="min-w-0">
                            <div class="transaction-item-title">
                                {{ $item->artwork_title }}
                            </div>

                            <div class="transaction-item-meta">
                                {{ $item->artist_name }} · Qty {{ $item->quantity }}
                            </div>

                            <div class="transaction-item-meta">
                                ₱{{ number_format($item->unit_price, 2) }} each
                            </div>
                        </div>

                        <span class="transaction-total">
                            ₱{{ number_format($item->subtotal, 2) }}
                        </span>
                    </div>
                @endforeach

                <div class="d-flex justify-content-between gap-3 fw-bold mt-3">
                    <span>Total</span>
                    <span class="text-nowrap">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </span>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-5">
            <section class="transaction-panel p-3 p-md-4 h-100" aria-labelledby="details-heading">
                <h2 id="details-heading">Transaction details</h2>

                <div class="transaction-detail">
                    <span>Order number</span>
                    <span>{{ $order->order_code }}</span>
                </div>

                <div class="transaction-detail">
                    <span>Buyer</span>
                    <span>{{ $order->buyer_name }}</span>
                </div>

                <div class="transaction-detail">
                    <span>Email</span>
                    <span>{{ $order->buyer_email }}</span>
                </div>

                <div class="transaction-detail">
                    <span>Payment method</span>
                    <span>{{ $order->payment_method }}</span>
                </div>

                <div class="transaction-detail">
                    <span>Payment status</span>
                    <span>{{ ucfirst($order->payment_status) }}</span>
                </div>

                <div class="transaction-detail">
                    <span>Order status</span>
                    <span>{{ $order->status }}</span>
                </div>

                <div class="transaction-detail">
                    <span>Delivery</span>
                    <span>Private digital download</span>
                </div>
            </section>
        </div>
    </div>

    @if ($order->status === 'Ready for download')
        <div class="alert bg-studio-card rounded-4 mt-3 mb-0" role="status">
            <i class="bi bi-download me-2 text-primary"></i>
            Your payment is confirmed and the artist has delivered your files. Download them below.
        </div>
        <section class="transaction-panel p-3 p-md-4 mt-3" aria-label="Digital artwork downloads">
            @foreach ($order->items as $item)
                <div class="transaction-item">
                    <div class="transaction-item-title">{{ $item->artwork_title }}</div>
                    <div class="d-grid justify-items-end gap-2">
                        <a class="btn btn-likha btn-sm rounded-pill" href="{{ route('transactions.items.download', [$order->order_code, $item->id]) }}">
                            <i class="bi bi-download me-1"></i>Download
                        </a>
                        @if ($item->review)
                            <span class="small text-warning" aria-label="You rated this artwork {{ $item->review->rating }} out of 5">
                                @for ($star = 1; $star <= 5; $star++)
                                    <i class="bi {{ $star <= $item->review->rating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                @endfor
                                <span class="text-muted">Your review</span>
                            </span>
                        @elseif ($item->downloaded_at)
                            <form method="POST" action="{{ route('transactions.items.review', [$order->order_code, $item->id]) }}" class="d-flex flex-wrap align-items-end gap-2">
                                @csrf
                                <div>
                                    <label class="form-label small mb-1" for="rating-{{ $item->id }}">Rate (1–5 stars)</label>
                                    <select class="form-select form-select-sm" id="rating-{{ $item->id }}" name="rating" required>
                                        <option value="">Choose rating</option>
                                        @for ($rating = 5; $rating >= 1; $rating--)
                                            <option value="{{ $rating }}">{{ $rating }} star{{ $rating === 1 ? '' : 's' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label small mb-1" for="comment-{{ $item->id }}">Review (optional)</label>
                                    <input class="form-control form-control-sm" id="comment-{{ $item->id }}" name="comment" maxlength="1000">
                                </div>
                                <button class="btn btn-outline-likha btn-sm rounded-pill" type="submit">Submit review</button>
                            </form>
                        @else
                            <span class="small text-muted">Download the artwork to leave a review.</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
    @endif
</div>
@endsection
