
@extends('layouts.app')

@section('title', 'Secure Checkout | LikhaSpace')

@push('styles')
<style>
    .checkout-page {
        color: #211b68;
        font-family: 'Poppins', sans-serif;
        padding-bottom: 35px;
    }

    .checkout-heading {
        margin-bottom: 24px;
    }

    .checkout-heading h1 {
        font-size: clamp(1.7rem, 3vw, 2.3rem);
        font-weight: 800;
        margin-bottom: 7px;
    }

    .checkout-heading p {
        color: #77739c;
        font-size: .9rem;
        margin: 0;
    }

    .checkout-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, .8fr);
        gap: 24px;
        align-items: start;
    }

    .checkout-card {
        background: #fff;
        border: 1px solid #ebe6f6;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 5px 20px rgba(50, 32, 110, .06);
    }

    .checkout-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .checkout-section-title .step-number {
        width: 32px;
        height: 32px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
        background: #eee8ff;
        color: #7057e8;
        border-radius: 50%;
        font-size: .85rem;
    }

    .checkout-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .checkout-field {
        min-width: 0;
    }

    .checkout-field.full-width {
        grid-column: 1 / -1;
    }

    .checkout-field label {
        display: block;
        font-size: .8rem;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .checkout-field input {
        width: 100%;
        min-height: 45px;
        padding: 11px 13px;
        border: 1px solid #ded8ee;
        border-radius: 10px;
        outline: none;
        font: inherit;
        font-size: .85rem;
        color: #211b68;
        background: #fff;
        box-sizing: border-box;
        transition: border-color .2s, box-shadow .2s;
    }

    .checkout-field input:focus {
        border-color: #8058ef;
        box-shadow: 0 0 0 3px rgba(128, 88, 239, .12);
    }

    .checkout-field input::placeholder {
        color: #aaa4bf;
    }

    .payment-options {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .payment-option {
        position: relative;
        min-width: 0;
    }

    .payment-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .payment-label {
        min-height: 125px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 11px;
        padding: 14px 8px;
        text-align: center;
        border: 2px solid #e8e2f2;
        border-radius: 14px;
        cursor: pointer;
        transition: border-color .2s, background .2s, transform .2s;
    }

    .payment-label:hover {
        border-color: #b9a5f5;
        transform: translateY(-2px);
    }

    .payment-option input:checked + .payment-label {
        border-color: #8058ef;
        background: #f5f0ff;
        box-shadow: 0 0 0 2px rgba(128, 88, 239, .08);
    }

    .payment-option input:focus-visible + .payment-label {
        outline: 3px solid #22baf2;
        outline-offset: 3px;
    }

    .payment-logo {
        min-width: 80px;
        min-height: 39px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 9px;
        font-size: .95rem;
        font-weight: 900;
        letter-spacing: -.5px;
    }

    .gcash-logo {
        color: #fff;
        background: #007dff;
    }

    .maya-logo {
        color: #fff;
        background: #00a870;
    }

    .landbank-logo {
        color: #fff;
        background: #087b43;
        font-size: .75rem;
        letter-spacing: 0;
    }

    .payment-name {
        font-size: .8rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .payment-check {
        color: #7057e8;
        font-size: .75rem;
    }

    .payment-security-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 18px;
        padding: 13px;
        background: #f4f0ff;
        color: #51429b;
        border-radius: 11px;
        font-size: .77rem;
        line-height: 1.6;
    }

    .payment-security-note i {
        font-size: 1.1rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .payment-security-note p {
        margin: 0;
    }

    .payment-demo-note {
        margin-top: 12px;
        padding: 12px;
        background: #fff5df;
        color: #875b13;
        border-radius: 10px;
        font-size: .76rem;
        line-height: 1.6;
    }

    .payment-demo-note strong {
        display: block;
        margin-bottom: 3px;
    }

    .order-summary {
        position: sticky;
        top: 20px;
    }

    .summary-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #f0edf6;
    }

    .summary-art-icon {
        width: 48px;
        height: 55px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eee7ff;
        color: #8058ef;
        border-radius: 10px;
        font-size: 1.4rem;
    }

    .summary-item-details {
        min-width: 0;
        flex: 1;
    }

    .summary-item-name {
        font-size: .8rem;
        font-weight: 800;
        overflow-wrap: anywhere;
        margin-bottom: 4px;
    }

    .summary-item-artist {
        color: #8983a5;
        font-size: .7rem;
        margin-bottom: 5px;
    }

    .summary-item-quantity {
        color: #77739c;
        font-size: .72rem;
    }

    .summary-item-price {
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .summary-totals {
        padding-top: 16px;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 13px;
        color: #77739c;
        font-size: .82rem;
    }

    .summary-line strong {
        color: #211b68;
        text-align: right;
    }

    .summary-total {
        border-top: 1px solid #e9e3f3;
        padding-top: 16px;
        margin-top: 5px;
        font-size: 1rem;
        font-weight: 800;
        color: #211b68;
    }

    .summary-total strong {
        color: #7057e8;
        font-size: 1.25rem;
    }

    .place-order-button {
        width: 100%;
        min-height: 49px;
        border: none;
        border-radius: 25px;
        background: #8058ef;
        color: #fff;
        font: inherit;
        font-size: .85rem;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 5px 14px rgba(128, 88, 239, .23);
        transition: background .2s, transform .2s;
    }

    .place-order-button:hover {
        background: #6843dc;
        transform: translateY(-2px);
    }

    .place-order-button:active {
        transform: scale(.98);
    }

    .place-order-button:disabled {
        opacity: .5;
        cursor: not-allowed;
        transform: none;
    }

    .checkout-back-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 15px;
        color: #7057e8;
        text-decoration: none;
        font-size: .8rem;
        font-weight: 700;
    }

    .checkout-back-link:hover {
        text-decoration: underline;
    }

    .empty-checkout {
        padding: 45px 20px;
        text-align: center;
        background: #fff;
        border: 1px solid #ebe6f6;
        border-radius: 18px;
    }

    .empty-checkout i {
        font-size: 3rem;
        color: #b5a0f5;
    }

    .empty-checkout h2 {
        margin: 12px 0 8px;
        font-size: 1.3rem;
        font-weight: 800;
    }

    .empty-checkout p {
        color: #77739c;
        font-size: .85rem;
    }

    .checkout-message {
        display: none;
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 12px;
        background: #fff2e2;
        color: #805313;
        font-size: .85rem;
        line-height: 1.6;
    }

    .checkout-message.show {
        display: block;
    }

    @media (max-width: 850px) {
        .checkout-layout {
            grid-template-columns: 1fr;
        }

        .order-summary {
            position: static;
        }
    }

    @media (max-width: 520px) {
        .checkout-card {
            padding: 17px;
        }

        .checkout-form-grid {
            grid-template-columns: 1fr;
        }

        .checkout-field.full-width {
            grid-column: auto;
        }

        .payment-options {
            grid-template-columns: 1fr;
        }

        .payment-label {
            min-height: 75px;
            flex-direction: row;
            justify-content: flex-start;
            padding: 12px;
            text-align: left;
        }

        .payment-check {
            margin-left: auto;
        }
    }
</style>
@endpush

@section('content')

<div class="checkout-page">

    <div class="checkout-heading">
        <h1><i class="bi bi-shield-lock"></i> Secure Checkout</h1>
        <p>You're almost there! Review your order and select your online payment method.</p>
    </div>

    <div id="emptyCheckout" class="empty-checkout" style="display:none;">
        <i class="bi bi-cart-x"></i>
        <h2>Your cart is empty</h2>
        <p>Add some artwork before proceeding to checkout.</p>
        <a href="{{ route('explore') }}" class="checkout-back-link">
            <i class="bi bi-arrow-left"></i> Explore Art
        </a>
    </div>

    <div id="checkoutContent" class="checkout-layout" style="display:none;">

        <div>
            <div id="checkoutMessage" class="checkout-message" role="status" aria-live="polite"></div>

            <form id="checkoutForm">

                <!-- CUSTOMER INFORMATION -->
                <section class="checkout-card">
                    <h2 class="checkout-section-title">
                        <span class="step-number">1</span>
                        Customer Information
                    </h2>

                    <div class="checkout-form-grid">

                        <div class="checkout-field">
                            <label for="customerName">Full Name *</label>
                            <input
                                type="text"
                                id="customerName"
                                name="customerName"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="checkout-field">
                            <label for="customerEmail">Email Address *</label>
                            <input
                                type="email"
                                id="customerEmail"
                                name="customerEmail"
                                placeholder="you@example.com"
                                autocomplete="email"
                                maxlength="150"
                                required
                            >
                        </div>

                        <div class="checkout-field full-width">
                            <label for="customerPhone">Contact Number *</label>
                            <input
                                type="tel"
                                id="customerPhone"
                                name="customerPhone"
                                placeholder="e.g. 09XXXXXXXXX"
                                autocomplete="tel"
                                inputmode="tel"
                                maxlength="20"
                                required
                            >
                        </div>

                    </div>
                </section>

                <!-- PAYMENT METHOD -->
                <section class="checkout-card">
                    <h2 class="checkout-section-title">
                        <span class="step-number">2</span>
                        Choose Online Payment
                    </h2>

                    <div class="payment-options">

                        <div class="payment-option">
                            <input
                                type="radio"
                                id="paymentGcash"
                                name="paymentMethod"
                                value="GCash"
                                required
                            >

                            <label class="payment-label" for="paymentGcash">
                                <span class="payment-logo gcash-logo">GCash</span>
                                <span class="payment-name">GCash</span>
                                <i class="bi bi-check-circle-fill payment-check"></i>
                            </label>
                        </div>

                        <div class="payment-option">
                            <input
                                type="radio"
                                id="paymentMaya"
                                name="paymentMethod"
                                value="Maya"
                                required
                            >

                            <label class="payment-label" for="paymentMaya">
                                <span class="payment-logo maya-logo">Maya</span>
                                <span class="payment-name">Maya</span>
                                <i class="bi bi-check-circle-fill payment-check"></i>
                            </label>
                        </div>

                        <div class="payment-option">
                            <input
                                type="radio"
                                id="paymentLandbank"
                                name="paymentMethod"
                                value="LANDBANK"
                                required
                            >

                            <label class="payment-label" for="paymentLandbank">
                                <span class="payment-logo landbank-logo">LANDBANK</span>
                                <span class="payment-name">LANDBANK</span>
                                <i class="bi bi-check-circle-fill payment-check"></i>
                            </label>
                        </div>

                    </div>

                    <div class="payment-security-note">
                        <i class="bi bi-shield-check"></i>
                        <p>
                            <strong>Keep your account secure.</strong>
                            Never enter your wallet PIN, bank password, or OTP into this form.
                            Payment authentication should take place through the official payment
                            provider's secure process.
                        </p>
                    </div>

                    <div class="payment-demo-note">
                        <strong><i class="bi bi-info-circle"></i> Demo payment notice</strong>
                        These payment choices are for the LikhaSpace project demonstration.
                        No real payment will be processed until a payment gateway is integrated.
                    </div>
                </section>

                <a href="{{ route('cart') }}" class="checkout-back-link">
                    <i class="bi bi-arrow-left"></i>
                    Return to Cart
                </a>

            </form>
        </div>

        <!-- ORDER SUMMARY -->
        <aside class="order-summary">
            <section class="checkout-card">
                <h2 class="checkout-section-title">
                    <span class="step-number"><i class="bi bi-bag-check"></i></span>
                    Order Summary
                </h2>

                <div id="summaryItems"></div>

                <div class="summary-totals">
                    <div class="summary-line">
                        <span>Items</span>
                        <strong id="summaryQuantity">0</strong>
                    </div>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <strong id="summarySubtotal">₱0.00</strong>
                    </div>

                    <div class="summary-line">
                        <span>Delivery</span>
                        <strong>Free</strong>
                    </div>

                    <div class="summary-line summary-total">
                        <span>Total</span>
                        <strong id="summaryTotal">₱0.00</strong>
                    </div>
                </div>

                <button
                    type="submit"
                    form="checkoutForm"
                    class="place-order-button"
                    id="placeOrderButton"
                >
                    <i class="bi bi-lock-fill"></i>
                    Continue to Payment
                </button>

                <p style="font-size:.72rem; color:#8983a5; text-align:center; line-height:1.6; margin:13px 0 0;">
                    Your order will not be marked as paid by this demo form.
                </p>
            </section>
        </aside>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const cartKey = 'likhaspace_cart';

    const artworks = {
        1: {
            name: 'Tikbalang in the Mist',
            artist: 'Janessa Mae Ramos',
            price: 300,
            stock: 8
        },
        2: {
            name: 'Moonlit Kitsune',
            artist: 'Vinz Jerby Acaylar',
            price: 350,
            stock: 5
        },
        3: {
            name: 'Olympian Dreams',
            artist: 'Celvic Anthony Borromeo',
            price: 400,
            stock: 12
        },
        4: {
            name: 'Guardian of the Highlands',
            artist: 'Vinz Jerby Acaylar',
            price: 320,
            stock: 3
        },
        5: {
            name: 'Sakura Reverie',
            artist: 'Lauren Jane Isleta',
            price: 280,
            stock: 0
        },
        6: {
            name: 'Enchanted Philippine Forest',
            artist: 'Celvic Anthony Borromeo',
            price: 300,
            stock: 6
        },
        7: {
            name: 'Highland Moon',
            artist: 'Janessa Mae Ramos',
            price: 330,
            stock: 9
        },
        8: {
            name: 'Athena’s Wisdom',
            artist: 'Lauren Jane Isleta',
            price: 380,
            stock: 4
        }
    };

    const emptyCheckout = document.getElementById('emptyCheckout');
    const checkoutContent = document.getElementById('checkoutContent');
    const summaryItems = document.getElementById('summaryItems');
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutMessage = document.getElementById('checkoutMessage');
    const placeOrderButton = document.getElementById('placeOrderButton');

    let cart = [];

    function readCart() {
        try {
            const savedCart = JSON.parse(localStorage.getItem(cartKey)) || [];
            return Array.isArray(savedCart) ? savedCart : [];
        } catch (error) {
            return [];
        }
    }

    function formatMoney(amount) {
        return '₱' + Number(amount).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function showMessage(message) {
        checkoutMessage.textContent = message;
        checkoutMessage.classList.add('show');
        checkoutMessage.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    function renderCheckout() {
        cart = readCart();

        // Remove invalid or unavailable items from the checkout display.
        cart = cart.filter(function (item) {
            const id = Number(item.id);
            const artwork = artworks[id];
            const quantity = Number(item.quantity);

            return artwork &&
                artwork.stock > 0 &&
                Number.isInteger(quantity) &&
                quantity > 0 &&
                quantity <= artwork.stock;
        });

        if (cart.length === 0) {
            checkoutContent.style.display = 'none';
            emptyCheckout.style.display = 'block';
            return;
        }

        emptyCheckout.style.display = 'none';
        checkoutContent.style.display = 'grid';

        summaryItems.replaceChildren();

        let totalQuantity = 0;
        let subtotal = 0;

        cart.forEach(function (item) {
            const artwork = artworks[Number(item.id)];
            const quantity = Number(item.quantity);
            const itemSubtotal = artwork.price * quantity;

            totalQuantity += quantity;
            subtotal += itemSubtotal;

            const row = document.createElement('div');
            row.className = 'summary-item';

            const imageIcon = document.createElement('div');
            imageIcon.className = 'summary-art-icon';
            imageIcon.innerHTML = '<i class="bi bi-image"></i>';

            const details = document.createElement('div');
            details.className = 'summary-item-details';

            const name = document.createElement('div');
            name.className = 'summary-item-name';
            name.textContent = artwork.name;

            const artist = document.createElement('div');
            artist.className = 'summary-item-artist';
            artist.textContent = artwork.artist;

            const quantityText = document.createElement('div');
            quantityText.className = 'summary-item-quantity';
            quantityText.textContent =
                formatMoney(artwork.price) + ' × ' + quantity;

            const price = document.createElement('div');
            price.className = 'summary-item-price';
            price.textContent = formatMoney(itemSubtotal);

            details.append(name, artist, quantityText);
            row.append(imageIcon, details, price);
            summaryItems.appendChild(row);
        });

        document.getElementById('summaryQuantity').textContent = totalQuantity;
        document.getElementById('summarySubtotal').textContent = formatMoney(subtotal);
        document.getElementById('summaryTotal').textContent = formatMoney(subtotal);
    }

    checkoutForm.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!checkoutForm.reportValidity()) {
            return;
        }

        // Check the cart again before proceeding.
        cart = readCart();

        if (cart.length === 0) {
            showMessage('Your cart is empty. Please return to Explore Art.');
            renderCheckout();
            return;
        }

        const paymentChoice = document.querySelector(
            'input[name="paymentMethod"]:checked'
        );

        if (!paymentChoice) {
            showMessage('Please select an online payment method.');
            return;
        }

        /*
         * Demo only:
         * No payment is collected and no order is marked as paid.
         * Do not collect wallet passwords, PINs, or OTPs here.
         */

        const selectedMethod = paymentChoice.value;

        showMessage(
            'Demo only: You selected ' + selectedMethod +
            '. Your details passed the browser form validation, but no payment was processed. ' +
            'To accept real payments, LikhaSpace must be connected to an approved payment gateway. ' +
            'Your cart has been kept so you can continue testing.'
        );
    });

    renderCheckout();
});
</script>
@endpush
