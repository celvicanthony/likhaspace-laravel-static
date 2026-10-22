
@extends('layouts.app')

@section('title', 'LikhaSpace | Explore Artworks')

@push('styles')
<style>
    .explore-page {
        color: #1d1b68;
        font-family: 'Poppins', sans-serif;
        width: 100%;
    }

    .explore-heading {
        font-size: 1.45rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.4;
    }

    .explore-subtitle {
        font-size: .7rem;
        color: #77739c;
        margin: 0 0 13px;
    }

    .explore-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .art-search {
        height: 37px;
        border: 1px solid #e2dfea;
        border-radius: 22px;
        padding: 0 12px;
        display: flex;
        align-items: center;
        flex: 1 1 190px;
        min-width: 150px;
        max-width: 300px;
        gap: 8px;
        background: #fff;
    }

    .art-search input {
        border: 0;
        outline: none;
        width: 100%;
        min-width: 0;
        color: #1d1b68;
        font-family: inherit;
        font-size: .68rem;
        background: transparent;
    }

    .art-search input::placeholder {
        color: #aaa5c5;
    }

    .art-search i {
        font-size: .9rem;
    }

    .category-button {
        border: 0;
        border-radius: 22px;
        padding: 10px 18px;
        background: #e2daf9;
        color: #7057e8;
        font-family: inherit;
        font-size: .67rem;
        font-weight: 800;
        white-space: nowrap;
        cursor: pointer;
        transition: .2s;
    }

    .category-button.active,
    .category-button:hover {
        color: #fff;
        background: #7057e8;
    }

    .art-sort {
        height: 37px;
        border: 1px solid #e2dfea;
        border-radius: 22px;
        padding: 0 12px;
        color: #45416e;
        background: #fff;
        font-family: inherit;
        font-size: .68rem;
        cursor: pointer;
    }

    .art-filter {
        border: 0;
        background: transparent;
        color: #1d1b68;
        font-size: 1.5rem;
        padding: 0 2px;
        cursor: pointer;
    }

    .artwork-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px 17px;
    }

    .artwork-card {
        min-width: 0;
        padding: 10px;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .2);
        border: 1px solid #efedf4;
    }

    .artwork-image {
        width: 100%;
        height: 140px;
        border-radius: 13px;
        background: #aa94ef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .artwork-image-link {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
    }

    .artwork-image-link:hover {
        color: #fff;
        background: rgba(29, 27, 104, .08);
    }

    .artwork-image .art-placeholder-icon {
        font-size: 2.6rem;
        opacity: .45;
    }

    .favorite-button {
        position: absolute;
        z-index: 2;
        top: 7px;
        right: 7px;
        width: 29px;
        height: 29px;
        border: 0;
        border-radius: 50%;
        background: #fff;
        color: #b5b0c9;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1rem;
        padding: 0;
        cursor: pointer;
    }

    .favorite-button.favorited {
        color: #ed1237;
    }

    .artwork-info {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        padding: 7px 5px 0;
        font-size: .66rem;
        line-height: 1.5;
    }

    .artwork-name {
        font-weight: 800;
        display: block;
    }

    .artwork-artist {
        display: block;
        color: #55517c;
    }

    .artwork-price {
        white-space: nowrap;
        text-align: right;
    }

    .artwork-price a {
        color: #1d1b68;
        text-decoration: none;
        white-space: nowrap;
        font-weight: 700;
    }

    .artwork-price a:hover {
        text-decoration: underline;
    }

    .artwork-stock {
        margin: 7px 5px 2px;
        padding: 5px 8px;
        border-radius: 8px;
        background: #f3efff;
        color: #51429b;
        font-size: .65rem;
        font-weight: 700;
    }

    .artwork-stock.low-stock {
        background: #fff1dd;
        color: #9b5a00;
    }

    .artwork-stock.out-of-stock {
        background: #ffe6e8;
        color: #bb1f38;
    }

    .no-artworks {
        display: none;
        padding: 25px;
        text-align: center;
        color: #77739c;
        font-size: .85rem;
    }

    @media (max-width: 1000px) {
        .artwork-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .explore-toolbar {
            gap: 8px;
        }
    }

    @media (max-width: 560px) {
        .artwork-grid {
            grid-template-columns: 1fr;
        }

        .artwork-image {
            height: 190px;
        }

        .art-search {
            max-width: none;
            flex-basis: 100%;
        }

        .category-button {
            padding: 9px 13px;
        }
    }
</style>
@endpush

@section('content')
<div class="explore-page">

    <h1 class="explore-heading">Explore Artworks</h1>
    <p class="explore-subtitle">
        Discover digital artwork from independent artists around the world.
    </p>

    <div class="explore-toolbar">
        <label class="art-search">
            <input
                id="artSearch"
                type="search"
                placeholder="Search artworks, artists, or styles"
                aria-label="Search artworks"
            >
            <i class="bi bi-search"></i>
        </label>

        <button type="button" class="category-button active" data-category="All">
            All
        </button>

        <button type="button" class="category-button" data-category="Philippines">
            Philippines
        </button>

        <button type="button" class="category-button" data-category="Japan">
            Japan
        </button>

        <button type="button" class="category-button" data-category="Scotland">
            Scotland
        </button>

        <button type="button" class="category-button" data-category="Greece">
            Greece
        </button>

        <select class="art-sort" id="artSort" aria-label="Sort artworks">
            <option value="newest">Newest</option>
            <option value="name">Name A-Z</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="stock-low">Stock: Low to High</option>
        </select>

        <button
            type="button"
            class="art-filter"
            id="filterButton"
            title="Reset filters"
            aria-label="Reset filters"
        >
            <i class="bi bi-funnel"></i>
        </button>
    </div>

    @php
        $artworks = [
            [
                'id' => 1,
                'name' => 'Tikbalang in the Mist',
                'artist' => 'Janessa Mae Ramos',
                'price' => 300,
                'category' => 'Philippines',
                'stock' => 8,
                'description' => 'A mystical digital artwork of the Tikbalang, a legendary Philippine creature with a human-like body and a horse’s head, standing mysteriously in an enchanted forest.',
                'rating' => 5.0,
                'sold' => 124,
                'format' => 'JPG',
                'resolution' => '6000 x 7500 px',
                'license' => 'Personal Use',
                'newest' => 6,
            ],
            [
                'id' => 2,
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
                'newest' => 5,
            ],
            [
                'id' => 3,
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
                'newest' => 4,
            ],
            [
                'id' => 4,
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
                'newest' => 3,
            ],
            [
                'id' => 5,
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
                'newest' => 2,
            ],
            [
                'id' => 6,
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
                'newest' => 1,
            ],
            [
                'id' => 7,
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
                'newest' => 7,
            ],
            [
                'id' => 8,
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
                'newest' => 8,
            ],
        ];
    @endphp

    <div class="artwork-grid" id="artworkGrid">
        @foreach ($artworks as $art)
            <article
                class="artwork-card"
                data-id="{{ $art['id'] }}"
                data-name="{{ strtolower($art['name']) }}"
                data-artist="{{ strtolower($art['artist']) }}"
                data-category="{{ $art['category'] }}"
                data-price="{{ $art['price'] }}"
                data-stock="{{ $art['stock'] }}"
                data-newest="{{ $art['newest'] }}"
            >
                <div class="artwork-image">
                    <a
                        class="artwork-image-link"
                        href="{{ route('product.show', ['id' => $art['id']]) }}"
                        aria-label="View {{ $art['name'] }} details"
                    >
                        <i class="bi bi-image art-placeholder-icon"></i>
                    </a>

                    <button
                        type="button"
                        class="favorite-button"
                        aria-label="Toggle favorite"
                        aria-pressed="false"
                    >
                        <i class="bi bi-heart"></i>
                    </button>
                </div>

                <div class="artwork-info">
                    <div>
                        <span class="artwork-name">{{ $art['name'] }}</span>
                        <span class="artwork-artist">By: {{ $art['artist'] }}</span>
                    </div>

                    <div class="artwork-price">
                        <span>Php {{ number_format($art['price'], 2) }}</span>
                        <br>
                        <a href="{{ route('product.show', ['id' => $art['id']]) }}">
                            View details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="artwork-stock {{ $art['stock'] === 0 ? 'out-of-stock' : ($art['stock'] <= 3 ? 'low-stock' : '') }}">
                    @if ($art['stock'] === 0)
                        <i class="bi bi-x-circle"></i> Out of stock
                    @elseif ($art['stock'] <= 3)
                        <i class="bi bi-exclamation-circle"></i>
                        Only {{ $art['stock'] }} left in stock
                    @else
                        <i class="bi bi-box-seam"></i>
                        Stock: {{ $art['stock'] }} left
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <div class="no-artworks" id="noArtworks">
        No artworks found. Try another search or category.
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('artSearch');
    const sortSelect = document.getElementById('artSort');
    const grid = document.getElementById('artworkGrid');
    const emptyMessage = document.getElementById('noArtworks');
    const categoryButtons = document.querySelectorAll('.category-button');
    const filterButton = document.getElementById('filterButton');

    let selectedCategory = 'All';

    const params = new URLSearchParams(window.location.search);
    const artistFromHome = params.get('artist');
    const categoryFromHome = params.get('category');

    if (artistFromHome) {
        searchInput.value = artistFromHome;
    }

    if (categoryFromHome) {
        selectedCategory = categoryFromHome;

        categoryButtons.forEach(function (button) {
            button.classList.toggle(
                'active',
                button.dataset.category === selectedCategory
            );
        });
    }

    function updateArtworks() {
        const query = searchInput.value.toLowerCase().trim();
        const cards = Array.from(grid.querySelectorAll('.artwork-card'));

        cards.forEach(function (card) {
            const matchesSearch =
                card.dataset.name.includes(query) ||
                card.dataset.artist.includes(query) ||
                card.dataset.category.toLowerCase().includes(query);

            const matchesCategory =
                selectedCategory === 'All' ||
                card.dataset.category === selectedCategory;

            card.style.display =
                matchesSearch && matchesCategory ? '' : 'none';
        });

        const visibleCards = cards.filter(function (card) {
            return card.style.display !== 'none';
        });

        visibleCards.sort(function (a, b) {
            if (sortSelect.value === 'name') {
                return a.dataset.name.localeCompare(b.dataset.name);
            }

            if (sortSelect.value === 'price-low') {
                return Number(a.dataset.price) - Number(b.dataset.price);
            }

            if (sortSelect.value === 'price-high') {
                return Number(b.dataset.price) - Number(a.dataset.price);
            }

            if (sortSelect.value === 'stock-low') {
                return Number(a.dataset.stock) - Number(b.dataset.stock);
            }

            return Number(b.dataset.newest) - Number(a.dataset.newest);
        });

        visibleCards.forEach(function (card) {
            grid.appendChild(card);
        });

        emptyMessage.style.display =
            visibleCards.length > 0 ? 'none' : 'block';
    }

    categoryButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            selectedCategory = button.dataset.category;

            categoryButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            button.classList.add('active');
            updateArtworks();
        });
    });

    searchInput.addEventListener('input', updateArtworks);
    sortSelect.addEventListener('change', updateArtworks);

    filterButton.addEventListener('click', function () {
        selectedCategory = 'All';
        searchInput.value = '';
        sortSelect.value = 'newest';

        categoryButtons.forEach(function (item) {
            item.classList.toggle('active', item.dataset.category === 'All');
        });

        updateArtworks();
    });

    document.querySelectorAll('.favorite-button').forEach(function (button) {
        button.addEventListener('click', function () {
            const favorited = button.classList.toggle('favorited');
            button.setAttribute('aria-pressed', favorited ? 'true' : 'false');

            const icon = button.querySelector('i');
            icon.className = favorited ? 'bi bi-heart-fill' : 'bi bi-heart';
        });
    });

    updateArtworks();
});
</script>
@endpush
