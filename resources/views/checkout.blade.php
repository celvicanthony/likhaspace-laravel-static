
@extends('layouts.app')

@section('title', 'Secure Checkout | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 1000px">
    <div class="mb-4">
        <div class="home-kicker">Almost yours</div>
        <h1 class="h2 fw-bold mb-1">Secure checkout</h1>
        <p class="text-muted mb-0">Review your order and contact details. Payments go directly to each artist.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success rounded-4" role="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.submit') }}">
        @csrf

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <section class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4 mb-4">
                    <h2 class="h5 fw-bold mb-3">Contact details</h2>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="checkout-name">Full name</label>
                            <input
                                id="checkout-name"
                                class="form-control rounded-3 @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name', $buyer->name ?? '') }}"
                                autocomplete="name"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="checkout-email">Email address</label>
                            <input
                                id="checkout-email"
                                class="form-control rounded-3 @error('email') is-invalid @enderror"
                                name="email"
                                type="email"
                                value="{{ old('email', $buyer->email ?? '') }}"
                                autocomplete="email"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </section>

                <section class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4">
                    <h2 class="h5 fw-bold mb-2">Artist payment details</h2>
                    <p class="text-muted small mb-3">Your cart is split into separate orders by artist. Review each recipient and amount below. After placing your order, submit your payment proof on the order page.</p>
                    @foreach ($artists as $artist)
                        <div class="border-top py-3">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <h3 class="h6 fw-bold mb-1">{{ $artist['name'] }}</h3>
                                    @if ($artist['ready'])
                                        <div class="small">{{ $artist['payment_method'] }}</div>
                                        <div class="small">Recipient: {{ $artist['recipient_name'] }}</div>
                                        <div class="small">Account: <span class="user-select-all">{{ $artist['recipient_identifier'] }}</span></div>
                                        <div class="small fw-semibold mt-1">Amount for this artist: ₱{{ number_format($artist['subtotal'], 2) }}</div>
                                    @else
                                        <span class="badge text-bg-warning">Payment setup unavailable</span>
                                    @endif
                                </div>
                                @if ($artist['qr_url'])
                                    <img
                                        src="{{ $artist['qr_url'] }}"
                                        alt="Payment QR code for {{ $artist['name'] }}"
                                        class="rounded-3 border p-1 bg-white"
                                        style="width: 144px; height: 144px; object-fit: contain"
                                        loading="eager"
                                        decoding="sync"
                                    >
                                @else
                                    <div class="small text-muted">No QR image saved. Use the account details to send payment.</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </section>
            </div>

            <aside class="col-12 col-lg-5">
                <section class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4">
                    <h2 class="h5 fw-bold mb-3">Order summary</h2>

                    @foreach ($items as $item)
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <div class="small">
                                <div class="fw-semibold">{{ $item['title'] }}</div>
                                <span class="text-muted">
                                    By {{ $item['artist'] }} · Qty {{ $item['quantity'] }}
                                </span>
                            </div>

                            <span class="text-nowrap">
                                ₱{{ number_format($item['subtotal'], 2) }}
                            </span>
                        </div>
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>

                    <p class="text-muted small">Payment is verified manually by each artist. Do not send a PIN or password, and submit only a payment receipt/screenshot as proof.</p>

                    @unless ($paymentReady)
                        <div class="alert alert-warning small" role="alert">An artist needs to finish their payment setup before this cart can be placed.</div>
                    @endunless
                    <button class="btn btn-likha rounded-4 w-100 py-3" type="submit" @disabled(! $paymentReady)>
                        <i class="bi bi-lock me-2"></i>
                        Create artist orders
                    </button>

                    <a href="{{ route('cart') }}" class="d-block text-center small mt-3">
                        Return to cart
                    </a>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection
