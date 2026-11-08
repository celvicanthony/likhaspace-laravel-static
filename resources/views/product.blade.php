
@extends('layouts.app')

@section('title', 'Artwork details | LikhaSpace')

@php
    $isDatabaseArtwork = is_object($artwork);

    $artworkId = $isDatabaseArtwork
        ? $artwork->id
        : ($id ?? $artwork['id'] ?? null);

    $artworkTitle = $isDatabaseArtwork
        ? $artwork->title
        : $artwork['title'];

    $artworkArtist = $isDatabaseArtwork
        ? ($artwork->artist?->name ?? 'Unknown Artist')
        : ($artwork['artist'] ?? 'Unknown Artist');

    $artworkCountry = $isDatabaseArtwork
        ? $artwork->country
        : $artwork['country'];

    $artworkPrice = $isDatabaseArtwork
        ? $artwork->price
        : $artwork['price'];

    $artworkDescription = $isDatabaseArtwork
        ? $artwork->description
        : $artwork['description'];

    $artworkImage = $isDatabaseArtwork
        ? ($artwork->image_path
            ? asset('storage/' . $artwork->image_path)
            : null)
        : (isset($artwork['image'])
            ? 'https://images.unsplash.com/' . $artwork['image'] . '?q=85&w=1200&auto=format&fit=crop'
            : null);

    $isFavorite = $isFavorite ?? false;

    $isClient = auth()->check() && auth()->user()->role === 'client';
    $artworkRating = $isDatabaseArtwork ? $artwork->reviews_avg_rating : null;
    $reviewCount = $isDatabaseArtwork ? (int) $artwork->reviews_count : 0;
    $paidSales = $isDatabaseArtwork ? (int) ($artwork->paid_sales_count ?? 0) : 0;
    $artworkReviews = $isDatabaseArtwork
        ? $artwork->reviews()->latest()->take(5)->get()
        : collect();
@endphp

@push('styles')
<style>
    .product-page { max-width: 1240px; margin: 0 auto; }
    .product-breadcrumb { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 24px; color: var(--muted); font-size: .84rem; }
    .product-breadcrumb a, .product-back { color: var(--studio-link); text-decoration: none; }
    .product-breadcrumb a:hover, .product-back:hover { color: var(--studio-primary); }
    .product-layout { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr); align-items: start; gap: clamp(24px, 4vw, 56px); }
    .product-artwork { position: relative; overflow: hidden; aspect-ratio: 1; padding: 14px; border: 1px solid var(--studio-border); border-radius: 24px; background: var(--studio-card); box-shadow: var(--shadow); }
    .product-artwork img { position: relative; z-index: 1; display: block; width: 100%; height: 100%; border-radius: 16px; object-fit: cover; }
    .product-image-fallback { position: absolute; inset: 14px; display: grid; place-content: center; gap: 12px; border-radius: 16px; background: radial-gradient(circle at 50% 40%, var(--surface-strong), var(--sidebar-bg)); color: var(--studio-link); text-align: center; }
    .product-image-fallback i { font-size: clamp(5rem, 14vw, 9rem); }
    .product-image-fallback span { color: var(--ink); font-family: Poppins, sans-serif; font-size: clamp(1rem, 2vw, 1.4rem); font-weight: 600; }
    .product-image-fallback.is-hidden { display: none; }
    .best-seller { position: absolute; top: 26px; right: 26px; z-index: 2; display: inline-flex; align-items: center; gap: 6px; padding: 8px 13px; border: 1px solid color-mix(in srgb, var(--studio-primary) 20%, transparent); border-radius: 999px; background: var(--surface); color: var(--studio-link); font-size: .76rem; font-weight: 700; box-shadow: var(--shadow); }
    .product-summary { min-width: 0; }
    .product-rating { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 14px; }
    .product-stars { color: var(--studio-primary); letter-spacing: .05em; }
    .product-rating-value { color: var(--ink); font-size: .9rem; font-weight: 700; }
    .product-sold { padding: 5px 11px; border: 1px solid var(--studio-border); border-radius: 999px; background: var(--surface); color: var(--studio-link); font-size: .75rem; font-weight: 600; }
    .product-title { margin: 0 0 20px; font-size: clamp(2rem, 3.5vw, 3rem); font-weight: 700; line-height: 1.15; letter-spacing: -.04em; overflow-wrap: anywhere; }
    .product-creator { display: flex; align-items: center; gap: 12px; margin-bottom: 19px; }
    .creator-icon { display: grid; width: 46px; height: 46px; flex: 0 0 46px; place-items: center; border: 1px solid var(--studio-border); border-radius: 50%; background: var(--surface-strong); color: var(--studio-link); font-size: 1.2rem; }
    .creator-details { min-width: 0; color: var(--muted); font-size: .78rem; line-height: 1.5; }
    .creator-name { display: flex; flex-wrap: wrap; align-items: center; gap: 5px; color: var(--ink); font-size: .92rem; font-weight: 700; }
    .creator-verified { color: var(--studio-primary); }
    .product-price { margin: 0 0 18px; color: var(--ink); font-size: 1.75rem; font-weight: 700; letter-spacing: -.03em; }
    .product-description-label { margin-bottom: 5px; color: var(--ink); font-size: .86rem; font-weight: 700; }
    .product-description { margin-bottom: 16px; color: var(--muted); font-size: .9rem; line-height: 1.7; }
    .availability-banner { display: flex; align-items: center; gap: 9px; margin-bottom: 16px; padding: 12px 14px; border: 1px solid var(--studio-border); border-radius: 14px; background: var(--surface-strong); color: var(--studio-link); font-size: .84rem; font-weight: 600; }
    .product-meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 18px; }
    .product-meta-item { min-width: 0; padding: 12px 10px; border: 1px solid var(--studio-border); border-radius: 14px; background: var(--studio-card); }
    .product-meta-label { display: block; margin-bottom: 4px; color: var(--muted); font-size: .68rem; }
    .product-meta-value { display: block; color: var(--ink); font-size: .78rem; font-weight: 700; overflow-wrap: anywhere; }
    .product-actions { display: flex; align-items: stretch; gap: 9px; }
    .product-favorite-form, .product-cart-form { display: flex; }
    .product-cart-form { flex: 1; }
    .product-add, .product-buy { display: inline-flex; min-height: 48px; flex: 1; align-items: center; justify-content: center; gap: 8px; padding: 10px 12px; border-radius: 13px; font-size: .86rem; font-weight: 700; text-decoration: none; transition: background-color .15s, color .15s, border-color .15s; }
    .product-add { border: 1px solid var(--studio-border); background: var(--surface); color: var(--ink); }
    .product-add:hover { border-color: var(--studio-primary); background: var(--surface-strong); color: var(--studio-link); }
    .product-buy { border: 1px solid var(--studio-primary); background: var(--studio-primary); color: #fff; }
    .product-buy:hover { border-color: var(--studio-primary); background: var(--studio-primary); color: #fff; filter: brightness(1.1); }
    .product-favorite { display: grid; width: 48px; height: 48px; flex: 0 0 48px; place-items: center; border: 1px solid var(--studio-border); border-radius: 50%; background: var(--surface); color: var(--studio-link); font-size: 1.1rem; }
    .product-favorite[aria-pressed="true"] { border-color: var(--studio-primary); background: var(--surface-strong); color: var(--studio-primary); }
    .product-trust { display: flex; flex-wrap: wrap; gap: 14px 20px; margin-top: 15px; color: var(--muted); font-size: .76rem; }
    .product-trust i { margin-right: 5px; color: var(--studio-link); }
    .product-back { display: inline-flex; align-items: center; gap: 7px; margin-top: 28px; font-size: .86rem; font-weight: 600; }

    @media (max-width: 767.98px) {
        .product-layout { grid-template-columns: minmax(0, 1fr); gap: 24px; }
        .product-artwork { max-width: 620px; width: 100%; margin: 0 auto; }
    }

    @media (max-width: 420px) {
        .product-breadcrumb { gap: 6px; font-size: .76rem; }
        .product-artwork { padding: 10px; border-radius: 20px; }
        .best-seller { top: 19px; right: 19px; }
        .product-meta { gap: 7px; }
        .product-meta-item { padding: 10px 8px; }
        .product-meta-value { font-size: .72rem; }
        .product-actions { gap: 7px; }
        .product-add, .product-buy { min-height: 46px; padding: 9px 7px; font-size: .78rem; }
        .product-favorite { width: 46px; height: 46px; flex-basis: 46px; }
    }
</style>
@endpush

@section('content')
<div class="product-page">

    {{-- Breadcrumb --}}
    <nav class="product-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <i class="bi bi-chevron-right small" aria-hidden="true"></i>
        <a href="{{ route('explore') }}">Explore Art</a>
        <i class="bi bi-chevron-right small" aria-hidden="true"></i>
        <span aria-current="page">{{ $artworkTitle }}</span>
    </nav>

    {{-- Success Message --}}
    @if (session('status'))
        <div class="alert alert-success rounded-3">
            {{ session('status') }}
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

    <article class="product-layout">

        {{-- Artwork Image --}}
        <div class="product-artwork">

            <div class="product-image-fallback" data-product-image-fallback aria-hidden="true">
                <i class="bi bi-stars"></i>
                <span>{{ $artworkTitle }}</span>
            </div>

            @if ($artworkImage)
                <img
                    src="{{ $artworkImage }}"
                    alt="{{ $artworkTitle }} artwork preview"
                    data-product-image>
            @endif

        </div>

        {{-- Artwork Information --}}
        <div class="product-summary">

            <div class="product-rating">
                @if ($reviewCount > 0)
                    <span class="product-stars" aria-label="Rated {{ number_format((float) $artworkRating, 1) }} out of 5">
                        @for ($star = 1; $star <= 5; $star++)
                            <i class="bi {{ $star <= round((float) $artworkRating) ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                        @endfor
                    </span>
                    <span class="product-rating-value">{{ number_format((float) $artworkRating, 1) }}/5</span>
                    <span class="product-sold">{{ $reviewCount }} review{{ $reviewCount === 1 ? '' : 's' }}</span>
                @else
                    <span class="product-sold">No reviews yet</span>
                @endif
                <span class="product-sold">{{ $paidSales }} sold</span>
            </div>

            <h1 class="product-title">{{ $artworkTitle }}</h1>

            <div class="product-creator">
                <span class="creator-icon" aria-hidden="true">
                    <i class="bi bi-person-fill"></i>
                </span>

                <div class="creator-details">
                    <span class="d-block">Created By:</span>

                    <span class="creator-name">
                        @if ($isDatabaseArtwork && $artwork->artist)
                            <a class="text-decoration-none" href="{{ route('artists.show', $artwork->artist->id) }}">{{ $artworkArtist }}</a>
                        @else
                        {{ $artworkArtist }}
                        @endif

                        @if (! $isDatabaseArtwork)
                            <i class="bi bi-patch-check-fill creator-verified" aria-label="Verified artist"></i>
                        @endif
                    </span>

                    <span>{{ $artworkCountry }} · Digital artist</span>
                </div>
            </div>

            <p class="product-price">
                ₱{{ number_format((float) $artworkPrice, 2) }}
            </p>

            <h2 class="product-description-label">About this artwork</h2>

            <p class="product-description">
                {{ $artworkDescription ?: 'No description provided for this artwork.' }}
            </p>

            <div class="availability-banner">
                <i class="bi bi-check-circle" aria-hidden="true"></i>

                @if ($isDatabaseArtwork)
                    <span>Approved artwork · Available for purchase</span>
                @else
                    <span>Available artwork</span>
                @endif
            </div>

            {{-- Artwork Details --}}
            <div class="product-meta" aria-label="Artwork details">

                <div class="product-meta-item">
                    <span class="product-meta-label">File Format</span>
                    <span class="product-meta-value">Digital Image</span>
                </div>

                <div class="product-meta-item">
                    <span class="product-meta-label">Category</span>
                    <span class="product-meta-value">Digital Art</span>
                </div>

                <div class="product-meta-item">
                    <span class="product-meta-label">License</span>
                    <span class="product-meta-value">Personal Use</span>
                </div>

            </div>

            {{-- Product Actions --}}
            <div class="product-actions">

                @if ($isClient)

                    @if ($isDatabaseArtwork)

                        {{-- Add to Cart --}}
                        <form
                            action="{{ route('cart.add', $artworkId) }}"
                            method="POST"
                            class="product-cart-form">

                            @csrf

                            <input type="hidden" name="quantity" value="1">

                            <button type="submit" class="product-add w-100">
                                <i class="bi bi-bag-plus" aria-hidden="true"></i>
                                Add to Cart
                            </button>

                        </form>

                        {{-- Buy Now --}}
                        <a href="{{ route('cart') }}" class="product-buy">
                            <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
                            Buy Now
                        </a>

                        {{-- Add to Favorites --}}
                        <form
                            action="{{ route('artwork.favorite', $artworkId) }}"
                            method="POST"
                            class="product-favorite-form">

                            @csrf

                            <button
                                class="product-favorite"
                                type="submit"
                                aria-label="{{ $isFavorite ? 'Remove from' : 'Add to' }} favorites"
                                aria-pressed="{{ $isFavorite ? 'true' : 'false' }}">

                                <i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                            </button>

                        </form>

                    @else

                        {{-- Demo Artwork Actions --}}
                        <a href="{{ route('cart') }}" class="product-add">
                            <i class="bi bi-bag-plus" aria-hidden="true"></i>
                            Add to Cart
                        </a>

                        <a href="{{ route('checkout') }}" class="product-buy">
                            <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
                            Buy Now
                        </a>

                        @if ($artworkId)
                            <form
                                action="{{ route('favorites.toggle', $artworkId) }}"
                                method="POST"
                                class="product-favorite-form">

                                @csrf

                                <button
                                    class="product-favorite"
                                    type="submit"
                                    aria-label="{{ $isFavorite ? 'Remove from' : 'Add to' }} favorites"
                                    aria-pressed="{{ $isFavorite ? 'true' : 'false' }}">

                                    <i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }}"></i>

                                </button>

                            </form>
                        @endif

                    @endif

                @else

                    <a
                        href="{{ route('explore', ['country' => $artworkCountry]) }}"
                        class="product-add">

                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        Back to Explore

                    </a>

                @endif

            </div>

            @if ($isClient)
                <div class="product-trust">
                    <span>
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                        Secure Checkout
                    </span>

                    <span>
                        <i class="bi bi-download" aria-hidden="true"></i>
                        Digital Artwork
                    </span>
                </div>
            @endif

        </div>

    </article>

    @if ($artworkReviews->isNotEmpty())
        <section class="bg-studio-card rounded-4 p-3 p-md-4 mt-4" aria-labelledby="artwork-reviews-heading">
            <h2 class="h5 fw-bold" id="artwork-reviews-heading">Buyer reviews</h2>
            @foreach ($artworkReviews as $review)
                <article class="border-top py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-warning" aria-label="{{ $review->rating }} out of 5 stars">
                            @for ($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                            @endfor
                        </span>
                        <span class="text-muted small">Verified buyer · {{ $review->created_at->format('M j, Y') }}</span>
                    </div>
                    @if ($review->comment)
                        <p class="mb-0 mt-2">{{ $review->comment }}</p>
                    @endif
                </article>
            @endforeach
        </section>
    @endif

    <a href="{{ route('explore') }}" class="product-back">
        <i class="bi bi-arrow-left" aria-hidden="true"></i>
        Back to Explore Art
    </a>

</div>
@endsection

@push('scripts')
<script>
    const productImage = document.querySelector('[data-product-image]');
    const productImageFallback = document.querySelector('[data-product-image-fallback]');

    if (productImage && productImageFallback) {
        productImage.addEventListener('load', () => {
            productImageFallback.classList.add('is-hidden');
        });

        productImage.addEventListener('error', () => {
            productImageFallback.classList.remove('is-hidden');
        });

        if (productImage.complete && productImage.naturalWidth > 0) {
            productImageFallback.classList.add('is-hidden');
        }
    }
</script>
@endpush
