@extends('layouts.app')

@section('title', 'Secure Checkout | LikhaSpace')

@section('content')
@php($total = array_sum(array_map(fn ($item) => $item['quantity'] * $item['price'], $items)))
<div class="mx-auto" style="max-width: 1000px">
    <div class="mb-4">
        <div class="home-kicker">Almost yours</div>
        <h1 class="h2 fw-bold mb-1">Secure checkout</h1>
        <p class="text-muted mb-0">Complete your details to place this demo order.</p>
    </div>
    @if (session('status'))
        <div class="alert alert-success rounded-4" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">Please review the highlighted checkout details and try again.</div>
    @endif
    <form method="POST" action="{{ route('checkout.submit') }}">
        @csrf
        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <section class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4 mb-4">
                    <h2 class="h5 fw-bold mb-3">Delivery details</h2>
                    <div class="row g-3">
                        <div class="col-12 col-md-6"><label class="form-label" for="checkout-name">Full name</label><input id="checkout-name" class="form-control rounded-3 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12 col-md-6"><label class="form-label" for="checkout-email">Email address</label><input id="checkout-email" class="form-control rounded-3 @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12"><label class="form-label" for="checkout-address">Shipping address</label><textarea id="checkout-address" class="form-control rounded-3 @error('address') is-invalid @enderror" name="address" rows="3" autocomplete="street-address" required>{{ old('address') }}</textarea>@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div>
                </section>
                <section class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4">
                    <h2 class="h5 fw-bold mb-3">Payment method</h2>
                    <div class="form-check border rounded-3 p-3 ps-5 mb-2"><input class="form-check-input" type="radio" name="payment" id="payment-card" value="card" checked><label class="form-check-label w-100" for="payment-card"><i class="bi bi-credit-card me-2"></i>Credit or debit card</label></div>
                    <div class="form-check border rounded-3 p-3 ps-5"><input class="form-check-input" type="radio" name="payment" id="payment-gcash" value="gcash"><label class="form-check-label w-100" for="payment-gcash"><i class="bi bi-phone me-2"></i>GCash</label></div>
                    @error('payment')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </section>
            </div>
            <aside class="col-12 col-lg-5">
                <section class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4">
                    <h2 class="h5 fw-bold mb-3">Order summary</h2>
                    @foreach ($items as $item)
                        <div class="d-flex justify-content-between gap-3 mb-3"><span class="small">{{ $item['title'] }} <span class="text-muted">× {{ $item['quantity'] }}</span></span><span class="text-nowrap">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</span></div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3"><span>Total</span><span>₱{{ number_format($total, 2) }}</span></div>
                    <p class="text-muted small">Demo checkout only. No payment will be collected.</p>
                    <button class="btn btn-likha rounded-4 w-100 py-3" type="submit"><i class="bi bi-lock me-2"></i>Place demo order</button>
                    <a href="{{ route('cart') }}" class="d-block text-center small mt-3">Return to cart</a>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection
