@extends('layouts.app')

@section('title', 'Artwork details | LikhaSpace')

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
    .best-seller { position: absolute; top: 26px; right: 26px; display: inline-flex; align-items: center; gap: 6px; padding: 8px 13px; border: 1px solid color-mix(in srgb, var(--studio-primary) 20%, transparent); border-radius: 999px; background: var(--surface); color: var(--studio-link); font-size: .76rem; font-weight: 700; box-shadow: var(--shadow); }
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
    .product-favorite-form { display: flex; }
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
    @media (max-width: 767.98px) { .product-layout { grid-template-columns: minmax(0, 1fr); gap: 24px; } .product-artwork { max-width: 620px; width: 100%; margin: 0 auto; } }
    @media (max-width: 420px) { .product-breadcrumb { gap: 6px; font-size: .76rem; } .product-artwork { padding: 10px; border-radius: 20px; } .best-seller { top: 19px; right: 19px; } .product-meta { gap: 7px; } .product-meta-item { padding: 10px 8px; } .product-meta-value { font-size: .72rem; } .product-actions { gap: 7px; } .product-add, .product-buy { min-height: 46px; padding: 9px 7px; font-size: .78rem; } .product-favorite { width: 46px; height: 46px; flex-basis: 46px; } }
</style>
@endpush

@section('content')
<div class="product-page">
    <nav class="product-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right small" aria-hidden="true"></i>
        <a href="{{ route('explore') }}">Explore Art</a><i class="bi bi-chevron-right small" aria-hidden="true"></i>
        <span aria-current="page">{{ $artwork['title'] }}</span>
    </nav>

    <article class="product-layout">
        <div class="product-artwork">
            <div class="product-image-fallback" data-product-image-fallback aria-hidden="true"><i class="bi bi-stars"></i><span>{{ $artwork['title'] }}</span></div>
            <img src="https://images.unsplash.com/{{ $artwork['image'] }}?q=85&w=1200&auto=format&fit=crop" alt="{{ $artwork['title'] }} artwork preview" data-product-image>
            <span class="best-seller"><i class="bi bi-stars" aria-hidden="true"></i> Best seller</span>
        </div>

        <div class="product-summary">
            <div class="product-rating" aria-label="Rated 4.9 out of 5; 32 sold">
                <span class="product-stars" aria-hidden="true"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></span>
                <span class="product-rating-value">4.9</span>
                <span class="product-sold">32 sold</span>
            </div>
            <h1 class="product-title">{{ $artwork['title'] }}</h1>

            <div class="product-creator">
                <span class="creator-icon" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
                <div class="creator-details">
                    <span class="d-block">Created By:</span>
                    <span class="creator-name">{{ $artwork['artist'] }} <i class="bi bi-patch-check-fill creator-verified" aria-label="Verified artist"></i></span>
                    <span>{{ $artwork['country'] }} · Digital artist</span>
                </div>
            </div>

            <p class="product-price">Php {{ number_format($artwork['price'], 2) }}</p>
            <h2 class="product-description-label">About this artwork</h2>
            <p class="product-description">{{ $artwork['description'] }}</p>

            <div class="availability-banner"><i class="bi bi-box-seam" aria-hidden="true"></i><span>4 artworks available</span></div>

            <div class="product-meta" aria-label="Artwork details">
                <div class="product-meta-item"><span class="product-meta-label">File Format</span><span class="product-meta-value">PNG</span></div>
                <div class="product-meta-item"><span class="product-meta-label">Resolution</span><span class="product-meta-value">6000 x 7000 px</span></div>
                <div class="product-meta-item"><span class="product-meta-label">License</span><span class="product-meta-value">Personal Use</span></div>
            </div>

            <div class="product-actions">
                @if (session('role', 'client') === 'client')
                    <a href="{{ route('cart') }}" class="product-add"><i class="bi bi-bag-plus" aria-hidden="true"></i>Add to Cart</a>
                    <a href="{{ route('checkout') }}" class="product-buy"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>Buy Now</a>
                    <form method="POST" action="{{ route('favorites.toggle', $id) }}" class="product-favorite-form">
                        @csrf
                        <button class="product-favorite" type="submit" aria-label="{{ $isFavorite ? 'Remove from' : 'Add to' }} favorites" aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"><i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }}"></i></button>
                    </form>
                @else
                    <a href="{{ route('explore', ['country' => $artwork['country']]) }}" class="product-add"><i class="bi bi-arrow-left" aria-hidden="true"></i>Back to Explore</a>
                @endif
            </div>
            @if (session('role', 'client') === 'client')
                <div class="product-trust"><span><i class="bi bi-shield-check" aria-hidden="true"></i>Secure Checkout</span><span><i class="bi bi-download" aria-hidden="true"></i>Lifetime Access</span></div>
            @endif
        </div>
    </article>

    <a href="{{ route('explore') }}" class="product-back"><i class="bi bi-arrow-left" aria-hidden="true"></i>Back to Explore Art</a>
</div>
@endsection

@push('scripts')
<script>
    const productImage = document.querySelector('[data-product-image]');
    const productImageFallback = document.querySelector('[data-product-image-fallback]');

    productImage?.addEventListener('load', () => productImageFallback.classList.add('is-hidden'));
    productImage?.addEventListener('error', () => productImageFallback.classList.remove('is-hidden'));
    if (productImage?.complete && productImage.naturalWidth > 0) {
        productImageFallback.classList.add('is-hidden');
    }
</script>
@endpush
