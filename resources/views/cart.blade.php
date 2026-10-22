
@extends('layouts.app')

@section('title', 'LikhaSpace | My Cart')

@push('styles')
<style>
    .cart-page {
        color: #1d1b68;
        font-family: 'Poppins', sans-serif;
    }

    .cart-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .cart-subtitle {
        color: #77739c;
        font-size: .76rem;
        margin-bottom: 24px;
    }

    .cart-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 20px;
        align-items: start;
    }

    .cart-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .cart-item {
        display: grid;
        grid-template-columns: 100px minmax(0, 1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 13px;
        border: 1px solid #e8e1fa;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(29, 27, 104, .06);
    }

    .cart-art-image {
        height: 100px;
        border-radius: 12px;
        background: #aa94ef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }

    .cart-art-name {
        font-size: .9rem;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .cart-art-artist {
        color: #77739c;
        font-size: .7rem;
        margin-bottom: 5px;
    }

    .cart-unit-price {
        font-size: .76rem;
        font-weight: 700;
    }

    .cart-quantity {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .quantity-button {
        width: 29px;
        height: 29px;
        border: 1px solid #ded6ff;
        border-radius: 8px;
        background: #f3efff;
        color: #7057e8;
        font-weight: 800;
        cursor: pointer;
    }

    .quantity-value {
        min-width: 18px;
        text-align: center;
        font-size: .8rem;
        font-weight: 700;
    }

    .cart-item-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 18px;
    }

    .cart-subtotal {
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .remove-cart-item {
        border: 0;
        background: transparent;
        color: #df3152;
        font-size: 1.1rem;
        cursor: pointer;
    }

    .cart-summary {
        padding: 20px;
        border: 1px solid #e3dafb;
        border-radius: 16px;
        background: #f8f5ff;
    }

    .cart-summary h2 {
        font-size: 1rem;
        font-weight: 800;
        margin-bottom: 18px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 13px;
        font-size: .75rem;
    }

    .summary-total {
        border-top: 1px solid #ded6ff;
        padding-top: 15px;
        margin-top: 15px;
        font-size: .9rem;
        font-weight: 800;
    }

    .checkout-button,
    .continue-shopping {
        display: block;
        width: 100%;
        padding: 11px 14px;
        border-radius: 24px;
        text-align: center;
        text-decoration: none;
        font-family: inherit;
        font-size: .75rem;
        font-weight: 800;
    }

    .checkout-button {
        margin-top: 18px;
        border: 0;
        color: #fff;
        background: #7057e8;
        cursor: pointer;
    }

    .checkout-button:hover {
        background: #5941d0;
        color: #fff;
    }

    .checkout-button:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .continue-shopping {
        margin-top: 9px;
        color: #7057e8;
        border: 1px solid #d9cff8;
        background: #fff;
    }

    .cart-empty {
        display: none;
        padding: 45px 20px;
        text-align: center;
        border: 1px dashed #d9cff8;
        border-radius: 16px;
        background: #faf8ff;
    }

    .cart-empty i {
        display: block;
        margin-bottom: 12px;
        font-size: 2.8rem;
        color: #7057e8;
    }

    .cart-empty h2 {
        font-size: 1.1rem;
        font-weight: 800;
    }

    .cart-empty p {
        color: #77739c;
        font-size: .8rem;
    }

    @media (max-width: 850px) {
        .cart-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .cart-item {
            grid-template-columns: 75px minmax(0, 1fr) auto;
            gap: 9px;
            padding: 9px;
        }

        .cart-art-image {
            height: 80px;
        }

        .cart-art-name {
            font-size: .76rem;
        }

        .cart-item-right {
            gap: 12px;
        }

        .cart-subtotal {
            font-size: .7rem;
        }
    }
</style>
@endpush

@section('content')
<div class="cart-page">
    <h1 class="cart-title">My Cart <i class="bi bi-cart3"></i></h1>
    <p class="cart-subtitle">Review your selected artworks before checkout.</p>

    <div class="cart-layout" id="cartLayout">
        <div class="cart-items" id="cartItems"></div>

        <aside class="cart-summary">
            <h2>Order Summary</h2>

            <div class="summary-row">
                <span>Items</span>
                <span id="cartItemCount">0</span>
            </div>

            <div class="summary-row">
                <span>Subtotal</span>
                <span id="cartSubtotal">₱0.00</span>
            </div>

            <div class="summary-row">
                <span>Delivery</span>
                <span>Digital delivery</span>
            </div>

            <div class="summary-row summary-total">
                <span>Total</span>
                <span id="cartTotal">₱0.00</span>
            </div>

            <button class="checkout-button" id="checkoutButton" type="button">
                Proceed to Checkout <i class="bi bi-arrow-right"></i>
            </button>

            <a href="{{ route('explore') }}" class="continue-shopping">
                Continue Shopping
            </a>
        </aside>
    </div>

    <div class="cart-empty" id="cartEmpty">
        <i class="bi bi-cart-x"></i>
        <h2>Your cart is empty</h2>
        <p>Discover artwork you love and add it to your cart.</p>
        <a href="{{ route('explore') }}" class="checkout-button">
            Explore Artworks
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cartKey = 'likhaspace_cart';

    const artworks = [
        { id: 1, name: 'Tikbalang in the Mist', artist: 'Janessa Mae Ramos', price: 300 },
        { id: 2, name: 'Moonlit Kitsune', artist: 'Vinz Jerby Acaylar', price: 350 },
        { id: 3, name: 'Olympian Dreams', artist: 'Celvic Anthony Borromeo', price: 400 },
        { id: 4, name: 'Guardian of the Highlands', artist: 'Vinz Jerby Acaylar', price: 320 },
        { id: 5, name: 'Sakura Reverie', artist: 'Lauren Jane Isleta', price: 280 },
        { id: 6, name: 'Enchanted Philippine Forest', artist: 'Celvic Anthony Borromeo', price: 300 },
        { id: 7, name: 'Highland Moon', artist: 'Janessa Mae Ramos', price: 330 },
        { id: 8, name: 'Athena’s Wisdom', artist: 'Lauren Jane Isleta', price: 380 }
    ];

    const itemsContainer = document.getElementById('cartItems');
    const cartLayout = document.getElementById('cartLayout');
    const emptyMessage = document.getElementById('cartEmpty');

    function getCart() {
        try {
            const cart = JSON.parse(localStorage.getItem(cartKey) || '[]');
            return Array.isArray(cart) ? cart : [];
        } catch (error) {
            return [];
        }
    }

    function saveCart(cart) {
        localStorage.setItem(cartKey, JSON.stringify(cart));
    }

    function money(amount) {
        return '₱' + amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function renderCart() {
        const cart = getCart();
        itemsContainer.innerHTML = '';

        let totalQuantity = 0;
        let totalPrice = 0;

        cart.forEach(function (item) {
            const artwork = artworks.find(function (art) {
                return String(art.id) === String(item.id);
            });

            if (!artwork) return;

            const quantity = Math.max(1, Math.floor(Number(item.quantity) || 1));
            const subtotal = artwork.price * quantity;

            totalQuantity += quantity;
            totalPrice += subtotal;

            const card = document.createElement('article');
            card.className = 'cart-item';

            card.innerHTML = `
                <div class="cart-art-image">
                    <i class="bi bi-image"></i>
                </div>

                <div>
                    <div class="cart-art-name"></div>
                    <div class="cart-art-artist"></div>
                    <div class="cart-unit-price">${money(artwork.price)} each</div>

                    <div class="cart-quantity">
                        <button type="button" class="quantity-button decrease"
                                aria-label="Decrease quantity">−</button>
                        <span class="quantity-value">${quantity}</span>
                        <button type="button" class="quantity-button increase"
                                aria-label="Increase quantity">+</button>
                    </div>
                </div>

                <div class="cart-item-right">
                    <span class="cart-subtotal">${money(subtotal)}</span>
                    <button type="button" class="remove-cart-item"
                            aria-label="Remove item" title="Remove item">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            `;

            card.querySelector('.cart-art-name').textContent = artwork.name;
            card.querySelector('.cart-art-artist').textContent = 'By: ' + artwork.artist;

            card.querySelector('.decrease').addEventListener('click', function () {
                changeQuantity(artwork.id, -1);
            });

            card.querySelector('.increase').addEventListener('click', function () {
                changeQuantity(artwork.id, 1);
            });

            card.querySelector('.remove-cart-item').addEventListener('click', function () {
                const updatedCart = getCart().filter(function (cartItem) {
                    return String(cartItem.id) !== String(artwork.id);
                });

                saveCart(updatedCart);
                renderCart();
            });

            itemsContainer.appendChild(card);
        });

        document.getElementById('cartItemCount').textContent = totalQuantity;
        document.getElementById('cartSubtotal').textContent = money(totalPrice);
        document.getElementById('cartTotal').textContent = money(totalPrice);

        const isEmpty = totalQuantity === 0;
        cartLayout.style.display = isEmpty ? 'none' : 'grid';
        emptyMessage.style.display = isEmpty ? 'block' : 'none';

        // Save a clean cart containing only valid artwork IDs.
        const validCart = cart.filter(function (item) {
            return artworks.some(function (art) {
                return String(art.id) === String(item.id);
            });
        });

        if (validCart.length !== cart.length) {
            saveCart(validCart);
        }
    }

    function changeQuantity(id, amount) {
        const cart = getCart();
        const item = cart.find(function (cartItem) {
            return String(cartItem.id) === String(id);
        });

        if (!item) return;

        item.quantity = Math.max(1, (Number(item.quantity) || 1) + amount);
        saveCart(cart);
        renderCart();
    }

    document.getElementById('checkoutButton').addEventListener('click', function () {
        const cart = getCart();

        if (cart.length === 0) return;

        window.location.href = @json(route('checkout'));
    });

    window.addEventListener('pageshow', renderCart);

    renderCart();
});
</script>
@endpush
