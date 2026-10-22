
@extends('layouts.app')

@section('title', 'Artwork Details | LikhaSpace')

@push('styles')
<style>
    .product-page {
        color: #1d1b68;
        font-family: 'Poppins', sans-serif;
    }

    .product-breadcrumb {
        font-size: .75rem;
        color: #77739c;
        margin-bottom: 14px;
    }

    .product-breadcrumb a {
        color: #7057e8;
        text-decoration: none;
    }

    .product-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(280px, .9fr);
        gap: 26px;
        align-items: start;
    }

    .product-image-card {
        padding: 12px;
        background: #fff;
        border: 1px solid #efedf4;
        border-radius: 17px;
        box-shadow: 0 4px 13px rgba(0, 0, 0, .2);
    }

    .product-artwork-image {
        position: relative;
        min-height: 380px;
        aspect-ratio: 4 / 5;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #aa94ef;
        color: #fff;
        overflow: hidden;
    }

    .product-artwork-image > i {
        font-size: 5rem;
        opacity: .35;
    }

    .best-seller-label {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #fff8f0;
        color: #1d1b68;
        padding: 7px 14px;
        border-radius: 22px;
        font-size: .8rem;
        font-weight: 800;
    }

    .product-information {
        min-width: 0;
        padding: 8px 2px;
    }

    .product-rating-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 17px;
    }

    .product-stars {
        color: #ffc66b;
        font-size: 1.65rem;
        letter-spacing: 1px;
        line-height: 1;
    }

    .rating-badge {
        border-radius: 20px;
        background: #e2daf9;
        padding: 6px 11px;
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .product-title {
        font-size: clamp(1.7rem, 2.8vw, 2.5rem);
        font-weight: 800;
        line-height: 1.2;
        overflow-wrap: anywhere;
        margin: 0 0 13px;
    }

    .product-artist {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 17px;
        font-size: .72rem;
        line-height: 1.5;
    }

    .product-artist-avatar {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #8058ef;
        color: #fff;
        border-radius: 50%;
        flex-shrink: 0;
        font-size: 1.7rem;
    }

    .product-artist-name {
        font-weight: 800;
    }

    .verified-icon {
        color: #7057e8;
        font-size: 1rem;
        margin-left: 3px;
    }

    .product-price {
        font-size: 1.05rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .product-description-heading {
        font-size: .76rem;
        font-weight: 800;
        margin: 0 0 9px;
    }

    .product-description {
        font-size: .75rem;
        line-height: 1.55;
        margin: 0 0 14px;
        color: #37335f;
    }

    .product-stock {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f3efff;
        color: #51429b;
        font-size: .78rem;
        font-weight: 800;
        margin-bottom: 13px;
    }

    .product-stock.low-stock {
        background: #fff1dd;
        color: #9b5a00;
    }

    .product-stock.out-of-stock {
        background: #ffe6e8;
        color: #bb1f38;
    }

    .product-specifications {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        border: 1px solid #e6e1ed;
        border-radius: 10px;
        margin: 0 0 13px;
        overflow: hidden;
    }

    .product-spec {
        padding: 10px 8px;
        font-size: .68rem;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .product-spec + .product-spec {
        border-left: 1px solid #e6e1ed;
    }

    .product-spec strong {
        display: block;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .product-actions {
        display: grid;
        grid-template-columns: 1fr 1fr 42px;
        gap: 10px;
        align-items: center;
        margin-bottom: 12px;
    }

    .product-action-button {
        min-height: 43px;
        border: 0;
        border-radius: 24px;
        padding: 10px 12px;
        font-family: inherit;
        font-size: .72rem;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(0, 0, 0, .15);
        transition: background .2s, transform .2s;
    }

    .add-cart-button {
        position: relative;
        overflow: hidden;
        color: #1d1b68;
        background: #fff;
    }

    .add-cart-button:hover {
        background: #f3efff;
    }

    .add-cart-button:active {
        transform: scale(.94);
    }

    .buy-now-button {
        color: #fff;
        background: #22baf2;
    }

    .buy-now-button:hover {
        background: #0ba5df;
    }

    .product-action-button:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .product-favorite-button {
        width: 40px;
        height: 40px;
        border: 1px solid #e7e3ee;
        border-radius: 50%;
        background: #fff;
        color: #e31837;
        font-size: 1.35rem;
        cursor: pointer;
        transition: transform .2s;
    }

    .product-favorite-button:active {
        transform: scale(.85);
    }

    .product-favorite-button:not(.favorited) {
        color: #aaa5c0;
    }

    /* Floating artwork animation */
    .cart-flying-art {
        position: fixed;
        z-index: 99999;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #8058ef;
        color: #fff;
        border: 3px solid #fff;
        border-radius: 15px;
        box-shadow: 0 8px 22px rgba(80, 48, 180, .3);
        font-size: 1.5rem;
        pointer-events: none;
        will-change: transform, left, top, opacity;
    }

    @keyframes cart-arrival-bounce {
        0% {
            transform: scale(1);
        }
        35% {
            transform: scale(1.35) rotate(-10deg);
        }
        60% {
            transform: scale(.85) rotate(7deg);
        }
        80% {
            transform: scale(1.12);
        }
        100% {
            transform: scale(1);
        }
    }

    .cart-arrival-bounce {
        animation: cart-arrival-bounce .55s ease;
    }

    /* Small confirmation message */
    .cart-toast {
        position: fixed;
        z-index: 100000;
        bottom: 25px;
        right: 25px;
        max-width: calc(100vw - 40px);
        padding: 14px 20px;
        border-radius: 14px;
        background: #1d1b68;
        color: white;
        box-shadow: 0 8px 25px rgba(29, 27, 104, .25);
        font-size: .85rem;
        font-weight: 700;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity .3s ease, transform .3s ease;
        pointer-events: none;
    }

    .cart-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .product-benefits {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        font-size: .68rem;
    }

    .product-benefits span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .product-benefits i {
        color: #7057e8;
        font-size: .95rem;
    }

    .back-to-explore {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 18px;
        font-size: .75rem;
        font-weight: 700;
        color: #7057e8;
        text-decoration: none;
    }

    .back-to-explore:hover {
        color: #4d38c5;
        text-decoration: underline;
    }

    @media (max-width: 900px) {
        .product-layout {
            grid-template-columns: 1fr;
        }

        .product-artwork-image {
            min-height: 300px;
            max-height: 600px;
        }
    }

    @media (max-width: 480px) {
        .product-specifications {
            grid-template-columns: 1fr;
        }

        .product-spec + .product-spec {
            border-left: 0;
            border-top: 1px solid #e6e1ed;
        }

        .product-actions {
            grid-template-columns: 1fr 1fr 38px;
            gap: 6px;
        }

        .product-action-button {
            padding: 9px 5px;
            font-size: .66rem;
        }

        .product-stars {
            font-size: 1.3rem;
        }

        .cart-toast {
            bottom: 15px;
            right: 15px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .cart-arrival-bounce {
            animation: none;
        }

        .cart-toast {
            transition: none;
        }
    }
</style>
@endpush

@section('content')

@php
    $artworks = [
        1 => [
            'name' => 'Tikbalang in the Mist',
            'artist' => 'Janessa Mae Ramos',
            'price' => 300,
            'category' => 'Philippines',
            'stock' => 8,
            'description' => 'A mystical digital artwork of the Tikbalang, a legendary Philippine creature with a human-like body and a horse’s head, standing mysteriously in a dark, enchanted forest surrounded by mist and glowing foliage.',
            'rating' => 5.0,
            'sold' => 124,
            'format' => 'JPG',
            'resolution' => '6000 x 7500 px',
            'license' => 'Personal Use',
        ],
        2 => [
            'name' => 'Moonlit Kitsune',
            'artist' => 'Vinz Jerby Acaylar',
            'price' => 350,
            'category' => 'Japan',
            'stock' => 5,
            'description' => 'A fantasy-inspired digital illustration featuring a mysterious kitsune beneath the moonlight.',
            'rating' => 4.9,
            'sold' => 98,
            'format' => 'PNG',
            'resolution' => '5000 x 6500 px',
            'license' => 'Personal Use',
        ],
        3 => [
            'name' => 'Olympian Dreams',
            'artist' => 'Celvic Anthony Borromeo',
            'price' => 400,
            'category' => 'Greece',
            'stock' => 12,
            'description' => 'An imaginative digital artwork inspired by Greek mythology, ancient legends, and the gods of Olympus.',
            'rating' => 4.8,
            'sold' => 76,
            'format' => 'JPG',
            'resolution' => '6000 x 6000 px',
            'license' => 'Personal Use',
        ],
        4 => [
            'name' => 'Guardian of the Highlands',
            'artist' => 'Vinz Jerby Acaylar',
            'price' => 320,
            'category' => 'Scotland',
            'stock' => 3,
            'description' => 'A fantasy artwork inspired by Scottish folklore, legendary guardians, and misty highland landscapes.',
            'rating' => 4.9,
            'sold' => 63,
            'format' => 'PNG',
            'resolution' => '5000 x 7000 px',
            'license' => 'Personal Use',
        ],
        5 => [
            'name' => 'Sakura Reverie',
            'artist' => 'Lauren Jane Isleta',
            'price' => 280,
            'category' => 'Japan',
            'stock' => 0,
            'description' => 'A gentle digital artwork inspired by cherry blossoms, springtime, and the beauty of Japanese scenery.',
            'rating' => 4.7,
            'sold' => 45,
            'format' => 'JPG',
            'resolution' => '4000 x 6000 px',
            'license' => 'Personal Use',
        ],
        6 => [
            'name' => 'Enchanted Philippine Forest',
            'artist' => 'Celvic Anthony Borromeo',
            'price' => 300,
            'category' => 'Philippines',
            'stock' => 6,
            'description' => 'A digital illustration celebrating the mystery of Philippine forests and the folklore hidden within them.',
            'rating' => 5.0,
            'sold' => 51,
            'format' => 'PNG',
            'resolution' => '5500 x 7000 px',
            'license' => 'Personal Use',
        ],
        7 => [
            'name' => 'Highland Moon',
            'artist' => 'Janessa Mae Ramos',
            'price' => 330,
            'category' => 'Scotland',
            'stock' => 9,
            'description' => 'A dreamy illustration of a moonlit Scottish landscape inspired by legends and folklore.',
            'rating' => 4.8,
            'sold' => 39,
            'format' => 'JPG',
            'resolution' => '5000 x 6500 px',
            'license' => 'Personal Use',
        ],
        8 => [
            'name' => 'Athena’s Wisdom',
            'artist' => 'Lauren Jane Isleta',
            'price' => 380,
            'category' => 'Greece',
            'stock' => 4,
            'description' => 'A mythology-inspired artwork celebrating Athena, wisdom, creativity, and courage.',
            'rating' => 4.9,
            'sold' => 32,
            'format' => 'PNG',
            'resolution' => '6000 x 7000 px',
            'license' => 'Personal Use',
        ],
    ];

    $artworkId = (int) request()->route('id', 1);
    $art = $artworks[$artworkId] ?? $artworks[1];
@endphp

<div class="product-page">

    <div class="product-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <i class="bi bi-chevron-right"></i>
        <a href="{{ route('explore') }}">Explore Art</a>
        <i class="bi bi-chevron-right"></i>
        {{ $art['name'] }}
    </div>

    <div class="product-layout">

        <div class="product-image-card">
            <div class="product-artwork-image">
                <i class="bi bi-image"></i>
                <span class="best-seller-label">Best seller</span>
            </div>
        </div>

        <div class="product-information">

            <div class="product-rating-row">
                <span class="product-stars" aria-label="{{ $art['rating'] }} out of 5 stars">
                    ★★★★★
                </span>

                <span class="rating-badge">{{ number_format($art['rating'], 1) }}</span>
                <span class="rating-badge">{{ $art['sold'] }} sold</span>
            </div>

            <h1 class="product-title">{{ $art['name'] }}</h1>

            <div class="product-artist">
                <div class="product-artist-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div>
                    <div>Created By:</div>
                    <div class="product-artist-name">
                        {{ $art['artist'] }}
                        <i class="bi bi-patch-check-fill verified-icon"></i>
                    </div>
                    <div>{{ $art['category'] }} · Digital artist</div>
                </div>
            </div>

            <div class="product-price">
                Php {{ number_format($art['price'], 2) }}
            </div>

            <h2 class="product-description-heading">Description:</h2>
            <p class="product-description">{{ $art['description'] }}</p>

            <div class="product-stock
                {{ $art['stock'] === 0 ? 'out-of-stock' : ($art['stock'] <= 3 ? 'low-stock' : '') }}">

                @if ($art['stock'] === 0)
                    <i class="bi bi-x-circle-fill"></i>
                    Out of stock — currently unavailable
                @elseif ($art['stock'] <= 3)
                    <i class="bi bi-exclamation-circle-fill"></i>
                    Only {{ $art['stock'] }} left in stock
                @else
                    <i class="bi bi-box-seam-fill"></i>
                    {{ $art['stock'] }} artworks available
                @endif

            </div>

            <div class="product-specifications">
                <div class="product-spec">
                    <strong>File Format:</strong>
                    {{ $art['format'] }}
                </div>

                <div class="product-spec">
                    <strong>Resolution:</strong>
                    {{ $art['resolution'] }}
                </div>

                <div class="product-spec">
                    <strong>License:</strong>
                    {{ $art['license'] }}
                </div>
            </div>

            <div class="product-actions">

                <button
                    type="button"
                    class="product-action-button add-cart-button"
                    id="addToCartButton"
                    {{ $art['stock'] === 0 ? 'disabled' : '' }}
                >
                    <i class="bi bi-cart-plus"></i>
                    Add to Cart
                </button>

                <button
                    type="button"
                    class="product-action-button buy-now-button"
                    id="buyNowButton"
                    {{ $art['stock'] === 0 ? 'disabled' : '' }}
                >
                    Buy Now
                </button>

                <button
                    type="button"
                    class="product-favorite-button"
                    id="productFavoriteButton"
                    aria-label="Add to favorites"
                    aria-pressed="false"
                >
                    <i class="bi bi-heart"></i>
                </button>

            </div>

            <div class="product-benefits">
                <span>
                    <i class="bi bi-check-square-fill"></i>
                    Secure Checkout
                </span>

                <span>
                    <i class="bi bi-check-square-fill"></i>
                    Lifetime Access
                </span>
            </div>

            <a href="{{ route('explore') }}" class="back-to-explore">
                <i class="bi bi-arrow-left"></i>
                Back to Explore Art
            </a>

        </div>
    </div>
</div>

<div class="cart-toast" id="cartToast" role="status" aria-live="polite"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addToCartButton = document.getElementById('addToCartButton');
    const buyNowButton = document.getElementById('buyNowButton');
    const favoriteButton = document.getElementById('productFavoriteButton');
    const cartToast = document.getElementById('cartToast');

    const artwork = {
        id: {{ $artworkId }},
        name: @json($art['name']),
        artist: @json($art['artist']),
        price: {{ $art['price'] }},
        stock: {{ $art['stock'] }}
    };

    const checkoutUrl = @json(route('checkout'));
    const cartUrl = @json(route('cart'));

    // Read the saved cart
    function getCart() {
        try {
            const cart = JSON.parse(localStorage.getItem('likhaspace_cart')) || [];
            return Array.isArray(cart) ? cart : [];
        } catch (error) {
            return [];
        }
    }

    // Save artwork to the cart
    function addToCart() {
        if (artwork.stock <= 0) {
            showToast('Sorry, this artwork is out of stock.');
            return false;
        }

        const cart = getCart();

        const existingItem = cart.find(function (item) {
            return Number(item.id) === artwork.id;
        });

        if (existingItem) {
            const currentQuantity = Number(existingItem.quantity) || 1;

            if (currentQuantity >= artwork.stock) {
                showToast('You have reached the available stock limit.');
                return false;
            }

            existingItem.quantity = currentQuantity + 1;
        } else {
            cart.push({
                id: artwork.id,
                quantity: 1
            });
        }

        try {
            localStorage.setItem('likhaspace_cart', JSON.stringify(cart));
            return true;
        } catch (error) {
            showToast('Unable to save your cart. Please check your browser settings.');
            return false;
        }
    }

    // Show a small confirmation message
    let toastTimer;

    function showToast(message) {
        cartToast.textContent = message;
        cartToast.classList.add('show');

        clearTimeout(toastTimer);

        toastTimer = setTimeout(function () {
            cartToast.classList.remove('show');
        }, 2500);
    }

    // Find the cart link in the sidebar
    function getCartLink() {
        return Array.from(document.querySelectorAll('a[href]')).find(function (link) {
            return link.pathname === new URL(cartUrl, window.location.origin).pathname;
        });
    }

    // Animate the artwork floating toward the cart
    function animateArtworkToCart() {
        const cartLink = getCartLink();

        if (!cartLink) {
            showToast(artwork.name + ' added to your cart!');
            return;
        }

        const start = addToCartButton.getBoundingClientRect();
        const end = cartLink.getBoundingClientRect();

        const flyer = document.createElement('div');
        flyer.className = 'cart-flying-art';
        flyer.innerHTML = '<i class="bi bi-image"></i>';
        flyer.setAttribute('aria-hidden', 'true');

        const startX = start.left + start.width / 2 - 24;
        const startY = start.top + start.height / 2 - 24;
        const endX = end.left + end.width / 2 - 24;
        const endY = end.top + end.height / 2 - 24;

        flyer.style.left = startX + 'px';
        flyer.style.top = startY + 'px';

        document.body.appendChild(flyer);

        const midX = (startX + endX) / 2;
        const midY = Math.min(startY, endY) - 100;

        const animation = flyer.animate([
            {
                left: startX + 'px',
                top: startY + 'px',
                transform: 'scale(1) rotate(0deg)',
                opacity: 1
            },
            {
                left: midX + 'px',
                top: midY + 'px',
                transform: 'scale(1.2) rotate(-15deg)',
                opacity: 1,
                offset: 0.45
            },
            {
                left: endX + 'px',
                top: endY + 'px',
                transform: 'scale(.35) rotate(20deg)',
                opacity: .4
            }
        ], {
            duration: 750,
            easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            fill: 'forwards'
        });

        animation.onfinish = function () {
            flyer.remove();

            cartLink.classList.remove('cart-arrival-bounce');
            void cartLink.offsetWidth;
            cartLink.classList.add('cart-arrival-bounce');

            setTimeout(function () {
                cartLink.classList.remove('cart-arrival-bounce');
            }, 600);

            showToast(artwork.name + ' added to your cart!');
        };

        animation.oncancel = function () {
            flyer.remove();
        };
    }

    // Add to Cart button
    addToCartButton.addEventListener('click', function () {
        if (addToCart()) {
            animateArtworkToCart();
        }
    });

    // Buy Now button
    buyNowButton.addEventListener('click', function () {
        if (addToCart()) {
            window.location.href = checkoutUrl;
        }
    });

    // Read saved favorites
    function getFavorites() {
        try {
            const favorites = JSON.parse(
                localStorage.getItem('likhaspace_favorites')
            ) || [];

            return Array.isArray(favorites) ? favorites.map(Number) : [];
        } catch (error) {
            return [];
        }
    }

    // Update the heart icon
    function updateFavoriteButton(isFavorited) {
        favoriteButton.classList.toggle('favorited', isFavorited);

        favoriteButton.setAttribute(
            'aria-pressed',
            isFavorited ? 'true' : 'false'
        );

        favoriteButton.setAttribute(
            'aria-label',
            isFavorited ? 'Remove from favorites' : 'Add to favorites'
        );

        favoriteButton.querySelector('i').className =
            isFavorited ? 'bi bi-heart-fill' : 'bi bi-heart';
    }

    // Restore favorite status on page load
    updateFavoriteButton(getFavorites().includes(artwork.id));

    // Toggle favorites
    favoriteButton.addEventListener('click', function () {
        let favorites = getFavorites();
        const isFavorited = favorites.includes(artwork.id);

        if (isFavorited) {
            favorites = favorites.filter(function (id) {
                return id !== artwork.id;
            });
        } else {
            favorites.push(artwork.id);
        }

        try {
            localStorage.setItem(
                'likhaspace_favorites',
                JSON.stringify(favorites)
            );

            updateFavoriteButton(!isFavorited);
            showToast(
                isFavorited
                    ? 'Removed from your favorites.'
                    : 'Added to your favorites!'
            );
        } catch (error) {
            showToast('Unable to save favorites in this browser.');
        }
    });
});
</script>
@endpush
