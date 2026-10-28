@extends('layouts.app')

@section('title', 'Your Cart | LikhaSpace')

@section('content')
@php($total = array_sum(array_column($items, 'price')))
<div class="mx-auto" style="max-width: 1000px">
    <div class="mb-4">
        <div class="home-kicker">Your collection</div>
        <h1 class="h2 fw-bold mb-1">My cart</h1>
        <p class="text-muted mb-0">Myth-inspired artwork, ready for your space.</p>
    </div>
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            @foreach ($items as $item)
                <article class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4 mb-3 d-flex align-items-center gap-3">
                    <span class="d-grid flex-shrink-0 rounded-4" style="width: 76px; height: 76px; place-items: center; background: var(--surface-strong); color: var(--studio-link)"><i class="bi bi-image fs-2"></i></span>
                    <div class="flex-grow-1 min-w-0">
                        <div class="small text-muted">{{ $item['country'] }} mythology</div>
                        <h2 class="h6 fw-bold mb-1">{{ $item['title'] }}</h2>
                        <p class="small text-muted mb-0">By {{ $item['artist'] }}</p>
                    </div>
                    <div class="text-end fw-bold text-nowrap">₱{{ number_format($item['price'], 2) }}<a href="{{ route('product.show', $item['artwork_id']) }}" class="d-block small fw-normal">View artwork</a></div>
                </article>
            @endforeach
            <a href="{{ route('explore') }}" class="btn btn-outline-likha rounded-pill mt-2"><i class="bi bi-arrow-left me-2"></i>Continue exploring</a>
        </div>
        <aside class="col-12 col-lg-4">
            <section class="bg-studio-card rounded-4 shadow-sm p-4">
                <h2 class="h5 fw-bold mb-3">Order summary</h2>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Items ({{ count($items) }})</span><span>₱{{ number_format($total, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-3"><span class="text-muted">Shipping</span><span>Calculated at checkout</span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5 mb-4"><span>Subtotal</span><span>₱{{ number_format($total, 2) }}</span></div>
                <a href="{{ route('checkout') }}" class="btn btn-likha rounded-4 w-100 py-3">Secure checkout <i class="bi bi-lock ms-2"></i></a>
            </section>
        </aside>
    </div>
</div>
@endsection
