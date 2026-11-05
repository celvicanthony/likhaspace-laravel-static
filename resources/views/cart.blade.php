
@extends('layouts.app')

@section('title', 'Your Cart | LikhaSpace')

@section('content')

<div class="mx-auto" style="max-width: 1000px">

    {{-- Header --}}
    <div class="mb-4">
        <div class="home-kicker">Your collection</div>
        <h1 class="h2 fw-bold mb-1">My cart</h1>
        <p class="text-muted mb-0">
            Myth-inspired artwork, ready for your space.
        </p>
    </div>

    {{-- Success Message --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-3">
            {{ session('status') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row g-4">

        {{-- Cart Items --}}
        <div class="col-12 col-lg-8">

            @forelse ($items as $item)

                @php
                    $artwork = $item['artwork'];
                @endphp

                <article class="bg-studio-card rounded-4 shadow-sm p-3 p-md-4 mb-3">

                    <div class="d-flex align-items-center gap-3">

                        {{-- Artwork Image --}}
                        <img
                            src="{{ asset('storage/' . $artwork->image_path) }}"
                            alt="{{ $artwork->title }}"
                            class="rounded-4 flex-shrink-0"
                            style="width: 76px; height: 76px; object-fit: cover;">

                        {{-- Artwork Information --}}
                        <div class="flex-grow-1 min-w-0">

                            <div class="small text-muted">
                                {{ $artwork->country }}
                            </div>

                            <h2 class="h6 fw-bold mb-1">
                                {{ $artwork->title }}
                            </h2>

                            <p class="small text-muted mb-1">
                                By {{ $artwork->artist?->name ?? 'Unknown Artist' }}
                            </p>

                            <a href="{{ route('artwork.show', $artwork->id) }}"
                                class="small text-decoration-none">
                                View artwork
                            </a>

                        </div>

                        {{-- Price --}}
                        <div class="text-end fw-bold text-nowrap">
                            ₱{{ number_format($item['price'], 2) }}
                        </div>

                    </div>

                    <hr>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                        {{-- Update Quantity --}}
                        <form
                            action="{{ route('cart.update', $artwork->id) }}"
                            method="POST"
                            class="d-flex align-items-center gap-2">

                            @csrf
                            @method('PATCH')

                            <label for="quantity-{{ $artwork->id }}"
                                class="small text-muted">
                                Quantity:
                            </label>

                            <input
                                type="number"
                                id="quantity-{{ $artwork->id }}"
                                name="quantity"
                                class="form-control form-control-sm"
                                style="width: 75px"
                                value="{{ $item['quantity'] }}"
                                min="1"
                                max="99"
                                required>

                            <button type="submit"
                                class="btn btn-sm btn-outline-primary rounded-pill">
                                Update
                            </button>

                        </form>

                        {{-- Item Subtotal --}}
                        <div class="text-end">

                            <div class="small text-muted">
                                Item subtotal
                            </div>

                            <strong>
                                ₱{{ number_format($item['line_total'], 2) }}
                            </strong>

                        </div>

                    </div>

                    {{-- Remove Item --}}
                    <div class="text-end mt-3">

                        <form
                            action="{{ route('cart.remove', $artwork->id) }}"
                            method="POST">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="btn btn-sm btn-link text-danger text-decoration-none"
                                onclick="return confirm('Remove this artwork from your cart?')">

                                <i class="bi bi-trash me-1"></i>
                                Remove

                            </button>

                        </form>

                    </div>

                </article>

            @empty

                {{-- Empty Cart --}}
                <div class="bg-studio-card rounded-4 shadow-sm p-5 text-center">

                    <i class="bi bi-cart3 display-3 text-muted"></i>

                    <h2 class="h5 fw-bold mt-3">
                        Your cart is empty
                    </h2>

                    <p class="text-muted">
                        Discover beautiful artwork and add your favorites to your cart.
                    </p>

                    <a href="{{ route('explore') }}"
                        class="btn btn-likha rounded-pill px-4 mt-2">
                        Explore artworks
                    </a>

                </div>

            @endforelse

            @if (count($items) > 0)

                <a href="{{ route('explore') }}"
                    class="btn btn-outline-likha rounded-pill mt-2">

                    <i class="bi bi-arrow-left me-2"></i>
                    Continue exploring

                </a>

            @endif

        </div>

        {{-- Order Summary --}}
        <aside class="col-12 col-lg-4">

            <section class="bg-studio-card rounded-4 shadow-sm p-4">

                <h2 class="h5 fw-bold mb-3">
                    Order summary
                </h2>

                <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Items ({{ collect($items)->sum('quantity') }})
                    </span>

                    <span>
                        ₱{{ number_format($subtotal, 2) }}
                    </span>

                </div>

                <div class="d-flex justify-content-between mb-3">

                    <span class="text-muted">
                        Shipping
                    </span>

                    <span>
                        Free
                    </span>

                </div>

                <hr>

                <div class="d-flex justify-content-between fw-bold fs-5 mb-4">

                    <span>Total</span>

                    <span>
                        ₱{{ number_format($subtotal, 2) }}
                    </span>

                </div>

                @if (count($items) > 0)

                    <a href="{{ route('checkout') }}"
                        class="btn btn-likha rounded-4 w-100 py-3">

                        Secure checkout
                        <i class="bi bi-lock ms-2"></i>

                    </a>

                @else

                    <button type="button"
                        class="btn btn-secondary rounded-4 w-100 py-3"
                        disabled>

                        Secure checkout
                        <i class="bi bi-lock ms-2"></i>

                    </button>

                @endif

            </section>

        </aside>

    </div>

</div>

@endsection
