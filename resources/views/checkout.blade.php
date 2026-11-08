
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
                            <label class="form-label" for="checkout-name">Name for your order</label>
                            <input
                                id="checkout-name"
                                class="form-control rounded-3 @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name', $buyer->display_name ?? '') }}"
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
                    <h2 class="h5 fw-bold mb-2">Pay each artist directly</h2>
                    <p class="text-muted small mb-3">Your cart is split into separate orders by artist. Each artist's private GCash or InstaPay details and QR code will appear only on that artist's order page.</p>
                    @foreach ($artists as $artist)
                        <div class="d-flex align-items-center justify-content-between gap-2 border-top py-2">
                            <span class="small">{{ $artist['name'] }}</span>
                            @if ($artist['ready'])
                                <span class="badge text-bg-success">Payment ready</span>
                            @else
                                <span class="badge text-bg-warning">Payment setup unavailable</span>
                            @endif
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
